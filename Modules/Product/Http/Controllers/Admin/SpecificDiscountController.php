<?php

namespace Modules\Product\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Category\Entities\Category;
use Modules\Core\Helpers\Helpers;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\SpecificDiscount;
use Modules\Product\Entities\SpecificDiscountItem;
use Modules\Product\Http\Requests\Admin\SpecificDiscountItemStoreRequest;
use Illuminate\Validation\Rule;
use Modules\Admin\Classes\ActivityLogHelper;

class SpecificDiscountController extends Controller
{
    public function index()
    {
        $specificDiscounts = SpecificDiscount::query()
            ->select(['id', 'title', 'start_date', 'end_date', 'created_at'])
            ->filters()
            ->orderByDesc('id')
            ->paginate(request('perPage', 15));

        return view('product::admin.specific-discount.index', compact('specificDiscounts'));
    }

    public function store(Request $request)
    {
        $request->offsetUnset('done_at');
        $request->validate([
            'title' => 'required|string|min:3',
            'start_date' => 'required|date_format:Y-m-d H:i|after:now',
            'end_date' => 'required|date_format:Y-m-d H:i|after:start_date',
        ]);
        $request->merge([
            'creator_id' => auth('admin')->user()->id,
            'updater_id' => auth('admin')->user()->id,
        ]);

        $specificDiscount = SpecificDiscount::create($request->all());
        ActivityLogHelper::simple('تخفیف ویژه ثبت شد', 'store', $specificDiscount);

        return redirect()->back()->with('success', 'تخفیف ویژه با موفقیت ذخیره شد');
    }

