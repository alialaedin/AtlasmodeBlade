@extends('front-layouts.master')

@section('content')
  <main id="main-home" class="main-home container-2xl px-4 px-lg-0 px-md-8 px-3xl-0 pb-12 mt-lg-3 mt-5">
    @include('home::includes.advertise')
    @include('home::includes.special-categories')
    @include('home::includes.products')
    @include('home::includes.posts')
    @include('front-layouts.includes.mobile-menu')
  </main>
@endsection

@section('scripts')
<script>
  mainPage()
</script> 
@stack('HomePageScripts')
@endsection
