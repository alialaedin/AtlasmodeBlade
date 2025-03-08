@extends('front-layouts.master')

@section('content')
  <main class="pb-4">

    <!-- Page Path -->
    <div class="bg-white">
      <div class="page-path container-2xl py-2 px-4 px-md-8 px-3xl-0 d-flex gap-1 align-items-center">
        <i class="icon-home1 icon-fs-medium-2"></i>
        <a href="/" class="text-button-1 mt-1">خانه</a>
        <i class="icon-angle-double-left icon-fs-medium"></i>
        <a href="{{ route('front.posts.index') }}" class="text-button-1 mt-1">پست ها</a>
      </div>
    </div>

    <section class="container-2xl d-flex flex-lg-row flex-column gap-4 py-3 px-4 px-md-8 px-3xl-0">
        <div class="weblog-list d-flex flex-column col-lg-9">
            <form class="d-flex gap-1 align-items-center border-b-gray-300 pb-1">
                <i class="icon-search icon-fs-large"></i>
                <input type="text" class="flex-grow-1 bg-white px-2 py-1">
            </form>
          <!-- Lists -->
          <div class="weblog grid mt-5">
            <div class="g-col-lg-4 g-col-6">
                <article class="weblog-cart">
                    <a href="./weblog-details.html" class="d-flex flex-column overflow-hidden position-relative">
                        <!-- Img -->
                        <figure class="blog-img overflow-hidden radius-medium position-relative">
                            <img class="main-img w-p-100 h-p-100 radius-medium" loading="lazy" src="assets/images/homePage/panbe.jpg" alt="">
                            <div class="position-absolute w-p-100 px-2 bottom-0 pb-1 svg-hover d-flex align-items-end justify-content-between">
                                <span class="text-button-1 color-white">
                                    اطلاعات بیشتر
                                </span>
                                <i class="icon-arrow-left2 icon-fs-small color-white"></i>
                            </div>
                        </figure>
                        <div class="blog-details d-flex flex-column gap-1 px-1 mt-1">
                            <!-- Little Explaintion -->
                            <div class="d-flex gap-1 align-items-center color-gray-900">
                                <i class="icon-caret-left icon-fs-medium"></i>
                                <h5 class="text-medium text-truncate">
                                    تاریخچه پنبه در ایران، از تولید تا تجارت 
                                </h5>
                            </div> 
                            <div class="d-lg-flex d-none align-items-center justify-content-between pb-2 border-b-gray-300">
                                <!-- Publication Date -->
                                <div class="d-flex align-items-center text-button-1 color-gray-700">
                                    <span class="color-gray-900">تاریخ انتشار:</span>
                                    <time datetime="1402/02/23">1402/02/23</time>
                                </div>
                                <div class="d-flex gap-1 align-items-center text-button-1">
                                    <span class="color-gray-900">دسته بندی:</span>
                                    <a href="./weblog-list.html" class="category color-gray-700">
                                        اموزش
                                    </a>
                                </div>
                            </div>
                            <p class="explain-weblog d-lg-block d-none text-button-1 color-gray-900 pt-1">
                                پنبه یکی از اولین رشته های گیاهی بود که برای تولید پارچه مورد استفاده قرار گرفت و علی رغم فرایند تولید پیچیده و سختش، هم ...
                            </p>
                        </div>
                    </a>
                </article>
            </div>
            <div class="g-col-lg-4 g-col-6">
                <article class="weblog-cart">
                    <a href="./weblog-details.html" class="d-flex flex-column overflow-hidden position-relative">
                        <!-- Img -->
                        <figure class="blog-img overflow-hidden radius-medium position-relative">
                            <img class="main-img w-p-100 h-p-100 radius-medium" loading="lazy" src="assets/images/homePage/panbe.jpg" alt="">
                            <div class="position-absolute w-p-100 px-2 bottom-0 pb-1 svg-hover d-flex align-items-end justify-content-between">
                                <span class="text-button-1 color-white">
                                    اطلاعات بیشتر
                                </span>
                                <i class="icon-arrow-left2 icon-fs-small color-white"></i>
                            </div>
                        </figure>
                        <div class="blog-details d-flex flex-column gap-1 px-1 mt-1">
                            <!-- Little Explaintion -->
                            <div class="d-flex gap-1 align-items-center color-gray-900">
                                <i class="icon-caret-left icon-fs-medium"></i>
                                <h5 class="text-medium text-truncate">
                                    تاریخچه پنبه در ایران، از تولید تا تجارت 
                                </h5>
                            </div> 
                            <div class="d-lg-flex d-none align-items-center justify-content-between pb-2 border-b-gray-300">
                                <!-- Publication Date -->
                                <div class="d-flex align-items-center text-button-1 color-gray-700">
                                    <span class="color-gray-900">تاریخ انتشار:</span>
                                    <time datetime="1402/02/23">1402/02/23</time>
                                </div>
                                <div class="d-flex gap-1 align-items-center text-button-1">
                                    <span class="color-gray-900">دسته بندی:</span>
                                    <a href="./weblog-list.html" class="category color-gray-700">
                                        اموزش
                                    </a>
                                </div>
                            </div>
                            <p class="explain-weblog d-lg-block d-none text-button-1 color-gray-900 pt-1">
                                پنبه یکی از اولین رشته های گیاهی بود که برای تولید پارچه مورد استفاده قرار گرفت و علی رغم فرایند تولید پیچیده و سختش، هم ...
                            </p>
                        </div>
                    </a>
                </article>
            </div>
            <div class="g-col-lg-4 g-col-6">
                <article class="weblog-cart">
                    <a href="./weblog-details.html" class="d-flex flex-column overflow-hidden position-relative">
                        <!-- Img -->
                        <figure class="blog-img overflow-hidden radius-medium position-relative">
                            <img class="main-img w-p-100 h-p-100 radius-medium" loading="lazy" src="assets/images/homePage/panbe.jpg" alt="">
                            <div class="position-absolute w-p-100 px-2 bottom-0 pb-1 svg-hover d-flex align-items-end justify-content-between">
                                <span class="text-button-1 color-white">
                                    اطلاعات بیشتر
                                </span>
                                <i class="icon-arrow-left2 icon-fs-small color-white"></i>
                            </div>
                        </figure>
                        <div class="blog-details d-flex flex-column gap-1 px-1 mt-1">
                            <!-- Little Explaintion -->
                            <div class="d-flex gap-1 align-items-center color-gray-900">
                                <i class="icon-caret-left icon-fs-medium"></i>
                                <h5 class="text-medium text-truncate">
                                    تاریخچه پنبه در ایران، از تولید تا تجارت 
                                </h5>
                            </div> 
                            <div class="d-lg-flex d-none align-items-center justify-content-between pb-2 border-b-gray-300">
                                <!-- Publication Date -->
                                <div class="d-flex align-items-center text-button-1 color-gray-700">
                                    <span class="color-gray-900">تاریخ انتشار:</span>
                                    <time datetime="1402/02/23">1402/02/23</time>
                                </div>
                                <div class="d-flex gap-1 align-items-center text-button-1">
                                    <span class="color-gray-900">دسته بندی:</span>
                                    <a href="./weblog-list.html" class="category color-gray-700">
                                        اموزش
                                    </a>
                                </div>
                            </div>
                            <p class="explain-weblog d-lg-block d-none text-button-1 color-gray-900 pt-1">
                                پنبه یکی از اولین رشته های گیاهی بود که برای تولید پارچه مورد استفاده قرار گرفت و علی رغم فرایند تولید پیچیده و سختش، هم ...
                            </p>
                        </div>
                    </a>
                </article>
            </div>
            <div class="g-col-lg-4 g-col-6">
                <article class="weblog-cart">
                    <a href="./weblog-details.html" class="d-flex flex-column overflow-hidden position-relative">
                        <!-- Img -->
                        <figure class="blog-img overflow-hidden radius-medium position-relative">
                            <img class="main-img w-p-100 h-p-100 radius-medium" loading="lazy" src="assets/images/homePage/panbe.jpg" alt="">
                            <div class="position-absolute w-p-100 px-2 bottom-0 pb-1 svg-hover d-flex align-items-end justify-content-between">
                                <span class="text-button-1 color-white">
                                    اطلاعات بیشتر
                                </span>
                                <i class="icon-arrow-left2 icon-fs-small color-white"></i>
                            </div>
                        </figure>
                        <div class="blog-details d-flex flex-column gap-1 px-1 mt-1">
                            <!-- Little Explaintion -->
                            <div class="d-flex gap-1 align-items-center color-gray-900">
                                <i class="icon-caret-left icon-fs-medium"></i>
                                <h5 class="text-medium text-truncate">
                                    تاریخچه پنبه در ایران، از تولید تا تجارت 
                                </h5>
                            </div> 
                            <div class="d-lg-flex d-none align-items-center justify-content-between pb-2 border-b-gray-300">
                                <!-- Publication Date -->
                                <div class="d-flex align-items-center text-button-1 color-gray-700">
                                    <span class="color-gray-900">تاریخ انتشار:</span>
                                    <time datetime="1402/02/23">1402/02/23</time>
                                </div>
                                <div class="d-flex gap-1 align-items-center text-button-1">
                                    <span class="color-gray-900">دسته بندی:</span>
                                    <a href="./weblog-list.html" class="category color-gray-700">
                                        اموزش
                                    </a>
                                </div>
                            </div>
                            <p class="explain-weblog d-lg-block d-none text-button-1 color-gray-900 pt-1">
                                پنبه یکی از اولین رشته های گیاهی بود که برای تولید پارچه مورد استفاده قرار گرفت و علی رغم فرایند تولید پیچیده و سختش، هم ...
                            </p>
                        </div>
                    </a>
                </article>
            </div>
          </div>
          <!-- Pages -->
          <ul class="pages mt-8 d-flex gap-3 justify-content-center">
            <li class="page lock d-flex align-items-center">
                <a href="#" class="page-prev pt-1">
                  <i class="icon-angle-right color-gray-700 icon-fs-medium"></i>
                </a>
            </li>
            <li class="page active text-center">
                <a href="" class="page-item text-medium pt-1">1</a>
            </li>
            <li class="page text-center">
                <a href="" class="page-item text-medium pt-1">2</a>
            </li>
            <li class="page text-center">
                <a href="" class="page-item text-medium pt-1">3</a>
            </li>
            <li class="page text-center">
                <a href="" class="page-item text-medium pt-1">4</a>
            </li>
            <li class="page text-center">
                <a href="" class="page-item text-medium pt-1">...</a>
            </li>
            <li class="page text-center">
                <a href="" class="page-item text-medium pt-1">17</a>
            </li>
            <li class="page text-center d-flex align-items-center">
                <a href="" class="page-next pt-1">
                  <i class="icon-angle-left color-gray-700 icon-fs-medium-2"></i>
                </a>
            </li>
          </ul>
        </div>
        <aside class="col-lg-3 px-2 mt-lg-0 mt-7">
            <!-- Categories -->
            <div class="weblog-categories bg-white position-sticky p-3 d-flex flex-column">
                <!-- Title -->
                <span class="title text-medium-2-strong color-gray-900 pb-2">
                    دسته بندی ها
                </span>
                <ul class="category-list mt-3">
                    <li class="mb-2">
                        <a href="./weblog-list.html" class="d-flex gap-1 align-items-center color-gray-800 text-medium">
                            <i class="icon-angle-left icon-fs-medium"></i>
                            <span>آموزش</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
    </section>

    <!-- Mobile Menu Bottom -->
    <section class="mobile-menu-bottom d-lg-none position-fixed bottom-0 end-0 start-0">
        <ul class="d-flex  bg-white pt-2 align-items-center justify-content-around">
            <li class="d-flex flex-column align-items-center active">
              <a href="/">
              <i class="icon-home1 icon-fs-medium-2"></i>
              </a>
              <span class="text-button">صفحه اصلی</span>
            </li>
            <li class="d-flex flex-column align-items-center">
                <button type="button" data-modal="category">
                    <i class="icon-category icon-fs-medium-2"></i>
                </button>
                <span class="text-button"> دسته بندی ها</span>
            </li> 
            <li class="d-flex flex-column align-items-center position-relative">
                <a href="./order.html">
                  <i class="icon-bag icon-fs-medium-2"></i>
                </a>
                                        <span class="text-button">سبد خرید</span>
                <!-- Number -->
                <span class="cart-number position-absolute bg-primary-500 color-white radius-circle  h-4 w-4 text-center top-0 start-0">2</span>
            </li>  
            <li class="d-flex flex-column align-items-center">
                <a href="./user-panel.html">
                  <i class="icon-user icon-fs-medium-2"></i>
                </a>
                <span class="text-button">پروفایل</span>
            </li> 
            
        </ul>
    </section>

    <!-- Whatsapp Icon In mobile -->
    <figure class="Whatsapp-icon-mobile d-lg-none">
      <a class="w-p-100" href="https://wa.me/+989039611333">
        <img class="w-p-100" src="assets/images/homePage/whatsApp.c1c819e5.png" alt="whatsapp">
      </a>
    </figure>

  </main>
@endsection

@section('modals')
  
@endsection

@section('scripts')

<script src="{{ asset('front-assets/js/lightbox.js') }}"></script>

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
