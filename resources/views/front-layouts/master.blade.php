<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'فروشگاه اطلس مد')</title>
  @include('front-layouts.includes.styles')
  @yield('styles')
  @stack('styles')
</head>
<body>
  <div id="main-content" class="page-wrapper bg-gray-100">
    @include('front-layouts.includes.header')
    @yield('content')
    @include('front-layouts.includes.footer')
    <div class="modal-overlay"></div>
    @include('front-layouts.includes.modals')
    @yield('modals')
  </div>
  @include('front-layouts.includes.scripts')
  @yield('scripts')
  @stack('scripts')
</body>
</html>