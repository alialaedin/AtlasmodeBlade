@extends('admin.layouts.master')
@section('content')

  <div class="page-header">
		<x-breadcrumb :items="[
      ['title' => 'لیست تخفیفات ویژه', 'route_link' => 'admin.specific-discounts.index'],
      ['title' => 'انواع تخفیفات']
    ]" />
		<x-create-button type="modal" target="CreateNewSpecificDiscountTypeModal" title="نوع تخفیف جدید" />
  </div>

  <x-card>
    <x-slot name="cardTitle">{{ $specificDiscount->title }}</x-slot>
    <x-slot name="cardOptions"><x-card-options /></x-slot>
    <x-slot name="cardBody">
      <x-table-component>
        <x-slot name="tableTh">
          <tr>
            <th>ردیف</th>
            <th>شناسه</th>
            <th>تخفیف</th>
            <th>نوع تخفیف</th>
            <th>عملیات</th>
          </tr>
        </x-slot>
        <x-slot name="tableTd">
          @forelse ($specificDiscountTypes as $type)
            <tr>
              <td class="font-weight-bold">{{ $loop->iteration }}</td>
              <td>{{ $type->id }}</td>
              <td>{{ number_format($type->discount) }}</td>
              <td>{{ $type->discount_type_label }}</td>
              <td>

                @include('core::includes.edit-modal-button', [
                  'target' => '#EditSpecificDiscountTypeModal-' . $type->id,
                  'title' => 'ویرایش'
                ])

                <a 
                  href="{{ route('admin.specific-discounts.items.index', $type) }}"
                  class="btn btn-sm btn-dark text-white">
                  <span>افزودن آیتم</span>
                  <i class="fa fa-plus mr-1"></i>
                </a>

                @include('core::includes.delete-icon-button', [
                  'model' => $type,
                  'route' => 'admin.specific-discounts.destroy',
                  'disabled' => false,
                  'title' => 'حذف'
                ])

              </td>
            </tr>
          @empty
            @include('core::includes.data-not-found-alert', ['colspan' => 5])
          @endforelse
        </x-slot>
      </x-table-component>
    </x-slot>
  </x-card>

  <x-modal id="CreateNewSpecificDiscountTypeModal" size="md">
    <x-slot name="title">ایجاد نوع جدید</x-slot>
    <x-slot name="body">
      <form action="{{ route('admin.specific-discounts.types.store', $specificDiscount) }}" class="specific-discount-type-form" method="POST">
        @csrf
        <div class="row">

          <div class="col-12 form-group">
            <label for="">نوع تخفیف : <span class="text-danger">&starf;</span></label>
            <select name="discount_type" class="form-control specific-discount-type-select" required>
              <option value=""></option>
              <option value="percentage">درصدی</option>
              <option value="flat">ثابت</option>
            </select>
          </div>
          
          <div class="col-12 form-group">
            <label for="">میزان تخفیف : <span class="text-danger">&starf;</span></label>
            <input type="text" class="form-control comma discount-amount" name="discount">
          </div>

        </div>

        <div class="modal-footer justify-content-center mt-2">
          <button class="btn btn-sm btn-primary" type="submit">ثبت و ذخیره</button>
          <button class="btn btn-sm btn-danger" type="button" data-dismiss="modal">انصراف</button>
        </div>

      </form>
    </x-slot>
  </x-modal>

  @foreach ($specificDiscountTypes ?? [] as $type)
    <x-modal id="EditSpecificDiscountTypeModal-{{ $type->id }}" size="md">
      <x-slot name="title">ویرایش تخفیف ویژه</x-slot>
      <x-slot name="body">
        <form action="{{ route('admin.specific-discounts.types.update', $type) }}" class="specific-discount-type-form" method="POST">

          @csrf
          @method('PUT')

          <div class="row">

            <div class="col-12 form-group">
              <label for="">نوع تخفیف : <span class="text-danger">&starf;</span></label>
              <select name="discount_type" class="form-control specific-discount-type-select" required>
                <option value=""></option>
                <option value="percentage" @if ($type->discount_type === 'percentage') selected @endif>درصدی</option>
                <option value="flat" @if ($type->discount_type === 'flat') selected @endif>ثابت</option>
              </select>
            </div>
            
            <div class="col-12 form-group">
              <label for="">میزان تخفیف : <span class="text-danger">&starf;</span></label>
              <input type="text" class="form-control comma discount-amount" name="discount" value="{{ number_format($type->discount) }}">
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
    <script>

      $('.specific-discount-type-select').each(function() {
        $(this).select2({
          placeholder: 'انتخاب نوع تخفیف'
        });
      });

      $('.specific-discount-type-form').each(function () {  
        $(this).on('submit', (event) => {  
          event.preventDefault();

          let discountInput = $(this).find('.discount-amount');  
          discountInput.val(discountInput.val()?.replace(/,/g, ""));

          $(this).off('submit'); 
          $(this).submit();
        });  
      });  

    </script>
@endsection