    public function update(Request $request, SpecificDiscount $specificDiscount)
    {
        $request->offsetUnset('done_at');
        $request->offsetUnset('creator_id');
        $request->validate([
            'title' => 'required|string|min:3',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($specificDiscount->start_date <= now() || $specificDiscount->done_at) {
            throw Helpers::makeValidationException('این تخفیف قابل ویرایش نمی باشد');
        }

        $request->merge(['updater_id' => auth('admin')->user()->id]);
        $specificDiscount->update($request->all());

        ActivityLogHelper::updatedModel('تخفیف ویژه ثبت شد', $specificDiscount);

        return redirect()->back()->with('success', 'تخفیف ها با موفقیت ویرایش شد');
    }

    public function destroy(SpecificDiscount $specificDiscount)
    {
        if ($specificDiscount->start_date <= now() || $specificDiscount->done_at)
            throw Helpers::makeValidationException('این تخفیف قابل حذف نمی باشد');
        $specificDiscount->delete();

        ActivityLogHelper::deletedModel('تخفیف ویژه با موفقیت حذف شد', $specificDiscount);

        return redirect()->back()->with('success', 'تخفیف ویژه با موفقیت حذف شد');
    }

    // =================================================================================================================
    // Type ============================================================================================================
    // =================================================================================================================

    public function TypeIndex($specificDiscount)
    {
        $specificDiscount = SpecificDiscount::findOrFail($specificDiscount);
        $types = $specificDiscount->types;
        $types->load('items');
        return response()->success('تخفیف های ویژه', compact('types'));
    }
    public function TypeShow($specificDiscount, $id)
    {
        $specificDiscount = SpecificDiscount::findOrFail($specificDiscount);
        $types = $specificDiscount->types;
        return response()->success('لیست', compact('types'));
    }
    public function TypeStore($specificDiscount, Request $request)
    {
        $request->validate([
            'discount' => 'nullable|integer',
            'discount_type' => ['nullable', Rule::in([Product::DISCOUNT_TYPE_PERCENTAGE, Product::DISCOUNT_TYPE_FLAT])],
        ]);
        $specificDiscount = SpecificDiscount::findOrFail($specificDiscount);
        $type = $specificDiscount->types()->create($request->all());
        return response()->success('با موفقیت ثبت شد', compact('type'));
    }
    public function TypeUpdate($specificDiscount, $id, Request $request)
    {
        $specificDiscount = SpecificDiscount::findOrFail($specificDiscount);
        $type = $specificDiscount->types()->where('id', $id)->firstOrFail();
        $type->fill($request->all());
        $type->save();
        return response()->success('با موفقیت ویرایش شد', compact('type'));
    }
    public function TypeDestroy($specificDiscount, $id)
    {
        if (!SpecificDiscount::is_deletable($specificDiscount)) {
            throw Helpers::makeValidationException('قابل حذف نمی باشد');
        }
        $specificDiscount = SpecificDiscount::findOrFail($specificDiscount);
        $type = $specificDiscount->types()->where('id', $id)->firstOrFail();
        $type->delete();
        return response()->success('با موفقیت حذف شد', compact('type'));
    }


    // =================================================================================================================
    // Item ============================================================================================================
    // =================================================================================================================

    public function ItemIndex($specificDiscount, $type)
    {
        $specificDiscount = SpecificDiscount::findOrFail($specificDiscount);
        $type = $specificDiscount->types()->where('id', $type)->firstOrFail();
        $items = $type->items;
        return response()->success('آیتم های تخفیف های ویژه', compact('items'));
    }
    public function ItemShow($specificDiscount, $type, $id)
    {
        $request = \Request();
        $specificDiscount = SpecificDiscount::findOrFail($specificDiscount);
        $specificType = $specificDiscount->types()->where('id', $type)->firstOrFail();
        $specificDiscountItem = $specificType->items()
            ->where('id', $id)
            ->firstOrFail();

        $products = [];
        $categories = [];

        switch ($specificDiscountItem->type) {
            case SpecificDiscountItem::TYPE_CATEGORY:
                $model_ids = explode(',', $specificDiscountItem->model_ids);
                $categories = Category::find($model_ids);
                foreach ($categories as $category) {
                    $here_products = $category->products;
                    foreach ($here_products as $here_product) {
                        $here_product?->makeHidden('images');
                        $here_product?->makeHidden('user_images');
                        $products[] = $here_product;
                    }
                }
                break;
            case SpecificDiscountItem::TYPE_PRODUCT:
                $model_ids = explode(',', $specificDiscountItem->model_ids);
                $here_products = Product::find($model_ids);
                foreach ($here_products as $here_product) {
                    $here_product?->makeHidden('images');
                    $here_product?->makeHidden('user_images');
                    $products[] = $here_product;
                }
                break;
        }

        $specificDiscountItem->products = $products;
        $specificDiscountItem->categories = $categories;

        return response()->success('لیست تخفیف های ویژه', compact('specificDiscountItem'));
    }
    public function ItemStore($specificDiscount, $type, SpecificDiscountItemStoreRequest $request)
    {
        $specificDiscount = SpecificDiscount::findOrFail($specificDiscount);
        $type = $specificDiscount->types()->where('id', $type)->firstOrFail();
        $item = $type->items()->create($request->all());
        return response()->success('آیتم با موفقیت ذخیره شد', compact('item'));
    }
    public function ItemUpdate($specificDiscount, $type, $id, SpecificDiscountItemStoreRequest $request)
    {
        $specificDiscount = SpecificDiscount::findOrFail($specificDiscount);
        $type = $specificDiscount->types()->where('id', $type)->firstOrFail();
        $item = $type->items()->where('id', $id)->firstOrFail();
        $item->fill($request->all());
        $item->save();
        return response()->success('آیتم با موفقیت ویرایش شد', compact('item'));
    }
    public function ItemDestroy($specificDiscount, $type, $id)
    {
        if (!SpecificDiscount::is_deletable($specificDiscount)) {
            throw Helpers::makeValidationException('قابل حذف نمی باشد');
        }
        $specificDiscount = SpecificDiscount::findOrFail($specificDiscount);
        $type = $specificDiscount->types()->where('id', $type)->firstOrFail();
        $item = $type->items()->where('id', $id)->firstOrFail();
        $item->delete();
        return response()->success('با موفقیت حذف شد', compact('item'));
    }


}
