@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        <x-breadcrumb :items="[['title' => 'لیست اسلایدر ها']]" />
        <div>
            <button id="submitButton" type="submit" class="btn btn-teal btn-sm align-items-center btn-sm">ذخیره مرتب سازی</button>
            <x-create-button type="modal" target="createSliderModal" title="اسلایدر جدید"/>
        </div>
    </div>

    <x-card>
        <x-slot name="cardTitle">لیست همه اسلایدر ها ({{ count($sliders) }})</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            @include('components.errors')
            <form id="myForm" action="{{ route('admin.sliders.sort', 1) }}" method="POST">
                @csrf
                @method('PATCH')
                <x-table-component idTbody="items">
                    <x-slot name="tableTh">
                        <tr>
                            @php($tableTh = ['انتخاب','ردیف', 'عنوان', 'تصویر', 'وضعیت', 'تاریخ ثبت', 'عملیات'])
                            @foreach ($tableTh as $th)
                                <th>{{ $th }}</th>
                            @endforeach
                        </tr>
                    </x-slot>
                    <x-slot name="tableTd">
                        @forelse($sliders as $slider)
                            <tr>
                                <td><i class="fe fe-move glyphicon-move text-dark"></i></td>
                                <td class="font-weight-bold">{{ $loop->iteration }}</td>
                                <td>{{ $slider->title }}</td>
                                <input type="hidden" value="{{ $slider->id }}" name="orders[]">
                                <input type="hidden" value="{{ $group }}" name="group">
                                <td>
                                    @if ($slider->image->url)
                                        <a href="{{ $slider->image->url }}" target="_blank">
                                            <div class="bg-light pb-1 pt-1 img-holder img-show w-100" style="max-height: 60px; border-radius: 4px;">
                                                <img src="{{ $slider->image->url }}" style="height: 50px;" alt="{{ $slider->image->url }}">
                                            </div>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>@include('core::includes.status', ['status' => $slider->status])</td>
                                <td>{{ verta($slider->created_at)->format('Y/m/d H:i') }}</td>
                                <td>
                                    {{-- Edit --}}
                                    @include('core::includes.edit-modal-button', [
                                        'target' => '#edit-slider-' . $slider->id,
                                    ])
                                    <button onclick="confirmDelete('delete-{{ $slider->id }}')"
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
                <button class="btn btn-sm btn-teal mt-5" type="submit">ذخیره مرتب سازی</button>
            </form>
        </x-slot>
    </x-card>
    @foreach ($sliders as $slider)
        <form action="{{ route('admin.sliders.destroy', $slider->id) }}" method="POST" id="delete-{{ $slider->id }}"
            style="display: none">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
    @include('slider::admin.create')
    @include('slider::admin.edit')
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


        var linkables = @json($linkables);

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

        function toggleEditInput(slider, id) {
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
