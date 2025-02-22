@extends('front-layouts.master')

@section('content')
  <main class="main-home container-2xl px-4 px-lg-0 pb-12 mt-lg-3 mt-5">
    @include('home::includes.advertise')
    @include('home::includes.special-categories')
    {{-- @include('home::includes.most-sale-products') --}}
    @include('home::includes.new-products')
    @include('home::includes.posts')
  </main>
@endsection

@section('scripts')
<script>
  mainPage()
</script> 
@endsection
