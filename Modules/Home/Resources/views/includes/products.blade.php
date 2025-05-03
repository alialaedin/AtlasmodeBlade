<section v-for="group in recommendationGroups" :key="recommendationGroups.id" class="most-saled d-flex flex-column gap-lg-6 gap-7 mt-12">
  <!-- Title -->
  <div class="d-flex align-items-center justify-content-between">
    <h2 class="h4-strong color-gray-900">@{{ group.label }} های نارین سنتر</h2>
    <a 
      :href="'/products?sort=' + group.name" 
      class="see-more pb-1 text-medium-strong color-gray-900">
      مشاهده بیشتر
    </a>
  </div>
  <div class="swiper products-swiper bg-gray-200 p-2">
    <div class="swiper-wrapper">
      <div v-for="product in group.products" :key="product.id" class="swiper-slide">
        <article class="product-cart">
          <a :href="'/products/' + product.id" class="bg-gray-100 d-flex flex-column overflow-hidden position-relative">
            <div class="hover-buttons d-flex flex-column gap-2 justify-content-center position-absolute">
              <button type="button" class="like-btn">
                <i class="icon-heart icon-fs-medium-2"></i>
                <svg class="heart-red" width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-v-10f8db86=""><path fill-rule="evenodd" clip-rule="evenodd" d="M15.8498 2.50071C16.4808 2.50071 17.1108 2.58971 17.7098 2.79071C21.4008 3.99071 22.7308 8.04071 21.6198 11.5807C20.9898 13.3897 19.9598 15.0407 18.6108 16.3897C16.6798 18.2597 14.5608 19.9197 12.2798 21.3497L12.0298 21.5007L11.7698 21.3397C9.4808 19.9197 7.3498 18.2597 5.4008 16.3797C4.0608 15.0307 3.0298 13.3897 2.3898 11.5807C1.2598 8.04071 2.5898 3.99071 6.3208 2.76971C6.6108 2.66971 6.9098 2.59971 7.2098 2.56071H7.3298C7.6108 2.51971 7.8898 2.50071 8.1698 2.50071H8.2798C8.9098 2.51971 9.5198 2.62971 10.1108 2.83071H10.1698C10.2098 2.84971 10.2398 2.87071 10.2598 2.88971C10.4808 2.96071 10.6898 3.04071 10.8898 3.15071L11.2698 3.32071C11.3616 3.36968 11.4647 3.44451 11.5538 3.50918C11.6102 3.55015 11.661 3.58705 11.6998 3.61071C11.7161 3.62034 11.7327 3.63002 11.7494 3.63978C11.8352 3.68983 11.9245 3.74197 11.9998 3.79971C13.1108 2.95071 14.4598 2.49071 15.8498 2.50071ZM18.5098 9.70071C18.9198 9.68971 19.2698 9.36071 19.2998 8.93971V8.82071C19.3298 7.41971 18.4808 6.15071 17.1898 5.66071C16.7798 5.51971 16.3298 5.74071 16.1798 6.16071C16.0398 6.58071 16.2598 7.04071 16.6798 7.18971C17.3208 7.42971 17.7498 8.06071 17.7498 8.75971V8.79071C17.7308 9.01971 17.7998 9.24071 17.9398 9.41071C18.0798 9.58071 18.2898 9.67971 18.5098 9.70071Z" data-v-10f8db86="" fill="#ee1212"></path></svg>
              </button>
            </div>
            <figure class="product-img overflow-hidden position-relative">
              <img 
                class="main-img w-p-100 h-p-100" 
                loading="lazy" 
                :src="product.main_image.url" 
                :alt="product.title"
              />
              <img 
                class="hover-img w-p-100 h-p-100 hidden position-absolute top-0 start-0" 
                loading="lazy" 
                :src="product.main_image.url" 
                :alt="product.title"
              />
              <button type="button" class="see-more-product text-nowrap text-center position-absolute bg-white radius-small ps-2 pe-1 py-1 text-medium">
                مشاهده بیشتر
              </button>
            </figure>
            <div class="product-details d-flex flex-column px-2 mt-2">
              <!-- Title -->
              <h5 class="text-medium-2-strong color-gray-900 text-truncate">@{{ product.title }}</h5> 
              <div class="d-flex flex-wrap gap-lg-1 align-items-center">
                <!-- Price -->
                <div class="d-flex gap-1 align-items-center">
                  <ins class="currency text-medium-2 color-primary-500">@{{ product.final_price.amount }}</ins>
                  <span class="text-medium color-gray-800">تومان </span>
                </div>
                <template v-if="product.final_price.discount > 0">
                  <!-- Discount Price -->
                  <div class="d-flex align-items-center color-gray-700">
                    <i class="icon-angle-double-right icon-fs-small pb-1"></i>
                    <s class="text-medium  currency">@{{ product.final_price.base_amount }}</s>
                  </div>
                  <!-- Discount Percent -->
                  <span class="px-2 radius-u text-button-1 bg-secondary-100">
                    @{{ product.final_price.discount.toLocaleString() }}
                    <template v-if="product.final_price.discount_type === 'flat'"> تومان</template>
                    <template v-else> %</template>
                  </span> 
                  <div></div>
                </template>
              </div>
            </div>
          </a>
        </article>
      </div>
    </div>
  </div>
</section> 
