@extends('front-layouts.master')

@section('content')
  <main class="main-products">

    <!-- Page Path -->
    <div class="bg-white">
      <div class="page-path container-2xl px-4 px-md-8 px-3xl-0 py-1 d-flex gap-1 align-items-center">
        <i class="icon-home1 icon-fs-medium-2"></i>
        <a href="/" class="text-button-1 mt-1">خانه</a>
        <i class="icon-angle-double-left icon-fs-medium"></i>
        <a href="{{ url('/products') }}" class="text-button-1 mt-1">
          لیست محصولات
        </a>
      </div>
    </div>

    <form id="filter-form" action="{{ route('front.products.index') }}" method="GET" class="d-none">
      <input hidden name="category_id" value="{{ request('category_id') }}">
      <input hidden name="attribute_value_id" value="{{ request('attribute_value_id') }}">
      <input hidden name="sort" value="{{ request('sort') }}" />
      <input hidden name="title" value="{{ request('title') }}" />
      <input hidden name="available" value="{{ request('available') }}" />
      <input hidden name="min_price" value="{{ request('min_price') }}" />
      <input hidden name="max_price" value="{{ request('max_price') }}" />
    </form>

    <section class="container-2xl d-flex px-4 px-md-8 px-3xl-0 gap-3 mt-12 pb-12">

      <aside class="aside d-lg-flex d-none flex-column align-items-center">

        <!-- Title -->
        <div class="w-p-100 d-flex gap-1 justify-content-center color-gray-900 pb-2 border-b-gray-300">
          <i class="icon-filter icon-fs-medium-2"></i>
          <span class="text-medium-2-strong">فیلتر جستجو</span>
        </div>

        <!-- Categories -->
        <nav class="category w-p-100 my-2 d-flex flex-column gap-1">
          <span class="text-medium-2-strong color-gray-900">دسته بندی ها</span>
          <ul class="category-lists d-flex flex-column py-1">
            <button 
              data-category-id=""
              class="title d-flex align-items-center gap-1 text-medium color-primary-700 category-filter-item">
              <i class="icon-dot-single icon-fs-medium"></i>
              <span>همه دسته بندی ها</span>
            </button>
            @foreach ($categories as $parentCategory)
              @if ($parentCategory->children->isEmpty())
                <li class="category-lists-item-main pointer py-1">
                  <button 
                    data-category-id="{{ $parentCategory->id }}"
                    class="d-flex align-items-center gap-1 text-medium color-gray-700 category-filter-item">
                    <i class="icon-dot-single icon-fs-medium color-primary-700"></i>
                    <span>{{ $parentCategory->title }}</span>
                  </button>
                </li>
              @else
                <li class="d-flex flex-column">
                  <div class="category-lists-item-main d-flex py-1 justify-content-between pointer">
                    <button 
                      data-category-id="{{ $parentCategory->id }}"
                      class="category-item text-medium d-flex align-items-center gap-1 color-gray-700 category-filter-item">
                      <i class="icon-dot-single icon-fs-medium color-primary-700"></i>
                      <span>{{ $parentCategory->title }}</span>
                    </button>
                    <button type="button" class="show-subList">
                      <i class="icon-plus color-gray-700 icon-fs-medium"></i>
                      <i class="icon-minus color-gray-700 icon-fs-medium"></i>
                    </button>
                  </div>
                  <ul class="category-sublist mt-1 px-2">
                    @foreach ($parentCategory->children as $childCategory)
                      @if ($childCategory->children->isEmpty())
                        <li class="category-sublist-item pointer mb-1">
                          <button
                            data-category-id="{{ $childCategory->id }}"
                            class="d-flex align-items-center text-medium color-gray-700 category-filter-item">
                            <i class="icon-dot-single icon-fs-medium color-primary-700"></i>
                            <span>{{ $childCategory->title }}</span>
                          </button>
                        </li>
                      @else
                        <li class="category-sublist-item d-flex flex-column gap-1 pointer mb-1">
                          <div class="d-flex justify-content-between">
                            <button
                              data-category-id="{{ $childCategory->id }}"
                              class="d-flex align-items-center text-medium color-gray-700 category-filter-item">
                              <i class="icon-dot-single icon-fs-medium color-primary-700"></i>
                              <span>{{ $childCategory->title }}</span>
                            </button>
                            <button type="button" class="show-subList-child">
                              <i class="icon-plus color-gray-700 icon-fs-small"></i>
                              <i class="icon-minus color-gray-700 icon-fs-small"></i>
                            </button>
                          </div>
                          <ul class="category-sublist-child mt-1 px-2">
                            @foreach ($childCategory->children as $grandChildCategory)
                              <li class="category-sublist-item pointer mb-1">
                                <button
                                  data-category-id="{{ $grandChildCategory->id }}" 
                                  class="d-flex align-items-center text-button-1 color-gray-700 category-filter-item">
                                  <i class="icon-dot-single icon-fs-medium color-primary-700"></i>
                                  <span>{{ $grandChildCategory->title }}</span>
                                </button>
                              </li>
                            @endforeach
                          </ul>
                        </li>
                      @endif
                    @endforeach
                  </ul>
                </li>
              @endif
            @endforeach
          </ul>
        </nav>

        <!-- Title -->
        <div class="title w-p-100 d-flex flex-column gap-4 px-2 pt-3 pb-6">
          <span class="text-medium-2-strong color-gray-900">عنوان</span>
          <input type="text" class="title-input text-medium border-gray-300 w-p-100 px-4 py-1" placeholder="عنوان جستجو را بنویسید" value="{{ request('title') }}">
        </div>

        <!-- Size -->
        {{-- <div class="size w-p-100 d-flex flex-column gap-2 px-2 pt-3 pb-3">
          <!-- Title -->
          <span class="text-medium-2-strong color-gray-900 pb-2 border-b-gray-300">سایز</span>
          <!-- Size Lists -->
          <div class="w-p-100 size-list grid gap-2 mt-3">
            @php
              $attributeValueIdsArray = json_decode(request('attribute_value_id', '')) ?? [];
            @endphp
            @foreach ($sizeValues->values as $sizeValue)
              <label class="d-flex gap-1 g-col-3">
                <input 
                  type="checkbox" 
                  {{ in_array($sizeValue->id, $attributeValueIdsArray) ? 'checked' : '' }}
                  value="{{ $sizeValue->id }}" 
                  class="size-list-item bg-white customCheckbox radius-medium text-center text-medium" 
                />
                <span>{{ Str::limit($sizeValue->value, 4) }}</span>
              </label>
            @endforeach
          </div>
        </div> --}}

        <!-- Available Mode -->
        <div class="available w-p-100 d-flex flex-column gap-4 radius-medium px-2 pt-3 pb-3">
          <span class="text-medium-2-strong pb-2 color-gray-900 border-b-gray-300">حالت نمایش</span>
          <div class="d-flex gap-3 align-items-center">
            <input 
              type="checkbox" 
              id="available"
              {{ request('available') == '1' ? 'checked' : '' }} 
              class="available-btn customSwitch bg-gray-400" 
            />
            <label class="text-medium color-gray-900" name="available">فقط کالاهای موجود</label>
          </div>
        </div>

        <!-- Price Range -->
        {{-- <div class="price-range w-p-100 d-flex flex-column gap-3 px-2 pt-4 pb-3">
          <span class="text-medium-2-strong color-gray-900">قیمت ها</span>
          <form class="range-input position-relative">
            <div class="slider">
              <div class="progress"></div>
            </div>
            <input type="range" class="range-min pointer" min="{{ $priceFilter['min_price'] }}" max="{{ $priceFilter['max_price'] }}" value="{{ $priceFilter['min_price'] }}" step="10000">
            <input type="range" class="range-max pointer" min="{{ $priceFilter['min_price'] }}" max="{{ $priceFilter['max_price'] }}" value="{{ $priceFilter['max_price'] }}" step="10000">
          </form>
          <div class="price-text d-flex justify-content-between mt-2">
            <div class="d-flex gap-1 color-gray-900 text-button-1">
              <span>از قیمت:</span>
              <span class="price-min currency">{{ $priceFilter['min_price'] }}</span>
              <span>تومان</span>
            </div>
            <div class="d-flex gap-1 color-gray-900 text-button-1">
              <span>تا:</span>
              <span class="price-max currency">{{ $priceFilter['max_price'] }}</span>
              <span>تومان</span>
            </div>
          </div>
        </div> --}}

        <!-- Set Filters Button -->
        <button type="button" class="setFilter-btn w-p-100 bg-black color-white  py-1 text-medium mt-2">اعمال فیلتر</button>

      </aside>

      <div class="products d-flex flex-column pe-lg-3 gap-4">

        <!-- Display Sort -->
        <div class="display-sort align-items-center d-flex flex-wrap justify-content-between">
          <div class="d-lg-flex d-none align-items-center gap-2">
            <button type="button" class="two-cloumn px-2 py-1 border-gray-300 d-flex">
              <span class="bg-gray-400"></span>
              <span class="bg-gray-400"></span>
            </button>
            <button type="button" class="three-cloumn select px-2 py-1 border-gray-300 d-flex">
              <span class="bg-gray-400"></span>
              <span class="bg-gray-400"></span>
              <span class="bg-gray-400"></span>
            </button>
            <!-- Available Input -->
            <div class="d-flex gap-1 align-items-center">
              <input 
                type="checkbox" 
                id="available" 
                class="available-btn2 customCheckbox" 
                {{ request('available') == '1' ? 'checked': '' }}
              />
              <label class="text-button-1 color-gray-900 mt-1" name="available">نمایش محصولات موجود</label>
            </div> 
          </div>
          <button type="button" data-modal="filter" class="d-lg-none d-flex gap-1 text-medium-strong color-gray-900">
            <i class="icon-filter icon-fs-medium"></i>
            <span>فیلتر جستجو</span>
          </button>
          <!-- Sorting Display -->
          <div>
            <span class="text-medium">فیلتر :</span>
            <select id="sort-select" class="select-sorting p-1 border-gray-300 text-button-1">
              @foreach ($sortTypes as $name => $label)
                <option value="{{ $name }}" {{ request('sort', 'newest') == $name ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Products Carts -->
        <div class="products-carts grid gap-md-4 gap-2 mx-lg-0 mx-auto">
          @foreach ($products as $product)
            <div class="g-col-md-4 g-col-6">
              <article class="product-cart">
                <a href="{{ route('front.products.show', $product) }}" class="bg-gray-100 d-flex flex-column overflow-hidden position-relative">
                  <!-- Hover Buttons -->
                  <div class="hover-buttons d-flex flex-column gap-2 justify-content-center position-absolute">
                    <button type="button" class="d-flex flex-column gap-1">
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                    </button>
                    <button type="button" class="like-btn">
                      <i class="icon-heart icon-fs-medium-2"></i>
                      <svg class="heart-red" width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-v-10f8db86=""><path fill-rule="evenodd" clip-rule="evenodd" d="M15.8498 2.50071C16.4808 2.50071 17.1108 2.58971 17.7098 2.79071C21.4008 3.99071 22.7308 8.04071 21.6198 11.5807C20.9898 13.3897 19.9598 15.0407 18.6108 16.3897C16.6798 18.2597 14.5608 19.9197 12.2798 21.3497L12.0298 21.5007L11.7698 21.3397C9.4808 19.9197 7.3498 18.2597 5.4008 16.3797C4.0608 15.0307 3.0298 13.3897 2.3898 11.5807C1.2598 8.04071 2.5898 3.99071 6.3208 2.76971C6.6108 2.66971 6.9098 2.59971 7.2098 2.56071H7.3298C7.6108 2.51971 7.8898 2.50071 8.1698 2.50071H8.2798C8.9098 2.51971 9.5198 2.62971 10.1108 2.83071H10.1698C10.2098 2.84971 10.2398 2.87071 10.2598 2.88971C10.4808 2.96071 10.6898 3.04071 10.8898 3.15071L11.2698 3.32071C11.3616 3.36968 11.4647 3.44451 11.5538 3.50918C11.6102 3.55015 11.661 3.58705 11.6998 3.61071C11.7161 3.62034 11.7327 3.63002 11.7494 3.63978C11.8352 3.68983 11.9245 3.74197 11.9998 3.79971C13.1108 2.95071 14.4598 2.49071 15.8498 2.50071ZM18.5098 9.70071C18.9198 9.68971 19.2698 9.36071 19.2998 8.93971V8.82071C19.3298 7.41971 18.4808 6.15071 17.1898 5.66071C16.7798 5.51971 16.3298 5.74071 16.1798 6.16071C16.0398 6.58071 16.2598 7.04071 16.6798 7.18971C17.3208 7.42971 17.7498 8.06071 17.7498 8.75971V8.79071C17.7308 9.01971 17.7998 9.24071 17.9398 9.41071C18.0798 9.58071 18.2898 9.67971 18.5098 9.70071Z" data-v-10f8db86="" fill="#ee1212"></path></svg>
                    </button>
                  </div>
                  <!-- Img -->
                  <figure class="product-img overflow-hidden position-relative">
                    @php
                      $imageUrl = $product->main_image ? '/storage/' . $product->main_image->uuid . '/' . $product->main_image->file_name : '';
                    @endphp
                    <img class="main-img w-p-100 h-p-100" loading="lazy" src="{{ $imageUrl }}" alt="">
                    <img class="hover-img w-p-100 h-p-100 hidden position-absolute top-0 start-0" loading="lazy" src="{{ $imageUrl }}" alt="">
                    <button type="button" class="see-more-product text-nowrap text-center position-absolute bg-white radius-small ps-2 pe-1 py-1 text-medium">مشاهده بیشتر</button>
                  </figure>
                  <div class="product-details d-flex flex-column px-2 mt-2">
                    <!-- Title -->
                    <h5 class="text-medium-2-strong color-gray-900 text-truncate">{{ $product->title }}</h5> 
                    <div class="d-flex flex-wrap gap-lg-1 align-items-center">
                      <!-- Price -->
                      <div class="d-flex gap-1 align-items-center">
                        <ins class="currency text-medium-2 color-primary-500">{{ $product->final_price['amount'] }}</ins>
                        <span class="text-medium color-gray-800">تومان</span>
                      </div>
                      @if ($product->final_price['discount'])
                        <!-- Discount Price -->
                        <div class="d-flex align-items-center color-gray-700">
                          <i class="icon-angle-double-right icon-fs-small pb-1"></i>
                          <s class="text-medium currency">{{ $product->final_price['base_amount'] }}</s>
                        </div>
                        <!-- Discount Percent -->
                        <span class="px-2 radius-u text-button-1 bg-secondary-100">
                          {{ number_format($product->final_price['discount']) }}
                          {{ $product->final_price['discount_type'] == 'flat' ? 'تومان' : '%' }}
                        </span> 
                      @endif
                      <div></div>
                    </div>
                  </div>
                </a>
              </article>
            </div>
          @endforeach
        </div>

        {{ $products->onEachSide(0)->links('vendor.pagination.front-product-index') }}

      </div>

    </section>

    @include('front-layouts.includes.mobile-menu')

  </main>
@endsection

@section('modals')
  <div class="modal modal-mobile-filter bg-white d-flex flex-column gap-2 pb-lg-4 overflow-auto" data-id="filter">

    <div class="d-flex position-sticky bg-white top-0 start-0 end-0 pb-2 pt-3 px-5 justify-content-between align-items-center">
      <button type="button" class="modal-close">
      <i class="icon-cancel icon-fs-medium-2 color-gray-700"></i>
      </button>
      <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-center">
        <i class="icon-filter icon-fs-medium"></i>
        <span class="text-medium-2-strong color-gray-900">فیلتر جستوجو</span>
      </div>
    </div>

    <div class="d-flex flex-column gap-1 bg-gray-100 px-3 pb-2">
      <!-- Title -->
      <form class="title w-p-100 d-flex flex-column gap-4 px-2 pt-3 pb-6">
        <span class="text-medium-2-strong color-gray-900">عنوان</span>
        <input type="text" class="title-input text-medium border-gray-300 w-p-100 px-4 py-1" placeholder="عنوان جستجو را بنویسید">
      </form>
      <!-- Size -->
      <div class="size w-p-100 d-flex flex-column gap-4 px-2 pt-3 pb-3">
          <!-- Title -->
          <span class="text-medium-2-strong color-gray-900 pb-2 border-b-gray-300">سایز</span>
            <!-- Size Lists -->
            <form class="w-p-100 size-list grid gap-2 mt-3">
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span> 5XL</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span> 4XL</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span> 3XL</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span> 2XL</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span> XL</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span> L</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span> M</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span>S</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span>XS</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span>36</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span>38</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span>40</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span>42</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span> 5XL</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span> 4XL</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span> 3XL</span>
              </label>
              <label for="" class="d-flex gap-1 g-col-3">
                <input  type="checkbox" class="size-list-item customCheckbox radius-medium text-center text-medium" />
                <span> 2XL</span>
              </label>
            </form>
      </div>
      <!-- Available Mode -->
      <div class="available w-p-100 d-flex flex-column gap-4 radius-medium px-2 pt-3 pb-3">
        <span class="text-medium-2-strong pb-2 color-gray-900 border-b-gray-300">حالت نمایش</span>
        <div class="d-flex gap-3 align-items-center">
          <!-- Available input -->
          <input type="checkbox" id="available" class="available-btn customSwitch bg-gray-400" />
          <label class="text-medium color-gray-900" name="available">فقط کالاهای موجود</label>
        </div>
      </div>
        <!-- Price Range -->
      <div class="price-range2 w-p-100 d-flex flex-column gap-3 px-4 pt-5 pb-3">
        <span class="text-medium-2-strong color-gray-900">قیمت ها</span>
        <div class="range-input2 position-relative">
          <div class="slider2">
            <div class="progress2"></div>
          </div>
          <input type="range" class="range-min2 pointer" min="10000" max="2000000" value="10000" step="10000">
          <input type="range" class="range-max2 pointer" min="10000" max="2000000" value="2000000" step="10000">
        </div>
        <div class="price-text2 d-flex justify-content-between mt-2">
          <div class="d-flex gap-1 color-gray-900 text-button-1">
            <span>از قیمت:</span>
            <span class="price-min currency">10000</span>
            <span>تومان</span>
          </div>
          <div class="d-flex gap-1 color-gray-900 text-button-1">
            <span>تا:</span>
            <span class="price-max currency">2000000</span>
            <span>تومان</span>
          </div>
        </div>
      </div>
      <button type="button" class="setFilter-btn w-p-100 bg-black color-white py-1 text-medium mt-2">اعمال فیلتر</button>
    </div>

  </div>
@endsection

@section('scripts')
  <script>
    productsPage();
    hoverImg();
  </script> 

<script>

  const submitFilterForm = () => $('#filter-form').submit();
  const appendNewFilter = (inputName, value) => $('#filter-form').find(`input[name=${inputName}]`).attr('value', value);

  function filterByCategoryId() {
    $('.category-filter-item').each(function () {
      $(this).click(() => {
        const categoryId = $(this).data('category-id') ?? null;
        appendNewFilter('category_id', categoryId);
        submitFilterForm();
      });
    });
  }

  function sortSystem() {
    $('#sort-select').change(function() {
      appendNewFilter('sort', $(this).val());
      submitFilterForm();
    });
  } 

  function handleFiltringWhenSetFilterClicked() {
    $('.setFilter-btn').click(() => {
      filterBySize();
      filterByTitle();
      filterByStoreBalance();
      filterByPrice();
      submitFilterForm();
    });
  }

  function filterBySize() {
    const sizeAttributeValueIds = [];
    $('.size-list-item').each(function() {
      if ($(this).is(':checked')) {
        sizeAttributeValueIds.push($(this).val());
      }
    });
    if (sizeAttributeValueIds.length < 1) {
      appendNewFilter('attribute_value_id', '');
    }else {
      appendNewFilter('attribute_value_id', JSON.stringify(sizeAttributeValueIds));
    }
  }

  function filterByTitle() {
    const title = $('.title-input').val()?.trim();
    appendNewFilter('title', title);
  }

  function filterByStoreBalance() {
    const isAvailableChecked = $('.available-btn').is(':checked') ? 1 : '';
    appendNewFilter('available', isAvailableChecked);
  }

  function filterByPrice() {
    // appendNewFilter('min_price', $('.range-min').val());
    // appendNewFilter('max_price', $('.range-max').val());
  }

  function showAvailableProducts() {
    $('.available-btn2').change(function() {
      console.log(1);
      const available = $(this).is(':checked') ? 1 : 0;
      appendNewFilter('available', available);
      submitFilterForm();
    });
  }

  $(document).ready(() => {
    filterByCategoryId();
    sortSystem();
    showAvailableProducts();
    handleFiltringWhenSetFilterClicked();
  });

</script>

@endsection
