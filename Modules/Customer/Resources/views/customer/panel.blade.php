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
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="black" viewBox="0 0 24 24"><symbol xmlns="http://www.w3.org/2000/svg" id="56032a17-76c7-49ea-ba79-e1b6d07c442d" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M13 1h-2v2.5H5a1 1 0 00-.928 1.371L4.923 7l-.851 2.129A1 1 0 005 10.5h6v1H6a1 1 0 00-.928.629l-1 2.5a1 1 0 000 .742l1 2.5A1 1 0 006 18.5h5v4h2v-4h6a1 1 0 00.928-1.371L19.078 15l.851-2.129A1 1 0 0019 11.5h-6v-1h5a1 1 0 00.928-.629l1-2.5a1 1 0 000-.742l-1-2.5A1 1 0 0018 3.5h-5V1zM6.928 6.629L6.477 5.5h10.846l.6 1.5-.6 1.5H6.477l.451-1.129a1 1 0 000-.742zM6.677 13.5h10.846l-.451 1.129a1 1 0 000 .742l.451 1.129H6.677l-.6-1.5.6-1.5z" clip-rule="evenodd"></path></symbol><use href="#56032a17-76c7-49ea-ba79-e1b6d07c442d"></use></svg>
          <span class="text-medium">لیست آدرس ها</span>
        </button>
        <!-- Orders Informations -->
        <button type="button" data-btn="orders" class="item d-flex gap-2 py-2 px-1 border-b-gray-400 align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="18" height="18" fill="black" viewBox="0 0 1024 1024"><g id="icomoon-ignore"></g><path fill="#000" d="M682.668 970.664h-341.335c-155.733 0-245.333-89.6-245.333-245.332v-426.669c0-155.733 89.6-245.333 245.333-245.333h341.335c155.732 0 245.332 89.6 245.332 245.333v426.669c0 155.732-89.6 245.332-245.332 245.332zM341.333 117.329c-122.026 0-181.333 59.307-181.333 181.333v426.669c0 122.024 59.307 181.332 181.333 181.332h341.335c122.024 0 181.332-59.308 181.332-181.332v-426.669c0-122.026-59.308-181.333-181.332-181.333h-341.335z"/><path fill="#000" d="M789.336 394.667h-85.332c-64.856 0-117.336-52.48-117.336-117.334v-85.333c0-17.493 14.508-32 32-32 17.496 0 32 14.507 32 32v85.333c0 29.44 23.896 53.334 53.336 53.334h85.332c17.492 0 32 14.506 32 32s-14.508 32-32 32z"/><path fill="#000" d="M511.995 586.668h-170.666c-17.493 0-32-14.504-32-32 0-17.492 14.507-32 32-32h170.666c17.496 0 32 14.508 32 32 0 17.496-14.504 32-32 32z"/><path fill="#000" d="M682.664 757.332h-341.334c-17.493 0-32-14.508-32-32 0-17.496 14.507-32 32-32h341.334c17.492 0 32 14.504 32 32 0 17.492-14.508 32-32 32z"/></svg>
          <span class="text-medium">سفارش ها</span>
        </button>
        <!-- Favorites -->
        <button type="button" data-btn="favorites" class="item d-flex gap-2 px-1 align-items-center py-2 border-b-gray-400">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="black" xmlns="http://www.w3.org/2000/svg"><path  fill-rule="evenodd" clip-rule="evenodd" d="M11.761 20.8538C9.5904 19.5179 7.57111 17.9456 5.73929 16.1652C4.45144 14.8829 3.47101 13.3198 2.8731 11.5954C1.79714 8.25031 3.05393 4.42083 6.57112 3.28752C8.41961 2.69243 10.4384 3.03255 11.9961 4.20148C13.5543 3.03398 15.5725 2.69398 17.4211 3.28752C20.9383 4.42083 22.2041 8.25031 21.1281 11.5954C20.5302 13.3198 19.5498 14.8829 18.2619 16.1652C16.4301 17.9456 14.4108 19.5179 12.2402 20.8538L12.0051 21L11.761 20.8538Z" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path  d="M15.7393 7.05301C16.8046 7.39331 17.5615 8.34971 17.6561 9.47499" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
          <span class="text-medium">لیست علاقه مندی ها</span>
        </button>
        <!-- Wallet -->
        <button type="button" data-btn="wallet" class="item d-flex gap-2 px-1 align-items-center py-2 border-b-gray-400">
          <svg data-v-2aa0890d="" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="0.3" xmlns="http://www.w3.org/2000/svg" class="ml-1"><path data-v-2aa0890d="" fill-rule="evenodd" clip-rule="evenodd" d="M7.78489 2.5H16.2142C19.4044 2.5 22 5.15478 22 8.41891V15.5811C22 18.8452 19.4044 21.5 16.2142 21.5C15.8462 21.5 15.5476 21.1944 15.5476 20.8179C15.5476 20.4414 15.8462 20.1358 16.2142 20.1358C18.6693 20.1358 20.6667 18.0931 20.6667 15.5811V9.86499H17.3831C16.3049 9.8659 15.4258 10.7645 15.4249 11.8686C15.4258 12.9727 16.3049 13.8713 17.3831 13.8722H18.7476C19.1156 13.8722 19.4142 14.1778 19.4142 14.5543C19.4142 14.9308 19.1156 15.2364 18.7476 15.2364H17.3831C15.5689 15.2355 14.0924 13.7248 14.0916 11.8686C14.0924 10.0123 15.5689 8.50168 17.3831 8.50077H20.6667V8.41891C20.6667 5.90692 18.6693 3.86422 16.2142 3.86422H7.78489C5.80622 3.86422 4.14578 5.19934 3.56711 7.02831H12.3547C12.7227 7.02831 13.0213 7.3339 13.0213 7.71043C13.0213 8.08786 12.7227 8.39254 12.3547 8.39254H3.336C3.336 8.39709 3.33533 8.40141 3.33467 8.40573C3.334 8.41004 3.33333 8.41436 3.33333 8.41891V15.5811C3.33333 18.0931 5.32978 20.1358 7.78489 20.1358H12.0258C12.3938 20.1358 12.6924 20.4414 12.6924 20.8179C12.6924 21.1944 12.3938 21.5 12.0258 21.5H7.78489C4.59467 21.5 2 18.8452 2 15.5811V8.41891C2 5.15478 4.59467 2.5 7.78489 2.5ZM16.861 11.8071C16.861 11.4306 17.1596 11.125 17.5276 11.125H17.8308C18.1988 11.125 18.4974 11.4306 18.4974 11.8071C18.4974 12.1836 18.1988 12.4892 17.8308 12.4892H17.5276C17.1596 12.4892 16.861 12.1836 16.861 11.8071Z" fill="#444"></path></svg>
          <span>کیف پول</span>
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

    <!-- Addresses -->
    <section data-id="address" class="address-info bg-white section col-lg-9 flex-column radius-medium border-gray-300 px-lg-5 py-5 px-2">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="text-medium-3 address-title position-relative">آدرس‌ها</h3>
        <button type="button" data-modal="add-address" class="add-address-btn d-flex align-items-center gap-1 color-gray-700 py-1 px-4">
          <i class="icon-map-pin icon-fs-medium"></i>
          <span class="text-medium">افزودن آدرس جدید</span>
        </button>
      </div>
      <!-- Saved Addresses -->
      <div class="saved-addresses d-flex flex-column mt-12 gap-2">
        <template v-if="addresses.length > 0" v-for="address in addresses" :key="address.id">
          <div class="item d-flex flex-column justify-content-between gap-1 pb-4">
            <div class="d-flex justify-content-between position-relative w-p-100 align-items-center">
              <span class="item-address-text text-medium color-gray-900">@{{ address.address }}</span>
              <button @click="popover($event)" type="button" class="edit-delete-btn" data-popover="deleteEdit">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="25" height="20" viewBox="0 0 512 512">
                  <g id="icomoon-ignore"></g>
                  <path fill="#000" d="M341.334 256c0 23.466 19.2 42.666 42.666 42.666s42.666-19.2 42.666-42.666c0-23.466-19.2-42.666-42.666-42.666s-42.666 19.2-42.666 42.666zM256 213.333c-23.466 0-42.666 19.2-42.666 42.667s19.2 42.666 42.666 42.666c23.466 0 42.666-19.2 42.666-42.666s-19.2-42.666-42.666-42.666zM85.334 256c0-23.466 19.2-42.666 42.666-42.666s42.666 19.2 42.666 42.666c0 23.466-19.2 42.666-42.666 42.666s-42.666-19.2-42.666-42.666z"/>
                </svg>
              </button>
              <!-- Popup Delete Or Edit Buttons -->
              <div data-id="deleteEdit" class="popover d-flex popover-deleteEdit radius-small position-absolute flex-column bg-white py-1">
                <button 
                  @click="setAddressDataForEdit(address.id)"
                  type="button" 
                  {{-- data-modal="edit-address-modal" --}}
                  class="edit-address-btn d-flex gap-3 px-2 py-1 align-items-center" 
                >
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path  fill-rule="evenodd" clip-rule="evenodd" d="M9.3764 20.0279L18.1628 8.66544C18.6403 8.0527 18.8101 7.3443 18.6509 6.62299C18.513 5.96726 18.1097 5.34377 17.5049 4.87078L16.0299 3.69906C14.7459 2.67784 13.1541 2.78534 12.2415 3.95706L11.2546 5.23735C11.1273 5.39752 11.1591 5.63401 11.3183 5.76301C11.3183 5.76301 13.812 7.76246 13.8651 7.80546C14.0349 7.96671 14.1622 8.1817 14.1941 8.43969C14.2471 8.94493 13.8969 9.41792 13.377 9.48242C13.1329 9.51467 12.8994 9.43942 12.7297 9.29967L10.1086 7.21422C9.98126 7.11855 9.79025 7.13898 9.68413 7.26797L3.45514 15.3303C3.0519 15.8355 2.91395 16.4912 3.0519 17.1255L3.84777 20.5761C3.89021 20.7589 4.04939 20.8879 4.24039 20.8879L7.74222 20.8449C8.37891 20.8341 8.97316 20.5439 9.3764 20.0279ZM14.2797 18.9533H19.9898C20.5469 18.9533 21 19.4123 21 19.9766C21 20.5421 20.5469 21 19.9898 21H14.2797C13.7226 21 13.2695 20.5421 13.2695 19.9766C13.2695 19.4123 13.7226 18.9533 14.2797 18.9533Z" fill="#000"></path></svg>
                  <span class="text-button">ویرایش</span>
                </button>                                   
                <button 
                  @click="deleteAddress(address.id)"
                  type="button" 
                  class="delete-address-btn d-flex gap-3 px-2 py-1 align-items-center">
                  <i class="icon-trash-2 icon-fs-medium color-error-100"></i>
                  <span class="text-button mt-1">حذف آدرس</span>
                </button>
              </div>
            </div>
            <div class="grid gap-2">
              <!-- City -->
              <div class="g-col-6 d-flex gap-1 text-medium color-gray-700 align-items-center">
                <i class="icon-map icon-fs-medium"></i>
                <span class="item-city">@{{ address.city.name }}</span>
              </div>
              <!-- Name -->
              <div class="g-col-6 d-flex align-items-center gap-1 text-medium color-gray-700 align-items-center">
                <i class="icon-user icon-fs-medium"></i>
                <div>
                  <span class="item-name">@{{ address.firstName }} </span>
                  <span class="item-lastName">@{{ address.lastName }}</span>
                </div>
              </div>
              <!-- Postal Code -->
              <div class="g-col-6 d-flex gap-1 text-medium color-gray-700 align-items-center">
                <i class="icon-mail icon-fs-medium"></i>
                <span class="item-postalCode">@{{ address.postalCode }}</span>
              </div>
              <!-- Phone Number -->
              <div class="g-col-6 d-flex gap-1 align-items-center text-medium color-gray-700 align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="18" height="18" viewBox="0 0 768 768"><g id="icomoon-ignore"></g><path fill="#888888" d="M475.488 191.392c30.176 5.888 55.68 21.856 73.92 43.968 13.44 16.32 22.912 35.968 27.264 57.376 3.488 17.312 20.384 28.512 37.696 25.024s28.512-20.384 25.024-37.696c-6.4-31.744-20.48-61.024-40.608-85.408-27.328-33.152-65.824-57.248-111.040-66.048-17.344-3.392-34.144 7.936-37.536 25.28s7.936 34.144 25.28 37.536zM478.080 63.808c64 7.104 119.776 37.184 160.16 81.408 35.776 39.168 59.456 89.408 65.984 144.608 2.080 17.536 17.984 30.112 35.52 28.032s30.112-17.984 28.032-35.52c-8.096-68.704-37.632-131.392-82.272-180.288-50.496-55.296-120.384-92.992-200.352-101.856-17.568-1.952-33.376 10.72-35.328 28.256s10.72 33.376 28.256 35.328zM736 541.44c0.512-22.848-7.552-44.928-21.536-62.176-14.72-18.112-36.128-30.944-61.6-34.56-25.6-3.136-54.24-10.048-82.752-20.672-13.856-5.088-28.576-6.976-43.008-5.568-21.216 2.048-41.824 11.168-58.208 27.36l-23.040 23.040c-56.64-35.744-107.52-85.344-146.656-146.848l23.232-23.232c10.304-10.56 18.016-23.232 22.624-36.992 6.784-20.224 6.848-42.752-1.248-64.352-9.248-24.096-16.576-52.608-20.608-83.040-3.328-22.848-14.592-43.040-30.816-57.728-17.248-15.552-40.192-24.928-64.864-24.672h-95.872c-2.816 0-5.824 0.128-8.736 0.384-26.368 2.4-49.344 15.296-65.056 34.112s-24.256 43.744-21.856 70.368c9.6 98.432 43.68 199.776 102.912 291.264 48.064 77.216 116.736 147.936 201.536 201.792 82.176 54.304 181.888 91.584 290.752 103.392 2.944 0.288 6.112 0.416 9.216 0.416 26.496-0.096 50.496-10.976 67.776-28.384s27.936-41.504 27.84-67.872zM672 541.44v96c0.032 8.96-3.488 16.96-9.28 22.784s-13.728 9.44-22.592 9.472c-100.768-10.752-190.752-44.512-264.416-93.184-77.696-49.376-139.488-113.184-182.464-182.208-54.304-83.84-84.96-175.392-93.568-263.616-0.768-8.576 2.048-16.832 7.328-23.168s12.896-10.56 21.696-11.36l98.816-0.16c8.672-0.096 16.224 3.008 21.984 8.192 5.44 4.928 9.216 11.712 10.336 19.456 4.544 34.304 13.056 67.744 24.224 96.8 2.592 6.912 2.56 14.304 0.32 21.056-1.568 4.64-4.192 8.992-7.744 12.64l-40.384 40.352c-10.368 10.368-12.128 26.048-5.184 38.432 50.688 89.12 122.848 158.624 204.192 204.096 12.704 7.104 28.224 4.608 38.24-5.312l40.64-40.64c5.312-5.248 12.064-8.224 19.136-8.928 4.864-0.48 9.92 0.16 14.688 1.92 32.704 12.192 66.24 20.352 97.088 24.128 7.712 1.088 14.784 5.312 19.68 11.36 4.704 5.792 7.392 13.184 7.232 21.824z"/></svg>
                <span class="item-number">@{{ address.mobile }}</span>
              </div>
            </div>
          </div>
        </template>
      </div>
    </section>

    <!-- Orders -->
    <section data-id="orders" class="orders-info bg-white section col-lg-9 flex-column gap-2 radius-medium border-gray-300 px-lg-5 py-5 px-2">

      <div class="d-flex flex-column gap-4">
        <h4  class="text-medium-3">تاریخچه سفارشات</h4>
        <ul class="orders-list overflow-auto d-flex gap-md-8 gap-sm-5 gap-1 pb-1 border-b-gray-400 px-1">
          <template v-for="statusObj in orderStatistics" :key="statusObj.name">
            <button 
              @click="showOrdersList($event)"
              type="button" 
              :data-btn="statusObj.name" 
              class="d-flex color-gray-700 align-items-center gap-1 delivered text-medium">
              <span class="text-nowrap">@{{ statusObj.label }}</span>
              <span class="delivered-number text-button bg-gray-300 color-white radius-small">@{{ statusObj.count }}</span>
            </button>
          </template>
        </ul>
      </div>

      <template v-for="statusObj in orderStatistics" :key="statusObj.name">
        <div :data-id="statusObj.name" class="div flex-column gap-3 px-1">
          <template v-for="order in filteredOrdersByStatus[statusObj.name]" :key="order.id">
            <div class="order d-flex flex-column gap-1 border-gray-300 radius-small p-2">
              <div class="d-flex justify-content-between">
                <div class="title d-flex align-items-center gap-3">
                  <span class="text-medium">@{{ statusObj.label }}</span>
                </div>
                <button type="button" :data-order-id="order.id" class="order-history-btn" @click="showOrderDetail($event)">
                  <i class="icon-arrow-left2 icon-fs-small"></i>
                </button>
              </div>
              <!-- Order Info -->
              <div class="d-flex flex-wrap align-items-center gap-1 pb-1 border-b-gray-400">
                <!-- Order Date -->
                <time :datetime="order.persian_created_at" class="color-gray-700">@{{ order.persian_created_at }}</time>
                <i class="icon-dot-single  icon-fs-medium color-gray-700"></i>
                <!-- Order Code -->
                <div class="d-flex align-items-center color-gray-700 gap-1">
                  <span>کد سفارش</span>
                  <span class="color-gray-900 text-medium">@{{ order.id }}</span>                       
                </div>
                <i class="icon-dot-single  icon-fs-medium color-gray-700"></i>
                <!-- Discount -->
                <div class="d-flex align-items-center color-gray-700 gap-1">
                  <span>تخفیف</span>
                  <div class="d-flex align-items-baseline gap-1">
                    <span class="currency color-gray-900 text-medium">@{{ order.discount_amount }}</span>
                    <span>تومان</span>
                  </div>
                </div>
                <i class="icon-dot-single  icon-fs-medium color-gray-700"></i>
                <!-- Price -->
                <div class="d-flex align-items-center color-gray-700 gap-1">
                  <span>مبلغ</span>
                  <div class="d-flex align-items-baseline gap-1">
                    <span class="currency color-gray-900 text-medium">@{{ order.total_amount }}</span>
                    <span>تومان</span>
                  </div>
                </div>
              </div>
              <!-- Order Img -->
              <div class="order-images d-flex gap-2 mt-2">
                <template v-for="item in order.items" :key="item.id">
                  <figure v-if="item.product.main_image != null">
                    <img class="w-p-100 radius-small" :src="item.product.main_image.url" :alt="item.product.title" />
                  </figure>
                </template>
              </div>
            </div>
          </template>
        </div>
      </template>
      
    </section>

    <!-- Orders History -->
    <section v-if="selectedOrderToShowDetail" class="orders-history section bg-white col-lg-9 flex-column gap-2 radius-medium border-gray-300 px-lg-5 py-5">

      <div class="d-flex justify-content-between align-items-center pb-1 pe-2 border-b-gray-400">
        <span class="text-medium-3 address-title position-relative">جزئیات سفارش</span>
        <button type="button" class="backTo-orders-info-btn d-flex align-items-center gap-1 color-primary-500 radius-medium px-4">
          <i class="icon-fleshBottom icon-fs-medium color-gray-900"></i>
        </button>
      </div>

      <div class="d-flex flex-column gap-3 mt-2 pb-2 border-b-gray-400 pe-2">
        <div class="d-flex flex-wrap gap-1 align-items-center pb-2 border-b-gray-400">
          <div class="d-flex align-items-center text-medium color-gray-700 gap-1">
            <span>کد سفارش</span>
            <span class="color-gray-900">@{{ selectedOrderToShowDetail.id }}</span>                       
          </div>
          <i class="icon-dot-single  icon-fs-medium color-gray-700"></i>
          <div class="d-flex align-items-center text-medium color-gray-700 gap-1">
            <span>تاریخ ثبت سفارش</span>
            <time 
              :datetime="selectedOrderToShowDetail.persian_created_at" 
              v-text="selectedOrderToShowDetail.persian_created_at"
              class="color-gray-900">
            </time>
          </div>
        </div>
        <div class="d-flex flex-column gap-1">
          <div class="d-flex flex-wrap gap-1 align-items-center">
            <!-- Name -->
            <div class="d-flex align-items-center text-medium color-gray-700 gap-1">
              <span class="text-medium-strong">تحویل گیرنده:</span>
              <span class="color-gray-900">
                @{{ selectedOrderToShowDetail.address.first_name + ' ' + selectedOrderToShowDetail.address.last_name }}
              </span>                       
            </div>
            <i class="icon-dot-single  icon-fs-medium color-gray-700"></i>
            <!-- Number -->
            <div class="d-flex align-items-center text-medium color-gray-700 gap-1">
              <span class="text-medium-strong">شماره موبایل:</span>
              <span class="color-gray-900"> @{{ selectedOrderToShowDetail.address.mobile }}</span>
            </div>
          </div>
          <!-- Address -->
          <div class="d-flex gap-1 text-medium">
            <span class="color-gray-700 text-medium-strong">آدرس:</span>
            <span class="color-gray-900">@{{ selectedOrderToShowDetail.address.address }}</span>
          </div>
        </div>
      </div>

      <!-- Costs -->
      <div class="d-flex flex-wrap gap-1 mt-1 align-items-center pe-2">
        <!-- Discount -->
        <div class="d-flex align-items-center text-medium color-gray-700 gap-1">
          <span>تخفیف</span>
          <span class="color-gray-900 currency">@{{ selectedOrderToShowDetail.discount_amount }}</span>
          <span>تومان</span>                      
        </div>
        <i class="icon-dot-single  icon-fs-medium color-gray-700"></i>
        <!-- Price -->
        <div class="d-flex align-items-center text-medium color-gray-700 gap-1">
          <span>مبلغ</span>
          <span class="color-gray-900 currency">@{{ selectedOrderToShowDetail.total_amount }}</span>
          <span>تومان</span>                       
        </div>
        <i class="icon-dot-single  icon-fs-medium color-gray-700"></i>
        <!-- Shipping Cost -->
        <div class="d-flex gap-1 text-medium">
          <span class="color-gray-700">هزینه ارسال</span>
          <span class="color-gray-900 currency">@{{ selectedOrderToShowDetail.shipping_amount }}</span>
          <span class="color-gray-700">تومان</span>                       
        </div>
      </div>

      <!-- Products Info -->
      <div class="products-info d-flex flex-column gap-2 px-1 mx-2 mx-lg-0 border-gray-300 radius-small">
        <div v-for="item in selectedOrderToShowDetail.items" :key="item.id" class="product d-flex flex-column p-4">
          <div class="grid gap-0">
            <a class="position-relative" :href="'/products/' + item.product_id">
              <figure>
                <img class="w-p-100 product-img" :src="item.product.main_image?.url" :alt="item.product.title">
              </figure>
              <span class="position-absolute bottom-0 start-0 bg-gray-100 px-1 radius-small">@{{ item.quantity }}</span>
            </a>
            <!-- Product Name , Color ,Size -->
            <div class="d-flex flex-column gap-1 me-3">
              <h4 class="text-medium-2 product-title">@{{ item.product.title }}</h4>
              <div v-for="attribute in item.variety.attributes" class="d-flex gap1 align-items-center gap-1 text-button color-gray-700">
                <span>@{{ attribute.label }}</span>
                <span class="text-medium-strong product-size">@{{ attribute.pivot.value }}</span>
              </div>
            </div>
            <span></span>
            <!-- Price -->
            <div class="d-flex flex-column me-1">
              <!-- Discount -->
              <div v-if="item.discount_amount > 0" class="d-flex gap-1 text-medium align-items-center">
                <span class="currency color-primary-500">@{{ item.discount_amount }}</span>
                <i class="icon-toman icon-fs-small color-primary-500"></i>
                <span class="color-primary-500">تخفیف</span>
              </div>
              <div class="d-flex gap-1 align-items-center">
                <span class="currency text-medium-3 mt-1">@{{ (item.amount - item.discount_amount) * item.quantity }}</span>
                <i class="icon-toman icon-fs-medium"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>

    <!-- Favorites -->
    <section data-id="favorites" class="favorite bg-white section col-lg-9 flex-column radius-medium border-gray-300 px-lg-5 py-5 px-2">
      <h2 class="text-medium-3 ">لیست علاقه مندی ها</h2>
      <div class="favorite-products grid gap-lg-3 gap-2 mt-3">
        <div v-for="product in favorites" :key="product.id" class="g-col-lg-3 g-col-md-6 g-col-12">
          <article>
            <a :href="'/products/' + product.id" class="product-cart d-flex flex-column align-items-center gap-2 bg-gray-200 p-1 position-relative w-p-100 radius-medium">
              <button type="button" @click.prevent="removeFromFavorites(product.id)" class="heart-btn position-absolute">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-v-10f8db86=""><path fill-rule="evenodd" clip-rule="evenodd" d="M15.8498 2.50071C16.4808 2.50071 17.1108 2.58971 17.7098 2.79071C21.4008 3.99071 22.7308 8.04071 21.6198 11.5807C20.9898 13.3897 19.9598 15.0407 18.6108 16.3897C16.6798 18.2597 14.5608 19.9197 12.2798 21.3497L12.0298 21.5007L11.7698 21.3397C9.4808 19.9197 7.3498 18.2597 5.4008 16.3797C4.0608 15.0307 3.0298 13.3897 2.3898 11.5807C1.2598 8.04071 2.5898 3.99071 6.3208 2.76971C6.6108 2.66971 6.9098 2.59971 7.2098 2.56071H7.3298C7.6108 2.51971 7.8898 2.50071 8.1698 2.50071H8.2798C8.9098 2.51971 9.5198 2.62971 10.1108 2.83071H10.1698C10.2098 2.84971 10.2398 2.87071 10.2598 2.88971C10.4808 2.96071 10.6898 3.04071 10.8898 3.15071L11.2698 3.32071C11.3616 3.36968 11.4647 3.44451 11.5538 3.50918C11.6102 3.55015 11.661 3.58705 11.6998 3.61071C11.7161 3.62034 11.7327 3.63002 11.7494 3.63978C11.8352 3.68983 11.9245 3.74197 11.9998 3.79971C13.1108 2.95071 14.4598 2.49071 15.8498 2.50071ZM18.5098 9.70071C18.9198 9.68971 19.2698 9.36071 19.2998 8.93971V8.82071C19.3298 7.41971 18.4808 6.15071 17.1898 5.66071C16.7798 5.51971 16.3298 5.74071 16.1798 6.16071C16.0398 6.58071 16.2598 7.04071 16.6798 7.18971C17.3208 7.42971 17.7498 8.06071 17.7498 8.75971V8.79071C17.7308 9.01971 17.7998 9.24071 17.9398 9.41071C18.0798 9.58071 18.2898 9.67971 18.5098 9.70071Z" data-v-10f8db86="" fill="#ee1212"></path></svg>
              </button>
              <figure class="product-cart-image w-p-100">
                <img class="w-p-100 radius-medium" loading="lazy" :src="product.main_image.url" :alt="product.title">
              </figure>
              <div class="product-details d-flex flex-column px-2 mt-2">
                <!-- Title -->
                <h5 class="text-medium-2-strong color-gray-900 text-truncate">@{{ product.title }}</h5> 
                <div class="d-flex flex-wrap align-items-center">
                  <!-- Price -->
                  <div class="d-flex gap-1 align-items-center">
                    <ins class="currency text-medium-2 color-primary-700">@{{ product.final_price.amount }}</ins>
                    <span class="text-medium color-gray-800"> تومان </span>
                  </div>
                  <!-- Discount Price -->
                  <template v-if="product.final_price.discount > 0">
                    <div class="d-flex align-items-center color-gray-700">
                      <i class="icon-angle-double-right icon-fs-small pb-1"></i>
                      <s class="text-medium currency">@{{ product.final_price.base_amount }}</s>
                    </div>
                    <span class="px-2 radius-u text-button-1 bg-secondary-100">
                      @{{ product.final_price.discount }}
                      <span v-if="product.final_price.discount_type == 'percentage'">%</span>  
                      <span v-else-if="product.final_price.discount_type == 'flat'">تومان</span>  
                      <span v-else></span>  
                    </span> 
                  </template>
                  <div></div>
                </div>
              </div>
            </a>
          </article>
        </div>
      </div>
    </section>

    <!-- Wallet -->
    <section data-id="wallet" class="wallet bg-white section col-lg-9 flex-column radius-medium border-gray-300 px-lg-5 py-5 px-2">

      <div class="d-flex flex-column px-5 py-2 border-gray-300 radius-medium">
        <span class="text-medium-strong w-p-100 border-b-gray-400 pb-1">کیف پول</span>
        <div class="d-flex flex-wrap gap-lg-0 gap-1 py-3 justify-content-around align-items-center">
          <!-- Wallent Balance -->
          <div class="d-flex gap-1 text-medium">
            <span class="">موجودی کیف پول</span>
            <!-- Value -->
            <span class="currency">@{{ customer.wallet.balance }}</span>
            <span>تومان</span>
          </div>
          <!-- Transactions Date -->
          <div v-if="transactions.length > 0" class="d-flex gap-1 align-items-baseline color-gray-700">
            <span class="text-button">تاریخ اخرین تراکنش شما:</span>
            <time datetime="{{ verta($customer->transactions->first()?->created_at)->format('Y/m/d') }}">
              {{ verta($customer->transactions->first()?->created_at)->format('%d %B %Y') }}
            </time>
          </div>
          <button type="button" data-modal="wallet-modal" class="px-6 py-1  text-button color-white radius-u bg-primary-700">شارژ کیف پول</button>
        </div>
      </div>

      <!-- Transactions Detail Dekstop -->
      <table class="transactions-detail-desktop d-lg-flex d-none flex-column px-5 py-2 mt-2 gap-4 border-gray-300 radius-medium">
        <thead>
          <tr class="d-flex gap-lg-8 gap-2 text-medium color-gray-700 bp-1 border-b-gray-400 px-lg-2">
            <th>شناسه</th>
            <th>نوع تراکنش</th>
            <th>تاریخ تراکنش</th>
            <th>مبلغ (تومان)</th>
            <th>وضعیت</th>
            {{-- <th>توضیحات</th> --}}
          </tr>
        </thead>
        <tbody>
          <tr v-for="transaction in transactions" :key="transaction.id" class="d-flex gap-lg-11 gap-4 text-button pb-2 border-b-gray-400 px-lg-2">
            <td>@{{ transaction.id }}</td>
            <td v-if="transaction.type == 'deposit'" class="bg-success-100 radius-small color-white p-1">واریز</td>
            <td v-else class="bg-primary-700 radius-small color-white p-1">برداشت</td>
            <td>
              <time :datetime="transaction.jalali_created_at">@{{ transaction.jalali_created_at }}</time>
            </td>
            <td class="currency">@{{ transaction.amount }}</td>
            <td v-if="transaction.confirmed" class="bg-success-100 color-white radius-small p-1">موفق</td>
            <td v-else class="bg-primary-700 color-white radius-small p-1">نا موفق</td>
            {{-- <td class="descrip d-none d-xl-block text-wrap position-absolute">@{{ transaction.meta.description }}</td> --}}
          </tr>
        </tbody>
      </table>

      <!-- Transactions Detail Mobile -->
      <div class="d-lg-none d-flex flex-column gap-2 px-2 py-2 mt-2 border-gray-300 radius-medium">
        <h4 class="h4-strong">تراکنش ها</h4>
        <!-- Withdraw Info -->
        <div v-for="transaction in transactions" :key="transaction.id" class="grid p-2 border-gray-300 radius-medium gap-1">
          <div class="g-col-6 d-flex gap-1 text-button align-items-center">
            <span>نوع تراکنش:</span>
            <span v-if="transaction.type == 'withdraw'" class="transaction color-warning-300 px-1 radius-medium">برداشت</span>
            <span v-else class="transaction color-success-100 px-1 radius-medium">برداشت</span>
          </div>
          <div class="g-col-6 d-flex gap-1 text-button align-items-center">
            <span class="text-nowrap">تاریخ :</span>
            <time :datetime="transaction.jalali_created_at">@{{ transaction.jalali_created_at }}</time>
          </div>
          <div class="g-col-6 d-flex gap-1 text-button align-items-center">
            <span>مبلغ:</span>
            <span class="currency">@{{ Math.abs(transaction.amount) }}</span>
          </div>
          <div class="g-col-6 d-flex gap-1 text-button align-items-center">
            <span>وضعیت:</span>
            <span v-if="transaction.confirmed" class="status color-success-100 px-1 radius-medium">موفق</span>
            <span v-else class="status color-warning-300 px-1 radius-medium">نا موفق</span>
          </div>
          {{-- <span class="g-col-12 text-center color-gray-700">@{{ transaction.meta.description }}</span> --}}
        </div>
      </div>

    </section>

    @include('front-layouts.includes.mobile-menu')

  </main>
