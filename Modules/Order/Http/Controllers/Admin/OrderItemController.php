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

	public function updateStatus(UpdateItemStatusRequest $request, OrderItem $orderItem)
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
}
