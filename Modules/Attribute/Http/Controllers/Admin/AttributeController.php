<?php

namespace Modules\Attribute\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Http\Requests\Admin\AttributeStoreRequest;
use Modules\Attribute\Http\Requests\Admin\AttributeUpdateRequest;

class AttributeController extends Controller
{
  public function index(): JsonResponse|View
  {
    $attributes = Attribute::latest('id')->filters()->paginate();
    $types = Attribute::getAvailableType();

    return view('attribute::admin.index', compact('attributes', 'types'));
  }
  public function create()
  {
    $attributes = Attribute::latest('id')->filters()->paginate();
    $types = Attribute::getAvailableType();

    return view('attribute::admin.create', compact('attributes', 'types'));
  }

  public function store(AttributeStoreRequest $request)
  {
    $attribute = Attribute::query()->create($request->all());
    ActivityLogHelper::storeModel('ویژگی ثبت شد', $attribute);

    if ($request->type === 'select' && $request->values) {
      foreach ($request->values as $value) {
        if (!is_null($value)) {
          $attribute->values()->create([
            'value' => $value
          ]);
        }
      }
    }

    return redirect()->route('admin.attributes.index')->with([
      'success' => 'ویژگی با موفقیت ثبت شد'
    ]);
  }

  public function edit(Attribute $attribute)
  {
    $types = Attribute::getAvailableType();

    return view('attribute::admin.edit', compact('attribute', 'types'));
  }

  public function update(AttributeUpdateRequest $request, Attribute $attribute)
  {
    // $attribute->update($request->validated());
    // ActivityLogHelper::updatedModel('ویژگی بروز شد', $attribute);

    // if ($attribute->type === 'select') {
    //     foreach ($request->input('values', []) as $value) {
    //         // If already exists don't add
    //         if (!$attribute->values()->where('value', $value)->exists()) {
    //             $attribute->values()->create([
    //                 'value' => $value
    //             ]);
    //         }
    //     }

    //     // مقادیری که ویرایش شده
    //     foreach ($request->input('edited_values', []) as $editedValue) {
    //         $attributeValue = $attribute->values()->find($editedValue['id']);
    //         if (!$attributeValue) {
    //             continue;
    //         }
    //         $attributeValue->value = $editedValue['value'];
    //         $attributeValue->save();
    //     }

    //     // مقادیری که حذف شده
    //     foreach ($request->input('deleted_values', []) as $editedValue) {
    //         $attributeValue = $attribute->values()->find($editedValue['id']);
    //         if (!$attributeValue) {
    //             continue;
    //         }
    //         $attributeValue->delete();
    //     }
    // }

    $attribute->load('values');
    $attribute->update($request->all());
    if (in_array($request->type, [Attribute::TYPE_SELECT]) && $request->values) {
      $notDeleteValues = [];
      foreach ($request->values as $value) {
        /**
         * @var $specification Collection
         */
        $attrValue = $attribute->values->where('id', $value)->first();
        $attrValue = $attrValue ?: $attribute->values->where('value', $value)->first();
        if ($attrValue) {
          $notDeleteValues[] = $attrValue->id;
          continue;
        }
        $v = $attribute->values()->create([
          'value' => $value
        ]);
        $notDeleteValues[] = $v->id;
      }
      $attribute->values()->whereNotIn('id', $notDeleteValues)->delete();
      $attribute->load('values');
    }

    return redirect()->route('admin.attributes.index')->with([
      'success' => 'ویژگی با موفقیت به روزرسانی شد'
    ]);
  }

  public function destroy(Attribute $attribute)
  {
    $attribute->delete();
    ActivityLogHelper::deletedModel('ویژگی حذف شد', $attribute);

    return redirect()->route('admin.attributes.index')->with([
      'success' => 'ویژگی با موفقیت حذف شد'
    ]);
  }
}
