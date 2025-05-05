@extends('front-layouts.master')

@section('content')
  <main class="main-products">

    <!-- Page Path -->
    <div class="bg-white">
      <div class="page-path container-2xl px-4 px-md-8 px-3xl-0 py-1 d-flex gap-1 align-items-center">
        <i class="icon-home1 icon-fs-medium-2"></i>
        <a href="/" class="text-button-1 mt-1">خانه</a>
        <i class="icon-angle-double-left icon-fs-medium"></i>
        <a href="{{ url('/products') }}" class="text-button-1 mt-1">لیست محصولات</a>
      </div>
    </div>

    <form id="filter-form" action="{{ route('front.products.index') }}" method="GET" class="d-none">
      <input hidden name="category_id" value="{{ request('category_id') }}">
      <input hidden name="sort" value="{{ request('sort') }}" />
      <input hidden name="title" value="{{ request('title') }}" />
      <input hidden name="available" value="{{ request('available') }}" />
      <input hidden name="min_price" value="{{ request('min_price') }}" />
      <input hidden name="max_price" value="{{ request('max_price') }}" />
    </form>

    <section class="container-2xl d-flex px-4 px-md-8 px-3xl-0 gap-3 mt-12 pb-12">

      <aside id="filter-aside" class="aside d-lg-flex d-none flex-column align-items-center">

        <!-- Title -->
        <div class="w-p-100 d-flex gap-1 justify-content-center color-gray-900 pb-2 border-b-gray-300">
          <i class="icon-filter icon-fs-medium-2"></i>
          <span class="text-medium-2-strong">فیلتر جستجو</span>
        </div>

        <div class="category w-p-100 my-2 d-flex flex-column gap-1">
          <!-- Title -->
          <span class="text-medium-2-strong color-gray-900">دسته بندی ها</span>
          <!-- Category Lists -->
          <form class="category-forms w-p-100 mt-2 d-flex flex-column gap-5">
            <!-- Parents -->
            <select v-model="parentCategoryId" placeholder="" class="p-2 w-p-100 bg-transparent border-gray-300">
              <option selected value="" hidden>دسته بندی مورد نظر را انتخاب کنید</option>
              <option 
                v-for="category in allParentCategories" 
                :key="category.id" 
                :value="category.id" 
                v-text="category.title"
              ></option>
            </select>
            <!-- Childeren -->
            <select v-if="childCategories.length > 0" v-model="childCategoryId" placeholder="" class="p-2 w-p-100 bg-transparent border-gray-300">
              <option selected value="" hidden>دسته بندی مورد نظر را انتخاب کنید</option>
              <option 
                v-for="category in childCategories" 
                :key="category.id" 
                :value="category.id" 
                v-text="category.title"
              ></option>
            </select>
            <!-- Grand Childeren -->
            <select v-if="grandChildCategories.length" v-model="grandChildCategoryId" placeholder="" class="p-2 w-p-100 bg-transparent border-gray-300">
              <option selected value="" hidden>دسته بندی مورد نظر را انتخاب کنید</option>
              <option 
                v-for="category in grandChildCategories" 
                :key="category.id" 
                :value="category.id" 
                v-text="category.title"
              ></option>
            </select>
          </form>
        </div>

        <!-- Title -->
        <div class="title w-p-100 d-flex flex-column gap-4 px-2 pt-3 pb-6">
          <span class="text-medium-2-strong color-gray-900">عنوان</span>
          <input type="text" v-model="filterByTitle" class="title-input text-medium border-gray-300 w-p-100 px-4 py-1" placeholder="عنوان جستجو را بنویسید">
        </div>

        <!-- Color -->
        <div class="colors p-4 d-flex flex-column gap-2">
          <span class="w-p-100 text-medium-2-strong color-gray-900 pb-2 border-b-gray-300">رنگ ها</span>
          <ul class="color-list d-flex flex-wrap gap-3 px-1">
            <template v-for="colorRange in colorRanges" :key="colorRange.id">
              <button type="button" @click="toggleColorRange($event)" class="color-btn d-flex flex-column gap-1 align-items-center" :data-id="colorRange.id">
                <figure class="h-8 w-8">
                  <img
                    class="w-p-100 h-p-100 d-block border-gray-300" 
                    :src="colorRange.logo.url"
                  />
                </figure>
                <span class="text-subtitle">@{{ colorRange.title }}</span>
              </button>
            </template>
          </ul>
          <button type="button" @click="openColorDetailsModal" class="color-detail w-p-100 d-flex align-items-center justify-content-between">
            <span class="text-medium color-gray-800">جزییات رنگ ها</span>
            <i class="icon-angle-left icon-fs-medium color-gray-700"></i>
          </button>
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
              v-model="filterByAvailable"
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
            <input type="range" @input="handlePriceRange($event)" class="range-min pointer" :min="minPriceFilter" :max="maxPriceFilter" v-model="filterByMinPrice" step="10000">
            <input type="range" @input="handlePriceRange($event)" class="range-max pointer" :min="minPriceFilter" :max="maxPriceFilter" v-model="filterByMaxPrice" step="10000">
          </form>
          <div class="price-text d-flex justify-content-between mt-2">
            <div class="d-flex gap-1 color-gray-900 text-button-1">
              <span>از قیمت:</span>
              <span class="price-min currency" v-text="minPriceFilter"></span>
              <span>تومان</span>
            </div>
            <div class="d-flex gap-1 color-gray-900 text-button-1">
              <span>تا:</span>
              <span class="price-max currency" v-text="maxPriceFilter"></span>
              <span>تومان</span>
            </div>
          </div>
        </div>

        <!-- Set Filters Button -->
        <button type="button" @click="filter" class="setFilter-btn w-p-100 bg-black color-white py-1 text-medium mt-2">اعمال فیلتر</button>

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
            {{-- <div class="d-flex gap-1 align-items-center">
              <input 
                type="checkbox" 
                id="available" 
                class="available-btn2 customCheckbox" 
                {{ request('available') == '1' ? 'checked': '' }}
              />
              <label class="text-button-1 color-gray-900 mt-1" name="available">نمایش محصولات موجود</label>
            </div>  --}}
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
                    {{-- <button type="button" class="d-flex flex-column gap-1">
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                      <i class="icon-star icon-fs-xsmall"></i>
                    </button> --}}
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

        @if ($requestCategory !== [])
          <div>
            {!! $requestCategory->description !!}
          </div>
        @endif

      </div>

    </section>

    @include('front-layouts.includes.mobile-menu')

  </main>
