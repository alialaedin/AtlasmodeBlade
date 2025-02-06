@extends('admin.layouts.master')
@section('content')

  <div class="page-header">
		<x-breadcrumb :items="[['title' => 'لیست تخفیفات ویژه']]" />
		<x-create-button type="modal" target="CreateNewSpecificDiscountModal" title="تخفیف ویژه جدید" />
  </div>

  <x-card>
		<x-slot name="cardTitle">لیست تخفیفات ویژه ({{ $specificDiscounts->total() }})</x-slot>
		<x-slot name="cardOptions"><x-card-options /></x-slot>
		<x-slot name="cardBody">
			<x-table-component>
				<x-slot name="tableTh">
					<tr>
						<th>ردیف</th>
						<th>عنوان</th>
						<th>تاریخ شروع</th>
						<th>تاریخ پایان</th>
						<th>تاریخ ثبت</th>
						<th>عملیات</th>
					</tr>
				</x-slot>
				<x-slot name="tableTd">
					@forelse ($specificDiscounts as $specificDiscount)
						<tr>
							<td class="font-weight-bold">{{ $loop->iteration }}</td>
							<td>{{ $specificDiscount->title }}</td>
							<td>{{ verta($specificDiscount->start_date)->format('Y/m/d H:i')}}</td>
							<td>{{ verta($specificDiscount->end_date)->format('Y/m/d H:i')}}</td>
							<td>{{ verta($specificDiscount->created_at)->format('Y/m/d H:i')}}</td>
							<td>

								@include('core::includes.edit-modal-button', [
									'target' => '#EditSpecificDiscountModal-' . $specificDiscount->id,
									'title' => 'ویرایش'
								])

								<a 
									href="{{ route('admin.specific-discounts.types.index', $specificDiscount) }}"
									class="btn btn-sm btn-dark text-white">
									<span>افزودن</span>
									<i class="fa fa-plus mr-1"></i>
								</a>

								@include('core::includes.delete-icon-button', [
									'model' => $specificDiscount,
									'route' => 'admin.specific-discounts.destroy',
									'disabled' => !$specificDiscount->is_deletable,
									'title' => 'حذف'
								])
							</td>
						</tr>
					@empty
						@include('core::includes.data-not-found-alert', ['colspan' => 7])
					@endforelse
				</x-slot>
				<x-slot name="extraData">{{ $specificDiscounts->onEachSide(0)->links('vendor.pagination.bootstrap-4') }}</x-slot>
			</x-table-component>
		</x-slot>
	</x-card>

<x-modal id="CreateNewSpecificDiscountModal" size="md">
	<x-slot name="title">ایجاد تخفیف ویژه جدید</x-slot>
	<x-slot name="body">
		<form action="{{ route('admin.specific-discounts.store') }}" method="POST">
			@csrf
			<div class="row">

				<div class="col-12 form-group">
					<label for="">عنوان : <span class="text-danger">&starf;</span></label>
					<input type="text" class="form-control" name="title" placeholder="عنوان تخفیف را وارد کنید" required/>
				</div>
				
				<div class="col-12 form-group">
					<label for="start_date_show">تاریخ شروع : <span class="text-danger">&starf;</span></label>
					<input class="form-control fc-datepicker" id="start_date_show" type="text" autocomplete="off" placeholder="تاریخ شروع را انتخاب کنید" required/>
					<input name="start_date" id="start_date_hide" type="hidden" value="{{ old('start_date') }}" />
				</div>

				<div class="col-12 form-group">
					<label for="end_date_show">تاریخ پایان : <span class="text-danger">&starf;</span></label>
					<input class="form-control fc-datepicker" id="end_date_show" type="text" autocomplete="off" placeholder="تاریخ پایان را انتخاب کنید" required/>
					<input name="end_date" id="end_date_hide" type="hidden" value="{{ old('end_date') }}" />
				</div>

			</div>

			<div class="modal-footer justify-content-center mt-2">
				<button class="btn btn-sm btn-primary" type="submit">ثبت و ذخیره</button>
				<button class="btn btn-sm btn-danger" type="button" data-dismiss="modal">انصراف</button>
			</div>

		</form>
	</x-slot>
</x-modal>

@foreach ($specificDiscounts ?? [] as $specDiscount)
	<x-modal id="EditSpecificDiscountModal-{{ $specDiscount->id }}" size="md">
		<x-slot name="title">ویرایش تخفیف ویژه</x-slot>
		<x-slot name="body">
			<form action="{{ route('admin.specific-discounts.update', $specDiscount->id) }}" method="POST">
				@csrf
				@method('PUT')
				<div class="row">

					<div class="col-12 form-group">
						<label for="">عنوان : <span class="text-danger">&starf;</span></label>
						<input type="text" value="{{ $specDiscount->title }}" class="form-control" name="title" placeholder="عنوان تخفیف را وارد کنید" required/>
					</div>

					<div class="col-12 form-group">
						<label for="start_date_show">تاریخ شروع : <span class="text-danger">&starf;</span></label>
						<input class="form-control fc-datepicker" id="start_date_show_{{ $specDiscount->id }}" type="text" autocomplete="off" placeholder="تاریخ شروع را انتخاب کنید" required/>
						<input name="start_date" id="start_date_hide_{{ $specDiscount->id }}" type="hidden" value="{{ $specDiscount->start_date }}" />
					</div>

					<div class="col-12 form-group">
						<label for="end_date_show">تاریخ پایان : <span class="text-danger">&starf;</span></label>
						<input class="form-control fc-datepicker" id="end_date_show_{{ $specDiscount->id }}" type="text" autocomplete="off" placeholder="تاریخ پایان را انتخاب کنید" required/>
						<input name="end_date" id="end_date_hide_{{ $specDiscount->id }}" type="hidden" value="{{ $specDiscount->end_date }}" />
					</div>

				</div>

				<div class="modal-footer justify-content-center mt-2">
					<button class="btn btn-sm btn-warning" type="submit">بروزرسانی</button>
					<button class="btn btn-sm btn-danger" data-dismiss="modal" type="button" >انصراف</button>
				</div>

			</form>
		</x-slot>
	</x-modal>
@endforeach

@endsection

@section('scripts')

	@foreach ($specificDiscounts ?? [] as $specDiscount)

		@include('core::includes.date-input-script', [
			'dateInputId' => 'end_date_hide_' . $specDiscount->id,
			'textInputId' => 'end_date_show_' . $specDiscount->id,
		])

		@include('core::includes.date-input-script', [
			'dateInputId' => 'start_date_hide_' . $specDiscount->id,
			'textInputId' => 'start_date_show_' . $specDiscount->id,
		])
		
	@endforeach

	@include('core::includes.date-input-script', [
        'dateInputId' => 'end_date_hide',
        'textInputId' => 'end_date_show',
    ])

    @include('core::includes.date-input-script', [
        'dateInputId' => 'start_date_hide',
        'textInputId' => 'start_date_show',
    ])

	@include('core::includes.date-input-script', [
		'textInputId' => 'filter_start_date_show',
		'dateInputId' => 'filter_start_date_hide',
	])

	@include('core::includes.date-input-script', [
		'textInputId' => 'filter_end_date_show',
		'dateInputId' => 'filter_end_date_hide',
	])

@endsection
