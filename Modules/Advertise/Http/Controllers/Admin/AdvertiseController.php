<?php

namespace Modules\Advertise\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Advertise\Entities\Advertise;
use Modules\Advertise\Http\Requests\Admin\AdvertiseUpdateRequest;
use Modules\Link\Http\Controllers\Admin\LinkController;

class AdvertiseController extends Controller
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
  
  public function index()
  {
    $advertisements = Advertise::getAdvertisementsForAdmin();
    return view('advertise::admin.advertise.index', compact('advertisements'));
  }

  public function edit(Advertise $advertise)
  {
    $linkables = $this->getAllLinkables();
    return view('advertise::admin.advertise.edit', compact(['advertise', 'linkables']));
  }

  public function update(AdvertiseUpdateRequest $request, Advertise $advertise)
  {
    Advertise::updateAdvertise($advertise, $request);
    return redirect()->route('admin.advertisements.index')->with('success', 'تبلیغ با موفقیت بروزرسانی شد.');
  }

  public function changeStatus(Advertise $advertise) {

    Advertise::changeStatus($advertise);
    return redirect()->back()->with('success', 'وضعیت بنر با موفقیت تغییر کرد.'); 
  }
}
