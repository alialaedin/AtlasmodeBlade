@extends('front-layouts.master')

@section('content')
  <main id="main-content" class="main mt-lg-0 mt-4">

		<button class="bg-black color-white text-medium" style="margin-right: 200px" @click="consoleLog">لاگ</button>
        <form action="{{ route('customer.carts.add') }}" method="POST">
            @csrf
            <input hidden name="variety_id" value="91">
            <input hidden name="quantity" value="2">
            <button class="bg-black color-white text-medium" style="margin-right: 200px">افزودن محصول</button>
        </form>

    <!-- Page Path -->
    <div class="bg-white">
      <ul class="page-path container-2xl px-4 px-md-8 px-3xl-0  d-flex gap-1 mb-lg-6 mb-4 align-items-center mt-lg-3 mt-0 pt-2 text-medium">
        <i class="icon-home1 icon-fs-medium-2"></i>
        <a href="/" class="text-button-1 mt-1">خانه</a>
        <i class="icon-angle-double-left icon-fs-medium"></i>
        <a href="{{ route('front.products.index') }}" class="text-button-1 mt-1">محصولات</a>
        <i class="icon-angle-double-left icon-fs-medium"></i>
        <a href="{{ route('customer.carts.index') }}" class="text-button-1 mt-1">تسویه حساب</a>
      </ul>
    </div>

    <!-- Top Cart -->
    <div class="top-cart d-flex justify-content-center px-lg-0 px-6 align-items-center">
      <button type="button" class="shipping-cart-btn active">
        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="none" viewBox="0 0 24 24" data-sentry-element="svg" data-sentry-component="SvgBagHappy" data-sentry-source-file="BagHappy.tsx"><path fill="#888888" d="M12 18.5a4.26 4.26 0 0 1-4.25-4.25c0-.41.34-.75.75-.75s.75.34.75.75C9.25 15.77 10.48 17 12 17s2.75-1.23 2.75-2.75c0-.41.34-.75.75-.75s.75.34.75.75A4.26 4.26 0 0 1 12 18.5M5.19 6.38c-.19 0-.39-.08-.53-.22a.754.754 0 0 1 0-1.06l3.63-3.63c.29-.29.77-.29 1.06 0s.29.77 0 1.06L5.72 6.16c-.15.14-.34.22-.53.22M18.81 6.38c-.19 0-.38-.07-.53-.22l-3.63-3.63a.754.754 0 0 1 0-1.06c.29-.29.77-.29 1.06 0l3.63 3.63c.29.29.29.77 0 1.06-.14.14-.34.22-.53.22" data-sentry-element="path" data-sentry-source-file="BagHappy.tsx"></path><path fill="#888888" d="M20.21 10.6H4c-.7.01-1.5.01-2.08-.57-.46-.45-.67-1.15-.67-2.18 0-2.75 2.01-2.75 2.97-2.75h15.56c.96 0 2.97 0 2.97 2.75 0 1.04-.21 1.73-.67 2.18-.52.52-1.22.57-1.87.57M4.22 9.1h15.79c.45.01.87.01 1.01-.13.07-.07.22-.31.22-1.12 0-1.13-.28-1.25-1.47-1.25H4.22c-1.19 0-1.47.12-1.47 1.25 0 .81.16 1.05.22 1.12.14.13.57.13 1.01.13z" data-sentry-element="path" data-sentry-source-file="BagHappy.tsx"></path><path fill="#888888" d="M14.89 22.75H8.86c-3.58 0-4.38-2.13-4.69-3.98l-1.41-8.65c-.07-.41.21-.79.62-.86.4-.07.79.21.86.62l1.41 8.64c.29 1.77.89 2.73 3.21 2.73h6.03c2.57 0 2.86-.9 3.19-2.64l1.68-8.75c.08-.41.47-.68.88-.59.41.08.67.47.59.88l-1.68 8.75c-.39 2.03-1.04 3.85-4.66 3.85" data-sentry-element="path" data-sentry-source-file="BagHappy.tsx"></path></svg>
      </button>
      <span class="shipp-info bg-gray-700"></span>
      <button type="button" class="send-information-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" data-sentry-element="svg" data-sentry-component="SvgBoxTime" data-sentry-source-file="BoxTime.tsx"><path fill="#888888" d="M12 13.3c-.13 0-.26-.03-.38-.1L2.79 8.09a.75.75 0 0 1-.27-1.03.75.75 0 0 1 1.03-.27L12 11.68l8.4-4.86a.76.76 0 0 1 1.03.27c.21.36.08.82-.27 1.03l-8.77 5.08a.9.9 0 0 1-.39.1" data-sentry-element="path" data-sentry-source-file="BoxTime.tsx"></path><path fill="#888888" d="M12 22.36c-.41 0-.75-.34-.75-.75v-9.07c0-.41.34-.75.75-.75s.75.34.75.75v9.07c0 .41-.34.75-.75.75" data-sentry-element="path" data-sentry-source-file="BoxTime.tsx"></path><path fill="#888888" d="M12 22.75c-.88 0-1.76-.19-2.44-.58l-5.34-2.96c-1.45-.8-2.59-2.73-2.59-4.39V9.16c0-1.66 1.14-3.58 2.59-4.39l5.34-2.96c1.36-.76 3.5-.76 4.87 0l5.34 2.96c1.45.8 2.59 2.73 2.59 4.39v5.66c0 .07 0 .17-.03.3a.753.753 0 0 1-1.23.4c-1.14-1-2.92-1.04-4.14-.07a3.224 3.224 0 0 0-.75 4.2c.08.14.16.25.25.35.15.17.21.4.17.62s-.18.42-.38.52l-1.83 1.01c-.67.41-1.54.6-2.42.6m0-20c-.62 0-1.25.13-1.7.38L4.96 6.09c-.97.54-1.81 1.97-1.81 3.07v5.66c0 1.1.85 2.54 1.81 3.07l5.34 2.96c.91.51 2.5.51 3.41 0l1.12-.62a4.7 4.7 0 0 1-.57-2.25c0-1.46.65-2.81 1.78-3.71 1.37-1.09 3.31-1.32 4.83-.66V9.15c0-1.1-.85-2.54-1.81-3.07l-5.34-2.96c-.47-.24-1.1-.37-1.72-.37" data-sentry-element="path" data-sentry-source-file="BoxTime.tsx"></path><path fill="#888888" d="M19 22.75c-2.62 0-4.75-2.13-4.75-4.75 0-1.46.65-2.81 1.78-3.71.84-.67 1.9-1.04 2.97-1.04 2.62 0 4.75 2.13 4.75 4.75 0 1.36-.59 2.66-1.62 3.56-.87.77-1.98 1.19-3.13 1.19m0-8c-.74 0-1.44.25-2.03.72-.77.61-1.22 1.54-1.22 2.53 0 1.79 1.46 3.25 3.25 3.25.78 0 1.54-.29 2.15-.81.7-.62 1.1-1.5 1.1-2.44 0-1.79-1.46-3.25-3.25-3.25" data-sentry-element="path" data-sentry-source-file="BoxTime.tsx"></path><path fill="#888888" d="M18 19.75c-.25 0-.5-.13-.64-.36-.21-.36-.1-.82.26-1.03l.89-.53v-1.08c0-.41.34-.75.75-.75s.75.34.75.75v1.5c0 .26-.14.51-.36.64l-1.25.75c-.14.08-.27.11-.4.11" data-sentry-element="path" data-sentry-source-file="BoxTime.tsx"></path></svg>
      </button>
      <span class="info-peyment bg-gray-700"></span>
      <button type="button" class="payment-information-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" data-sentry-element="svg" data-sentry-component="SvgWalletCheck" data-sentry-source-file="WalletCheck.tsx"><path fill="#888888" d="M5 20.75c-1.66 0-3.22-.88-4.06-2.31C.49 17.72.25 16.87.25 16c0-2.62 2.13-4.75 4.75-4.75S9.75 13.38 9.75 16c0 .87-.24 1.72-.69 2.45-.84 1.42-2.4 2.3-4.06 2.3m0-8c-1.79 0-3.25 1.46-3.25 3.25 0 .59.16 1.17.47 1.67.59 1 1.63 1.58 2.78 1.58s2.19-.59 2.78-1.57c.31-.51.47-1.08.47-1.68 0-1.79-1.46-3.25-3.25-3.25" data-sentry-element="path" data-sentry-source-file="WalletCheck.tsx"></path><path fill="#888888" d="M4.43 17.74c-.19 0-.38-.07-.53-.22l-.99-.99a.755.755 0 0 1 0-1.06c.29-.29.77-.29 1.06 0l.48.48 1.6-1.48c.3-.28.78-.26 1.06.04s.26.78-.04 1.06l-2.13 1.97c-.15.13-.33.2-.51.2" data-sentry-element="path" data-sentry-source-file="WalletCheck.tsx"></path><path fill="#888888" d="M17 19.75H7.63c-.32 0-.6-.2-.71-.49-.11-.3-.02-.63.22-.83s.46-.46.62-.74c.32-.51.48-1.09.48-1.68 0-1.79-1.46-3.25-3.25-3.25-.93 0-1.82.4-2.44 1.11-.21.23-.54.32-.83.21a.76.76 0 0 1-.49-.7V9c0-3.08 1.9-5.31 4.85-5.68.27-.04.58-.07.9-.07h10c.24 0 .55.01.87.06 2.95.34 4.88 2.58 4.88 5.69v5c.02 3.44-2.29 5.75-5.73 5.75m-7.82-1.5H17c2.58 0 4.25-1.67 4.25-4.25V9c0-2.34-1.37-3.95-3.59-4.21-.24-.04-.45-.04-.66-.04H7c-.24 0-.47.02-.7.05-2.2.28-3.55 1.88-3.55 4.2v2.82c.68-.37 1.46-.57 2.25-.57 2.62 0 4.75 2.13 4.75 4.75 0 .79-.2 1.57-.57 2.25" data-sentry-element="path" data-sentry-source-file="WalletCheck.tsx"></path><path fill="#888888" d="M22 14.25h-3c-1.52 0-2.75-1.23-2.75-2.75S17.48 8.75 19 8.75h3c.41 0 .75.34.75.75s-.34.75-.75.75h-3a1.25 1.25 0 0 0 0 2.5h3c.41 0 .75.34.75.75s-.34.75-.75.75" data-sentry-element="path" data-sentry-source-file="WalletCheck.tsx"></path></svg>
      </button>
    </div>

    <!-- Shipping Cart -->
    <section class="shipping-cart active container-2xl mt-5 gap-3 px-lg-0 px-4 mb-4">
      <div class="d-flex flex-lg-row flex-column w-p-100 gap-2 px-md-2 mt-1">
          <!-- Product Lists -->
          <div class="product-list col-lg-7 d-flex flex-column gap-3 px-1">
              <div class="shippingCart-info d-flex justify-content-between">
                  <div class="d-flex align-items-center gap-3">
                      <h5 class="text-medium-strong">سبد خرید شما</h5>
                      <span class="text-medium-2">1 کالا</span>
                  </div>
                  <!-- Delete Button -->
                  <button class="delete-allProducts d-flex align-items-center gap-1" type="button">
                      <span class="text-medium">حذف کل سبد خرید</span>
                      <i class="icon-trash-2 icon-fs-medium"></i>
                  </button>
              </div>
              <!-- Products -->
                  <div class="product-item d-flex flex-column p-3 gap-lg-2 position-relative">
                      <button class="delete-product position-absolute  radius-circle" type="button">
                          <i class="icon-trash-2 icon-fs-medium color-gray-700"></i>
                      </button>
                      <!-- Img And Title -->
                      <div class="d-flex gap-2 align-items-center">
                          <a href="./product-detail.html">
                            <img class="product-item-img radius-large" src="{{ asset('front-assets/images/product-detail/product-5120 (1).jpg') }}" alt="">
                          </a>
                          <!-- Title And Color And Size -->
                          <div class="d-flex flex-column justify-content-between pt-md-4 pt-7 pb-4">
                              <a class="text-medium-2-strong color-gray-900">پیراهن راه راه سارینا 5393</a>
                              <span class="color-gray-700 text-medium">رنگ:صورتی</span>
                              <span class="color-gray-700 text-medium">سایز:38</span>
                          </div>
                      </div>
                      <div class="price d-flex justify-content-around radius-small">
                          <!-- Price -->
                          <div class="product-item-price d-flex align-items-center gap-md-3 gap-1 text-medium py-2">
                              <span class="bg-secondary-100 radius-u px-md-2 px-1 text-button-1">60 %</span>
                              <!-- First Price -->
                              <s class="color-gray-700 text-linethrough currency">1600000</s>
                              <!-- Discount Price -->
                              <div class="d-flex gap-1 justify-content-start">
                                  <ins class="currency text-medium-2-strong">1356800</ins>
                                  <span class="text-medium">تومان</span>
                              </div>
                          </div>
                          <!-- Counter -->
                          <div class="counter d-flex justify-content-center align-items-center text-medium-3 gap-md-4 gap-2">
                              <button type="button" class="add-btn border-gray-200 color-gray-700">
                                  <i class="icon-plus icon-fs-medium-2"></i>
                              </button>
                              <span class="count">1</span>
                              <button type="button" disabled class="remove-btn border-gray-200 color-gray-700">
                                  <i class="icon-minus icon-fs-medium-2"></i>
                              </button>
                          </div>
                      </div>
                  </div>
                  <div class="product-item d-flex flex-column p-3 gap-lg-2 position-relative">
                      <button class="delete-product position-absolute  radius-circle" type="button">
                          <i class="icon-trash-2 icon-fs-medium color-gray-700"></i>
                      </button>
                      <!-- Img And Title -->
                      <div class="d-flex gap-2 align-items-center">
                          <a href="./product-detail.html">
                            <img class="product-item-img radius-large" src="{{ asset('front-assets/images/product-detail/product-5120 (1).jpg') }}" alt="">
                          </a>
                          <!-- Title And Color And Size -->
                          <div class="d-flex flex-column justify-content-between pt-md-4 pt-7 pb-4">
                              <a class="text-medium-2-strong color-gray-900">پیراهن راه راه سارینا 5393</a>
                              <span class="color-gray-700 text-medium">رنگ:صورتی</span>
                              <span class="color-gray-700 text-medium">سایز:38</span>
                          </div>
                      </div>
                      <div class="price d-flex justify-content-around radius-small">
                          <!-- Price -->
                          <div class="product-item-price d-flex align-items-center gap-md-3 gap-1 text-medium py-2">
                              <span class="bg-secondary-100 radius-u px-md-2 px-1 text-button-1">60 %</span>
                              <!-- First Price -->
                              <s class="color-gray-700 text-linethrough currency">1600000</s>
                              <!-- Discount Price -->
                              <div class="d-flex gap-1 justify-content-start">
                                  <ins class="currency text-medium-2-strong">1356800</ins>
                                  <span class="text-medium">تومان</span>
                              </div>
                          </div>
                          <!-- Counter -->
                          <div class="counter d-flex justify-content-center align-items-center text-medium-3 gap-md-4 gap-2">
                              <button type="button" class="add-btn border-gray-200 color-gray-700">
                                  <i class="icon-plus icon-fs-medium-2"></i>
                              </button>
                              <span class="count">1</span>
                              <button type="button" disabled class="remove-btn border-gray-200 color-gray-700">
                                  <i class="icon-minus icon-fs-medium-2"></i>
                              </button>
                          </div>
                      </div>
                  </div>
                  <div class="product-item d-flex flex-column p-3 gap-lg-2 position-relative">
                      <button class="delete-product position-absolute  radius-circle" type="button">
                          <i class="icon-trash-2 icon-fs-medium color-gray-700"></i>
                      </button>
                      <!-- Img And Title -->
                      <div class="d-flex gap-2 align-items-center">
                          <a href="./product-detail.html">
                            <img class="product-item-img radius-large" src="{{ asset('front-assets/images/product-detail/product-5120 (1).jpg') }}" alt="">
                          </a>
                          <!-- Title And Color And Size -->
                          <div class="d-flex flex-column justify-content-between pt-md-4 pt-7 pb-4">
                              <a class="text-medium-2-strong color-gray-900">پیراهن راه راه سارینا 5393</a>
                              <span class="color-gray-700 text-medium">رنگ:صورتی</span>
                              <span class="color-gray-700 text-medium">سایز:38</span>
                          </div>
                      </div>
                      <div class="price d-flex justify-content-around radius-small">
                          <!-- Price -->
                          <div class="product-item-price d-flex align-items-center gap-md-3 gap-1 text-medium py-2">
                              <span class="bg-secondary-100 radius-u px-md-2 px-1 text-button-1">60 %</span>
                              <!-- First Price -->
                              <s class="color-gray-700 text-linethrough currency">1600000</s>
                              <!-- Discount Price -->
                              <div class="d-flex gap-1 justify-content-start">
                                  <ins class="currency text-medium-2-strong">1356800</ins>
                                  <span class="text-medium">تومان</span>
                              </div>
                          </div>
                          <!-- Counter -->
                          <div class="counter d-flex justify-content-center align-items-center text-medium-3 gap-md-4 gap-2">
                              <button type="button" class="add-btn border-gray-200 color-gray-700">
                                  <i class="icon-plus icon-fs-medium-2"></i>
                              </button>
                              <span class="count">1</span>
                              <button type="button" disabled class="remove-btn border-gray-200 color-gray-700">
                                  <i class="icon-minus icon-fs-medium-2"></i>
                              </button>
                          </div>
                      </div>
                  </div>
          </div> 
          <!-- Price Details -->
          <div class="price-details position-sticky col-lg-5 d-flex flex-column mt-lg-0 mt-3 gap-2">
              <span class="text-medium-3-strong pe-4">صورت حساب</span>
              <div class="d-flex flex-column p-5 gap-5">
                  <div class="d-flex flex-column gap-1">
                      <!-- Total Price -->
                      <div class="d-flex justify-content-between">
                      <span class="text-medium color-gray-900">مجموع قیمت ها :</span>
                      <div class="d-flex align-items-center gap-1">
                          <span class="currency text-medium-2 color-gray-900">1234000</span>
                          <span class="text-medium color-gray-900">تومان</span>
                      </div>
                      </div>
                      <!-- Discount -->
                      <div class="d-flex justify-content-between">
                          <span class="text-medium color-gray-900">تخفیف:</span>
                          <div class="d-flex align-items-center gap-1">
                              <span class="currency text-medium-3 color-gray-900">840000</span>
                              <span class="text-medium color-gray-900">تومان</span>
                          </div>
                      </div>
                      <!-- Final Price -->
                      <div class="d-flex justify-content-between">
                      <span class="text-medium-strong color-gray-900">پرداخت نهایی:</span>
                      <div class="d-flex align-items-center gap-1">
                          <span class="currency text-medium-3-strong color-gray-900">840000</span>
                          <span class="text-medium-strong color-gray-900">تومان</span>
                      </div>
                      </div>
                  </div>
                  <form class="discount-form mx-auto">
                      <input type="text" placeholder="کد تخفیف را وارد کنید" class="discount-input bg-gray-200 p-2">
                      <button type="button" class="discount-btn bg-gray-700 color-white text-medium py-1 px-md-3 px-1">ثبت کد تخفیف</button>
                  </form>
                  <button type="button" class="continue-process-btn bg-black color-white text-medium">
                      ادامه فرآیند خرید
                  </button>
              </div>
          </div>
      </div>
      <div class="empty-cart flex-column align-items-center justify-content-center radius-medium">
        <i class="icon-cart color-gray-600"></i>
        <h2 class="text-medium-3-strong color-gray-600">سبد خرید شما خالیست!</h2>
      </div>
    </section>

    <!-- Address And Delivery Informations -->
    <section class="send-information flex-lg-row flex-column container-2xl mb-4 mt-4 gap-5 px-lg-2 px-4">
        <!-- Addresses And Sending Methods -->
        <div class="d-flex bg-gray-100 p-2 radius-small flex-column gap-3 col-lg-8">
            <!-- Address -->
            <div class="d-flex flex-column gap-1">
                <div class="d-flex gap-2 align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"  data-sentry-element="svg" data-sentry-source-file="Location.tsx" data-sentry-component="SvgLocation"><path fill="#a1a3a8" stroke="#a1a3a8" d="M12 14.17c-2.13 0-3.87-1.73-3.87-3.87S9.87 6.44 12 6.44s3.87 1.73 3.87 3.87-1.74 3.86-3.87 3.86Zm0-6.23c-1.3 0-2.37 1.06-2.37 2.37s1.06 2.37 2.37 2.37 2.37-1.06 2.37-2.37S13.3 7.94 12 7.94Z" data-sentry-element="path" data-sentry-source-file="Location.tsx"></path><path fill="#a1a3a8" stroke="#a1a3a8" d="M12 22.76a5.97 5.97 0 0 1-4.13-1.67c-2.95-2.84-6.21-7.37-4.98-12.76C4 3.44 8.27 1.25 12 1.25h.01c3.73 0 8 2.19 9.11 7.09 1.22 5.39-2.04 9.91-4.99 12.75A5.97 5.97 0 0 1 12 22.76Zm0-20.01c-2.91 0-6.65 1.55-7.64 5.91C3.28 13.37 6.24 17.43 8.92 20a4.426 4.426 0 0 0 6.17 0c2.67-2.57 5.63-6.63 4.57-11.34-1-4.36-4.75-5.91-7.66-5.91Z" data-sentry-element="path" data-sentry-source-file="Location.tsx"></path></svg>
                    <span class="text-medium-strong color-gray-600">آدرس دریافت سفارش</span>
                </div>
                <div class="addresses d-flex flex-column px-2 pb-2">
                    <div class="d-flex text-medium">
                        <!-- city -->
                        <span class="city">گرگان</span>
                        <span>,</span>
                        <!-- Address -->
                        <span class="address">صداوسیما جام جم16 مجتمع آرنیکا واحد3</span>
                        <!-- If User Doesnt Have An Address -->
                        <!-- <p>برای ادامه فرآیند خرید, ابتدا ادرس دریافت را وارد کنید</p> -->
                    </div>
                    <button type="button" data-modal="choose-address" class="change-edit-btn color-gray-700 text-nowrap text-medium-strong px-3 mt-lg-0 mt-2">تغییر یا ویرایش آدرس</button>
                    <!-- If User Doesnt Have An Address -->
                    <!-- <button type="button" data-modal="add-address" class="change-edit-btn color-gray-700 text-nowrap text-medium-strong px-3 mt-lg-0 mt-2">
                        افزودن ادرس
                    </button> -->
                </div>
            </div>
            <!-- Delivery Methods -->
            <div class="d-flex flex-column gap-1">
                <div class="d-flex gap-2 align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="none" viewBox="0 0 24 24" class="h-4 w-4 lg:h-6 lg:w-6" data-sentry-element="svg" data-sentry-source-file="Timer.tsx" data-sentry-component="SvgTimer"><path fill="#a1a3a8" d="M12 22.75c-5.24 0-9.5-4.26-9.5-9.5s4.26-9.5 9.5-9.5 9.5 4.26 9.5 9.5-4.26 9.5-9.5 9.5m0-17.5c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8" data-sentry-element="path" data-sentry-source-file="Timer.tsx"></path><path fill="#a1a3a8" d="M12 13.75c-.41 0-.75-.34-.75-.75V8c0-.41.34-.75.75-.75s.75.34.75.75v5c0 .41-.34.75-.75.75M15 2.75H9c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h6c.41 0 .75.34.75.75s-.34.75-.75.75" data-sentry-element="path" data-sentry-source-file="Timer.tsx"></path></svg>
                    <span class="text-medium-strong color-gray-600">شیوه تحویل</span>
                </div>
                <div class="delivery-method d-flex flex-column px-2 pb-2">
                    <!-- Choose Method -->
                    <span class="method text-medium">پس از تایید آدرس، شیوه دریافت را تعیین کنید.</span>
                    <button type="button" data-modal="delivery-method" class="choose-method-btn color-gray-700 text-medium-strong px-3 mt-lg-0 mt-2 text-nowrap">انتخاب شیوه ارسال </button>
                </div>
            </div>
        </div>
          <!-- Price Details -->
        <div class="price-details position-sticky bg-gray-100 radius-small col-lg-4 d-flex flex-column  p-5 gap-lg-3 gap-1">
            <!-- Total Price -->
            <div class="d-flex justify-content-between">
                <span class="text-medium color-gray-900">مجموع قیمت ها :</span>
                <div class="d-flex align-items-center gap-1">
                    <span class="currency text-medium-3 color-gray-900">1234000</span>
                    <span class="text-medium color-gray-900">تومان</span>
                </div>
            </div>
            <!-- Discount -->
            <div class="d-flex justify-content-between">
                <span class="text-medium color-gray-900">تخفیف:</span>
                <div class="d-flex align-items-center gap-1">
                    <span class="currency text-medium-3 color-gray-900">840000</span>
                    <span class="text-medium color-gray-900">تومان</span>
                </div>
            </div>
            <!-- Final Price -->
            <div class="d-flex justify-content-between">
                <span class="text-medium-strong color-gray-900">پرداخت نهایی:</span>
                <div class="d-flex align-items-center gap-1">
                    <span class="currency text-medium-3-strong color-gray-900">840000</span>
                    <span class="text-medium-strong color-gray-900">تومان</span>
                </div>
            </div>
            <button type="button"  class="delivery-btn bg-black color-white text-medium">
                مرحله بعـد
            </button>
        </div>
    </section>

    <!-- Payment Information -->
    <section class="payment-information flex-lg-row flex-column container-2xl my-4 gap-5 px-lg-2 px-4">
        <div class="d-flex bg-gray-100 p-2 radius-small flex-column gap-3 col-lg-8 mt-2 mt-lg-0 mb-lg-0">
            <!-- Payment Way And Portal -->
            <div class="d-flex flex-column gap-3">
                <!-- Title -->
                <div class="d-flex gap-2 align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24.855" height="18.969" viewBox="0 0 24.855 18.969" style="vertical-align: middle;"><path id="Icon_material-account-balance-wallet" data-name="Icon material-account-balance-wallet" d="M27.1,19.474v1a2.306,2.306,0,0,1-2.511,2H7.011a2.3,2.3,0,0,1-2.511-2V6.5a2.3,2.3,0,0,1,2.511-2H24.589a2.306,2.306,0,0,1,2.511,2v1H15.8a2.3,2.3,0,0,0-2.511,2v7.986a2.3,2.3,0,0,0,2.511,2Zm-11.3-2H28.355V9.491H15.8Zm5.022-2.5a1.722,1.722,0,0,1-1.883-1.5,1.722,1.722,0,0,1,1.883-1.5,1.722,1.722,0,0,1,1.883,1.5A1.722,1.722,0,0,1,20.822,14.982Z" transform="translate(-4 -4)" fill="none" stroke="#888888" stroke-width="1"></path></svg>
                    <span class="text-medium-strong color-gray-700">شیوۀ پرداخت خود را انتخاب کنید.</span>
                </div>
                <!-- Payment Way -->
                <form class="payment-way d-flex flex-column px-2 pb-2">
                    <div class="d-flex text-medium gap-2 align-items-center pb-1">
                      <input type="radio" id="internet-payment" checked name="pay-way" class="customRadio">
                      <label for="internet-payment"> پرداخت اینترنتی </label>
                    </div>
                    <div class="d-flex text-medium gap-2 align-items-center pb-1">
                        <input type="radio" id="wallet-payment" name="pay-way" class="customRadio">
                        <label for="wallet-payment" class="d-flex gap-1">
                            <span>پرداخت از کیف پول</span>
                            <span> (موجودی فعلی: ۰ تومان)</span>
                        </label>
                    </div>
                </form>
                <!-- portal -->
                <div class="portal d-flex flex-lg-row flex-column justify-content-between gap-lg-0 gap-3 py-3">
                    <span class="text-medium-strong color-gray-700">پرداخت با کلیه کارت های اعتباری شرکت</span>
                    <div class="d-flex gap-8">
                        <button type="button" class="portal-item-sep active position-relative">
                          <figure>
                            <img class="tick position-absolute" src="{{ asset('front-assets/images/cart/tick-box.db941cf5.svg') }}" alt="tick">
                            <img src="{{ asset('front-assets/images/cart/sep.png') }}" alt="saman">
                          </figure>
                        </button>
                        <button type="button" class="portal-item-zarinpal position-relative">
                          <figure>
                            <img class="tick position-absolute" src="{{ asset('front-assets/images/cart/tick-box.db941cf5.svg') }}" alt="tick">
                            <img src="{{ asset('front-assets/images/cart/zarinpal.png') }}" alt="zarinpal">
                          </figure>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Order Summery -->
            <div class="d-flex flex-column gap-1">
                <!-- Title -->
                <div class="d-flex gap-1 align-items-center mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="none" viewBox="0 0 24 24" class="h-4 w-4" data-sentry-element="svg" data-sentry-source-file="ReceiptText.tsx" data-sentry-component="SvgReceiptText"><path fill="#a4a4a4" d="M19.42 11.75H16c-.41 0-.75-.34-.75-.75V4.01c0-.74.29-1.43.81-1.95s1.21-.81 1.95-.81h.01c1.25.01 2.43.5 3.33 1.39.9.91 1.39 2.11 1.39 3.36v2.42c.01 1.99-1.33 3.33-3.32 3.33m-2.67-1.5h2.67c1.16 0 1.83-.67 1.83-1.83V6c0-.86-.34-1.68-.95-2.3-.61-.6-1.42-.94-2.28-.95h-.01c-.33 0-.65.13-.89.37s-.37.55-.37.89z" data-sentry-element="path" data-sentry-source-file="ReceiptText.tsx"></path><path fill="#a4a4a4" d="M9 23.33c-.47 0-.91-.18-1.24-.52L6.1 21.14a.246.246 0 0 0-.33-.02L4.06 22.4c-.53.4-1.23.47-1.83.17s-.97-.9-.97-1.57V6c0-3.02 1.73-4.75 4.75-4.75h12c.41 0 .75.34.75.75s-.34.75-.75.75c-.69 0-1.25.56-1.25 1.25v17c0 .67-.37 1.27-.97 1.57-.59.3-1.3.23-1.83-.17l-1.71-1.28a.243.243 0 0 0-.32.02l-1.68 1.68c-.34.33-.78.51-1.25.51m-3.09-3.76c.46 0 .91.17 1.25.52l1.66 1.67c.06.06.14.07.18.07s.12-.01.18-.07l1.68-1.68c.62-.62 1.6-.68 2.29-.15l1.7 1.27c.11.08.21.05.26.02s.14-.09.14-.22V4c0-.45.11-.88.3-1.25H6C3.78 2.75 2.75 3.78 2.75 6v15c0 .14.09.2.14.23.06.03.16.05.26-.03l1.71-1.28c.31-.23.68-.35 1.05-.35" data-sentry-element="path" data-sentry-source-file="ReceiptText.tsx"></path><path fill="#a4a4a4" d="M12 9.75H6c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h6c.41 0 .75.34.75.75s-.34.75-.75.75M11.25 13.75h-4.5c-.41 0-.75-.34-.75-.75s.34-.75.75-.75h4.5c.41 0 .75.34.75.75s-.34.75-.75.75" data-sentry-element="path" data-sentry-source-file="ReceiptText.tsx"></path></svg>
                    <span class="text-medium-strong color-gray-700">خلاصه سفارش</span>
                </div>
                <!-- Address -->
                <div class="d-flex gap-4 pe-2">
                    <span class="text-button-1 color-gray-600">آدرس:</span>
                    <span class="text-button-1">جام جم 16 مجتمع ارنیکا</span>
                </div>
                <!-- Phone Number -->
                <div class="d-flex gap-4 pe-2">
                    <span class="text-button-1 color-gray-600">شماره تماس:</span>
                    <span class="text-button-1">09331498232</span>
                </div>
                <!-- Orders -->
                <div class="orders flex-column gap-1 pe-2">
                    <span class="text-button-1 color-gray-600">سبد خرید:</span>
                    <!-- Items -->
                    <div class="order-items d-flex gap-2">
                        <div class="order-item position-relative radius-small">
                            <img class="w-p-100 h-p-100 radius-small" src="{{ asset('front-assets/images/product-detail/product-5120 (1).jpg') }}" alt="">
                            <span class="text-button bottom-0 start-0 position-absolute bg-gray-100 radius-small">2</span>
                        </div>
                        <div class="order-item position-relative radius-small">
                            <img class="w-p-100 h-p-100 radius-small" src="{{ asset('front-assets/images/product-detail/product-5120 (1).jpg') }}" alt="">
                            <span class="text-button bottom-0 start-0 position-absolute bg-gray-100 radius-small">2</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="orderType-priceDetails position-sticky col-lg-4 d-flex flex-column gap-3">
            <!-- Order Type -->
            <div class="d-flex flex-column gap-1 px-5 py-2 bg-gray-100 radius-small">
                <!-- Title -->
                <div class="d-flex gap-2 align-items-center">
                    <i class="icon-cart icon-fs-medium color-gray-700"></i>
                    <span class="text-medium-strong color-gray-700">نوع سفارش</span>
                </div>
                <!-- Type -->
                <form class="order-type d-flex gap-4 px-2 pb-2">
                    <div class="d-flex text-medium gap-2 align-items-center pb-1">
                      <input type="radio" id="normal" checked name="order-type" class="customRadio">
                      <label for="normal"> عادی</label>
                    </div>
                    <div class="d-flex text-medium gap-2 align-items-center pb-1">
                        <input type="radio" id="reserve" name="order-type" class="customRadio">
                        <label for="reserve" class="d-flex gap-1">رزرو</label>
                    </div>
                </form>
            </div> 
            <!-- Price Details -->
            <div class="price-details bg-gray-100 radius-small d-flex flex-column p-5 gap-2">
                <!-- Total Price -->
                <div class="d-flex justify-content-between">
                    <span class="text-medium color-gray-900">مجموع قیمت ها :</span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="currency text-medium-3  color-gray-900">1234000</span>
                        <span class="text-medium color-gray-900">تومان</span>
                    </div>
                </div>
                <!-- Discount -->
                <div class="d-flex justify-content-between">
                    <span class="text-medium  color-gray-900">تخفیف:</span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="currency text-medium-3  color-gray-900">840000</span>
                        <span class="text-medium color-gray-900">تومان</span>
                    </div>
                </div>
                <!-- Shipping Cost -->
                <div class="d-flex justify-content-between color-gray-600">
                    <span class="text-medium-2 ">هزینه ارسال:</span>
                    <span class="text-medium-2 ">رایگان</span>
                </div>
                <!-- Final Price -->
                <div class="d-flex justify-content-between">
                    <span class="text-medium-strong color-gray-900">پرداخت نهایی:</span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="currency text-medium-3 -strong color-gray-900">840000</span>
                        <span class="text-medium-strong color-gray-900">تومان</span>
                    </div>
                </div>
                <button type="button"  class="pay-btn bg-black color-white text-medium">
                    پرداخت
                </button>
            </div>
        </div>
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
        <img class="w-p-100" src="{{ asset('front-assets/images/homePage/whatsApp.c1c819e5.png') }}" alt="whatsapp">
      </a>
    </figure>

  </main>
@endsection

@section('scripts')

<script>
  orderPage()
  editAddress()
  choosePortal()
  counter()
</script>  

<script src="{{ asset('assets/vue/vue3/vue.global.prod.js') }}"></script>

<script>

  const {
    createApp
  } = Vue;

  createApp({
    data() {
      return {
        carts: @json($carts)
      }
    },
		watch: {},
    methods: {
			consoleLog() {
				console.log(this.carts);
			}
		},
    computed: {}
  }).mount("#main-content");
</script>

@endsection
