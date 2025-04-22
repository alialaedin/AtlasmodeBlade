@extends('admin.layouts.master')
@section('content')

	<div class="page-header">
		<x-breadcrumb :items="[['title' => 'اسلایدر های ' . config('slider.groupLabels.' . $group)]]" />
		<div>
			@php $sortBtnClass = $sliders->count() > 0 ? '' : 'd-none' @endphp
			<button id="sort-btn" type="button" class="btn btn-teal btn-sm align-items-center btn-sm {{ $sortBtnClass }}">ذخیره مرتب سازی</button>
			<x-create-button route="admin.sliders.create" :parameter="['group' => $group]" title="ثبت اسلایدر جدید"/>
		</div>
	</div>

	<x-card>
		<x-slot name="cardTitle">لیست اسلایدر ها ({{ $sliders->count() }})</x-slot>
		<x-slot name="cardOptions"><x-card-options /></x-slot>
		<x-slot name="cardBody">
			<x-table-component id="sliders-table">
				<x-slot name="tableTh">
					<tr>
						<th>ردیف</th>
						<th>عنوان</th>
						<th>تصویر</th>
						<th>وضعیت</th>
						<th>تاریخ ثبت</th>
						<th>عملیات</th>
					</tr>
				</x-slot>
				<x-slot name="tableTd">
					@forelse($sliders as $slider)
						<tr class="glyphicon-move" style="cursor: move">
							<td class="d-none sort-slider-id" data-id="{{ $slider->id }}"></td>
							<td class="font-weight-bold">{{ $loop->iteration }}</td>
							<td>{{ $slider->title }}</td>
							<td>
								@if ($slider->image != null)
									@php
										$url = '/storage/' . $slider->image->uuid . '/' . $slider->image->file_name;
									@endphp
									<a href="{{ $url }}" target="_blank">
										<div class="bg-light pb-1 pt-1 img-holder img-show w-100" style="max-height: 60px; border-radius: 4px;">
											<img src="{{ $url }}" style="height: 50px;" alt="{{ $url }}">
										</div>
									</a>
								@endif
							</td>
							<td>@include('core::includes.status', ['status' => $slider->status])</td>
							<td>{{ verta($slider->created_at)->format('Y/m/d H:i') }}</td>
							<td>
								@include('core::includes.edit-icon-button', [
                  'model' => $slider,
                  'route' => 'admin.sliders.edit',
                ])
								@include('core::includes.delete-icon-button', [
                  'model' => $slider,
                  'route' => 'admin.sliders.destroy',
                ])
							</td>
						</tr>
					@empty
						@include('core::includes.data-not-found-alert', ['colspan' => 6])
					@endforelse
				</x-slot>
			</x-table-component>
		</x-slot>
	</x-card>

@endsection

@section('scripts')

<script>

	var items = document.querySelector('#sliders-table tbody');
	var sortable = Sortable.create(items, {
		handle: '.glyphicon-move',
		animation: 150
	});

	$(document).ready(() => {

		$('#sort').click(async () => {
	
			const sortButton = $('#sort');
			const sliders = [];

			sortButton.prop('disabled', true);

			$('#sliders-table tbody tr').each(function() {
				sliders.push($(this).find('.sort-slider-id').data('id'));
			});

			try {
				const url = @json(route('admin.sliders.sort', ['group' => $group]));
				const response = await fetch(url, {
					method: 'POST',
					body: { 
						orders: JSON.stringify(sliders),
						group: @json($group),
						_method: 'PATCH',
						_token: @json(csrf_token())
					},
					headers: {
						'Accept': 'application/json' 
					}
				});

				if (!response.ok) {
					throw new Error(`HTTP error! Status: ${response.status}`);
				}

				const result = await response.json();
				if (response.status === 402) {
					showValidationError(result.errors);
				}else if (response.status === 500) {
					popup('error', 'خطای سرور', result.message);
				}else if (response.status === 200) {
					popup('success', 'عملیات موفق', result.message);
				}

			} catch (error) {
				console.error('Error during fetch:', error.message);
			}finally {
				sortButton.prop('disabled', false); 
			}
		});
	});
</script>

@endsection

@section('styles')
	<style>
		.glyphicon-move::before {
			content: none;
		}
	</style>
@endsection
