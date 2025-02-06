@extends('admin.layouts.master')

@section('content')

    <div class="page-header">
        <x-breadcrumb :items="[['title' => 'لیست سفارشات', 'route_link' => 'admin.orders.index'], ['title' => 'ثبت سفارش جدید']]" />
        <a href="{{ route('admin.orders.index') }}" class="btn btn-warning text-dark font-weight-bold btn-sm">بازگشت</a>
    </div>

    <form action="" method="POST" id="NewOrderForm">

        @csrf

        <x-card>
            <x-slot name="cardTitle">اطلاعات سفارش</x-slot>
            <x-slot name="cardOptions"></x-slot>
            <x-slot name="cardBody">
                <div class="row">
                    <div class="col-12 col-xl-3 form-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="customer-select-box">مشتری <span class="text-danger">&starf;</span></label>
                            <button 
                                class="btn btn-icon btn-sm btn-success" 
                                type="button" 
                                data-toggle="modal" 
                                data-target="#CreateNewCustomerModal" 
                                style="padding-block: 2px;">
                                <span class="fs-10">ثبت مشتری جدید</span>
                                <i class="fa fa-plus" style="font-size: 8px;"></i>
                            </button>
                        </div>
                        <select id="customer-select-box" name="customer_id" class="form-control"></select>
                    </div>
                    <div class="col-12 col-xl-3 form-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="AddressSelectBox">آدرس <span class="text-danger">&starf;</span></label>
                            <button 
                                id="NewAddressBtn" 
                                class="btn btn-icon btn-sm btn-primary d-none" 
                                type="button" 
                                data-toggle="modal" 
                                data-target="#CreateNewAddressModal" 
                                style="padding-block: 2px;">
                                <span class="fs-10">ثبت آدرس جدید</span>
                                <i class="fa fa-plus" style="font-size: 8px;"></i>
                            </button>
                        </div>
                        <select id="AddressSelectBox" name="address_id" class="form-control">
                            <option value="">انتخاب آدرس</option>
                        </select>
                    </div>
                    <div class="col-12 col-xl-3 form-group">
                        <label for="ShippingSelectBox">حمل و نقل <span class="text-danger">&starf;</span></label>
                        <select id="ShippingSelectBox" name="shipping_id" class="form-control">
                            <option value="">انتخاب آدرس</option>
                        </select>
                    </div>
                    <div class="col-12 col-xl-3 form-group">
                        <label for="DiscountOnOrderInput">تخفیف روی سفارش (تومان) :</label>
                        <input id="DiscountOnOrderInput" type="text" class="form-control comma" name="discount_on_order">
                    </div>
                </div>
                <div class="row">
                    {{-- <div class="col-12 col-xl-3 form-group">
                        <label for="PayTypeSelectBox">نوع پرداخت <span class="text-danger">&starf;</span></label>
                        <select name="pay_type" id="PayTypeSelectBox" class="form-control">
                            <option value=""></option>
                            <option value="gateway">درگاه</option>
                            <option value="wallet">کیف پول</option>
                            <option value="both">هر دو</option>
                        </select>
                    </div>
                    <div class="col-12 col-xl-3 form-group">
                        <label for="PaymentDrivereSelectBox">انتخاب درگاه</label>
                        <select name="payment_driver" id="PaymentDrivereSelectBox" class="form-control">
                            <option value=""></option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver['name'] }}">{{ $driver['label'] }}</option>
                            @endforeach
                        </select>
                        <b class="text-muted-dark mt-2 mr-1 fs-11">اگر نوع پرداخت درگاه بود انتخاب این فیلد الزامی است</b>
                    </div> --}}
                    {{-- <div class="col-12 col-xl-3 form-group">
                        <label for="DiscountOnOrderInput">تخفیف روی سفارش (تومان) :</label>
                        <input id="DiscountOnOrderInput" type="text" class="form-control comma" name="discount_on_order">
                    </div> --}}
                    {{-- <div class="col-12 col-xl-3 form-group">
                        <label for="CouponCode">کد تخفیف :</label>
                        <input id="CouponCode" type="text" class="form-control" name="copon_code">
                    </div> --}}
                </div>
                <div class="row">
                    <div class="col-12 form-group">
                        <textarea name="description" id="description" rows="3" class="form-control" placeholder="توضیحات"></textarea>
                    </div>
                </div>
            </x-slot>
        </x-card>

        <x-card>
            <x-slot name="cardTitle">انتخاب محصولات</x-slot>
            <x-slot name="cardOptions"><x-card-options /></x-slot>
            <x-slot name="cardBody">
                <div class="row mb-5">
                    <div class="col-xl-3 col-12">
                        <div class="form-group">
                            <label>انتخاب محصول :</label>
                            <select class="form-control" id="search-products">
                                <option value="">انتخاب</option>
                                {{-- @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->title }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-12">
                        <div class="form-group">
                            <label>انتخاب تنوع :</label>
                            <select id="search-varieties" class="form-control">
                            	<option value="">انتخاب</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
					<x-table-component id="VarietiesTable">
						<x-slot name="tableTh">
							<tr>
								<th>تصویر</th>
								<th>عنوان</th>
								<th>قیمت واحد (تومان)</th>
								<th>تعداد</th>
								<th>تخفیف واحد</th>
								<th>قیمت نهایی</th>
								<th>حذف</th>
							</tr>
						</x-slot>
						<x-slot name="tableTd">
							<tr id="ExampleTableRow">
								<td class="d-none hidden-inputs">
									<input type="hidden" class="variety-id-hidden-input">
									<input type="hidden" class="variety-quantity-hidden-input">
								</td>
								<td class="variety-image">
									<div class="bg-light pb-1 pt-1 img-holder img-show w-100" style="max-height: 60px; border-radius: 4px;">
										<img src="" style="height: 50px;">
									</div>
								</td>
								<td class="variety-title"></td>
								<td class="variety-amount"></td>
								<td class="variety-quantity">
									<button type="button" class="btn btn-sm btn-icon btn-warning" onclick="decrementQuantity(event)">
										<i class="fa fa-minus"></i>
									</button>
									<span class="variety-quantity-text mx-2" style="pointer-events: none;">1</span>
									<button type="button" class="btn btn-sm btn-icon btn-success" onclick="incrementQuantity(event)">
										<i class="fa fa-plus"></i>
									</button>
								</td>
								<td class="variety-discount-price"></td>
								<td class="variety-final-price"></td>
								<td class="variety-delete-btn">
									<button type="button" class="btn btn-sm btn-icon btn-danger text-white" data-original-title="حذف" onclick="removeVarityRowFromTable(event)">
										<i class="fa fa-trash-o"></i>
									</button>
								</td>
							</tr>
						</x-slot>
					</x-table-component>
                </div>
            </x-slot>
        </x-card>

        <div class="row" style="margin-bottom: 200px;">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-sm btn-primary" onclick="storeNewOrder()">محاسبه سفارش</button>
                </div>
            </div>
        </div>

    </form>

    {{-- <x-modal id="PayLinkModal" size="md">
        <x-slot name="title">لینک درگاه سفارش جدید</x-slot>
        <x-slot name="body">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success">
                        <span id="NewOrderSuccessMessage"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <span id="NewOrderPayLink"></span>
                    </div>
                </div>
            </div>
        </x-slot>
    </x-modal> --}}

    {{-- <x-modal id="OrderShowcaseModal" size="lg">
        <x-slot name="title">صورتحساب سفارش جدید</x-slot>
        <x-slot name="body">

            <x-table-component id="VarietiesShowcaseTable">
                <x-slot name="tableTh">
                    <tr>
                        <th>عنوان</th>
                        <th>قیمت</th>
                        <th>تعداد</th>
                        <th>تخفیف</th>
                        <th>قیمت نهایی</th>
                    </tr>
                </x-slot>
                <x-slot name="tableTd">
                    <tr id="ExampleShowcaseTableRow">
                        <td class="variety-title"></td>
                        <td class="variety-base-price"></td>
                        <td class="variety-quantity"></td>
                        <td class="variety-discount-amount"></td>
                        <td class="variety-final-price"></td>
                    </tr>
                </x-slot>
            </x-table-component>

            <div class="row">
                <div class="col-12 col-xl-6 my-2">
                    <ul class="list-group" id="TotalInvoicesShowcase"></ul>
                </div>
                <div class="col-12 col-xl-6 my-2">
                    <ul class="list-group" id="DiscountsShowcase"></ul>
                </div>
                <div class="col-12 col-xl-6 my-2">
                    <ul class="list-group" id="ShippingsShowcase"></ul>
                </div>
                <div class="col-12 col-xl-6 my-2">
                    <ul class="list-group" id="PaysShowcase"></ul>
                </div>
                <div class="col-12 mt-5">
                    <button class="btn btn-block btn-primary" onclick="storeNewOrder()">تایید نهایی</button>
                </div>
            </div>
        </x-slot>
    </x-modal> --}}

    <x-modal id="CreateNewCustomerModal" size="md">
        <x-slot name="title">ثبت مشتری جدید</x-slot>
        <x-slot name="body">
            <form id="CreateNewCustomerForm" action="{{ route('admin.customers.store') }}" method="POST">
                <div class="row">
                    <div class="col-12 col-xl-6 form-group">
                        <label for="first_name"> نام: <span class="text-danger">&starf;</span></label>
                        <input type="text" id="first_name" class="form-control" name="first_name" value="{{ old('first_name') }}" />
                    </div>
                    <div class="col-12 col-xl-6 form-group">
                        <label for="last_name">نام خانوادگی: <span class="text-danger">&starf;</span></label>
                        <input type="text" id="last_name" class="form-control" name="last_name" value="{{ old('last_name') }}" />
                    </div>
                    <div class="col-12 col-xl-6 form-group">
                        <label for="mobile">شماره همراه: <span class="text-danger">&starf;</span></label>
                        <input type="text" id="mobile" class="form-control" name="mobile" value="{{ old('mobile') }}" />
                    </div>
                    <div class="col-12 col-xl-6 form-group">
                        <label for="password"> کلمه عبور: <span class="text-danger">&starf;</span></label>
                        <input type="text" id="password" class="form-control" name="password" />
                    </div>
                    <div class="col-12 col-xl-6 form-group">
                        <label for="email"> ایمیل:</label>
                        <input type="text" id="email" class="form-control" name="email" value="{{ old('email') }}" />
                    </div>
                    <div class="col-12 col-xl-6 form-group">
                        <label for="national_code"> کد ملی:</label>
                        <input type="text" id="national_code" class="form-control" name="national_code" value="{{ old('national_code') }}" />
                    </div>
                    <div class="col-12 col-xl-6 form-group">
                        <label for="card_number"> شماره کارت:</label>
                        <input type="text" id="card_number" class="form-control" name="card_number" value="{{ old('card_number') }}" />
                    </div>
                    <div class="col-12 col-xl-6 form-group">
                        <label for="birth_date_show">تاریخ تولد :</label>
                        <input class="form-control fc-datepicker" id="birth_date_show" type="text"  autocomplete="off" />
                        <input name="birth_date" id="birth_date_hide" type="hidden" value="{{ old('birth_date') }}" />
                    </div>
                    <div class="col-12 form-group">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="newsletter" value="1" {{ old('newsletter', 1) == 1 ? '' : null }} />
                            <span class="custom-control-label">خبرنامه</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="foreign_national" value="1" {{ old('foreign_national', 1) == 1 ? '' : null }} />
                            <span class="custom-control-label">تبعه خارجی</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="status" value="1" {{ old('status', 1) == 1 ? 'checked' : null }} />
                            <span class="custom-control-label">وضعیت</span>
                        </label>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <div class="w-100 d-flex justify-content-center mt-2" style="gap: 8px;">
                <button class="btn btn-primary btn-sm" onclick="storeNewCustomer()">ثبت و ذخیره</button>
                <button data-dismiss="modal" class="btn btn-outline-danger btn-sm">انصراف</button>
            </div>
        </x-slot>
    </x-modal>

    <x-modal id="CreateNewAddressModal" size="md">
        <x-slot name="title">ثبت آدرس جدید</x-slot>
        <x-slot name="body">
            <form id="CreateNewAddressForm" action="{{ route('admin.addresses.store') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-12 col-xl-6">
                        <div class="form-group">
                            <label for="province_id">انتخاب استان : <span class="text-danger">&starf;</span></label>
                            <select name="province_id" id="province_id" class="form-control select2" onchange="appendCitiesToSelectBox(event)" required>
                                <option value="">استان را انتخاب کنید</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                      <div class="form-group" id="city_id_container" style="display: none;">
                        <label for="city_id">انتخاب شهر : <span class="text-danger">&starf;</span></label>
                        <select name="city" id="city_id" class="form-control select2" required>
                          <option value="">شهر را انتخاب کنید</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-12 col-xl-6">
                      <div class="form-group">
                        <label>نام : <span class="text-danger">&starf;</span></label>
                        <input id="NewAddressFirstName" type="text" class="form-control" placeholder="نام کاربر را وارد کنید" required>
                      </div>
                    </div>
                    <div class="col-12 col-xl-6">
                      <div class="form-group">
                        <label>نام و نام خانوادگی :<span class="text-danger">&starf;</span></label>
                        <input id="NewAddressLastName" type="text" class="form-control" placeholder="نام خانوادگی کاربر را وارد کنید" required>
                      </div>
                    </div>
                    <div class="col-12 col-xl-6">
                      <div class="form-group">
                        <label>کد پستی :<span class="text-danger">&starf;</span></label>
                        <input id="NewAddressPostalCode" type="text" class="form-control" placeholder="کد پستی را وارد کنید" required >
                      </div>
                    </div>
                    <div class="col-12 col-xl-6">
                      <div class="form-group">
                        <label>موبایل :<span class="text-danger">&starf;</span></label>
                        <input id="NewAddressMobile" type="text" class="form-control" placeholder="موبایل را وارد کنید" required>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group">
                        <label>آدرس :<span class="text-danger">&starf;</span></label>
                        <textarea id="NewAddressAddress" class="form-control" rows="3"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
            </form>
            <div class="modal-footer justify-content-center mt-2" style="gap: 8px;">
                <button class="btn btn-primary btn-sm" onclick="storeNewAddress()">ثبت و ذخیره</button>
                <button data-dismiss="modal" class="btn btn-outline-danger btn-sm">انصراف</button>
            </div>
        </x-slot>
    </x-modal>

@endsection

@section('scripts')

    @include('core::includes.date-input-script', [
        'dateInputId' => 'birth_date_hide',
        'textInputId' => 'birth_date_show',
    ])

    <script>

        let searchedCustomers = [];

        function formatRepo(repo) {
            if (repo.loading) {
                return "در حال بارگذاری...";
            }

            var $container = $(
                    "<div class='select2-result-repository clearfix'>" +
                    "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__title'></div>" +
                    "</div>" +
                    "</div>"
            );

            let text = `شناسه: ${repo.id} | موبایل: ${repo.mobile}`;
            if (repo.name) {
                text +=  ` | نام: ${repo.name}`;
            }
            $container.find(".select2-result-repository__title").text(text);

            return $container;
        }

        function formatRepoSelection(repo) {
            let text = `شناسه: ${repo.id} | موبایل: ${repo.mobile}`;
            if (repo.name) {
                text += ` | نام: ${repo.name}`;
            }
            return repo.id ? text : repo.text;
        }

        $('#customer-select-box').select2({
            ajax: {
                url: '{{ route('admin.customers.search') }}',
                dataType: 'json',
                processResults: (response) => {

                    let customers = response.data.customers || [];
                    searchedCustomers = customers;

                    return {
                        results: customers.map(customer => ({
                            id: customer.id,
                            mobile: customer.mobile,
                            name: customer.full_name || ''
                        })),
                    };
                },
                cache: true,
            },
            placeholder: 'انتخاب مشتری',
            templateResult: formatRepo,
            minimumInputLength: 1,
            templateSelection: formatRepoSelection
        });

    </script>

    <script>

        const customerSelectBox = $('#customer-select-box');
        const createNewCustomerModal =$('#CreateNewCustomerModal');
        const createNewAddressModal = $('#CreateNewAddressModal');
        const addressSelectBox = $('#AddressSelectBox');
        const shippingSelectBox = $('#ShippingSelectBox');
        // const payTypeSelectBox = $('#PayTypeSelectBox');
        // const paymentDrivereSelectBox = $('#PaymentDrivereSelectBox');
        const provinceSelectBox = $('#province_id');
        const citySelectBox = $('#city_id');
        const newAddressButton = $('#NewAddressBtn');
        const productsSelectBox = $('#search-products');
        const varietiesSelectBox = $('#search-varieties');
        const varietiesTable = $('#VarietiesTable');
        const exampleTableRow = $('#ExampleTableRow').clone().removeAttr('id');
        const varietiesShowcaseTable = $('#VarietiesShowcaseTable');
        const exampleShowcaseTableRow = $('#ExampleShowcaseTableRow').clone().removeAttr('id');
        // const orderShowcaseModal = $('#OrderShowcaseModal');
        // const payLinkModal = $('#PayLinkModal');

        let searchedVarieties = [];
        let productsCollection = [];
        let index = 0;
        
        let showNewAddressButton = () => newAddressButton.hasClass('d-none') ? newAddressButton.removeClass('d-none') : null;
        let makeSelectBoxLabel = (element, newLabel) => element.select2({placeholder: newLabel});
        // let closeOrderShowcaseModal = () => orderShowcaseModal.modal('hide');
        // let showPayLinkModal = () => payLinkModal.modal('show');

        makeSelectBoxLabel(addressSelectBox, 'ابتدا مشتری را انتخاب کنید');
        makeSelectBoxLabel(shippingSelectBox, 'ابتدا یک آدرس را انتخاب کنید');
        makeSelectBoxLabel(productsSelectBox, 'انتخاب محصول');
        makeSelectBoxLabel(varietiesSelectBox, 'ابتدا محصول را انتخاب کنید سپس تنوع');
        // makeSelectBoxLabel(payTypeSelectBox, 'نوع پرداخت را انتخاب کنید');
        // makeSelectBoxLabel(paymentDrivereSelectBox, 'درگاه پرداخت را در صورت نیاز انتخاب کنید');
        makeSelectBoxLabel(provinceSelectBox, 'استان را انتخاب کنید');
        makeSelectBoxLabel(citySelectBox, 'شهر را انتخاب کنید');

        $(document).ready(() => {

            customerSelectBox.on('change', (event) => {
                let customerId = event.target.value;
                loadCustomerAddresses(event.target.value);
            });

            $('#ExampleTableRow').remove();
            $('#ExampleShowcaseTableRow').remove();

			searchProducts();

            productsSelectBox.change(() => {
                $.ajax({
                    url: @json(route('admin.products.load-varieties')),
                    type: 'GET',
                    data: {
                        product_id: productsSelectBox.val()
                    },
                    headers: {
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    success: function(response) {
                        makeSelectBoxLabel(varietiesSelectBox, 'تنوع را انتخاب کنید');
                        makeVarietiesOptions(response.varieties);
                    },
                    error: function(error) {
                        showErrorMessages(error);
                    }
                });
            });

            varietiesSelectBox.on('select2:select', () => {
                let varietyId = varietiesSelectBox.val();
                addVarietyToTable(varietyId);
            });

            addressSelectBox.on('select2:select', () => {
                getShippableShippings();
            });

        });

		function searchProducts() {  
			productsSelectBox.select2({  
				ajax: {  
					url: @json(route('admin.products.search')),  
					dataType: 'json',  
					delay: 250, 
					processResults: (response) => {  
						let products = response.data.products || [];   
                        console.log(products);
						products.forEach(product => {  
							if (!productsCollection.find(p => p.id === product.id)) {  
								productsCollection.push(product);  
							}   
						});  
						return {  
							results: products.map(product => ({  
								id: product.id,  
								title: product.title,  
							})),  
						};  
					},  
					cache: true,  
					error: (jqXHR, textStatus, errorThrown) => {  
						console.error("Error fetching products:", textStatus, errorThrown);  
					},  
				},  
				placeholder: 'عنوان محصول را وارد کنید',  
				minimumInputLength: 1,  
				templateResult: (repo) => {  
					if (repo.loading) return "در حال بارگذاری...";  

					let $container = $(  
					"<div class='select2-result-repository clearfix'>" +  
					"<div class='select2-result-repository__meta'>" +  
					"<div class='select2-result-repository__title'></div>" +  
					"</div>" +  
					"</div>"  
					);  

					$container.find(".select2-result-repository__title").text(repo.title);  

					return $container;  
				},  
				templateSelection: (repo) => {  
					return repo.id ? repo.title : repo.text;  
				},  
			});  
		}

        function getShippableShippings() {

            if (customerSelectBox.val() === null) {
                popup('انتخاب مشتری', 'warning', 'انتخاب مشتری الزامی است');
                return;
            }
            if (addressSelectBox.val() === null) {
                popup('انتخاب آدرس', 'warning', 'انتخاب آدرس الزامی است');
                return;
            }
            
            const data = {
                address_id: addressSelectBox.val(),
                customer_id: customerSelectBox.val()
            };

            $.ajax({
                url: @json(route('admin.orders.shippable-shippings')),
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': @json(csrf_token())
                },
                success: (response) => {
                    appendAvailableShippingsToSelectBox(response.data.shippings)
                },
                error: (error) => {
                    showErrorMessages(error);
                }
            });

        }

        function validation() {
            if (varietiesTable.find('tbody tr').length < 1) 
                popup('عدم وجود محصول', 'warning', 'انتخاب حداقل یک محصول الزامی است');
            else if (customerSelectBox.val() === null) 
                popup('انتخاب مشتری', 'warning', 'انتخاب مشتری الزامی است');
            else if (addressSelectBox.val() === null) 
                popup('انتخاب آدرس', 'warning', 'انتخاب آدرس الزامی است');
            // else if (payTypeSelectBox.val() === null) 
            //     popup('انتخاب نوع پرداخت', 'warning', 'انتخاب نوع پرداخت الزامی است');
            else if (shippingSelectBox.val() === null) 
                popup('انتخاب نوع حمل و نقل', 'warning', 'انتخاب نوع حمل و نقل الزامی است');
            // else if (['gateway', 'both'].includes(payTypeSelectBox.val()) && paymentDrivereSelectBox.val() === null) 
            //     popup('انتخاب درگاه', 'warning', 'با انتخاب روش پرداختی فعلی شما , انتخاب درگاه الزامی است');
            else return true
            return false
        }

        function getFormData() {

            const formData = new FormData();

            formData.append('customer_id', customerSelectBox.val());
            formData.append('address_id', addressSelectBox.val());
            formData.append('shipping_id', shippingSelectBox.val());
            // formData.append('pay_type', payTypeSelectBox.val());
            formData.append('discount_amount', $('#DiscountOnOrderInput').val().replace(/,/g, ""));
            // formData.append('coupon_code', $('#CouponCode').val());
            formData.append('description', $('#description').val());
            // formData.append('payment_driver', paymentDrivereSelectBox.val());

            let index = 0;
            varietiesTable.find('tbody tr').each(function() {  
                formData.append(`varieties[${index}][id]`, $(this).find('.variety-id-hidden-input').val());  
                formData.append(`varieties[${index}][quantity]`, $(this).find('.variety-quantity-hidden-input').val());  
                index++;  
            });  

            return formData;
        }

        function showOrderShowcaseModal(carts, cartsShowcase) {

            varietiesShowcaseTable.find('tbody').empty();
            carts.forEach(cart => {
                let tr = exampleShowcaseTableRow.clone();
                tr.find('.variety-title').text(cart.variety.title_showcase.fullTitle);
                tr.find('.variety-base-price').text(cart.price.toLocaleString());
                tr.find('.variety-quantity').text(cart.quantity);
                tr.find('.variety-discount-amount').text(cart.discount_price.toLocaleString());
                tr.find('.variety-final-price').text(cart.cart_price_amount.toLocaleString());
                varietiesShowcaseTable.find('tbody').append(tr);
            });

            const cartShowcaseTotalsLabels = {
                total_items_amount: 'جمع مبلغ محصولات با تخفیف (تومان)',
                total_items_amount_without_discount: 'جمع مبلغ محصولات بدون تخفیف (تومان)',
                total_items_count: 'تعداد آیتم های خریداری شده',
                total_quantity: 'جمع اقلام',
            };

            const cartShowcaseDiscountLabels = {
                discount_on_coupon: 'تخفیف روی کد تخفیف (تومان)',
                discount_on_items: 'تخفیف روی آیتم های سفارش (تومان)',
                discount_on_order: 'تخفیف روی سفارش (تومان)',
                discount_total: 'جمع کل تخفیف ها (تومان)',
            };

            const cartShowcasePaysLabels = {
                total_invoices_amount: 'مبلغ کل سفارش (تومان)',
                pay_by_gateway: 'پرداخت با درگاه (تومان)',
                pay_by_wallet_main_balance: 'پرداخت با کیف پول اصلی (تومان)',
                pay_by_wallet_gift_balance: 'پرداخت با کیف پول هدیه (تومان)',
            };

            const cartShowcaseShippingsLabels = {
                shipping_amount: 'هزینه حمل و نقل (تومان)',
                shipping_first_packet_size: 'سایز اولین بسته',
                shipping_more_packet_price: 'هزینه بسته های اضافی',
                shipping_packet_amount: 'هزینه هر بسته',
            };

            $('#PaysShowcase').empty();
            $('#DiscountsShowcase').empty();
            $('#ShippingsShowcase').empty();
            $('#TotalInvoicesShowcase').empty();

            $.each(cartsShowcase, function(key, value) {
                let amount = parseInt(value).toLocaleString();
                if (key in cartShowcasePaysLabels) {
                    $('#PaysShowcase').append(`<li class="list-group-item">${cartShowcasePaysLabels[key]} : <b>${amount}</b></li>`);
                }else if (key in cartShowcaseDiscountLabels) {
                    $('#DiscountsShowcase').append(`<li class="list-group-item">${cartShowcaseDiscountLabels[key]} : <b>${amount}</b></li>`);
                }else if (key in cartShowcaseTotalsLabels) {
                    $('#TotalInvoicesShowcase').append(`<li class="list-group-item">${cartShowcaseTotalsLabels[key]}: <b>${amount}</b></li>`);
                }else if (key in cartShowcaseShippingsLabels) {
                    $('#ShippingsShowcase').append(`<li class="list-group-item">${cartShowcaseShippingsLabels[key]}: <b>${amount}</b></li>`);
                }
            });

            orderShowcaseModal.modal('show');
        }

        function makeVarietiesOptions(varieties) {
            console.log(varieties);
            if (Array.isArray(varieties)) {
                searchedVarieties = varieties;
                let options = '<option value="">انتخاب</option>';
                searchedVarieties.forEach((variety) => {
                    let title = variety.title + ' -- ' + 'موجودی ' + variety.quantity ?? 0;
                    options += `<option value="${variety.id}">${title}</option>`;
                });
                $('#search-varieties').html(options).trigger('change');
            } 
        }

        function incrementQuantity(event) {  
            let row = $(event.target).closest('tr');  
            let quantityInput = row.find('.variety-quantity-hidden-input');  
            let quantityText = row.find('.variety-quantity-text');  
            let quantity = parseInt(quantityInput.val()) + 1;  
            quantityInput.val(quantity);  
            quantityText.text(quantity); 
            updateVarietyFinalPriceInTable(row);  
        } 

        function decrementQuantity(e) {

            let row = $(event.target).closest('tr');  
            let quantityInput = row.find('.variety-quantity-hidden-input');  
            let quantityText = row.find('.variety-quantity-text');  
            let quantity = parseInt(quantityInput.val());  

            if (quantity > 1) { 
            quantityInput.val(quantity - 1);  
            quantityText.text(quantity - 1); 
            updateVarietyFinalPriceInTable(row);  
            return
            } 

            Swal.fire ({  
            title: 'حداقل تعداد 1 می باشد',
            text: 'آیا تمایل دارید محصول را از سبد حذف کنید',
            icon: "warning",  
            confirmButtonText: 'حذف کن',  
            showDenyButton: true,  
            denyButtonText: 'انصراف',  
            dangerMode: true,  
            }).then((result) => {  
            if (result.isConfirmed) {  
                removeVarityRowFromTable(e)
                Swal.fire({  
                icon: "success",  
                text: "آیتم با موفقیت حذف شد."  
                });  
            } 
            });
        }

        function updateVarietyFinalPriceInTable(tr) {  
            let price = parseInt(tr.find('.variety-amount').text().replace(/,/g, ""));  
            let discountPrice = parseInt(tr.find('.variety-discount-price').text().replace(/,/g, ""));  
            let quantity = parseInt(tr.find('.variety-quantity-hidden-input').val()); 
            if (!isNaN(quantity) && quantity > 0) { 
                let finalPrice = quantity * (price - discountPrice);  
                tr.find('.variety-final-price').text(finalPrice.toLocaleString());  
            } else {  
                tr.find('.variety-final-price').text('0'); 
            }  
        } 

        function removeVarityRowFromTable(e) {
            $(e.target).closest('tr').remove();
        }
    
        function addVarietyToTable(varietyId) {  

            let variety = searchedVarieties.find(v => v.id == varietyId);     
			let product = productsCollection.find(p => p.id == variety.product_id);
			let newRow = exampleTableRow.clone();  

            newRow.find('.variety-image img').attr('src', product.main_image?.url);  
            newRow.find('.variety-title').text(variety.title);  
            newRow.find('.variety-amount').text(variety.final_price.base_amount.toLocaleString());  
            newRow.find('.variety-discount-price').text(variety.final_price.discount_price.toLocaleString());  
            newRow.find('.variety-final-price').text(variety.final_price.amount.toLocaleString());  
            
            newRow.find('.variety-id-hidden-input')  
				.val(variety.id)  
				.attr('name', `carts[${index}][variety_id]`);  

            newRow.find('.variety-quantity-hidden-input')  
				.val(1) 
				.attr('name', `carts[${index}][quantity]`);  

            newRow.find('.variety-quantity-text').text(1);   

            varietiesTable.append(newRow);  
            comma(); 
            index++; 
        }  

        function loadCustomerAddresses(customerId) {
            $.ajax({
                url: @json(route('admin.customers.index')) + '/' + customerId + '/addresses',
                type: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                success: (response) => {
                    appendAddressesToSelectBox(response.data.addresses, true);
                    showNewAddressButton();
                },
                error: (error) => {
                    showErrorMessages(error);
                    return false;
                }
            });
        }

        function appendAddressesToSelectBox(addresses, isCustomerNew) {
            if (isCustomerNew) {
                addressSelectBox.empty();
                addressSelectBox.append('<option value=""></option>');
                makeSelectBoxLabel(addressSelectBox, 'آدرس را انتخاب کنید');
            }
            addresses.forEach(address => {
                let option = $('<option></option>');
                option.attr('value', address.id).text(`${address.address} - کد پستی : ${address.postal_code}`);
                addressSelectBox.append(option);
            });
        }

        function appendAvailableShippingsToSelectBox(shippings) {

            if (shippings.length < 1) {
                popup('عدم وجود حمل و نقل', 'warning', 'حمل و نقل مناسبی برای این آدرس پیدا نشده است');
                return;
            }

            shippingSelectBox.empty();
            shippingSelectBox.append('<option value=""></option>');
            makeSelectBoxLabel(shippingSelectBox, 'نوع حمل و نقل را انتخاب کنید');

            shippings.forEach(shipping => {
                let option = $('<option value="" data-amount=""></option>');
                option.attr('value', shipping.id).attr('data-amount', shipping.amount_showcase).text(shipping.name);
                shippingSelectBox.append(option);
            });

        }

        function storeNewCustomer() {
            
            let form = createNewCustomerModal.find('#CreateNewCustomerForm'); 
            let data = {  
                first_name: form.find('#first_name').val(),  
                last_name: form.find('#last_name').val(),  
                mobile: form.find('#mobile').val(),  
                password: form.find('#password').val(),  
                email: form.find('#email').val(),  
                national_code: form.find('#national_code').val(),  
                card_number: form.find('#card_number').val(),  
                birth_date: form.find('#birth_date_hide').val(),  
                newsletter: form.find('input[name="newsletter"]').is(':checked') ? 1 : 0,  
                foreign_national: form.find('input[name="foreign_national"]').is(':checked') ? 1 : 0,  
                status: form.find('input[name="status"]').is(':checked') ? 1 : 0  
            };  

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': @json(csrf_token()),
                    'Accept': 'application/json'
                },
                success: (response) => {  
                    popup('ثبت مشتری', 'success', response.message);
                },  
                error: (error) => {  
                    showErrorMessages(error);
                }  
            });

            createNewCustomerModal.modal('hide');
        }

        function storeNewAddress() {

            let form = createNewAddressModal.find('#CreateNewAddressForm'); 
            let data = {  
                city: form.find('#city_id').val(),  
                province_id: form.find('#province_id').val(),  
                first_name: form.find('#NewAddressFirstName').val(),  
                last_name: form.find('#NewAddressLastName').val(),  
                postal_code: form.find('#NewAddressPostalCode').val(),  
                mobile: form.find('#NewAddressMobile').val(),  
                address: form.find('#NewAddressAddress').val(),  
                customer_id: customerSelectBox.val(),  
            };  

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': @json(csrf_token()),
                    'Accept': 'application/json'
                },
                success: (response) => {  
                    popup('ثبت آدرس جدید', 'success', response.message);
                    appendAddressesToSelectBox([response.data.address], false)
                },  
                error: (error) => {  
                    showErrorMessages(error);
                }  
            });

            createNewAddressModal.modal('hide');
        }

        function storeNewOrder() {

            if (!validation()) return; 
            const data = getFormData();

            $.ajax({
                url: @json(route('admin.orders.store')),
                type: 'POST',
                data: data,
                contentType: false,  
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': @json(csrf_token())
                },
                success: (response) => {
                    console.log(response);
                    
                    // if (response.data.link.length > 1) {
                    //     // closeOrderShowcaseModal();
                    //     showPayLinkModal();
                    //     payLinkModal.find('#NewOrderSuccessMessage').text(response.message);
                    //     payLinkModal.find('#NewOrderPayLink').text(response.data.link);
                    // }else {
                    //     window.location.href = @json(route('admin.orders.index'));
                    // }
                },
                error: (error) => {
                    // closeOrderShowcaseModal();
                    showErrorMessages(error);
                }
            });

        }

        function appendCitiesToSelectBox(event) {

            var provinceId = $(event.target).val();

            citySelectBox.empty();
            citySelectBox.append('<option value="">شهر را انتخاب کنید</option>');
            $('#city_id_container').hide();

            if (provinceId) {
                $.ajax({
                    url: @json(route("admin.getCity")),
                    data: {provinceId: provinceId},
                    type: 'GET',
                    success: function (data) {
                        $.each(data, function (index, city) {
                            citySelectBox.append('<option value="' + city.id + '">' + city.name + '</option>');
                        });
                        $('#city_id_container').show();
                    },
                    error: function () {
                        alert('خطا در بارگذاری شهرها.');
                    }
                });
            }
        }

        function popup(title, type, message) {
            Swal.fire ({
                title: title,
                text: message,
                icon: type,
                confirmButtonText: 'بستن',
            });
        }

        function showErrorMessages(error) {
            let messages = '';

            if (error.responseJSON.errors) {
                for (const key in error.responseJSON.errors) {
                    if (error.responseJSON.errors.hasOwnProperty(key)) {
                        error.responseJSON.errors[key].forEach(message => {
                            messages += ' ' + message;
                        });
                    }
                }
            } else if (error.responseJSON.data) {
                for (const key in error.responseJSON.data) {
                    if (error.responseJSON.data.hasOwnProperty(key)) {
                        error.responseJSON.data[key].forEach(message => {
                            messages += ' ' + message;
                        });
                    }
                }
            } else {
                messages = error.responseJSON.message || 'An unknown error occurred.';
            }

            popup('خطا', 'error', messages);
        }

    </script>
@endsection