@endsection

@section('modals')
<!-- Color Details -->
<div id="color-details-modal" class="modal color-details-modal bg-white d-flex flex-column radius-small overflow-hidden gap-2 pb-3 pt-4 px-4">
  <!-- Header -->
  <div class="px-3 pt-2 bg-white d-flex justify-content-between position-fixed top-0 start-0 end-0 align-items-center pb-2 border-b-gray-300">
    <div class="d-flex flex-column gap-1">
      <span class="text-medium">جزییات رنگ‌ها</span>
      <p class="text-button color-gray-700">جزییات رنگ‌ها</p>
    </div>
    <button type="button" class="modal-close">
      <i class="icon-cancel icon-fs-medium color-gray-700"></i>
    </button>
  </div>
  <!-- Description -->
  <div class="d-flex flex-column gap-3 mt-12 overflow-auto">
    @foreach ($colorRanges as $colorRange)
      <div class="w-p-100 d-flex flex-column gap-1">
        <div class="d-flex gap-1">
          <figure class="border-gray-300 h-8 w-8">
            <img src="{{ Storage::url($colorRange->logo->uuid .'/'. $colorRange->logo->file_name) }}" class="brown w-p-100 h-p-100 d-block border-gray-300" />
          </figure>
          <span class="text-subtitle">{{ $colorRange->title }}</span>
        </div>
        <p class="text-medium color-gray-700">{{ $colorRange->description }}</p>
      </div>
    @endforeach
  </div>
</div>
@endsection

@section('scripts')

<script>
  productsPage();
  hoverImg();
</script> 

<script src="{{ asset('assets/vue/vue3/vue.global.prod.js') }}"></script>
<script src="{{ asset('assets/sweetalert2/sweetalert2.js') }}"></script>

