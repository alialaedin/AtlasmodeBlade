@extends('admin.layouts.master')
@section('content')

  <div class="page-header">
		<x-breadcrumb :items="[
      ['title' => 'لیست تخفیفات ویژه', 'route_link' => 'admin.specific-discounts.index'],
      ['title' => 'انواع تخفیفات', 'route_link' => 'admin.specific-discounts.types.index', 'parameter' => $specificDiscountType->specific_discount],
      ['title' => 'آیتم های تخفیف']
    ]" />
		<x-create-button route="admin.specific-discounts.items.create" :parameter="$specificDiscountType" title="ایجاد آیتم جدید" />
  </div>

  <x-card>
    <x-slot name="cardTitle">
      <span class="font-weight-normal">عنوان تخفیف : <b>{{ $specificDiscountType->specific_discount->title}}</b></span> - 
      <span class="font-weight-normal">میزان تخفیف : <b>{{ number_format($specificDiscountType->discount) .' '. $specificDiscountType->discount_type == 'flat' ? 'تومان' : 'درصد' }}</b></span>
    </x-slot>
    <x-slot name="cardOptions"><x-card-options /></x-slot>
    <x-slot name="cardBody">
      <x-table-component>
        <x-slot name="tableTh">
          <tr>
            <th>ردیف</th>
            <th>شناسه آیتم</th>
            <th>نوع</th>
            <th>عملیات</th>
          </tr>
        </x-slot>
        <x-slot name="tableTd">
          @forelse ($specificDiscountItems as $item)
            <tr>
              <td class="font-weight-bold">{{ $loop->iteration }}</td>
              <td>{{ $item->id }}</td>
              <td>{{ $item->type_label }}</td>
              <td>

                @include('core::includes.edit-icon-button', [
                  'route' => 'admin.specific-discounts.items.edit',
                  'model' => $item,
                  'title' => 'ویرایش'
                ])

                @include('core::includes.delete-icon-button', [
                  'model' => $item,
                  'route' => 'admin.specific-discounts.items.destroy',
                  'disabled' => false,
                  'title' => 'حذف'
                ])

              </td>
            </tr>
          @empty
            @include('core::includes.data-not-found-alert', ['colspan' => 4])
          @endforelse
        </x-slot>
      </x-table-component>
    </x-slot>
  </x-card>

@endsection