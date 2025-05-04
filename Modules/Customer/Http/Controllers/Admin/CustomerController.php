<?php

namespace Modules\Customer\Http\Controllers\Admin;


use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Area\Entities\City;
use Modules\Area\Entities\Province;
use Modules\Core\Classes\Transaction;
use Modules\Customer\Entities\Address;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\SmsToken;
use Modules\Customer\Http\Requests\Admin\CustomerDepositRequest;
use Modules\Customer\Http\Requests\Customer\CustomerStoreRequest;
use Modules\Customer\Http\Requests\Customer\CustomerUpdateRequest;
use Modules\Customer\Notifications\DepositWalletSuccessfulNotification;
use Modules\Order\Entities\Order;

class CustomerController extends Controller
{
	public function index()
	{
		$customers = Customer::query()->filters()->latest('id')->paginate()->withQueryString();

		return view('customer::admin.customer.index', compact('customers'));
	}

	public function create()
	{
		return view('customer::admin.customer.create');
	}

	public function search()
	{
		$q = \request('q');
		if (empty($q)) return response()->error('ورودی نامعتبر است');

		$customers = Customer::query()->select(['id', 'first_name', 'last_name', 'mobile']);

		if (is_numeric($q)) {
			$customers->orWhere('id', $q);
			$customers->orWhere('mobile', 'LIKE', '%' . $q . '%');
		} else {
			$customers->orWhere('first_name', 'LIKE', '%' . $q . '%');
			$customers->orWhere('last_name', 'LIKE', '%' . $q . '%');
		}

		$count = $customers->count();
		$customers = $customers->take(20)->get();

		return response()->success('', compact('customers', 'count'));
	}

	public function show($id)
	{
		$customer = Customer::query()
			->select(['id', 'first_name', 'last_name', 'mobile', 'email', 'birth_date', 'card_number', 'national_code', 'gender'])
			->with([
				'orders' => fn($oQuery) => $oQuery->with('items')->orderByDesc('id')
			])
			->findOrFail($id);
		
		$provinces = Province::query()->select('id', 'name')->active()->get();
		$cities = City::select('id', 'name', 'province_id')->get();
		$transactions = $customer->wallet->transactions->sortByDesc('id');

		$walletStatistics = [
			'withdrawsCount' => $transactions->where('type', 'withdraw')->count() ?? 0,
			'withdrawsAmount' => abs($transactions->where('type', 'withdraw')->sum('amount')) ?? 0,
			'dipositsCount' => $transactions->where('type', 'deposit')->count() ?? 0,
			'dipositsAmount' => abs($transactions->where('type', 'deposit')->sum('amount')) ?? 0,
		];

		$orderStatistics = Order::getOrderStatisticsForCustomer($customer);

		return view('customer::admin.customer.show', compact(['customer', 'transactions', 'cities', 'provinces', 'walletStatistics', 'orderStatistics']));
	}

	public function depositCustomerWallet(CustomerDepositRequest $request)
	{
		/**
		 * @var Customer $customer 
		 */
		$customer = Customer::query()->whereKey($request->customer_id)->first();
		$full_name = $customer->first_name . ' ' . $customer->last_name;
		$customer->deposit($request->amount, [
			'admin_id' => auth()->user()->id,
			'tracking_code' => $request->tracking_code,
			'description' => $request->description ?? " افزایش موجودی حساب توسط ادمین با شناسه " . auth()->user()->id
		]);

		ActivityLogHelper::updatedModel('افزایش موجودی کیف پول', $customer);
		$customer->notify(new DepositWalletSuccessfulNotification($customer, $request->amount));

		return redirect()
			->route('admin.customers.show', $request->customer_id)
			->with('success', "کیف پول مشتری {$full_name} با موفقیت به مبلغ {$request->amount} افزایش یافت");
	}

