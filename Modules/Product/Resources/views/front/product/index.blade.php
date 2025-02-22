@extends('front-layouts.master')

@section('content')
  <main class="main-products">

    <!-- Page Path -->
    <div class="bg-white">
      <div class="page-path container-2xl py-2 d-flex gap-1 align-items-center">
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

    <section class="container-2xl d-flex px-4 gap-3 mt-12 pb-12">

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
        <div class="size w-p-100 d-flex flex-column gap-2 px-2 pt-3 pb-3">
          <!-- Title -->
          <span class="text-medium-2-strong color-gray-900 pb-2 border-b-gray-300">سایز</span>
          <!-- Size Lists -->
          <div class="w-p-100 size-list grid gap-2 mt-3">
            @php
              $attributeValueIdsArray = json_decode(request('attribute_value_id', []));
            @endphp
            @foreach ($sizeValues->values as $sizeValue)
              <label class="d-flex gap-1 g-col-3">
                <input 
                  type="checkbox" 
                  {{ in_array($sizeValue->id, $attributeValueIdsArray) ? 'checked' : '' }}
                  value="{{ $sizeValue->id }}" 
                  class="size-list-item bg-white customCheckbox radius-medium text-center text-medium" 
                />
                <span>{{ $sizeValue->value }}</span>
              </label>
            @endforeach
          </div>
        </div>

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
        <div class="price-range w-p-100 d-flex flex-column gap-3 px-2 pt-4 pb-3">
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
        </div>

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
              <input type="checkbox" id="available" class="available-btn2 customCheckbox" />
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
            <select class="select-sorting p-1 border-gray-300 text-button-1">
              <option value="most_visited" {{ request('sort', 'newest') == 'most_visited' ? 'selected' : '' }}>پربازدید ترین</option>
              <option value="low_to_high" {{ request('sort', 'newest') == 'low_to_high' ? 'selected' : '' }}>ارزان ترین</option>
              <option value="high_to_low" {{ request('sort', 'newest') == 'high_to_low' ? 'selected' : '' }}>گران ترین</option>
              <option value="top_sales" {{ request('sort', 'newest') == 'top_sales' ? 'selected' : '' }}>پرفروش ترین</option>
              <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>جدید ترین</option>
              <option value="most_discount" {{ request('sort', 'newest') == 'most_discount' ? 'selected' : '' }}>ویژه</option>
            </select>
          </div>
        </div>

        <!-- Products Carts -->
        <div class="products-carts grid gap-md-4 gap-2 mx-lg-0 mx-auto">
          @foreach ($products as $product)
            <div class="g-col-md-4 g-col-6">
              <article class="product-cart">
                <a href="./product-detail.html" class="bg-gray-100 d-flex flex-column overflow-hidden position-relative">
                  <!-- Hover Buttons -->
                  <div class="hover-buttons d-flex flex-column gap-2 justify-content-center position-absolute">
                    <button type="button" class="d-flex flex-column gap-1">
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                    </button>
                    <button type="button" class="like">
                      <i class="icon-heart icon-fs-medium-2"></i>
                    </button>
                  </div>
                  <!-- Img -->
                  <figure class="product-img overflow-hidden position-relative">
                    <img class="main-img w-p-100 h-p-100" loading="lazy" src="{{ asset('front-assets/images/homePage/product-5125-thumb_lg.jpg') }}" alt="">
                    <img class="hover-img w-p-100 h-p-100 hidden position-absolute top-0 start-0" loading="lazy" src="{{ asset('front-assets/images/homePage/product-5125-thumb_lg (1).jpg') }}" alt="">
                    <button type="button" class="see-more-product text-nowrap text-center position-absolute bg-white radius-small ps-2 pe-1 py-1 text-medium">مشاهده بیشتر</button>
                  </figure>
                  <div class="product-details d-flex flex-column px-2 mt-2">
                    <!-- Title -->
                    <h5 class="text-medium-2-strong color-gray-900 text-truncate">بلوز یقه گرد ژاکارد 3871  </h5> 
                    <div class="d-flex flex-wrap gap-lg-1 align-items-center">
                      <!-- Price -->
                      <div class="d-flex gap-1 align-items-center">
                        <ins class="currency text-medium-2 color-primary-500">549000</ins>
                        <span class="text-medium color-gray-800">تومان</span>
                      </div>
                      <!-- Discount Price -->
                      <div class="d-flex align-items-center color-gray-700">
                        <i class="icon-angle-double-right icon-fs-small pb-1"></i>
                        <s class="text-medium  currency">595000</s>
                      </div>
                      <!-- Discount Percent -->
                      <span class="px-2 radius-u text-button-1 bg-secondary-100">20%</span> 
                      <div></div>
                    </div>
                  </div>
                </a>
              </article>
            </div>
          @endforeach
        </div>

        <!-- Products Pages -->
        <ul class="pages mt-3 d-flex gap-3 justify-content-center">
            <li class="page lock d-flex align-items-center">
                <a href="#" class="page-prev">
                  <i class="icon-angle-right color-gray-700 icon-fs-medium"></i>
                </a>
            </li>
            <li class="page active text-center">
                <a href="#" class="page-item text-medium-2">1</a>
            </li>
            <li class="page text-center">
                <a href="#" class="page-item text-medium-2">2</a>
            </li>
            <li class="page text-center">
                <a href="#" class="page-item text-medium-2">3</a>
            </li>
            <li class="page text-center">
                <a href="#" class="page-item text-medium-2">4</a>
            </li>
            <li class="page text-center">
                <a href="#" class="page-item text-medium-2">...</a>
            </li>
            <li class="page text-center">
                <a href="#" class="page-item text-medium-2">17</a>
            </li>
            <li class="page text-center d-flex align-items-center">
                <a href="#" class="page-next">
                  <i class="icon-angle-left color-gray-700 icon-fs-medium-2"></i>
                </a>
            </li>
        </ul>

      </div>

    </section>

    <!-- Mobile Menu Bottom -->
    <section class="mobile-menu-bottom d-lg-none position-fixed bottom-0 end-0 start-0">
        <ul class="d-flex  bg-white pt-2 align-items-center justify-content-around">
          <li class="d-flex flex-column align-items-center active">
            <a href="/"><i class="icon-home1 icon-fs-medium-2"></i></a>
            <span class="text-button">صفحه اصلی</span>
          </li>
          <li class="d-flex flex-column align-items-center">
            <button type="button" data-modal="category">
              <i class="icon-category icon-fs-medium-2"></i>
            </button>
            <span class="text-button"> دسته بندی ها</span>
          </li> 
          <li class="d-flex flex-column align-items-center position-relative">
            <a href="./order.html"><i class="icon-bag icon-fs-medium-2"></i></a>
            <span class="text-button">سبد خرید</span>
            <span class="cart-number position-absolute bg-primary-500 color-white radius-circle  h-4 w-4 text-center top-0 start-0">2</span>
          </li>  
          <li class="d-flex flex-column align-items-center">
            <a href="./user-panel.html"><i class="icon-user icon-fs-medium-2"></i></a>
            <span class="text-button">پروفایل</span>
          </li> 
        </ul>
    </section>

      <!-- Whatsapp Icon In mobile -->
    <figure class="Whatsapp-icon-mobile d-lg-none">
      <a class="w-p-100" href="https://wa.me/+989039611333">
        <img class="w-p-100" src="{{ asset('front-assets/images/homePage/whatsApp.c1c819e5.png') }}" alt="whatsapp">
      </a>
    </figure>

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
             <span class="text-medium-2-strong color-gray-900">
              فیلتر جستوجو
             </span>
         </div>
      </div>
      <div class="d-flex flex-column gap-1 bg-gray-100 px-3">
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
    $('.select-sorting').chnage(() => {
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
    appendNewFilter('attribute_value_id', JSON.stringify(sizeAttributeValueIds));
  }

  function filterByTitle() {
    const title = $('.title-input').val()?.trim();
    if (title.length > 0) {
      appendNewFilter('title', title);
    }
  }

  function filterByStoreBalance() {
    const isAvailableChecked = $('.available-btn').is(':checked') ? 1 : '';
    appendNewFilter('available', isAvailableChecked);
  }

  function filterByPrice() {
    appendNewFilter('min_price', $('.range-min').val());
    appendNewFilter('max_price', $('.range-max').val());
  }

  $(document).ready(() => {
    filterByCategoryId();
    handleFiltringWhenSetFilterClicked();
  });

</script>

@endsection
