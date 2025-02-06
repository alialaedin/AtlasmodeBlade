@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        <x-breadcrumb :items="[['title' => 'لیست حمل و نقل ها']]" />
        <div>
            <button id="submitButton" type="submit" class="btn btn-teal btn-sm align-items-center btn-sm">ذخیره مرتب سازی</button>
            @can('write_shipping')
            <x-create-button route="admin.shippings.create" title="حمل و نقل جدید" />
            @endcan
        </div>
    </div>
    <x-card>
        <x-slot name="cardTitle">لیست حمل و نقل ها ({{ $totalShipping }})</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            @include('components.errors')
            <form id="myForm" action="{{ route('admin.shippings.sort') }}" method="POST">
                @csrf
                @method('PATCH')
                <x-table-component idTbody="items">
                    <x-slot name="tableTh">
                        <tr>
                            <th>انتخاب</th>
                            <th>ردیف</th>
                            <th>نام</th>
                            <th>لوگو</th>
                            <th>مبلغ پیش فرض (تومان)</th>
                            <th>عمومی</th>
                            <th>وضعیت</th>
                            <th>تاریخ ثبت</th>
                            <th>عملیات</th>
                        </tr>
                    </x-slot>
                    <x-slot name="tableTd">
                        @forelse ($shippings as $shipping)
                            <tr>
                                <td class="text-center"><i class="fe fe-move glyphicon-move text-dark"></i></td>
                                <input type="hidden" value="{{ $shipping->id }}" name="orders[]">
                                <td class="font-weight-bold">{{ $loop->iteration }}</td>
                                <td>{{ $shipping->name }}</td>
                                <td>
                                    @if($shipping->logo?->url)
                                        <a href="{{ $shipping->logo->url }}" target="_blank">
                                            <div class="bg-light pb-1 pt-1 img-holder img-show w-100" style="max-height: 60px; border-radius: 4px;">
                                                <img src="{{ $shipping->logo->url }}" style="height: 50px;" alt="{{ $shipping->logo->url }}">
                                            </div>
                                        </a>
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>{{ number_format($shipping->default_price) }}</td>
                                <td>
                                    @if ($shipping->is_public)
                                        <span><i class="text-success fs-26 fa fa-check-circle-o"></i></span>
                                    @else
                                        <span><i class="text-danger fs-24 fa fa-close"></i></span>
                                    @endif
                                </td>
                                <td>
                                    @if ($shipping->status)
                                        <x-badge text="فعال" type="success" isLight="true" fontSize="14" />
                                    @else
                                        <x-badge text="غیر فعال" type="danger" isLight="true" fontSize="14" />
                                    @endif
                                </td>
                                <td>{{ verta($shipping->created_at)->format('Y/m/d H:i') }}</td>
                                <td>
                                    @can('modify_shipping')
                                        <a href="{{ route('admin.shippings.cities', $shipping) }}"
                                            class="btn btn-sm btn-dark btn-icon text-white"> شهر ها
                                        </a>
                                    @endcan
                                    <a href="{{route('admin.shippings.show', $shipping)}}" 
                                        class="btn btn-sm btn-primary btn-icon text-white"> جزئیات
                                    </a>
                                    @can('modify_shipping')
                                        <a href="{{route('admin.shippings.edit', $shipping)}}"
                                            class="btn btn-sm btn-icon btn-warning text-white"> ویرایش
                                        </a>
                                    @endcan
                                    @can('delete_shipping')
                                        <button onclick="confirmDelete('delete-{{ $shipping->id }}')"
                                            class="btn btn-sm btn-icon btn-danger text-white" type="button"> حذف
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            @include('core::includes.data-not-found-alert', ['colspan' => 8])
                        @endforelse
                    </x-slot>
                </x-table-component>
            </form>
        </x-slot>
    </x-card>
    @foreach ($shippings as $shipping)
        <form action="{{ route('admin.shippings.destroy', $shipping->id) }}" method="POST" id="delete-{{ $shipping->id }}"
            style="display: none">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
@section('scripts')
    <script>
        var items = document.getElementById('items');
        var sortable = Sortable.create(items, {
            handle: '.glyphicon-move',
            animation: 150
        });
        document.getElementById('submitButton').addEventListener('click', function() {
            document.getElementById('myForm').submit();
        });
    </script>
@endsection
