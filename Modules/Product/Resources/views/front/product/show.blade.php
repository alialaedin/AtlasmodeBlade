@extends('front-layouts.master')

@section('content')
  <main class="main mb-3">

    <!-- Page Path -->
    <div class="bg-white">
      <div class="page-path container-2xl px-4 px-md-8 px-3xl-0 py-2 d-flex gap-1 align-items-center">
        <i class="icon-home1 icon-fs-medium-2"></i>
        <a href="/" class="text-button-1 mt-1">خانه</a>
        <i class="icon-angle-double-left icon-fs-medium"></i>
        <a href="/products" class="text-button-1 mt-1">محصولات</a>
        <i class="icon-angle-left icon-fs-medium"></i>
        <a :href="'/products/' + product.id" class="text-button-1 mt-1">@{{ product.title }}</a>
      </div>
    </div>

    <section class="container-2xl d-flex flex-column flex-lg-row gap-4 px-4 px-md-8 px-3xl-0 mt-lg-10 mt-6">

      <!-- Product Images  -->
      <div class="product-images d-flex gap-lg-2 position-relative pb-3 pb-lg-0 pt-lg-0">

        <div class="col-3 product-thumbImages-swiper swiper position-sticky">
          <div class="swiper-wrapper">
            <div v-for="image in product.images" :key="image.id" class="swiper-slide">
              <figure class="w-p-100">
                <img class="w-p-100" :src="image.url" :alt="product.image_alt">   
              </figure>
            </div>
          </div>
          <div class="swiper-scrollbar"></div>
        </div>

        <div class="col-9 product-main-swiper swiper pb-lg-0 pb-1">
          <div class="swiper-wrapper">
            <div v-for="image in product.images" :key="image.id" class="swiper-slide">
              <figure>
                <a :href="image.url" class="w-p-100" data-lightbox="product-image">
                  <img class="w-p-100" :src="image.url" :alt="product.image_alt">   
                </a>
              </figure>
            </div>
          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

      <!-- Details -->
      <div class="product-details d-flex flex-column flex-grow-1 pe-lg-2 pb-10 color-gray-900 px-5 px-lg-0">

        <!-- Category -->
        <span class="d-none d-lg-block text-medium color-gray-600" v-text="categoriesTitle"></span>

        <!-- Product Title , Price , Svgs -->
        <div class="d-flex flex-column gap-1 border-b-2 pb-1">
          <!-- Title and Svg -->
          <div class="d-flex justify-content-between align-items-center">
            <h1 class="h5-strong product-title color-gray-900" v-text="product.title"></h1>
            <div class="d-flex align-items-center gap-1">
              <button type="button" class="like-btn">
                <svg class="heart" width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.761 20.8538C9.5904 19.5179 7.57111 17.9456 5.73929 16.1652C4.45144 14.8829 3.47101 13.3198 2.8731 11.5954C1.79714 8.25031 3.05393 4.42083 6.57112 3.28752C8.41961 2.69243 10.4384 3.03255 11.9961 4.20148C13.5543 3.03398 15.5725 2.69398 17.4211 3.28752C20.9383 4.42083 22.2041 8.25031 21.1281 11.5954C20.5302 13.3198 19.5498 14.8829 18.2619 16.1652C16.4301 17.9456 14.4108 19.5179 12.2402 20.8538L12.0051 21L11.761 20.8538Z" stroke="#444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15.7393 7.05301C16.8046 7.39331 17.5615 8.34971 17.6561 9.47499" stroke="#444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                <svg class="heart-red" width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-v-10f8db86=""><path fill-rule="evenodd" clip-rule="evenodd" d="M15.8498 2.50071C16.4808 2.50071 17.1108 2.58971 17.7098 2.79071C21.4008 3.99071 22.7308 8.04071 21.6198 11.5807C20.9898 13.3897 19.9598 15.0407 18.6108 16.3897C16.6798 18.2597 14.5608 19.9197 12.2798 21.3497L12.0298 21.5007L11.7698 21.3397C9.4808 19.9197 7.3498 18.2597 5.4008 16.3797C4.0608 15.0307 3.0298 13.3897 2.3898 11.5807C1.2598 8.04071 2.5898 3.99071 6.3208 2.76971C6.6108 2.66971 6.9098 2.59971 7.2098 2.56071H7.3298C7.6108 2.51971 7.8898 2.50071 8.1698 2.50071H8.2798C8.9098 2.51971 9.5198 2.62971 10.1108 2.83071H10.1698C10.2098 2.84971 10.2398 2.87071 10.2598 2.88971C10.4808 2.96071 10.6898 3.04071 10.8898 3.15071L11.2698 3.32071C11.3616 3.36968 11.4647 3.44451 11.5538 3.50918C11.6102 3.55015 11.661 3.58705 11.6998 3.61071C11.7161 3.62034 11.7327 3.63002 11.7494 3.63978C11.8352 3.68983 11.9245 3.74197 11.9998 3.79971C13.1108 2.95071 14.4598 2.49071 15.8498 2.50071ZM18.5098 9.70071C18.9198 9.68971 19.2698 9.36071 19.2998 8.93971V8.82071C19.3298 7.41971 18.4808 6.15071 17.1898 5.66071C16.7798 5.51971 16.3298 5.74071 16.1798 6.16071C16.0398 6.58071 16.2598 7.04071 16.6798 7.18971C17.3208 7.42971 17.7498 8.06071 17.7498 8.75971V8.79071C17.7308 9.01971 17.7998 9.24071 17.9398 9.41071C18.0798 9.58071 18.2898 9.67971 18.5098 9.70071Z" data-v-10f8db86="" fill="#ee1212"></path></svg>
              </button>
              <button type="button" class="share-btn">
                <i class="icon-share icon-fs-medium-2"></i>
              </button>
            </div>
          </div>
          <!-- Price  -->
          <div class="d-flex flex-wrap gap-2 align-items-center pb-2 border-b-gray-300">
            <!-- Price -->
            <div class="d-flex gap-1 align-items-center">
              <ins class="currency text-medium-3-strong color-primary-500" v-text="product.final_price.amount"></ins>
              <span class="text-medium-2 color-gray-900">تومان </span>
            </div>
            <template v-if="product.final_price.discount_price > 0">
              <!-- Discount Price -->
              <div class="d-flex gap-1 align-items-center color-gray-700">
                <span class="horizontal-divider h-4 bg-gray-300"></span>
                <s class="text-medium-2 d-flex gap-1 currency">
                  <span class="currency" v-text="product.final_price.base_amount"></span>
                  <span class="">تومان </span>
                </s>
              </div>
              <!-- Discount Percent -->
              <span class="px-2 radius-u text-button-1 bg-secondary-100" v-text="product.final_price.discount_price"></span> 
            </template>
          </div>
        </div>

        <template v-for="attribute in uniqueAttributes" :key="attribute.id">

          <template v-if="attribute.style == 'box'">
            <div class="product-sizes d-flex flex-column gap-1 mt-2">
              <div class="d-flex justify-content-between align-items-center">
                <span class="text-button-1">انتخاب @{{ attribute.label }}</span>
              </div>
              <div class="d-flex flex-wrap gap-2 mt-lg-0 mt-2 align-items-center">
                <div class="d-flex gap-1">
                  <button 
                    v-for="value in attribute.values"
                    :key="value"
                    :class="{ 
                      active: checkAttributeIsActive(attribute.id, value), 
                      disabled: checkAttributeIsDisable(attribute.id, value) 
                    }"
                    type="button"
                    class="size-btn text-center text-button radius-circle"
                    @click="setActiveAttributes(attribute.id, value)">
                    <span v-text="value"></span>
                  </button>
                </div>
              </div>
            </div>
          </template>

          <template v-else-if="attribute.style == 'select'">
            <div class="pattern d-flex flex-column gap-2 mt-3">
              <span class="text-button-1">انتخاب @{{ attribute.label }}</span>
              <select 
                name="pattern-select-option" 
                class="p-2 border-gray-300 bg-gray-100"
                @change="setActiveAttributes(attribute.id, $event.target.value)"
              >
                <option value="" selected disabled v-text="attribute.label"></option>
                <option
                  v-for="value in attribute.values"
                  :key="value"
                  :disabled="checkAttributeIsDisable(attribute.id, value)"
                  :value="value"
                  v-text="value"
                ></option>
              </select>
            </div>
          </template>

        </template>

        <!-- Counter -->
        <template v-if="Object.keys(selectedVariety).length > 0">
          <div class="d-flex flex-column gap-2 mt-3">
            <div class="counter d-flex justify-content-center align-items-center p-2 border-gray-300 justify-content-between">
              <button type="button" :disabled="quantityToAddInCart == selectedVariety.store.balance" class="add-btn" @click="increaseCartQuantity">
                <i class="icon-plus icon-fs-medium-2"></i>
              </button>
              <span class="count text-medium-2" v-text="quantityToAddInCart"></span>
              <button type="button" :disabled="quantityToAddInCart == 1" class="remove-btn" @click="decreaseCartQuantity">
                <i class="icon-minus icon-fs-medium-2"></i>
              </button>
            </div>
            <p class="quantity text-button-1 border-green p-2 mt-1 w-p-fit active">
              از این محصول @{{ selectedVariety.store.balance }} عدد در انبار باقی است
            </p>
          </div>
        </template>

        <!-- Add To Cart Button -->
        <button
          @click="addToCart" 
          type="button"  
          class="add-toCart mt-4 d-lg-flex d-none gap-2 align-items-center justify-content-center bg-black text-medium color-white">
          <i class="icon-troli icon-fs-medium"></i>    
          <span>افزودن به سبد خرید</span>
        </button>

      </div>

    </section>

    <section class="container-2xl d-flex flex-column gap-4 mt-lg-12 mt-6 px-4 px-md-8 px-3xl-0">

      <ul class="second-section-list d-flex justify-content-center gap-4 text-medium px-3">
        <li v-if="product.description != null" @click="showDesscription" class="description-title position-relative d-flex flex-column gap-1 justify-content-center pointer">
          <span class="px-1 pb-1 text-medium-strong">نقد و بررسی</span>
        </li>
        <li @click="showSpecifications" class="specifications-title active position-relative d-flex flex-column justify-content-center align-items-center pointer">
          <span class="px-1 pb-1 text-medium-strong">مشخصات</span>
        </li>
        <li @click="showComments" class="comments-title position-relative d-flex flex-column gap-1 justify-content-center pointer">
          <span class="px-1 pb-1 text-medium-strong">نظرات کار بران</span>
        </li>
      </ul>

      <div class="second-section-content">

        <!-- Specifications -->
        <ul class="specification-table specifications active flex-column px-lg-0 px-3 mx-auto text-medium">
          <li v-for="specification in product.specifications" :key="specification.id" class="d-flex p-2 px-md-8">
            <span class="text-medium-strong">@{{ specification.label }}</span>
            <span>@{{ getSpecificationValue(specification.id) }}</span>
          </li>
        </ul>

        <!-- description -->
        <div class="description py-6 flex-column gap-2 color-gray-700">
          @{{ product.description }}
        </div>

        <div class="comments mt-6 flex-column">

          <template v-if="product.product_comments.length == 0">
            <div class="d-flex flex-column gap-2 align-items-center mx-auto">
              <span class="color-gray-700 text-medium">دیدگاهی برای این محصول ثبت نشده است.</span>
              <span class="text-medium-2-strong color-gray-900">اولین نفری باشید که دیدگاهی را ارسال می‌کند.</span>
            </div>
          </template>

          <button 
            @click="toggleCommentForm"
            type="button" 
            class="writeComment mx-auto bg-black color-white d-flex gap-1 align-items-center px-8 py-1">
            <i class="icon-comment icon-fs-medium-2"></i>
            <span class="text-medium">برای ارسال نظر کلیک کنید</span>
          </button>

          <!-- Set A Comment -->
          <form @submit.prevent="storeNewComment" id="comment-from" class="comments-form flex-lg-row flex-column gap-3">
            <div class="col-lg-3 col-12 d-flex flex-column gap-2">
              <input type="text" v-model="commentData.title" placeholder="عنوان" class="comment-form-titleInput w-p-100 p-2 border-gray-300 radius-small">
              <!-- Stars -->
              <div class="rating d-flex gap-1 mt-lg-3 mt-1 px-lg-4">
                <button type="button" class="star-fill">
                  <i class="icon-star-fill color-rating-0-2 icon-fs-medium-2"></i>
                </button>
                <button type="button" class="starSimple">
                  <i class="icon-star color-rating-0-2 icon-fs-medium-2"></i>
                </button>
                <button type="button" class="star-fill">
                  <i class="icon-star-fill color-rating-0-2 icon-fs-medium-2"></i>
                </button>
                <button type="button" class="starSimple">
                  <i class="icon-star color-rating-0-2 icon-fs-medium-2"></i>
                </button>
                <button type="button" class="star-fill">
                  <i class="icon-star-fill color-rating-0-2 icon-fs-medium-2"></i>
                </button>
                <button type="button" class="starSimple">
                  <i class="icon-star color-rating-0-2 icon-fs-medium-2"></i>
                </button>
                <button type="button" class="star-fill">
                  <i class="icon-star-fill color-rating-0-2 icon-fs-medium-2"></i>
                </button>
                <button type="button" class="starSimple">
                  <i class="icon-star color-rating-0-2 icon-fs-medium-2"></i>
                </button>
                <button type="button" class="star-fill">
                  <i class="icon-star-fill color-rating-0-2 icon-fs-medium-2"></i>
                </button>
                <button type="button" class="starSimple">
                  <i class="icon-star color-rating-0-2 icon-fs-medium-2"></i>
                </button>
              </div>
              <!-- Show User Name Input -->
              <div class="name-input d-flex gap-1 align-items-center mt-1 px-lg-4">
                <input type="checkbox" checked v-model="commentData.showName" class="customCheckbox">
                <span class="text-button-1">نمایش نام شما</span>
              </div>
            </div>
            <div class="col-lg-9 col-12 d-flex flex-column ps-lg-3">
              <textarea  
                class="comment-textarea w-p-100 p-2 radius-small border-gray-300" 
                v-model="commentData.body" 
                cols="5" 
                rows="5" 
                placeholder="دیدگاه شما *">
              </textarea>
              <button type="submit" class="sendopinion bg-black px-8 py-1 text-medium color-white mt-3"> ارسال پیام</button>
            </div>
          </form>

          <!-- Users Comments -->
          <template v-if="product.product_comments.length">
            <div class="user-comments d-flex flex-column gap-3">
              @foreach ($product->productComments as $comment)
                <div class="user-comment bg-gray-200 d-flex flex-column gap-3 px-3 pt-3 pb-2 border-gray-300 radius-small">
                  <!-- User Name And Rating -->
                  <div class="d-flex flex-lg-row flex-column justify-content-lg-between">
                    <div class="d-flex gap-6 align-items-center">
                      <!-- User Name -->
                      @if ($comment->show_customer_name && $comment->creator_id)
                        <div class="d-flex gap-1 align-items-center color-gray-700 ">
                          <i class="icon-user1 icon-fs-medium-2"></i>
                          <span class="text-button-1"></span>
                        </div>
                      @endif
                      <!-- Publication Date -->
                      <div class="d-flex gap-2">
                        <span class="text-button-1-strong">تاریخ انتشار :</span>
                        @php
                          $time = verta($comment->created_at)->format('Y/m/d');
                        @endphp
                        <time class="text-button-1" datetime="{{ $time }}">{{ $time }}</time>
                      </div>
                    </div>
                    <!-- Stars -->
                    {{-- <div class="user-rating d-flex gap-1 mt-1 " dir="ltr">
                      <i class="icon-star-fill icon-fs-medium-2 color-rating-0-2"></i>
                      <i class="icon-star-fill icon-fs-medium-2 color-rating-0-2"></i>
                      <i class="icon-star-fill icon-fs-medium-2 color-rating-0-2"></i>
                      <i class="icon-star-fill icon-fs-medium-2 color-rating-0-2"></i>
                      <i class="icon-star icon-fs-medium-2 color-rating-0-2"></i>
                    </div> --}}
                  </div>
                  <!-- Text -->
                  <p class="user-comment-text text-medium">{{ $comment->body }}</p>
                </div>
              @endforeach
            </div>
          </template>

        </div>
      </div>
    </section>

    <section v-if="relatedProducts.length" class="container-2xl d-flex flex-column mt-12 gap-6 px-4 px-md-8 px-3xl-0">
      <!-- Title -->
      <div class="d-flex align-items-center justify-content-between">
        <h2 class="h4-strong color-gray-900">محصولات مشابه</h2>
        <a href="/products" class="see-more pb-1 text-medium-strong color-gray-900">مشاهده همه محصولات</a>
      </div>
      <div class="similar-products-swiper swiper">
        <div class="swiper-wrapper pb-3">
          <template v-for="relatedProduct in relatedProducts" :key="relatedProduct.id">
            <div class="swiper-slide">
              <article class="product-cart">
                <a :href="'/products/' + relatedProduct.id" class="bg-gray-100 d-flex flex-column overflow-hidden position-relative">
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
                      <!-- <svg class="heart-red" width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-v-10f8db86=""><path fill-rule="evenodd" clip-rule="evenodd" d="M15.8498 2.50071C16.4808 2.50071 17.1108 2.58971 17.7098 2.79071C21.4008 3.99071 22.7308 8.04071 21.6198 11.5807C20.9898 13.3897 19.9598 15.0407 18.6108 16.3897C16.6798 18.2597 14.5608 19.9197 12.2798 21.3497L12.0298 21.5007L11.7698 21.3397C9.4808 19.9197 7.3498 18.2597 5.4008 16.3797C4.0608 15.0307 3.0298 13.3897 2.3898 11.5807C1.2598 8.04071 2.5898 3.99071 6.3208 2.76971C6.6108 2.66971 6.9098 2.59971 7.2098 2.56071H7.3298C7.6108 2.51971 7.8898 2.50071 8.1698 2.50071H8.2798C8.9098 2.51971 9.5198 2.62971 10.1108 2.83071H10.1698C10.2098 2.84971 10.2398 2.87071 10.2598 2.88971C10.4808 2.96071 10.6898 3.04071 10.8898 3.15071L11.2698 3.32071C11.3616 3.36968 11.4647 3.44451 11.5538 3.50918C11.6102 3.55015 11.661 3.58705 11.6998 3.61071C11.7161 3.62034 11.7327 3.63002 11.7494 3.63978C11.8352 3.68983 11.9245 3.74197 11.9998 3.79971C13.1108 2.95071 14.4598 2.49071 15.8498 2.50071ZM18.5098 9.70071C18.9198 9.68971 19.2698 9.36071 19.2998 8.93971V8.82071C19.3298 7.41971 18.4808 6.15071 17.1898 5.66071C16.7798 5.51971 16.3298 5.74071 16.1798 6.16071C16.0398 6.58071 16.2598 7.04071 16.6798 7.18971C17.3208 7.42971 17.7498 8.06071 17.7498 8.75971V8.79071C17.7308 9.01971 17.7998 9.24071 17.9398 9.41071C18.0798 9.58071 18.2898 9.67971 18.5098 9.70071Z" data-v-10f8db86="" fill="#ee1212"></path></svg> -->
                    </button>
                  </div>
                  <!-- Img -->
                  <figure class="product-img overflow-hidden position-relative">
                    <img class="main-img w-p-100 h-p-100" loading="lazy" :src="relatedProduct.main_image.url" :alt="relatedProduct.image_alt">
                    <img class="hover-img w-p-100 h-p-100 hidden position-absolute top-0 start-0" loading="lazy" :src="relatedProduct.main_image.url" :alt="relatedProduct.image_alt">
                    <button type="button" class="see-more-product text-nowrap text-center position-absolute bg-white radius-small ps-2 pe-1 py-1 text-medium">مشاهده بیشتر</button>
                  </figure>
                  <div class="product-details d-flex flex-column px-2 mt-2">
                    <!-- Title -->
                    <h5 class="text-medium-2-strong color-gray-900 text-truncate">@{{ relatedProduct.title }}</h5> 
                    <div class="d-flex flex-wrap gap-lg-1 align-items-center">
                      <!-- Price -->
                      <div class="d-flex gap-1 align-items-center">
                        <ins class="currency text-medium-2 color-primary-500">@{{ relatedProduct.final_price.amount }}</ins>
                        <span class="text-medium color-gray-800"> تومان</span>
                      </div>
                      <template v-if="relatedProduct.final_price.discount_price > 0">
                        <!-- Discount Price -->
                        <div class="d-flex align-items-center color-gray-700">
                          <i class="icon-angle-double-right icon-fs-small pb-1"></i>
                          <s class="text-medium currency">@{{ relatedProduct.final_price.base_amount }}</s>
                        </div>
                        <!-- Discount Percent -->
                        <span class="px-2 radius-u text-button-1 bg-secondary-100">
                          @{{ relatedProdumainct.final_price.discount }} 
                          <span v-if="relatedProduct.final_price.discount_type === flat">تومان</span> 
                          <span v-else-if="relatedProduct.final_price.discount_type === percentage">%</span> 
                        </span> 
                        <div></div>
                      </template>
                    </div>
                  </div>
                </a>
              </article>
            </div>
          </template>
        </div>
        <div class="swiper-pagination position-absolute"></div>
      </div>
    </section>
    
  </main>
