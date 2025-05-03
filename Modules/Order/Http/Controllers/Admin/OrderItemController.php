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
use Modules\Core\Helpers\Helpers;
use Modules\Store\Entities\Store;
use Modules\Store\Entities\StoreTransaction;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Order\Entities\OrderLog;
use Modules\Order\Entities\OrderItemLog;
use Shetabit\Shopit\Modules\Order\Events\OrderChangedEvent;

class OrderItemController extends Controller
{
	public function store(AddItemsRequest $request, Order $order)
	{
		try {
			event(new OrderChangedEvent($order, $request));

			$product = Product::with('activeFlash')->findOrFail($request->product_id);
			$variety = $request->variety;
			$parentOrder = $order->reserved_id ? Order::findOrFail($order->reserved_id) : $order;

			$oldTotalAmount = $order->total_amount;
			$oldShippingAmount = $parentOrder->shipping_amount;

			DB::beginTransaction();

			$orderItem = $order->items()->create([
				'product_id' => $request->product_id,
				'variety_id' => $variety->id,
				'quantity' => $request->quantity,
				'amount' => $variety->final_price['amount'],
				'flash_id' => optional($product->activeFlash->first())->id,
				'discount_amount' => $variety->final_price['discount_price'],
				'extra' => [
					'attributes' => $variety->attributes()->get(['name', 'label', 'value']),
					'color' => $variety->color ? $variety->color->name : null,
				],
			]);

			$newShippingAmount = $parentOrder->recalculateShippingAmount();
			$calculateAmount = ($orderItem->amount * $orderItem->quantity) + ($newShippingAmount - $oldShippingAmount);

			$customer = $order->customer;
			$customer->withdraw($calculateAmount, [
				'name' => $customer->full_name,
				'mobile' => $customer->mobile,
				'description' => "اضافه کردن محصول {$variety->title} به سفارش {$order->id}",
			]);

			Store::insertModel((object)[
				'type' => Store::TYPE_DECREMENT,
				'description' => "اضافه کردن محصول {$variety->title} به سفارش {$order->id}",
				'quantity' => $orderItem->quantity,
				'variety_id' => $orderItem->variety_id,
				'order_id' => $order->id
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

			return redirect()->back()->with('success', 'تنوع مورد نظر با موفقیت به لیست خرید اضافه شد');
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e->getTraceAsString());
			return redirect()->back()->with('error', 'عملیات ناموفق ' . $e->getMessage());
		}
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

			$newShippingAmount = $order->recalculateShippingAmount();
			$diffShippingAmount = $oldShippingAmount - $newShippingAmount;

			$calculateAmount = ($orderItem->amount * $orderItem->quantity) + $diffShippingAmount;

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
		$order = $orderItem->order;
		event(new OrderChangedEvent($order, $request));

		$parentOrder = $order->reserved_id ? Order::findOrFail($order->reserved_id) : $order;
		$oldTotalAmount = $order->total_amount;
		$oldQuantity = $orderItem->quantity;
		$oldShippingAmount = $parentOrder->shipping_amount;

		try {
			DB::beginTransaction();

			$variety = $request->variety;
			$newQuantity = $request->quantity;
			$diffQuantity = $newQuantity - $oldQuantity;

			if ($diffQuantity > 0) {
				if ($variety->quantity < $diffQuantity) {
					throw Helpers::makeValidationException("تعداد سفارش این تنوع بیشتر از موجودی است. موجودی این تنوع: {$variety->quantity}");
				}
			}

			$orderItem->update(['quantity' => $newQuantity]);

			$diffAmount = $diffQuantity * $orderItem->amount;
			$shippingDiff = $parentOrder->recalculateShippingAmount() - $oldShippingAmount;
			$totalDiffAmount = $diffAmount + $shippingDiff;

			$walletType = ($diffQuantity > 0) ? 'decrement' : 'increment';

			$customer = $order->customer;
			$description = ($walletType === 'decrement')
				? "از محصول {$variety->title} به تعداد {$newQuantity} از سفارش کم شد"
				: "از محصول {$variety->title} به تعداد {$newQuantity} به سفارش اضافه شد";

			if ($walletType === 'decrement') {
				$customer->withdraw($totalDiffAmount, [
					'name' => $customer->getFullNameAttribute(),
					'mobile' => $customer->mobile,
					'description' => $description,
				]);
			} else {
				$customer->deposit($totalDiffAmount, [
					'name' => $customer->getFullNameAttribute(),
					'mobile' => $customer->mobile,
					'description' => $description,
				]);
			}

			Store::insertModel((object)[
				'type' => $walletType,
				'description' => $description,
				'quantity' => abs($diffQuantity),
				'variety_id' => $variety->id,
				'order_id' => $order->id
			]);

			$parentOrder->recalculateShippingAmount();
			$order->load('items');

			$orderLog = OrderLog::addLog(
				$order,
				$order->total_amount - $oldTotalAmount,
				0,
				null,
				null
			);

			$logType = ($diffQuantity > 0) ? 'decrement' : 'increment';
			OrderItemLog::addLog($orderLog, $orderItem, $logType, abs($oldQuantity - $newQuantity));

			DB::commit();
			return redirect()->back()->with('success', 'محصول مورد نظر با موفقیت بروزرسانی شد');
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e->getTraceAsString());
			return redirect()->back()->with('error', 'عملیات ناموفق ' . $e->getMessage());
		}
	}

