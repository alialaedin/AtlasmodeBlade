<?php

namespace Modules\Color\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Color\Entities\ColorRange;
use Modules\Color\Http\Requests\Admin\ColorRangeSortRequest;
use Modules\Color\Http\Requests\Admin\ColorRangeStoreRequest;
use Modules\Color\Http\Requests\Admin\ColorRangeUpdateRequest;

class ColorRangeController extends Controller
{
  public function index()
	{
		$colorRanges = ColorRange::getColorRangesForAdmin();
		return view('color::admin.color-range.index', compact('colorRanges'));
	}

	public function create()
	{
		return view('color::admin.color-range.create');
	}

	public function store(ColorRangeStoreRequest $request)
	{
		ColorRange::storeOrUpdate($request);
		return redirect()->route('admin.color-ranges.index')->with('success', 'طیف رنکی با موفقیت ثبت شد');
	}

	public function edit(ColorRange $colorRange)
	{
		return view('color::admin.color-range.edit', compact('colorRange'));
	} 

	public function update(ColorRangeUpdateRequest $request, ColorRange $colorRange)
	{
		ColorRange::storeOrUpdate($request, $colorRange);
		return redirect()->route('admin.color-ranges.index')->with('success', 'طیف رنکی با موفقیت بروز شد');
	}

	public function destroy(ColorRange $colorRange)
	{
		$colorRange->delete();
		return redirect()->route('admin.color-ranges.index')->with('success', 'طیف رنکی با موفقیت جذف شد');
	} 

	public function sort(ColorRangeSortRequest $request)
	{
		ColorRange::sort($request);
		return redirect()->route('admin.color-ranges.index')->with('success', 'طیف های رنگی با موفقیت مرتب سازی شدند');
	} 
}
