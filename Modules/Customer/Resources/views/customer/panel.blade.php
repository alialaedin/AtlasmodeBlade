@extends('front-layouts.master')

@section('content')
  <main class="user-panel container-2xl px-4 px-md-8 px-3xl-0 mt-lg-0 pt-9 pb-5 d-flex flex-lg-row flex-column gap-2">

    <aside class="panel-side bg-white position-lg-sticky d-flex flex-column border-gray-300 col-lg-3 radius-medium pt-4">

      <!-- User Informations -->
      <div class="d-flex flex-column px-2 mb-1">
        <span class="text-medium-3">@{{ customer.full_name }}</span>
        <div class="d-flex justify-content-between align-items-center">
          <span class="color-gray-700 text-medium">@{{ customer.mobile }}</span>
        </div>
      </div>

      <!-- Wallet Information -->
      <div class="d-flex justify-content-between border-b-gray-400 pb-1 px-2">
        <span class="text-medium">موجودی کیف پول:</span>
        <div class="d-flex gap-1 align-items-baseline">
          <span class="text-button-2 currency color-gray-700">@{{ customer.wallet.balance }}</span>
          <span class="text-button-2 color-gray-700">تومان</span>
        </div>
        <button 
          type="button" 
          data-modal="wallet-modal"
          class="bg-black color-white radius-circle d-flex justify-content-center align-items-center w-4 h-4">
          <i class="icon-plus icon-fs-small"></i>
        </button>
      </div>

      <ul class="lists d-flex flex-column px-lg-1">
        <!-- User Informations -->
        <button type="button" data-btn="user" class="select item d-flex gap-1 py-2 border-b-gray-400 px-1">
          <i class="icon-user1 icon-fs-medium"></i>
          <span class="text-medium">اطلاعات حساب کاربری</span>
        </button>
        <!-- Address Informations -->
        <button type="button" data-btn="address" class="item d-flex gap-2 align-items-center py-2 px-1 border-b-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><symbol xmlns="http://www.w3.org/2000/svg" id="56032a17-76c7-49ea-ba79-e1b6d07c442d" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M13 1h-2v2.5H5a1 1 0 00-.928 1.371L4.923 7l-.851 2.129A1 1 0 005 10.5h6v1H6a1 1 0 00-.928.629l-1 2.5a1 1 0 000 .742l1 2.5A1 1 0 006 18.5h5v4h2v-4h6a1 1 0 00.928-1.371L19.078 15l.851-2.129A1 1 0 0019 11.5h-6v-1h5a1 1 0 00.928-.629l1-2.5a1 1 0 000-.742l-1-2.5A1 1 0 0018 3.5h-5V1zM6.928 6.629L6.477 5.5h10.846l.6 1.5-.6 1.5H6.477l.451-1.129a1 1 0 000-.742zM6.677 13.5h10.846l-.451 1.129a1 1 0 000 .742l.451 1.129H6.677l-.6-1.5.6-1.5z" clip-rule="evenodd"></path></symbol><use href="#56032a17-76c7-49ea-ba79-e1b6d07c442d"></use></svg>
          <span class="text-medium">لیست آدرس ها</span>
        </button>
        <!-- Orders Informations -->
        <button type="button" data-btn="orders" class="item d-flex gap-2 py-2 px-1 border-b-gray-400 align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="18" height="18" viewBox="0 0 1024 1024">
            <g id="icomoon-ignore"></g>
            <path fill="#000" d="M682.668 970.664h-341.335c-155.733 0-245.333-89.6-245.333-245.332v-426.669c0-155.733 89.6-245.333 245.333-245.333h341.335c155.732 0 245.332 89.6 245.332 245.333v426.669c0 155.732-89.6 245.332-245.332 245.332zM341.333 117.329c-122.026 0-181.333 59.307-181.333 181.333v426.669c0 122.024 59.307 181.332 181.333 181.332h341.335c122.024 0 181.332-59.308 181.332-181.332v-426.669c0-122.026-59.308-181.333-181.332-181.333h-341.335z"/>
            <path fill="#000" d="M789.336 394.667h-85.332c-64.856 0-117.336-52.48-117.336-117.334v-85.333c0-17.493 14.508-32 32-32 17.496 0 32 14.507 32 32v85.333c0 29.44 23.896 53.334 53.336 53.334h85.332c17.492 0 32 14.506 32 32s-14.508 32-32 32z"/>
            <path fill="#000" d="M511.995 586.668h-170.666c-17.493 0-32-14.504-32-32 0-17.492 14.507-32 32-32h170.666c17.496 0 32 14.508 32 32 0 17.496-14.504 32-32 32z"/>
            <path fill="#000" d="M682.664 757.332h-341.334c-17.493 0-32-14.508-32-32 0-17.496 14.507-32 32-32h341.334c17.492 0 32 14.504 32 32 0 17.492-14.508 32-32 32z"/>
          </svg>
          <span class="text-medium">سفارش ها</span>
        </button>
        <!-- Favorites -->
        <button type="button" data-btn="favorites" class="item d-flex gap-2 px-1 align-items-center py-2 border-b-gray-400">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path  fill-rule="evenodd" clip-rule="evenodd" d="M11.761 20.8538C9.5904 19.5179 7.57111 17.9456 5.73929 16.1652C4.45144 14.8829 3.47101 13.3198 2.8731 11.5954C1.79714 8.25031 3.05393 4.42083 6.57112 3.28752C8.41961 2.69243 10.4384 3.03255 11.9961 4.20148C13.5543 3.03398 15.5725 2.69398 17.4211 3.28752C20.9383 4.42083 22.2041 8.25031 21.1281 11.5954C20.5302 13.3198 19.5498 14.8829 18.2619 16.1652C16.4301 17.9456 14.4108 19.5179 12.2402 20.8538L12.0051 21L11.761 20.8538Z" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path  d="M15.7393 7.05301C16.8046 7.39331 17.5615 8.34971 17.6561 9.47499" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
          <span class="text-medium">لیست علاقه مندی ها</span>
        </button>
        <!-- Wallet -->
        <button type="button" data-btn="wallet" class="item d-flex gap-2 px-1 align-items-center py-2 border-b-gray-400">
          <svg data-v-2aa0890d="" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-1"><path data-v-2aa0890d="" fill-rule="evenodd" clip-rule="evenodd" d="M7.78489 2.5H16.2142C19.4044 2.5 22 5.15478 22 8.41891V15.5811C22 18.8452 19.4044 21.5 16.2142 21.5C15.8462 21.5 15.5476 21.1944 15.5476 20.8179C15.5476 20.4414 15.8462 20.1358 16.2142 20.1358C18.6693 20.1358 20.6667 18.0931 20.6667 15.5811V9.86499H17.3831C16.3049 9.8659 15.4258 10.7645 15.4249 11.8686C15.4258 12.9727 16.3049 13.8713 17.3831 13.8722H18.7476C19.1156 13.8722 19.4142 14.1778 19.4142 14.5543C19.4142 14.9308 19.1156 15.2364 18.7476 15.2364H17.3831C15.5689 15.2355 14.0924 13.7248 14.0916 11.8686C14.0924 10.0123 15.5689 8.50168 17.3831 8.50077H20.6667V8.41891C20.6667 5.90692 18.6693 3.86422 16.2142 3.86422H7.78489C5.80622 3.86422 4.14578 5.19934 3.56711 7.02831H12.3547C12.7227 7.02831 13.0213 7.3339 13.0213 7.71043C13.0213 8.08786 12.7227 8.39254 12.3547 8.39254H3.336C3.336 8.39709 3.33533 8.40141 3.33467 8.40573C3.334 8.41004 3.33333 8.41436 3.33333 8.41891V15.5811C3.33333 18.0931 5.32978 20.1358 7.78489 20.1358H12.0258C12.3938 20.1358 12.6924 20.4414 12.6924 20.8179C12.6924 21.1944 12.3938 21.5 12.0258 21.5H7.78489C4.59467 21.5 2 18.8452 2 15.5811V8.41891C2 5.15478 4.59467 2.5 7.78489 2.5ZM16.861 11.8071C16.861 11.4306 17.1596 11.125 17.5276 11.125H17.8308C18.1988 11.125 18.4974 11.4306 18.4974 11.8071C18.4974 12.1836 18.1988 12.4892 17.8308 12.4892H17.5276C17.1596 12.4892 16.861 12.1836 16.861 11.8071Z" fill="#444"></path></svg>
          <span class="text-medium">کیف پول</span>
        </button>
        <!-- Exit -->
        <button type="button" data-modal="exit" class="exit-btn d-flex gap-2 px-1 align-items-center py-2">
          <svg class="ml-2" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" ><path d="M15.016 7.38951V6.45651C15.016 4.42151 13.366 2.77151 11.331 2.77151H6.45597C4.42197 2.77151 2.77197 4.42151 2.77197 6.45651V17.5865C2.77197 19.6215 4.42197 21.2715 6.45597 21.2715H11.341C13.37 21.2715 15.016 19.6265 15.016 17.5975V16.6545" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" ></path><path d="M21.8095 12.0214H9.76849" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" ></path><path d="M18.8812 9.10632L21.8092 12.0213L18.8812 14.9373" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" ></path></svg>
          <span class="text-medium">خروج از حساب کاربری</span>
        </button>
      </ul>

    </aside>

    <!-- User Information -->
    <section data-id="user" class="active bg-white section user-info col-lg-9 flex-column radius-medium border-gray-300 px-lg-5 py-5">

      <div class="d-flex border-b-gray-400 col-12">
        <!-- First Name & Last Name -->
        <div class="col-6 d-flex flex-column gap-1 p-2">
          <span class="text-medium color-gray-700">نام و نام خانوادگی</span>
          <div class="d-flex justify-content-between text-medium">
            <span>@{{ customer.full_name ?? '-' }}</span>
            <!-- Edit -->
            <button type="button" data-modal="user-info" class="edit-info-btn">
              <svg  width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path  fill-rule="evenodd" clip-rule="evenodd" d="M9.3764 20.0279L18.1628 8.66544C18.6403 8.0527 18.8101 7.3443 18.6509 6.62299C18.513 5.96726 18.1097 5.34377 17.5049 4.87078L16.0299 3.69906C14.7459 2.67784 13.1541 2.78534 12.2415 3.95706L11.2546 5.23735C11.1273 5.39752 11.1591 5.63401 11.3183 5.76301C11.3183 5.76301 13.812 7.76246 13.8651 7.80546C14.0349 7.96671 14.1622 8.1817 14.1941 8.43969C14.2471 8.94493 13.8969 9.41792 13.377 9.48242C13.1329 9.51467 12.8994 9.43942 12.7297 9.29967L10.1086 7.21422C9.98126 7.11855 9.79025 7.13898 9.68413 7.26797L3.45514 15.3303C3.0519 15.8355 2.91395 16.4912 3.0519 17.1255L3.84777 20.5761C3.89021 20.7589 4.04939 20.8879 4.24039 20.8879L7.74222 20.8449C8.37891 20.8341 8.97316 20.5439 9.3764 20.0279ZM14.2797 18.9533H19.9898C20.5469 18.9533 21 19.4123 21 19.9766C21 20.5421 20.5469 21 19.9898 21H14.2797C13.7226 21 13.2695 20.5421 13.2695 19.9766C13.2695 19.4123 13.7226 18.9533 14.2797 18.9533Z" fill="#000"></path></svg>
            </button>
          </div>
        </div>
        <!-- National Code -->
        <div class="col-6 d-flex flex-column gap-1 p-2">
          <span class="text-medium color-gray-700">کد ملی</span>
          <div class="d-flex justify-content-between text-medium">
            <span>@{{ customer.national_code ?? "-" }}</span>
            <!-- Edit -->
            <button type="button" data-modal="user-info" class="edit-info-btn">
              <svg  width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path  fill-rule="evenodd" clip-rule="evenodd" d="M9.3764 20.0279L18.1628 8.66544C18.6403 8.0527 18.8101 7.3443 18.6509 6.62299C18.513 5.96726 18.1097 5.34377 17.5049 4.87078L16.0299 3.69906C14.7459 2.67784 13.1541 2.78534 12.2415 3.95706L11.2546 5.23735C11.1273 5.39752 11.1591 5.63401 11.3183 5.76301C11.3183 5.76301 13.812 7.76246 13.8651 7.80546C14.0349 7.96671 14.1622 8.1817 14.1941 8.43969C14.2471 8.94493 13.8969 9.41792 13.377 9.48242C13.1329 9.51467 12.8994 9.43942 12.7297 9.29967L10.1086 7.21422C9.98126 7.11855 9.79025 7.13898 9.68413 7.26797L3.45514 15.3303C3.0519 15.8355 2.91395 16.4912 3.0519 17.1255L3.84777 20.5761C3.89021 20.7589 4.04939 20.8879 4.24039 20.8879L7.74222 20.8449C8.37891 20.8341 8.97316 20.5439 9.3764 20.0279ZM14.2797 18.9533H19.9898C20.5469 18.9533 21 19.4123 21 19.9766C21 20.5421 20.5469 21 19.9898 21H14.2797C13.7226 21 13.2695 20.5421 13.2695 19.9766C13.2695 19.4123 13.7226 18.9533 14.2797 18.9533Z" fill="#000"></path></svg>
            </button>
          </div>
        </div>
      </div>

      <div class="d-flex border-b-gray-400 col-12">
        <!-- Phone Number -->
        <div class="col-6 d-flex flex-column gap-1 p-2">
          <span class="text-medium color-gray-700">شماره موبایل</span>
          <div class="d-flex justify-content-between text-medium">
            <span>@{{ customer.mobile }}</span>
          </div>
        </div>
          <!-- Email -->
        <div class="col-6 d-flex flex-column gap-1 p-2">
          <span class="text-medium color-gray-700">ایمیل</span>
          <div class="d-flex justify-content-between text-medium">
            <span v-if="customer.email" v-text="customer.email"></span>
            <span v-else>-</span>
            <button type="button" data-modal="user-info" class="add-btn">
              <i class="icon-simpleAdd"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Birth Date -->
      {{-- <div class="birth-date col-6 d-flex flex-column gap-1 border-b-gray-400 p-2">
        <span class="text-medium color-gray-700">تاریخ تولد</span>
        <div class="d-flex justify-content-between gap-12 text-medium">
          <span>@{{ birthDate ?? '-' }}</span>
          <!-- Add -->
          <button type="button" data-modal="user-info" class="add-btn">
            <i class="icon-simpleAdd"></i>
          </button>
        </div>
      </div> --}}
      
  </section>

  </main>
