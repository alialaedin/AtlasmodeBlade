<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Comment\Entities\Comment;
use Modules\Dashboard\Services\ReportService;
use Modules\Home\Entities\SiteView;
use Modules\Order\Entities\Order;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Customer\Entities\Customer;
use Modules\ProductComment\Entities\ProductComment;
use Spatie\Activitylog\Models\Activity;


class DashboardController extends Controller
{
	public function __construct(protected ReportService $reportService) {}

	public function index()
	{
		$latestOrders = $this->getLatestOrders();
		$allOrdersCount = Order::count();
		$todayOrdersCount = $this->getTodayOrdersCount();
		$todayTotalSales = $this->getTodayTotalSales();
		$activityLogs = $this->getLatestActivityLogs();
		$lastLogins = $this->getLatLogins();

		$newProductComments = $this->getNewProductComments();
		$productCommentsCount = ProductComment::count();

		$newPostComments =  $this->getNewPostComments();
		$postCommentsCount =  Comment::count();

		$genderStatistics = $this->getGenderStatistics();
		$dataGender = [
			'labels' => ['مرد', 'زن', 'انتخاب نشده'],
			'data' => [
				$genderStatistics->males_count,
				$genderStatistics->females_count,
				$genderStatistics->unknowns_count,
			],
		];
		$sumDataGender = $genderStatistics->males_count + $genderStatistics->females_count + $genderStatistics->unknowns_count;
		$siteviews = $this->getSiteViews();

		// barCharts data ====================================================================
		$barCharts = Cache::get("barChart");
		if (!$barCharts) {
			$yearlySums = Order::query()
				->selectRaw('DATE_FORMAT(created_at, "%Y") as year, SUM(total_amount - discount_amount + shipping_amount) as total_invoices_amount_per_year')
				->whereIn('status', Order::ACTIVE_STATUSES)
				->groupBy('year')
				->orderBy('year', 'asc')
				->pluck('total_invoices_amount_per_year')->toArray();

			$startOfPersianMonth = verta()->startYear();
			$monthlySums = [];
			while ($startOfPersianMonth->format('m') <= verta()->format('m')) {
				$monthlySums[] = Order::query()
					->selectRaw(' SUM(total_amount - discount_amount + shipping_amount) as total_invoices_amount_per_month')
					->whereIn('status', Order::ACTIVE_STATUSES)
					->whereBetween('created_at', [$startOfPersianMonth->formatGregorian('Y-m-d H:i:s'), $startOfPersianMonth->addMonth()->formatGregorian('Y-m-d H:i:s')])
					//                    ->groupBy('month')
					//                    ->orderBy('month', 'asc')
					->first()->total_invoices_amount_per_month;
				$startOfPersianMonth = $startOfPersianMonth->addMonth();
			}

			$firstDayOfThisMonthInPersianDate = verta()->startMonth()->formatGregorian('Y-m-d H:i:s');
			$dailySums = Order::query()
				->selectRaw('DATE(created_at) as date, SUM(total_amount - discount_amount + shipping_amount) as total_invoices_amount_per_day')
				->whereIn('status', Order::ACTIVE_STATUSES)
				->where('created_at', '>=', $firstDayOfThisMonthInPersianDate)
				->groupBy('date')
				->orderBy('date', 'asc')
				->pluck('total_invoices_amount_per_day')->toArray();

			$yearsList = [];
			$firstOrder = Order::query()->first();
			$yearOfFirstOrder = verta(($firstOrder->created_at) ?? null)->format('Y');
			$yearOfNow = verta()->format('Y');
			while ($yearOfFirstOrder <= $yearOfNow) {
				$yearsList[] = (int)$yearOfFirstOrder;
				$yearOfFirstOrder++;
			}

			$barCharts = compact('yearlySums', 'monthlySums', 'dailySums', 'yearsList');
			Cache::put("barChart", $barCharts, now()->addHour());
		}

		return view('admin::dashboard', compact([
			'latestOrders',
			'allOrdersCount',
			'todayOrdersCount',
			'todayTotalSales',
			'activityLogs',
			'lastLogins',
			'newProductComments',
			'productCommentsCount',
			'postCommentsCount',
			'newPostComments',
			'dataGender',
			'sumDataGender',
			'siteviews',
			'barCharts'
		]));
	}

	private function getLatestOrders()
	{
		return Order::query()
			->whereNull('parent_id')
			->select('id', 'status', 'created_at', 'shipping_amount', 'discount_amount')
			->with([
				'items' => fn($iQuery) => $iQuery->select(['id', 'order_id', 'status', 'amount', 'quantity'])
			])
			->withCount('items')
			->whereIn('status', Order::ACTIVE_STATUSES)
			->latest('id')
			->take(8)
			->get()
			->each(function ($order) {
				$order->append(['total_amount']);
				$order->makeHidden(['items']);
			});
	}

	private function getTodayOrdersCount()
	{
		return Order::query()->whereBetween(
			'created_at',
			[
				Carbon::createFromTimestamp(verta()->startDay()->getTimestamp()),
				Carbon::now()
			]
		)->count();
	}

	private function getTodayTotalSales()
	{
		return Order::query()
			->whereDate('created_at', Carbon::today())
			->whereIn('status', Order::ACTIVE_STATUSES)
			->selectRaw('SUM(total_amount - discount_amount + shipping_amount) AS total')
			->value('total');
	}

	private function getLatestActivityLogs()
	{
		return Activity::query()
			->select('id', 'causer_id', 'description', 'created_at')
			->latest('id')
			->take(6)
			->get();
	}

	private function getLatLogins()
	{
		return PersonalAccessToken::query()
			->select(['id', 'tokenable_id', 'tokenable_type', 'created_at'])
			->latest('id')
			->take(4)
			->with('tokenable')
			->get();
	}

	private function getNewProductComments()
	{
		return ProductComment::query()
			->latest('id')
			->with(['creator', 'product'])
			->whereStatus(ProductComment::STATUS_PENDING)
			->take(5)
			->get();
	}

	private function getNewPostComments()
	{
		return Comment::query()
			->latest('id')
			->whereStatus(Comment::STATUS_UNAPPROVED)
			->with('commentable')
			->take(5)
			->get();
	}

	private function getGenderStatistics()
	{
		$malesCount = Customer::query()->where('gender', Customer::MALE)->count();
		$femalesCount = Customer::query()->where('gender', Customer::FEMALE)->count();
		$allCount = Customer::count();

		return (object) [
			'males_count' => $malesCount,
			'females_count' => $femalesCount,
			'unknowns_count' => $allCount - $femalesCount - $malesCount
		];
	}

	private function getSiteViews()
	{
		$siteviewslist = array();
		$siteViews = SiteView::query()
			->orderBy('id', 'DESC')
			->where('date', '>=', now()->subDays(8)->endOfDay())
			->get()
			->groupBy('date');

		foreach ($siteViews as $y => $siteview) {
			$siteviewslist[$y] = 0;
			foreach ($siteview as $x) {
				$siteviewslist[$y] = $siteviewslist[$y] + $x->count;
			}
		}

		return (object) $siteviewslist;
	}
}
