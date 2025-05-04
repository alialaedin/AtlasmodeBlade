<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Comment\Entities\Comment;
use Modules\Dashboard\Services\ReportService;
use Modules\Home\Entities\SiteView;
use Modules\Order\Entities\Order;
use Carbon\Carbon;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Core\Helpers\Helpers;
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
		$thisMonthTotalSales = $this->getThisMonthTotalSales();
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

		return view('admin::dashboard', compact([
			'latestOrders',
			'allOrdersCount',
			'todayOrdersCount',
			'todayTotalSales',
			'activityLogs',
			'lastLogins',
			'newProductComments',
			'thisMonthTotalSales',
			'productCommentsCount',
			'postCommentsCount',
			'newPostComments',
			'dataGender',
			'sumDataGender',
			'siteviews',
		]));
	}

	private function getLatestOrders()
	{
		return Order::query()
			->whereNull('parent_id')
			->with([
				'items' => fn($iQuery) => $iQuery->select(['id', 'order_id', 'status', 'amount', 'quantity'])
			])
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
		$totalSales = DB::table('orders as o')
			->join('order_items as oi', 'oi.order_id', '=', 'o.id')
			->whereDate('o.created_at', Carbon::today())
			->whereIn('o.status', Order::ACTIVE_STATUSES)
			->select([
				DB::raw('SUM(oi.amount * oi.quantity) AS item_total'),
				DB::raw('SUM(o.discount_amount) AS total_discount'),
				DB::raw('SUM(o.shipping_amount) AS total_shipping')
			])
			->first();
		
		return $totalSales->item_total - $totalSales->total_discount + $totalSales->total_shipping;
	}

	private function getThisMonthTotalSales()
	{
		$startDate = Helpers::toGregorian(Verta::startMonth());
		$endDate = Helpers::toGregorian(Verta::endMonth());

		$totalSales = DB::table('orders as o')
			->join('order_items as oi', 'oi.order_id', '=', 'o.id')
			->whereBetween('o.created_at', [$startDate, $endDate])
			->whereIn('o.status', Order::ACTIVE_STATUSES)
			->select([
				DB::raw('SUM(oi.amount * oi.quantity) AS item_total'),
				DB::raw('SUM(o.discount_amount) AS total_discount'),
				DB::raw('SUM(o.shipping_amount) AS total_shipping')
			])
			->first();
		
		return $totalSales->item_total - $totalSales->total_discount + $totalSales->total_shipping;
	}


	private function getLatestActivityLogs()
	{
		return Activity::query()
			->select('id', 'causer_id', 'description', 'created_at')
			->latest('id')
			->take(10)
			->get();
	}

	private function getLatLogins()
	{
		return PersonalAccessToken::query()
			->select(['id', 'tokenable_id', 'tokenable_type', 'updated_at'])
			->latest('id')
			->take(10)
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
			->whereIn('status', [Comment::STATUS_UNAPPROVED, Comment::STATUS_PENDING])
			->with('post.creator')
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
		$siteviewslist = [];
		$siteViews = SiteView::query()
			->orderBy('id', 'DESC')
			->where('date', '>=', now()->subDays(10)->endOfDay())
			->get()
			->groupBy('date');

		foreach ($siteViews as $y => $siteview) {
			$siteviewslist[$y] = 0;
			foreach ($siteview as $x) {
				$siteviewslist[$y] = $siteviewslist[$y] + $x->count;
			}
		}

		return $siteviewslist;
	}
}
