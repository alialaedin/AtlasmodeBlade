<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Category\Entities\Category;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\SpecificDiscountItem;

class SpecificDiscountItemStoreRequest extends FormRequest
{
    public function rules()
    {   
        dd($this->all());
        return [
            'type' => ['required', Rule::in(SpecificDiscountItem::getAvailableTypes())],
            'discount' => 'nullable|integer',
            'discount_type' => ['nullable', Rule::in([Product::DISCOUNT_TYPE_PERCENTAGE, Product::DISCOUNT_TYPE_FLAT])],
            'discount_until' => 'nullable|date_format:Y-m-d H:i:s',
            'model_ids' => 'nullable|string',
            'balance' => 'nullable|integer',
            'balance_type' => ['nullable', Rule::in([SpecificDiscountItem::BALANCE_TYPE_LESS, SpecificDiscountItem::BALANCE_TYPE_MORE])],
            'range_from' => 'nullable|integer',
            'range_to' => 'nullable|integer',
        ];
    }

    public function authorize()
    {
        return true;
    }
    protected function prepareForValidation()
    {
        if (!$this->filled('discount') && !$this->discount) {
            $this->merge([
                'discount' => null,
                'discount_type' => null,
                'discount_until' => null
            ]);
        }
        // front send discount_until with this template: Y-m-d H:m  this is not correct. we convert to this: Y-m-d H:m:s
        elseif ($this->filled('discount_until') && $this->discount_until) {
            $this->merge([
                'discount_until' => $this->input('discount_until') . ':00'
            ]);
        }


        if ($this->input('discount_type') && $this->discount_type == 'percentage') {
            if ($this->discount < 0 || $this->discount > 100) {
                throw Helpers::makeValidationException([ 'discount_type' => ['درصد باید بین صفر تا صد باشد'] ]);
            }
        }
        $this->merge([
            'creator_id' => auth('admin')->user()->id
        ]);
    }


    protected function passedValidation()
    {
        switch ($this->input('type')) {
            case SpecificDiscountItem::TYPE_CATEGORY: /* TYPE_CATEGORY */
                if (!$this->input('model_ids')) {
                    throw Helpers::makeValidationException([ 'model_ids' => ['لطفا دسته بندی های را انتخاب کنید'] ]);
                }
                $model_ids = explode(',', $this->input('model_ids'));
                foreach ($model_ids as $model_id) {
                    if (Category::query()->where('id', $model_id)->count() != 1) {
                        throw Helpers::makeValidationException([ 'model_ids' => ['دسته بندی با شناسه ' . $model_id . ' وجود ندارد'] ]);
                    }
                }

                $this->merge([
                    'balance' => null,
                    'balance_type' => null,
                    'range_from' => null,
                    'range_to' => null,
                ]);
                break;
            // =========================================================================================================
            case SpecificDiscountItem::TYPE_PRODUCT: /* TYPE_PRODUCT */
                if (!$this->input('model_ids')) {
                    throw Helpers::makeValidationException([ 'model_ids' => ['لطفا محصولات را انتخاب کنید'] ]);
                }
                $model_ids = explode(',', $this->input('model_ids'));
                foreach ($model_ids as $model_id) {
                    if (Product::query()->where('id', $model_id)->count() != 1) {
                        throw Helpers::makeValidationException([ 'model_ids' => ['محصول با شناسه ' . $model_id . ' وجود ندارد'] ]);
                    }
                }
                $this->merge([
                    'balance' => null,
                    'balance_type' => null,
                    'range_from' => null,
                    'range_to' => null,
                ]);
                break;
            // =========================================================================================================
            case SpecificDiscountItem::TYPE_BALANCE: /* TYPE_BALANCE */
                if (!$this->input('balance') || !$this->input('balance_type')) {
                    throw Helpers::makeValidationException([ 'balance' => ['مقدار موجودی و نوع موجودی الزامی است'] ]);
                }

                $this->merge([
                    'model_ids' => null,
                    'range_from' => null,
                    'range_to' => null,
                ]);

                $this->offsetUnset('model_ids');
                $this->offsetUnset('range_from');
                $this->offsetUnset('range_to');
                break;
            // =========================================================================================================
            case SpecificDiscountItem::TYPE_RANGE: /* TYPE_RANGE */
                if (!$this->input('range_from') || !$this->input('range_to')) {
                    throw Helpers::makeValidationException([ 'range_from' => ['مقادیر بازه قیمتی الزامی هستند'] ]);
                }
                $this->merge([
                    'model_ids' => null,
                    'balance' => null,
                    'balance_type' => null,
                ]);
                break;

        }
    }
}
