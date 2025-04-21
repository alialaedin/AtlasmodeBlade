@extends('admin.layouts.master')

@section('styles')
	<style>
		.changeStatus {
			opacity: 1;
			transition: opacity 0.3s ease;
		}

		.inactive {
			opacity: 0.5;
		}

		.hidden {
			display: none;
			float: left;
		}

		.add {
			display: flex;
			float: left;
		}
	</style>
@endsection

@section('content')

	<div class="page-header">
		<x-breadcrumb :items="[['title' => 'لیست سفارشات']]" />
		@can('write_order')
			<x-create-button route="admin.orders.create" title="سفارش جدید" />
		@endcan
	</div>

	<x-card>
		<x-slot name="cardTitle">جستجوی پیشرفته</x-slot>
		<x-slot name="cardOptions"><x-card-options /></x-slot>
		<x-slot name="cardBody">
			<form action="{{ route('admin.orders.index') }}" class="col-12">
				<div class="row">
					<input hidden name="perPage" value="{{ request('perPage', 15) }}">
					<div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<input type="number" placeholder="شناسه" name="id" class="form-control" value="{{ request('id') }}">
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<input type="number" placeholder="کد رهگیری" name="tracking_code" class="form-control" value="{{ request('tracking_code') }}">
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<input type="text" placeholder="شهر" name="city" class="form-control" value="{{ request('city') }}">
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<input type="text" placeholder="استان" name="province" class="form-control" value="{{ request('province') }}">
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<input type="text" placeholder="نام" name="first_name" class="form-control" value="{{ request('first_name') }}">
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<input type="text" placeholder="نام خانوادگی" name="last_name" class="form-control" value="{{ request('last_name') }}">
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<select name="customer_id" class="form-control search-customer-ajax"></select>
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<select name="status" class="form-control status-select-box">
								<option value="">انتخاب وضعیت</option>
								@foreach ($orderStatuses as $statusName => $ordersCount)
									<option value="{{ $statusName }}"
										{{ request('status') == $statusName ? 'selected' : '' }}>
										{{ config('order.statusLabels.' . $statusName) }}
									</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<input class="form-control fc-datepicker" id="start_date_show" type="text" placeholder="از تاریخ" />
							<input name="start_date" id="start_date_hide" type="hidden" value="{{ request('start_date') }}" />
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<input class="form-control fc-datepicker" id="end_date_show" type="text" placeholder="تا تاریخ" />
							<input name="end_date" id="end_date_hide" type="hidden" value="{{ request('end_date') }}" />
						</div>
					</div>
					{{-- <div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<input type="number" name="invoices_amount_from" placeholder="مبلغ سفارش از" class="form-control" value="{{ request('invoices_amount_from') }}">
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-3">
						<div class="form-group">
							<input type="number" name="invoices_amount_to" placeholder="مبلغ سفارش تا" class="form-control" value="{{ request('invoices_amount_to') }}">
						</div>
					</div> --}}
					<x-product-search cols="col-12 col-md-6 col-xl-3" :has-label="false"/>
				</div>
				<div class="row">
					<div class="col-12 d-flex justify-content-center" style="gap: 8px">
						<button class="btn btn-sm btn-primary" type="submit">جستجو و فیلتر <i class="fa fa-search"></i></button>
						<a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-danger">حذف فیلتر ها <i class="fa fa-close"></i></a>
					</div>
				</div>
			</form>
		</x-slot>
	</x-card>

	<x-card>
		<x-slot name="cardTitle">سفارشات ({{ number_format($orders->total()) }})</x-slot>
		<x-slot name="cardOptions">
			<div class="card-options">
				<div id="operation-buttons-row" class="ml-3">
					<button type="button" class="btn mr-2 btn-warning" data-target="#change-orders-status-modal" data-toggle="modal">تغییر وضعیت</button>
					{{-- <button type="button" class="btn mr-2 btn-purple" id="print-orders-btn">چاپ<i class="si si-printer mr-1"></i></button> --}}
				</div>
				<form action="{{ route('admin.orders.index') }}" method="GET">
					<div class="form-group mb-0">
						<select name="perPage" id="PaginateSelectBox" class="form-control">
							@foreach (config('order.orderPaginations') as $p)
								<option value="{{ $p }}" {{ request('perPage') == $p ? 'selected' : '' }}>{{ $p }}</option>
							@endforeach
						</select>
					</div>
				</form>
			</div>
		</x-slot>
		<x-slot name="cardBody">

			<div class="rwo mb-3">
				@foreach ($orderStatuses as $statusName => $ordersCount)
					<a 
						href="{{ route('admin.orders.index', ['status' => $statusName]) }}"
						class="status-btn btn btn-sm {{ config('order.statusColors.' . $statusName) }} {{ request('status') == $statusName ? 'changeStatus' : 'inactive' }}">
						{{ config('order.statusLabels.' . $statusName) }} ({{ $ordersCount }})
					</a>
				@endforeach
			</div>

			<x-table-component>
				<x-slot name="tableTh">
					<tr>
						<th class="wd-20p" style="width: 5%;">
							<input type="checkbox" class="order-change-status-checkbox" id="all-order-change-status-checkbox">
						</th>
						<th>شناسه</th>
						<th>مشتری</th>
						<th>گیرنده</th>
						<th>تعداد آیتم</th>
						<th>مبلغ کل (تومان)</th>
						<th>شماره پیگیری</th>
						<th>تاریخ ثبت</th>
						<th>وضعیت</th>
						<th>تاریخ ارسال</th>
						<th>عملیات</th>
					</tr>
				</x-slot>
				<x-slot name="tableTd">
					@forelse($orders as $order)
						<tr>
							<td>
								<input type="checkbox" class="checkbox order-change-status-checkbox" value="{{ $order->id }}">
							</td>
							<td>{{ $order->id }}</td>
							<td>{{ $order->customer->mobile }}</td>
							<td>
								@php( $address = json_decode($order->address) ) 
								{{ $address->first_name .' '. $address->last_name }}
							</td>
							<td>{{ $order->items_count }}</td>
							<td>{{ number_format($order->total_amount) }}</td>
							<td>{{ $order->active_payment?->tracking_code ?? '-' }}</td>
							<td>{{ verta($order->created_at)->format('Y/m/d H:i') }}</td>
							<td>
								<button class="{{ config('order.statusColors.' . $order->status) }} btn-sm btn">
									{{ config('order.statusLabels.' . $order->status) }}
								</button>
							</td>
							<td>{{ $order->delivered_at ? verta($order->delivered_at)->format('Y/m/d') : '-' }}</td>
							<td>
								@include('core::includes.show-icon-button', [
									'route' => 'admin.orders.show',
									'model' => $order,
								])
								<a href="{{ route('admin.orders.print', ['ids' => $order->id]) }}" target="_blank" class="btn btn-purple font-weight-bold btn-sm btn-icon">
									<i class="si si-printer"></i>
								</a>
							</td>
						</tr>
					@empty
						@include('core::includes.data-not-found-alert', ['colspan' => 8])
					@endforelse
				</x-slot>
				<x-slot name="extraData">{{ $orders->onEachSide(0)->links('vendor.pagination.bootstrap-4') }}</x-slot>
			</x-table-component>
		</x-slot>
	</x-card>

	<form 
		id="PrintMultiOrdersForm" 
		action="{{ route('admin.orders.print') }}" 
		class="d-none">
		<input type="hidden" name="ids" id="OrderIdsInput">
	</form>

	<x-modal id="change-orders-status-modal" size="md">
		<x-slot name="title">تغییر وضعیت سفارشات</x-slot>
		<x-slot name="body">
			<form action="{{ route('admin.orders.changeStatusSelectedOrders') }}" method="POST">

				@csrf

				<div class="row">
					<div class="col-12">
						<div class="form-group">
							<select class="form-control status-select-box" required>
								<option value="">- انتخاب کنید -</option>
								<option value="in_progress">در حال پردازش</option>
								<option value="delivered">ارسال شده</option>
								<option value="new">در انتظار تکمیل</option>
							</select>
						</div>
					</div>
				</div>

				<div class="modal-footer justify-content-center mt-2">
					<button class="btn btn-sm btn-warning" id="change-orders-status-submit-btn" type="button">تغییر وضعیت</button>
					<button class="btn btn-sm btn-outline-danger" data-dismiss="modal">انصراف</button>
				</div>

			</form>
		</x-slot>
	</x-modal>

