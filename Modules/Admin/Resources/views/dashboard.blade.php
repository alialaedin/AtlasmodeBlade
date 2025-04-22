@extends('admin.layouts.master')

@section('content')

<div class="page-header">
	<x-breadcrumb />
</div>

@php
  $statistics = [
    ['title' => 'تعداد کل سفارشات', 'value' => number_format($allOrdersCount), 'color' => 'info', 'icon' => 'shopping-basket'],
    ['title' => 'تعداد سفارشات امروز', 'value' => number_format($todayOrdersCount), 'color' => 'danger', 'icon' => 'cube'],
    ['title' => 'میزان فروش ماه', 'value' => number_format($thisMonthTotalSales), 'color' => 'secondary', 'icon' => 'calendar'],
    ['title' => 'میزان فروش امروز', 'value' => number_format($todayTotalSales), 'color' => 'success', 'icon' => 'dollar']
  ];
@endphp

<div class="row">
  @foreach ($statistics as $item)
    <div class="col-xl-3 col-lg-6 col-md-12">
      <div class="card">
        <div class="card-body">
          <i class="fa fa-{{ $item['icon'] }} card-custom-icon icon-dropshadow-{{ $item['color'] }} text-{{ $item['color'] }} fs-60"></i>
          <p class=" mb-1">{{ $item['title'] }}</p>
          <h3 class="mb-1 font-weight-bold">{{ $item['value'] }}</h3>
        </div>
      </div>
    </div>
  @endforeach
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header border-0">
				<h3 class="card-title font-weight-bold">آخرین سفارشات</h3>
				<div class="card-options">
					<a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-light ml-3">مشاهده همه</a>
				</div>
			</div>
			<div class="table-responsive attendance_table mt-4 border-top">
				<table class="table mb-0 text-nowrap">
					<thead>
						<tr>
							<th class="text-center">شناسه</th>
							<th class="text-center">وضعیت</th>
							<th class="text-center">تعداد محصولات</th>
							<th class="text-center">مبلغ (تومان)</th>
							<th class="text-center">تاریخ</th>
							<th class="text-center">عملیات</th>
						</tr>
					</thead>
					<tbody>

						@forelse ($latestOrders as $order)
							<tr class="border-bottom">
								<td class="text-center font-weight-bold">{{ $order->id }}</td>
								<td class="text-center">
									<span class="badge bg-{{ config('admin.orderStatusColorsForDashboard.' . $order->status) }}-transparent">
										{{ config('order.statusLabels.' . $order->status) }}
									</span>
								</td>
								<td class="text-center">{{ $order->items_count }}</td>
								<td class="text-center">{{ number_format($order->total_amount) }}</td>
								<td class="text-center">{{ verta($order->created_at)->format('Y/m/d H:i') }}</td>
								<td class="text-center">
									<a href="{{ route('admin.orders.show', $order) }}" class="action-btns">
										<i class="feather feather-eye text-primary"></i>
									</a>
								</td>
							</tr>
						@empty
							<tr>
								<td class="text-center" colspan="6">
									<div class="text-center">
										<span class="text-danger">سفارشی تاکنون ثبت نشد است</span>
									</div>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xl-4 col-12 custom-card-height">
		<x-card>
			<x-slot name="cardTitle">آخرین فعالیت ها</x-slot>
			<x-slot name="cardBody">
				<div class="list-group overflow-auto" style="height: 300px;">
					@foreach ($activityLogs ?? [] as $index => $activityLog)
						<div class="list-group-item d-flex pt-3 pb-3 align-items-center border-0">
							<div class="ml-3 ml-xs-0">
								<div class="calendar-icon icons">
									<div class="date_time {{ $index % 2 == 0 ? 'bg-pink-transparent' : 'bg-info-transparent' }}">
										<span class="date">{{ verta($activityLog->created_at)->format('d') }}</span>
										<span class="month">{{ verta($activityLog->created_at)->format('%B') }}</span>
									</div>
								</div>
							</div>
							<div class="ml-1">
								<small class="text-muted">{{ Str::limit($activityLog->description, 35, '...') }}</small>
							</div>
						</div>
					@endforeach
				</div>
			</x-slot>
		</x-card>
	</div>
	<div class="col-xl-4 col-12 custom-card-height">
		<x-card>
			<x-slot name="cardTitle">آخرین ورود ها به سیستم</x-slot>
			<x-slot name="cardBody">
				<ul class="timeline overflow-auto" style="height: 300px;">
					@foreach ($lastLogins as $lastLogin)
						<li>
							<a target="_blank" href="#" class="font-weight-semibold fs-15 mr-3">{{ $lastLogin->tokenable->name }}</a>
							<a href="#" class="text-muted float-left fs-13">{{ $lastLogin->updated_at->diffForHumans() }}</a>
							<p class="mb-0 pb-0 text-muted pt-1 fs-11 mr-3">{{ $lastLogin->tokenable->mobile }}</p>
							<span class="text-muted  mr-3 fs-11">{{ verta($lastLogin->updated_at)->format('Y/m/d H:i') }}</span>
						</li>
					@endforeach
				</ul>
			</x-slot>
		</x-card>
	</div>
	<div class="col-xl-4 col-12 custom-card-height">
		<x-card>
			<x-slot name="cardTitle">آخرین بازدید های سایت</x-slot>
			<x-slot name="cardBody">
				<ul class="timeline overflow-auto" style="height: 300px;">
					@foreach ($siteviews ?? [] as $data => $count)
						<li class="success">
							<span class="font-weight-bold fs-15 mr-3">{{ verta($data)->format('Y/m/d') }}</span>
							<span class="float-left fs-16">{{ $count }}</span>
						</li>
					@endforeach
				</ul>
			</x-slot>
		</x-card>
	</div>
