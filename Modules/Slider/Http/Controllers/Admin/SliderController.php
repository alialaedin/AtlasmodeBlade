<?php

namespace Modules\Slider\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Link\Http\Controllers\Admin\LinkController;
use Modules\Slider\Entities\Slider;
use Modules\Slider\Http\Requests\Admin\SliderSortRequest;
use Modules\Slider\Http\Requests\Admin\SliderStoreRequest;

class SliderController extends Controller
{
  public function groups()
  {
    $group_labels = collect(config('slider.groups'))->map(function ($name) {
      if (Lang::has('core::groups.' . $name)) {
        return trans('core::groups.' . $name);
      }
      return false;
    });

    return view('slider::admin.groups', ['groups' => config('slider.groups'), 'group_labels' => $group_labels]);
  }

  public function index($group)
  {
    $linkableData = (new LinkController)->create();

    $dataArray = $linkableData->getData(true);

    if ($dataArray['success'] && isset($dataArray['data']['linkables'])) {
      $linkables = $dataArray['data']['linkables'];
    }
    $sliders = Slider::orderBy('order', 'DESC')->whereGroup($group)->get();

    return view('slider::admin.index', compact('sliders', 'linkables', 'group'));
  }

  public function store(SliderStoreRequest $request)
  {
    $slider = Slider::query()->create($request->all());
    $slider->addImage($request->file('image'));
    $slider->load('media');
    $slider->refresh();
    ActivityLogHelper::storeModel('اسلایدر ثبت شد', $slider);

    return redirect()->route('admin.sliders.groups.index', $slider->group)
      ->with('success', 'اسلایدر با موفقیت اضافه شد.');
  }

  public function update(SliderStoreRequest $request, $sliderId)
  {
    $slider = Slider::query()->findOrFail($sliderId);
    if ($request->linkable_id) {
      $slider->link = null;
    }
    $slider->update($request->all());

    $slider->updateFiles($request->images, 'image');
    $slider->load('media');
    ActivityLogHelper::updatedModel('اسلایدر بروز شد', $slider);

    return redirect()->route('admin.sliders.groups.index', $slider->group)
      ->with('success', 'اسلایدر با موفقیت بروز شد.');
  }
  public function sort(SliderSortRequest $request)
  {
    $idsFromRequest = $request->input('orders');
    $c = 999999;
    foreach ($idsFromRequest as $id) {
      $slider = Slider::query()->find($id);
      $slider->order = $c--;
      $slider->save();
    }

    return redirect()->route('admin.sliders.groups.index', $request->group)
      ->with('success', 'اسلایدر با موفقیت مرتب سازی شد.');
  }


  public function destroy($sliderId)
  {
    $slider = Slider::query()->findOrFail($sliderId);
    $group = $slider->group;
    $slider->delete();
    ActivityLogHelper::deletedModel('اسلایدر حذف شد', $slider);

    return redirect()->route('admin.sliders.groups.index', $group)
      ->with('success', 'اسلایدر با موفقیت حذف شد.');
  }
}
