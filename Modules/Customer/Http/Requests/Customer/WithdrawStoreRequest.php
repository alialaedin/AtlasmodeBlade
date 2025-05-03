<?php

namespace Modules\Customer\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Modules\Customer\Entities\Customer;
use Modules\Core\Helpers\Helpers;

class WithdrawStoreRequest extends FormRequest
{
  public function rules()
  {
    return [
      'amount' => 'required|integer|min:1000',
    ];
  }

  public function passedValidation()
  {
    $this->checkBalance();
    $this->checkProfile();
  }


  public function checkBalance()
  {
    /** @var Customer $customer */
    $customer = Auth::user();
    if ($customer->balance < $this->amount) {
      throw Helpers::makeValidationException('مبلغ مورد نظر از شارژ کیف پول بیشتر است');
    }
  }

  public function checkProfile()
  {
    /** @var Customer $customer */
    $customer = Auth::user();
    $fields = [
      'bank_account_number',
      'card_number',
      'shaba_code'
    ];

    $anyExists = false;
    foreach ($fields as $field) {
      if ($customer->$field) {
        $anyExists = true;
      }
    }
    if (!$anyExists) {
      throw Helpers::makeValidationException('وارد کردن شماره کارت یا شماره حساب در پروفایل الزامی است');
    }
  }

  public function all($keys = null)
  {
    /** @var Customer $customer */
    $customer = Auth::user();
    $all = parent::all($keys);

    $excepts = ['tracking_code'];

    foreach ($excepts as $except) {
      unset($all[$except]);
    }

    $fields = [
      'bank_account_number' => $customer->bank_account_number,
      'card_number' => $customer->card_number,
      'shaba_code' => $customer->shaba_code
    ];

    return array_merge($all, $fields);
  }
}
