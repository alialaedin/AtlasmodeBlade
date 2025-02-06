<?php

namespace Modules\Order\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Modules\Core\Helpers\Helpers;
use Modules\Order\Entities\Order;
use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateStatusRequest extends FormRequest
{
  public function rules()
  {
    return [
      'status' => [
        'required',
        Rule::in(Order::getAvailableStatuses())
      ],
      'description' => 'nullable|string|max:10000'
    ];
  }

  public function authorize()
  {
    return true;
  }

  protected function passedValidation()
  {
    $order = Order::query()->findOrFail($this->route('order'));

    if (
      in_array($order->status, [Order::STATUS_CANCELED, Order::STATUS_FAILED])
      &&
      $this->status == Order::STATUS_WAIT_FOR_PAYMENT
    ) {
      throw Helpers::makeValidationException('شما وضعیت سفارش را از کنسل و خطا نمیتوانید به وضعیت در انتظار پرداخت تغییر دهید', 'status');
    }

    $this->merge([
      'order' => $order
    ]);
  }
}
