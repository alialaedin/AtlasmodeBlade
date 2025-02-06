<?php

namespace Modules\Store\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Modules\Product\Entities\Variety;

class VarietyTransferStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
//            'variety_id' => 'required|array',
            'varieties.*.id' => 'required|exists:varieties,id',
            'varieties.*.quantity' => 'required|integer',
//            'quantity' => 'required|integer',
            'from' => 'nullable|string',
            'from_location_id' => 'nullable|integer|exists:variety_transfer_locations,id',
            'to' => 'nullable|string',
            'to_location_id' => 'nullable|integer|exists:variety_transfer_locations,id',
            'mover' => 'required|string',
            'receiver' => 'required|string',
            'description' => 'nullable|string'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    protected function passedValidation()
    {
        $total_quantity = 0;
        foreach ($this->varieties as $requestVariety) {
            $variety = Variety::findOrFail($requestVariety['id']);
            if ($variety->store->balance < $requestVariety['quantity']) {
                throw ValidationException::withMessages([ 'error' => ['تعداد وارد شده از موجودی انبار بیشتر است'] ]);
            }
            $total_quantity += $requestVariety['quantity'];
        }

        $this->merge([
            'quantity' => $total_quantity
        ]);
    }
}
