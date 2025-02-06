<?php

namespace Modules\Report\Http\Controllers\Admin;

use Bavix\Wallet\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Area\Entities\Province;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\Deposit;
use Modules\Customer\Entities\Withdraw;
use Modules\Home\Entities\SiteView;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderItem;
use Modules\Product\Entities\Product;
use Modules\Product\Services\ProductsCollectionService;
use Modules\Report\Exports\CustomerReportExport;
use Modules\Report\Exports\OrderReportExport;
use Modules\Report\Exports\ProductReportExport;
use Modules\Report\Exports\SiteViewReportExport;
use Modules\Report\Exports\VarietyBalanceReportExport;
use Modules\Report\Exports\VarietyReportExport;
use Modules\Report\Exports\WalletReportExport;

class NewReportController extends Controller
{
  public function customers(Request $request)
  {
    //   if (\request('start_date') === null) {
    //     \request()->merge(['start_date' => now()->subDay()->startOfDay()->format('Y-m-d 00:00:00')]);
    //   }

    //   if (\request('end_date') === null) {
    //     \request()->merge(['end_date' => now()->startOfDay()->format('Y-m-d 23:59:59')]);
    //   }
    $customersQuery = Customer::query()
      ->select(['id', 'mobile', 'first_name', 'last_name'])
      ->with('orders', fn($q) => $q->select(['id', 'customer_id', 'total_invoices_amount', 'created_at'])->orderByDesc('id'))
      ->withCount('orders')
      ->filters()
      ->latest('id');

    if ($request->header('accept') === 'x-xlsx') {
      $currentPage = $request->input('page', 1);
      $perPage = request('perPage', 15);

      $customers = $customersQuery->paginate($perPage, ['*'], 'page', $currentPage);

      return Excel::download(new CustomerReportExport($customers), 'customers-' . now()->toDateString() . '.xlsx');
    }

    $customers = $customersQuery->paginate(request('perPage', 15))->withQueryString();
    $maxInvoiceAmount = Order::max('total_invoices_amount');
    $maxItemCount = Order::max('total_items_count');
    $provinces = Province::query()->active()->select('id', 'name')->with('cities')->get();

    return view('report::admin.customers.index', compact(['customers', 'maxInvoiceAmount', 'maxItemCount', 'provinces']));
  }

  public function products(Request $request)
  {
    $productsQuery = Product::query()
      ->filters()
      ->select(['id', 'title', 'created_at']);

    if ($request->header('accept') === 'x-xlsx') {
      $currentPage = $request->input('page', 1);
      $perPage = request('perPage', 50);
      $products = $productsQuery->paginate($perPage, ['*'], 'page', $currentPage);
      $products->getCollection()->transform(function ($product) {
        $product->setAppends(['sales_count', 'total_sale_amount']);
        return $product;
      });

      return Excel::download(new ProductReportExport($products), 'products-' . now()->toDateString() . '.xlsx');
    }
    $products = $productsQuery->paginate(50)->withQueryString();
    $salesReport = Order::query()
      ->selectRaw('SUM(total_invoices_amount) as total_sales')
      ->selectRaw('SUM(total_quantity) as total_quantity_sales')
      ->first()
      ->toArray();

    $totals['total_sales']['label'] = 'جمع فروش';
    $totals['total_sales']['amount'] = $salesReport['total_sales'];

    $totals['total_quantity_sales']['label'] = 'جمع تعداد';
    $totals['total_quantity_sales']['amount'] = $salesReport['total_quantity_sales'];

    return view('report::admin.products.index', compact('products', 'totals'));
  }