@endsection

@section('modals')

<!-- Wallet Modal -->
<div data-id="wallet-modal" class="modal charge-wallet-modal radius-medium d-flex flex-column bg-white gap-5 px-6 py-4">
  <h4 class="text-medium-3 text-center">شارژ کیف پول</h4>
  <form class="wallet-form d-flex flex-column gap-2">
    <span class="text-medium">مبلغ برحسب تومان</span>
    <input type="text" autofocus class="priceinput border-gray-300 p-3 bg-gray-100 radius-small" placeholder="مبلغ را به تومان وارد کنید">
  </form>
  <!-- Portal -->
  <div class="prtal d-flex align-items-center justify-content-between">
    <span class="text-medium">انتخاب درگاه</span>
    <div class="d-flex gap-3">
      <button type="button" class="active portal-item-sep radius-small position-relative">
        <figure>
          <img class="tick position-absolute" src="{{ asset('front-assets/images/cart/tick-box.db941cf5.svg') }}" alt="tick">
          <img src="{{ asset('front-assets/images/cart/sep.png') }}" alt="saman">
        </figure>
      </button>
      <button type="button" class="portal-item-zarinpal position-relative">
        <figure>
          <img class="tick position-absolute" src="{{ asset('front-assets/images/cart/tick-box.db941cf5.svg') }}" alt="tick">
          <img src="{{ asset('front-assets/images/cart/zarinpal.png') }}" alt="zarinpal">
        </figure>
      </button>
    </div>
  </div>
  <button type="button" class="close-modal bg-black color-white text-medium  py-1">افزایش موجودی</button>
