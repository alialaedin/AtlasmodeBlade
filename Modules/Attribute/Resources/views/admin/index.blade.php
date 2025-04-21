@extends('admin.layouts.master')
@section('content')

	<div class="page-header">
		<x-breadcrumb :items="[['title' => 'لیست ویژگی ها']]" />
		@can('write_customer')
			<x-create-button route="admin.attributes.create" title="ویژگی جدید" />
		@endcan
	</div>

	<x-card>
		<x-slot name="cardTitle">جستجوی پیشرفته</x-slot>
		<x-slot name="cardOptions"><x-card-options /></x-slot>
		<x-slot name="cardBody">
			<form action="{{ route('admin.attributes.index') }}" method="GET">
				<div class="row">
					<div class="col-xl-3 col-lg-6 col-12 form-group">
						<label>نام:</label>
						<input type="text" name="name" value="{{ request('name') }}" class="form-control" />
					</div>
					<div class="col-xl-3 col-lg-6 col-12 form-group">
						<label>لیبل:</label>
						<input type="text" name="label" value="{{ request('label') }}" class="form-control" />
					</div>
					<div class="col-xl-3 col-lg-6 col-12 form-group">
						<label for="type">نوع ویژگی:</label>
						<select class="form-control" name="type" id="type">
							<option value=""></option>
							@foreach ($types as $type)
								<option value="{{ $type }}" {{ request('type') == $type ? 'selected' : null }}>
									{{ config('attribute.translates.types.' . $type) }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-xl-3 col-lg-6 col-12 form-group">
						<label for="style">نوع نمایش:</label>
						<select class="form-control" name="style" id="style">
							<option value="">انتخاب</option>
							@foreach ($styles as $style)
								<option value="{{ $style }}" {{ request('style') == $style ? 'selected' : null }}>
									{{ config('attribute.translates.styles.' . $style) }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-xl-3 col-lg-6 col-12 form-group">
						<div class="form-group">
							<label>نمایش در فیلتر:</label>
							<select name="show_filter" class="form-control" id="show-filter">
								<option value="">همه</option>
								<option value="0" @if (request('show_filter') == '0') selected @endif>نباشد</option>
								<option value="1" @if (request('show_filter') == '1') selected @endif>باشد</option>
							</select>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-12 form-group">
						<div class="form-group">
							<label>وضعیت :</label>
							<select name="status" class="form-control" id="status">
								<option value="">همه</option>
								<option value="0" @if (request('status') == '0') selected @endif>غیر فعال</option>
								<option value="1" @if (request('status') == '1') selected @endif>فعال</option>
							</select>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-12 form-group">
						<label for="start_date_show">از تاریخ</label>
						<input class="form-control fc-datepicker" id="start_date_show" type="text" autocomplete="off" />
						<input name="start_date" id="start_date_hide" type="hidden" value="{{ request('start_date') }}" />
					</div>
					<div class="col-xl-3 col-lg-6 col-12 form-group">
						<label for="end_date_show">تا تاریخ</label>
						<input class="form-control fc-datepicker" id="end_date_show" type="text" autocomplete="off" />
						<input name="end_date" id="end_date_hide" type="hidden" value="{{ request('end_date') }}" />
					</div> 
				</div>
				<div class="row">
					<div class="col-xl-9">
						<button class="col-12 btn btn-primary align-self-center">جستجو</button>
					</div>
					<div class="col-xl-3">
						<a href="{{ route('admin.attributes.index') }}" class="col-12 btn btn-danger align-self-center">حذف فیلتر ها<i class="fa fa-close" aria-hidden="true"></i></a>
					</div>
				</div>
			</form>
		</x-slot>
	</x-card>

	<x-card>
		<x-slot name="cardTitle">لیست ویژگی ها ({{ $attributes->count() }})</x-slot>
		<x-slot name="cardOptions"><x-card-options /></x-slot>
		<x-slot name="cardBody">
			<x-table-component>
				<x-slot name="tableTh">
					<tr>
						<th>ردیف</th>
						<th>نام</th>
						<th>لیبل</th>
						<th>نوع</th>
						<th>وضعیت</th>
						<th>نمایش در فیلتر</th>
						<th>تاریخ ثبت</th>
						<th>عملیات</th>
					</tr>
				</x-slot>
					<x-slot name="tableTd">
						@forelse($attributes as $attribute)
							<tr>
								<td class="font-weight-bold">{{ $loop->iteration }}</td>
								<td>{{ $attribute->name }}</td>
								<td>{{ $attribute->label }}</td>
								<td>{{ $attribute->type == 'select' ? 'انتخابی' : 'متنی' }}</td>
								<td>
									<x-badge :is-light="true">
										<x-slot name="type">{{ $attribute->status ? 'success' : 'danger' }}</x-slot>
										<x-slot name="text">{{ $attribute->status ? 'فعال' : 'غیر فعال' }}</x-slot>
									</x-badge>
								</td>
								<td>
									@if ($attribute->show_filter)
										<span><i class="fs-26 fa fa-check-circle-o text-success"></i></span>
									@else
										<span><i class="fs-26 fa fa-close text-danger"></i></span>
									@endif
								</td>
								<td>{{ verta($attribute->created_at)->format('Y/m/d H:i') }}</td>
								<td>
									@include('core::includes.edit-icon-button', [
										'model' => $attribute,
										'route' => 'admin.attributes.edit',
									])
									@include('core::includes.delete-icon-button', [
										'model' => $attribute,
										'route' => 'admin.attributes.destroy',
									])
								</td>
							</tr>
						@empty
							@include('core::includes.data-not-found-alert', ['colspan' => 8])
						@endforelse
					</x-slot>
			</x-table-component>
		</x-slot>
	</x-card>

@endsection

@section('scripts')

	@include('core::includes.date-input-script', [
    'dateInputId' => 'start_date_hide',
    'textInputId' => 'start_date_show',
  ])

  @include('core::includes.date-input-script', [
    'dateInputId' => 'end_date_hide',
    'textInputId' => 'end_date_show',
  ])

	<script>
		$('#type').select2({ placeholder: 'انتخاب نوع ویژگی' });
		$('#style').select2({ placeholder: 'انتخاب نوع نمایش' });
		$('#show-filter').select2({ placeholder: 'نمایش در فیلتر' });
		$('#status').select2({ placeholder: 'انتخاب وضعیت' });
	</script>
@endsection
