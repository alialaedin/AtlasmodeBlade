@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        <x-breadcrumb :items="[['title' => 'لیست برند ها']]" />
        @can('write_brand')
            <x-create-button type="modal" target="createBrandModal" title="برند جدید" />
        @endcan
    </div>

    <x-card>
        <x-slot name="cardTitle">برند ها ({{ $brands->count() }})</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <x-table-component>
                <x-slot name="tableTh">
                    <tr>
                        <th>ردیف</th>
                        <th>نام برند</th>
                        <th>تصویر</th>
                        <th>سازنده</th>
                        <th>آخرین ویرایش کننده</th>
                        <th>وضعیت</th>
                        <th>نمایش در صفحه اصلی</th>
                        <th>تاریخ ثبت</th>
                        <th>عملیات</th>
                    </tr>
                </x-slot>
                <x-slot name="tableTd">
                    @forelse ($brands as $brand)
                        <tr>
                            <td class="font-weight-bold">{{ $loop->iteration }}</td>
                            <td>{{ $brand->name }}</td>
                            <td class="m-0 p-0">
                                @if ($brand->image)
                                    <figure class="figure my-2">
                                        <a target="_blank"
                                            href="{{ Storage::url($brand->image['uuid'] . '/' . $brand->image['file_name']) }}">
                                            <img src="{{ Storage::url($brand->image['uuid'] . '/' . $brand->image['file_name']) }}"
                                                class="img-thumbnail" alt="image" width="50"
                                                style="max-height: 32px;" />
                                        </a>
                                    </figure>
                                @else
                                    <span> - </span>
                                @endif
                            </td>
                            <td>{{ $brand->creator->name }}</td>
                            <td>{{ $brand->updater->name }}</td>
                            <td>@include('core::includes.status', ['status' => $brand->status])</td>
                            <td>
                                @if ($brand->show_index)
                                    <span><i class="text-success fs-26 fa fa-check-circle-o"></i></span>
                                @else
                                    <span><i class="text-danger fs-26 fa fa-close"></i></span>
                                @endif
                            </td>
                            <td>{{ verta($brand->created_at)->format('Y/m/d H:i') }}</td>
                            <td>

                                <button class="btn btn-sm btn-icon btn-primary"
                                    onclick="showBrandDescriptionModal('{{ $brand->description }}')" data-toggle="tooltip"
                                    data-original-title="توضیحات">
                                    <i class="fa fa-book"></i>
                                </button>

                                @can('modify_brand')
                                    @include('core::includes.edit-modal-button', [
                                        'target' => '#editBrandModal-' . $brand->id,
                                    ])
                                @endcan

                                @can('delete_brand')
                                    @include('core::includes.delete-icon-button', [
                                        'model' => $brand,
                                        'route' => 'admin.brands.destroy',
                                        'disabled' => false,
                                    ])
                                @endcan

                            </td>
                        </tr>
                    @empty
                        @include('core::includes.data-not-found-alert', ['colspan' => 9])
                    @endforelse
                </x-slot>
            </x-table-component>
        </x-slot>
    </x-card>

    @can('write_brand')
        @include('brand::admin.includes.create-modal')
    @endcan

    @can('modify_brand')
        @include('brand::admin.includes.edit-modal')
    @endcan

    @include('brand::admin.includes.show-description-modal')

@endsection

@section('scripts')
    <script>
        function showBrandDescriptionModal(description) {
            let modal = $('#showDescriptionModal');
            modal.find('#description').text(description ?? '-');
            modal.modal('show');
        }
    </script>
@endsection