  public function orders(Request $request)
  {
    $ordersQuery = Order::query()
      ->select([
        'id',
        'total_items_count',
        'discount_on_order',
        'discount_on_coupon',
        'discount_on_items',
        'discount_amount',
        'shipping_amount',
        'total_invoices_amount',
        'total_items_amount',
        'total_quantity',
        'customer_id',
        'created_at',
      ])
      ->when(request()->filled('start_date'), fn($q) => $q->whereDate('created_at', '>=', request('start_date')))
      ->when(request()->filled('end_date'), fn($q) => $q->whereDate('created_at', '<=', request('end_date')))
      ->latest('id');
    if ($request->header('accept') === 'x-xlsx') {
      $currentPage = $request->input('page', 1);
      $perPage = request('perPage', 15);

      $orders = $ordersQuery->paginate($perPage, ['*'], 'page', $currentPage);

      return Excel::download(new OrderReportExport($orders), 'orders-' . now()->toDateString() . '.xlsx');
    }
    $isRequestHasStartDate = request()->has('start_date') && request()->filled('start_date');
    $isRequestHasEndDate = request()->has('end_date') && request()->filled('end_date');

    $totals = $isRequestHasStartDate || $isRequestHasEndDate ? $this->calculateOrdersReportTotals((clone $ordersQuery)->get()) : [];
    $orders = $ordersQuery->paginate(request('perPage', 15))->withQueryString();
    $provinces = Province::query()->active()->select('id', 'name')->with('cities')->get();

    return view('report::admin.orders.index', compact(['orders', 'provinces', 'totals']));
  }

  private function calculateOrdersReportTotals($orders)
  {
    $totals = [
      'gross_sales' => ['label' => 'فروش ناخالص (بدن تخفیف + بدون حمل و نقل)', 'amount' => 0, 'col' => 'col-xl-3'],
      'sum_discount_on_order' => ['label' => 'تخفیف بدون کد', 'amount' => 0, 'col' => 'col-xl-3'],
      'sum_discount_on_coupon' => ['label' => 'تخفیف با کد', 'amount' => 0, 'col' => 'col-xl-3'],
      'sum_total_discount' => ['label' => 'جمع تخفیف', 'amount' => 0, 'col' => 'col-xl-3'],
      'sum_shipping_amount' => ['label' => 'هزینه حمل و نقل', 'amount' => 0, 'col' => 'col-xl-6'],
      'sum_total_invoices_amount' => ['label' => 'جمع کل فاکتور ها (فروش ناخالص + حمل و نقل + جمع تخفیف)', 'amount' => 0, 'col' => 'col-xl-6'],
      'sum_total_quantity' => ['label' => 'مجموع اقلام سفارشات', 'amount' => 0, 'col' => 'col-xl-4'],
      // 'sum_total_items_count' => ['label' => 'تعداد اقلام سفارشات','amount' => 0,'col' => 'col-xl-4'],
      'count_orders' => ['label' => 'تعداد سفارشات', 'amount' => 0, 'col' => 'col-xl-4'],

      'pay_by_wallet_main_balance' => ['label' => 'تسویه از کیف پول اصلی', 'amount' => 0, 'col' => 'col-xl-3'],
      'pay_by_wallet_gift_balance' => ['label' => 'تسویه از کیف پول هدیه', 'amount' => 0, 'col' => 'col-xl-3'],
      'total_paid_from_gateway' => ['label' => 'تسویه از درگاه', 'amount' => 0, 'col' => 'col-xl-3'],
      'sum_total_paid' => ['label' => 'جمع کل تسویه', 'amount' => 0, 'col' => 'col-xl-3'],

      'total_wallet_deposit' => ['label' => 'شارژ کیف پول', 'amount' => 0, 'col' => 'col-xl-3'],
      'total_main_wallet_balance' => ['label' => 'مانده کیف پول اصلی', 'amount' => 0, 'col' => 'col-xl-3'],
      'total_gift_wallet_balance' => ['label' => 'مانده کیف پول هدیه', 'amount' => 0, 'col' => 'col-xl-3'],
      'total_wallet_balance' => ['label' => 'مانده کیف پول ها', 'amount' => 0, 'col' => 'col-xl-3'],
    ];

    foreach ($orders as $order) {
      $totals['gross_sales']['amount'] += $order->total_items_amount;
      $totals['sum_discount_on_order']['amount'] += $order->discount_on_order;
      $totals['sum_discount_on_coupon']['amount'] += $order->discount_on_coupon;
      $totals['sum_total_discount']['amount'] += $order->discount_on_order + $order->discount_on_coupon;
      $totals['count_orders']['amount'] += 1;
      $totals['sum_shipping_amount']['amount'] += $order->shipping_amount;
      $totals['sum_total_invoices_amount']['amount'] += $order->total_invoices_amount;
      // $totals['sum_total_items_count']['amount'] += $order->total_items_count;
      $totals['sum_total_quantity']['amount'] += $order->total_quantity;
      $totals['pay_by_wallet_main_balance']['amount'] += $order->pay_by_wallet_main_balance;
      $totals['pay_by_wallet_gift_balance']['amount'] += $order->pay_by_wallet_gift_balance;
      $totals['total_paid_from_gateway']['amount'] += $order->total_invoices_amount - $order->pay_by_wallet_main_balance + $order->pay_by_wallet_gift_balance;
      $totals['sum_total_paid']['amount'] += $order->total_invoices_amount;
    }

    $totals['total_wallet_deposit']['amount'] = Deposit::query()
      ->where('status', "success")
      ->when(request()->filled('start_date'), fn($q) => $q->whereDate('created_at', '>=', request('start_date')))
      ->when(request()->filled('end_date'), fn($q) => $q->whereDate('created_at', '<=', request('end_date')))
      ->sum('amount');

    $walletDetails = DB::table('wallets')
      ->select(DB::raw('sum(balance) as sum_balance, sum(gift_balance) as sum_gift_balance'))
      ->first();

    $totals['total_main_wallet_balance']['amount'] = $walletDetails->sum_balance;
    $totals['total_gift_wallet_balance']['amount'] = $walletDetails->sum_gift_balance;
    $totals['total_wallet_balance']['amount'] = $walletDetails->sum_balance + $walletDetails->sum_gift_balance;

    return $totals;
  }

