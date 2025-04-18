@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        <x-breadcrumb :items="[
            ['title' => 'لیست ویژگی ها', 'route_link' => 'admin.attributes.index'],
            ['title' => 'ویرایش ویژگی', 'route_link' => null],
        ]" />
    </div>

    <x-card>
        <x-slot name="cardTitle">ویرایش ویژگی - کد {{ $attribute->id }}</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <x-alert-danger />
            <form action="{{ route('admin.attributes.update', $attribute) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="form-group">
                            <label for="name" class="control-label"> نام : <span class="text-danger">&starf;</span></label>
                            <input type="text" id="name" class="form-control" name="name" value="{{ old('name', $attribute->name) }}" required autofocus />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">نام ویژگی را حتما به <span class="text-danger">انگیلیسی</span> وارد کنید!</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="form-group">
                            <label for="label" class="control-label"> لیبل : <span class="text-danger">&starf;</span></label>
                            <input type="text" id="label" class="form-control" name="label" value="{{ old('label', $attribute->label) }}" required />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">لیبل ویژگی را حتما به <span class="text-danger">فارسی</span> وارد کنید!</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="form-group">
                            <label for="type" class="control-label">نحوه نمایش : <span class="text-danger">&starf;</span></label>
                            <select id="attribute-style-selectBox" name="style" class="form-control" required>
                                <option value=""></option>
                                @foreach ($styles as $style)
                                    <option @if(old('style', $attribute->style) === $style) selected @endif value="{{ $style }}">
                                        {{ config('attribute.translates.styles.' . $style) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="form-group">
                            <label for="type" class="control-label"> نوع : <span class="text-danger">&starf;</span></label>
                            <select class="form-control" name="type" id="type" required>
                                @foreach ($types as $type)
                                    <option @if(old('type', $attribute->type) === $type) selected @endif value="{{ $type }}">
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
                                    {{ old('status', $attribute->status) == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">وضعیت</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="show_filter" value="1"
                                    {{ old('show_filter', $attribute->show_filter) == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">نمایش در فیلتر</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row my-2" id="attribute-values-section">
                    <div class="col-12">
                        <p class="header pr-2 font-weight-bold fs-22">مقادیر ویژگی</p>
                    </div>
                    <div class="col-12">
                        <div class="row" id="attribute-values-row"></div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <div class="d-flex justify-content-center align-items-center" style="gap: 10px;">
                            <button class="btn btn-sm btn-warning" type="submit">بروزرسانی</button>
                            <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-sm btn-danger"">ریست فرم</a>
                        </div>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-card>
    <div id="examples">  
        <div id="examples-attribute-value-box" class="col-xl-3 d-flex attribute-value-box my-1" style="gap: 5px;">  
            <button type="button" class="positive-btn btn btn-success btn-sm" onclick="makeAttrValueBox()">+</button>  
            <button type="button" class="negative-btn btn btn-danger btn-sm" onclick="removeAttrValueBox(event)">-</button>  
            <input type="text" name="" placeholder="مقدار" class="form-control form-control-sm">  
        </div>  
    </div>  
@endsection
@section('scripts')  
<script>  

    $('#attribute-style-selectBox').select2({ placeholder: 'انتخاب نحوه نمایش' });
    $('#type').select2({ placeholder: 'انتخاب نحوه مقدار دهی' });

    const exampleAttributeValueBox = $('#examples-attribute-value-box').clone().removeAttr('id');  
    const attributeTypeSelect = $('#type');  
    const attributeValuesSection = $('#attribute-values-section');  
    const attributeValuesRow = $('#attribute-values-row');  

    let removeExamplesFromDOM = () => $('#examples').remove();  
    let hideAttrValuesSection = () => attributeValuesSection.hide();  
    let showAttrValuesSection = () => attributeValuesSection.show();  
    let emptyTheAttrValuesRow = () => attributeValuesRow.empty();  

    const allAttributeTypesArray = @json($types);  
    const hasValueTypes = ['select']; 
    let index = 0;  
    const attrValues = @json($attribute->values ?? []);  

    function makeAttrValueBox(value = null) {  
        let attrValueBox = exampleAttributeValueBox.clone();  
        attrValueBox.find('input').attr('name', `values[${index++}]`);  
        if (value !== null) attrValueBox.find('input').val(value);  
        attributeValuesRow.append(attrValueBox);  
    }  

    function removeAttrValueBox(event) {  
        const box = $(event.target).closest('.attribute-value-box');  
        const totalBoxes = $(".attribute-value-box").length;  

        if (totalBoxes > 1) {  
            box.remove();  
            index--;  
        } else {  
            alert("حداقل یک مقدار باید وجود داشته باشد."); 
        }  
    }  

    function checkForDisplayAttrValuesSection(e) {  
        let type = $(e.target).val();  
        if (type === 'select') {  
            showAttrValuesSection();  
            emptyTheAttrValuesRow();  

            if (attrValues.length > 0) {  
                attrValues.forEach(attrValue => {  
                    makeAttrValueBox(attrValue.value);  
                });  
            } else {  
                makeAttrValueBox();  
            }  
        } else {  
            hideAttrValuesSection(); 
            emptyTheAttrValuesRow(); 
        }  
    }  

    $(document).ready(() => {  
        removeExamplesFromDOM();  
        emptyTheAttrValuesRow();  
        hideAttrValuesSection();  

        if (attributeTypeSelect.val() && hasValueTypes.includes(attributeTypeSelect.val())) {  
            showAttrValuesSection();  
            if (attrValues.length > 0) {  
                attrValues.forEach(attrValue => {  
                    makeAttrValueBox(attrValue.value);  
                });  
            } else {  
                makeAttrValueBox();  
            }  
        }  

        attributeTypeSelect.on('change', checkForDisplayAttrValuesSection);  
    });  
</script>  
@endsection