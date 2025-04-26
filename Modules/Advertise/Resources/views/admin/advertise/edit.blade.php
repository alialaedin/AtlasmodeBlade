@extends('admin.layouts.master')
@section('content')

<div class="page-header">
	<x-breadcrumb :items="[
		['title' => 'بنر های تبلیغاتی', 'route_link' => 'admin.advertisements.index'],
		['title' => 'ویرایش بنر تبلیغاتی'],
	]"/>
</div>

<x-card>
	<x-slot name="cardTitle">ویرایش بنر تبلیغاتی</x-slot>
	<x-slot name="cardOptions"><x-card-options /></x-slot>
	<x-slot name="cardBody">
		@include('components.errors')
		<form id="submit-form" action="{{ route('admin.advertisements.update',$advertise) }}" method="POST" enctype="multipart/form-data">

			@csrf
			@method('PUT')

			<div class="row">

				<div class="col-xl-4 col-lg-6 col-12">
					<label>تصویر :</label>
					<div class="custom-file">
						<input type="file" name="picture" class="custom-file-input" accept="image/*" >
						<label class="custom-file-label">انتخاب تصویر</label>
					</div>
				</div>

				<div class="col-xl-4 col-lg-6 col-12">
					<div class="form-group">
						<label for="start_show">تاریخ آغاز :</label>
						<input class="form-control fc-datepicker" id="start_show" type="text" autocomplete="off" placeholder="انتخاب تاریخ آغاز" />
						<input name="start" id="start_hide" type="hidden" value="{{ old('start', $advertise->start) }}" />
					</div>
				</div>

				<div class="col-xl-4 col-lg-6 col-12">
					<div class="form-group">
						<label for="start_show">تاریخ پایان :</label>
						<input class="form-control fc-datepicker" id="end_show" type="text" autocomplete="off" placeholder="انتخاب تاریخ پایان" />
						<input name="end" id="end_hide" type="hidden" value="{{ old('end', $advertise->end) }}" />
					</div>
				</div>

				<div class="col-xl-4 col-lg-6 col-12">
					<div class="form-group">
						<label>نوع لینک :</label>
						<select id="linkableTypeSelect" name="linkable_unique_type" class="form-control">
							<option value="self_link" @if (old('linkable_unique_type') == 'self_link' || $advertise->link) selected @endif>لینک دلخواه</option>
							@foreach ($linkables as $linkable)
								<option 
									@if ($advertise->unique_type == $linkable['unique_type']) selected @endif
									value="{{ $linkable['unique_type'] }}">
									{{ $linkable['label'] }}
								</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="col-xl-4 col-lg-6 col-12">
					<div class="form-group">
						<label>آیتم های لینک :</label>
						<input hidden name="linkable_type">	
						<select id="linkableIdSelect" name="linkable_id" class="form-control">
							<option value="">انتخاب</option>
						</select>
					</div>
				</div>

				<div class="col-xl-4 col-lg-6 col-12">
					<div class="form-group">
						<label>لینک دلخواه :</label>
						<input id="selfLinkInput" type="text" name="link" value="{{ old('link', $advertise->link) }}" class="form-control">
					</div>
				</div>

				@if ($advertise->picture !== null)
					<div class="col-12">
						<p class="header pr-2 font-weight-bold fs-22">تصویر فعلی</p>
						<div class="bg-light pb-1 pt-1 img-holder img-show w-100" style="border-radius: 4px;">
							<img src="{{ $advertise->picture_url }}">
						</div>
					</div>
				@endif

				@php
					$checkboxes = [
						['title' => 'status', 'label' => 'وضعیت', 'default' => $advertise->status],
						['title' => 'new_tab', 'label' => 'تب جدید', 'default' => $advertise->new_tab],
					];
				@endphp

				<div class="col-12">
					<div class="form-group">
						@foreach ($checkboxes as $checkbox)
							<label class="custom-control custom-checkbox">
								<input 
									type="checkbox" 
									class="custom-control-input" 
									name="{{ $checkbox['title'] }}" 
									value="1" 
									{{ old($checkbox['title'], $checkbox['default']) == 1 ? 'checked' : null }} />
								<span class="custom-control-label">{{ $checkbox['label'] }}</span>
							</label>
						@endforeach
					</div>
				</div>

			</div>

			<div class="row">
				<div class="col">
					<div class="text-center">
						<button class="btn btn-sm btn-warning" type="submit">بروزرسانی</button>
						<button class="btn btn-sm btn-danger" type="button" onclick="window.location.reload()">ریست فرم</ذ>
					</div>
				</div>
			</div>

		</form>
	</x-slot>
