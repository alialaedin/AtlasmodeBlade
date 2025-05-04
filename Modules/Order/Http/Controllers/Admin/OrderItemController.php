<?php

namespace Modules\Order\Http\Controllers\Admin;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderItem;
use Modules\Order\Http\Requests\Admin\AddItemsRequest;
use Modules\Order\Http\Requests\Admin\UpdateItemsRequest;
use Modules\Order\Http\Requests\Admin\UpdateItemStatusRequest;
use Modules\Store\Entities\Store;
use Modules\Store\Entities\StoreTransaction;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Customer\Entities\Customer;
use Modules\Order\Entities\OrderLog;
use Modules\Order\Entities\OrderItemLog;
use Modules\Product\Entities\Product;
use Shetabit\Shopit\Modules\Order\Events\OrderChangedEvent;

class OrderItemController extends Controller
{
	public function store(AddItemsRequest $request, Order $order)
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
				'amount' => $variety->final_price['base_amount'],
				'flash_id' => $activeFlash->id ?? null,
				'discount_amount' => $variety->final_price['discount_price'],
				'extra' => collect([
					'attributes' => $variety->attributes()->get(['name', 'label', 'value']),
					'color' => $variety->color()->exists() ? $variety->color->name : null
				])
			]);

			$order->increment('discount_on_items', $orderItem->discount_amount * $orderItem->quantity);
			$order->increment('total_items_amount', $orderItem->amount * $orderItem->quantity);
			$order->increment('total_items_amount_with_discount', (($orderItem->amount - $orderItem->discount_amount) * $orderItem->quantity));
			$order->increment('items_count', 1);
			$order->increment('items_quantity', $orderItem->quantity);

			$newShippingAmount = $order->recalculateShippingAmount();

			$calculateAmount = ($orderItem->amount - $orderItem->discount_amount) * $orderItem->quantity + ($newShippingAmount - $oldShippingAmount);
			/**
			 * @var Customer $customer
			 */
			$customer = $order->customer;
			$customer->withdraw($calculateAmount, [
				'name' => $customer->full_name,
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

	public function destroy(OrderItem $orderItem)
	{
		if ($orderItem->status == 0) {
			$orderItem->delete();
			ActivityLogHelper::deletedModel('آیتم سفارش حذف شد', $orderItem);
			return redirect()->back()->with('success', 'آیتم سفارش با موفقیت حذف شد');
		}

		try {
			DB::beginTransaction();

			$order = $orderItem->order;
			$variety = $orderItem->variety;
			$oldShippingAmount = $order->shipping_amount;

			$orderItem->delete();

			$order->discount_on_items -= $orderItem->discount_amount * $orderItem->quantity;
			$order->total_items_amount -= $orderItem->amount * $orderItem->quantity;
			$order->total_items_amount_with_discount -= (($orderItem->amount - $orderItem->discount_amount) * $orderItem->quantity);

			$order->save();
			$order->decrement('items_count', 1);
			$order->decrement('items_quantity', $orderItem->quantity);

			$newShippingAmount = $order->recalculateShippingAmount();
			$diffShippingAmount = $oldShippingAmount - $newShippingAmount;

			$calculateAmount = (($orderItem->amount - $orderItem->discount_amount) * $orderItem->quantity) + $diffShippingAmount;

			$customer = $order->customer;
			$description = "به دلیل حذف آیتم سفارش از سفارش به شناسه {$order->id}";

			$customer->deposit($calculateAmount, [
				'name' => $customer->full_name,
				'mobile' => $customer->mobile,
				'description' => $description,
			]);

			Store::insertModel((object) [
				'type' => StoreTransaction::TYPE_INCREMENT,
				'description' => "با حذف آیتم سفارش به تعداد {$orderItem->quantity} عدد به محصول {$variety->title} اضافه شد",
				'quantity' => $orderItem->quantity,
				'variety_id' => $variety->id,
				'order_id' => $order->id
			]);

			ActivityLogHelper::deletedModel('آیتم سفارش حذف شد', $orderItem);
			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e->getTraceAsString());
			return redirect()->back()->with('error', 'عملیات ناموفق ' . $e->getMessage());
		}

		return redirect()->back()->with('success', 'آیتم سفارش با موفقیت حذف شد');
	}

	public function updateQuantity(UpdateItemsRequest $request, OrderItem $orderItem)
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

			$variety = $request->variety;
			$oldQuantity = $orderItem->quantity;
			$newQuantity = $request->quantity;
			$diffQuantity = $oldQuantity - $newQuantity;

			$orderItem->quantity = $newQuantity;
			$orderItem->save();

			$method = $diffQuantity > 0 ? Store::TYPE_DECREMENT : Store::TYPE_INCREMENT;
			$absDiffQuantity = abs($diffQuantity);

			if ($orderItem->discount_amount) {
				$order->$method('discount_on_items', $orderItem->discount_amount * $absDiffQuantity);
			}

			$order->$method('total_items_amount', $orderItem->amount * $absDiffQuantity);
			$order->$method('total_items_amount_with_discount', ($orderItem->amount - $orderItem->discount_amount) * $absDiffQuantity);
			$order->$method('items_quantity', $absDiffQuantity);

			$diffAmount = ($orderItem->amount - $orderItem->discount_amount) * $absDiffQuantity;
			$shippingAmount = $parentOrder->recalculateShippingAmount();

			$wallet['type'] = $method;
			$wallet['amount'] = $diffAmount + $shippingAmount - $oldShippingAmount;

			$customer = $orderItem->order->customer()->first();

			$walletMethod = $wallet['type'] == Store::TYPE_INCREMENT ? 'deposit' : 'withdraw';
			$description = $wallet['type'] == Store::TYPE_INCREMENT
				? "از محصول {$variety->title} به تعداد {$request->quantity} از سفارش کم شد"
				: "از محصول {$variety->title} به تعداد {$request->quantity} به سفارش اضافه شد";

			$customer->$walletMethod($wallet['amount'], [
				'name' => $customer->full_name,
				'mobile' => $customer->mobile,
				'description' => $description
			]);

			Store::insertModel((object) [
				'type' => $wallet['type'],
				'description' => $description,
				'quantity' => $absDiffQuantity,
				'variety_id' => $variety->id,
				'order_id' => $order->id
			]);

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

	public function updateStatus(UpdateItemStatusRequest $request, OrderItem $orderItem)
	{
		event(new OrderChangedEvent($orderItem->order, $request));
		$order = $orderItem->order;
		$oldTotalAmount = $order->total_amount;
		$parentOrder = $order->reserved_id == null ? $order : Order::query()->findOrFail($order->reserved_id);

		try {
			$variety = $request->variety;
			$oldDiscountAmount = $order->discount_amount;

			DB::beginTransaction();
			if ($orderItem->status == $request->status) {
				return redirect()->back()->with('success', 'وضعیت با موفقیت تغییر کرد');
			}

			$orderItem->status = $request->status;
			$orderItem->save();

			$orderMethod = $request->status ? 'increment' : 'decrement';

			if ($orderItem->discount_amount) {
				$order->$orderMethod('discount_on_items', $orderItem->discount_amount * $orderItem->quantity);
			}
			$order->$orderMethod('total_items_amount', $orderItem->amount * $orderItem->quantity);
			$order->$orderMethod('total_items_amount_with_discount', ($orderItem->amount - $orderItem->discount_amount) * $orderItem->quantity);
			$order->$orderMethod('items_count', 1);
			$order->$orderMethod('items_quantity', $orderItem->quantity);

			$oldShippingAmount = $parentOrder->shipping_amount;
			$newShippingAmount = $parentOrder->calculateShippingAmount();
			$diffShippingAmount = abs($newShippingAmount - $oldShippingAmount);

			$calculateAmount = (($orderItem->amount - $orderItem->discount_amount) * $orderItem->quantity) + $diffShippingAmount;

			$walletMethod = $request->status ? 'withdraw' : 'deposit';
			$type = $request->status ? Store::TYPE_DECREMENT : Store::TYPE_INCREMENT;
			$description = $type == Store::TYPE_DECREMENT
				? "با تغییر وضعیت آیتم سفارش  به تعداد {$orderItem->quantity} عدد از محصول {$variety->title} کم شد"
				: "با تغییر وضعیت آیتم سفارش  به تعداد {$orderItem->quantity} عدد به محصول {$variety->title} اضافه شد";

			$customer = $orderItem->order->customer;
			$customer->$walletMethod($calculateAmount, [
				'name' => $customer->full_name,
				'mobile' => $customer->mobile,
				'description' => $description
			]);

			Store::insertModel((object)[
				'type' => $type,
				'description' => $description,
				'quantity' => $orderItem->quantity,
				'variety_id' => $variety->id,
				'order_id' => $order->id
			]);

			$parentOrder->recalculateShippingAmount();
			$orderLog = OrderLog::addLog($order, ($order->total_amount - $oldTotalAmount), $order->discount_amount - $oldDiscountAmount);
			$orderItemLogStatus = $request->status ? OrderItemLog::TYPE_NEW : OrderItemLog::TYPE_DELETE;
			OrderItemLog::addLog($orderLog, $orderItem, $orderItemLogStatus, $orderItem->quantity);

			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e->getTraceAsString());
			return redirect()->back()->with('error', 'عملیات ناموفق ' . $e->getMessage());
		}
		return redirect()->back()->with('success', 'وضعیت با موفقیت تغییر کرد');
	}
}
