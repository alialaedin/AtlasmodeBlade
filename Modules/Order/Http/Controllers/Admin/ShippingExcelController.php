<?php

namespace Modules\Order\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Order\Entities\ShippingExcel;
use Modules\Order\Imports\ShippingExcelImport;

class ShippingExcelController extends BaseController
{
  public function index()
  {
    $shippingExcels = ShippingExcel::latest()
      ->filters()
      ->latest('id')
      ->paginate();

    return view('order::admin.shipping-excel.index', compact('shippingExcels'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'file' => 'required'
    ]);
    Excel::import(new ShippingExcelImport, $request->file('file'));

    return redirect()->back()->with('succes', 'با موفقیت اضافه شد');
  }

  public function destroy($id)
  {
    $shippingExcel = ShippingExcel::query()->findOrFail($id);
    $shippingExcel->delete();

    return redirect()->back()->with('succes', 'با موفقیت ویرایش شد');
  }

  public function multipleDelete(Request $request)
  {
    $ids = $request->ids;
    DB::table("shipping_excels")->whereIn('id', explode(",", $ids))->delete();

    return redirect()->back()->with('succes', 'گزینه های انتخابی با موفقیت حذف شدند');
  }
}
