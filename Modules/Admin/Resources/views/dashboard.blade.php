@extends('admin.layouts.master')
@section('styles')
<style>
    .entry {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
    }
    .entry:last-child {
        border-bottom: none;
    }
    .entry .date {
        color: #777;
        text-align: right;
    }
    .entry .action {
        color: #e74c3c;
        font-weight: bold;
    }
    .dot {
        height: 10px;
        width: 10px;
        background-color: #e74c3c;
        border-radius: 50%;
        display: inline-block;
        margin-left: 10px;
    }
    .dott {
        height: 10px;
        width: 10px;
        background-color: #33ff74;
        border-radius: 50%;
        display: inline-block;
        margin-left: 10px;
    }
    .log-date {
        border-bottom: 1px solid #ddd;
    }
    .log-date:last-of-type {
        border-bottom: none;
    }
</style>
@endsection
@section('content')

<div class="page-header">
	<x-breadcrumb />
</div>

<div class="row">

	<div class="col-xl-9">
		<div class="row">

			<div class="col-xl-4 col-lg-6 col-md-12">
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-9">
								<a href="{{route('admin.orders.index')}}">
									<div class="mt-0 text-right">
										<span class="fs-16 font-weight-semibold"> تعداد کل سفارشات : </span>
										<p class="mb-0 mt-1 text-primary fs-20"> {{ number_format($allOrdersCount) }}</p>
									</div>
								</a>
							</div>
							<div class="col-3">
								<div class="icon1 bg-primary my-auto float-left">
									<i class="fe fe-users"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-4 col-lg-6 col-md-12">
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-9">
								<a href="{{route('admin.orders.index')}}">
									<div class="mt-0 text-right">
										<span class="fs-16 font-weight-semibold"> تعداد سفارشات امروز :</span>
										<p class="mb-0 mt-1 text-pink  fs-20">{{ number_format($todayOrdersCount) }}</p>
									</div>
								</a>
							</div>
							<div class="col-3">
								<div class="icon1 bg-pink my-auto float-left">
									<i class="feather feather-box"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-4 col-lg-6 col-md-12">
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-9">
								<a href="{{route('admin.orders.index')}}">
									<div class="mt-0 text-right">
										<span class="fs-16 font-weight-semibold"> میزان فروش امروز :</span>
										<p class="mb-0 mt-1 text-success fs-20"> {{ number_format($todayTotalSales) . ' تومان'  }} </p>
									</div>
								</a>
							</div>
							<div class="col-3">
								<div class="icon1 bg-success my-auto float-left">
									<i class="feather feather-dollar-sign"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-12 col-lg-12 col-md-12">
				<div class="card">
					<div class="card-header border-0">
						<div class="card-title">آمار فروش</div>
					</div>
					<div class="card-body">
						<div class="row">
							<canvas id="barChart" height="150"></canvas>
							<select id="dataSelect">
								<option value="totalSales">فروش این ماه</option>
								<option value="month">ماه</option>
								<option value="year">فروش سال</option>
							</select>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div class="col-xl-3 col-lg-6 col-md-12">
		<x-card>
			<x-slot name="cardTitle">آخرین فعالیت ها</x-slot>
			<x-slot name="cardBody">
				<div class="list-group">
					@foreach ($activityLogs as $index => $activityLog)
						<div class="list-group-item d-flex pt-3 pb-3 align-items-center border-0 p-0 m-0">
							<div class="ml-3 ml-xs-0">
								<div class="calendar-icon icons" style="line-height:0;">
									<div class="date_time {{ $index % 2 == 0 ? 'bg-pink-transparent' : 'bg-info-transparent' }}">
										<span class="date" style="line-height: normal;">{{ verta($activityLog->created_at)->format('m/d H:i') }}</span>
									</div>
								</div>
							</div>
							<div class="ml-1">
								<div class="mb-1"><span class="font-weight-normal">{{ $activityLog->description }}</span></div>
							</div>
						</div>
					@endforeach
				</div>
			</x-slot>
		</x-card>
	</div>

</div>

<div class="row">

	<div class="col-xl-4 col-lg-6 col-md-12">
		<x-card>
			<x-slot name="cardTitle">کاربران</x-slot>
			<x-slot name="cardBody">
				<div style="width: 70%; margin: auto;">
					<div class="d-flex justify-content-center">
					<span class="fs-16">مجموع کاربران: <span class="font-weight-bold">{{$sumDataGender}}</span></span>
				</div>
				<div>
					<canvas id="pieChart"></canvas>
				</div>
			</x-slot>
		</x-card>
	</div>

	<div class="col-xl-4 col-lg-6 col-md-12">
		<x-card>
			<x-slot name="cardTitle">آخرین ورود ها به سیستم</x-slot>
			<x-slot name="cardBody">
				@foreach ($lastLogins as $lastLogin)
					<div class="entry">
						<div class="action"><span class="dot"></span>ورود به سیستم</div>
						<div class="date">{{ $lastLogin->created_at->diffForHumans() }}</div>
					</div>
					<div class="d-flex justify-content-between">
						<p class="font-weight-normall m-0">{{$lastLogin->tokenable->mobile}}</p>
						<p class="text-muted text-end log-date mb-0">{{verta($lastLogin->created_at)->format('Y/m/d H:i')}}</p>
					</div>
				@endforeach
			</x-slot>
		</x-card>
	</div>

	<div class="col-xl-4 col-lg-6 col-md-12">
		<x-card>
			<x-slot name="cardTitle">آخرین بازدید های سایت</x-slot>
			<x-slot name="cardBody">
				@foreach ($siteviews as $date => $value)
					<div class="entry log-date">
						<div><span class="dott"></span>{{verta($date)->format('Y/m/d')}}</div>
						<div class="date">{{ $value }}</div>
					</div>
				@endforeach
			</x-slot>
		</x-card>
	</div>

