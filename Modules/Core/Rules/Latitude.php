<?php

namespace Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class Latitude implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {
    return preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', $value);
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return trans('validation.latitude');
  }
}