	public function updateStatus(UpdateItemStatusRequest $request, OrderItem $orderItem)
	{
		$order = $orderItem->order;
		event(new OrderChangedEvent($order, $request));

		$oldTotalAmount = $order->total_amount;
		$oldAddress = $order->address;
		$oldShippingId = $order->shipping_id;
		$oldDiscountAmount = $order->discount_amount;
		$parentOrder = $order->reserved_id ? Order::findOrFail($order->reserved_id) : $order;

		try {
			$variety = $request->variety;
			$newStatus = $request->status;

			if ($orderItem->status == $newStatus) {
				return redirect()->back()->with('success', 'وضعیت با موفقیت تغییر کرد');
			}

			DB::beginTransaction();

			$orderItem->update(['status' => $newStatus]);

			$oldShippingAmount = $parentOrder->shipping_amount;
			$newShippingAmount = $parentOrder->calculateShippingAmount();
			$diffShippingAmount = ($newStatus == 1)
				? ($newShippingAmount - $oldShippingAmount)
				: ($oldShippingAmount - $newShippingAmount);
			$calculateAmount = $orderItem->amount * $orderItem->quantity + $diffShippingAmount;

			$quantity = $orderItem->quantity;
			$amount = $calculateAmount;
			$isDecrement = ($newStatus == 1);
			$walletType = $isDecrement ? 'decrement' : 'increment';
			$storeType = $walletType;
			$descriptionTemplate = $isDecrement
				? "با تغییر وضعیت آیتم سفارش  به تعداد {$quantity} عدد از محصول {$variety->title} کم شد"
				: "با تغییر وضعیت آیتم سفارش  به تعداد {$quantity} عدد به محصول {$variety->title} اضافه شد";

			$customer = $order->customer;
			$amountField = ['name' => $customer->getFullNameAttribute(), 'mobile' => $customer->mobile, 'description' => $descriptionTemplate];

			if ($isDecrement) {
				$customer->withdraw($amount, $amountField);
			} else {
				$customer->deposit($amount, $amountField);
			}

			Store::insertModel((object)[
				'type' => $storeType,
				'description' => $descriptionTemplate,
				'quantity' => $quantity,
				'variety_id' => $variety->id,
				'order_id' => $order->id
			]);

			$parentOrder->recalculateShippingAmount();
			$order->load('items');

			$orderLog = OrderLog::addLog(
				$order,
				($order->total_amount - $oldTotalAmount),
				$order->discount_amount - $oldDiscountAmount,
				($order->address != $oldAddress) ? $order->address : null,
				($order->shipping_id != $oldShippingId) ? $order->shipping_id : null
			);

			$logType = ($newStatus == 0) ? OrderItemLog::TYPE_DELETE : OrderItemLog::TYPE_NEW;
			OrderItemLog::addLog($orderLog, $orderItem, $logType, $orderItem->quantity);

			DB::commit();
			return redirect()->back()->with('success', 'وضعیت با موفقیت تغییر کرد');
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e->getTraceAsString());
			return redirect()->back()->with('error', 'عملیات ناموفق ' . $e->getMessage());
		}
	}
}
