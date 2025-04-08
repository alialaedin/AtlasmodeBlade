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
    <main class="main" id="app">
      <section class="login-register d-flex justify-content-center">
        <form 
          @submit.prevent="sendMobileToReceiveToken" 
          class="login-register-form mx-md-0 mx-2 active radius-small border-gray-400 px-4 py-6 flex-column gap-2 bg-white"
        >
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
              <input v-model="mobile" type="text" autofocus class="w-p-100 bg-gray-100 number-input p-2 text-medium" required maxlength="11">
            </div>
            <button type="submit" class="login-btn mt-5 bg-black color-white text-medium-strong">ورود</button>
          </div>
        </form>

        <form 
          @submit.prevent="login"
          class="token-form radius-small mx-md-0 mx-2 px-4 py-6 flex-column border-gray-400 align-items-center gap-2 bg-white"
        >
          <div class="d-flex gap-12 align-items-center">
            <button @click="activeMobileForm" type="button" class="back-btn"><i class="icon-arrow-right2 icon-fs-medium"></i></button>
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
                  <span v-text="mobile"></span>
                  <span>ارسال شد.</span>
                </div>
                <!-- Edit -->
                <button type="button" class="edit-btn d-flex gap-1">
                  <svg data-v-18d457be="" width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path data-v-18d457be="" fill-rule="evenodd" clip-rule="evenodd" d="M9.3764 20.0279L18.1628 8.66544C18.6403 8.0527 18.8101 7.3443 18.6509 6.62299C18.513 5.96726 18.1097 5.34377 17.5049 4.87078L16.0299 3.69906C14.7459 2.67784 13.1541 2.78534 12.2415 3.95706L11.2546 5.23735C11.1273 5.39752 11.1591 5.63401 11.3183 5.76301C11.3183 5.76301 13.812 7.76246 13.8651 7.80546C14.0349 7.96671 14.1622 8.1817 14.1941 8.43969C14.2471 8.94493 13.8969 9.41792 13.377 9.48242C13.1329 9.51467 12.8994 9.43942 12.7297 9.29967L10.1086 7.21422C9.98126 7.11855 9.79025 7.13898 9.68413 7.26797L3.45514 15.3303C3.0519 15.8355 2.91395 16.4912 3.0519 17.1255L3.84777 20.5761C3.89021 20.7589 4.04939 20.8879 4.24039 20.8879L7.74222 20.8449C8.37891 20.8341 8.97316 20.5439 9.3764 20.0279ZM14.2797 18.9533H19.9898C20.5469 18.9533 21 19.4123 21 19.9766C21 20.5421 20.5469 21 19.9898 21H14.2797C13.7226 21 13.2695 20.5421 13.2695 19.9766C13.2695 19.4123 13.7226 18.9533 14.2797 18.9533Z" fill="#28a745"></path></svg>
                  <span class="color-gray-700" @click="activeMobileForm">تصحیح شماره</span>
                </button>
              </div>
              <input type="text" v-model="smsToken" autofocus class="token-input bg-gray-100 p-2 text-medium">
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
  <script src="{{ asset('assets/vue/vue3/vue.global.prod.js') }}"></script>
  <script src="{{ asset('assets/sweetalert2/sweetalert2.js') }}"></script>

  <script>

    const toast = (icon, message) => {
      $.toast({
        text: message,
        showHideTransition: 'slide',
        position: 'top-right',
        icon: icon
      });
    }

    const { createApp } = Vue;

    createApp({
      data() {
        return {
          csrfToken: @json(csrf_token()),
          mobile: '',
          smsToken: ''
        }
      },
      methods: {
        getCartCookie() {
          let cookieArr = document.cookie.split(";");  
          for (let i = 0; i < cookieArr.length; i++) {  
            let cookiePair = cookieArr[i].split("=");  
            if (cookiePair[0].trim() == 'cartCookie') {  
              return JSON.parse(decodeURIComponent(cookiePair[1])) ?? [];  
            }  
          }  
          return [];
        },
        activeMobileForm() {
          document.querySelector('.login-register-form').classList.add('active');
          document.querySelector('.token-form').classList.remove('active');
        },
        activeTokenForm() {
          document.querySelector('.login-register-form').classList.remove('active');
          document.querySelector('.token-form').classList.add('active');
        },
        onSuccessSendingMobile() {
          toast('success', 'کد ورود برای شما ارسال شد');
          this.activeTokenForm();
          this.countdown();
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
        countdown() {
          setInterval(function () {
            let timerElement = document.querySelector('.timer');
            let timer = timerElement.textContent;
            timer = timer.split(':');
            let minutes = parseInt(timer[0]);
            let seconds = parseInt(timer[1]);
            seconds -= 1;

            if (minutes < 0) return;
            else if (seconds < 0 && minutes !== 0) {
              minutes -= 1;
              seconds = 59;
            } else if (seconds < 10 && seconds.toString().length !== 2) {
              seconds = '0' + seconds;
            }

            timerElement.textContent = minutes + ':' + seconds;

            if (minutes === 0 && seconds === 0) {
              timerElement.classList.add('d-none');
              document.querySelector('.send-token-again').classList.add('d-block');
            }
          }, 1000);
        },
        async sendMobileToReceiveToken() {

          const iranianMobileRegex = /^(?:\+98|0)?9\d{9}$/;
          const isValide = iranianMobileRegex.test(this.mobile);

          if (!isValide) {
            toast('error', 'شماره همراه به درستی وار دنشده است');
            return;
          }

          try {

            const url = @json(route('customer.send-token'));
            const formData = new FormData();

            formData.append('_token', this.csrfToken); 
            formData.append('mobile', this.mobile);

            const options = {
              method: 'POST',
              body: formData,
              accept: 'application/json'
            };

            const response = await fetch(url, options);
            const result = await response.json();

            if (!response.ok && response.status == 422) {
              this.showValidationError(result.errors);
            }

            if (response.ok && response.status == 200) {
              this.onSuccessSendingMobile();
            }

          } catch (error) {
            console.error('error:', error);
          }

        },
        async login() {
          try {
            const url = @json(route('customer.login'));
            const cartCookie = this.getCartCookie();
            const formData = new FormData();

            formData.append('_token', this.csrfToken); 
            formData.append('mobile', this.mobile);
            formData.append('sms_token', this.smsToken);

            if (cartCookie.length > 0) {
              formData.append('cookieCarts', JSON.stringify(cartCookie));
            }

            const options = {
              method: 'POST',
              body: formData,
              headers: {
                'Accept': 'application/json',
              },
            };

            const response = await fetch(url, options);
            const result = await response.json();

            if (!response.ok && response.status == 422) {
              this.showValidationError(result.errors);
            }else if (response.ok && response.status == 200) {
              document.cookie = "cartCookie=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
              toast('success', 'احراز هویت با موفقیت انجام شد');
              window.location.replace('/');
            } else {
              toast('error', 'خطا در تلاش برای احراز هویت');
            }

          } catch (error) {
            console.error('error:', error);
          }
        }
      },
    }).mount("#app");

  </script>
  
</body>
</html>