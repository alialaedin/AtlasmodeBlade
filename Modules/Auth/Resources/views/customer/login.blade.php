<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">  

  <title>فروشگاه اطلس مد</title>

  <link rel="stylesheet" href="{{ asset('front-assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('front-assets/css/swiper-bundle.min.css') }}"/>
  <link rel="stylesheet" href="{{ asset('front-assets/css/jquery.toast.css') }}"/>

</head>
<body>

  <div class="page-wrapper">
    <main class="main">
      <section class="login-register d-flex justify-content-center">

        <form class="login-register-form mx-md-0 mx-2  active radius-small border-gray-400 px-4 py-6 flex-column  gap-2 bg-white">
          <a href="/" class="mx-auto">
            <figure>
              <img src="{{ asset('front-assets/images/header/logo.9208f443 (1).svg') }}" alt="logo">
            </figure>
          <a>
          <div class="w-p-100 d-flex flex-column gap-2 w-p-100">
            <h2 class="text-medium-2-strong">
              <span>ورود</span>
              <span>|</span>
              <span>ثبت نام</span>
            </h2>
            <div class="w-p-100 d-flex flex-column gap-1">
              <span class="color-gray-700 text-medium">سلام!</span>
              <span class="color-gray-700 text-medium mb-1">لطفا شماره تماس خود را وارد کنید.</span>
              <input type="text" autofocus class="w-p-100 bg-gray-100 number-input p-2 text-medium" required maxlength="11">
            </div>
            <button type="button" class="login-btn mt-5 bg-black color-white text-medium-strong">ورود</button>
          </div>
        </form>

        <form 
          action="{{ route('customer.login') }}" 
          method="POST" 
          class="token-form radius-small mx-md-0 mx-2 px-4 py-6 flex-column border-gray-400 align-items-center gap-2 bg-white"
        >
          @csrf  
          <input hidden id="hidden-mobile-input" name="mobile" value="">
          <div class="d-flex gap-12 align-items-center">
            <button type="button" class="back-btn"><i class="icon-arrow-right2 icon-fs-medium"></i></button>
            <a href="/">
              <figure>
                <img src="{{ asset('front-assets/images/header/logo.9208f443 (1).svg') }}" alt="logo">
              </figure>
            <a>
          </div>
          <div class="d-flex flex-column gap-2 w-p-100">
            <h2 class="h2">کد تایید را وارد کنید</h2>
            <div class="w-p-100 d-flex flex-column gap-1">
              <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex color-gray-700 text-medium gap-1">
                  <span>کد تایید برای شماره </span>
                  <!-- Number -->
                  <span class="mobile-span"></span>
                  <span>ارسال شد.</span>
                </div>
                <!-- Edit -->
                <button type="button" class="edit-btn d-flex gap-1">
                  <svg data-v-18d457be="" width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path data-v-18d457be="" fill-rule="evenodd" clip-rule="evenodd" d="M9.3764 20.0279L18.1628 8.66544C18.6403 8.0527 18.8101 7.3443 18.6509 6.62299C18.513 5.96726 18.1097 5.34377 17.5049 4.87078L16.0299 3.69906C14.7459 2.67784 13.1541 2.78534 12.2415 3.95706L11.2546 5.23735C11.1273 5.39752 11.1591 5.63401 11.3183 5.76301C11.3183 5.76301 13.812 7.76246 13.8651 7.80546C14.0349 7.96671 14.1622 8.1817 14.1941 8.43969C14.2471 8.94493 13.8969 9.41792 13.377 9.48242C13.1329 9.51467 12.8994 9.43942 12.7297 9.29967L10.1086 7.21422C9.98126 7.11855 9.79025 7.13898 9.68413 7.26797L3.45514 15.3303C3.0519 15.8355 2.91395 16.4912 3.0519 17.1255L3.84777 20.5761C3.89021 20.7589 4.04939 20.8879 4.24039 20.8879L7.74222 20.8449C8.37891 20.8341 8.97316 20.5439 9.3764 20.0279ZM14.2797 18.9533H19.9898C20.5469 18.9533 21 19.4123 21 19.9766C21 20.5421 20.5469 21 19.9898 21H14.2797C13.7226 21 13.2695 20.5421 13.2695 19.9766C13.2695 19.4123 13.7226 18.9533 14.2797 18.9533Z" fill="#28a745"></path></svg>
                  <span class="color-gray-700">تصحیح شماره</span>
                </button>
              </div>
              <input type="text" name="sms_token" autofocus class="token-input bg-gray-100 p-2 text-medium">
            </div>
            <div class="d-flex gap-1 mt-2">
              <span class="text-button">ارسال مجدد کد:</span>
              <span class="timer text-button">2:00</span>
              <button type="button" class="send-token-again text-button color-gray-700">درخواست مجدد</button>
            </div>
            <button type="submit" class="token-btn mt-5 bg-black text-center color-white text-medium-strong">تایید</button>
          </div>
        </form>

      </section>
    </main>
  </div>

  <script src="{{ asset('front-assets/js/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('front-assets/js/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('front-assets/js/main.js') }}"></script>
  <script src="{{ asset('front-assets/js/jquery.toast.js') }}"></script>

  <script> loginRegisterPage() </script>
  
  <script>

    function sendMobileToReceiveToken() {

      const loginRegForm = $('.login-register-form');
      const smsTokenForm = $('.token-form');

      const loginBtn = $('.login-btn');
      const mobileInput = $('.number-input');

      const toggleFormClasses = () => {
        loginRegForm.toggleClass('active');
        smsTokenForm.toggleClass('active');
      };

      const toast = (icon, message) => {
        $.toast({
          text: message,
          showHideTransition: 'slide',
          position: 'top-right',
          icon: icon
        })
      }

      loginBtn.click((event) => {

        if(mobileInput.val().length == 11 && mobileInput.val().trim() != null){

          const url = @json(route('customer.send-token'));
          const csrfToken = $('meta[name="csrf-token"]').attr('content');
          const formData = new FormData();  

          formData.append('_token', @json(csrf_token())); 
          formData.append('mobile', mobileInput.val());

          const options = {
            method: 'POST',
            body: formData,
            accept: 'application/json'
          };

          fetch(url, options).then(response => {  
            if (response.ok && response.status == 200) {  
              toast('success', 'کد فعال سازی برای شما پیامک شد');
              $('.mobile-span').text(mobileInput.val());
              $('#hidden-mobile-input').val(mobileInput.val());
              toggleFormClasses();
              countdown();
            }  
          }).catch(error => {  
            toast('error', 'خطا در بررسی اطلاعات');
          });  
        }else {
          toast('warning', 'تلفن همراه را به درستی وارد کنید');
        }
          
      });
    }

    $(document).ready(() => {
      sendMobileToReceiveToken();
    });
  </script>

</body>
</html>