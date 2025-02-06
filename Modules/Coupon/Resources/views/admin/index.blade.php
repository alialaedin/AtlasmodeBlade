@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        <x-breadcrumb :items="[['title' => 'لیست کد تخفیف ها']]" />
        <x-create-button route="admin.coupons.create" title="کد تخفیف جدید" />
    </div>

    <x-card>
        <x-slot name="cardTitle">جستجوی پیشرفته</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <form action="{{ route('admin.coupons.index') }}" method="GET">
                <div class="row">

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>عنوان :</label>
                            <input type="text" class="form-control" name="title" value="{{ request('title') }}">
                        </div>
                    </div>


                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="start_date_show">تاریخ شروع :</label>
                            <input class="form-control fc-datepicker" id="start_date_show" type="text"
                                autocomplete="off" />
                            <input name="start_date" id="start_date_hide" type="hidden"
                                value="{{ request('start_date') }}" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="end_date_show">تاریخ پایان :</label>
                            <input class="form-control fc-datepicker" id="end_date_show" type="text"
                                autocomplete="off" />
                            <input name="end_date" id="end_date_hide" type="hidden" value="{{ request('end_date') }}" />
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-xl-8 col-md-6 col-12">
                        <button class="btn btn-primary btn-block" type="submit">جستجو <i class="fa fa-search"></i></button>
                    </div>

                    <div class="col-xl-4 col-md-6 col-12">
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-danger btn-block">حذف همه فیلتر ها
                            <i class="fa fa-close"></i></a>
                    </div>

                </div>
            </form>
        </x-slot>
    </x-card>

    <x-card>
        <x-slot name="cardTitle">کد تخفیف ها ({{ $coupons->total() }})</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <x-table-component>
                <x-slot name="tableTh">
                    <tr>
                        <th>ردیف</th>
                        <th>عنوان</th>
                        <th>کد</th>
                        <th>سقف استفاده</th>
                        <th>سقف استفاده برای هر کاربر</th>
                        <th>تعداد استفاده</th>
                        <th>تاریخ شروع</th>
                        <th>تاریخ پایان</th>
                        <th>تاریخ ثبت</th>
                        <th>عملیات</th>
                    </tr>
                </x-slot>
                <x-slot name="tableTd">
                    @forelse ($coupons as $coupon)
                        <tr>
                            <td class="font-weight-bold">{{ $loop->iteration }}</td>
                            <td>{{ $coupon->title }}</td>
                            <td>{{ $coupon->code }}</td>
                            <td>{{ $coupon->usage_limit ?? 0 }}</td>
                            <td>{{ $coupon->usage_per_user_limit ?? 0 }}</td>
                            <td>{{ $coupon->total_usage }}</td>
                            <td>{{ verta($coupon->start_date)->format('Y/m/d H:i') }}</td>
                            <td>{{ verta($coupon->end_date)->format('Y/m/d H:i') }}</td>
                            <td>{{ verta($coupon->created_at)->format('Y/m/d H:i') }}</td>
                            <td>

                                @can('read_coupon')
                                    @include('core::includes.show-icon-button', [
                                        'model' => $coupon,
                                        'route' => 'admin.coupons.show',
                                    ])
                                @endcan

                                @can('modify_coupon')
                                    @include('core::includes.edit-icon-button', [
                                        'model' => $coupon,
                                        'route' => 'admin.coupons.edit',
                                    ])
                                @endcan

                                @can('delete_coupon')
                                    @include('core::includes.delete-icon-button', [
                                        'model' => $coupon,
                                        'route' => 'admin.coupons.destroy',
                                    ])
                                @endcan

                            </td>
                        </tr>
                    @empty
                        @include('core::includes.data-not-found-alert', ['colspan' => 10])
                    @endforelse
                </x-slot>
                <x-slot name="extraData">
                    {{ $coupons->onEachSide(0)->links('vendor.pagination.bootstrap-4') }}
                </x-slot>
            </x-table-component>
        </x-slot>
    </x-card>
@endsection

@section('scripts')
    @include('core::includes.date-input-script', [
        'dateInputId' => 'start_date_hide',
        'textInputId' => 'start_date_show',
    ])

    @include('core::includes.date-input-script', [
        'dateInputId' => 'end_date_hide',
        'textInputId' => 'end_date_show',
    ])
@endsection