@endsection

@section('modals')

  <!-- Cart Modal -->
  <div id="added-to-cart-modal" class="modal modal-cart gap-2 overflow-auto d-flex flex-column align-items-center radius-medium px-lg-6 px-3 pt-3 pb-6 bg-white">
    <!-- Header -->
    <div class="header-modal d-flex w-p-100 justify-content-between align-items-center">
      <div class="d-flex gap-1 align-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 512 512">
          <g id="icomoon-ignore"></g>
          <path d="M256 0c-141.376 0-256 114.624-256 256s114.624 256 256 256 256-114.624 256-256-114.624-256-256-256zM216.32 374.128l-117.792-117.808 45.248-45.248 72.528 72.56 153.872-153.872 45.248 45.248-199.104 199.12z"/>
        </svg>
        <span class="text-medium-3">کالا به سبد خرید اضافه شد</span>
      </div>
      <i class="modal-close icon-close icon-fs-large"></i>
    </div>
    <!-- Item-->
    <div class="item w-p-100 d-flex flex-lg-row flex-column align-items-center justify-content-between radius-medium px-2 py-1">
      <div class="w-p-100 d-flex ms-lg-1 gap-lg-2 gap-4 align-items-center">
        <img class="item-img radius-medium" :src="product.main_image.url" :alt="product.image_alt">
        <span class="text-medium-strong">@{{ product.title }}</span>
      </div>
      <div class="price d-flex flex-row flex-lg-column text-subtitle gap-lg-0 gap-1 align-items-center">
        <span class="text-button d-lg-none">قیمت:</span>
        <!-- Discount -->
        <s 
          v-if="product.final_price.discount_price" 
          class="currency text-center color-gray-600"
          v-text="product.final_price.base_amount">
        </s>
        <div class="d-flex gap-1 align-items-center">
          <ins class="currency text-strong" v-text="product.final_price.amount"></ins>
          <span class="">تومان</span>
        </div>
      </div>
    </div>
    <!-- Button -->
    <a 
      href="{{ route('customer.carts.index') }}" 
      class="goToOrder mt-3 color-gray-900 bg-gray-200 d-flex gap-2 align-items-center justify-content-center text-medium-3 py-1 radius-small ">
      <span>مشاهده سبد خرید </span>
      <i class="icon-arrowLeft icon-fs-small color-gray-900"></i>
    </a>
  </div>

