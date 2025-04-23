@extends('front-layouts.master')

@section('content')
  <main id="main-home" class="main-home container-2xl px-4 px-lg-0 px-md-8 px-3xl-0 pb-12 mt-lg-3 mt-5">
    @include('home::includes.slider')
    @include('home::includes.special-categories')
    @include('home::includes.products')
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
      },  
      data() {
        return {
          recommendationGroups: @json($recommendationGroups)
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
        }
      }
    }).mount('#main-home');
  </script> 
@endsection
