@extends('front-layouts.master')

@section('content')
  <main class="pb-4">

    <!-- Page Path -->
    <div class="bg-white">
      <div class="page-path container-2xl py-2 px-4 px-md-8 px-3xl-0 d-flex gap-1 align-items-center">
        <i class="icon-home1 icon-fs-medium-2"></i>
        <a href="/" class="text-button-1 mt-1">خانه</a>
        <i class="icon-angle-double-left icon-fs-medium"></i>
        <a href="{{ route('front.posts.index') }}" class="text-button-1 mt-1">پست ها</a>
      </div>
    </div>

    <section class="container-2xl d-flex flex-lg-row flex-column gap-4 py-3 px-4 px-md-8 px-3xl-0">
      <div class="weblog-list d-flex flex-column col-lg-9">

        <form action="{{ route('front.posts.index') }}" class="d-flex gap-1 align-items-center border-b-gray-300 pb-1" method="GET">
          <i class="icon-search icon-fs-large"></i>
          <input type="text" name="search" class="flex-grow-1 bg-white px-2 py-1">
        </form>

        <!-- Lists -->
        <div class="weblog grid mt-5">
          @foreach ($posts ?? [] as $post)
            <div class="g-col-lg-4 g-col-6">
              <article class="weblog-cart">
                <a href="{{ route('front.posts.show', $post) }}" class="d-flex flex-column overflow-hidden position-relative">
                  <!-- Img -->
                  <figure class="blog-img overflow-hidden radius-medium position-relative">
                    <img class="main-img w-p-100 h-p-100 radius-medium" loading="lazy" src="{{ asset('front-assets/images/homePage/panbe.jpg') }}" alt="{{ $post->slug }}">
                    <div class="position-absolute w-p-100 px-2 bottom-0 pb-1 svg-hover d-flex align-items-end justify-content-between">
                      <span class="text-button-1 color-white">اطلاعات بیشتر</span>
                      <i class="icon-arrow-left2 icon-fs-small color-white"></i>
                    </div>
                  </figure>
                  <div class="blog-details d-flex flex-column gap-1 px-1 mt-1">
                    <!-- Little Explaintion -->
                    <div class="d-flex gap-1 align-items-center color-gray-900">
                      <i class="icon-caret-left icon-fs-medium"></i>
                      <h5 class="text-medium text-truncate">{{ Str::limit($post->title, 40) }}</h5>
                    </div> 
                      <div class="d-lg-flex d-none align-items-center justify-content-between pb-2 border-b-gray-300">
                        <!-- Publication Date -->
                        <div class="d-flex align-items-center text-button-1 color-gray-700">
                          <span class="color-gray-900">تاریخ انتشار:</span>
                          @php
                            $jalaliPublishedAt = verta($post->published_at)->format('Y/m/d');    
                          @endphp
                          <time datetime="{{ $jalaliPublishedAt }}">{{ $jalaliPublishedAt }}</time>
                        </div>
                        <div class="d-flex gap-1 align-items-center text-button-1">
                          <span class="color-gray-900">دسته بندی:</span>
                          <a 
                            href="{{ route('front.posts.byCategory', ['categoryId' => $post->post_category_id, 'slug' => $post->category->slug]) }}" 
                            class="category color-gray-700">
                            {{ $post->category->name }}
                          </a>
                        </div>
                      </div>
                      <p class="explain-weblog d-lg-block d-none text-button-1 color-gray-900 pt-1">{{ $post->summary }}</p>
                  </div>
                </a>
              </article>
            </div>
          @endforeach
        </div>

        <!-- Pages -->
        {{ $posts->onEachSide(0)->links('pagination.default') }}

      </div>

      <aside class="col-lg-3 px-2 mt-lg-0 mt-7">
        <div class="weblog-categories bg-white position-sticky p-3 d-flex flex-column">
          <span class="title text-medium-2-strong color-gray-900 pb-2">دسته بندی ها</span>
          <ul class="category-list mt-3">
            @foreach ($postCategories ?? [] as $category)
              <li class="mb-2">
                <a 
                  href="{{ route('front.posts.byCategory', ['categoryId' => $category->id, 'slug' => $category->slug]) }}" 
                  class="d-flex gap-1 align-items-center color-gray-800 text-medium">
                  <i class="icon-angle-left icon-fs-medium"></i>
                  <span>{{ $category->name }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      </aside>

    </section>

    @include('front-layouts.includes.mobile-menu')

  </main>
@endsection

@section('scripts')
<script src="{{ asset('front-assets/js/lightbox.js') }}"></script>
@endsection
