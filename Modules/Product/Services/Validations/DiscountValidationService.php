<?php

namespace Modules\Product\Services\Validations;

use Modules\Core\Helpers\Helpers;

class DiscountValidationService
{
  protected $requestDiscountType = '';
  protected $model;
  protected $discount = '';
  protected $unitPrice = '';

  public function __construct($requestDiscountType, $model, $discount, $unit_price = null)
  {
    $this->requestDiscountType = $requestDiscountType ?? null;
    $this->model = $model ?? null;
    $this->discount = $discount ?? null;
    $this->unitPrice = $unit_price ?? null;
  }

  public function checkDiscount()
  {
    if ($this->requestDiscountType) {
      // موقع ثبت کد تخفیف که تنوعی نداریم هم اینجا میاد!!
      if (($this->requestDiscountType == $this->model::DISCOUNT_TYPE_FLAT) &&
        ($this->unitPrice !== null && $this->discount > $this->unitPrice)
      ) {
        throw Helpers::makeValidationException('تخفیف نمیتواند بیشتر از قیمت محصول یا تنوع باشد');
      }
      if (($this->requestDiscountType == $this->model::DISCOUNT_TYPE_PERCENTAGE) and
        ($this->discount > 100)
      ) {
        throw Helpers::makeValidationException('تخفیف نمیتواند بیشتر از ۱۰۰ درصد باشد');
      }
    }
    if ($this->requestDiscountType == null and ($this->discount != 0)) {
      throw Helpers::makeValidationException('ابتدا نوع تخفیف را انتخاب کنید');
    }
  }
}
