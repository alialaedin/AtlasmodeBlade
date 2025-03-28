@extends('admin.layouts.master')

@section('content')

<div class="page-header">
  <x-breadcrumb :items="[['title' => 'لیست سفارشات', 'route_link' => 'admin.orders.index'], ['title' => 'جزئیات سفارش']]" />
  <div class="d-flex" style="gap: 6px;">
    <button class="btn btn-sm btn-warning" data-target="#edit-order-modal" data-toggle="modal">ویرایش سفارش</button>
    <button class="btn btn-sm btn-secondary" data-target="#edit-order-status-modal" data-toggle="modal">تغییر وضعیت</button>
    <a href="{{ route('admin.orders.print', ['ids' => $order->id]) }}" target="_blank" class="btn btn-purple btn-sm">پرینت</a>
  </div>
</div>

@php
  $statusColors = [
    'wait_for_payment' => 'warning',
    'new' => 'primary',
    'in_progress' => 'secondary',
    'delivered' => 'success',
    'canceled' => 'danger',
    'failed' => 'danger',
    'reserved' => 'info',
    'canceled_by_user' => 'danger',
  ];
@endphp

<x-card>
  <x-slot name="cardTitle">جزئیات سفارش</x-slot>
  <x-slot name="cardOptions"><x-card-options /></x-slot>
  <x-slot name="cardBody">
    @php
      $orderDetails = [
        ['title' => 'شناسه سفارش', 'value' => $order->id],
        ['title' => 'تاریخ ثبت', 'value' => verta($order->created_at)->format('Y/m/d H:i:')],
        ['title' => 'تاریخ تحویل', 'value' => verta($order->delivered_at)->format('Y/m/d H:i:s')],
        ['title' => 'وضعیت سفارش', 'value' => __('statuses.' . $order->status)],
        ['title' => 'شیوه ارسال', 'value' => $order->shipping->name],
        ['title' => 'رزرو شده', 'value' => $order->reserved ? 'بله' : 'خیر'],
      ];
    @endphp
    <div class="row">
      @foreach ($orderDetails as $detail)
        <div class="col-xl-4 my-1">
          <span class="font-weight-bold">{{ $detail['title'] }} : </span>
          <span>{{ $detail['value'] }}</span>
        </div>
      @endforeach
    </div>
  </x-slot>
</x-card>

@if ($order->description)
  <x-card>
    <x-slot name="cardTitle">توضیحات</x-slot>
    <x-slot name="cardOptions"><x-card-options /></x-slot>
    <x-slot name="cardBody">
      <div class="row">
        <p>{{ $order->description }}</p>
      </div>
    </x-slot>
  </x-card>
@endif

<x-card>
  <x-slot name="cardTitle">اطلاعات مشتری</x-slot>
  <x-slot name="cardOptions"><x-card-options /></x-slot>
  <x-slot name="cardBody">
    @php
      $customer = $order->customer;
      $genders = [
        'male' => 'مرد',
        'female' => 'زن',
        null => null,
      ];
      $customerDetail = [
        ['title' => 'شناسه', 'value' => $customer->id],
        ['title' => 'نام', 'value' => $customer->first_name],
        ['title' => 'نام خانوادگی', 'value' => $customer->last_name],
        ['title' => 'موبایل', 'value' => $customer->mobile],
        ['title' => 'ایمیل', 'value' => $customer->email],
        ['title' => 'تاریخ تولد', 'value' => $customer->birth_date ? verta($customer->birth_date)->format('Y/m/d') : null],
        ['title' => 'جنسیت', 'value' => $genders[$customer->gender]],
        ['title' => 'موجودی کیف پول', 'value' => $customer->wallet ? number_format($customer->wallet->balance) . ' تومان' : 0],
      ];
    @endphp
    <div class="row">
      @foreach ($customerDetail as $detail)
        <div class="col-xl-3 my-1">
          <span class="font-weight-bold">{{ $detail['title'] }} : </span>
          <span>{{ $detail['value'] }}</span>
        </div>
      @endforeach
    </div>
  </x-slot>
