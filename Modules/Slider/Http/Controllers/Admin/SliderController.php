<?php

namespace Modules\Slider\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Link\Http\Controllers\Admin\LinkController;
use Modules\Slider\Entities\Slider;
use Modules\Slider\Http\Requests\Admin\SliderSortRequest;
use Modules\Slider\Http\Requests\Admin\SliderStoreRequest;

class SliderController extends Controller
{
  private function getAllLinkables()
  {
    $linkableData = (new LinkController)->create();
    $dataArray = $linkableData->getData(true);

    $linkables = [];
    if ($dataArray['success'] && isset($dataArray['data']['linkables'])) {
      $linkables = $dataArray['data']['linkables'];
    }
    return $linkables;
  }

  public function index($group)
  {
    $sliders = Slider::getAllSlidersByGroup($group);
    return view('slider::admin.index', compact(['sliders', 'group']));
  }

  public function create($group)
  {
    $linkables = $this->getAllLinkables();
    return view('slider::admin.create', compact(['group', 'linkables']));
  }

  public function store(SliderStoreRequest $request)
  {
    Slider::createOrUpdate($request);
    return redirect()->route('admin.sliders.index', ['group' => $request->group])->with('success', 'اسلایدر با موفقیت ثبت شد');
  }

  public function edit(Slider $slider)
  {
    $linkables = $this->getAllLinkables();
    return view('slider::admin.edit', compact(['slider', 'linkables']));
  }

  public function update(SliderStoreRequest $request, Slider $slider)
  {
    Slider::createOrUpdate($request, $slider);
    return redirect()->route('admin.sliders.index', ['group' => $request->group])->with('success', 'اسلایدر با موفقیت بروز شد.');
  }

  public function sort(SliderSortRequest $request)
  {
    Slider::sort($request);
    return redirect()->back()->with('success', 'اسلایدر با موفقیت مرتب سازی شد.');
  }

  public function destroy(Slider $slider)
  {
    $slider->delete();
    ActivityLogHelper::deletedModel('اسلایدر حذف شد', $slider);
    return redirect()->back()->with('success', 'اسلایدر با موفقیت حذف شد.');
  }
}
