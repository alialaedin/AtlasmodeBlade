<?php

namespace Modules\Order\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Bavix\Wallet\Exceptions\BalanceIsEmpty;
use Bavix\Wallet\Exceptions\InsufficientFunds;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Helpers\Helpers;
use Modules\Customer\Entities\Customer;
use Modules\Order\Entities\Order;
use Modules\Store\Entities\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Modules\Area\Entities\Province;
use Modules\Cart\Entities\Cart;
use Modules\Order\Entities\OrderLog;
use Modules\Order\Entities\OrderItem;
use Modules\Order\Entities\OrderItemLog;
use Modules\Order\Http\Requests\Admin\AddItemsRequest;
use Modules\Order\Http\Requests\Admin\OrderStoreRequest;
use Modules\Order\Http\Requests\Admin\OrderUpdateRequest;
use Modules\Order\Http\Requests\Admin\OrderUpdateStatusRequest;
use Modules\Order\Http\Requests\Admin\UpdateItemsRequest;
use Shetabit\Shopit\Modules\Order\Events\OrderChangedEvent;
use Modules\Order\Http\Requests\Admin\UpdateItemStatusRequest;
use Modules\Order\Jobs\ChangeStatusNotificationJob;
use Modules\Order\Services\Statuses\ChangeStatus;
use Modules\Product\Entities\Product;
use Modules\Setting\Entities\Setting;
use Modules\Product\Entities\Variety;
use Modules\Shipping\Services\ShippingCalculatorService;
use Throwable;

class OrderController extends Controller
{
    public function index()
    {
        $ordersQuery = Order::query()
            ->withCount('items')
            ->applyFilter()
            ->parents()
            ->filters()
            ->latest('id');

        $orders = $ordersQuery->paginate(request('perPage', 15))->withQueryString();
        $copyOrderQuery = clone $ordersQuery;
        Helpers::removeWhere($copyOrderQuery->getQuery(), 'status');
        $orderStatuses = Order::getAllStatuses($copyOrderQuery);

        return view('order::admin.order.index', compact(['orders', 'orderStatuses']));
    }

    public function show($id)
    {
        $order = Order::query()
            ->with([
                // 'orderLogs',
                'childs',
                'invoices.payments',
                'items'
            ])
            ->findOrFail($id);

        $child_items = [];
        foreach ($order->childs as $child) {
            if (in_array($child->status, Order::ACTIVE_STATUSES)) {
                foreach ($child->items->where('status', 1) as $ch_item) {
                    $child_items[] = $ch_item;
                }
            }
        }

        foreach ($child_items as $child_order_item) {
            if (in_array($child_order_item->variety_id, $order->items->pluck('variety_id')->toArray())) {
                foreach ($order->items as $item) {
                    if ($item->variety_id == $child_order_item->variety_id) {
                        if ($item->status == $child_order_item->status) {
                            $order->items->where('variety_id', $child_order_item->variety_id)->first()->quantity += $child_order_item->quantity;
                        } else {
                            $order->items->push($child_order_item);
                        }
                    }
                }
            } else {
                $order->items->push($child_order_item);
            }
        }

        $orderStatuses = Order::getAvailableStatuses();
        $addresses = $order->customer->addresses;

        return view('order::admin.order.show', compact('order', 'orderStatuses', 'addresses'));
    }

    public function create()
    {
      $provinces = Province::select(['id', 'name'])->active()->get();

      return view('order::admin.order.create', compact('provinces'));
    }

