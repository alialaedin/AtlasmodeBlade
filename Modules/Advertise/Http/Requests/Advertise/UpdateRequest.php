<?php

namespace Modules\Advertise\Http\Requests\Advertise;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Link\Services\LinkValidator;

class UpdateRequest extends FormRequest
{


    public function rules()
    {
        return [
            'image' => 'nullable',
            'link' => "nullable|string",
            'new_tab' => 'required|in:0,1',
            'start' => 'nullable',
            'end' => 'nullable',
        ];
    }

    public function passedValidation()
    {
        (new LinkValidator($this))->validate();
    }
}