</div>

<!-- User Info -->
<div class="modal modal-userInfo radius-medium d-flex flex-column bg-white gap-2 px-6 py-4" data-id="user-info">
  <!-- Header Modal -->
  <div class="header-modal d-flex justify-content-between pb-2">
    <span class="text-medium-3">اطلاعات حساب شخصی</span>
    <button type="button" class="modal-close">
      <i class="icon-cancel icon-fs-small color-gray-700"></i>
    </button>
   </div>
   <form class="newAddress-from grid mb-4 mt-3">
      <div class="d-flex flex-column gap-2 g-col-lg-6 g-col-12">
        <label for="name">نام</label>
        <input type="text" v-model="updateInformation.firstName" id="name" class="p-2 bg-gray-100">
      </div>
      <div class="d-flex flex-column gap-2 g-col-lg-6 g-col-12">
        <label for="last-name">نام خانوادگی</label>
        <input type="text" v-model="updateInformation.lastName" id="last-name" class="p-2 bg-gray-100">
      </div>
      <div class="d-flex flex-column gap-2 g-col-12">
        <label for="email">ایمیل</label>
        <input type="text" v-model="updateInformation.email" id="email" class="p-2 bg-gray-100">
      </div>
      <div class="d-flex flex-column gap-2 g-col-lg-4 g-col-6">
        <label for="national-code">کد ملی</label>
        <input type="text" v-model="updateInformation.nationalCode" id="national-code" class="p-2 bg-gray-100">
      </div>
      {{-- <div class="d-flex flex-column gap-2 g-col-lg-4 g-col-6">
        <label for="birth-date">تاریخ تولد</label>
        <input type="date" v-model="updateInformation.firstName" id="birth-date pdpDefault" pdp-id="pdp-9250357"  class="p-2 pdp-el bg-gray-100">
      </div> --}}
   </form>
  <button 
    @click="updateCustomerInformation"
    type="button" 
    class="save-info-btn bg-black color-white text-medium">
    ثبت اطلاعات
  </button>
