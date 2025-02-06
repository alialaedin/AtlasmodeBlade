<?php

namespace Modules\Store\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Store\Entities\VarietyTransferLocation;

class VarietyTransferLocationController extends Controller
{

    public function index()
    {
        $locations = VarietyTransferLocation::query()
            ->where('is_delete', '=', false)
            ->get();

        return response()->success('لیست مکان ها', compact('locations'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string'
        ]);
        $newLocation = VarietyTransferLocation::create([
            'title' => $request->title,
            'is_delete' => false
        ]);
        return response()->success('مکان جدید با موفقیت ایجاد شد', compact('newLocation'));
    }



    public function update(Request $request, $id)
    {
        $request->validate(rules: [
            'title' => 'required|string'
        ]);
        $location = VarietyTransferLocation::findOrFail($id);
        $location->title = $request->title;
        $location->save();
        return response()->success('مکان با موفقیت ویرایش شد', compact('location'));
    }

    public function destroy($id)
    {
        $location = VarietyTransferLocation::findOrFail($id);
        $location->is_delete = true;
        $location->save();
        return response()->success('مکان با موفقیت حذف شد', compact('location'));
    }
}