<script>
  const { createApp } = Vue;
  createApp({
    mounted() {
      this.handleCategoriesFilter();
    },
    data() {
      return {

        minPriceFilter: @json($priceFilter['minPrice']),
        maxPriceFilter: @json($priceFilter['maxPrice']),

        colorRanges: @json($colorRanges),
        requestCategoryId: @json(request('category_id')) ?? null,
        filterByAvailable: @json(request('available', 0)) == 1 ? true : false,
        filterByTitle: @json(request('title')) ?? '',
        filterByMinPrice: @json(request('min_price', $priceFilter['minPrice'])),
        filterByMaxPrice: @json(request('max_price', $priceFilter['maxPrice'])),

        parentCategoryId: '',
        childCategoryId: '',
        grandChildCategoryId: '',

        allParentCategories: @json($parentCategories),
        allChildCategories: @json($childCategories),
        allGrandChildCategories: @json($grandChildCategories),

        childCategories: [],
        grandChildCategories: [],

      }
    },
    watch: {
      parentCategoryId(id) {
        this.childCategories = this.allChildCategories?.filter(c => c.parent_id == id) ?? [];
        this.childCategoryId = '',
        this.grandChildCategories = [];
        this.grandChildCategoryId = '';
      },
      childCategoryId(id) {
        this.grandChildCategories = this.allGrandChildCategories?.filter(c => c.parent_id == id) ?? [];
        this.grandChildCategoryId = '';
      }
    },
    methods: {
      async request(url, method, data = null, onSuccessRequest) {

        let options = {
          method: method,
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': @json(csrf_token())
          },
        };

        if (data === null) {
          options.headers['Content-Type'] = 'application/json';
        } else {
          options.body = data instanceof FormData ? data : JSON.stringify(data);
        }

        const response = await fetch(url, options);
        const result = await response.json();

        if (!response.ok) {
          switch (response.status) {
            case 422:
              this.showValidationError(result.errors);
              break;
            case 404:
              this.popup('error', 'خطای 404', 'چنین چیزی وجود ندارد');
              break;
            case 500:
              this.popup('error', 'خطای سرور', result.message);
              break;
            default: 
              this.popup('error', 'خطای نا شناخته');
              break;
          }
          return;
        }

        onSuccessRequest(result);
      },
      async handleCategoriesFilter() {
        if (this.requestCategoryId) {
          await this.setParentCategory();
          await this.setChildCategory();
          await this.setGrandChildCategory();
        }
      },
      setParentCategory() {
        const isParent = this.allParentCategories?.find(c => c.id == this.requestCategoryId);
        if (isParent) {
          this.parentCategoryId = this.requestCategoryId;
          return;
        } 

        const isChild = this.allChildCategories?.find(c => c.id == this.requestCategoryId);
        if (isChild) {
          this.parentCategoryId = isChild.parent_id;
          return;
        } 

        const isGrandChild = this.allGrandChildCategories?.find(c => c.id == this.requestCategoryId);
        if (isGrandChild) {
          const child = this.allChildCategories?.find(c => c.id == isGrandChild.parent_id);
          if (child) {
            this.parentCategoryId = child.parent_id;
          }
        }

      },
      setChildCategory() {
        this.childCategories = this.allChildCategories?.filter(c => c.parent_id == this.parentCategoryId);
        const isChild = this.allChildCategories?.find(c => c.id == this.requestCategoryId);
        if (isChild) {
          this.childCategoryId = isChild.id;
          return;
        } 

        const isGrandChild = this.allGrandChildCategories?.find(c => c.id == this.requestCategoryId);
        if (isGrandChild) {
          this.childCategoryId = isGrandChild.parent_id;
        }
      },
      setGrandChildCategory() {
        this.grandChildCategories = this.allGrandChildCategories?.filter(c => c.parent_id == this.childCategoryId);
        const isGrandChild = this.allGrandChildCategories?.find(c => c.id == this.requestCategoryId);
        if (isGrandChild) {
          this.grandChildCategoryId = isGrandChild.id;
        }
      },
      openColorDetailsModal() {
        document.querySelector('#color-details-modal').classList.add('active');  
        document.querySelector('.modal-overlay').classList.add('active');
        document.body.classList.add('no-overflow');
      },
      handlePriceRange(e) {

        const rangeInput = document.querySelectorAll('.range-input input');       
        const price = document.querySelectorAll('.price-text .currency');
        const range = document.querySelector('.slider .progress');

        let priceGap = 10000;

        const minVal = parseInt(rangeInput[0].value);
        const maxVal = parseInt(rangeInput[1].value);

        if (maxVal - minVal < priceGap) {
          if (e.target.className === "range-min") {
            rangeInput[0].value = maxVal - priceGap;
          } else {
            rangeInput[1].value = minVal + priceGap;
          }
        } else {
          range.style.right = (minVal / rangeInput[0].max) * 100 + "%";
          range.style.left = 100 - (maxVal / rangeInput[1].max) * 100 + "%";
          let min = minVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
          let max = maxVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');    
          price[0].textContent = min;
          price[1].textContent = max;
        }
      },
      toggleColorRange(e) {
        const button = e.currentTarget;
        button.classList.toggle('select');
      },
      getFinalCategoryId() {
        if (this.grandChildCategoryId) {
          return this.grandChildCategoryId
        } else if (this.childCategoryId) {
          return this.childCategoryId;
        } else if (this.parentCategoryId) {
          return this.parentCategoryId;
        } else {
          return '';
        }
      },
      getColorRangeIds() {
        const selectedColorRanges = document.querySelectorAll('.color-btn.select');
        if (selectedColorRanges.length == 0) return '';

        const ids = [];
        selectedColorRanges.forEach(colorRangeBtn => {
          ids.push(parseInt(colorRangeBtn.getAttribute('data-id')));
        })

        return ids.join(',');
      },
      filter() {

        const params = {
          title: this.filterByTitle,
          min_price: this.filterByMinPrice,
          max_price: this.filterByMaxPrice,
          available: this.filterByAvailable ? 1 : 0,
          category_id: this.getFinalCategoryId(),
          color_range_ids: this.getColorRangeIds(),
        };

        const queryString = Object.entries(params)
          .filter(([_, value]) => value !== null && value !== undefined && value !== '')
          .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
          .join("&");

        const baseUrl = @json(route('front.products.index'));
        const url = `${baseUrl}?${queryString}`;

        window.location.replace(url);
      },
    },
    computed: {

    }
  }).mount('#filter-aside')
</script>

<script>
  $(document).ready(() => {
    $('#sort-select').change(function() {
      $('#filter-form').find('input[name=sort]').attr('value', $(this).val());
      $('#filter-form').submit();
    });
  });
</script> 

@endsection
