@extends('admin.layouts.master')
@section('content')

  <div class="page-header">
    <x-breadcrumb :items="[
      [
				'title' => 'اسلایدر های ' . config('slider.groupLabels.' . $slider->group), 
				'route_link' => 'admin.sliders.index', 
				'parameter' => ['group' => $slider->group]
			],
      ['title' => 'ویرایش اسلایدر'],
    ]" />
  </div>
    
  <x-card>
		<x-slot name="cardTitle">ویرایش اسلایدر</x-slot>
		<x-slot name="cardOptions"><x-card-options /></x-slot>
		<x-slot name="cardBody">
			<form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">

				@csrf
				@method('PUT')
				<input hidden name="group" value="{{ $slider->group }}">

				<div class="row">

					<div class="col-12">
						<div class="form-group">
							<label>عنوان :<span class="text-danger">&starf;</span></label>
							<input type="text" class="form-control" name="title"  placeholder="عنوان را اینجا وارد کنید" value="{{ old('title', $slider->title) }}" required autofocus>
						</div>
					</div>

					<div class="col-xl-3 col-lg-6 col-12">
						<label>تصویر :</label>
						<div class="custom-file">
							<input type="file" name="image" class="custom-file-input" accept="image/*" >
							<label class="custom-file-label">انتخاب تصویر</label>
						</div>
					</div>

					<div class="col-xl-3 col-lg-6 col-12">
						<div class="form-group">
							<label>نوع لینک :</label>
							<select id="linkableTypeSelect" name="linkable_type" class="form-control">
								<option value="self_link" @if (old('linkable_type') == 'self_link' || $slider->link) selected @endif>لینک دلخواه</option>
								@foreach ($linkables as $linkable)
									<option 
										@if ($slider->unique_type == $linkable['linkable_type']) selected @endif
										value="{{ $linkable['linkable_type'] }}">
										{{ $linkable['label'] }}
									</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="col-xl-3 col-lg-6 col-12">
						<div class="form-group">
							<label>آیتم های لینک :</label>
							<select id="linkableIdSelect" name="linkable_id" class="form-control">
								<option value="">انتخاب</option>
							</select>
						</div>
					</div>

					<div class="col-xl-3 col-lg-6 col-12">
						<div class="form-group">
							<label>لینک دلخواه :</label>
							<input id="selfLinkInput" type="text" name="link" value="{{ old('link', $slider->link) }}" class="form-control">
						</div>
					</div>

					<div class="col-12">
						<div class="form-group">
							<label>توضیحات :</label>
							<textarea name="description" class="form-control" rows="5">{{ old('description', $slider->description) }}</textarea>
						</div>
					</div>
					
					<div class="col-12">
						<label for="status-checkbox" class="custom-control custom-checkbox">
							<input 
								{{ old('status', $slider->status) ? 'checked' : '' }}
								id="status-checkbox" 
								name="status" 
								type="checkbox" 
								class="custom-control-input" 
								value="1"
							/>
							<span class="custom-control-label">وضعیت</span>
						</label>
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

		const slider = @json($slider);
		const linkables = @json($linkables);

		const linkableTypeSelect = $('#linkableTypeSelect');
		const linkableIdSelect = $('#linkableIdSelect');
		const selfLinkInput = $('#selfLinkInput');

		if (slider.link) {
			linkableTypeSelect.prepend('<option value="">انتخاب</option>');
			linkableTypeSelect.select2({ placeholder: 'نوع لینک را اتنخاب کنید' });
		} else {
			selfLinkInput.prop('disabled', true);
			const selectedLinkable = linkables.find(l => l.linkable_type == slider.linkable_type);
			if (selectedLinkable.models !== null || selectedLinkable.models?.length > 0) {
				linkableIdSelect.select2({ placeholder: 'آیتم مورد نظر را انتخاب کنید' });
				selectedLinkable.models.forEach(model => {
					const selected = slider.linkable_id == model.id ? 'selected' : '';
					linkableIdSelect.append(`<option value="${model.id}" ${selected}>${model.title}</option>`);
				});
			} else {
				linkableIdSelect.select2({ placeholder: 'آیتمی برای انتخاب وجود ندارد' });
			}
		}

    const makeSelect2Label = (element, label) => $(element).select2({ placeholder: label });
		const changeSelfLinkInputDisabled = (bool) => selfLinkInput.prop('disabled', bool);
		const emptyLinkableIdSelect = () => {
			linkableIdSelect.empty();
			linkableIdSelect.append('<option value="">انتخاب</option>');
		}

    function handleLinkableTypeSelect() {

      linkableTypeSelect.on('select2:select', (event) => {

        const value = event.target.value;
        changeSelfLinkInputDisabled(value !== 'self_link');

        if (value !== 'self_link') {

          const linakbleUniqueType = value;
          const linkable = linkables.find(l => l.unique_type === linakbleUniqueType);

          if (linkable.models == null || linkable.models.length == 0) {
            emptyLinkableIdSelect();
            makeSelect2Label(linkableIdSelect, 'آیتمی برای انتخاب وجود ندارد');
            return;
          } 

          emptyLinkableIdSelect();
          makeSelect2Label(linkableIdSelect, 'آیتم مورد نظر را انتخاب کنید');
          linkable.models.forEach(model => {
            linkableIdSelect.append(`<option value="${model.id}">${model.title}</option>`);
          });

        }
      });
    }

    $(document).ready(() => {
      handleLinkableTypeSelect();
    });

  </script>

@endsection