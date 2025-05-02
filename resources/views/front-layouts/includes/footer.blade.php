<footer class="d-flex flex-column pt-10 pb-lg-7 pb-10 container-2xl px-4 px-md-8 px-3xl-0">
  <!-- Subscribe To The Newsletter -->
  <div class="newsletter mb-4 w-p-100 px-2 d-flex flex-lg-row gap-2 flex-column pb-3 justify-content-between align-items-lg-center">
		<div class="col-lg-6 col-12 d-flex flex-column color-gray-900">
			<span class="text-medium">عضویت در خبرنامه</span>
			<p class="text-button-1 d-none d-lg-block">با اشتراک خبرنامه هفتگی ما از تخفیف ها , اخبارها و مطالب با خبر شوید.</p>
		</div>
		<form class="email-form col-lg-6 mb-2 col-12 me-lg-3 mt-lg-8 d-flex align-items-center border-b-gray-400 justify-content-between">
			<i class="icon-mail icon-fs-medium color-gray-600"></i>
			<input type="text" class="flex-grow-1 me-1" placeholder="ایمیل خود را وارد کنید..." />
			<button type="button" class="subscribe-btn py-1 px-2 bg-black color-white text-button-1">عضویت</button>
		</form>
  </div>
  <section class="w-p-100 d-flex px-2 gap-lg-4 gap-2 flex-lg-row flex-column align-items-lg-center">
     <!-- Informations -->
     <address class="col-lg-5 col-11 d-flex flex-column gap-4">
        <!-- Phone & Email -->
        <div class="d-flex flex-column gap-1">
          <!-- Title -->
          <div class="d-flex gap-1 color-gray-900">
              <i class="icon-phone-call icon-fs-medium"></i>
              <span class="text-medium">شماره تماس و ایمیل:</span>
          </div> 
          <div class="d-flex gap-1 text-button-1">
              <!-- Email -->
							@php
								$email = $settings->where('name', 'email')->first()->value;
							@endphp
              <a class="color-gray-900" href="mailto:{{ $email }}">{{ $email }}</a>
              <span>/</span>
              <!-- Phone -->
              <a class="color-gray-900">
								{{ $settings->where('name', 'shop_telephone')->first()->value }}
							</a>
          </div>
        </div>
        <!-- Address -->
        <div class="d-flex flex-column gap-1">
          <!-- Title -->
          <div class="d-flex gap-1 color-gray-900">
              <i class="icon-map-pin icon-fs-medium"></i>
              <span class="text-medium">آدرس فروشگاه:</span>
          </div> 
          <p class="text-button-1 color-gray-900">{{ $settings->where('name', 'shop_address')->first()->value }}</p>
        </div>
     </address>
     <!-- Enamad -->
     <figure class="enamad col-1">
        <img class="w-p-100" src="{{ asset('front-assets/images/footer/enamad.09e76a6a.png') }}" alt="enamad">
     </figure>
     <div class="col-lg-6  col-12 d-flex flex-column gap-4">
          <!-- Logo & Instagram -->
          <div class="d-flex justify-content-between align-items-center border-lg-none border-b-gray-300 pb-lg-0 pb-3">
              <figure class="d-none d-lg-block">
                  <a href="/" class="footer-logo">
                    <img src="{{ Storage::url($settings->where('name', 'logo')->first()->value) }}" alt="footer-logo">
                  </a>
              </figure>
              <!-- Instagram Logo -->
              <a href="" class="instgram-logo mt-lg-0 mt-2">
                 <i class="icon-instagram icon-fs-medium"></i>
              </a>
          </div>
          <!-- Menu -->
          <nav class="d-none d-lg-flex justify-content-between align-items-center pb-3 border-lg-none border-b-gray-300">
              <span>
                  <b class="text-medium-strong color-gray-900">
                      دسترسی سریع:
                  </b>
              </span>
              <ul class="menu-footer d-flex gap-2">
                @foreach (count($menus) && isset($menus['footer']) ? $menus['footer'] : [] as $footerMenuItem)
                    <li>
                        <a 
                            href="{{ $footerMenuItem->link_url }}" 
                            class="text-button-1 color-gray-900"
                            @if($footerMenuItem->new_tab) target="_blank" @endif
                        >
                            {{ $footerMenuItem->title }}
                        </a>
                    </li>
                @endforeach
              </ul>
          </nav>
          <!-- CopyRight -->
          <div class="d-flex flex-lg-row flex-column justify-content-center justify-content-lg-start gap-1 align-items-center">
              <i class="icon-copyright icon-fs-medium-2 d-lg-block d-none"></i>
              <p class="text-button color-gray-600 text-center">تمامی حقوق این سایت مربوط به سایت اطلس‌مد می باشد .</p>
              <span class="text-button color-gray-600 ">
                  طراحی شده:
                  <a href="https://shetabit.com/">
                      <strong class="text-button color-black">شتاب</strong>
                  </a>
              </span>
          </div>
     </div>
  </section>
</footer>