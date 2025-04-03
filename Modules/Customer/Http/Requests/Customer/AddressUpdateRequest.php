<?php

namespace Modules\Customer\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Classes\CoreSettings;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Rules\Latitude;
use Modules\Core\Rules\Longitude;
use Modules\Customer\Entities\Address;

class AddressUpdateRequest extends FormRequest
{
  public function rules()
  {
    $coreSetting = app(CoreSettings::class);

    return [
      'city' => 'required|integer|exists:cities,id',
      'first_name' => 'required|string|max:191',
      'last_name' => 'required|string|max:191',
      'address' => 'required|string|max:500',
      'postal_code' => [
        $coreSetting->get('order.postal_code_required', true) ? 'required' : 'nullable',
        'digits:10'
      ],
      'mobile' => 'required|string|size:11',
      'telephone' => 'nullable|string|max:191',
      'latitude' => ['nullable', new Latitude()],
      'longitude' => ['nullable', new Longitude()]
    ];
  }

  public function authorize()
  {
    return true;
  }

  public function passedValidation()
  {
    $address = $this->route('address');
    /** @var Address $address */
    //        $address = Address::query()->findOrFail(1);
    $hasOrdersReserved = $address->orders()->isReserved()->exists();
    if ($hasOrdersReserved) {
      throw Helpers::makeValidationException('آدرس دارای سفارش رزو شده می باشد درحال حاظر قادر به ویرایش آن نمی باشید');
    }
    $this->merge([
      'city_id' => $this->city
    ]);
  }
}