</div>

@endsection

@section('scripts')

  <script src="https://unpkg.com/jalali-moment/dist/jalali-moment.browser.js"></script>
  <script src="{{ asset('assets/vue/vue3/vue.global.prod.js') }}"></script>
  <script src="{{ asset('assets/sweetalert2/sweetalert2.js') }}"></script>

  <script>

    const { createApp } = Vue;

    createApp({
      mounted() {
        console.log(this.customer);
        this.setCustomerBirthDate();
      },
      data() {
        return {
          customer: @json($customer),
          birthDate: '',
          updateInformation: {
            firstName: this.customer.first_name,
            lastName: this.customer.last_name,
            email: this.customer.email,
            nationalCode: this.customer.national_code,
            // birthDate: this.customer.birth_date,
          },
        }
      },
      methods: {

        // global methods
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

        setCustomerBirthDate() {
          if (this.customer.birth_date !== null) {
            this.birthDate = moment(this.customer.birth_date).locale('fa').format('YYYY/M/D');
          }
        },

        async updateCustomerInformation() {
          try {

            const url = @json(route('customer.profile.update'));
            const formData = new FormData();

            formData.append('first_name', this.updateInformation.firstName);
            formData.append('last_name', this.updateInformation.lastName);
            formData.append('email', this.updateInformation.email);
            formData.append('national_code', this.updateInformation.nationalCode);

            const options = {
              method: 'PUT',
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
            console.log(result);

          } catch (error) {
            console.error('error:', error);
          }
        },

      },
      computed: {
        
      }
    }).mount('#main-content');

  </script>

  <script>

    function activeSection() {
      const listsBtn = document.querySelectorAll('.lists > :not(:last-child)');
      listsBtn.forEach(button => {
        button.addEventListener('click', function () {
          listsBtn.forEach(btn => btn.classList.remove('select'));
          button.classList.add('select');
          const data = button.getAttribute('data-btn');
          const sections = document.querySelectorAll('.section');
          sections.forEach(section => section.classList.remove('active'));
          const targetSection = document.querySelector(`[data-id="${data}"]`);
          targetSection.classList.add('active');
          window.scrollTo({
            top: targetSection.offsetTop - 100,
            behavior: 'smooth'
          });
        });
      });
    }

    function modal() {
      document.querySelectorAll('[data-modal]').forEach(element => {
        element.addEventListener('click', function () {
          const id = this.getAttribute('data-modal');
          document.querySelector(`.modal[data-id="${id}"]`).classList.add('active');
          document.querySelector('.modal-overlay').classList.add('active');
          document.body.classList.add('no-overflow');
        });
      });

      document.querySelectorAll('.modal-close').forEach(closeBtn => {
        closeBtn.addEventListener('click', function () {
          document.querySelector('.modal-overlay').classList.remove('active');
          document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('active'));
          document.body.classList.remove('no-overflow');
        });
      });

      document.querySelector('.modal-overlay').addEventListener('click', function () {
        document.querySelector('.modal-overlay').classList.remove('active');
        document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('active'));
        document.body.classList.remove('no-overflow');
      });
    }

    $(document).ready(() => {
      activeSection();
      modal();
    });

  </script>

@endsection