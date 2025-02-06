@extends('admin.layouts.master')
@section('content')

    <div class="page-header">
        <x-breadcrumb :items="[['title' => 'محصولات انبار']]" />
        <div style="display: flex; gap: 8px;">
            <button id="increment-store-btn" class="btn btn-outline-success btn-sm">افزایش موجودی</button>
            <button id="decrement-store-btn" class="btn btn-outline-danger btn-sm">کاهش موجودی</button>
        </div>
    </div>

    <x-alert-danger />

    <x-card>
        <x-slot name="cardTitle">جستجوی پیشرفته</x-slot>
        <x-slot name="cardOptions"><x-card-options /></x-slot>
        <x-slot name="cardBody">
            <div class="row">
                <form action="{{ route('admin.stores.index') }}" method="GET" class="col-12">
                    <div class="row">

                        <div class="col-12 col-xl-3">
							<div class="form-group">
								<label>انتخاب محصول :</label>
								<select class="form-control product-select-box">
									<option value=""></option>
								</select>
							</div>
						</div>
						
						<div class="col-12 col-xl-3">
							<div class="form-group">
								<label>انتخاب تنوع :</label>
								<select name="variety_id" class="form-control variety-select-box" required>
									<option value=""></option>
								</select>
							</div>
						</div>

                        <div class="col-12 col-xl-3 form-group">
                            <label>از تاریخ :</label>
                            <input class="form-control fc-datepicker" id="start_date_show" type="text" autocomplete="off" placeholder="از تاریخ" />
                            <input name="start_date" id="start_date_hide" type="hidden" value="{{ request('start_date') }}" />
                        </div>
                        <div class="col-12 col-xl-3 form-group">
                            <label>تا تاریخ :</label>
                            <input class="form-control fc-datepicker" id="end_date_show" type="text" autocomplete="off"  placeholder="تا تاریخ" />
                            <input name="end_date" id="end_date_hide" type="hidden" value="{{ request('end_date') }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-9 col-lg-8 col-md-6 col-12">
                            <button class="btn btn-primary btn-block" type="submit">جستجو <i class="fa fa-search"></i></button>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                            <a href="{{ route('admin.stores.index') }}" class="btn btn-danger btn-block">
                                <span>حذف همه فیلتر ها</span>
                                <i class="fa fa-close"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </x-slot>
    </x-card>

    <x-card>
        <x-slot name="cardTitle">محصولات انبار</x-slot>
        <x-slot name="cardOptions">
			<div class="card-options">
				<a href="{{ route('admin.stores.transactions') }}" class="btn btn-sm btn-info fs-12">تراکنش ها</a>
			</div>
		</x-slot>
        <x-slot name="cardBody">
            <div class="row">
                <x-table-component>
                    <x-slot name="tableTh">
                        <tr>
                            @foreach(['ردیف', 'شناسه تنوع', 'تنوع', 'موجودی', 'اولین تراکنش', 'آخرین تراکنش'] as $th)
								<th>{{ $th }}</th>
							@endforeach
                        </tr>
                    </x-slot>
                    <x-slot name="tableTd">
                        @forelse ($stores as $store)
                            <tr>
                                <td class="font-weight-bold">{{ $loop->iteration }}</td>
                                <td>{{ $store->variety_id }}</td>
                                <td>{{ $store->variety->title }}</td>
                                <td>{{ number_format($store->balance) }}</td>
                                <td>{{ verta($store->created_at)->format('Y/m/d H:i') }}</td>
                                <td>{{ verta($store->updated_at)->format('Y/m/d H:i') }}</td>
                            </tr>
                        @empty
                            @include('core::includes.data-not-found-alert', ['colspan' => 6])
                        @endforelse
                    </x-slot>
                    <x-slot name="extraData">{{ $stores->onEachSide(0)->links('vendor.pagination.bootstrap-4') }}</x-slot>
                </x-table-component>
            </div>
        </x-slot>
    </x-card>

    <x-modal id="increase-decrease-modal" size="md">
        <x-slot name="title"></x-slot>
        <x-slot name="body">
            <form action="{{ route('admin.stores.store') }}" method="POST">

                @csrf
                <input type="hidden" name="type" id="type">

                <div class="row">

					<div class="col-12">
						<div class="form-group">
							<select class="form-control product-select-box">
								<option value=""></option>
							</select>
						</div>
					</div>
					
					<div class="col-12">
						<div class="form-group">
							<select name="variety_id" class="form-control variety-select-box" required>
								<option value=""></option>
							</select>
						</div>
					</div>

                    <div class="col-12">
                        <div class="form-group">
                            <input class="form-control" placeholder="تعداد" name="quantity" required/>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <textarea class="form-control" row="2" placeholder="توضیحات" name="description" required></textarea>
                        </div>
                    </div>

                </div>

                <div class="modal-footer justify-content-center mt-2">
                    <button class="btn btn-sm btn-primary" type="submit">ثبت و ذخیره</button>
                    <button class="btn btn-sm btn-outline-danger" data-dismiss="modal">انصراف</button>
                </div>

            </form>
        </x-slot>
    </x-modal>
	
@endsection

@section('scripts')

    @include('core::includes.date-input-script', [
        'dateInputId' => 'start_date_hide',
        'textInputId' => 'start_date_show',
    ])

    @include('core::includes.date-input-script', [
        'dateInputId' => 'end_date_hide',
        'textInputId' => 'end_date_show',
    ])

    <script>

		$('.variety-select-box').select2({ placeholder: 'ابتدا محصول را جستجو کنید' });

		function searchProducts() {  
			$('.product-select-box').each(function () {
				$(this).select2({  
					ajax: {  
						url: @json(route('admin.products.search')),  
						dataType: 'json',  
						delay: 250, 
						processResults: (response) => {  
							let products = response.data.products || [];  
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
			});
		}

		function searchVarieties() {
			$('.product-select-box').each(function() {
				let productSelectBox = $(this);
				let varietySelectBox = $(this).closest('form').find('.variety-select-box');
				productSelectBox.on('select2:select', () => {
					$.ajax({
						url: @json(route('admin.products.load-varieties')),
						type: 'GET',
						data: {
							product_id: productSelectBox.val()
						},
						success: function(response) {

							if (Array.isArray(response.varieties) && response.varieties.length > 0) {

								let url = window.location.href;
								let parsedUrl = new URL(url);
								let params = new URLSearchParams(parsedUrl.search);
								let selectedVarietyId = params.get('variety_id');

								varietySelectBox.empty();
								let options = '<option value="">انتخاب</option>';
								response.varieties.forEach((variety) => {
									options += `<option value="${variety.id}" ${selectedVarietyId === String(variety.id) ? 'selected' : ''}>${variety.title}</option>`;
								});
								varietySelectBox.append(options);
								varietySelectBox.select2({ placeholder: 'تنوع را انتخاب کنید' });

							}
						}
					});
				});
			});
		}

        $(document).ready(() => {

			searchProducts();
			searchVarieties();

            $('#increment-store-btn').click(() => showModal('increment'));
            $('#decrement-store-btn').click(() => showModal('decrement'));

        });

        function showModal(type) {
            const modal = $('#increase-decrease-modal');
            let text = type == 'increment' ? 'اضافه کردن به انبار' : 'کم کردن از انبار';
            modal.find('.modal-title').text(text);
            modal.find('#type').val(type);
            modal.modal('show');
        }
    </script>
@endsection
