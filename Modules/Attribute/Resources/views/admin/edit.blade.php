@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        @php
            $items = [
                ['title' => 'لیست ویژگی ها', 'route_link' => 'admin.attributes.index'],
                ['title' => 'ویرایش ویژگی', 'route_link' => null],
            ];
        @endphp
        <x-breadcrumb :items="$items" />
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
                    <div class="col-6">
                        <div class="form-group">
                            <label for="name" class="control-label"> نام: <span
                                    class="text-danger">&starf;</span></label>
                            <input type="text" id="name" class="form-control" name="name"
                                value="{{ old('name', $attribute->name) }}" required autofocus />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">نام ویژگی را حتما به <span
                                    class="text-danger">انگیلیسی</span> وارد کنید!</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="label" class="control-label"> لیبل: <span
                                    class="text-danger">&starf;</span></label>
                            <input type="text" id="label" class="form-control" name="label"
                                value="{{ old('label', $attribute->label) }}" required />
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
                                <option value="select" @if ($attribute->style == 'select') selected @endif>کوبمو</option>
                                <option value="box" @if ($attribute->style == 'box') selected @endif>مربعی</option>
                                <option value="image" @if ($attribute->style == 'image') selected @endif>مربعی با عکس
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="type" class="control-label"> انتخاب نوع ویژگی: </label>
                            <select class="form-control" name="type" id="type">
                                @foreach ($types as $type)
                                    <option value="{{ $type }}"
                                        {{ old('type', $attribute->type) == $type ? 'selected' : null }}>
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
                                    {{ old('status', $attribute->status) == 1 ? 'checked' : null }} />
                                <span class="custom-control-label">وضعیت</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-6">
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
                            <button class="btn btn-warning" type="submit">ویرایش و ذخیره</button>
                            <a class="btn btn-outline-danger" href="{{ route('admin.attributes.index') }}">برگشت</a>
                        </div>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-card>
    <div id="examples">  
        <div id="examples-attribute-value-box" class="col-xl-3 d-flex attribute-value-bo my-1" style="gap: 5px;">  
            <button type="button" class="positive-btn btn btn-success btn-sm" onclick="makeAttrValueBox()">+</button>  
            <button type="button" class="negative-btn btn btn-danger btn-sm" onclick="removeAttrValueBox(event)">-</button>  
            <input type="text" name="" placeholder="مقدار" class="form-control form-control-sm">  
        </div>  
    </div>  
@endsection
@section('scripts')  
<script>  
    const exampleAttributeValueBox = $('#examples-attribute-value-box').clone().removeAttr('id');  
    const attributeTypeSelect = $('#type');  
    const attributeValuesSection = $('#attribute-values-section');  
    const attributeValuesRow = $('#attribute-values-row');  

    let removeExamplesFromDOM = () => $('#examples').remove();  
    let hideAttrValuesSection = () => attributeValuesSection.hide();  
    let showAttrValuesSection = () => attributeValuesSection.show();  
    let emptyTheAttrValuesRow = () => attributeValuesRow.empty();  

    const allAttributeTypesArray = @json($types);  
    const hasValueTypes = ['select']; // فقط نوع های با مقدار select  
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

        // اگر تعداد باکس‌ها فقط 1 باشد، حذف نکنید  
        if (totalBoxes > 1) {  
            box.remove();  
            index--;  
        } else {  
            alert("حداقل یک مقدار باید وجود داشته باشد."); // پیام هشدار  
        }  
    }  

    function checkForDisplayAttrValuesSection(e) {  
        let type = $(e.target).val();  
        if (type === 'select') {  
            showAttrValuesSection();  
            emptyTheAttrValuesRow();  

            // اگر مقادیر قبلاً وجود داشته باشند، باکس‌ها را پر کنید  
            if (attrValues.length > 0) {  
                attrValues.forEach(attrValue => {  
                    makeAttrValueBox(attrValue.value);  
                });  
            } else {  
                // اگر هیچ مقدار قبلی وجود ندارد، باکس جدید بسازید  
                makeAttrValueBox();  
            }  
        } else {  
            hideAttrValuesSection(); // مخفی کردن اگر نوع دیگری انتخاب شده باشد  
            emptyTheAttrValuesRow(); // خالی کردن ردیف مقادیر  
        }  
    }  

    $(document).ready(() => {  
        removeExamplesFromDOM();  
        emptyTheAttrValuesRow();  
        hideAttrValuesSection();  
        attributeTypeSelect.select2({  
            placeholder: 'انتخاب نوع مشخصه'  
        });  

        // بررسی اینکه آیا نوع انتخاب شده، دارای مقادیر است  
        if (attributeTypeSelect.val() && hasValueTypes.includes(attributeTypeSelect.val())) {  
            showAttrValuesSection();  
            if (attrValues.length > 0) {  
                // اگر مقادیر وجود دارند، نمایش آن‌ها  
                attrValues.forEach(attrValue => {  
                    makeAttrValueBox(attrValue.value);  
                });  
            } else {  
                // اگر هیچ مقدار قبلی وجود ندارد، باکس جدید بسازید  
                makeAttrValueBox();  
            }  
        }  

        attributeTypeSelect.on('change', checkForDisplayAttrValuesSection);  
    });  
</script>  
@endsection