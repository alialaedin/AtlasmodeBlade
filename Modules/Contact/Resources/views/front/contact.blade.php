@extends('front-layouts.master')

@section('content')
<main class="main bg-gray-100">
  <!-- Page Path -->
  <div class="bg-white">
      <div class="page-path container-2xl px-4 px-md-8 px-3xl-0 py-2 d-flex gap-1 align-items-center">
          <i class="icon-home1 icon-fs-medium-2"></i>
          <a href="/" class="text-button-1 mt-1">خانه</a>
          <i class="icon-angle-double-left icon-fs-medium"></i>
          <a href="./contact-us.html" class="text-button-1 mt-1">
             تماس با ما
          </a>
      </div>
  </div>
  <section class="container-2xl mt-9 bg-white py-2 px-4 px-md-8 px-3xl-0 d-flex flex-column gap-2">
     <h1 class="h4-strong color-gray-900">ارتباط با {{ $shopTitle }}</h1>
     <p class="text-button-1 color-gray-700">{{ $aboutUsText }}</p>
     <form action="{{ route('front.comments.store') }}" method="POST" class="contact-us-form grid">
      @csrf
      <input type="text" name="name" class="g-col-lg-4 g-col-12 p-2 border-gray-300 bg-gray-100 text-button-1" placeholder="نام:">
      <input type="text" name="phone_number" class="g-col-lg-4 g-col-12 p-2 border-gray-300 bg-gray-100 text-button-1" placeholder="شماره تماس:">
      <input type="text" name="subject" class="g-col-lg-4 g-col-12 p-2 border-gray-300 bg-gray-100 text-button-1" placeholder="موضوع:">
      <textarea rows="10" name="body" placeholder="پیام:" class="g-col-12 p-2 border-gray-300 bg-gray-100 text-button-1" ></textarea>
      <div class="g-col-12 d-flex flex-lg-row flex-column justify-content-between align-items-lg-center">
        <!-- Write Country Name -->
        <div class="d-flex align-items-center gap-1">
          <svg data-v-f36d028e="" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path data-v-f36d028e="" fill-rule="evenodd" clip-rule="evenodd" d="M11.9999 2.75021C17.1089 2.75021 21.2499 6.89221 21.2499 12.0002C21.2499 17.1082 17.1089 21.2502 11.9999 21.2502C6.89188 21.2502 2.74988 17.1082 2.74988 12.0002C2.74988 6.89221 6.89188 2.75021 11.9999 2.75021Z" stroke="#999" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path data-v-f36d028e="" d="M11.995 8.20432V12.6233" stroke="#999" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path data-v-f36d028e="" d="M11.995 15.7961H12.005" stroke="#999" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
          <span class="text-button-1 color-gray-700">نام کشور iran را به فارسی بنویسید</span>
          <input type="text" name="cn8dsada032" class="border-gray-300 bg-gray-100 p-1 w-12"></input>
        </div>
        <!-- Send Message -->
        <button type="button" class="col-3 bg-black color-white py-2 text-button-1">ارسال پیام</button>
      </div>
     </form>
     <div class="contact-us-infos d-flex flex-column gap-2 px-2 py-3">
        <h4 class="h4-strong color-white">اطلاعات تماس</h4>
        <div class="info d-flex justify-content-between px-2 py-3">
          <div class="d-flex gap-1 text-button-1 color-white">
              <span>شماره تماس ما</span>
              <i class="icon-angle-double-left icon-fs-medium-2"></i>
          </div>
          <div class="d-flex gap-1 text-button-1 align-items-center color-white">
              <span>{{ $mobile }}</span>
              <i class="icon-phone icon-fs-medium-2 color-white"></i>
          </div>
        </div>
        <div class="info d-flex justify-content-between px-2 py-3">
          <div class="d-flex gap-1 text-button-1 color-white">
              <span>ایمیل ارتباطی</span>
              <i class="icon-angle-double-left icon-fs-medium"></i>
          </div>
          <div class="d-flex gap-1 text-button-1 align-items-center color-white">
              <span>{{ $email }}</span>
              <i class="icon-mail icon-fs-medium color-white"></i>
          </div>
        </div>
        <div class="info d-flex flex-lg-row flex-column justify-content-lg-between px-2 py-3">
          <div class="d-flex gap-1 text-button-1 color-white">
              <span>آدرس دفترمرکزی</span>
              <i class="icon-angle-double-left icon-fs-medium"></i>
          </div>
          <div class="d-flex gap-1 text-button-1 align-items-center color-white">
              <span>{{ $address }}</span>
              <i class="icon-map-pin icon-fs-medium color-white"></i>
          </div>
        </div>
     </div>
  </section>
   <!-- Mobile Menu Bottom -->
   @include('front-layouts.includes.mobile-menu')
</main>
@endsection