  public function varieties(Request $request)
  {
    $productId = request()->has('product_id') && request()->filled('product_id') ? request('product_id') : null;
    $varietyId = request()->has('variety_id') && request()->filled('variety_id') ? request('variety_id') : null;

    $service = new ProductsCollectionService();

    $products = $service->getProductsCollection();
    $varieties = $service->getVarietiesCollection()
      ->when($productId, fn($q) => $q->where('product_id', $productId))
      ->when($varietyId, fn($q) => $q->where('id', $varietyId))
      ->filter(fn($variety) => $service->isVarietyIdActive($variety->id));
    if ($request->header('accept') === 'x-xlsx') {
      $varieties->transform(function ($variety) {
        $variety->setAppends(['title_showcase', 'total_sales']);
        return $variety;
      });
      return Excel::download(new VarietyReportExport($varieties), 'varieties-' . now()->toDateString() . '.xlsx');
    }
    return view('report::admin.varieties.varieties', compact('varieties', 'products'));
  }

  public function varietiesBalance(Request $request)
  {
    $service = new ProductsCollectionService();
    $varieties = $service->getVarietiesCollection()->filter(fn($variety) => $service->isVarietyIdActive($variety->id));
    if ($request->header('accept') === 'x-xlsx') {
      $varieties->transform(function ($variety) {
        $variety->setAppends(['title_showcase', 'store_balance']);
        return $variety;
      });
      return Excel::download(new VarietyBalanceReportExport($varieties), 'varieties-' . now()->toDateString() . '.xlsx');
    }
    return view('report::admin.varieties.varieties-balance', compact('varieties'));
  }

