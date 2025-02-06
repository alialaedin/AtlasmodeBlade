<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Category\Entities\Category;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\SpecificDiscount;
use Modules\Product\Entities\SpecificDiscountItem;
use Modules\Product\Entities\SpecificDiscountType;
use Modules\Product\Http\Requests\Admin\SpecificDiscountItemStoreRequest;

class SpecificDiscountItemController extends Controller
{
    public function index(SpecificDiscountType $specificDiscountType)
    {
        $specificDiscountItems = $specificDiscountType->items->sortByDesc('id');

        return view('product::admin.specific-discount.items', compact(['specificDiscountItems', 'specificDiscountType']));
    }

    public function create(SpecificDiscountType $specificDiscountType) 
    {
        $categories = Category::select(['id', 'title'])->get();
        $itemTypes = SpecificDiscountItem::getAvailableTypes();

        return view('product::admin.specific-discount.create-item', compact(['itemTypes', 'specificDiscountType', 'categories']));
    }

    public function store(SpecificDiscountType $specificDiscountType, SpecificDiscountItemStoreRequest $request)
    {
        $specificDiscountItem = $specificDiscountType->items()->create($request->all());
        ActivityLogHelper::simple('آیتم با موفقیت ذخیره شد', 'store', $specificDiscountItem);

        return redirect()->back()->with('success', 'آیتم با موفقیت ذخیره شد');
    }

    public function update(SpecificDiscountItem $specificDiscountItem, SpecificDiscountItemStoreRequest $request)
    {
        $specificDiscountItem->update($request->all());
        ActivityLogHelper::updatedModel('با موفقیت ویرایش شد',$specificDiscountItem);

        return redirect()->back()->with('success', 'آیتم با موفقیت ویرایش شد');
    }
    
    public function destroy(SpecificDiscountItem $specificDiscountItem)
    {
        if (!SpecificDiscount::is_deletable($specificDiscountItem->type->specific_discount_id)) {
            throw Helpers::makeValidationException('قابل حذف نمی باشد');
        }

        $specificDiscountItem->delete();
        ActivityLogHelper::deletedModel('با موفقیت حذف شد',$specificDiscountItem);

        return redirect()->back()->with('success', 'آیتم با موفقیت حذف شد');
    }
}
