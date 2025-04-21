@extends('admin.layouts.master')
@section('content')

<div class="page-header">
  <x-breadcrumb :items="[['title' => 'بنر های تبلیغاتی']]" />
</div>

<x-card>
  <x-slot name="cardTitle">لیست بنر های تبلیغاتی ({{ $advertisements->count() }})</x-slot>
  <x-slot name="cardOptions"><x-card-options /></x-slot>
  <x-slot name="cardBody">
    <x-table-component>
      <x-slot name="tableTh">
        <tr>
          <th>ردیف</th>
          <th>عنوان</th>
          <th>لوگو</th>
          <th>وضعیت</th>
          <th>تاریخ شروع</th>
          <th>تاریخ پایان</th>
          <th>تاریخ آخرین بروزرسانی</th>
          <th>عملیات</th>
        </tr>
      </x-slot>
      <x-slot name="tableTd">
        @foreach ($advertisements as $advertise)
          <tr>
            <td class="font-weight-bold">{{ $loop->iteration }}</td>
            <td>{{ $advertise->title }}</td>
            <td>
              @if ($advertise->picture != null)
                <div class="bg-light pb-1 pt-1 img-holder img-show w-100" style="max-height: 60px; border-radius: 4px;">
                  <img src="{{ $advertise->picture_url }}" style="height: 50px;">
                </div>
              @else
                <span>-</span>
              @endif
            </td>
            <td>
              <span class="badge badge-{{ $advertise->status ? 'success' : 'danger' }}">
                {{ $advertise->status ? 'فعال' : 'غیر فعال' }}
              </span>
            </td>
            <td>{{ $advertise->start ? verta($advertise->start)->format('Y/m/d') : '-' }}</td>
            <td>{{ $advertise->end ? verta($advertise->end)->format('Y/m/d') : '-' }}</td>
            <td>{{ verta($advertise->updated_at)->format('Y/m/d H:i') }}</td>
            <td>
              <x-edit-button route="admin.advertisements.edit" :model="$advertise" :has-title="true" />
              <button
                onclick="$(this).next('form').submit()"
                class="edit-item-status-button btn btn-sm btn-secondary">
                تغییر وضعیت
              </button>
              <form 
                action="{{ route('admin.advertisements.change-status', $advertise) }}"
                method="POST">
                @csrf
                @method('PATCH')
              </form>
            </td>
          </tr>
        @endforeach
      </x-slot>
    </x-table-component>
  </x-slot>
</x-card>

@endsection
