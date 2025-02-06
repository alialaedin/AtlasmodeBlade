@extends('admin.layouts.master')
@section('content')

    <div class="page-header">
        <x-breadcrumb :items="[['title' => 'لیست بسته بندی های هدیه']]" />
        @can('write_gift_package')
            <x-create-button type="modal" target="create-gift-package-modal" title="بسته بندی هدیه جدید" />
        @endcan
    </div>

    <x-card>
        <x-slot name="cardTitle">بسته بندی های هدیه ({{ $giftPackages->count() }})</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <x-table-component>
                <x-slot name="tableTh">
                    <tr>
                        <th>ردیف</th>
                        <th>نام</th>
                        <th>تصویر</th>
                        <th>قیمت (تومان)</th>
                        <th>ادمین سازنده</th>
                        <th>آخرین ویرایش کننده</th>
                        <th>وضعیت</th>
                        <th>تاریخ ثبت</th>
                        <th>عملیات</th>
                    </tr>
                </x-slot>
                <x-slot name="tableTd">
                    @forelse ($giftPackages as $giftPackage)
                        <tr>
                            <td class="font-weight-bold">{{ $loop->iteration }}</td>
                            <td>{{ $giftPackage->name }}</td>
                            <td class="m-0 p-0">
                                @if ($giftPackage->image)
                                    <figure class="figure my-2">
                                        <a target="_blank" href="{{ $giftPackage->image->url }}">
                                            <img src="{{ $giftPackage->image->url }}" class="img-thumbnail" alt="image"
                                                width="50" style="max-height: 32px;" />
                                        </a>
                                    </figure>
                                @else
                                    <span> - </span>
                                @endif
                            </td>
                            <td>{{ number_format($giftPackage->price) }}</td>
                            <td>{{ $giftPackage->creator->name }}</td>
                            <td>{{ $giftPackage->updater->name }}</td>
                            <td>
                                <x-badge isLight="true">
                                    <x-slot name="type">{{ $giftPackage->status ? 'success' : 'danger' }}</x-slot>
                                    <x-slot name="text">{{ $giftPackage->status ? 'فعال' : 'غیر فعال' }}</x-slot>
                                </x-badge>
                            </td>
                            <td>{{ verta($giftPackage->created_at)->format('Y/m/d H:i') }}</td>
                            <td>

                                <button class="btn btn-sm btn-icon btn-primary"
                                    onclick="showDescriptionModal('{{ $giftPackage->description }}')" data-toggle="tooltip"
                                    data-original-title="توضیحات">
                                    <span>توضیحات</span>
                                    <i class="fa fa-book mr-1"></i>
                                </button>

                                @can('modify_gift_package')
                                    @include('core::includes.edit-modal-button', [
                                        'target' => '#editGiftPackageModal-' . $giftPackage->id,
                                        'title' => 'ویرایش',
                                    ])
                                @endcan

                                @can('delete_gift_package')
                                    @include('core::includes.delete-icon-button', [
                                        'model' => $giftPackage,
                                        'route' => 'admin.gift-packages.destroy',
                                        'title' => 'حذف',
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

    @can('write_gift_package')
        <x-modal id="create-gift-package-modal" size="md">
            <x-slot name="title">ثبت بسته بندی هدیه جدید</x-slot>
            <x-slot name="body">
                <form action="{{ route('admin.gift-packages.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="row">

                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">نام بسته : <span class="text-danger">&starf;</span></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">قیمت (تومان) : <span class="text-danger">&starf;</span></label>
                                <input type="text" class="form-control comma" name="price" value="{{ old('price') }}"
                                    required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">تصویر : <span class="text-danger">&starf;</span></label>
                                <input type="file" class="form-control" name="image" required>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">توضیحات :</label>
                                <textarea class="form-control" row="2" placeholder="توضیحات" name="description"></textarea>
                            </div>
                        </div>

                        <div class="col-12 form-group">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="status" value="1"
                                    {{ old('status', 1) == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">وضعیت</span>
                            </label>
                        </div>

                    </div>

                    <div class="modal-footer justify-content-center mt-2">
                        <button class="btn btn-sm btn-primary" type="submit">ثبت و ذخیره</button>
                        <button class="btn btn-sm btn-outline-danger" data-dismiss="modal">انصراف</button>
                    </div>

                </form>
            </x-slot>
        </x-modal>
    @endcan

    @can('modify_gift_package')
        @foreach ($giftPackages ?? [] as $giftPackage)
            <x-modal id="editGiftPackageModal-{{ $giftPackage->id }}" size="md">
                <x-slot name="title">ویرایش بسته بندی هدیه جدید</x-slot>
                <x-slot name="body">
                    <form action="{{ route('admin.gift-packages.update', $giftPackage) }}" method="POST"
                        enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        <div class="row">

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">نام بسته : <span class="text-danger">&starf;</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ $giftPackage->name }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">قیمت (تومان) : <span
                                            class="text-danger">&starf;</span></label>
                                    <input type="text" class="form-control comma" name="price"
                                        value="{{ number_format($giftPackage->price) }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">تصویر : </label>
                                    <input type="file" class="form-control" name="image">
                                </div>
                            </div>


                            <div class="col-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">توضیحات :</label>
                                    <textarea class="form-control" row="2" placeholder="توضیحات" name="description">{{ $giftPackage->description }}</textarea>
                                </div>
                            </div>

                            <div class="col-12 form-group">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="status" value="1"
                                        {{ $giftPackage->status == 1 ? 'checked' : null }} />
                                    <span class="custom-control-label">وضعیت</span>
                                </label>
                            </div>

                        </div>

                        <div class="modal-footer justify-content-center mt-2">
                            <button class="btn btn-sm btn-warning" type="submit">بروزرسانی</button>
                            <button class="btn btn-sm btn-outline-danger" data-dismiss="modal">انصراف</button>
                        </div>

                    </form>
                </x-slot>
            </x-modal>
        @endforeach
    @endcan

    <div class="modal fade" id="showDescriptionModal" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <p class="modal-title" style="font-size: 20px;">توضیحات</p><button aria-label="Close" class="close"
                        data-dismiss="modal"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <p id="description"></p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function showDescriptionModal(description) {
            let modal = $('#showDescriptionModal');
            modal.find('#description').text(description ?? '-');
            modal.modal('show');
        }
    </script>
@endsection
