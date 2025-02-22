@if ($mostSaleProducts->isNotEmpty())
<section class="most-saled d-flex flex-column gap-lg-6 gap-7 mt-12">
  <!-- Title -->
  <div class="d-flex align-items-center justify-content-between">
    <h2 class="h4-strong color-gray-900">پرفروشترین های اطلس</h2>
    <a href="./products.html" class="see-more pb-1 text-medium-strong color-gray-900">مشاهده بیشتر</a>
  </div>
  <div class="swiper mostSaled-swiper bg-gray-200">
    <div class="swiper-wrapper">
      @foreach ($mostSaleProducts as $product)
        <div class="swiper-slide">
          <article class="product-cart">
              <a href="./product-detail.html" class="bg-gray-100 d-flex flex-column overflow-hidden position-relative">
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
                <figure class="product-img overflow-hidden position-relative">
                  <img 
                    class="main-img w-p-100 h-p-100" 
                    loading="lazy" 
                    src="{{ asset('assets/images/homePage/product-5125-thumb_lg.jpg') }}" 
                    alt=""
                  />
                  <img 
                    class="hover-img w-p-100 h-p-100 hidden position-absolute top-0 start-0" 
                    loading="lazy" 
                    src="{{ asset('assets/images/homePage/product-5125-thumb_lg (1).jpg') }}" 
                    alt=""
                  />
                  <button type="button" class="see-more-product text-nowrap text-center position-absolute bg-white radius-small ps-2 pe-1 py-1 text-medium">
                    مشاهده بیشتر
                  </button>
                </figure>
                <div class="product-details d-flex flex-column px-2 mt-2">
                    <!-- Title -->
                    <h5 class="text-medium-2-strong color-gray-900 text-truncate">{{ $product->title }}</h5> 
                    <div class="d-flex flex-wrap gap-lg-1 align-items-center">
                      <!-- Price -->
                      <div class="d-flex gap-1 align-items-center">
                        <ins class="currency text-medium-2 color-primary-500">{{ $product->final_price['final_price'] }}</ins>
                        <span class="text-medium color-gray-800">تومان </span>
                      </div>
                      @if ($product->final_price['discount'])
                        <!-- Discount Price -->
                        <div class="d-flex align-items-center color-gray-700">
                          <i class="icon-angle-double-right icon-fs-small pb-1"></i>
                          <s class="text-medium  currency">{{ $product->final_price['unit_price'] }}</s>
                        </div>
                        <!-- Discount Percent -->
                        <span class="px-2 radius-u text-button-1 bg-secondary-100">
                          @if ($product->final_price['discount_type'] === 'flat')
                            {{ number_format($product->final_price['discount']) }} تومان  
                          @else
                            {{ $product->final_price['discount'] }} %
                          @endif
                        </span> 
                        <div></div>
                      @endif
                    </div>
                </div>
              </a>
          </article>
        </div>
      @endforeach
    </div>
  </div>
 </section> 
@endif
