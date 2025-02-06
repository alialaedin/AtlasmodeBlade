<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Comment\Entities\Comment;
use Modules\Dashboard\Services\ReportService;
use Modules\Home\Entities\SiteView;
use Modules\Order\Entities\Order;
use Carbon\Carbon;
use Modules\ProductComment\Entities\ProductComment;
use Spatie\Activitylog\Models\Activity;


class DashboardController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function index()
    {

        $order = Order::query()
            ->whereNull('parent_id')
            ->select('id', 'status')
            ->whereIn('status', Order::ACTIVE_STATUSES);

        $ordersCount = $order->count();

        $totalSalesToday = Order::query()  
            ->whereDate('created_at', Carbon::today())  
            ->whereIn('status', Order::ACTIVE_STATUSES)  
            ->selectRaw('SUM(total_amount - discount_amount + shipping_amount) AS total')  
            ->value('total');  

        $orderCountToday = $order->whereBetween(
            'created_at',
            [
                Carbon::createFromTimestamp(verta()->startDay()->getTimestamp()),
                Carbon::now()
            ]
        )->count();

        $activityLogs = Activity::query()
            ->select('id', 'causer_id', 'description', 'created_at')
            ->latest('id')
            ->take(7)
            ->get();
        $last_logins = $this->reportService->getLastLogins();
        $comments = ProductComment::query()->latest('id')->with(['creator', 'product'])
            ->take(5)->get();
        $newProductCommentsCount = ProductComment::query()->latest('id')
            ->whereStatus(ProductComment::STATUS_PENDING)->count();
        $blogComments = Comment::query()->latest('id')->whereStatus(Comment::STATUS_UNAPPROVED)->with('commentable')->take(5)->get();
        $newBlogCommentsCount = Comment::query()
            ->whereStatus(Comment::STATUS_UNAPPROVED)->count();
        $gender_statistics = $this->reportService->getCustomersGender();
        $dataGender = [
            'labels' => ['مرد', 'زن', 'انتخاب نشده'],
            'data' => [
                $gender_statistics['males_count'],
                $gender_statistics['females_count'],
                $gender_statistics['unknowns_count'],
            ],
        ];
        $sumDataGender = $gender_statistics['males_count'] + $gender_statistics['females_count'] + $gender_statistics['unknowns_count'];
        $siteviews = SiteView::query()
            ->orderBy('id', 'DESC')
            ->where('date', '>=', now()->subDays(8)->endOfDay())
            ->get()->groupBy('date');

        $siteviewslist = array();

        foreach ($siteviews as $y => $siteview) {
            $siteviewslist[$y] = 0;
            foreach ($siteview as $x) {
                $siteviewslist[$y] = $siteviewslist[$y] + $x->count;
            }
        }

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
            'ordersCount',
            'sumDataGender',
            'activityLogs',
            'siteviewslist',
            'dataGender',
            'last_logins',
            'orderCountToday',
            'totalSalesToday',
            'comments',
            'newProductCommentsCount',
            'blogComments',
            'newBlogCommentsCount',
            'barCharts'
        ]));
    }
}
