<?php

namespace Modules\Specification\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Specification\Entities\Specification;
use Modules\Specification\Http\Requests\Admin\SpecificationSortRequest;
use Modules\Specification\Http\Requests\Admin\SpecificationStoreRequest;
use Modules\Specification\Http\Requests\Admin\SpecificationUpdateRequest;

class SpecificationController extends Controller
{
  public function index()
  {
    $specifications = Specification::query()
      ->orderByDesc('order')
      ->filters()
      ->paginate();

    return view('specification::admin.index', compact('specifications'));
  }

  public function create()
  {
    $types = Specification::getAvailableTypes();

    return view('specification::admin.create', compact('types'));
  }

  public function store(SpecificationStoreRequest $request)
  {
    $specification = Specification::query()->create($request->all());

    if (in_array($request->type, [Specification::TYPE_SELECT, Specification::TYPE_MULTI_SELECT]) && $request->values) {
      foreach ($request->values as $value) {
        $specification->values()->create([
          'value' => $value
        ]);
      }
    }

    ActivityLogHelper::storeModel(' مشخصه ثبت شد', $specification);

    return redirect()->route('admin.specifications.index')->with([
      'success' => 'مشخصه جدید با موفقیت ثبت شد'
    ]);
  }

  /**
   * Show the specified resource.
   * @param int $id
   * @return JsonResponse|View
   */
  public function show(Specification $specification)
  {
    return view('specification::admin.show', compact('specification'));
  }

  public function edit(Specification $specification)
  {
    $types = Specification::getAvailableTypes();
    $specification->load('values');

    return view('specification::admin.edit', compact(['specification', 'types']));
  }

  public function update(SpecificationUpdateRequest $request, Specification $specification)
  {
    $specification->load('values');
    $specification->update($request->all());
    if (in_array($request->type, [Specification::TYPE_SELECT, Specification::TYPE_MULTI_SELECT]) && $request->values) {
      $notDeleteValues = [];
      foreach ($request->values as $value) {
        /**
         * @var $specification Collection
         */
        $specValue = $specification->values->where('id', $value)->first();
        $specValue = $specValue ?: $specification->values->where('value', $value)->first();
        if ($specValue) {
          $notDeleteValues[] = $specValue->id;
          continue;
        }
        $v = $specification->values()->create([
          'value' => $value
        ]);
        $notDeleteValues[] = $v->id;
      }
      $specification->values()->whereNotIn('id', $notDeleteValues)->delete();
      $specification->load('values');
    }

    ActivityLogHelper::updatedModel(' مشخصه ویرایش شد', $specification);

    return redirect()->route('admin.specifications.index')->with([
      'success' => 'مشخصه با موفقیت ویرایش شد'
    ]);
  }

  public function sort(SpecificationSortRequest $request)
  {
    $order = 999999;
    foreach ($request->ids as $id) {
      $specification = Specification::query()->find($id);
      if (!$specification) continue;
      $specification->order = $order--;
      $specification->save();
    }

    if (request()->header('Accept') == 'application/json') {
      return response()->success('مرتب سازی با موفقیت انجام شد');
    }
  }

  public function destroy(Specification $specification)
  {
    $specification->delete();
    ActivityLogHelper::deletedModel(' مشخصه حذف شد', $specification);

    return redirect()->back()->with('success', 'مشخصه با موفقیت حذف شد');
  }
}
