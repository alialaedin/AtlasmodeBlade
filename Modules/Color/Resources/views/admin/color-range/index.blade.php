@extends('admin.layouts.master')

@section('content')

<div class="page-header">
  <x-breadcrumb :items="[['title' => 'طیف های رنگی']]" />
  <div>
    @php $sortBtnClass = $colorRanges->count() > 0 ? '' : 'd-none' @endphp
    <button id="sortBtn" type="submit" class="btn btn-teal btn-sm btn-sm {{ $sortBtnClass }}">ذخیره مرتب سازی</button>
    <x-create-button route="admin.color-ranges.create" title="طیف رنگی جدید" />
  </div>
</div>

<x-card>
  <x-slot name="cardTitle">لیست طیف های رنگی ({{ $colorRanges->count() }})</x-slot>
  <x-slot name="cardOptions"><x-card-options /></x-slot>
  <x-slot name="cardBody">
    <x-table-component id="colorRanges-table">
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
        @forelse($colorRanges as $colorRange)
          <tr class="glyphicon-move" style="cursor: move">
            <td class="d-none sort-colorRange-id" data-id="{{ $colorRange->id }}"></td>
            <td class="font-weight-bold">{{ $loop->iteration }}</td>
            <td>{{ $colorRange->title }}</td>
            <td class="m-0 p-0">
              <figure class="figure my-2">
                <img 
                  src="{{ Storage::url($colorRange->logo->uuid . '/' . $colorRange->logo->file_name) }}" 
                  class="img-thumbnail" alt="image" width="60" style="max-height: 40px;"
                />
              </figure>
            </td>
            <td>@include('core::includes.status', ['status' => $colorRange->status])</td>
            <td>{{ verta($colorRange->created_at)->format('Y/m/d H:i') }}</td>
            <td>
              @include('core::includes.edit-icon-button', [
                'model' => $colorRange,
                'route' => 'admin.color-ranges.edit',
              ])
              @include('core::includes.delete-icon-button', [
                'model' => $colorRange,
                'route' => 'admin.color-ranges.destroy',
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

	var items = document.querySelector('#colorRanges-table tbody');
	var sortable = Sortable.create(items, {
		handle: '.glyphicon-move',
		animation: 150
	});

	$(document).ready(() => {

		$('#sortBtn').click(async () => {
	
			const sortButton = $('#sortBtn');
			const colorRanges = [];

			sortButton.prop('disabled', true);

			$('#colorRanges-table tbody tr').each(function() {
				colorRanges.push($(this).find('.sort-colorRange-id').data('id'));
			});

			try {
				const url = @json(route('admin.color-ranges.sort'));
				const response = await fetch(url, {
					method: 'PATCH',
					body: JSON.stringify({ 
            color_range_ids: colorRanges,
          }),
					headers: {
						'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': @json(csrf_token())
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
