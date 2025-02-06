<?php

namespace Modules\Coupon\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Core\Helpers\Helpers;
use Illuminate\View\View;
use Modules\Category\Entities\Category;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Http\Requests\Admin\CouponStoreRequest;
use Modules\Coupon\Http\Requests\Admin\CouponUpdateRequest;

class CouponController extends Controller
{

  public function index(): View
  {
    $coupons = Coupon::query()->latest('id')->filters()->paginate()->withQueryString();

    return view('coupon::admin.index', compact('coupons'));
  }

  public function create()
  {
    $categories = Category::query()->select(['id', 'title'])->get();

    return view('coupon::admin.create', compact('categories'));
  }

  public function store(CouponStoreRequest $request, Coupon $coupon): RedirectResponse
  {
    Helpers::toCarbonRequest(['start_date', 'end_date'], $request);
    $coupon->fill($request->all())->save();
    ActivityLogHelper::storeModel(' کد تخفیف ثبت شد', $coupon);

    return redirect()->route('admin.coupons.index')->with('success', 'کد تخفیف با موفقیت ایجاد شد');
  }

  public function edit(Coupon $coupon)
  {
    $categories = Category::query()->select(['id', 'title'])->get();

    return view('coupon::admin.edit', compact(['coupon', 'categories']));
  }

  public function update(CouponUpdateRequest $request, Coupon $coupon): RedirectResponse
  {
    Helpers::toCarbonRequest(['start_date', 'end_date'], $request);
    $coupon->update($request->all());
    ActivityLogHelper::updatedModel(' کد تخفیف بروزرسانی شد', $coupon);

    return redirect()->route('admin.coupons.index')->with('success', 'کد تخفیف با موفقیت بروزرسانی شد');
  }

  public function show(Coupon $coupon): View
  {
    return view('coupon::admin.show', compact('coupon'));
  }

  public function destroy(Coupon $coupon): RedirectResponse
  {
    $coupon->delete();
    ActivityLogHelper::deletedModel(' کد تخفیف حذف شد', $coupon);

    return redirect()->route('admin.coupons.index')->with('success', 'کد تخفیف با موفقیت حذف شد');
  }
}
