@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        @php
            $items = [
                ['title' => 'لیست ویژگی ها', 'route_link' => 'admin.attributes.index'],
                ['title' => 'ثبت ویژگی جدید', 'route_link' => null],
            ];
        @endphp
        <x-breadcrumb :items="$items" />
    </div>

    <x-card>
        <x-slot name="cardTitle">ثبت ویژگی جدید</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <x-alert-danger />
            <form action="{{ route('admin.attributes.store') }}" method="POST" class="save" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="name" class="control-label"> نام: <span
                                    class="text-danger">&starf;</span></label>
                            <input type="text" id="name" class="form-control" name="name"
                                value="{{ old('name') }}" required autofocus />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">نام ویژگی را حتما به <span
                                    class="text-danger">انگیلیسی</span> وارد کنید!</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="label" class="control-label"> لیبل: <span
                                    class="text-danger">&starf;</span></label>
                            <input type="text" id="label" class="form-control" name="label"
                                value="{{ old('label') }}" required />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">لیبل ویژگی را حتما به <span
                                    class="text-danger">فارسی</span> وارد کنید!</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="type" class="control-label">نحوه نمایش: <span
                                    class="text-danger">&starf;</span></label>
                            <select name="style" class="form-control" required>
                                <option value="select">کوبمو</option>
                                <option value="box">مربعی</option>
                                <option value="image">مربعی با عکس</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="type" class="control-label"> نوع : </label>
                            <select class="form-control" name="type" id="type">
                                <option value="">انتخاب کنید</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : null }}>
                                        {{ config('attribute.types.' . $type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="status" value="1"
                                    {{ old('status', 1) == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">وضعیت</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="show_filter" value="1"
                                    {{ old('show_filter') == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">نمایش در فیلتر</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row my-2" id="specification-values-section" style="display: none">
                    <div class="col-12">
                        <p class="header pr-2 font-weight-bold fs-22">مقادیر ویژگی</p>
                    </div>
                    <div class="col-12" id="specification-values-group">
                        <div class="row" id="specification-values-group-row">
                            <div class="col-4 d-flex plus-negative-container mt-2">
                                <button id="positive-btn-0" type="button"
                                    class="positive-btn btn btn-success ml-1">+</button>
                                <button id="negative-btn-0" type="button" class="negative-btn btn btn-danger ml-1"
                                    disabled>-</button>
                                <input id="value-0" name="values[0]" type="text" placeholder="مقدار"
                                    class="form-control mx-1">
                            </div>  
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <div class="text-center">
                            <button class="btn btn-primary" type="submit">ثبت و ذخیره</button>
                        </div>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-card>
@endsection
@section('scripts')
    <script>
        $(document).ready(() => {

            let counter = 1;
            let specificationValuesGroupRow = $('#specification-values-group-row');
            let specificationValuesSection = $('#specification-values-section');
            let firstNegativeButton = $('#negative-btn-0');

            let hasValue = {
                text: false,
                select: true,
            };

            $('#type').on('change', () => {
                let type = $('#type').val();
                if (hasValue[type]) {
                    $('#specification-values-section').css('display', 'flex');
                } else {
                    $('#specification-values-section').css('display', 'none');
                }
            });

            specificationValuesGroupRow.on('click', '.positive-btn', (event) => {

                let newPositiveBtn = $(event.currentTarget)
                    .clone()
                    .attr('id', `positive-btn-${counter}`)
                    .text('+');

                let newNegativeBtn = firstNegativeButton
                    .clone()
                    .attr('id', `negative-btn-${counter}`)
                    .removeAttr('disabled')
                    .text('-');

                let newInput = $(
                    `<input
          id="value-${counter}"
          name="values[${counter}]"
          type="text"
          placeholder="مقدار"
          class="form-control mx-1"
        />`
                );

                let newGroup = $('<div class="col-4 d-flex plus-negative-container mt-2"></div>');

                newGroup
                    .append(newPositiveBtn)
                    .append(newNegativeBtn)
                    .append(newInput);

                specificationValuesGroupRow.append(newGroup);

                counter++;

            });

            specificationValuesGroupRow.on('click', '.negative-btn', (event) => {
                $(event.currentTarget).closest('.plus-negative-container').remove();
                counter--;
            });

        });
    </script>
@endsection
