<?php

namespace Modules\Customer\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Rules\Base64Image;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\Deposit;
use Modules\Customer\Http\Requests\Customer\ChangePasswordRequest;
use Modules\Customer\Http\Requests\Customer\ProfileUpdateRequest;
use Modules\Newsletters\Entities\UsersNewsletters;
use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Area\Entities\Province;
use Throwable;

class ProfileController extends Controller
{
  private null|Authenticatable|Customer $user;

  public function __construct()
  {
    $this->middleware(function ($request, $next) {
      $this->user = auth()->user();

      return $next($request);
    });
  }

  public function myAccount()
  {
    /** @var Customer $customer */
    $customer = Auth::guard('customer')->user();
    $customer->loadCount('orders');
    $customer->load([
      'orders' => fn($q) => $q->with('items.product')
    ]);

    $addresses = $customer->addresses()
      ->select(['id', 'address', 'postal_code', 'mobile', 'customer_id', 'first_name', 'last_name', 'city_id'])
      ->with('city', fn ($q) => $q->select(['id', 'name', 'province_id']))
      ->latest('id')
      ->get();

    $customer['addresses'] = $addresses;
    $provinces = Province::getAllProvinces(true);

    return view('customer::customer.panel', compact([
      'customer',
      'provinces',
    ]));
  }

  public function edit()
  {
    $customer = $this->user;
    $customer->loadCommonRelations();

    return response()->success('دریافت اطلاعات پروفایل مشتری', compact('customer'));
  }

  public function update(ProfileUpdateRequest $request)
  {
    /** @var Customer $customer */
    $customer = auth('customer')->user();
    $validatedFields = $request->validated();
    unset($validatedFields['password']);
    $customer->fill($validatedFields);
    if ($request->filled('password')) {
      $customer->password = $request->input('password');
    }
    if ($request->newsletter) {
      UsersNewsletters::query()->firstOrCreate($request->only('email'));
    } else {
      $email = UsersNewsletters::query()->where('email', $request->email)->first();
      $email && $email->delete();
    }
    $customer->save();

    if ($request->hasFile('image')) {
      $customer->addImage($request->image);
    }

    return response()->success('پروفایل کاربری با موفقیت به روزرسانی شد', compact('customer'));
  }

  public function changePassword(ChangePasswordRequest $request)
  {
    $customer = $this->user;

    $customer->fill(['password' => $request->password])->save();

    return response()->success('کلمه عبور با موفقیت تغییر کرد.');
  }

  public function depositWallet(Request $request)
  {
    $request->validate([
      'amount' => 'required|integer|min:1000'
    ]);

    try {
      $deposit = Deposit::storeModel($request->amount);

      return $deposit->pay();
    } catch (Throwable $e) {
      Log::error($e->getTraceAsString());
      throw Helpers::makeValidationException('عملیات شارژ کیف پول ناموفق بود،لطفا دوباره تلاش کنید.' . $e->getMessage(), $e->getTrace());
    }
  }

  public function transactionsWallet(): \Illuminate\Http\JsonResponse
  {
    /** @var Customer $customer */
    $customer = auth()->user();
    $transactions = $customer->transactions()->latest();
    Helpers::applyFilters($transactions);
    $transactions = Helpers::paginateOrAll($transactions);

    return response()->success('گزارشات کیف پول شما', compact('transactions'));
  }

  public function uploadImage(Request $request)
  {
    $request->validate([
      'image' => ['required', 'string', new Base64Image()]
    ]);

    $image = $this->user->addImage($request->image);

    return response()->success('عکس پروفایل با موفقیت ویرایش شد', compact('image'));
  }

  public function walletBalance()
  {
    /** @var Customer $user */
    $user = Auth::user();

    return $user->balance;
  }
}