</x-card>

@endsection

@section('scripts')

  @include('core::includes.date-input-script', [
    'dateInputId' => 'start_hide',
    'textInputId' => 'start_show'
  ])

  @include('core::includes.date-input-script', [
    'dateInputId' => 'end_hide',
    'textInputId' => 'end_show'
  ])

<script>

	const advertise = @json($advertise);
	const linkables = @json($linkables);

	const linkableTypeSelect = $('#linkableTypeSelect');
	const linkableIdSelect = $('#linkableIdSelect');
	const selfLinkInput = $('#selfLinkInput');

	if (advertise.link) {
		linkableTypeSelect.prepend('<option value="">انتخاب</option>');
		linkableTypeSelect.select2({ placeholder: 'نوع لینک را اتنخاب کنید' });
	} else {
		selfLinkInput.prop('disabled', true);
		const selectedLinkable = linkables.find(l => l.unique_type == advertise.unique_type);
		if (selectedLinkable.models !== null || selectedLinkable.models?.length > 0) {
			linkableIdSelect.select2({ placeholder: 'آیتم مورد نظر را انتخاب کنید' });
			selectedLinkable.models.forEach(model => {
				const selected = advertise.linkable_id == model.id ? 'selected' : '';
				linkableIdSelect.append(`<option value="${model.id}" ${selected}>${model.title}</option>`);
			});
		} else {
			linkableIdSelect.select2({ placeholder: 'آیتمی برای انتخاب وجود ندارد' });
		}
	}

	const changeSelfLinkInputDisabled = (bool) => selfLinkInput.prop('disabled', bool);
	const isEmpty = (models) => !models || models.length === 0;
	const getLinkableByUniqueType = (type) => linkables.find(l => l.unique_type == type);
	const hasValidModels = (models) => models && models.length > 0;
	const initializeSelect2 = (element, placeholder) => element.select2({ placeholder });

	const emptyLinkableIdSelect = () => {
		linkableIdSelect.empty();
		linkableIdSelect.append('<option value="">انتخاب</option>');
	}

	linkableTypeSelect.select2();

	function handleLinkableTypeSelect() {

		linkableTypeSelect.on('change', () => {

			const value = linkableTypeSelect.val();

			changeSelfLinkInputDisabled(value !== 'self_link');
			emptyLinkableIdSelect();

			if (value == 'self_link') {
				initializeSelect2(linkableIdSelect, 'لطفا لینک دلخواه را پر کنید');
				return;
			}

			const linkable = getLinkableByUniqueType(value);

			if (isEmpty(linkable?.models ?? [])) {
				initializeSelect2(linkableIdSelect, 'آیتمی برای انتخاب وجود ندارد');
				return;
			}

			initializeSelect2(linkableIdSelect, 'آیتم مورد نظر را انتخاب کنید');
			linkable.models.forEach((model) => {
				linkableIdSelect.append(`<option value="${model.id}">${model.title}</option>`);
			});

		});
	}

	function submit(event) {
		event.preventDefault();
		const selectedUniqueType = linkableTypeSelect.val()?.trim();
		if (selectedUniqueType && selectedUniqueType != 'self_link') {
			const linkable = getLinkableByUniqueType(selectedUniqueType);
			if (linkable) {
				$(event.currentTarget).find('input[name=linkable_type]').val(linkable.linkable_type);
			}
		}
		$(event.currentTarget).off('submit').submit();
		$(event.currentTarget).on('submit', submit);
	}

	$(document).ready(() => {
		handleLinkableTypeSelect();
		$("#submit-form").submit((event) => {
			submit(event);
		});
	});

</script>

@endsection