	public function store(CustomerStoreRequest $request)
	{
		try {
			DB::beginTransaction();
			$customer = Customer::query()->create($request->all());
			ActivityLogHelper::storeModel('مشتری ایجاد شد', $customer);
			if (!SmsToken::where('mobile', $customer->mobile)->exists()) {
				SmsToken::query()->create([
					'mobile' => $customer->mobile,
					'token' => random_int(10000, 99999),
					'expired_at' => Carbon::now()->addHours(24),
					'verified_at' => now()
				]);
			}
			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
			return redirect()->back()->with('error', 'خطا در ثبت مشتری.' . $e->getMessage());
		}

		return redirect()->route('admin.customers.index')->with('success', 'مشتری با موفقیت ایجاد شد.');
	}
	
	public function edit(Customer $customer)
	{
		return view('customer::admin.customer.edit', compact('customer'));
	}

	public function update(CustomerUpdateRequest $request, Customer $customer)
	{
		$customer->update($request->all());
		ActivityLogHelper::updatedModel('مشتری بروز شد', $customer);
		
		return redirect()->route('admin.customers.index')->with('success', 'مشتری با موفقیت به روزرسانی شد.');
	}

	public function destroy(Customer $customer)
	{
		$customer->delete();
		ActivityLogHelper::deletedModel('مشتری حذف شد', $customer);

		return redirect()->route('admin.customers.index')->with('success', 'مشتری با موفقیت حذف شد.');
	}

	public function getTransactionsExcel()
	{
		$transactions = Transaction::query()->where('payable_type', Customer::class)->select('id', 'type', 'confirmed', 'meta', 'created_at')
			->latest('id')->get();
		if (\request()->header('accept') == 'x-xlsx') {
			$final_list = [];
			foreach ($transactions as $transaction) {
				$final_list[] = [
					$transaction->id,
					$transaction->type = 'deposit' ? 'واریز به کیف پول' : 'برداشت از کیف پول',
					$transaction->confirmed = 1 ? 'موفقیت امیز' : 'ناموفق',
					$description =  isset($transaction->meta['description']) ? $transaction->meta['description'] : ' ',
					\verta($transaction->created_at)->format('y.m.d H:i'),
				];
			}
			return Excel::download(
				new CustomerTransactionExport($final_list),
				__FUNCTION__ . '-' . now()->toDateString() . '.xlsx'
			);
		} else {

			return  response('لطفا فرمت درست را در هدر وارد کنید');
		}
	}

	public function transactionsWallet()
	{
		$transactions = Transaction::query()
			->where('payable_type', Customer::class)
			->filters()
			->latest('id')
			->with('deposit', fn($q) => $q->select('id', 'amount'))
			->paginate();

		$allTotals = Transaction::query()
			->where('payable_type', Customer::class)
			->select('type', DB::raw('sum(amount) as total'))
			->groupBy('type')
			->pluck('total', 'type');

		$total['deposit'] = $allTotals->get('deposit', 0);
		$total['withdraw'] = $allTotals->get('withdraw', 0);

		return view('customer::admin.wallet.transactions', compact(['transactions', 'total']));
	}

	public function withdrawCustomerWallet(Request $request)
  {
    $request->merge([
      'amount' => str_replace(',', '', $request->input('amount')),
    ]);
    $request->validate([
      'customer_id' => 'required|integer|exists:customers,id',
      'amount' => 'required|integer|min:1000',
      'description' => 'nullable|string'
    ]);
    $admin = auth()->user();
    /** @var Customer $customer */
    $customer = Customer::query()->find($request->customer_id);
    $customer->withdraw($request->amount, [
      'description' => $request->description ?? "کاهش موجودیی کیف پول توسط ادمین با شناسه {$admin->id}"
    ]);

    return redirect()->route('admin.customers.show', $request->customer_id)->with('success', 'مشتری با موفقیت حذف شد.');
  }

	public function getAddresses($customer_id)
  {
    $addresses = Address::query()
      ->select(['id', 'customer_id', 'address', 'postal_code', 'city_id'])
			->with('city.province')
      ->where('customer_id', $customer_id)
      ->get();

    return response()->success('لیست آدرس های مشتری', compact('addresses'));
  }
}
