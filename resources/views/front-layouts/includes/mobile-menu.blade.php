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
      <button id="mobile-menu-categories-btn" type="button">
        <i class="icon-category icon-fs-medium-2"></i>
      </button>
      <span class="text-button"> دسته بندی ها</span>
    </li> 

    <li class="d-flex flex-column align-items-center position-relative">
      <a href="{{ route('customer.carts.index') }}">
        <i class="icon-bag icon-fs-medium-2"></i>
      </a>
      <span class="text-button">سبد خرید</span>
      <span class="cart-number carts-count-span position-absolute bg-primary-500 color-white radius-circle h-4 w-4 text-center top-0 start-0"></span>
    </li>  

    <li class="d-flex flex-column align-items-center">
      <a href="{{ route('customer.my-account') }}">
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

@push('scripts')
  <script>
    $(document).ready(() => {
      $('#mobile-menu-categories-btn').click(() => {
        $('.modal[data-id=category]').addClass('active');
        $('.modal-overlay').addClass('active');
        $('body').addClass('no-overflow');
      });
    });
  </script>
@endpush