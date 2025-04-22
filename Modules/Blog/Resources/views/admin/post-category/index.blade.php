@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        <x-breadcrumb :items="[['title' => 'لیست دسته بندی های مطالب']]" />
        @can('write_post-category')
            <x-create-button type="modal" target="createPostCategoryModal" title="دسته بندی جدید" />
        @endcan
    </div>

    <x-card>
        <x-slot name="cardTitle">دسته بندی های مطالب ({{ number_format($postCategories->count()) }})</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            @include('components.errors')
            <x-table-component>
                <x-slot name="tableTh">
                    <tr>
                        @php($tableTh = ['ردیف', 'نام', 'تعداد مطالب', 'وضعیت', 'تاریخ ثبت', 'عملیات'])
                        @foreach ($tableTh as $th)
                            <th>{{ $th }}</th>
                        @endforeach
                    </tr>
                </x-slot>
                <x-slot name="tableTd">
                    @forelse ($postCategories as $postCategory)
                        <tr>
                            <td class="font-weight-bold">{{ $loop->iteration }}</td>
                            <td>{{ $postCategory->name }}</td>
                            <td>{{ $postCategory->posts_count }}</td>
                            <td>@include('core::includes.status', ['status' => $postCategory->status])</td>
                            <td>{{ verta($postCategory->created_at)->format('Y/m/d H:i:s') }}</td>
                            <td>

                                @can('modify_post-category')
                                    @include('core::includes.edit-modal-button', [
                                        'target' => '#editPostCategoryModal-' . $postCategory->id,
                                    ])
                                @endcan

                                @can('delete_post-category')
                                    @include('core::includes.delete-icon-button', [
                                        'model' => $postCategory,
                                        'route' => 'admin.post-categories.destroy',
                                        'disabled' => !$postCategory->is_deletable,
                                    ])
                                @endcan

                            </td>
                        </tr>
                    @empty

                        @include('core::includes.data-not-found-alert', ['colspan' => 8])
                    @endforelse
                </x-slot>
            </x-table-component>
        </x-slot>
    </x-card>

    @can('write_post-category')
        @include('blog::admin.post-category.includes.create-modal')
    @endcan

    @can('modify_post-category')
        @include('blog::admin.post-category.includes.edit-modal')
    @endcan
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
