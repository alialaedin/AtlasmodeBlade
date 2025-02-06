@extends('admin.layouts.master')

@section('styles')
  <style>
    .oneLine {
      position: relative;
    }

    .full-title {
      display: none;
      opacity: 0;
      visibility: hidden;
      position: absolute;
      background-color: #000000;
      color: #FFFFFF;
      border: 1px solid #ccc;
      padding: 5px;
      z-index: 10;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      border-radius: 4px;
      width: 200px;
      white-space: normal;
      transition: opacity 0.5s ease, visibility 0.5s ease;
    }

    .oneLine:hover .full-title {
      display: block;
      opacity: 1;
      visibility: visible;
      transition-delay: 1s;
    }

    .short-title {
      display: inline;
    }

  </style>
@endsection

@section('content')

  <div class="page-header">
    <x-breadcrumb :items="[['title' => 'لیست محصولات']]" />
    @can('write_product')
      <x-create-button route="admin.products.create" title="محصول جدید" />
    @endcan
  </div>

  <x-card>
    <x-slot name="cardTitle">جستجوی پیشرفته</x-slot>
    <x-slot name="cardOptions"><x-card-options /></x-slot>
    <x-slot name="cardBody">
      <form id="filter-form" action="{{ route('admin.products.index') }}" method="GET">
        <div class="row">
          <div class="col-xl-3 col-lg-6 col-12">
            <div class="form-group">
              <label for="filter-by-product-id-input">شناسه محصول</label>
              <input id="filter-by-product-id-input" type="text" name="id" value="{{ request('id') }}" class="form-control">
            </div>
          </div>  
          <div class="col-xl-3 col-lg-6 col-12">
            <div class="form-group">
              <label for="filter-by-product-title-input">عنوان محصول</label>
              <input id="filter-by-product-title-input" type="text" name="title" value="{{ request('title') }}" class="form-control">
            </div>
          </div>  
          <div class="col-xl-3 col-lg-6 col-12">
            <div class="form-group">
              <label for="filter-by-category-select-box">دسته بندی</label>
              <select class="form-control" name="category_id" id="filter-by-category-select-box">
                <option value="">انتخاب</option>
                @foreach ($categories as $category)
                  <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : null }}>
                    {{ $category->title }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>  
          <div class="col-xl-3 col-lg-6 col-12">
            <div class="form-group">
              <label for="filter-by-status-select-">وضعیت</label>
              <select class="form-control" name="status" id="filter-by-status-select-box">
                <option value="">انتخاب</option>
                <option value="all">همه</option>
                @foreach ($statuses as $status)
                  <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : null }}>
                    {{ config('product.prdocutStatusLabels.' . $status) }}
                  </option>
                @endforeach
              </select>
            </div>
          </div> 
          <div class="col-xl-3 col-lg-6 col-12">
            <div class="form-group">
              <label for="filter-by-approval-status-select-box">وضعیت تایید</label>
              <select class="form-control" name="is_approved" id="filter-by-approval-status-select-box">
                <option value="">انتخاب</option>
                <option value="all" {{ request('is_approved') == 'all' ? 'selected' : null }}>همه</option>
                <option value="1" {{ request('is_approved') == '1' ? 'selected' : null }}>تایید شده</option>
                <option value="0" {{ request('is_approved') == '0' ? 'selected' : null }}>تایید نشده</option>
              </select>
            </div>
          </div>  
          <div class="col-xl-3 col-lg-6 col-12">
            <div class="form-group">
              <label for="paginate">صفحه بندی</label>
              <select class="form-control" name="per_page" id="paginate">
                <option value="">انتخاب</option>
                @foreach ([15, 20, 25, 30, 40, 50, 60, 75, 90, 100] as $paginate)
                  <option value="{{ $paginate }}" {{ request('per_page') == $paginate ? 'selected' : '' }} >
                    {{ $paginate }}
                  </option>
                @endforeach
              </select>
            </div>
          </div> 
          <div class="col-xl-3 col-lg-6 col-12">
            <div class="form-group">
              <label for="start_date_show">از تاریخ</label>
              <input class="form-control fc-datepicker" id="start_date_show" type="text" autocomplete="off" />
              <input name="start_date" id="start_date_hide" type="hidden" value="{{ request('start_date') }}" />
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-12">
            <div class="form-group">
              <label for="end_date_show">تا تاریخ</label>
              <input class="form-control fc-datepicker" id="end_date_show" type="text" autocomplete="off" />
              <input name="end_date" id="end_date_hide" type="hidden" value="{{ request('end_date') }}" />
            </div>
          </div> 
        </div>
        <div class="row">
          <div class="col-xl-9 col-lg-8 col-md-6 col-12">
            <button class="btn btn-primary btn-block" type="submit">جستجو <i class="fa fa-search"></i></button>
          </div>
          <div class="col-xl-3 col-lg-4 col-md-6 col-12">
            <a href="{{ route('admin.products.index') }}" class="btn btn-danger btn-block">
              حذف همه فیلتر ها <i class="fa fa-close"></i>
            </a>
          </div>
        </div>
      </form>
    </x-slot>
  </x-card> 

  <x-card>
    <x-slot name="cardTitle">لیست محصولات</x-slot>
    <x-slot name="cardBody">
      
      <div class="rwo mb-3">
        <a
          href="{{ route('admin.products.index', ['status' => 'all']) }}" 
          class="fs-13 btn btn-sm btn-dark">
          همه ( {{ $countAllProducts }} )
        </a>
				@foreach ($statusCounts as $status => $count)
					<a
            href="{{ route('admin.products.index', ['status' => $status]) }}" 
            class="fs-13 btn btn-sm btn-{{ config('product.prdocutStatusColors.' . $status) }}">
						{{ config('product.prdocutStatusLabels.' . $status) }} ( {{ $count }} )
					</a>
				@endforeach
			</div>

      <x-table-component>
        <x-slot name="tableTh">
          <tr>
            <th>ردیف</th>
            <th>شناسه</th>
            <th>عنوان</th>
            <th>قیمت (تومان)</th>
            <th>موجودی</th>
            <th>دسته بندی ها</th>
            <th>وضعیت</th>
            <th>وضعیت تایید</th>
            <th>تاریخ ثبت</th>
            <th>عملیات</th>
          </tr>
        </x-slot>
        <x-slot name="tableTd">
          @forelse($products as $product)
            <tr>
              <td class="font-weight-bold">{{ $loop->iteration }}</td>
              <td>{{ $product->id }}</td>
              <td class="oneLine">
                <span class="short-title">{{ Str::limit($product->title, 30) }}</span>
                @if (Str::length($product->title) > 30)
                  <span class="full-title">{{ $product->title }}</span>
                @endif
              </td>
              <td>{{ number_format($product->varieties->min('price')) }}</td>
              <td>{{ $product->total_quantity }}</td>
              <td>
                <span
                  class="btn btn-outline-secondary btn-sm" 
                  data-toggle="tooltip" 
                  data-original-title="{{ $product->categories->pluck('title')->join(' - ') }}" 
                  style="cursor: pointer;">مشاهده
                </span>
              </td>
              <td>
                <x-badge>
                  <x-slot name="type">{{ config('product.prdocutStatusColors.' . $product->status) }}</x-slot>
                  <x-slot name="text">{{ config('product.prdocutStatusLabels.' . $product->status) }}</x-slot>
                </x-badge>
              <td>
                @php
                  $route = $product->approved_at ? 'admin.products.disapprove' : 'admin.products.approve';
                @endphp
                <form action="{{ route($route, $product) }}" method="POST">
                  @csrf
                  <button class="btn btn-sm">
                    @if ($product->approved_at)
                      <i class="text-success fs-26 fa fa-check-circle-o"></i>
                    @else
                      <i class="text-danger fs-26 fa fa-close"></i>
                    @endif
                  </button>
                </form>
              </td>
              <td style="direction: ltr">{{ verta($product->created_at)->format('Y/m/d H:i') }}</td>
              <td>
                @include('core::includes.edit-icon-button', [
                  'model' => $product,
                  'route' => 'admin.products.edit',
                ])
                @include('core::includes.delete-icon-button', [
                  'model' => $product,
                  'route' => 'admin.products.destroy',
                ])
                <a
                  target="_blank"
                  style="padding: 3.5px 7px"
                  class="btn btn-sm btn-purple btn-icon text-white"
                  data-toggle="tooltip"
                  data-original-title="پرینت">
                  <i class="fe fe-printer" style="font-size: 12px;"></i>
                </a>
              </td>
            </tr>
          @empty
            @include('core::includes.data-not-found-alert', ['colspan' => 12])
          @endforelse
        </x-slot>
        <x-slot name="extraData">
          <div class="mb-2">{{ $products->onEachSide(0)->links('vendor.pagination.bootstrap-4') }}</div>
        </x-slot>
      </x-table-component>
    </x-slot>
  </x-card>

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
    $('#filter-by-category-select-box').select2({
      placeholder: 'انتخاب دسته بندی'
    });
    $('#filter-by-status-select-box').select2({
      placeholder: 'انتخاب وضعیت'
    });
    $('#paginate').select2({
      placeholder: 'تعداد داده ها در هر صفحه'
    });
    $('#filter-by-approval-status-select-box').select2({
      placeholder: 'انتخاب وضعیت تایید'
    });
    $(document).ready(() => {
      $('#paginate').change(() => {
        $('#filter-form').submit();
      });
    });
  </script>
@endsection
