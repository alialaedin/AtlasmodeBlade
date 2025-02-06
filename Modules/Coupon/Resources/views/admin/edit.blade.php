@extends('admin.layouts.master')
@section('content')
    <div class="page-header">
        @php
            $items = [
                ['title' => 'لیست تخفیف ها', 'route_link' => 'admin.coupons.index'],
                ['title' => 'ویرایش کد تخفیف', 'route_link' => null],
            ];
        @endphp
        <x-breadcrumb :items="$items" />
    </div>

    <x-card>
        <x-slot name="cardTitle">ویرایش کد تخفیف کد - {{ $coupon->id }}</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <x-alert-danger />
            <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <div class="col-12 col-xl-4">
                        <div class="form-group">
                            <label for="title" class="control-label"> عنوان: <span
                                    class="text-danger">&starf;</span></label>
                            <input type="text" id="title" class="form-control" name="title"
                                value="{{ old('title', $coupon->title) }}" required autofocus />
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="form-group">
                            <label for="code" class="control-label"> کد: <span
                                    class="text-danger">&starf;</span></label>
                            <input type="text" id="code" class="form-control" name="code"
                                value="{{ old('code', $coupon->code) }}" required autofocus />
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="form-group">
                            <label for="start_date_show" class="control-label">تاریخ شروع : <span
                                    class="text-danger">&starf;</span></label>
                            <input class="form-control fc-datepicker" id="start_date_show" type="text" autocomplete="off"
                                placeholder="تاریخ شروع را انتخاب کنید" />
                            <input name="start_date" id="start_date_hide" type="hidden"
                                value="{{ old('start_date', $coupon->start_date) }}" required />

                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="form-group">
                            <label for="end_date_show" class="control-label">تاریخ پایان : <span
                                    class="text-danger">&starf;</span></label>
                            <input class="form-control fc-datepicker" id="end_date_show" type="text" autocomplete="off"
                                placeholder="تاریخ پایان را انتخاب کنید" />
                            <input name="end_date" id="end_date_hide" type="hidden"
                                value="{{ old('end_date', $coupon->end_date) }}" />
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="form-group">
                            <label for="type" class="control-label"> نوع: </label>
                            <select class="form-control" name="type" id="type">
                                <option value="flat" {{ old('type', $coupon->type) == 'flat' ? 'selected' : '' }}>
                                    مبلغ
                                </option>
                                <option value="percentage"
                                    {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>
                                    درصد
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="form-group">
                            <label for="amount" id="amount-label" class="control-label"></label>
                            <input type="text" id="amount" class="form-control comma" name="amount"
                                value="{{ old('amount', number_format($coupon->amount)) }}" />
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="form-group">
                            <label for="usage_limit" class="control-label"> سقف استفاده : </label>
                            <input type="number" id="usage_limit" class="form-control" name="usage_limit"
                                value="{{ old('usage_limit', $coupon->usage_limit) }}" />
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="form-group">
                            <label for="usage_per_user_limit" class="control-label"> سقف استفاده برای هر کاربر : </label>
                            <input type="number" id="usage_per_user_limit" class="form-control" name="usage_per_user_limit"
                                value="{{ old('usage_per_user_limit', $coupon->usage_per_user_limit) }}" />
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="form-group">
                            <label for="min_order_amount" class="control-label"> حداقل مبلغ سبد خرید (تومان) : </label>
                            <input type="text" id="min_order_amount" class="form-control comma"
                                name="min_order_amount"
                                value="{{ old('min_order_amount', number_format($coupon->min_order_amount)) }}" />
                        </div>
                    </div>

                    {{-- <div class="col-12 col-xl-9">
                        <div class="form-group">
                            <label for="categories" class="control-label"> انتخاب دسته بندی : </label>
                            <select class="form-control select2" multiple id="categories">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $coupon->categories->contains($category->id) ? 'selected' : null }}>
                                        {{ $category->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}

                </div>

                {{-- <div class="row justify-content-center">
                    <div class="col-10">
                        <x-table-component id="categories-table" class="{{ $coupon->categories->isEmpty() ? 'd-none' : '' }} mt-3">
                            <x-slot name="tableTh">
                                <tr>
                                    <th>شناسه دسته بندی</th>
                                    <th>عنوان</th>
                                    <th>میزان تخفیف (%)</th>
                                </tr>
                            </x-slot>
                            <x-slot name="tableTd">
                                <tr id="example-td" class="d-none">
                                    <td class="category-id">
                                        <input type="hidden" name="" value="">
                                        <span></span>
                                    </td>
                                    <td class="category-title"></td>
                                    <td class="category-coupon-amount">
                                        <input type="text" class="form-control comma" name="">
                                    </td>
                                </tr>
                                @foreach($coupon->categories as $category)
                                    <tr>
                                        <td class="category-id">
                                            <input type="hidden" name="categories[{{ $loop->iteration }}][id]" value="{{ $category->id }}">
                                            <span class="font-weight-bold">{{ $category->id }}</span>
                                        </td>
                                        <td class="category-title">{{ $category->title }}</td>
                                        <td class="category-coupon-amount">
                                            <input
                                                type="text"
                                                class="form-control comma"
                                                name="categories[{{ $loop->iteration }}][amount]"
                                                value="{{ number_format($category->pivot->amount) }}"
                                            >
                                        </td>
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-table-component>
                    </div>
                </div> --}}

                <div class="row">
                    <div class="col">
                        <div class="text-center">
                            <button class="btn btn-warning" type="submit">بروزرسانی</button>
                        </div>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-card>
@endsection

@section('scripts')
    @include('core::includes.date-input-script', [
        'dateInputId' => 'end_date_hide',
        'textInputId' => 'end_date_show',
    ])

    @include('core::includes.date-input-script', [
        'dateInputId' => 'start_date_hide',
        'textInputId' => 'start_date_show',
    ])

    <script>
        $(document).ready(function() {

            const categoriesSelectOption = $('#categories');
            const categoriesTable = $('#categories-table');
            const exampleTableRow = categoriesTable.find('#example-td');
            const categoriesTableBody = categoriesTable.find('tbody');
            const couponTypeSelectOption = $('#type');
            const couponAmountInput = $('#amount');
            const allCategories = @json($categories);

            categoriesTable.removeClass('table-striped');
            categoriesTable.find('thead').css('background-color', '#333236');
            categoriesTable.find('thead tr th').css('color', '#FFFFFF');

            let initialValue = couponTypeSelectOption.val();
            let text = initialValue === 'flat' ? 'مبلغ : ' : 'درصد : ';
            $('#amount-label').text(text);

            couponTypeSelectOption.on('change', () => {
                text = $('#type').val() === 'flat' ? 'مبلغ : ' : 'درصد : ';
                $('#amount-label').text(text);
            });

            // categoriesSelectOption.on('change', (e) => {

            //     if (categoriesTable.hasClass('d-none')) {
            //         categoriesTable.removeClass('d-none');
            //     }

            //     const categoryIdsArr = $(e.target).val();
            //     let counter = 0;

            //     categoriesTableBody.find('tr').each(function () {
            //         const rowCategoryId = $(this).find('.category-id span').text();
            //         if (!categoryIdsArr.includes(rowCategoryId)) {
            //             $(this).remove();
            //         }
            //     });

            //     categoryIdsArr.forEach(categoryId => {

            //         const category = allCategories.find((c) => c.id === parseInt(categoryId));
            //         const existingRow = categoriesTableBody.find(`.category-id span:contains(${category.id})`);

            //         if (existingRow.length === 0) {

            //             const tableRow = exampleTableRow.clone();
            //             const couponAmount = couponAmountInput.val();
            //             const defaultAmount = couponAmount !== null && couponAmount > 0 ? couponAmount.toLocaleString() : 0;

            //             tableRow.removeAttr('id').removeClass('d-none');
            //             tableRow.find('.category-title').text(category.title);
            //             tableRow.find('.category-coupon-amount input').attr({
            //                 name: `categories[${counter}][amount]`,
            //                 value: defaultAmount
            //             });
            //             tableRow.find('.category-id span').text(category.id).addClass('font-weight-bold');
            //             tableRow.find('.category-id input').attr({
            //                 name: `categories[${counter}][id]`,
            //                 value: category.id
            //             });

            //             categoriesTableBody.prepend(tableRow);
            //         }

            //         counter++;

            //     });
            // });

        });
    </script>
@endsection