  public function wallets(Request $request)
  {
    $walletsCalculations = Wallet::query()
      ->selectRaw("SUM(balance) as total_balance, SUM(gift_balance) as total_gift_balance")
      ->where('holder_type', "Modules\Customer\Entities\Customer")
      ->first();


    $totals = [
      'all_customers_wallet_main_balance' => ['label' => 'موجودی اصلی تمامی مشتریان', 'amount' => ($walletsCalculations->total_balance - $walletsCalculations->total_gift_balance)],
      'all_customers_wallet_gift_balance' => ['label' => 'موجودی هدیه تمامی مشتریان', 'amount' => $walletsCalculations->total_gift_balance],
    ];


    $customersQuery = Customer::query()
      ->selectRaw("
                customers.id, customers.first_name, customers.last_name, customers.mobile,
                COUNT(deposits.id) as deposits_count,
                COUNT(withdraws.id) as withdraws_count,
                (wallets.balance - wallets.gift_balance) as main_balance,
                wallets.gift_balance
            ")
      ->leftJoin('wallets', function ($join) {
        $join->on('customers.id', '=', 'wallets.holder_id')->where('wallets.holder_type', Customer::class);
      })
      ->leftJoin('deposits', function ($join) {
        $join->on('customers.id', '=', 'deposits.customer_id')->where('deposits.status', Deposit::STATUS_SUCCESS);
      })
      ->leftJoin('withdraws', function ($join) {
        $join->on('customers.id', '=', 'withdraws.customer_id')->where('withdraws.status', Withdraw::STATUS_PAID);
      })
      ->groupByRaw("customers.id, customers.first_name, customers.last_name, customers.mobile")
      ->orderByDesc('main_balance');

    if ($request->header('accept') === 'x-xlsx') {
      $currentPage = $request->input('page', 1);
      $perPage = request('perPage', 15);

      $customers = $customersQuery->paginate($perPage, ['*'], 'page', $currentPage);

      return Excel::download(new WalletReportExport($customers), 'customer-wallets-' . now()->toDateString() . '.xlsx');
    }
    $customers = $customersQuery->paginate(request('perPage', 15))->withQueryString();

    return view('report::admin.wallets.index', compact('customers', 'totals'));
  }

  public function siteviews(Request $request)
  {
    $siteviews = SiteView::query()
      ->select('date')
      ->selectRaw('SUM(count) as total_count')
      ->where('date', '<=', now())
      ->groupBy('date')
      ->orderBy('date', 'DESC')
      ->paginate()
      ->withQueryString();
    if ($request->header('accept') === 'x-xlsx') {
      return Excel::download(new SiteViewReportExport($siteviews), 'siteviews-' . now()->toDateString() . '.xlsx');
    }

    return view('report::admin.siteviews.index', compact('siteviews'));
  }

  public function loadSiteViews(Request $request)
  {
    $siteviews = SiteView::query()
      ->where('date', $request->input('date'))
      ->latest('hour')
      ->get(['date', 'hour', 'count']);

    return response()->success('', compact('siteviews'));
  }

  public function loadVarieties()
  {
    $product = Product::queryy()->findOrFail(request('product_id'));

    if ($product->hasFakeVariety()) {
      return response()->success(['no_variety' => true]);
    }

    $varietyReports = [];
    $sumTotal = 0;
    $sumQuantity = 0;

    foreach ($product->varieties()->with(['attributes', 'color'])->get() as $variety) {

      $varietyReport = [];
      $varietyReport['variety'] = $variety;

      $orderItems = OrderItem::query()
        ->whereHas('order', fn($q) => $q->success())
        ->where('variety_id', $variety->id)
        ->active()
        ->get();

      $varietyReport['total'] = $orderItems->sum('amount');
      $varietyReport['quantity'] = $orderItems->sum('quantity');
      $sumTotal += $varietyReport['total'];
      $sumQuantity += $varietyReport['quantity'];
      $varietyReports[] = $varietyReport;
    }


    return response()->success('', [
      'variety_reports' => $varietyReports,
      'no_variety' => false,
      'sum_quantity' => $sumQuantity,
      'sum_total' => $sumTotal
    ]);
  }

}
