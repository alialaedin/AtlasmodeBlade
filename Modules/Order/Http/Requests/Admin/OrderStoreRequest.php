<?php

namespace Modules\Order\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Customer\Entities\Customer;
use Modules\Order\Services\Validations\Customer\OrderValidationService;

class OrderStoreRequest extends FormRequest
{
  protected $stopOnFirstFailure = true;

  public function rules()
  {
    return [
      'customer_id' => 'bail|required|integer|min:1|exists:customers,id',
      'address_id' => [
        'bail',
        'required',
        'integer',
        'min:1',
        Rule::exists('addresses', 'id')->where(function ($query) {
          return $query->where('customer_id', $this->customer_id);
        })
      ],
      'shipping_id' => [
        'bail',
        'required',
        'integer',
        'min:1',
        Rule::exists('shippings', 'id')->where(function ($query) {
          return $query->where('status', 1);
        })
      ],
      'discount_amount' => 'nullable|integer|min:1',
      'description' => 'nullable|string|max:65000',
      'varieties' => 'required|array',
      'varieties.*.id' => [
        'bail',
        'required',
        'integer',
        'min:1',
        Rule::exists('varieties', 'id')
      ],
      'varieties.*.quantity' => ['required', 'integer', 'min:1'],
      'reserved' => 'nullable|boolean'
    ];
  }

  public function authorize()
  {
    return true;
  }

  public function passedValidation()
  {
    /**
     * @var Customer $customer
     */
    $customer = Customer::query()->findOrFail($this->customer_id);
    $service = new OrderValidationService($this->all(), $customer, true, $this->varieties);

    $this->merge([
      'delivered_at' => now(),
      'customer' => $customer,
      'orderStoreProperties' => $service->properties
    ]);

  }
}