@endsection

@section('modals')

<!-- Wallet Modal -->
<div data-id="wallet-modal" class="modal charge-wallet-modal radius-medium d-flex flex-column bg-white gap-5 px-6 py-4">
  <h4 class="text-medium-3 text-center">شارژ کیف پول</h4>
  <form class="wallet-form d-flex flex-column gap-2">
    <span class="text-medium">مبلغ برحسب تومان</span>
    <input v-model="depositWalletAmount" type="text" autofocus class="priceinput border-gray-300 p-3 bg-gray-100 radius-small" placeholder="مبلغ را به تومان وارد کنید">
  </form>
  <!-- Portal -->
  <div class="prtal d-flex align-items-center justify-content-between">
    <span class="text-medium">انتخاب درگاه</span>
    <div class="d-flex gap-3">
      <template v-for="(gateway, gatewayIndex) in gateways" :key="gatewayIndex">
        <button 
          type="button" 
          :portal-name="gateway.name"
          :class="['portal-item position-relative', gatewayIndex === 0 ? 'active' : '']" 
          @click="choosePortal($event)"
        >
          <figure>
            <img class="tick position-absolute" src="{{ asset('front-assets/images/cart/tick-box.db941cf5.svg') }}" alt="tick">
            <img :src="gateway.image" :alt="gateway.name" />
          </figure>
        </button>
      </template>
    </div>
  </div>
  <button @click="depositWallet" type="button" class="close-modal bg-black color-white text-medium  py-1">افزایش موجودی</button>
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
      <div class="d-flex flex-column gap-2 g-col-lg-6 g-col-12">
        <label for="email">ایمیل</label>
        <input type="text" v-model="updateInformation.email" id="email" class="p-2 bg-gray-100">
      </div>
      <div class="d-flex flex-column gap-2 g-col-lg-6 g-col-12">
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
    class="save-info-btn bg-black color-white text-medium modal-close">
    ثبت اطلاعات
  </button>