</div>

<div class="row">
	<div class="col-xl-8">
		<div class="card">
			<div class="card-header border-bottom-0">
				<h3 class="card-title font-weight-bold">نظرات جدید</h3>
			</div>
			<div class="tab-menu-heading table_tabs mt-2 p-0 ">
				<div class="tabs-menu1">
					<ul class="nav panel-tabs">
						<li class="mr-4"><a href="#product-comments" data-toggle="tab" class="active">نظرات محصول ({{ $productCommentsCount }})</a></li>
						<li><a href="#post-comments" data-toggle="tab" class="">نظرات بلاگ ({{ $postCommentsCount }})</a></li>
					</ul>
				</div>
			</div>
			<div class="panel-body tabs-menu-body table_tabs1 p-0 border-0">
				<div class="tab-content">
					<div class="tab-pane active" id="product-comments">
						<div class="table-responsive recent_jobs pt-2 pb-2 pl-2 pr-2 card-body" style="max-height: 250px; overflow-y: auto;">
							<table class="table mb-0 text-nowrap">
								<tbody>
									@foreach ($newProductComments as $comment)
										<tr class="{{ $newProductComments->last()->id !== $comment->id ? 'border-bottom' : '' }}">
											<td class="text-center">
												<div class="d-flex">
													@php
														$image = $comment->product->main_image;
														$imageUrl = '/storage/' . $image->uuid . '/' . $image->file_name;
													@endphp
													<img src="{{ $imageUrl }}" alt="img" class="avatar avatar-md brround ml-3">
													<div class="align-self-center mr-3 mt-0 mt-sm-1 d-block">
														<h6 class="mb-0">{{ $comment->product->title }}</h6>
													</div>
												</div>
											</td>
											<td class="text-center fs-13">{{ $comment->created_at->diffForHumans() }}</td>
											<td class="text-center">
												{{ Str::limit($comment->title, 20, '...') }}
											</td>
											<td class="text-center">
												<x-badge :is-light="true">
													<x-slot name="type">{{ config('productcomment.status_color.' . $comment->status) }}</x-slot>
													<x-slot name="text">{{ config('productcomment.statuses.' . $comment->status) }}</x-slot>
												</x-badge>
											</td>
											<td class="text-center">
												<span>
													@if ($comment->rate > 0)
														@for ($i = 1; $i <= (int) $comment->rate; $i++)
															<i class="fa fa-star text-warning"></i>
														@endfor
													@endif
													@php
														$remainingRate = 5 - (int) $comment->rate															
													@endphp
													@if ($remainingRate > 0)
														@for ($i = 1; $i <= $remainingRate; $i++)
															<i class="fa fa-star-o text-warning"></i>
														@endfor
													@endif
												</span>
											</td>
										</tr>		
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					<div class="tab-pane" id="post-comments">
						<div class="table-responsive recent_jobs pt-2 pb-2 pl-2 pr-2 card-body" style="max-height: 250px; overflow-y: auto;">
							<table class="table mb-0 text-nowrap">
								<tbody>
									@foreach ($newPostComments as $comment)
										<tr class="{{ $newPostComments->last()->id !== $comment->id ? 'border-bottom' : '' }}">
											<td class="text-center">
												<div class="d-flex">
													@php
														$image = $comment->post->image;
														$imageUrl = '/storage/' . $image->uuid . '/' . $image->file_name;
													@endphp
													<img src="{{ $imageUrl }}" alt="img" class="avatar avatar-md brround ml-3">
													<div class="mr-3 mt-0 mt-sm-1 d-block">
														<h6 class="mb-0">{{ Str::limit($comment->post->title, 20, '...') }}</h6>
														<div class="clearfix"></div>
														<small class="text-muted">{{ $comment->post->creatorable->name }}</small>
													</div>
												</div>
											</td>
											<td class="text-center fs-13">{{ $comment->created_at->diffForHumans() }}</td>
											<td class="text-center">
												{{ Str::limit($comment->body, 30, '...') }}
											</td>
											<td class="text-center">
												<x-badge :is-light="true">
													<x-slot name="type">{{ config('comment.status_color.' . $comment->status) }}</x-slot>
													<x-slot name="text">{{ config('comment.statuses.' . $comment->status) }}</x-slot>
												</x-badge>
											</td>
										</tr>		
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-4">
		<x-card>
			<x-slot name="cardTitle">کاربران</x-slot>
			<x-slot name="cardBody">
				<div style="width: 70%; margin: auto;">
					<div class="d-flex justify-content-center">
					<span class="fs-16">مجموع کاربران: <span class="font-weight-bold">{{ $sumDataGender }}</span></span>
				</div>
				<div>
					<canvas id="pieChart"></canvas>
				</div>
			</x-slot>
		</x-card>
	</div>
</div>

@endsection

@section('scripts')
    <script src="{{asset('assets/js/pieChart/chart.js')}}"></script>
  <script>
   
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


  </script>
@endsection