@endsection

@section('scripts')

	@include('core::includes.date-input-script', [
		'textInputId' => 'start_date_show',
		'dateInputId' => 'start_date_hide',
	])

	@include('core::includes.date-input-script', [
		'dateInputId' => 'end_date_hide',
		'textInputId' => 'end_date_show',
	])

	@stack('ProductSearchScripts')

	<script>

		$('.search-customer-ajax').select2({
				ajax: {
						url: '{{ route('admin.customers.search') }}',
						dataType: 'json',
						processResults: (response) => {
								let customers = response.data.customers || [];

								return {
										results: customers.map(customer => ({
												id: customer.id,
												mobile: customer.mobile,
												name: customer.full_name || ''
										})),
								};
						},
						cache: true,
				},
				placeholder: 'انتخاب مشتری',
				templateResult: formatRepo,
				minimumInputLength: 1,
				templateSelection: formatRepoSelection
		});

		function formatRepo(repo) {
				if (repo.loading) {
						return "در حال بارگذاری...";
				}

				var $container = $(
						"<div class='select2-result-repository clearfix'>" +
						"<div class='select2-result-repository__meta'>" +
						"<div class='select2-result-repository__title'></div>" +
						"</div>" +
						"</div>"
				);

				let text = `${repo.mobile}`;
				if (repo.name) {
					text +=  ` | ${repo.name}`;
				}
				$container.find(".select2-result-repository__title").text(text);

				return $container;
		}

		function formatRepoSelection(repo) {
				let text = `شناسه: ${repo.id} | موبایل: ${repo.mobile}`;
				if (repo.name) {
						text += ` | نام: ${repo.name}`;
				}
				return repo.id ? text : repo.text;
		}

	</script>

	<script>

		let hideOperationButtonsRow = () => $('#operation-buttons-row').hide();
		let showOperationButtonsRow = () => $('#operation-buttons-row').show();

		function makeStatusSelectBoxesLabels() {
			$('.status-select-box').each(function () {
				$(this).select2({
					placeholder: 'انتخاب وضعیت'
				});
			});
		}

		function handleAllOrdersChangeStatusCheckboxOnChangeEvent() {  
			$('#all-order-change-status-checkbox').change(function () {  
				const isChecked = $(this).is(':checked');  
				$('.order-change-status-checkbox').prop('checked', isChecked);  
			});  
		}  

		function paginate() {
			$('#PaginateSelectBox').on('change', (event) => {
				$(event.target).closest('form').submit();
			});
		}

		function print() {
			$('#print-orders-btn').click((e) => {

				e.preventDefault();

				const form = $('#PrintMultiOrdersForm');
				const input = form.find('#OrderIdsInput');

				let orderIds = [];
				$('.checkbox:checked').each(function() {
					orderIds.push($(this).val());
				});

				input.val(orderIds);
				form.submit();

			});
		}

		function handleShowingOperationButtons() {  
			$('.order-change-status-checkbox').each(function () {  
				$(this).change(() => {
					if ($('.order-change-status-checkbox:checked').length > 0) {  
						showOperationButtonsRow();  
					}else {
						hideOperationButtonsRow();
					}
				});  
			});  
		}  

		function changeStatus() {
			$('#change-orders-status-submit-btn').click((event) => {
				event.preventDefault();
				let form = $(event.target).closest('form');
				let index = 0;
				$('.order-change-status-checkbox:checked').each(function () {
					form.append(`<input hidden name="ids[${index++}]" value="${$(this).val()}" />`);
				});
				form.submit();
			});
		}

		makeStatusSelectBoxesLabels();

		$(document).ready(() => {
			paginate();
			print();
			changeStatus();
			hideOperationButtonsRow();
			handleShowingOperationButtons();
			handleAllOrdersChangeStatusCheckboxOnChangeEvent();
		});

	</script>
  
@endsection
