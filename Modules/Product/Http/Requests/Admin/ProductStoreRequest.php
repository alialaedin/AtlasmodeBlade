<?php

namespace Modules\Product\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Modules\Core\Classes\CoreSettings;
use Modules\Core\Rules\Base64OrMediaId;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Variety;
use Shetabit\Shopit\Modules\Product\Entities\Gift;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Services\Validations\ProductAttributeValidationService;
use Modules\Product\Services\Validations\DiscountValidationService;
use Modules\Product\Services\Validations\ProductSpecificationValidationService;


class ProductStoreRequest extends FormRequest
{
    public function rules()
    {
        $setting = app(CoreSettings::class);
        $haveType = $setting->get('size_chart.type')
            ?'required|integer|exists:size_chart_types,id'
            :'nullable|integer|exists:size_chart_types,id';
        $unitPriceNullable = $this->varieties && count($this->varieties) ? 'nullable' : 'required';

        return array_merge([
            // Product
            'product'                               => 'required|array',
            'product.title'                         => 'required|string|min:1|max:191',
            'product.short_description'             => 'nullable|string|max:191',
            'product.description'                   => 'nullable|string|max:10000',
            'product.unit_price'                    => "$unitPriceNullable|integer|min:1000,",
            'product.purchase_price'                => 'nullable|integer|min:1',
            'product.discount_type'                 => ['nullable', 'string', Rule::in(Product::getAvailableDiscountTypes())],
            'product.discount'                      => ['nullable','integer', 'required_with:product.discount_type'],
            'product.discount_until'                => ['nullable','date_format:Y/m/d H:i'],
            'product.SKU'                           => 'nullable|string',
            'product.barcode'                       => 'nullable|string',
            'product.image_alt'                     => 'nullable|string',
            'product.images'                        => ['nullable','array'],
            'product.images.*'                      => ['nullable', new Base64OrMediaId(Product::ACCEPTED_IMAGE_MIMES)],
            'product.brand_id'                      => 'nullable|exists:brands,id',
            'product.unit_id'                       => 'required|exists:units,id',
            'product.meta_description'              => 'nullable|string|max:15000',
            'product.meta_title'                    => 'nullable|string|max:191',
            'product.low_stock_quantity_warning'    => 'required|between:0,999999999999999.9999999999',
            'product.show_quantity'                 => 'required|boolean',
            'product.quantity'                      => 'nullable|required_without:product.varieties',
            'product.status'                        => ['required', Rule::in(Product::getAvailableStatuses())],
            'product.tags'                          => 'nullable|array',
            'product.published_at'                  => 'nullable',
            'product.categories'                    => 'required|array',
            'product.categories.*'                  => 'nullable|exists:categories,id',
            'product.gifts'                         => 'nullable|array',
            'product.gifts.*.id'                    => 'required_if:product.gifts.*,!=,null|integer|'.Rule::exists('gifts','id')
                    ->using(function ($query) { (new Gift())->scopeActive($query);}),
            'product.gifts.*.should_merge'          => 'required_if:product.gifts.*,!=,null|boolean',

            //size charts
            'product.size_charts'                   => 'nullable|array',
            'product.size_charts.*.title'           => 'required|string|min:1',
            'product.size_charts.*.chart'           => 'required|array',
            'product.size_charts.*.type_id'         => $haveType,
            //specifications
            'product.specifications'                => 'nullable|array',
            'product.specifications.*.id'           => 'required|integer|exists:specifications,id',
            'product.specifications.*.value'        => 'required|nullable', // array , text
            //varieties
            'product.varieties'                     => 'array',
            'product.varieties.*.price'             => 'required|integer|min:1000',
            'product.varieties.*.max_number_purchases'=> 'nullable|integer',
            'product.varieties.*.SKU'               => 'nullable|string|min:2|max:191',
            'product.varieties.*.barcode'           => 'nullable|min:2',
            'product.varieties.*.purchase_price'    => 'nullable|integer|min:2',
            'product.varieties.*.discount_type'     => ['nullable', 'string', Rule::in((new Variety())->getAvailableDiscountTypes())],
            'product.varieties.*.discount'          => ['nullable','integer','required_with:product.varieties.*.discount_type'],
            'product.varieties.*.quantity'          => 'nullable|integer|min:0', // TODO Fix fa numbers to en
            'product.varieties.*.images'            => ['nullable','array'],
            'product.varieties.*.images.*'          => ['nullable', new Base64OrMediaId(Variety::ACCEPTED_IMAGE_MIMES)],
            // gift
            'product.varieties.*.gifts'             => 'nullable|array',
            'product.varieties.*.gifts.*.id'        => 'required_if:product.varieties.*.gifts.*,!=,null|integer|'.Rule::exists('gifts','id')
                    ->using(function ($query) { (new Gift())->scopeActive($query);}),
            // attribute
            'product.varieties.*.attributes'        => 'nullable',
            'product.varieties.*.attributes.*'      => 'nullable',
            'product.varieties.*.attributes.*.id'   => 'required|exists:attributes,id',
            'product.varieties.*.attributes.*.value'=> 'required', //id , text
            // Color
            'product.varieties.*.color_id'          => 'nullable|integer|exists:colors,id',

            // Is chargeable
            'product.chargeable' => 'nullable|boolean'
        ], $this->customRules());
    }

    
    public function messages()
    {
        return array_merge(config('product.messages'), []);
    }

    private function customRules()
    {
        return [];
    }

    public function passedValidation()
    {
        $this->fixRequest();
        if (!empty($this->product['published_at'])){
            $data = Carbon::parse($this->product['published_at'])->toDateTimeString();
            throw_if($data < now()->subHour(), Helpers::makeValidationException('تاریخ انتشار وارد شده نمیتواند قبل از زمان حال باشد'));
        }
        
        (new DiscountValidationService($this->product['discount_type'], new Product , $this->product['discount'], $this->product['unit_price']))->checkDiscount();
        new ProductSpecificationValidationService($this->product['specifications'], $this->product['categories']);
        new ProductAttributeValidationService($this->product['varieties'], $this->product);  // check discount variety

        // Check barcode uniqueness
        foreach ($this->product['varieties'] as $variety) {
            if ($variety['barcode']) {
                $alreadyExists = Variety::where('barcode', Helpers::convertFaNumbersToEn($variety['barcode']))->exists();
                $alreadyExists && throw Helpers::makeValidationException('بارکد تکراری: ' . $variety['barcode']);
            }
            
        }
       
    }

    // After validation
    private function fixRequest()
    {
        $product = $this->product;
        foreach (['tags', 'varieties', 'specifications', 'size_charts'] as $key) {
            if (!isset($product[$key])){
                $product[$key] = [];
            }
        }

        $this->merge(['product' => $product]);
    }

    // Before validation
    private function beforeFixRequest()
    {
        $product = $this->product;
        foreach (['barcode', 'sku'] as $key) {
            if (isset($product[$key])) {
                $product[$key] = Helpers::convertFaNumbersToEn($product[$key]);
            }
        }
        if (empty($product['unit_price'])) {
            if (!empty($this->product['varieties'])){
                $product['unit_price'] = $this->product['varieties'][0]['price'];
            }
        }
        $this->merge(['product' => $product]);
    }

    protected function prepareForValidation()
    {
        $this->beforeFixRequest();
    }
}
