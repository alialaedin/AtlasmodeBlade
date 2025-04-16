@extends('admin.layouts.master')
@section('content')

  <div class="page-header">
    <x-breadcrumb :items="[
      ['title' => 'اسلایدر های ' . config('slider.groupLabels.' . $group), 'route_link' => 'admin.sliders.index', 'parameter' => ['group' => $group]],
      ['title' => 'ثبت اسلایدر جدید'],
    ]" />
  </div>
    
  <x-card>
    <x-slot name="cardTitle">ثبت اسلایدر جدید</x-slot>
    <x-slot name="cardOptions"><x-card-options /></x-slot>
    <x-slot name="cardBody">
      <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">

        @csrf
        <input hidden name="group" value="{{$group}}">

        <div class="row">

          <div class="col-12">
            <div class="form-group">
              <label>عنوان :<span class="text-danger">&starf;</span></label>
              <input type="text" class="form-control" name="title"  placeholder="عنوان را اینجا وارد کنید" value="{{ old('title') }}" required autofocus>
            </div>
          </div>

          <div class="col-xl-3 col-lg-6 col-12">
            <label>تصویر :<span class="text-danger">&starf;</span></label>
            <div class="custom-file">
              <input type="file" name="image" class="custom-file-input" accept="image/*" >
              <label class="custom-file-label">انتخاب تصویر</label>
            </div>
          </div>

          <div class="col-xl-3 col-lg-6 col-12">
            <div class="form-group">
              <label>نوع لینک :</label>
              <select id="linkableTypeSelect" name="linkable_type" class="form-control">
                <option value="">انتخاب</option>
                <option value="self_link" @if (old('linkable_type') == 'self_link') selected @endif>لینک دلخواه</option>
                @foreach ($linkables as $linkable)
                  <option 
                    @if (old('linkable_type') == $linkable['unique_type']) selected @endif
                    value="{{ $linkable['unique_type'] }}">
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
              <input id="selfLinkInput" type="text" name="link" class="form-control">
            </div>
          </div>

          <div class="col-12">
            <div class="form-group">
              <label>توضیحات :</label>
              <textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
            </div>
          </div>
          
          <div class="col-12">
            <label for="status-checkbox" class="custom-control custom-checkbox">
              <input id="status-checkbox" name="status" type="checkbox" class="custom-control-input" value="1"/>
              <span class="custom-control-label">وضعیت</span>
            </label>
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

    const makeSelect2Label = (element, label) => $(element).select2({ placeholder: label });

    makeSelect2Label('#linkableTypeSelect', 'نوع لینک را اتنخاب کنید');
    makeSelect2Label('#linkableIdSelect', 'ابتدا نوع لینک را انتخاب کنید');

    function handleLinkableTypeSelect() {

      const linkableTypeSelect = $('#linkableTypeSelect');
      const linkableIdSelect = $('#linkableIdSelect');
      const selfLinkInput = $('#selfLinkInput');
      const linkables = @json($linkables); 

      const changeSelfLinkInputDisabled = (bool) => selfLinkInput.prop('disabled', bool);

      changeSelfLinkInputDisabled(true);

      linkableTypeSelect.on('select2:select', (event) => {

        const value = event.target.value;
        changeSelfLinkInputDisabled(value !== 'self_link');

        if (value !== 'self_link') {

          const linakbleUniqueType = value;
          const linkable = linkables.find(l => l.unique_type === linakbleUniqueType);

          const emptyLinkableIdSelect = () => {
            linkableIdSelect.empty();
            linkableIdSelect.append('<option value="">انتخاب</option>');
          }

          if (linkable.models == null || linkable.models?.length == 0) {
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