</x-card>

<x-card>
  <x-slot name="cardTitle">اطلاعات دریافت کننده</x-slot>
  <x-slot name="cardOptions"><x-card-options /></x-slot>
  <x-slot name="cardBody">
    @php
      $address = json_decode($order->address);
      $receiverDetail = [
        ['title' => 'نام', 'value' => $address->first_name, 'col' => 'col-xl-3'],
        ['title' => 'نام خانوادگی', 'value' => $address->last_name, 'col' => 'col-xl-3'],
        ['title' => 'موبایل', 'value' => $address->mobile, 'col' => 'col-xl-3'],
        ['title' => 'کد پستی', 'value' => $address->postal_code, 'col' => 'col-xl-3'],
        ['title' => 'آدرس', 'value' => $address->address, 'col' => 'col-xl-12'],
      ];
    @endphp
    <div class="row">
      @foreach ($receiverDetail as $detail)
        <div class="{{ $detail['col'] }} my-1">
          <span class="font-weight-bold">{{ $detail['title'] }} : </span>
          <span>{{ $detail['value'] }}</span>
        </div>
      @endforeach
    </div>
  </x-slot>
</x-card>

<x-card>
  <x-slot name="cardTitle">اطلاعات پرداخت</x-slot>
  <x-slot name="cardOptions"><x-card-options /></x-slot>
  <x-slot name="cardBody">
    <x-table-component>
      <x-slot name="tableTh">
        <tr>
          <th>ردیف</th>
          <th>شناسه</th>
          <th>زمان</th>
          <th>کد رهگیری</th>
          <th>پرداختی از کیف پول</th>
          <th>پرداختی از درگاه</th>
          <th>مبلغ پرداختی</th>
        </tr>
      </x-slot>
      <x-slot name="tableTd">
        @forelse ($invoices = $order->invoices->filter(fn($invoice) => $invoice->status === 'success') as $invoice)
          <tr>
            <?php
              $data = [
                ['value' => $loop->iteration, 'hasColor' => false],
                ['value' => $invoice->id, 'hasColor' => false],
                ['value' => verta($invoice->created_at)->format('Y/m/d H:i'), 'hasColor' => false],
                ['value' => $invoice->payments->first()->tracking_code ?? '-', 'hasColor' => false],
                ['value' => $invoice->wallet_amount , 'hasColor' => true],
                ['value' => $invoice->amount - $invoice->wallet_amount, 'hasColor' => true],
                ['value' => $invoice->amount, 'hasColor' => true],
              ];
            ?>
            @foreach ($data as $d)
              <td>
                <span @if($d['hasColor']) class="{{ is_numeric($d['value']) && $d['value'] < 0 ? 'text-success' : 'text-dark' }}" @endif>
                  {{ is_numeric($d['value']) ? number_format(abs($d['value'])) : $d['value'] }}
                </span>
              </td>
            @endforeach
          </tr>
        @empty
          @include('core::includes.data-not-found-alert', ['colspan' => 9])
        @endforelse
      </x-slot>
    </x-table-component>
  </x-slot>
</x-card>

