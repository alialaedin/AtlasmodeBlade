<?php

namespace Modules\GiftPackage\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\GiftPackage\Entities\GiftPackage;
use Modules\GiftPackage\Http\Requests\Admin\GiftPackageStoreRequest;
use Modules\GiftPackage\Http\Requests\Admin\GiftPackageUpdateRequest;

class GiftPackageController extends Controller
{
    public function index()
    {
        $giftPackages = GiftPackage::query()->latest('id')->get();
        
        return view('giftpackage::admin.index', compact('giftPackages')); 
    }


    public function store(GiftPackageStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $giftPackage = GiftPackage::query()->create($request->all());
            $giftPackage->storeImage($request);
            ActivityLogHelper::storeModel('بسته بندی هدیه با موفقیت ثبت شد', $giftPackage);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getTraceAsString());
            return redirect()->back()->with('error', 'مشکلی در ثبت بسته بندی هدیه به وجود آمده است: ' . $exception->getMessage());
        }
        
        return redirect()->back()->with('success', 'بسته بندی هدیه با موفقیت ثبت شد');
    }

    public function sort(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'required|exists:gift_packages,id'
        ]);
        $order = 99;
        foreach ($request->input('orders') as $itemId) {
            $model = GiftPackage::query()->find($itemId);
            if (!$model) {
                continue;
            }
            $model->order = $order--;
            $model->save();
        }

        return response()->success('مرتب سازی با موفقیت انجام شد');
    }

    public function update(GiftPackageUpdateRequest $request, GiftPackage $giftPackage)
    {
        DB::beginTransaction();
        try {
            $giftPackage->update($request->all());
            $giftPackage->storeImage($request);
            ActivityLogHelper::updatedModel('بسته بندی هدیه با موفقیت به روزرسانی شد', $giftPackage);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getTraceAsString());
            return redirect()->back()->with('error', 'مشکلی در به روزرسانی بسته بندی هدیه به وجود آمده است: ' . $exception->getMessage());
        }

        return redirect()->back()->with('success', 'بسته بندی هدیه با موفقیت به روزرسانی شد');
    }



    public function destroy(GiftPackage $giftPackage)
    {
        $giftPackage->delete();
        ActivityLogHelper::deletedModel('بسته بندی هدیه با موفقیت حذف شد', $giftPackage);

        return redirect()->back()->with('success', 'بسته بندی هدیه با موفقیت حذف شد');
    }
}
