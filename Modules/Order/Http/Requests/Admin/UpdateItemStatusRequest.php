<?php

namespace Modules\Order\Http\Requests\Admin;

use Modules\Core\Helpers\Helpers;
use Modules\Order\Entities\Order;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Order\Entities\OrderItem;

class UpdateItemStatusRequest extends FormRequest
{
  public function rules()
  {
    return [
      'status' => ['required', 'boolean'],
    ];
  }

  public function authorize()
  {
    return true;
  }

  protected function passedValidation()
  {
    $itemId = Helpers::getModelIdOnPut('order_item');
    $orderItem = OrderItem::query()->findOrFail($itemId);
    $order = $orderItem->order;
    $variety = $orderItem->variety()->with(['attributes'])->first();
    if (!$variety) {
      throw Helpers::makeValidationException('تنوع این آیتم حذف شده است.');
    }
    if ($order->status == Order::STATUS_CANCELED || $order->status == Order::STATUS_FAILED) {
      throw Helpers::makeValidationException("زمانی که وضعیت محصول کنسل یا خطا است نمیتوانید وضعیت آیتم اضافه کنید");
    }

    $this->variety = $variety;
  }
}
