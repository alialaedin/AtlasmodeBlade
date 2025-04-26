@extends('admin.layouts.master')
@section('content')

<div class="page-header">
	<x-breadcrumb :items="[
		['title' => 'منو های گروه ' . $menuGroup->label, 'route_link' => 'admin.menus.index', 'parameter' => $menuGroup],
		['title' => 'ایجاد منو جدید'],
	]"/>
</div>

<x-card>
	<x-slot name="cardTitle">ایجاد منو جدید</x-slot>
	<x-slot name="cardOptions"><x-card-options /></x-slot>
	<x-slot name="cardBody">
		@include('components.errors')
		<form id="submit-form" action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">

			@csrf
			<input hidden name="group_id" value="{{ $menuGroup->id }}">

			<div class="row">

				<div class="col-xl-4 col-lg-6 col-12">
					<div class="form-group">
						<label>عنوان: <span class="text-danger">&starf;</span></label>
						<input type="text" name="title" class="form-control" value="{{ old('title') }}">
					</div>
				</div>

				<div class="col-xl-4 col-lg-6 col-12">
					<div class="form-group">
						<label>پدر: <span class="text-danger">&starf;</span></label>
						<select name="parent_id" id="parent-menu-select" class="form-control">
							<option value="">انتخاب</option>
							<option value="none-parent" {{ old('parent_id') == 'none-parent' ? 'selected' : '' }}>بدون پدر</option>
							@foreach ($menuItems ?? [] as $menuItem)
								<option value="{{ $menuItem->id }}" {{ old('parent_id') || request('parent_id') == $menuItem->id ? 'selected' : '' }}>{{ $menuItem->title }}</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="col-xl-4 col-lg-6 col-12">
					<label>آیکون :</label>
					<div class="custom-file">
						<input type="file" name="icon" class="custom-file-input" accept="image/*" >
						<label class="custom-file-label">انتخاب تصویر</label>
					</div>
				</div>

				<div class="col-xl-4 col-lg-6 col-12">
					<div class="form-group">
						<label>نوع لینک :</label>
						<select id="uniqueTypeSelect" name="unique_type" class="form-control">
							<option value=""></option>
							<option value="self_link" @if (old('unique_type') == 'self_link') selected @endif>لینک دلخواه</option>
							@foreach ($linkables as $linkable)
								<option value="{{ $linkable['unique_type'] }}">{{ $linkable['label'] }}</option>
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
						<input id="selfLinkInput" type="text" name="link" value="{{ old('link') }}" class="form-control">
					</div>
				</div>

				@php
					$checkboxes = [
						['title' => 'status', 'label' => 'وضعیت'],
						['title' => 'new_tab', 'label' => 'تب جدید'],
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
									{{ old($checkbox['title'], 1) == 1 ? 'checked' : null }} />
								<span class="custom-control-label">{{ $checkbox['label'] }}</span>
							</label>
						@endforeach
					</div>
				</div>

			</div>

			<div class="row">
				<div class="col">
					<div class="text-center">
						<button class="btn btn-sm btn-primary" type="submit">ثبت و ذخیره</button>
						<button class="btn btn-sm btn-danger" type="button" onclick="window.location.reload()">ریست فرم</ذ>
					</div>
				</div>
			</div>

		</form>
	</x-slot>
</x-card>

@endsection

@section('scripts')

<script>

	const linkables = @json($linkables);

	const uniqueTypeSelect = $('#uniqueTypeSelect');
	const linkableIdSelect = $('#linkableIdSelect');
	const selfLinkInput = $('#selfLinkInput');
	const parentMenuSelect = $('#parent-menu-select');

	const changeSelfLinkInputDisabled = (bool) => selfLinkInput.prop('disabled', bool);
	const isEmpty = (models) => !models || models.length === 0;
	const getLinkableByUniqueType = (type) => linkables.find(l => l.unique_type == type);
	const hasValidModels = (models) => models && models.length > 0;
	const initializeSelect2 = (element, placeholder) => element.select2({ placeholder });

	const emptyLinkableIdSelect = () => {
		linkableIdSelect.empty();
		linkableIdSelect.append('<option value="">انتخاب</option>');
	}

	parentMenuSelect.select2({ placeholder: 'انتخاب پدر' });
	uniqueTypeSelect.select2({ placeholder: 'نوع لینک را انتخاب کنید' });
	linkableIdSelect.select2({ placeholder: 'ابتدا نوع لینک را انتخاب کنید' });

	function handleLinkableTypeSelect() {

		uniqueTypeSelect.on('select2:select', () => {

			const value = uniqueTypeSelect.val();

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
		const selectedUniqueType = uniqueTypeSelect.val()?.trim();
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
