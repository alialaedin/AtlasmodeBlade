<?php

namespace Modules\Cart\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Cart\Entities\Cart;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\Variety;
use function Modules\Core\Helpers\Helpers;

class CartStoreRequest extends FormRequest
{
    public Variety $variety;
    // اگر توی سبدش از قبل باشه مقدار میگیره
    public ?Cart $alreadyInCart;

    public function rules()
    {
        return [
            'quantity' => 'required|integer|min:1',
        ];
    }

    protected function passedValidation()
    {
        $varietyId = Helpers::getModelIdOnPut('variety');
        $this->variety = Variety::query()->with('product.activeFlash')->whereKey($varietyId)->firstOrFail();
        if ($this->variety->product->status != 'available') {
            throw Helpers::makeValidationException('وضعیت محصول ناموجود است');
        }
        if ($this->variety->quantity == null || $this->variety->quantity == 0){
            throw Helpers::makeValidationException('تنوع مورد نظر ناموجود است');
        }
        // چک کنبم اگر از قبل تو سبد خریدش وجود داشت با اون چک کنیم
        $this->alreadyInCart = \Auth::user()->carts()->where('variety_id', $this->variety->id)->first();
        // ممکنه نال باشه پس صفر بجاش بذاز
        if ($this->quantity + ($this->alreadyInCart ? $this->alreadyInCart->quantity : 0) > $this->variety->quantity){
            throw Helpers::makeValidationException(' از تنوع مورد نظر فقط ' . $this->variety->quantity . " {$this->variety->product->unit->symbol} " . "موجود است ");
        }
    }
}