</div>

<div class="row">

	<div class="col-xl-6">
		<x-card>
			<x-slot name="cardTitle">نظرات محصولات ({{ $productCommentsCount }})</x-slot>
			<x-slot name="cardOptions">
				<div class="card-options">
					<a href="{{ route('admin.product-comments.index') }}" class="btn btn-outline-info ml-3">مشاهده همه</a>
				</div>
			</x-slot>
			<x-slot name="cardBody">
				<x-table-component>
					<x-slot name="tableTh">
						<tr>
							<th>ردیف</th>
							<th>کاربر</th>
							<th>محصول</th>
							<th>وضعیت</th>
							<th>تاریخ</th>
						</tr>
					</x-slot>
					<x-slot name="tableTd">
						@forelse($newProductComments as $comment)
							<tr>
								<td class="font-weight-bold">{{ $loop->iteration }}</td>
								<td>{{ $comment->creator?->full_name ?? $comment->creator?->mobile ?? '-'}}</td>
								<td>{{Str::limit($comment->product->title,15,'...')}}</td>
								<td>
									<span class="badge badge-{{ config('productcomment.status_color.' . $comment->status) }}">
										{{ config('productcomment.statuses.' . $comment->status) }}
									</span>
								</td>
								<td>{{ verta($comment->created_at)->format('Y/m/d') }}</td>
							</tr>
						@empty
							@include('core::includes.data-not-found-alert', ['colspan' => 5])
						@endforelse
					</x-slot>
				</x-table-component>
			</x-slot>
		</x-card>
	</div>

	<div class="col-xl-6">
		<x-card>
			<x-slot name="cardTitle">نظرات مطالب ({{ $postCommentsCount }})</x-slot>
			<x-slot name="cardOptions">
				<div class="card-options">
					<a href="{{ route('admin.post-comments.index') }}" class="btn btn-outline-info ml-3">مشاهده همه</a>
				</div>
			</x-slot>
			<x-slot name="cardBody">
				<x-table-component>
					<x-slot name="tableTh">
						<tr>
							<th>ردیف</th>
							<th>کاربر</th>
							<th>مطلب</th>
							<th>وضعیت</th>
							<th>تاریخ</th>
						</tr>
					</x-slot>
					<x-slot name="tableTd">
						@forelse($newPostComments as $comment)
							<tr>
								<td class="font-weight-bold">{{ $loop->iteration }}</td>
								<td>{{ $comment->name ?? '-' }}</td>
								<td>{{Str::limit($comment->commentable->title, 20,'...')}}</td>
								<td>
									<span class="badge badge-{{ config('comment.status_color.' . $comment->status) }}">
										{{ config('comment.statuses.' . $comment->status) }}
									</span>
								</td>
								<td>{{ verta($comment->created_at)->format('Y/m/d') }}</td>
							</tr>
						@empty
							@include('core::includes.data-not-found-alert', ['colspan' => 5])
						@endforelse
					</x-slot>
				</x-table-component>
			</x-slot>
		</x-card>
	</div>

</div>

@endsection

@section('scripts')
    <script src="{{asset('assets/js/pieChart/chart.js')}}"></script>
  <script>
     function assignStatus(status, commentId) {
        $('#status-' + commentId).attr('value', status);
        $('#assign-status-form-' + commentId).submit();
    }
    var ctx = document.getElementById('pieChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: @json($dataGender['labels']),
            datasets: [{
                data: @json($dataGender['data']),
                backgroundColor: [
                    'red',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                ],
                borderColor: [
                    'red',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                ],
                borderWidth: 1
            }]
        },
    });


    document.addEventListener('DOMContentLoaded', function() {
        const salesData = {
            totalSales: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31],
            month: ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'],
            year: @json($barCharts['yearsList'])
        };

        const amountData = {
            totalSales: @json($barCharts['dailySums']),
            month: @json($barCharts['monthlySums']),
            year: @json($barCharts['yearlySums'])
        };
        const barCtx = document.getElementById("barChart").getContext('2d');
        let myBarChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: salesData.totalSales,
                datasets: [{
                    label: 'نمودار',
                    data: amountData.totalSales,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        function updateChart(selectedValue) {
            myBarChart.data.labels = salesData[selectedValue];
            myBarChart.data.datasets[0].data = amountData[selectedValue];
            myBarChart.update();
        }

        document.getElementById('dataSelect').addEventListener('change', function() {
            updateChart(this.value);
        });

        // بارگذاری اولیه نمودار
        updateChart('totalSales');
    });

  </script>
@endsection