    public function store(OrderStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            /** @var Customer $customer */
            $customer = Customer::query()->whereKey($request->customer_id)->firstOrFail();

            /** @var Order $order */
            $order = Order::store($customer, $request);

            $order->payWithWallet($customer);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getTraceAsString());
            return response()->error(
                'ثبت سفارش به مشکل خورده است:' . $exception->getMessage(),
                $exception->getTrace(),
                500
            );
        }

        return response()->success('سفارش مشتری با موفقیت ثبت شد', compact('order'));
    }

    public function update(OrderUpdateRequest $request, $id)
    {
        event(new OrderChangedEvent($request->order, $request));
        DB::beginTransaction();
        try {
            /** @var Order $order */
            $order = $request->order;
            $parentOrder = $order->reserved_id == null ? $order : Order::query()->findOrFail($order->reserved_id);

            /** @var Customer $customer */
            $customer = $request->customer;
            $oldTotalAmount = $order->total_amount;
            $oldAddress = $order->address;
            $oldShipping = $order->shipping_id;
            $oldDiscountAmount = $order->discount_amount;
            $order->update([
                'shipping_id' => $request->shipping_id,
                'address' => $request->address->toJson(),
                'discount_amount' => $request->discount_amount,
                'description' => $request->description,
            ]);

            $parentOrder->recalculateShippingAmount();

            //Create status logs
            if ($order->statusLogs->count() < 1) {
                $order->statusLogs()->createMany([
                    ['status' => Order::STATUS_WAIT_FOR_PAYMENT],
                    ['status' => Order::STATUS_IN_PROGRESS]
                ]);
            }

            $diffTotalAmount = $order->total_amount - $oldTotalAmount ;
            //withdraw customer wallet
            if ($diffTotalAmount > 0) {
                $order->setPayDescription('کسر مبلغ اضافه شده سفارش از کیف پول بعد از به روزرسانی مدیریت #' . $order->id);
                $customer->withdraw($diffTotalAmount, $order->getMetaProduct());
            } elseif ($diffTotalAmount < 0) {
                $order->setPayDescription('افزایش مبلغ کم شده سفارش به کیف پول بعد از به روزرسانی مدیریت #' . $order->id);
                $customer->deposit(-$diffTotalAmount, $order->getMetaProduct());
            }
            $order->load('shipping', 'orderLogs');

            OrderLog::addLog($order,
                $order->total_amount - $oldTotalAmount,
                $order->discount_amount - $oldDiscountAmount,
                $order->address != $oldAddress ? $order->address : null,
                $order->shipping_id != $oldShipping ? $order->shipping_id : null
            );


            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getTraceAsString());
            return redirect()->back()->with('error', 'به روزرسانی سفارش به مشکل خورده است:' . $exception->getMessage());
        }

        return redirect()->back()->with('success', 'سفارش مشتری با موفقیت به روزرسانی ');
    }

    public function updateItemStatus(UpdateItemStatusRequest $request, OrderItem $orderItem)
    {
        event(new OrderChangedEvent($orderItem->order, $request));
        $order = $orderItem->order;
        $oldTotalAmount = $order->total_amount;
        $parentOrder = $order->reserved_id == null ? $order : Order::query()->findOrFail($order->reserved_id);

        try {
            $variety = $request->variety;
            $oldAddress = $order->address;
            $oldShipping = $order->shipping_id;
            $oldDiscountAmount = $order->discount_amount;

            DB::beginTransaction();
            if ($orderItem->status == $request->status) {
                return redirect()->back()->with('success', 'وضعیت با موفقیت تغییر کرد');
            }

            $orderItem->update(['status' => $request->status]);
            $oldShippingAmount = $parentOrder->shipping_amount;
            $newShippingAmount = $parentOrder->calculateShippingAmount();
            $diffShippingAmount = ($request->status == 1) ? ($newShippingAmount - $oldShippingAmount) : ($oldShippingAmount - $newShippingAmount);
            $calculateAmount = $orderItem->amount * $orderItem->quantity + $diffShippingAmount;

            if ($request->status == 1) {
                $quantity = $orderItem->quantity;
                $amount = $calculateAmount;
                $wallet = ['type' => 'decrement', 'amount' => $amount];
                $store = ['type' => 'decrement', 'quantity' => $quantity];
            } elseif ($request->status == 0) {
                $quantity = $orderItem->quantity;
                $amount = $calculateAmount;
                $wallet = ['type' => 'increment', 'amount' => $amount];
                $store = ['type' => 'increment', 'quantity' => $quantity];
            }
            /** @var Customer $customer */
            $customer = $orderItem->order->customer()->first();
            if ($wallet['type'] == 'decrement') {
                $customer->withdraw($wallet['amount'], [
                    'name' => $customer->getFullNameAttribute(),
                    'mobile' => $customer->mobile,
                    'description' => "با تغییر وضعیت آیتم سفارش  به تعداد {$quantity} عدد به محصول {$variety->title} اضافه شد"
                ]);

                Store::insertModel((object)[
                    'type' => $store['type'],
                    'description' => "با تغییر وضعیت آیتم سفارش  به تعداد {$quantity} عدد به محصول {$variety->title} اضافه شد",
                    'quantity' => $store['quantity'],
                    'variety_id' => $variety->id
                ]);
            }

            if ($wallet['type'] == 'increment') {
                $customer->deposit($wallet['amount'], [
                    'name' => $customer->getFullNameAttribute(),
                    'mobile' => $customer->mobile,
                    'description' => "با تغییر وضعیت آیتم سفارش به تعداد {$quantity} عدد از محصول {$variety->title} کم شد"
                ]);

                Store::insertModel((object)[
                    'type' => $store['type'],
                    'description' => "با تغییر وضعیت آیتم سفارش  به تعداد {$quantity} عدد از محصول {$variety->title} کم شد",
                    'quantity' => $store['quantity'],
                    'variety_id' => $variety->id
                ]);
            }
            $parentOrder->recalculateShippingAmount();
            $order->load('items');
            $orderLog = OrderLog::addLog(
                $order,
                ($order->total_amount - $oldTotalAmount),
                $order->discount_amount - $oldDiscountAmount,
                $order->address != $oldAddress ? $order->address : null,
                $order->shipping_id != $oldShipping ? $order->shipping_id : null
            );

            if ($request->status == 0) {
                $status = OrderItemLog::TYPE_DELETE;
            } else {
                $status = OrderItemLog::TYPE_NEW;
            }

            OrderItemLog::addLog($orderLog, $orderItem, $status, $orderItem->quantity);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'عملیات ناموفق ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'وضعیت با موفقیت تغییر کرد');
    }

    public function updateQuantityItem(UpdateItemsRequest $request, OrderItem $orderItem)
    {
        event(new OrderChangedEvent($orderItem->order, $request));
        /** @var Order $order */
        $order = $orderItem->order;
        $parentOrder = $order->reserved_id == null ? $order : Order::query()->findOrFail($order->reserved_id);

        $oldTotalAmount = $order->total_amount;
        $oldQuantity = $orderItem->quantity;
        $oldShippingAmount = $parentOrder->shipping_amount;
        try {
            DB::beginTransaction();
            $diffQuantity = 0;
            /**
             * @var OrderItem $orderItem
             */
            $variety = $request->variety;

            if ($orderItem->quantity > $request->quantity) {
                $newQuantity = $request->quantity; // - 2 = 5 - 3 for orderItem
                $diffQuantity = $orderItem->quantity - $request->quantity; // - 2 = 5 - 3
                $diffAmount = ($diffQuantity * $orderItem->amount);

                $orderItem->update([
                    'quantity' => $newQuantity,
                ]);
                /**
                 * Store Increment
                 * Wallet Increment
                 */
                $wallet = ['type' => 'increment', 'amount' => ($diffAmount + ($parentOrder->recalculateShippingAmount() - $oldShippingAmount))];
            } elseif ($orderItem->quantity < $request->quantity) {
                $newQuantity = $request->quantity;

                $diffQuantity = $request->quantity - $orderItem->quantity; // +2 = 4 - 2
                $diffAmount = ($diffQuantity * $orderItem->amount);
                if ($variety->quantity < $diffQuantity) {
                    throw Helpers::makeValidationException("تعداد سفارش این تنوع بیشتر از موجودی است. موجودی این تنوع : {$variety->quantity}");
                }
                $orderItem->update([
                    'quantity' => $newQuantity,
                ]);
                /**
                 * Store Decrement
                 * Wallet Decrement
                 * Variety quantity Increment
                 */

                $wallet = ['type' => 'decrement', 'amount' => ($diffAmount + ($parentOrder->recalculateShippingAmount() - $oldShippingAmount))];
            }
            /**
             * @var Customer $customer
             */
            $customer = $orderItem->order->customer()->first();
            if ($wallet['type'] == 'increment') {

                $customer->deposit($wallet['amount'], [
                    'name' => $customer->getFullNameAttribute(),
                    'mobile' => $customer->mobile,
                    'description' => "از محصول {$variety->title} به تعداد {$request->quantity} از سفارش کم شد"
                ]);

                Store::insertModel((object)[
                    'type' => $wallet['type'],
                    'description' => "از محصول {$variety->title} به تعداد {$diffQuantity} از سفارش کم شد",
                    'quantity' => $diffQuantity,
                    'variety_id' => $variety->id
                ]);
            } elseif ($wallet['type'] == 'decrement') {
                $customer->withdraw($wallet['amount'], [
                    'name' => $customer->getFullNameAttribute(),
                    'mobile' => $customer->mobile,
                    'description' => "از محصول {$variety->title} به تعداد {$request->quantity} به سفارش اضافه شد"
                ]);

                Store::insertModel((object)[
                    'type' => $wallet['type'],
                    'description' => "از محصول {$variety->title} به تعداد {$diffQuantity} به سفارش اضافه شد",
                    'quantity' => $diffQuantity,
                    'variety_id' => $variety->id
                ]);
            }


            $order->load('items');
            $orderLog = OrderLog::addLog(
                $order,
                $order->total_amount - $oldTotalAmount,
                0,
                null,
                null
            );

            $type = $wallet['type'] === 'increment' ? 'decrement' : 'increment';

            OrderItemLog::addLog($orderLog, $orderItem, $type, abs($oldQuantity - $request->quantity));

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', ' عملیات ناموفق ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'محصول مورد نظر با موفقیت بروزرسانی شد');
    }

    public function changeStatusSelectedOrders(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:orders,id',
            'status' => ['required', Rule::in([Order::STATUS_NEW, Order::STATUS_DELIVERED, Order::STATUS_IN_PROGRESS])]
        ]);
        $orders = Order::whereIn('id', $request->ids)->whereNull('parent_id')->get();

        try {
            DB::beginTransaction();
            foreach ($orders as $order) {
                $order->update(['status' => $request->status]);
                ChangeStatusNotificationJob::dispatch($order);
            }
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            Log::error($exception->getMessage() . $exception->getTraceAsString());
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('success', 'تغییر وضعیت با موفقیت انجام شد.');
    }

    public function print(Request $request)
    {
        $orderIdsArray = explode(',', $request->ids);
        $orders = Order::query()
            ->with([
                'items' => fn($q) => $q->select(['id', 'order_id', 'variety_id', 'amount', 'discount_amount', 'quantity']),
            ])
            ->select([
                'id',
                'customer_id',
                'address_id',
                'status',
                'parent_id',
                'created_at',
                'description',
                'shipping_amount',
                'discount_on_coupon',
                'total_items_amount',
                'total_invoices_amount'
            ])
            ->parents()
            ->whereIn('id', $orderIdsArray)
            ->latest('id')
            ->get();

        $settings = Setting::where('group', 'shop_info')->get();

        return view('order::admin.print', compact(['orders', 'settings']));
    }

    public function updateStatus(OrderUpdateStatusRequest $request, $id)
    {
        event(new OrderChangedEvent($request->order, $request));
        DB::beginTransaction();
        try {

            /** @var Order $order */
            $order = $request->order;
            (new ChangeStatus($order, $request))->checkStatus();
            ChangeStatusNotificationJob::dispatch($order);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            if (!($exception instanceof BalanceIsEmpty) && !($exception instanceof InsufficientFunds)) {
                Log::error($exception->getTraceAsString());
            }
            return redirect()->back()->with('error', 'عملیات به مشکل خورده است: ' . $exception->getMessage());
        }

        return redirect()->back()->with('success', 'وضعیت سفارش با موفقیت تغییر کرد');
    }

    public function addItem(AddItemsRequest $request, Order $order)
    {
        event(new OrderChangedEvent($order, $request));
        $variety = $request->variety;
        $oldTotalAmount = $order->total_amount;
        /**
         * @var Product $product 
         */
        $product = Product::query()->find($request->product_id);
        $activeFlash = $product->activeFlash->first();
        $parentOrder = $order->reserved_id == null ? $order : Order::query()->findOrFail($order->reserved_id);
        $oldShippingAmount = $parentOrder->shipping_amount;

        try {
            DB::beginTransaction();
            $orderItem = $order->items()->create([
                'product_id' => $request->product_id,
                'variety_id' => $variety->id,
                'quantity' => $request->quantity,
                'amount' => $variety->final_price['amount'],
                'flash_id' => $activeFlash->id ?? null,
                'discount_amount' => $variety->final_price['discount_price'],
                'extra' => collect([
                    'attributes' => $variety->attributes()->get(['name', 'label', 'value']),
                    'color' => $variety->color()->exists() ? $variety->color->name : null
                ])
            ]);
            $newShippingAmount = $parentOrder->recalculateShippingAmount();

            $calculateAmount = $orderItem->amount * $orderItem->quantity + ($newShippingAmount - $oldShippingAmount);
            /**
             * @var Customer $customer
             */
            $customer = $order->customer;
            $customer->withdraw($calculateAmount, [
                'name' => $customer->getFullNameAttribute(),
                'mobile' => $customer->mobile,
                'description' => "اضافه کردن محصول {$variety->title} به سفارش " . $order->id
            ]);

            Store::insertModel((object)[
                'type' => Store::TYPE_DECREMENT,
                'description' => "اضافه کردن محصول {$variety->title}  به سفارش " . $order->id,
                'quantity' => $orderItem->quantity,
                'variety_id' => $orderItem->variety_id
            ]);
            $order->load('items');

            $orderLog = OrderLog::addLog(
                $order,
                ($order->total_amount - $oldTotalAmount),
                0,
                null,
                null
            );

            OrderItemLog::addLog($orderLog, $orderItem, 'new', $orderItem->quantity);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'عملیات ناموفق ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'تنوع مورد نظر با موفقیت به لیست خرید اضافه شد');
    }

    public function getShippableShippings(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'varieties' => 'nullable|array',
            'varieties.*.variety_id' => 'nullable|exists:varieties,id',
            'varieties.*.quantity' => 'nullable|integer|min:1',
            'shipping_id' => ['nullable', 'exists:shippings,id'],
            'address_id' => ['nullable', 'exists:addresses,id'],
        ]);
        /* @var $customer Customer */
        $customer = Customer::query()->findOrFail($request->customer_id);
        $carts = [];
        foreach ($request->varieties ?? [] as $requestCart) {
            $variety = Variety::query()->find($requestCart['variety_id']);
            $carts[] = Cart::fakeCartMaker($variety->id, $requestCart['quantity'], $variety->final_price['discount_price'], $variety->final_price['amount']);
        }
        $carts = collect($carts);

        $hasFreeShippingProduct = Cart::hasfreeShippingProduct($carts);
        $shippings = ShippingCalculatorService::getShippableShippingsForFront(
            customer:  $customer,
            carts:  $carts,
            chosenAddress: ($request->has('address_id')) ? $customer->addresses()->where('id',$request->address_id)->first() : null,
        );

        return response()->success('سبد خرید شما', compact(
            'carts',
            'hasFreeShippingProduct',
            'shippings'
        ));
    }
}
