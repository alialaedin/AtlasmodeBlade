<?php

namespace Modules\Customer\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Rules\Base64Image;
use Modules\Customer\Entities\Customer;
use Modules\Order\Entities\Order;
use Modules\Customer\Entities\Deposit;
use Modules\Customer\Http\Requests\Customer\ChangePasswordRequest;
use Modules\Customer\Http\Requests\Customer\ProfileUpdateRequest;
use Modules\Newsletters\Entities\UsersNewsletters;
use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Area\Entities\Province;
use Modules\Customer\Entities\Withdraw;
use Modules\Invoice\Entities\Payment;
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

    $addresses = $customer->addresses()
      ->select(['id', 'address', 'postal_code', 'mobile', 'customer_id', 'first_name', 'last_name', 'city_id'])
      ->with('city', fn ($q) => $q->select(['id', 'name', 'province_id']))
      ->latest('id')
      ->get();
    
    $orders = $customer->orders()
      ->select(['id', 'customer_id', 'discount_amount', 'total_amount', 'shipping_amount', 'status', 'created_at', 'address_id'])
      ->with([
        'items' => function ($iQuery) {
          $iQuery->active();
          $iQuery->select(['id', 'order_id', 'status', 'variety_id', 'product_id', 'discount_amount', 'quantity', 'amount']);
          $iQuery->with([
            'variety' => fn ($vQuery) => $vQuery->select(['id', 'product_id'])->with('attributes'),
            'product' => fn ($pQuery) => $pQuery->select(['id', 'title'])->with('media')
          ]);
        },
        'address' => function ($aQuery) {
          $aQuery->select(['id', 'address', 'first_name', 'last_name', 'mobile']);
        }
      ])
      ->latest('id')
      ->get()
      ->each(function($order) {
        $order['persian_created_at'] = verta($order->created_at)->format('%d %B %Y');
        foreach ($order->items as $item) {
          $item->product->append('main_image');
        }
      });

    $favorites = $customer->favorites()
      ->select(['products.id', 'products.title', 'products.slug', 'products.image_alt'])
      ->with([
        'media',
        'varieties' => fn($q) => $q->select(['id', 'product_id', 'price', 'discount', 'discount_until', 'discount_type']),
      ])
      ->get()
      ->each(function ($p) {
        $p->append(['main_image', 'final_price']);
        $p->makeHidden('varieties');
      });

    $transactions = $customer->transactions()
      ->select(['id', 'created_at', 'meta', 'payable_id', 'payable_type', 'type', 'amount', 'confirmed'])
      ->latest('id')
      ->get()
      ->each(fn ($tr) => $tr['jalali_created_at'] = verta($tr->created_at)->format('H:i Y/m/d'));

    $withdraws = Withdraw::query()
      ->where('customer_id', $customer->id)
      ->select(['id', 'amount', 'status', 'created_at'])
      ->get()
      ->each(fn ($tr) => $tr['jalali_created_at'] = verta($tr->created_at)->format('H:i Y/m/d'));;

    $customer['addresses'] = $addresses;
    $customer['favorites'] = $favorites;
    $customer['orders'] = $orders;
    $customer['transactions'] = $transactions;
    $customer['withdraws'] = $withdraws;

    $orderStatistics = Order::getOrderStatisticsForCustomer($customer);
    $provinces = Province::getAllProvinces(true);
    $gateways = Payment::getAvailableDriversForFront();

    return view('customer::customer.panel', compact([
      'customer',
      'provinces',
      'orderStatistics',
      'favorites',
      'gateways'
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
    $request->merge(['amount' => str_replace(',', '', $request->amount)]);
    $request->validate([
      'amount' => 'required|integer|min:1000'
    ]);
    try {
      $deposit = Deposit::storeModel($request->amount);
      return $deposit->pay();
    } catch (Throwable $e) {
      Log::error($e->getTraceAsString());
      throw Helpers::makeValidationException('عملیات شارژ کیف پول ناموفق بود،لطفا دوباره تلاش کنید.' . $e->getMessage());
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
