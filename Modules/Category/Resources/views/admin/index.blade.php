@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        <x-breadcrumb :items="[['title' => 'لیست دسته بندی ها']]" />
        <div>
            <button id="submitButton" type="submit" class="btn btn-teal btn-sm">ذخیره مرتب سازی</button>
            @can('write_category')
                @if (isset($categories[0]->parent_id))
                    <x-create-button   
                        route="admin.categories.create"   
                        title="دسته بندی جدید"   
                        :routeParams="['parent_id' => $categories[0]->parent_id]"   
                    />  
                <a href="{{route('admin.categories.index')}}" class="btn btn-warning btn-sm">برگشت</a>
                @else
                    <x-create-button   
                        route="admin.categories.create"   
                        title="دسته بندی جدید"   
                    />  
                @endif
            @endcan
        </div>
    </div>

    <x-card>
        @if (isset($parentCategory))
            <x-slot name="cardTitle">لیست دسته بندی های ({{ $parentCategory->title }})</x-slot>
        @else
            <x-slot name="cardTitle">لیست دسته بندی ها</x-slot>
        @endif
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            @include('components.errors')
            <form id="myForm" action="{{ route('admin.categories.sort') }}" method="POST">
                @csrf
                <x-table-component idTbody="items">
                    <x-slot name="tableTh">
                        <tr>
                            @php($tableTh = ['انتخاب','عنوان', 'تعداد', 'وضعیت', 'تاریخ ثبت', 'عملیات'])
                            @foreach ($tableTh as $th)
                                <th>{{ $th }}</th>
                            @endforeach
                        </tr>
                    </x-slot>
                    <x-slot name="tableTd">
                        @forelse($categories as $category)
                            <tr>
                                <td class="text-center"><i class="fe fe-move glyphicon-move text-dark"></i></td>
                                <input type="hidden" value="{{ $category->id }}" name="categories[]">
                                @php($count = count($category->children))
                                <td class="">
                                    @if ($count == 0)
                                        {{ $category->title }}
                                    @else
                                        <a class="text-info" data-original-title="مشاهده"
                                            href="{{ route('admin.categories.index', $category) }}">{{ $category->title }}</a>
                                    @endif
                                </td>
                                <td>
                                    @if ($count == 0)
                                        {{ $count }}
                                    @else
                                        <a class="text-info" data-original-title="مشاهده"
                                            href="{{ route('admin.categories.index', $category) }}">{{ $count }}</a>
                                    @endif
                                </td>
                                <td>@include('core::includes.status', ['status' => $category->status])</td>
                                <td>{{ verta($category->created_at)->format('Y/m/d H:i') }}</td>
                                <td>
                                    @include('core::includes.edit-icon-button', [
                                        'model' => $category,
                                        'route' => 'admin.categories.edit',
                                    ])
                                    <button onclick="confirmDelete('delete-{{ $category->id }}')"
                                        class="btn btn-sm btn-icon btn-danger text-white" data-toggle="tooltip"
                                        type="button" data-original-title="حذف">
                                        <i class="fa fa-trash-o {{ isset($title) ? 'mr-1' : null }}"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            @include('core::includes.data-not-found-alert', ['colspan' => 6])
                        @endforelse
                    </x-slot>
                </x-table-component>
                <button class="btn btn-teal btn-sm mt-5" type="submit">ذخیره مرتب سازی</button>
            </form>
        </x-slot>
    </x-card>
    @foreach ($categories as $category)
        <form
            action="{{ route('admin.categories.destroy', $category->id) }}"
            method="POST"
            id="delete-{{ $category->id }}"
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
{{-- @section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tippy.js@6/dist/tippy-bundle.umd.js"></script>
    <script src="{{ asset('assets/js/treeSorable/treeSortable.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/treeSorable/script.js') }}"></script> --}}
{{-- <script>
        $(document).ready(function() {
            const usersTreeData = @json($categoriesTreeData);
            console.log(usersTreeData);

            const leftTreeId = '#left-tree';
            const leftSortable = new TreeSortable({
                treeSelector: leftTreeId,
            });
            const $leftTree = $(leftTreeId);
            const $content = usersTreeData.map(leftSortable.createBranch);
            $leftTree.html($content);
            leftSortable.run();

            // const delay = () => {
            //     return new Promise(resolve => {
            //         setTimeout(() => {
            //             resolve();
            //         }, 1000);
            //     });
            // };
            $leftTree.on("sortupdate", function(event, ui) {
                var sortedIDs = $leftTree.sortable( "toArray",{attribute :'id'} );
                console.log(sortedIDs);


            });

            leftSortable.onSortCompleted(async (event, ui) => {
                // await delay();
                // console.log('ui.item', event)
                // console.log('helper', ui.helper)

                let token = $('meta[name="csrf-token"]').attr('content');

                // const getAllOrders = () => {
                //     const orders = [];
                //     $leftTree.find('.branch').each(function(index) {
                //         const id = $(this).data('id');
                //         const depth = $(this).parentsUntil($leftTree, '.branch').length;
                //         orders.push({ id, order: index, depth });
                //     });
                //     return orders;
                // };

                // console.log(getAllOrders());
                // $.ajax({
                //     url: '{{ route('admin.categories.sort') }}',
                //     type: 'POST',
                //     headers: { 'X-CSRF-TOKEN': token },
                //     data: { categories: getAllOrders() },
                //     success: function(response) {
                //         console.log('Order updated successfully');
                //     },
                //     error: function(xhr, status, error) {
                //         console.error('Error updating order:', error);
                //     }
                // });


            });

            leftSortable.addListener('click', '.add-child', function(event, instance) {
                event.preventDefault();
                instance.addChildBranch($(event.target));
            });

            leftSortable.addListener('click', '.add-sibling', function(event, instance) {
                event.preventDefault();
                instance.addSiblingBranch($(event.target));
            });

            leftSortable.addListener('click', '.remove-branch', function(event, instance) {
                event.preventDefault();

                const confirm = window.confirm('Are you sure you want to delete this branch?');
                if (!confirm) {
                    return;
                }
                instance.removeBranch($(event.target));
            });



            tippy('[data-tippy-content]');
        });
    </script>
@endsection --}}