</div>

<!-- Add New Address -->
<div class="modal modal-add-newAddress radius-medium d-flex flex-column bg-white gap-2 px-6 py-4" data-id="add-address">
  <!-- Header Modal -->
  <div class="header-modal d-flex justify-content-between pb-2">
    <span class="text-medium-3">افزودن آدرس پستی</span>
    <button type="button" class="modal-close">
      <i class="icon-cancel icon-fs-small color-gray-700"></i>
    </button>
  </div>
  <form class="newAddress-from grid gap-1 gap-lg-2 mt-1">
    <div class="d-flex flex-column gap-2 g-col-lg-6 g-col-12">
      <label for="country">استان<span class="color-error-100">*</span></label>
      <select id="country" class="p-2 bg-gray-100" v-model="newAddressData.provinceId">
        <option value="" disabled selected>انتخاب استان</option>
        <option
          v-for="province in provinces"
          v-text="province.name"
          :key="province.id"
          :value="province.id">
        </option>
      </select>
    </div>
    <div class="d-flex flex-column gap-2 g-col-lg-6 g-col-12">
      <label for="city">شهر<span class="color-error-100">*</span></label>
      <select id="city" class="p-2 bg-gray-100" v-model="newAddressData.cityId">
        <option v-if="newAddressData.cities.length == 0" value="" disabled selected>ابتدا استان را انتخاب کنید</option>
        <option
          v-else
          v-for="city in newAddressData.cities"
          v-text="city.name"
          :key="city.id"
          :value="city.id">
        </option>
      </select>
    </div>
    <div class="d-flex flex-column gap-2 g-col-12">
      <label for="address-input">آدرس کامل پستی<span class="color-error-100">*</span></label>
      <input type="text" v-model="newAddressData.address" id="address-input" required minlength="10" class="p-2 bg-gray-100">
    </div>
    <div class="d-flex flex-column gap-2 g-col-12">
      <label for="postal-code">کد پستی<span class="color-error-100">*</span></label>
      <input type="text" v-model="newAddressData.postalCode" id="postal-code" required  class="p-2 bg-gray-100">
    </div>
    <div class="grid gap-2 pt-2 g-col-12">
      <div class="d-flex flex-column gap-2 g-col-12 g-col-lg-6">
        <label for="name">نام گیرنده<span class="color-error-100">*</span></label>
        <input type="text" v-model="newAddressData.firstName" id="name" required class="p-2 bg-gray-100">
      </div>
      <div class="d-flex flex-column gap-2 g-col-12 g-col-lg-6">
        <label for="last-name">نام‌خانوادگی گیرنده<span class="color-error-100">*</span></label>
        <input type="text" v-model="newAddressData.lastName" id="last-name" required  class="p-2 bg-gray-100">
      </div>
      <div class="d-flex flex-column gap-1 g-col-12 g-col-lg-6">
        <label for="phone-number">شماره موبایل<span class="color-error-100">*</span></label>
        <input type="text" v-model="newAddressData.mobile" id="phone-number" required maxlength="11" placeholder="مثل: 09123457686" class="p-2 bg-gray-100">
      </div>
    </div>
  </form>
  <button
    @click="storeNewAddress"
    type="button" 
    class="add-newAddress-btn bg-black color-white text-medium">
    ثبت آدرس
  </button>
