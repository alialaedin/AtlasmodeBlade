<?php

namespace Modules\Order\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Helpers\Helpers;
use Modules\Order\Entities\Order;
use Modules\Product\Entities\Variety;

class UpdateItemsRequest extends FormRequest
{
  public function rules()
  {
    return [
      'variety_id' => 'required|integer|exists:varieties,id',
      'quantity' => 'required|integer|min:1',
    ];
  }

  public function authorize()
  {
    return true;
  }

  public function passedValidation()
  {
    $variety = Variety::query()->with('attributes')->findOrFail($this->variety_id);
    $this->variety = $variety;
    $orderItem = $this->route('order_item');
    $order = $orderItem->order;
    if ($orderItem->quantity == $this->quantity) {
      throw Helpers::makeValidationException('تعداد وارد شده برابر با تعداد فعلی محصول در سفارش می باشد');
    }
    if ($order->status == Order::STATUS_CANCELED || $order->status == Order::STATUS_FAILED) {
      throw Helpers::makeValidationException("زمانی که وضعیت محصول کنسل یا خطا است نمیتوانید به آن آیتم اضافه کنید");
    }
  }
}
