@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        <x-breadcrumb :items="[
            ['title' => 'لیست ویژگی ها', 'route_link' => 'admin.attributes.index'],
            ['title' => 'ثبت ویژگی جدید', 'route_link' => null],
        ]" />
    </div>

    <x-card>
        <x-slot name="cardTitle">ثبت ویژگی جدید</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <x-alert-danger />
            <form action="{{ route('admin.attributes.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="form-group">
                            <label for="name" class="control-label"> نام : <span class="text-danger">&starf;</span></label>
                            <input type="text" id="name" class="form-control" name="name" value="{{ old('name') }}" required autofocus />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">نام ویژگی را حتما به <span class="text-danger">انگیلیسی</span> وارد کنید!</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="form-group">
                            <label for="label" class="control-label"> لیبل : <span class="text-danger">&starf;</span></label>
                            <input type="text" id="label" class="form-control" name="label" value="{{ old('label') }}" required />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">لیبل ویژگی را حتما به <span class="text-danger">فارسی</span> وارد کنید!</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="form-group">
                            <label for="attribute-style-selectBox" class="control-label">نحوه نمایش : <span class="text-danger">&starf;</span></label>
                            <select id="attribute-style-selectBox" name="style" class="form-control" required>
                                <option value=""></option>
                                @foreach ($styles as $style)
                                    <option @if(old('style') === $style) selected @endif value="{{ $style }}">
                                        {{ config('attribute.translates.styles.' . $style) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="form-group">
                            <label for="attribute-type-selectBox" class="control-label"> نوع : <span class="text-danger">&starf;</span></label>
                            <select class="form-control" name="type" id="attribute-type-selectBox" required>
                                <option value=""></option>
                                @foreach ($types as $type)
                                    <option @if(old('type') === $type) selected @endif value="{{ $type }}">
                                        {{ config('attribute.translates.types.' . $type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="status" value="1"
                                    {{ old('status', 1) == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">وضعیت</span>
                            </label>
                        </div>
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
                    <div class="col d-flex justify-content-center align-items-center">
                        <div class="text-center d-flex" style="gap: 8px;">
                            <button class="btn btn-sm btn-primary" type="submit">ثبت و ذخیره</button>
                            <a href="{{ route('admin.attributes.create') }}" class="btn btn-sm btn-danger">ریست فرم</a>
                        </div>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-card>
@endsection
@section('scripts')
    <script>
        $('#attribute-style-selectBox').select2({ placeholder: 'انتخاب نحوه نمایش' });
        $('#attribute-type-selectBox').select2({ placeholder: 'انتخاب نحوه مقدار دهی' });
        $(document).ready(() => {

            let counter = 1;
            let specificationValuesGroupRow = $('#specification-values-group-row');
            let specificationValuesSection = $('#specification-values-section');
            let firstNegativeButton = $('#negative-btn-0');

            let hasValue = {
                text: false,
                select: true,
            };

            $('#attribute-type-selectBox').on('change', () => {
                let type = $('#attribute-type-selectBox').val();
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