@endsection

@section('scripts')

  <script>
    $(document).ready(() => {
      $('.star-fill').each(function(){
        $(this).click(function(){
          let index=$(this).index('.star-fill');
          $('.star-fill').eq(index).nextAll().addClass('deactive');
          $('.starSimple').eq(index).nextAll().addClass('active');
        });
      });
      $('.starSimple').each(function(){
        $(this).click(function(){
          let index=$(this).index('.starSimple');              
          $('.starSimple').eq(index).prevAll().removeClass('active');
          $('.star-fill').eq(index).prevAll().removeClass('deactive'); 
          if(index==4){
            $('.starSimple').eq(index).removeClass('active');
            $('.star-fill').eq(4).removeClass('deactive');
          }
        });
      });
    });
  </script>

  <script src="{{ asset('assets/vue/vue3/vue.global.prod.js') }}"></script>
  <script src="{{ asset('assets/sweetalert2/sweetalert2.js') }}"></script>

  <script>

    const { createApp } = Vue;

    createApp({
      mounted() {
        this.initProductImagesSwiper();
        this.initRelatedProductsSwiper();
        this.setCategoriesTitle();
        this.setUniqueAttributes();
      },
      data() {
        return {
          product: @json($product),
          relatedProducts: @json($relatedProducts),
          quantityToAddInCart: 1,
          categoriesTitle:'',
          isLoggin: @json(auth()->guard('customer')->check()),
          uniqueAttributes: {},
          activeAttributes: [],
          commentData: {
            title: '',
            body: '',
            showName: false,
          }
        }
      },
      methods: {

        // global methods
        openModal(selector) {
          document.querySelector(selector).classList.add('active');  
          document.querySelector('.modal-overlay').classList.add('active');
          document.body.classList.add('no-overflow');
        },
        closeModal(selector) {
          document.querySelector(selector).classList.remove('active');  
          document.querySelector('.modal-overlay').classList.remove('active');
          document.body.classList.remove('no-overflow');
        },
        showValidationError(errors) {  

          const list = document.createElement('ul');  
          list.className = 'list-group';

          for (const key in errors) {  
            if (errors.hasOwnProperty(key)) {  
              const errorsArray = errors[key];  
              errorsArray.forEach((errorMessage) => {  
                const listItem = document.createElement('li');  
                listItem.className = 'list-group-item';  
                listItem.textContent = errorMessage;
                list.appendChild(listItem); 
              });  
            }  
          }  

          Swal.fire({  
            title: "<b>خطا های زیر رخ داده است</b>",  
            html: list.outerHTML, 
            icon: "error",  
            confirmButtonText: "بستن",  
          });  
        },
        popup(type, title, message) {
          Swal.fire({
            title: title,
            text: message,
            icon: type,
            confirmButtonText: "بستن",
          });
        },

        // Swiper
        initProductImagesSwiper() {

          let thumbImg = document.querySelectorAll('.product-thumbImages-swiper .swiper-wrapper .swiper-slide');

          let thumb = new Swiper('.product-thumbImages-swiper', {
            slidesPerView: 'auto',
            direction: 'vertical',
            freeMode: true,
            scrollbar: {
              el: '.swiper-scrollbar',
              draggable: true,
              dragSize: 'auto'
            },
            mousewheel: true,
          });

          let main = new Swiper('.product-main-swiper', {
            slidesPerView: '1',
            freeMode: true,
            thumbs: { swiper: thumb },
            pagination: {
              el: ".swiper-pagination",
              dynamicBullets: true,
              dynamicMainBullets: 2,
            },
          });

          thumbImg.forEach(function(slide) {
            slide.addEventListener('mouseenter', function() {
              const index = Array.from(thumbImg).indexOf(slide);
              main.slideTo(index);
            });
          });

          lightbox.option({
            resizeDuration: 500,
            wrapAround: true,
            showImageNumberLabel: true,
            disableScrolling: true,
            alwaysShowNavOnTouchDevices: true,
          });
        },
        initRelatedProductsSwiper() {
          new Swiper('.similar-products-swiper', {
            slidesPerView:'5.2',
            freeMode: true,
            breakpoints:{
              200: { slidesPerView: 2, spaceBetweenSlides: 10 },
              360: { slidesPerView: 2, spaceBetweenSlides: 10 },
              420: { slidesPerView: 2.2, spaceBetweenSlides: 10 },
              640: { slidesPerView: 3, spaceBetweenSlides: 10 },
              768: { slidesPerView:4, spaceBetweenSlides: 10 },
              1024: { slidesPerView: 5, spaceBetweenSlides: 10 }
            },
            pagination:{
              el:".swiper-pagination",
              dynamicBullets: true,
              dynamicMainBullets:2
            }
          });
        },

        // cart counter operation
        increaseCartQuantity() {
          const oldQuantity = this.quantityToAddInCart;
          const newQuantity = oldQuantity + 1;
          const maxQuantity = this.selectedVariety.store.balance;
          if (newQuantity > maxQuantity) {
            return;
          }
          this.quantityToAddInCart++;
        },
        decreaseCartQuantity() {
          if (this.quantityToAddInCart > 1) {
            this.quantityToAddInCart--;
          }
        },
        
        setCategoriesTitle() {
          this.categoriesTitle = this.product.categories.map(category => category.title).join(' , ');
        },
        setUniqueAttributes() {
          const attributes = {};
          this.product.varieties.forEach(variety => {
            variety.attributes.forEach(attribute => {
              if (!attributes[attribute.id]) {
                attributes[attribute.id] = {
                  id: attribute.id,
                  style: attribute.style,
                  name: attribute.name,
                  label: attribute.label,
                  values: []
                };
              }
              if (!attributes[attribute.id].values.includes(attribute.pivot.value)) {
                attributes[attribute.id].values.push(attribute.pivot.value);
              }
            });
          });
          this.uniqueAttributes = attributes;
        },
        setActiveAttributes(attributeId, value) {
          this.activeAttributes = this.activeAttributes.filter(
            attr => attr.attributeId !== attributeId
          );
          this.activeAttributes.push({
            attributeId: attributeId,
            attributeValue: value
          });
        },
        checkAttributeIsActive(attributeId, value) {
          return this.activeAttributes.some(
            activeAttribute =>
              activeAttribute.attributeId == attributeId &&
              activeAttribute.attributeValue == value
          );
        },
        checkAttributeIsDisable(attributeId, value) {
          return this.product.varieties.every(variety => {
            const hasAttribute = variety.attributes.some(attr => {
              return attr.id === attributeId && attr.pivot.value === value;
            });
            return hasAttribute ? variety.store.balance === 0 : true;
          });
        },
        getSpecificationValue(specId) {
          const specification = this.product.specifications.find(s => s.id == specId);
          if (specification.type === 'multi_select') {
            return specification.pivot.specification_values.map(sv => sv.value).join(' , ');
          } else if (specification.type === 'select') {
            return specification.pivot.specification_value.value;
          } else {
            specification.pivot.value;
          }
        },

        // add to cart
        addToCart() {
          if (this.isLoggin) {
            this.addVarietyToDBCart();
          }else {
            this.addVarietyToCookieCart();
          }
        },
        addVarietyToCookieCart() {

          const varietyId = this.selectedVariety.id;
          const quantity = this.quantityToAddInCart;
          const cookie = this.getCartCookie();
          const cart = cookie.find(cart => cart.variety_id == varietyId);  

          if (cart) {
            cart.quantity = parseInt(cart.quantity) + parseInt(quantity);
          }else {
            cookie.push({ variety_id: varietyId, quantity: quantity });
          }

          const expireDate = new Date();
          expireDate.setDate(expireDate.getDate() + 7);
          document.cookie = `cartCookie=${encodeURIComponent(JSON.stringify(cookie))}; path=/; expires=${expireDate.toUTCString()};`;

          this.openModal('.modal-cart');
        },
        getCartCookie() {
          let cookieArr = document.cookie.split(";");  
          for (let i = 0; i < cookieArr.length; i++) {  
            let cookiePair = cookieArr[i].split("=");  
            if (cookiePair[0].trim() == 'cartCookie') {  
              return JSON.parse(decodeURIComponent(cookiePair[1]));  
            }  
          }  
          return [];
        },
        async addVarietyToDBCart() {
          try {

            const url = @json(route('customer.carts.add'));
            const formData = new FormData();

            formData.append('variety_id', this.selectedVariety.id);
            formData.append('quantity', this.quantityToAddInCart);

            const options = {
              method: 'POST',
              headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token())
              },
              body: formData,
            };

            const response = await fetch(url, options);
            const result = await response.json();

            if (response.ok) {
              console.log(result);
              this.openModal('#added-to-cart-modal');
            }

          } catch (error) {
            console.error('error:', error);
          }
        },
      
        // comment - specifications - description
        deactiveAllTitles() {
          const listChildren = document.querySelector('.second-section-list').children;
          const contentChildren = document.querySelector('.second-section-content').children;
          for (const child of listChildren)
            child.classList.remove('active');
          for (const child of contentChildren)
            child.classList.remove('active');
        },
        activeTitle(title) {
          document.querySelector(`.${title}-title`).classList.add('active');
          document.querySelector(`.${title}`).classList.add('active');
        },
        showComments() {
          this.deactiveAllTitles();
          this.activeTitle('comments');
        },
        showSpecifications() {
          this.deactiveAllTitles();
          this.activeTitle('specifications');
        },
        showDescription() {
          this.deactiveAllTitles();
          this.activeTitle('description');
        },
        toggleCommentForm() {
          document.querySelector('#comment-from').classList.toggle('show');
        },
        async storeNewComment() {
          try {

            const url = @json(route('product-comments.store'));
            const formData = new FormData();

            formData.append('product_id', this.product.id);
            formData.append('rate', 5);
            formData.append('title', this.commentData.title);
            formData.append('body', this.commentData.body);
            formData.append('show_customer_name', this.commentData.showName ? 1 : 0);

            const options = {
              method: 'POST',
              headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token())
              },
              body: formData,
            };

            const response = await fetch(url, options);
            const result = await response.json();

            if (!response.ok && response.status == 422)
              this.showValidationError(result.errors);
            else if (response.ok && response.status == 200) 
              this.popup('success', 'عملیات موفق', result.message);

          } catch (error) {
            console.error('error:', error);
          }
        },
      },
      computed: {
        selectedVariety() {
          const matchedVarieties = this.product.varieties.filter(variety => {
            return this.activeAttributes.every(activeAttr => {
              return variety.attributes.some(attribute => {
                return (
                  attribute.id == activeAttr.attributeId &&
                  attribute.pivot.value == activeAttr.attributeValue
                );
              });
            });
          });
          this.quantityToAddInCart = 1;
          return matchedVarieties.length === 1 ? matchedVarieties[0] : {};
        },
      }
    }).mount('#main-content');

  </script>
  
@endsection