</div>

<!-- Edit Address -->
<div id="edit-address-modal" class="modal modal-add-newAddress radius-medium d-flex flex-column bg-white gap-2 px-6 py-4">
  <template v-if="editAddressData.addressId != ''">
    <!-- Header Modal -->
    <div class="header-modal d-flex justify-content-between pb-2">
      <span class="text-medium-3"> ویرایش آدرس پستی</span>
      <button type="button" class="modal-close">
        <i class="icon-cancel icon-fs-small color-gray-700"></i>
      </button>
    </div>
    <form class="newAddress-from grid gap-1 gap-lg-2 mt-1">
      <div class="d-flex flex-column gap-2 g-col-lg-6 g-col-12">
        <label>استان<span class="color-error-100">*</span></label>
        <select class="p-2 bg-gray-100" v-model="editAddressData.provinceId" @change="loadCitiesForEditAddress">
          <option
            v-for="province in provinces"
            v-text="province.name"
            :selected="editAddressData.province.id == province.id"
            :key="province.id"
            :value="province.id">
          </option>
        </select>
      </div>
      <div class="d-flex flex-column gap-2 g-col-lg-6 g-col-12">
        <label>شهر<span class="color-error-100">*</span></label>
        <select class="p-2 bg-gray-100" v-model="editAddressData.cityId">
          <option
            v-for="city in editAddressData.province.cities"
            v-text="city.name"
            :selected="editAddressData.cityId == city.id"
            :key="city.id"
            :value="city.id">
          </option>
        </select>
      </div>
      <div class="d-flex flex-column gap-2 g-col-12">
        <label for="address-input-2">آدرس کامل پستی<span class="color-error-100">*</span></label>
        <input type="text" v-model="editAddressData.address" id="address-input-2" required minlength="10" class="p-2 bg-gray-100">
      </div>
      <div class="d-flex flex-column gap-2 g-col-12">
        <label for="postal-code-2">کد پستی<span class="color-error-100">*</span></label>
        <input type="text" v-model="editAddressData.postalCode" id="postal-code-2" required  class="p-2 bg-gray-100">
      </div>
      <div class="grid gap-2 pt-2 g-col-12">
        <div class="d-flex flex-column gap-2 g-col-12 g-col-lg-6">
          <label for="name-2">نام گیرنده<span class="color-error-100">*</span></label>
          <input type="text" v-model="editAddressData.firstName" id="name-2" required class="p-2 bg-gray-100">
        </div>
        <div class="d-flex flex-column gap-2 g-col-12 g-col-lg-6">
          <label for="last-name-2">نام‌خانوادگی گیرنده<span class="color-error-100">*</span></label>
          <input type="text" v-model="editAddressData.lastName" id="last-name-2" required  class="p-2 bg-gray-100">
        </div>
        <div class="d-flex flex-column gap-1 g-col-12 g-col-lg-6">
          <label for="phone-number-2">شماره موبایل<span class="color-error-100">*</span></label>
          <input type="text" v-model="editAddressData.mobile" id="phone-number-2" required maxlength="11" class="p-2 bg-gray-100">
        </div>
      </div>
    </form>
    <button
      @click="updateAddress"
      type="button" 
      class="add-newAddress-btn bg-black color-white text-medium">
      بروزرسانی آدرس
    </button>
  </template>
