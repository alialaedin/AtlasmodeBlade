@extends('admin.layouts.master')
@section('content')

<div class="page-header">
	<x-breadcrumb :items="[
		['title' => 'منو های گروه ' . $menuGroup->label, 'route_link' => 'admin.menus.index', 'parameter' => $menuGroup],
		['title' => 'ویرایش منو'],
	]"/>
</div>

<x-card>
	<x-slot name="cardTitle">ویرایش منو</x-slot>
	<x-slot name="cardOptions"><x-card-options /></x-slot>
	<x-slot name="cardBody">
		@include('components.errors')
		<form action="{{ route('admin.menus.update', $menuItem) }}" method="POST" enctype="multipart/form-data">

			@csrf
			@method('PUT')
			<input hidden name="group_id" value="{{ $menuGroup->id }}">

			<div class="row">

				<div class="col-xl-4 col-lg-6 col-12">
					<div class="form-group">
						<label>عنوان: <span class="text-danger">&starf;</span></label>
						<input type="text" name="title" class="form-control" value="{{ old('title', $menuItem->title) }}">
					</div>
				</div>

				<div class="col-xl-4 col-lg-6 col-12">
					<div class="form-group">
						<label>پدر: <span class="text-danger">&starf;</span></label>
						<select name="parent_id" id="parent-menu-select" class="form-control">
							<option 
								value="none-parent" 
								{{ old('parent_id') == 'none-parent' || is_null($menuItem->parent_id) ? 'selected' : '' }}>
								بدون پدر
							</option>
							@foreach ($menuItems->where('id', '!=', $menuItem->id) ?? [] as $menuItemDB)
								<option 
									value="{{ $menuItemDB->id }}" 
									{{ old('parent_id', $menuItem->parent_id) == $menuItemDB->id ? 'selected' : '' }}>
									{{ $menuItemDB->title }}
								</option>
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
						<select id="linkableTypeSelect" name="linkable_type" class="form-control">
							<option value="self_link" @if (old('linkable_type') == 'self_link' || $menuItem->link) selected @endif>لینک دلخواه</option>
							@foreach ($linkables as $linkable)
								<option 
									@if ($menuItem->unique_type == $linkable['unique_type']) selected @endif
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
						<select id="linkableIdSelect" name="linkable_id" class="form-control">
							<option value="">انتخاب</option>
						</select>
					</div>
				</div>

				<div class="col-xl-4 col-lg-6 col-12">
					<div class="form-group">
						<label>لینک دلخواه :</label>
						<input id="selfLinkInput" type="text" name="link" value="{{ old('link', $menuItem->link) }}" class="form-control">
					</div>
				</div>

				@php
					$checkboxes = [
						['title' => 'status', 'label' => 'وضعیت', 'default' => $menuItem->status],
						['title' => 'new_tab', 'label' => 'تب جدید', 'default' => $menuItem->new_tab],
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

<script>

	const linkables = @json($linkables);
	const menuItem = @json($menuItem);

	const linkableTypeSelect = $('#linkableTypeSelect');
	const linkableIdSelect = $('#linkableIdSelect');
	const selfLinkInput = $('#selfLinkInput');
	const parentMenuSelect = $('#parent-menu-select');

	if (menuItem.link) {
		linkableTypeSelect.prepend('<option value="">انتخاب</option>');
		linkableTypeSelect.select2({ placeholder: 'نوع لینک را اتنخاب کنید' });
	} else {
		selfLinkInput.prop('disabled', true);
		const selectedLinkable = linkables.find(l => l.unique_type == menuItem.unique_type);
		if (selectedLinkable.models !== null || selectedLinkable.models?.length > 0) {
			linkableIdSelect.select2({ placeholder: 'آیتم مورد نظر را انتخاب کنید' });
			selectedLinkable.models.forEach(model => {
				const selected = menuItem.linkable_id == model.id ? 'selected' : '';
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

	parentMenuSelect.select2();
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

	$(document).ready(() => {
		handleLinkableTypeSelect();
	});

</script>

@endsection
