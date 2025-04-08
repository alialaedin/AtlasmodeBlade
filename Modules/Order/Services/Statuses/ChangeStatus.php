<?php

namespace Modules\Order\Services\Statuses;

use Modules\Invoice\Entities\Invoice;
use Modules\Order\Entities\Order;
use Modules\Order\Events\OrderDelivered;
use Illuminate\Http\Request;
use Modules\Customer\Entities\Customer;
use Modules\Order\Entities\OrderStatusLog;
use Modules\Store\Entities\Store;

class ChangeStatus
{
    protected Customer $customer;

    const SUCCESS_STATUS = [Order::STATUS_NEW,Order::STATUS_DELIVERED,Order::STATUS_IN_PROGRESS,Order::STATUS_RESERVED];
    const FAILED_STATUS = [Order::STATUS_CANCELED, Order::STATUS_FAILED, Order::STATUS_WAIT_FOR_PAYMENT];

    public function __construct(public Order $order, public Request $request){
        $this->customer = $this->getCustomer($this->order->customer_id);
    }

    public function checkStatus(): Order
    {
        if ($this->request->status == $this->order->status){
            return $this->order;
        }

        if (in_array($this->request->status, static::FAILED_STATUS))
        {
            return $this->GoToCancelOrFail($this->order->status, $this->request->status);
        }

        if (in_array($this->request->status, static::SUCCESS_STATUS))
        {
            return $this->GoToNewOrDeliveredORInProgress($this->order->status, $this->request->status);
        }

        return $this->order;
    }

    public function GoToCancelOrFail($beforeStatus, $newStatus): Order
    {
        /** @var Invoice $invoice */
        $amount = $this->order->getTotalAmount();
        $orderItems = $this->order->items->where('status',1);
        if (!in_array($beforeStatus, self::FAILED_STATUS)) {
            if (!\request('no_charge')) {
                $this->depositCustomer($amount, $newStatus);
            }
            $this->depositStore($orderItems);
        }

        return $this->setStatus($newStatus);
    }

    protected function setStatus($status): Order
    {
        OrderStatusLog::store($this->order, $this->order->status);
        $this->order->status = $status;
        $this->order->save();

        return $this->order;
    }

    private function getCustomer($customerId)
    {
        return Customer::query()->findOrFail($customerId);
    }

    protected function depositCustomer($amount, $newStatus)
    {
        $this->customer->deposit($amount, [
            'causer_id' => auth()->user()->id,
            'causer_mobile' => auth()->user()->mobile,
            'description' => "برگشت مبلغ سفارش در اثر تغییر وضعیت به {$newStatus}"
        ]);
        $this->order->orderLogs()->create([
            'amount' => -$this->order->getTotalAmount(),
            'status' => $this->request->status
        ]);
    }

    public function depositStore($orderItems)
    {
        foreach ($orderItems as $item) {
            Store::insertModel((object)
            [
                'variety_id' => $item->variety_id,
                'description' => "با تغییر وضغیت سفارش با شناسه {$item->order_id} به انبار اضافه شد",
                'type' => Store::TYPE_INCREMENT,
                'quantity' => $item->quantity
            ]);
        }
    }
    

    public function GoToNewOrDeliveredORInProgress($beforeStatus, $newStatus): Order
    {
        if($newStatus == Order::STATUS_DELIVERED){
           event(new OrderDelivered($this->order));
        }
        
        if (!in_array($beforeStatus, self::SUCCESS_STATUS)) {
            $this->order->payWithWallet($this->customer);
            Invoice::withoutEvents(function (){
                $this->order->invoices()->latest('id')->first()->delete();
            });
            $this->order->orderLogs()->create([
                'amount' => $this->order->getTotalAmount(),
                'status' => $this->request->status
            ]);
        }
        // انبار در قسمت on success چک میشه

        return $this->setStatus($newStatus);
    }
}