</div>

<!-- Exit Modal -->
<div class="modal modal-exit radius-medium d-flex flex-column bg-white gap-4 px-6 py-4" data-id="exit">
  <div class="d-flex justify-content-between border-b-gray-400 px-2">
    <h4 class="h4 text-center"> حساب کاربری خارج شوید؟</h4>
    <button type="button" class="modal-close">
      <i class="icon-cancel icon-fs-small"></i>
    </button>
  </div>
  <p class="text-button">با خروج از حساب کاربری, به سبد خرید فعلی خود دسترسی نخواهید داشت.</p>
  <div class="d-flex justify-content-between px-lg-4">
    <button type="button" class="cancel-modal-btn modal-close bg-secondary-300 color-white text-medium radius-medium px-6 py-1">انصراف</button>
    <button 
      @click="logout"
      type="button" 
      class="exit-modal-btn bg-error-100 color-white text-medium px-6 py-1 radius-medium">
      خروج از حساب
    </button>
  </div>
</div>

@endsection

@section('scripts')

  <script src="https://unpkg.com/jalali-moment/dist/jalali-moment.browser.js"></script>
  <script src="{{ asset('assets/vue/vue3/vue.global.prod.js') }}"></script>
  <script src="{{ asset('assets/sweetalert2/sweetalert2.js') }}"></script>

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
        element?.addEventListener('click', function () {
          const id = this.getAttribute('data-modal');
          document.querySelector(`.modal[data-id="${id}"]`).classList.add('active');
          document.querySelector('.modal-overlay').classList.add('active');
          document.body.classList.add('no-overflow');
        });
      });

      // document.querySelectorAll('.modal-close').forEach(closeBtn => {
      //   closeBtn?.addEventListener('click', function () {
      //     document.querySelector('.modal-overlay').classList.remove('active');
      //     document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('active'));
      //     document.body.classList.remove('no-overflow');
      //   });
      // });

      // document.querySelector('.modal-overlay')?.addEventListener('click', function () {
      //   document.querySelector('.modal-overlay').classList.remove('active');
      //   document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('active'));
      //   document.body.classList.remove('no-overflow');
      // });
    }
    function closeAllPopovers() {
      document.addEventListener('click', function() {
        document.querySelectorAll('[data-id]').forEach(el => el.classList.remove('opened'));
      });
    }
  </script>

  <script>

    const { createApp } = Vue;

    createApp({
      mounted() {
        activeSection();
        modal();
        closeAllPopovers();
        this.setCustomerBirthDate();
        this.setCustomerInformationForUpdate();
        this.setFilteredOrders();
        this.setOrderStatistics();
        this.activeLoginBtn();
        this.closingModals();
      },
      data() {
        return {
          customer: @json($customer),
          allOrders: @json($customer->orders),
          provinces: @json($provinces),
          existsAddresses: @json($customer->addresses ?? []),
          gateways: @json($gateways),
          favorites: @json($customer->favorites),
          transactions: @json($customer->transactions),
          depositWalletAmount: '',
          birthDate: '',
          updateInformation: {
            firstName: '',
            lastName: '',
            email: '',
            nationalCode: '',
          },
          newAddressData: {
            provinceId: '',
            cities: [],
            cityId: '',
            firstName: '',
            lastName: '',
            postalCode: '',
            address: '',
            mobile: '',
          },
          editAddressData: {
            addressId: '',
            province: '',
            provinceId: '',
            city: {},
            cityId: '',
            firstName: '',
            lastName: '',
            postalCode: '',
            address: '',
            mobile: '',
          },
          orderStatistics: [],
          filteredOrdersByStatus: {
            canceled: [],
            delivered: [],
            failed: [],
            in_progress: [],
            new: [],
            reserved: [],
            wait_for_payment: [],
          },
          selectedOrderToShowDetail: @json($customer->orders->first()),
        }
      },
      watch: {
        'newAddressData.provinceId'(newProvinceId, oldProvinceId) {
          if (typeof newProvinceId !== 'number') return;
          this.newAddressData.cities = this.provinces.find(p => p.id == newProvinceId)?.cities;
          this.newAddressData.cityId = this.newAddressData.cities[0].id;
        },
      },
      methods: {

        popover(event) {
          event.stopPropagation();
          const id = event.currentTarget.getAttribute('data-popover');
          document.querySelectorAll('[data-id]').forEach(el => el.classList.remove('opened'));
          event.currentTarget.closest('.item').querySelector(`[data-id="${id}"]`).classList.add('opened');
        },
        closingModals() {
          document.querySelector('.modal-overlay').addEventListener('click', () => {
            document.querySelector('.modal-overlay').classList.remove('active');
            document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('active'));
            document.body.classList.remove('no-overflow');
          });
          document.querySelectorAll('.modal-close').forEach(modalCloseButton => {
            modalCloseButton.addEventListener('click', () => {
              document.querySelector('.modal-overlay').classList.remove('active');
              document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('active'));
              document.body.classList.remove('no-overflow');
            });
          })
        },
        activeLoginBtn() {
          const btn = document.querySelector('.login-btn');
          btn.addEventListener('click', function () {
            const child = this.querySelectorAll('div')[1];
            child.classList.toggle('active');
            if (child.classList.contains('active')) {
              this.style.borderBottomLeftRadius = 'unset';
              this.style.borderTopLeftRadius = '18px';
              this.style.borderBottomRightRadius = 'unset';
              this.style.borderTopRightRadius = '18px';
            } else {
              this.style.borderRadius = '100px';
            }
          });
        },
        openModal(selector) {
          document.querySelector(selector).classList.add('active');  
          document.querySelector('.modal-overlay').classList.add('active');
          document.body.classList.add('no-overflow');
        },
        closeModal(selector) {
          document.querySelector(selector).classList.remove('active');  
          document.querySelector('.modal-overlay').classList.remove('active');
          document.body.classList.remove('no-overflow');
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
        popup(type, title, message) {
          Swal.fire({
            title: title,
            text: message,
            icon: type,
            confirmButtonText: "بستن",
          });
        },
        popupWithConfirmCallback(type, message, confirmButtonText, isConfirmedCallback) {
          Swal.fire({
            text: message,
            icon: type,
            confirmButtonText: confirmButtonText,
            showDenyButton: true,
            denyButtonText: "انصراف",
          }).then((result) => {
            if (result.isConfirmed) isConfirmedCallback();
          });
        },

        setCustomerBirthDate() {
          if (this.customer.birth_date !== null) {
            this.birthDate = moment(this.customer.birth_date).locale('fa').format('YYYY/M/D');
          }
        },
        setCustomerInformationForUpdate() {
          this.updateInformation.firstName = this.customer?.first_name;
          this.updateInformation.lastName = this.customer?.last_name;
          this.updateInformation.email = this.customer?.email;
          this.updateInformation.nationalCode = this.customer?.national_code;
        },
        setNewCustomerInformation(requestCustomer) {
          this.customer.first_name = requestCustomer.first_name
          this.customer.last_name = requestCustomer.last_name
          this.customer.national_code = requestCustomer.national_code
          this.customer.email = requestCustomer.email
        },
        setNewAddress(requestAddress) {
          this.existsAddresses.unshift(requestAddress);
        },
        setAddressDataForEdit(addressId) {
          const address = this.addresses.find(a => a.id == addressId);
          this.editAddressData.addressId = addressId;
          this.editAddressData.province = address.province;
          this.editAddressData.provinceId = address.province.id;
          this.editAddressData.city = address.city;
          this.editAddressData.cityId = address.cityId;
          this.editAddressData.firstName = address.firstName;
          this.editAddressData.lastName = address.lastName;
          this.editAddressData.postalCode = address.postalCode;
          this.editAddressData.address = address.address;
          this.editAddressData.mobile = address.mobile;
          this.openModal('#edit-address-modal');
        },
        setFilteredOrders() {
          Object.keys(this.filteredOrdersByStatus).forEach(status => {
            this.filteredOrdersByStatus[status] = this.allOrders?.filter(o => o.status === status) ?? [];
          });
        },
        setOrderStatistics() {

          const orderStatistics = @json($orderStatistics);
          const orderStatusTranslate = {
            canceled: 'کنسل شده',
            delivered: 'ارسال شده',
            failed: 'خطا',
            in_progress: 'در انتطار تکمیل',
            new: 'جدید',
            reserved: 'رزرو شده',
            wait_for_payment: 'در انتظار پرداخت',
          };

          Object.keys(orderStatistics).forEach(status => {
            this.orderStatistics.push({
              name: status,
              label: orderStatusTranslate[status],
              count: orderStatistics[status],
            })
          });

        },

        choosePortal(event) {
          const selectedPortalItem = event.currentTarget;
          document.querySelectorAll('.portal-item').forEach(portalItem => {
            if (portalItem.classList.contains('active')) {
              portalItem.classList.remove('active');
            }
          });
          selectedPortalItem.classList.add('active');
        },
        loadCitiesForEditAddress() {
          const province = this.provinces.find(p => p.id == this.editAddressData.provinceId);
          this.editAddressData.province = province;
          this.editAddressData.city = province.cities[0];
          this.editAddressData.cityId = province.cities[0].id;
        },
        showOrdersList(event) {
          
          const button = event.currentTarget;
          const listsBtns = document.querySelectorAll('.orders-list button');
          const data = button.getAttribute('data-btn');
          
          button.classList.add('active');
          document.querySelectorAll('.orders-list button').forEach(btn => btn.classList.remove('active'));
          document.querySelectorAll('.div').forEach(div => div.classList.remove('active'));
          document.querySelector(`[data-id="${data}"]`).classList.add('active');
        },
        showOrderDetail(event) {

          const btn = event.currentTarget;
          const orderStatus = btn.closest('.div').getAttribute('data-id');
          const orderId = btn.getAttribute('data-order-id');
          const order = this.filteredOrdersByStatus[orderStatus].find(o => o.id == orderId);

          this.selectedOrderToShowDetail = order;

          document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
          document.querySelector('.section.orders-history').classList.add('active');

        },

        async request(url, method, data = null, onSuccessRequest) {

          let options = {
            method: method,
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': @json(csrf_token())
            },
          };

          if (data === null) {
            options.headers['Content-Type'] = 'application/json';
          } else {
            options.body = data instanceof FormData ? data : JSON.stringify(data);
          }

          const response = await fetch(url, options);
          const result = await response.json();

          if (!response.ok) {
            switch (response.status) {
              case 422:
                this.showValidationError(result.errors);
                break;
              case 404:
                this.popup('error', 'خطای 404', 'چنین چیزی وجود ندارد');
                break;
              case 409:
                this.popup('error', '', result.message);
                break;
              case 500:
                this.popup('error', 'خطای سرور', result.message);
                break;
              default: 
                this.popup('error', 'خطای نا شناخته');
                break;
            }
            return;
          }

          onSuccessRequest(result);
        },
        async depositWallet() {
          try {

            const url = @json(route('customer.wallet.deposit'));
            const formData = new FormData();

            formData.append('amount', this.depositWalletAmount);
            await this.request(url, 'POST', formData, async (result) => {
              console.log(result);
            });
          } catch (error) {
            console.error('error:', error);
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

            await this.request(url, 'PUT', formData, async (result) => {
              this.setNewCustomerInformation(result.data.customer);
              this.popup('success', '', result.data.message);
            });

          } catch (error) {
            console.error('error:', error);
          }
        },
        async storeNewAddress() {
          try {

            const url = @json(route('customer.addresses.store'));
            const formData = new FormData();

            formData.append('first_name', this.newAddressData.firstName);
            formData.append('last_name', this.newAddressData.lastName);
            formData.append('mobile', this.newAddressData.mobile);
            formData.append('postal_code', this.newAddressData.postalCode);
            formData.append('address', this.newAddressData.address);
            formData.append('city', this.newAddressData.cityId);

            await this.request(url, 'POST', formData, async (result) => {
              this.setNewAddress(result.data.address);
              this.popup('success', '', result.data.message);
            });

          } catch (error) {
            console.error('error:', error);
          }
        },
        async updateAddress() {
          try {

            const url = `/addresses/${this.editAddressData.addressId}`;
            const data = {
              first_name: this.editAddressData.firstName,
              last_name: this.editAddressData.lastName,
              mobile: this.editAddressData.mobile,
              postal_code: this.editAddressData.postalCode,
              address: this.editAddressData.address,
              city: this.editAddressData.cityId,
            };

            await this.request(url, 'PUT', data, async (result) => {
              this.popup('success', '', result.data.message);
            });

          } catch (error) {
            console.error('error:', error);
          }
        },
        async deleteAddress(addressId) {
          try {
            const url = `/addresses/${addressId}`;
            await this.request(url, 'DELETE', null, async (result) => {
              this.popup('success', '', result.message);
              this.existsAddresses = this.existsAddresses.filter(a => a.id != addressId);
            });
          } catch (error) {
            console.error('error:', error);
          }
        },
        async removeFromFavorites(productId) {
          this.popupWithConfirmCallback('warning', 'آیا میخواهید محصول را از علاقه مندی ها حذف کنید ؟', 'بله', async () => {
            try {
              await this.request(`/favorites/${productId}`, 'DELETE', null, async (result) => {
                this.popup('success', '', result.message);
                this.favorites = this.favorites.filter(p => p.id != productId);
              });
            } catch (error) {
              console.error('error:', error);
            }
          });
        },
        async logout() {
          try {
            const url = @json(route('customer.logout'));
            await this.request(url, 'POST', null, async (result) => {
              this.popup('success', '', result.message);
              window.location.replace('/');
            });
          } catch (error) {
            console.error('error:', error);
          }
        }
      },
      computed: {
        addresses() {
          if (this.existsAddresses.length == 0) {
            return [];
          }
          return this.existsAddresses?.map(address => {
            const province = this.provinces.find(p => p.id == address.city?.province_id);
            const city = province?.cities.find(c => c.id == address.city_id);
            return {
              id: address.id,
              province: province,
              city: city,
              cityId: address.city_id,
              firstName: address.first_name,
              lastName: address.last_name,
              mobile: address.mobile,
              postalCode: address.postal_code,
              address: address.address,
            }
          });
        },
      }
    }).mount('#main-content');

  </script>

@endsection