<x-card>
  <x-slot name="cardTitle">اقلام سفارش</x-slot>
  <x-slot name="cardOptions">
    <div class="card-options">
      <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#add-new-item-modal">افزودن قلم جدید</button>
    </div>
  </x-slot>
  <x-slot name="cardBody">

    <div class="col-xl-4 bg-warning rounded">
      <p class="text-dark py-2">برای تغییر وضعیت روی <b>وضعیت هر آیتم</b> کلیک کنید</p>
    </div>

    <x-table-component>
      <x-slot name="tableTh">
        <tr>
          <th>ردیف</th>
          <th>محصول</th>
          <th>وضعیت</th>
          <th>مبلغ واحد (تومان)</th>
          <th>تخفیف واحد (تومان)</th>
          <th>تعداد</th>
          <th>مبلغ کل (تومان)</th>
          <th>تخفیف کل (تومان)</th>
          <th>مبلغ با تخفیف (تومان)</th>
          <th>عملیات</th>
        </tr>
      </x-slot>
      <x-slot name="tableTd">
        @foreach($order->items as $item)
          <tr style="{{ !$item->status ? 'background-color: #e195a2' : null }}">
            <td class="font-weight-bold">{{ $loop->iteration }}</td>
            <td>{{ $item->variety->title }}</td>
            <td>
              <button
                data-item-id="{{ $item->id }}"
                data-update-status-url="{{ route('admin.orders.update-item-status', $item) }}"
                class="edit-item-status-button btn btn-sm btn-{{ $item->status ? 'success' : 'danger' }}">
                {{ $item->status ? 'فعال' : 'غیر فعال' }}
              </button>
            </td>
            <td>{{ number_format($item->amount) }}</td>
            <td>{{ number_format($item->discount_amount) }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->amount * $item->quantity) }}</td>
            <td>{{ number_format($item->discount_amount * $item->quantity) }}</td>
            <td>{{ number_format(($item->amount - $item->discount_amount) * $item->quantity) }}</td>
            <td>
              <button 
                data-item-id="{{ $item->id }}"
                data-update-quantity-url="{{ route('admin.orders.update-item-quantity', $item) }}"
                class="btn btn-sm btn-dark edit-item-quantity-button">
                ویرایش تعداد
              </button>
            </td>
          </tr>
        @endforeach
      </x-slot>
    </x-table-component>
  </x-slot>
</x-card>

<div style="margin-bottom: 200px"></div>

<div class="modal fade" id="edit-order-status-modal" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content modal-content-demo">
      <div class="modal-header">
        <p class="modal-title">تغییر وضعیت سفارش</p>
        <button aria-label="Close" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">

          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-12 my-1">
              <strong class="fs-15">وضعیت فعلی: </strong><span>{{ __('statuses.' . $order->status) }}</span>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-12">
              <div class="form-group">
                <select name="status" id="order_status" class="form-control" required>
                  <option value="">انتخاب وضعیت</option>
                  @foreach ($orderStatuses as $status)
                    @if ($status != $order->status)
                      <option value="{{ $status }}">{{ __('statuses.' . $status) }}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <textarea id="description" class="form-control" rows="2" name="description" placeholder="توضیحات">{{ old('description') }}</textarea>
              </div>
            </div>
          </div>

          <div class="modal-footer justify-content-center">
            <button class="btn btn-outline-warning" type="submit">ویرایش</button>
            <button class="btn btn-outline-danger" data-dismiss="modal">انصراف</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<x-modal id="add-new-item-modal" size="md">
  <x-slot name="title">افزودن به سفارش</x-slot>
  <x-slot name="body">
    <form action="{{ route('admin.orders.add-item', $order) }}" method="POST">

      @csrf

      <div class="col-12">
        <div class="form-group">
          <select name="product_id" class="form-control product-select-box" required>
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
          <input class="form-control" type="number" placeholder="تعداد" name="quantity" required/>
        </div>
      </div>

      <div class="modal-footer justify-content-center mt-2">
        <button class="btn btn-sm btn-info" type="submit">افزودن</button>
        <button class="btn btn-sm btn-outline-danger" data-dismiss="modal">انصراف</button>
      </div>

    </form>
  </x-slot>
</x-modal>

