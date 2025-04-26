@extends('admin.layouts.master')
@section('content')

  <div class="page-header">
    <x-breadcrumb :items="[
      ['title' => 'طیف های رنگی', 'route_link' => 'admin.color-ranges.index'],
      ['title' => 'ویرایش طیف رنگی'],
    ]" />
  </div>
    
  <x-card>
    <x-slot name="cardTitle">ویرایش طیف رنگی</x-slot>
    <x-slot name="cardOptions"><x-card-options /></x-slot>
    <x-slot name="cardBody">
      <form action="{{ route('admin.color-ranges.update', $colorRange) }}" method="POST" enctype="multipart/form-data">

        @csrf
        @method("PUT")

        <div class="row">

          <div class="col-lg-6 col-12">
            <div class="form-group">
              <label>عنوان :<span class="text-danger">&starf;</span></label>
              <input type="text" class="form-control" name="title"  placeholder="عنوان را اینجا وارد کنید" value="{{ old('title', $colorRange->title) }}" required autofocus>
            </div>
          </div>

          <div class="col-lg-6 col-12">
            <label>تصویر :</label>
            <div class="custom-file">
              <input type="file" name="logo" class="custom-file-input" accept="image/*" >
              <label class="custom-file-label">انتخاب تصویر</label>
            </div>
          </div>

          <div class="col-12">
            <div class="form-group">
              <label>توضیحات :<span class="text-danger">&starf;</span></label>
              <textarea name="description" class="form-control" rows="5">{{ old('description', $colorRange->description) }}</textarea>
            </div>
          </div>
          
          <div class="col-12">
            <label for="status-checkbox" class="custom-control custom-checkbox">
              <input id="status-checkbox" name="status" type="checkbox" {{ old('status', $colorRange->status) ? 'checked' : '' }} class="custom-control-input" value="1"/>
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