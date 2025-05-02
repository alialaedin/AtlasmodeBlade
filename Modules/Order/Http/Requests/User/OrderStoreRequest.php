<?php

namespace Modules\Order\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\GiftPackage\Entities\GiftPackage;
use Modules\Invoice\Entities\Payment;
use Modules\Order\Classes\OrderStoreProperties;
use Modules\Order\Services\Validations\Customer\OrderValidationService;

class OrderStoreRequest extends FormRequest
{
	protected $stopOnFirstFailure = true;
	public  OrderStoreProperties $orderStoreProperties;

	public function rules(): array
	{
		$customer = $this->user();

		return [
			'address_id' => [
				'required',
				Rule::exists('addresses', 'id')->where(function ($query) use ($customer) {
					return $query->where('customer_id', $customer->id);
				})
			],
			'shipping_id' => [
				'required',
				Rule::exists('shippings', 'id')->where(function ($query) {
					return $query->where('status', 1);
				})
			],
			'coupon_code' => [
				'nullable',
				'string',
				'max:191',
				Rule::exists('coupons', 'code')
			],
			'payment_driver' => ['required', 'string', Rule::in(Payment::getAvailableDrivers())],
			// 'delivered_at' => 'required|date_format:Y-m-d',
			'pay_wallet' => 'required|boolean',
			'reserved' => 'nullable|boolean'
		];
	}


	// public function prepareForValidation()
	// {
	// 	$this->merge([
	// 		'delivered_at' => now()->format('Y-m-d')
	// 	]);
	// }

	public function passedValidation()
	{
		$this->user()->removeEmptyCarts();
		$service = new OrderValidationService($this->all(), $this->user());
		$this->orderStoreProperties = $service->properties;
		$this->merge([
			'gift_package_price' => ($this->get('gift_package_id')) ? GiftPackage::findOrFail($this->get('gift_package_id'))->price : 0
		]);
	}
}