<x-modal id="edit-item-quantity-modal" size="md">
  <x-slot name="title">ویرایش تعداد آیتم سفارش</x-slot>
  <x-slot name="body">
    <form action="" method="POST">

      @csrf
      @method('PUT')

      <div class="col-12 mb-2">
        <span>تعداد فعلی : </span>
        <b class="old-quantity"></b>
      </div>

      <div class="col-12">
        <div class="form-group">
          <input class="form-control" type="number" placeholder="تعداد جدید را وارد کنید" name="quantity" required/>
        </div>
      </div>

      <div class="col-12">
        <div class="row justify-content-center" style="gap: 8px">
          <button class="btn btn-sm btn-warning" type="submit">بروزرسانی تعداد</button>
          <button class="btn btn-sm btn-outline-danger" data-dismiss="modal">انصراف</button>
        </div>
      </div>

    </form>
  </x-slot>
</x-modal>

<x-modal id="edit-order-modal" size="md">
  <x-slot name="title">ویرایش سفارش</x-slot>
  <x-slot name="body">
    <form action="{{ route('admin.orders.update', $order) }}" method="POST">

      @csrf
      @method('PUT')

      <div class="col-12">
        <div class="form-group">
          <label class="font-weight-bold">آدرس : <span class="text-danger">&starf;</span></label>
          <select id="address-select-box" name="address_id" class="form-control">
            <option value=""></option>
            @php
              $addressId = $order->address_id ?? json_decode($order->address)->id;
            @endphp
            @foreach ($addresses ?? [] as $address)
              <option value="{{ $address->id }}" @if($addressId == $address->id) selected @endif>{{ $address->address }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-12">
        <div class="form-group">
          <label class="font-weight-bold">حمل و نقل : <span class="text-danger">&starf;</span></label>
          <select id="shipping-select-box" name="shipping_id" class="form-control">
            <option value=""></option>
            <option value="{{ $order->shipping_id }}" selected>{{ $order->shipping->name }}</option>
          </select>
        </div>
      </div>

      <div class="col-12">
        <div class="form-group">
          <label class="font-weight-bold">تخفیف روی سفارش (تومان) : <span class="text-danger">&starf;</span></label>
          <input type="text" name="discount_amount" class="form-control comma" value="{{ $order->discount_amount ? number_format($order->discount_amount) : null }}">
        </div>
      </div>
      
      <div class="col-12">
        <div class="form-group">
          <label class="font-weight-bold">توضیحات : <span class="text-danger">&starf;</span></label>
          <textarea name="description" class="form-control" rows="2">{{ $order->description }}</textarea>
        </div>
      </div>

      <div class="modal-footer justify-content-center mt-2">
        <button class="btn btn-sm btn-warning" type="submit">بروزرسانی اطلاعات</button>
        <button class="btn btn-sm btn-outline-danger" data-dismiss="modal">انصراف</button>
      </div>

    </form>
  </x-slot>
</x-modal>

<form 
  id="update-item-status-form"
  class="d-none"
  action=""
  method="POST">
  @csrf
  @method('PUT')
  <input hidden name="status" value="" required>
</form>

@endsection

@section('scripts')
  <script>

    $('#address-select-box').select2({placeholder: 'آدرس جدید را انخاب کنید'});
    $('#shipping-select-box').select2({placeholder: 'ابتدا آدرس جدید انتخاب کنید'});
    $('#order_status').select2({placeholder: 'وضعیت جدید را انتخاب کنید'});
    $('.variety-select-box').select2({ placeholder: 'ابتدا محصول را جستجو کنید' });

    function loadShippingsFromAddress() {

      const addressSelectBox = $('#address-select-box');
      const shippingSelectBox = $('#shipping-select-box');
      const customerId = @json($order->customer_id);
      const oldShippingId = @json($order->shipping_id);

      const data = {
        address_id: addressSelectBox.val(),
        customer_id: customerId
      };

      const headers = {
        'X-CSRF-TOKEN': @json(csrf_token())
      };

      $.ajax({  
        url: @json(route('admin.orders.shippable-shippings')),  
        type: 'POST',  
        data: data,  
        headers: headers,  
        success: (response) => {  
          const shippings = response.data.shippings;  
          shippingSelectBox.empty();  
          shippings.forEach(shipping => {  
            let option = $(`<option value="${shipping.id}" data-amount="${shipping.amount_showcase}"></option>`);  
            option.text(shipping.name);  
            if (shipping.id == oldShippingId) {  
              option.attr('selected', 'selected');  
            }  
            shippingSelectBox.append(option);  
          });  
        }  
      });  

      addressSelectBox.change(() => {

        if (addressSelectBox.val() === null) {
          Swal.fire ({
            text: 'انتخاب آدرس الزامی است',
            icon: 'warning',
            confirmButtonText: 'بستن',
          });
          return;
        }

        $.ajax({
          url: @json(route('admin.orders.shippable-shippings')),
          type: 'POST',
          data: data,
          headers: headers,
          success: (response) => {

            const shippings = response.data.shippings;

            if (shippings.length < 1) {
              Swal.fire ({
                text: 'حمل و نقل مناسبی برای این آدرس پیدا نشده است',
                icon: 'warning',
                confirmButtonText: 'بستن',
              });
              return;
            }

            shippingSelectBox.empty();
            shippingSelectBox.append('<option value=""></option>');
            shippingSelectBox.select2({placeholder: 'نوع حمل و نقل را انتخاب کنید'});

            shippings.forEach(shipping => {
              let option = $('<option value="" data-amount=""></option>');
              option.attr('value', shipping.id).attr('data-amount', shipping.amount_showcase).text(shipping.name);
              shippingSelectBox.append(option);
            });
          },
          error: (error) => {
            Swal.fire ({
              text: 'خطا دربارگذاری سرویس های حمل و نقل',
              icon: 'error',
              confirmButtonText: 'بستن',
            });
          }
        });

      });
    }

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


								varietySelectBox.empty();
								let options = '<option value="">انتخاب</option>';
								response.varieties.forEach((variety) => {
									options += `<option value="${variety.id}">${variety.title}</option>`;
								});
								varietySelectBox.append(options);
								varietySelectBox.select2({ placeholder: 'تنوع را انتخاب کنید' });

							}
						}
					});
				});
			});
		}

    function updateOrderItemStatus() {

      const orderItems = @json($order->items);
      const editItemStatusModal = $('#edit-item-status-modal');
      const updateStatusForm = $('#update-item-status-form');

      $('.edit-item-status-button').each(function () {

        $(this).click(() => {

          let orderItemId = $(this).data('item-id');
          let orderItem = orderItems.find(o => o.id == orderItemId);
          let newStatus = !orderItem.status;
          let updateUrl = $(this).data('update-status-url');

          Swal.fire ({  
            text: 'آیا تمایل دارید وضعیت آیتم را تغییر دهید',
            icon: "warning",  
            confirmButtonText: 'تغییر وضعیت',  
            showDenyButton: true,  
            denyButtonText: 'انصراف',  
            dangerMode: true,  
          }).then((result) => {  
            if (result.isConfirmed) {  
              updateStatusForm.find('input[name=status]').val(newStatus);
              updateStatusForm.attr('action', updateUrl);
              updateStatusForm.submit();
            } 
          });

        });

      }); 
    }

    function updateOrderItemQuantity() {

      const updateQuantityModal = $('#edit-item-quantity-modal');
      const updateQuantityForm = updateQuantityModal.find('form');

      $('.edit-item-quantity-button').each(function () {
        $(this).click(() => {

          const orderItem = @json($order->items).find(oi => oi.id == $(this).data('item-id'));
          const updateUrl = $(this).data('update-quantity-url');

          updateQuantityForm.find('.old-quantity').text(orderItem.quantity);
          updateQuantityForm.find('input[name=variety_id]').val(orderItem.variety_id);
          updateQuantityForm.attr('action', updateUrl);

          updateQuantityModal.modal('show');
        });
      });
    }

    $(document).ready(() => {
			searchProducts();
			searchVarieties();
      updateOrderItemStatus();
      updateOrderItemQuantity();
      loadShippingsFromAddress();
    });

  </script>
@endsection
