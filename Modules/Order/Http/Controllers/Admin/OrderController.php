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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Modules\Area\Entities\Province;
use Modules\Cart\Entities\Cart;
use Modules\Order\Entities\OrderLog;
use Modules\Order\Http\Requests\Admin\OrderStoreRequest;
use Modules\Order\Http\Requests\Admin\OrderUpdateRequest;
use Modules\Order\Http\Requests\Admin\OrderUpdateStatusRequest;
use Shetabit\Shopit\Modules\Order\Events\OrderChangedEvent;
use Modules\Order\Jobs\ChangeStatusNotificationJob;
use Modules\Order\Services\Statuses\ChangeStatus;
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
        'items.variety.attributes',
        'orderLogs.logItems'
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

      $diffTotalAmount = $order->total_amount - $oldTotalAmount;
      //withdraw customer wallet
      if ($diffTotalAmount > 0) {
        $order->setPayDescription('کسر مبلغ اضافه شده سفارش از کیف پول بعد از به روزرسانی مدیریت #' . $order->id);
        $customer->withdraw($diffTotalAmount, $order->getMetaProduct());
      } elseif ($diffTotalAmount < 0) {
        $order->setPayDescription('افزایش مبلغ کم شده سفارش به کیف پول بعد از به روزرسانی مدیریت #' . $order->id);
        $customer->deposit(-$diffTotalAmount, $order->getMetaProduct());
      }
      $order->load('shipping', 'orderLogs');

      OrderLog::addLog(
        $order,
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
      customer: $customer,
      carts: $carts,
      chosenAddress: ($request->has('address_id')) ? $customer->addresses()->where('id', $request->address_id)->first() : null,
    );

    return response()->success('سبد خرید شما', compact(
      'carts',
      'hasFreeShippingProduct',
      'shippings'
    ));
  }
}
