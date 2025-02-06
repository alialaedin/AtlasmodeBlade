@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        @php
            $items = [
                ['title' => 'لیست حمل و نقل ها', 'route_link' => 'admin.shippings.index'],
                ['title' => 'ویرایش حمل و نقل'],
            ];
        @endphp
        <x-breadcrumb :items="$items" />
    </div>
    <x-card>
        <x-slot name="cardTitle">ویرایش حمل و نقل - کد {{ $shipping->id }}</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            @include('components.errors')
            <form id="update-form" action="{{ route('admin.shippings.update', $shipping) }}" method="POST" enctype="multipart/form-data">

                @csrf
                @method('PUT')

                @php
                    $defaultPrice = $shipping->default_price ? number_format($shipping->default_price) : null;
                    $freeThreshold = $shipping->free_threshold ? number_format($shipping->free_threshold) : null;
                @endphp

                <div class="row">
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="name" class="control-label"> نام: <span
                                    class="text-danger">&starf;</span></label>
                            <input type="text" id="name" class="form-control" name="name"
                                value="{{ old('name', $shipping->name) }}" required autofocus />
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="default_price" class="control-label"> مبلغ پیش فرض (تومان):</label>
                            <input type="text" id="default_price" class="form-control comma" name="default_price"
                                value="{{ old('default_price', $defaultPrice) }}" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="free_threshold" class="control-label"> حد ارسال رایگان (تومان): </label>
                            <input type="text" id="free_threshold" class="form-control comma" name="free_threshold"
                                value="{{ old('free_threshold', $freeThreshold) }}" />
                        </div>
                    </div>



                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="packet_size" class="control-label"> سایز هر بسته : </label>
                            <input type="number" id="packet_size" class="form-control" name="packet_size"
                                   placeholder="سایز هر بسته وارد کنید" value="{{ old('packet_size', $shipping->packet_size) }}" min="1" />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">اگر خالی باشد به صورت پیش فرض <span class="text-danger">1</span> قرار می گیرد!</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="first_packet_size" class="control-label"> سایز اولین بسته :</label>
                            <input type="number" id="first_packet_size" class="form-control" name="first_packet_size"
                                   placeholder="سایز اولین بسته را وارد کنید" value="{{ old('first_packet_size', $shipping->first_packet_size) }}"
                            />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">اگر خالی باشد به صورت پیش فرض <span class="text-danger">1</span> قرار می گیرد!</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="more_packet_price" class="control-label"> هزینه اضافه به ازای هر بسته (تومان) :</label>
                            <input type="text" id="more_packet_price" class="form-control comma" name="more_packet_price"
                                   placeholder="هزینه اضافه به ازای هر بسته را وارد کنید"
                                   value="{{ old('more_packet_price', number_format($shipping->more_packet_price)) }}"  />
                            <span class="text-muted-dark mt-2 mr-1 font-weight-bold fs-11">اگر خالی باشد به صورت پیش فرض <span class="text-danger">0</span> قرار می گیرد!</span>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="logo" class="control-label"> لوگو: </label>
                            <input type="file" id="logo" class="form-control" name="logo" value="{{ old('logo') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label for="provinces" class="control-label"> استان ها: </label>
                            <select class="form-control" id="province-select-box">
                                <option value="">انتخاب</option>
                                @foreach ($provinces ?? [] as $province)
                                    <option value="{{ $province->id }}"
                                        {{ in_array($province->id, old('provinces', [])) ? 'selected' : null }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if ($shipping->logo)
                        <div class="col-12 text-center">
                            <div class="img-holder my-4 img-show w-100 bg-light" style="max-height: 300px;">
                                <img src="{{ asset($shipping->logo->url) }}" style="max-height: 300px" alt="logo">
                            </div>
                        </div>
                    @endif
                    <div class="col-12">
                        <div class="form-group">
                            <label for="description" class="control-label">توضیحات :</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $shipping->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="form-group d-flex">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="status" value="1"
                                    {{ old('status', $shipping->status) ? 'checked' : '' }} />
                                <span class="custom-control-label">فعال</span>
                            </label>
                            <label class="custom-control custom-checkbox mr-5">
                                <input id="is-public-checkbox" type="checkbox" class="custom-control-input" name="is_public" value="1" {{ $shipping->is_public ? 'checked' : '' }} />
                                <span class="custom-control-label">عمومی</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="province-price-section" class="row mt-5 justify-content-center">
                    <div class="col-12 bg-gray-darker text-center py-3" style="border-radius: 10px;">
                      <p class="fs-18 text-white font-weight-bold mb-0 ">قیمت دهی به استان ها</p>
                    </div>
                    <div class="col-12 col-xl-7 mt-4">
                        <div class="row">
                            <x-table-component id="provinces-table">
                                <x-slot name="tableTh">
                                    <tr>
                                        <th>استان</th>
                                        <th>هزینه ارسال (تومان)</th>
                                        <th>حذف</th>
                                    </tr>
                                </x-slot>
                                <x-slot name="tableTd">
                                    <tr id="example-provinces-table-tr">
                                        <td class="d-none">
                                            <input hidden class="province-id" value="">
                                        </td>
                                        <td class="name"></td>
                                        <td class="price">
                                            <input type="text" class="comma form-control province-price">
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-icon btn-danger" onclick="removeProvinceTr(event)">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @foreach ($shipping->provinces ?? [] as $province)
                                        <tr>
                                            <td class="d-none">
                                                <input hidden class="province-id" value="{{ $province->id }}">
                                            </td>
                                            <td class="name">{{ $province->name }}</td>
                                            <td class="price">
                                                <input type="text" class="comma form-control province-price" value="{{ number_format($province->pivot->price) }}">
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-icon btn-danger" onclick="removeProvinceTr(event)">
                                                    <i class="fa fa-trash-o"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-table-component>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="text-center">
                            <button class="btn btn-warning" type="submit" onclick="update(event)">بروزرسانی</button>
                        </div>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-card>
@endsection

@section('scripts')
    <script>

        const isPublicCheckbox = $('#is-public-checkbox');
        const provinceSelectBox = $('#province-select-box');  
        const provincesTable = $('#provinces-table tbody'); 
        const provincePriceSection = $('#province-price-section');  
        const exampleProvinceTr = $('#example-provinces-table-tr').clone().removeAttr('id').hide();  
        const allProvinces = @json($provinces);  

        let removeProvinceTr = (event) => $(event.target).closest('tr').remove();

        provinceSelectBox.select2({
            placeholder: 'انتخاب استان'
        });

        $(document).ready(() => {  

            $('#example-provinces-table-tr').remove(); 

            if (@json($shipping->is_public)) {
                provincePriceSection.hide();
            }

            provinceSelectBox.change((event) => {  

                const defaultPrice = $('#default_price').val();  
                const provinceId = event.target.value;  
                const province = allProvinces.find(p => p.id == provinceId);  

                let isProvinceExist = false;
                provincesTable.find('tr').each(function() {
                    if ($(this).find('.province-id')?.val() == provinceId) isProvinceExist = true;
                });

                if (isProvinceExist) return;  

                const newTr = exampleProvinceTr.clone();  
                newTr.find('.province-id').val(province.id);  
                newTr.find('.name').text(province.name);  
                newTr.find('.province-price').val(defaultPrice);  

                provincesTable.append(newTr.show()); 
                comma();

                provincePriceSection.show(); 
            });  

            provincesTable.on('click', '.btn-danger', function () {  
                $(this).closest('tr').remove();  
            });  

            isPublicCheckbox.click((event) => {
                let isChecked = $(event.target).is(':checked');
                if (!provincePriceSection.hasClass('d-none')) {
                    if (isChecked) {
                        provincePriceSection.fadeOut(600);
                    }else {
                        provincePriceSection.fadeIn(600);
                    }
                }
            })
        });  

        function update(event) {

            event.preventDefault();

            if (isPublicCheckbox.is(':checked')) {
                $('#update-form').submit();
                return;
            }

            let index = 0;
            provincesTable.find('tr')?.each(function() {

                let provinceIdInput = $('<input hidden name="" value="" />');
                let provincePriceInput = $('<input hidden name="" value="" />');

                let provinceId = $(this).find('.province-id').val(); 
                let provincePrice = $(this).find('.province-price').val()?.replace(/,/g, "");

                provinceIdInput.attr('name', `provinces[${index}][id]`).val(provinceId);
                provincePriceInput.attr('name', `provinces[${index}][price]`).val(provincePrice);

                $('#update-form').append(provinceIdInput);
                $('#update-form').append(provincePriceInput);

                index++;
            });

            $('#update-form').submit();

        }

    </script>
@endsection