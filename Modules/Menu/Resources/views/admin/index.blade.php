@extends('admin.layouts.master')

@section('content')
    <div class="page-header">
        <x-breadcrumb :items="[['title' => 'لیست منو ها']]" />
            <div>
                <button id="submitButton" type="submit" class="btn btn-teal btn-sm align-items-center"><span>ذخیره مرتب سازی</span></button>
                <x-create-button type="modal" target="createMenuModal" title="منو جدید" />
                @if ($parentMenu)
                    <a href="{{route('admin.menu.index',$parentMenu->group_id)}}" class="btn btn-warning btn-sm">برگشت</a>
                @endif
            </div>
    </div>

    <x-card>
        @if ($parentMenu)
        <x-slot name="cardTitle">لیست منو های ({{$parentMenu->title}})</x-slot>
        @else
        <x-slot name="cardTitle">لیست منو ها</x-slot>
        @endif
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            @include('components.errors')
            <form id="myForm" action="{{ route('admin.menu.sort') }}" method="POST">
                @csrf
                @method('PATCH')
                <x-table-component idTbody="items">
                    <x-slot name="tableTh">
                        <tr>
                            @php($tableTh = ['انتخاب','عنوان', 'تعداد فرزند ها','صفحه جدید', 'وضعیت', 'تاریخ ثبت', 'عملیات'])
                            @foreach ($tableTh as $th)
                                <th>{{ $th }}</th>
                            @endforeach
                        </tr>
                    </x-slot>
                    <x-slot name="tableTd">
                        @forelse($menu_items as $menu_item)
                            @php($group = $menu_item->group_id)
                            <tr>
                                <input type="hidden" value="{{ $menu_item->group }}" name="group">
                                <td class="text-center"><i class="fe fe-move glyphicon-move text-dark"></i></td>
                                <input type="hidden" value="{{ $menu_item->id }}" name="orders[]">
                                @php($count = count($menu_item->children))
                                <td class="">
                                    @if ($count == 0)
                                        {{ $menu_item->title }}
                                    @else
                                        <a class="text-info" data-original-title="مشاهده"
                                            href="{{ route('admin.menu.index', [$group, $menu_item->id]) }}">{{ $menu_item->title }}</a>
                                    @endif
                                </td>
                                <td>
                                    @if ($count == 0)
                                        {{ $count }}
                                    @else
                                        <a class="text-info" data-original-title="مشاهده"
                                            href="{{ route('admin.menu.index', [$group, $menu_item->id]) }}">{{ $count }}</a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($menu_item->new_tab)
                                        <span class=""><i
                                                class="text-success fs-26 fa fa-check-circle-o"></i></span>
                                    @else
                                        <span class=""><i class="text-danger fs-26 fa fa-close"></i></span>
                                    @endif
                                </td>
                                <td>
                                    @include('core::includes.status', [
                                        'status' => $menu_item->status,
                                    ])
                                </td>
                                <td>{{ verta($menu_item->created_at)->format('Y/m/d H:i') }}</td>
                                <td>
                                    @include('core::includes.edit-modal-button', [
                                        'target' => '#edit-menu-' . $menu_item->id,
                                    ])
                                    <button onclick="confirmDelete('delete-{{ $menu_item->id }}')"
                                        class="btn btn-sm btn-icon btn-danger text-white" data-toggle="tooltip"
                                        type="button" data-original-title="حذف"
                                        {{ isset($disabled) ? 'disabled' : null }}>
                                        {{ isset($title) ? $title : null }}
                                        <i class="fa fa-trash-o {{ isset($title) ? 'mr-1' : null }}"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            @include('core::includes.data-not-found-alert', ['colspan' => 7])
                        @endforelse
                    </x-slot>
                </x-table-component>
                <button class="btn btn-teal mt-5" type="submit">ذخیره مرتب سازی</button>
            </form>
        </x-slot>
    </x-card>
    @foreach ($menu_items as $menu_item)
        <form action="{{ route('admin.menu.destroy', $menu_item->id) }}" method="POST" id="delete-{{ $menu_item->id }}"
            style="display: none">

            @csrf
            @method('DELETE')
        </form>
    @endforeach
    @include('menu::admin.create')
    @include('menu::admin.edit')
    <!-- row closed -->
@endsection
@section('scripts')
    <script>
        var items = document.getElementById('items');
        var sortable = Sortable.create(items, {
            handle: '.glyphicon-move',
            animation: 150
        });
        var items = document.getElementById('items');
        var sortable = Sortable.create(items, {
            handle: '.glyphicon-move',
            animation: 150
        });
        document.getElementById('submitButton').addEventListener('click', function() {
            document.getElementById('myForm').submit();
        });
        function toggleInput() {
            let selectOption = document.getElementById('linkableType');
            let linkInput = document.getElementById('link');
            $('#divLinkable').hide();
            // Reset linkableId
            linkInput.value = '';
            linkInput.disabled = true;

            if (selectOption.value === "self_link") {
                linkInput.disabled = false;
            } else if (selectOption.value === 'none') {
                linkInput.disabled = true;
            } else {
                $('#divLinkable').show();
                let linkables = @json($linkables);
                let linkableId = document.getElementById('linkableId');
                linkableId.innerHTML = '';

                let findedItem = linkables.find(linkable => {
                    return linkable.unique_type === document.getElementById('linkableType').value;
                });

                if (findedItem) {
                    let option = '';
                    if (findedItem.models !== null) {
                        findedItem.models.forEach(model => {
                            let title = model.title ?? (model.name ?? 'ندارد');
                            option += `<option value="${model.id}">${title}</option>`;
                        });
                        linkableId.innerHTML = option;
                    } else {
                        linkableId.innerHTML = `<option value="" selected disabled>آیتمی وجود ندارد</option>`;
                    }
                }
            }
        }

        function toggleEditInput(editItem, id) {
            let selectOption2 = $(`#typeLink-${id}`).val();
            let linkInput = $(`#linkEdit-${id}`);
            $(`#divLinkableEditId-${id}`).hide();
            linkInput.value = '';
            linkInput.disabled = true;
            if (selectOption2 == "self_link2") {
                linkInput.removeAttr('disabled');
            } else {

                linkInput.attr("disabled", "disabled");
                $(`#divLinkableEditId-${id}`).show();
                let linkables = @json($linkables);
                let linkableId = document.getElementById(`linkableEditId-${id}`);
                linkableId.innerHTML = '';

                let findedItem = linkables.find(linkable => {

                    return linkable.unique_type == $(`#typeLink-${id}`).val();
                });

                if (findedItem) {
                    let option = '';
                    if (findedItem.models != null) {
                        findedItem.models.forEach(model => {
                            let title = model.title ?? (model.name ?? 'ندارد');
                            option += `<option value="${model.id}">${title}</option>`;
                        });
                        linkableId.innerHTML = option;
                    } else {
                        linkableId.innerHTML = `<option value="" selected disabled>آیتمی وجود ندارد</option>`;
                    }
                }
            }
        }
    </script>
@endsection
