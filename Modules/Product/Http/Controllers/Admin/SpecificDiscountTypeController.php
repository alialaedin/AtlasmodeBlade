<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\SpecificDiscount;
use Modules\Product\Entities\SpecificDiscountType;

class SpecificDiscountTypeController extends Controller
{
    private function validateDiscount($request)
    {
        $request->validate([
            'discount' => 'nullable|integer',
            'discount_type' => ['nullable', Rule::in([SpecificDiscountType::DISCOUNT_TYPE_PERCENTAGE, SpecificDiscountType::DISCOUNT_TYPE_FLAT])],
        ]);
    }

    public function index(SpecificDiscount $specificDiscount)
    {
        $specificDiscountTypes = $specificDiscount->types->sortByDesc('id');

        return view('product::admin.specific-discount.types', compact(['specificDiscountTypes', 'specificDiscount']));
    }

    public function store(SpecificDiscount $specificDiscount, Request $request)
    {
        $this->validateDiscount($request);
        $type = $specificDiscount->types()->create($request->all());
        ActivityLogHelper::simple('با موفقیت ثبت شد', 'store', $type);

        return redirect()->back()->with('success', 'با موفقیت ثبت شد');
    }

    public function update(SpecificDiscountType $specificDiscountType, Request $request)
    {
        $this->validateDiscount($request);
        $specificDiscountType->update($request->all());
        ActivityLogHelper::updatedModel('با موفقیت ثبت شد',$specificDiscountType);

        return redirect()->back()->with('success', 'با موفقیت ویرایش شد');
    }

    public function destroy(SpecificDiscountType $specificDiscountType)
    {
        if (!SpecificDiscount::is_deletable($specificDiscountType->specific_discount_id)) {
            throw Helpers::makeValidationException('قابل حذف نمی باشد');
        }

        $specificDiscountType->delete();
        ActivityLogHelper::updatedModel('با موفقیت حذف شد',$specificDiscountType);

        return redirect()->back()->with('success', 'با موفقیت حذف شد');
    }
}
