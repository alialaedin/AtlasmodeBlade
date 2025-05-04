<?php

namespace Modules\Order\Http\Requests\Admin;

use Modules\Core\Helpers\Helpers;
use Modules\Order\Entities\Order;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Order\Entities\OrderItem;

class UpdateItemStatusRequest extends FormRequest
{
  public function prepareForValidation()
  {
    if(is_string($this->status) && in_array($this->status, ["true", "false"])) {
      $this->merge([
        'status' => $this->status == 'false' ? 0 : 1
      ]);
    }
  }

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
    $itemId = Helpers::getModelIdOnPut('orderItem');
    $orderItem = OrderItem::query()->findOrFail($itemId);
    $order = $orderItem->order;
    $variety = $orderItem->variety()->with(['attributes'])->first();
    if (!$variety) {
      throw Helpers::makeValidationException('تنوع این آیتم حذف شده است.');
    }
    if ($order->status == Order::STATUS_CANCELED || $order->status == Order::STATUS_FAILED) {
      throw Helpers::makeValidationException("زمانی که وضعیت سفارش کنسل یا خطا است نمیتوانید وضعیت آیتم آن را تغییر دهید");
    }
    $this->merge([
      'variety' => $variety
    ]);
  }
}
