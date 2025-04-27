@extends('front-layouts.master')

@section('content')
  <main id="main-home" class="main-home container-2xl px-4 px-lg-0 px-md-8 px-3xl-0 pb-12 mt-lg-3 mt-5">
    @include('home::includes.slider')
    @include('home::includes.special-categories')
    @include('home::includes.products')
    @include('home::includes.home-categories-products')
    {{-- @include('home::includes.advertise') --}}
    @include('home::includes.posts')
    @include('front-layouts.includes.mobile-menu')
  </main>
@endsection

@section('scripts')

  <script>
    mainPage()
  </script>

  <script src="{{ asset('assets/vue/vue3/vue.global.prod.js') }}"></script>

  <script>
    const { createApp } = Vue;
    createApp({
      mounted() {
        this.handleSlidersSwiper();
        this.handleProductSwiper();
        this.handleCategoriesProductSwiper();
        // this.showInHomeCategories = this.showInHomeCategories.filter(c => c.products.length > 0);
        console.log(this.showInHomeCategories);
      },  
      data() {
        return {
          recommendationGroups: @json($recommendationGroups),
          showInHomeCategories: @json($showInHomeCategories)
        }
      },
      methods: {
        handleSlidersSwiper() {
          new Swiper(".main-swiper",{
            slidesPerView: "1",
            freeMode:true,
            autoplay:{
              delay: 1500,
            },
            pagination: {
              el: ".swiper-pagination",
              dynamicBullets: true,
            },
          });
        },
        handleProductSwiper() {
          new Swiper(".products-swiper",{
            slidesPerView:4,
            freeMode: true,
            spaceBetween:15,
            autoplay:true,
            breakpoints:{
              200: { slidesPerView: 1.5, spaceBetweenSlides: 10 },
              360: { slidesPerView: 1.8, spaceBetweenSlides: 10 },
              420: { slidesPerView: 2, spaceBetweenSlides: 10 },
              640: { slidesPerView: 3, spaceBetweenSlides: 10 },
              768: { slidesPerView:3.4, spaceBetweenSlides: 10 },
              1024: { slidesPerView: 4, spaceBetweenSlides: 10 },
            }
          });
        },
        handleCategoriesProductSwiper() {
          new Swiper(".categories-products-swiper",{
            slidesPerView:4,
            freeMode: true,
            spaceBetween:15,
            autoplay:true,
            breakpoints:{
              200: { slidesPerView: 1.5, spaceBetweenSlides: 10 },
              360: { slidesPerView: 1.8, spaceBetweenSlides: 10 },
              420: { slidesPerView: 2, spaceBetweenSlides: 10 },
              640: { slidesPerView: 3, spaceBetweenSlides: 10 },
              768: { slidesPerView:3.4, spaceBetweenSlides: 10 },
              1024: { slidesPerView: 4, spaceBetweenSlides: 10 },
            }
          });
        },
      }
    }).mount('#main-home');
  </script> 
@endsection
