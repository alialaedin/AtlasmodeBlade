@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        @php
            $items = [
                ['title' => 'لیست مشخصات', 'route_link' => 'admin.specifications.index'],
                ['title' => 'ثبت مشخصه جدید', 'route_link' => null],
            ];
        @endphp
        <x-breadcrumb :items="$items" />
    </div>

    <x-card>
        <x-slot name="cardTitle">ثبت مشخصه جدید</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <x-alert-danger />
            <form action="{{ route('admin.specifications.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="name" class="control-label"> نام: <span
                                    class="text-danger">&starf;</span></label>
                            <input type="text" id="name" class="form-control" name="name"
                                value="{{ old('name') }}" required autofocus />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">نام مشخصه را حتما به <span
                                    class="text-danger">انگیلیسی</span> وارد کنید!</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="label" class="control-label"> لیبل: <span
                                    class="text-danger">&starf;</span></label>
                            <input type="text" id="label" class="form-control" name="label"
                                value="{{ old('label') }}" required />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">لیبل مشخصه را حتما به <span
                                    class="text-danger">فارسی</span> وارد کنید!</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="type" class="control-label"> انتخاب نوع مشخصه: </label>
                            <select class="form-control" name="type" id="type" onchange="checkForDisplaySpecValuesSection(event)">
                                <option value="">انتخاب کنید</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : null }}>
                                        {{ config('specification.types.' . $type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="group" class="control-label">گروه :</label>
                            <input type="text" id="group" class="form-control" name="group"
                                value="{{ old('group') }}" />
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group d-flex">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="status" value="1"
                                    {{ old('status', 1) == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">وضعیت</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group d-flex">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="show_filter" value="1"
                                    {{ old('show_filter') == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">نمایش در فیلتر</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group d-flex">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="required" value="1"
                                    {{ old('required') == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">الزامی</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group d-flex">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="public" value="1"
                                    {{ old('public') == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">عمومی</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row my-2" id="specification-values-section">
                    <div class="col-12">
                        <p class="header pr-2 font-weight-bold fs-22">مقادیر مشخصه</p>
                    </div>
                    <div class="col-12">
                        <div class="row" id="specification-values-row"></div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <div class="text-center">
                            <button class="btn btn-primary" type="submit">ثبت و ذخیره</button>
                        </div>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-card>


    <div id="examples">
        <div id="examples-specification-value-box" class="col-xl-3 d-flex specification-value-box" style="gap: 5px;">
            <button type="button" class="positive-btn btn btn-success btn-sm" onclick="addSpecValueBox()">+</button>
            <button type="button" class="negative-btn btn btn-danger btn-sm"  onclick="removeSpecValueBox(event)">-</button>
            <input type="text" name="" placeholder="مقدار" class="form-control form-control-sm">
        </div>
    </div>

@endsection

@section('scripts')


    <script>

        let exampleSpecificationValueBox = $('#examples-specification-value-box').clone().removeAttr('id');
        let specificationTypeSelect = $('#type');
        let specificationValuesSection = $('#specification-values-section');
        let specificationValuesRow = $('#specification-values-row');
        
        let removeExamplesFromDOM = () => $('#examples').remove();
        let hideSpecValuesSection = () => specificationValuesSection.hide();
        let showSpecValuesSection = () => specificationValuesSection.show();
        let emptyTheSpecValuesRow = () => specificationValuesRow.empty();

        const allSpecificationTypesArray = @json($types);
        const hasValueTypes = ['select', 'multi_select'];
        let index = 0;

        specificationTypeSelect.select2({
            placeholder: 'انتخاب نوع مشخصه'
        });

        function addFirstSpecValueBox() {
            if (specificationValuesRow.find('.specification-value-box').length < 1) {
                let specValueBox = exampleSpecificationValueBox.clone();
                specValueBox.find('input').attr('name', `values[${index++}]`);
                specificationValuesRow.append(specValueBox);
            }
        }

        function addSpecValueBox() {
            let specValueBox = exampleSpecificationValueBox.clone();
            specValueBox.find('input').attr('name', `values[${index++}]`);
            specificationValuesRow.append(specValueBox);
        }

        function removeSpecValueBox(e) {
            if (specificationValuesRow.find('.specification-value-box').length > 1) {
                $(e.target).closest('.specification-value-box').remove();
            }
        }

        function checkForDisplaySpecValuesSection(e) {
            let type = $(e.target).val();
            if (allSpecificationTypesArray.includes(type) && hasValueTypes.includes(type)) {
                showSpecValuesSection();
                addFirstSpecValueBox();
            }else {
                hideSpecValuesSection();
                emptyTheSpecValuesRow()
            }
        }

        $(document).ready(() => {

            removeExamplesFromDOM();
            emptyTheSpecValuesRow();
            hideSpecValuesSection(); 

        });

    </script>

@endsection
