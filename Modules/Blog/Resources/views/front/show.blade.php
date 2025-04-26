@extends('front-layouts.master')

@section('content')
  <main class=" pb-4">

    <!-- Page Path -->
    <div class="bg-white">
      <div class="page-path container-2xl py-2 px-4 px-md-8 px-3xl-0 d-flex gap-1 align-items-center">
        <i class="icon-home1 icon-fs-medium-2"></i>
        <a href="/" class="text-button-1 mt-1">خانه</a>
        <i class="icon-angle-double-left icon-fs-medium"></i>
        <a href="{{ route('front.posts.index') }}" class="text-button-1 mt-1">پست ها</a>
        <i class="icon-angle-double-left icon-fs-medium"></i>
        <a href="{{ route('front.posts.show', $post) }}" class="text-button-1 mt-1">{{ $post->title }}</a>
      </div>
    </div>

    <section class="container-2xl d-flex flex-lg-row flex-column gap-4 py-3 px-4 px-md-8 px-3xl-0">

      <div class="d-flex flex-column col-lg-9">

        <!-- Weblog Text -->
        <div class="weblog-detail-text bg-white p-lg-3 p-2 d-flex flex-column">
          <!-- Title -->
          <h1 class="text-medium-3 color-gray-900 pb-2 border-b-gray-300">{{ $post->title }}</h1>
          <!-- Article Details -->
          <div class="d-flex flex-wrap justify-content-lg-around gap-3 mt-4 pb-2 border-b-gray-300">
            <span class="text-medium d-none d-lg-block color-gray-900">{{ $post->creatorable->name }}</span>
            <!-- Publishing Date -->
            <div class="d-flex gap-1 text-medium align-items-center">
              <svg data-v-59eaadbe="" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path data-v-59eaadbe="" fill-rule="evenodd" clip-rule="evenodd" d="M12 22.0001C6.48 22.0001 2 17.5301 2 12.0001C2 6.48011 6.48 2.00011 12 2.00011C17.53 2.00011 22 6.48011 22 12.0001C22 17.5301 17.53 22.0001 12 22.0001ZM15.19 15.7101C15.31 15.7801 15.44 15.8201 15.58 15.8201C15.83 15.8201 16.08 15.6901 16.22 15.4501C16.43 15.1001 16.32 14.6401 15.96 14.4201L12.4 12.3001V7.68011C12.4 7.26011 12.06 6.93011 11.65 6.93011C11.24 6.93011 10.9 7.26011 10.9 7.68011V12.7301C10.9 12.9901 11.04 13.2301 11.27 13.3701L15.19 15.7101Z" fill="#999"></path></svg>
              <span class="text-medium-strong color-gray-900">تاریخ انتشار :</span>
              @php
                $jalaliPublishedAt = verta($post->published_at)->format('Y/m/d')
              @endphp
              <time class="color-gray-700" datetime="{{ $jalaliPublishedAt }}">{{ $jalaliPublishedAt }}</time>
            </div>
            <!-- View -->
            <div class="d-flex gap-1 text-medium  align-items-center">
              <svg data-v-59eaadbe="" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path data-v-59eaadbe="" fill-rule="evenodd" clip-rule="evenodd" d="M8.09756 12C8.09756 14.1333 9.8439 15.8691 12 15.8691C14.1463 15.8691 15.8927 14.1333 15.8927 12C15.8927 9.85697 14.1463 8.12121 12 8.12121C9.8439 8.12121 8.09756 9.85697 8.09756 12ZM17.7366 6.04606C19.4439 7.36485 20.8976 9.29455 21.9415 11.7091C22.0195 11.8933 22.0195 12.1067 21.9415 12.2812C19.8537 17.1103 16.1366 20 12 20H11.9902C7.86341 20 4.14634 17.1103 2.05854 12.2812C1.98049 12.1067 1.98049 11.8933 2.05854 11.7091C4.14634 6.88 7.86341 4 11.9902 4H12C14.0683 4 16.0293 4.71758 17.7366 6.04606ZM12.0012 14.4124C13.3378 14.4124 14.4304 13.3264 14.4304 11.9979C14.4304 10.6597 13.3378 9.57362 12.0012 9.57362C11.8841 9.57362 11.767 9.58332 11.6597 9.60272C11.6207 10.6694 10.7426 11.5227 9.65971 11.5227H9.61093C9.58166 11.6779 9.56215 11.833 9.56215 11.9979C9.56215 13.3264 10.6548 14.4124 12.0012 14.4124Z" fill="#999"></path></svg>
              <span class="text-medium-strong color-gray-900"> بازدید:</span>
              <span class="color-gray-700">{{ $post->views_count }}</span>
            </div>
            <!-- Opinion -->
            <div class="d-flex gap-1 text-medium  align-items-center">
              <svg data-v-59eaadbe="" width="19" height="19" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path data-v-59eaadbe="" fill-rule="evenodd" clip-rule="evenodd" d="M2 12.015C2 6.74712 6.21 2 12.02 2C17.7 2 22 6.65699 22 11.985C22 18.1642 16.96 22 12 22C10.36 22 8.54 21.5593 7.08 20.698C6.57 20.3876 6.14 20.1572 5.59 20.3375L3.57 20.9384C3.06 21.0986 2.6 20.698 2.75 20.1572L3.42 17.9139C3.53 17.6034 3.51 17.2729 3.35 17.0125C2.49 15.4301 2 13.6975 2 12.015ZM10.7 12.015C10.7 12.7261 11.27 13.2969 11.98 13.307C12.69 13.307 13.26 12.7261 13.26 12.025C13.26 11.314 12.69 10.7431 11.98 10.7431C11.28 10.7331 10.7 11.314 10.7 12.015ZM15.31 12.025C15.31 12.7261 15.88 13.307 16.59 13.307C17.3 13.307 17.87 12.7261 17.87 12.025C17.87 11.314 17.3 10.7431 16.59 10.7431C15.88 10.7431 15.31 11.314 15.31 12.025ZM7.37 13.307C6.67 13.307 6.09 12.7261 6.09 12.025C6.09 11.314 6.66 10.7431 7.37 10.7431C8.08 10.7431 8.65 11.314 8.65 12.025C8.65 12.7261 8.08 13.2969 7.37 13.307Z" fill="#999"></path></svg>
              <span class="text-medium-strong color-gray-900"> دیدگاه:</span>
              <span class="color-gray-700">{{ $post->comments_count }}</span>
            </div>
          </div>

          {!! $post->body !!}

          <!-- Share -->
          <div class="share d-flex justify-content-between align-items-center border-t-gray-300 mt-4 pt-1">
            <span class="text-medium">اشتراک گذاری مطلب :</span>
            <!-- Social Media Svgs -->
            <div class="social-media d-flex gap-2 mt-1">
              <a href="">
                <svg data-v-d7a8f494="" id="Capa_1" height="18" version="1.1" viewBox="0 0 512 512" width="18" x="0px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" y="0px"><path data-v-d7a8f494="" d="M256.064,0h-0.128C114.784,0,0,114.816,0,256c0,56,18.048,107.904,48.736,150.048l-31.904,95.104l98.4-31.456
                  C155.712,496.512,204,512,256.064,512C397.216,512,512,397.152,512,256S397.216,0,256.064,0z M405.024,361.504
                  c-6.176,17.44-30.688,31.904-50.24,36.128c-13.376,2.848-30.848,5.12-89.664-19.264C189.888,347.2,141.44,270.752,137.664,265.792
                  c-3.616-4.96-30.4-40.48-30.4-77.216s18.656-54.624,26.176-62.304c6.176-6.304,16.384-9.184,26.176-9.184
                  c3.168,0,6.016,0.16,8.576,0.288c7.52,0.32,11.296,0.768,16.256,12.64c6.176,14.88,21.216,51.616,23.008,55.392
                  c1.824,3.776,3.648,8.896,1.088,13.856c-2.4,5.12-4.512,7.392-8.288,11.744c-3.776,4.352-7.36,7.68-11.136,12.352
                  c-3.456,4.064-7.36,8.416-3.008,15.936c4.352,7.36,19.392,31.904,41.536,51.616c28.576,25.44,51.744,33.568,60.032,37.024
                  c6.176,2.56,13.536,1.952,18.048-2.848c5.728-6.176,12.8-16.416,20-26.496c5.12-7.232,11.584-8.128,18.368-5.568
                  c6.912,2.4,43.488,20.48,51.008,24.224c7.52,3.776,12.48,5.568,14.304,8.736C411.2,329.152,411.2,344.032,405.024,361.504z" fill="#bbb"></path>
                </svg>
              </a>
              <a href="">
                <svg data-v-d7a8f494="" id="Capa_1" height="18" version="1.1" viewBox="0 0 512 512" width="18" x="0px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" y="0px"><path data-v-d7a8f494="" d="M512,97.248c-19.04,8.352-39.328,13.888-60.48,16.576c21.76-12.992,38.368-33.408,46.176-58.016
                  c-20.288,12.096-42.688,20.64-66.56,25.408C411.872,60.704,384.416,48,354.464,48c-58.112,0-104.896,47.168-104.896,104.992
                  c0,8.32,0.704,16.32,2.432,23.936c-87.264-4.256-164.48-46.08-216.352-109.792c-9.056,15.712-14.368,33.696-14.368,53.056
                  c0,36.352,18.72,68.576,46.624,87.232c-16.864-0.32-33.408-5.216-47.424-12.928c0,0.32,0,0.736,0,1.152
                  c0,51.008,36.384,93.376,84.096,103.136c-8.544,2.336-17.856,3.456-27.52,3.456c-6.72,0-13.504-0.384-19.872-1.792
                  c13.6,41.568,52.192,72.128,98.08,73.12c-35.712,27.936-81.056,44.768-130.144,44.768c-8.608,0-16.864-0.384-25.12-1.44
                  C46.496,446.88,101.6,464,161.024,464c193.152,0,298.752-160,298.752-298.688c0-4.64-0.16-9.12-0.384-13.568
                  C480.224,136.96,497.728,118.496,512,97.248z" fill="#bbb"></path>
                </svg>
              </a>
              <a href="">
                <svg data-v-d7a8f494="" id="Capa_1" height="18" version="1.1" viewBox="0 0 512 512" width="18" x="0px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" y="0px"><path data-v-d7a8f494="" d="M448,0H64C28.704,0,0,28.704,0,64v384c0,35.296,28.704,64,64,64h192V336h-64v-80h64v-64c0-53.024,42.976-96,96-96h64v80
                  h-32c-17.664,0-32-1.664-32,16v64h80l-32,80h-48v176h96c35.296,0,64-28.704,64-64V64C512,28.704,483.296,0,448,0z" fill="#bbb"></path>
                </svg>
              </a>
            </div>
          </div>
          
        </div>

        @if($relatedPosts->isNotEmpty())
          <!-- Similar Contents -->
          <div class="d-flex flex-column gap-1 mt-10 px-lg-3">
            <!-- Title & Btn -->
            <div class="d-flex justify-content-between">
              <h5 class="h4 color-gray-900">مطالب مشابه</h5>
              <a href="{{ route('front.posts.index') }}" class="text-button-1 border-b-black">مشاهده همه پست ها</a>
            </div>
            <!-- Similar Articels -->
            <div class="similar-articels weblog grid mt-4">
              @foreach ($relatedPosts as $relatedPost)
                <div class="g-col-lg-4 g-col-6">
                  <article class="weblog-cart">
                    <a href="{{ route('front.posts.show', $relatedPost->id) }}" class="d-flex flex-column overflow-hidden position-relative">
                      <!-- Img -->
                      <figure class="blog-img overflow-hidden radius-medium position-relative">
                        <img class="main-img w-p-100 h-p-100 radius-medium" loading="lazy" src="{{ '/storage/' . $relatedPost->image->uuid . '/' . $relatedPost->image->file_name }}" alt="{{ $relatedPost->slug }}">
                        <div class="position-absolute w-p-100 px-2 bottom-0 pb-1 svg-hover d-flex align-items-end justify-content-between">
                          <span class="text-button-1 color-white">اطلاعات بیشتر</span>
                          <i class="icon-arrow-left2 icon-fs-small color-white"></i>
                        </div>
                      </figure>
                      <div class="blog-details d-flex flex-column gap-1 px-1 mt-1">
                        <!-- Little Explaintion -->
                        <div class="d-flex gap-1 align-items-center color-gray-900">
                          <i class="icon-caret-left icon-fs-medium"></i>
                          <h5 class="text-medium text-truncate">{{ $relatedPost->title }}</h5>
                        </div> 
                        <div class="d-lg-flex d-none align-items-center justify-content-between pb-2 border-b-gray-300">
                          <!-- Publication Date -->
                          <div class="d-flex align-items-center text-button-1 color-gray-700">
                            <span class="color-gray-900">تاریخ انتشار:</span>
                            @php
                              $jalaliPublishedAt = verta($relatedPost->published_at)->format('Y/m/d')
                            @endphp
                            <time datetime="{{ $jalaliPublishedAt }}">{{ $jalaliPublishedAt }}</time>
                          </div>
                          <div class="d-flex gap-1 align-items-center text-button-1">
                            <span class="color-gray-900">دسته بندی:</span>
                            <a 
                              href="{{ route('front.posts.byCategory', ['categoryId' => $relatedPost->post_category_id, 'slug' => $relatedPost->category->slug]) }}" 
                              class="category color-gray-700">
                              {{ $relatedPost->category->name }}
                            </a>
                          </div>
                        </div>
                        <p class="explain-weblog d-lg-block d-none text-button-1 color-gray-900 pt-1">{{ $relatedPost->summary }}</p>
                      </div>
                    </a>
                  </article>
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <!-- Write A Comment -->
        <div class="weblog-comment d-flex flex-column bg-white p-lg-3 mt-6">
          <!-- Title -->
          <div class="d-flex gap-1 align-items-center">
            <i class="icon-squareQuestion icon-fs-large"></i>
            <h4 class="text-medium-2 color-gray-900">دیدگاهی بگذارید</h4>
          </div>
          <span class="text-button">نشانی ایمیل شما منتشر نخواهد شد</span>
          <input hidden name="parent_id" value="">
          <form action="{{ route('front.comments.store', $post) }}" method="POST" class="grid comment-form mt-3">
            <input type="text" name="name" class="g-col-md-5 g-col-12 bg-gray-200 p-1 text-medium" placeholder="نام کامل*" required>
            <input type="email" name="email" class="g-col-md-5 g-col-12 bg-gray-200 p-1 text-medium" placeholder=" آدرس ایمیل*" required>
            <textarea name="body" class="g-col-12 bg-gray-200 p-1 text-medium p-2" placeholder="دیدگاه شما*" rows="12" required></textarea>
            <div class="d-flex justify-content-end g-col-12">
              <button type="submit" class="weblog-send-comment-btn bg-black px-12 py-1 align-items-center color-white text-medium">ارسال پیام</button>
            </div>
          </form>
          <!-- comments -->
          <div class="comments d-flex flex-column gap-3 mt-2">
            <div class="bg-white radius-large p-lg-4 d-flex flex-column justify-content-end">
              @foreach ($postComments as $comment)
                <!-- User Name & Date -->
                <div class="d-flex align-items-center gap-lg-3 justify-content-lg-end justify-content-between mb-2">
                  <div class="d-flex gap-1 align-items-center">
                    <span class="p-3 radius-medium bg-black">
                      <i class="icon-user icon-fs-medium-2 color-white"></i>
                    </span>
                    <span class="text-medium-strong">{{ $comment->name }}</span>
                  </div>
                  <!-- Hour -->
                  <div class="d-flex gap-1">
                    <span class="text-button-1 color-gray-900">{{ verta($comment->published_at)->format('H:i') }}</span>
                    @php
                      $jalaliDate = verta($comment->published_at)->format('Y/m/d')
                    @endphp
                    <time datetime="{{ $jalaliDate }}" class="text-button-1 color-gray-900">{{ $jalaliDate }}</time>
                  </div>
                </div>
                <!-- Text -->
                <p class="text-button-1 color-gray-900 mb-3 pe-3">{{ $comment->body }}</p>
                <!-- Answer Button -->
                <button type="button" class="weblog-answer-btn me-4 text-button-1 p-2 color-secondary-500">پاسخ دهید</button>
                <!-- Answer Form -->
                <form action="{{ route('front.comments.store', $post) }}" method="POST" class="weblog-answerForm grid p-lg-3 my-3">
                  <input hidden name="parent_id" value="{{ $comment->id }}">
                  <input type="text" name="name" class="g-col-md-6 g-col-12 bg-gray-100 p-2" placeholder="نام کامل*" required>
                  <input type="text" name="email" class="g-col-md-6 g-col-12 bg-gray-100 p-2" placeholder="آدرس ایمیل*" required>
                  <textarea type="text" name="body" rows="8" class="g-col-12 bg-gray-100 p-2" placeholder="دیدگاه شما*" required></textarea>
                  <!-- Button -->
                  <div class="d-flex justify-content-end g-col-12">
                    <button type="submit" class="weblog-send-comment-btn bg-black px-10 py-1 d-flex gap-1 align-items-center color-white text-medium">ارسال پیام</button>
                  </div>
                </form>
                <!-- Answer -->
                @if($comment->children->isNotEmpty())
                  @php
                    $answerComment = $comment->children->first();
                  @endphp
                  <div class="d-flex flex-column bg-gray-100 p-4 mt-2">
                    <!-- User Name & Date -->
                    <div class="d-flex align-items-center gap-lg-3 justify-content-lg-end justify-content-between mb-2">
                      <div class="d-flex gap-1 align-items-center">
                        <span class="p-3 radius-medium bg-gray-600">
                          <i class="icon-user icon-fs-medium-2 color-white"></i>
                        </span>
                        <span class="text-medium-strong">{{ $answerComment->name }}</span>
                      </div>
                      <div class="d-flex gap-1">
                        <!-- Hour -->
                        <span class="text-button-1 color-gray-800">{{ verta($answerComment->created_at)->format('H:i') }}</span>
                        @php
                          $jalaliDate = verta($answerComment->created_at)->format('Y/m/d')
                        @endphp
                        <time datetime="{{ $jalaliDate }}" class="text-button-1 color-gray-800">{{ $jalaliDate }}</time>
                      </div>
                    </div>
                    <p class="text-medium color-gray-900 pe-3">{{ $answerComment->body }}</p>
                  </div>
                @endif
              @endforeach
            </div>
          </div>
        </div>

      </div>

      <aside class="col-lg-3 mt-lg-0 mt-3">
        <div class="weblog-categories bg-white position-sticky p-3 d-flex flex-column">
          <span class="title text-medium-2-strong color-gray-900 pb-2">دسته بندی ها</span>
          <ul class="category-list mt-3">
            @foreach ($postCategories as $category)
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
        @if ($latestPosts->isNotEmpty())
          <!-- Articels List -->
          <div class="aside-article-list d-flex flex-column gap-2 p-2 bg-white mt-3 overflow-hidden">
            <!-- Title -->
            <span class="title text-medium-2-strong color-gray-900 pb-2">آخرین مطالب</span>
            <ul>
              @foreach ($latestPosts as $latestPost)
                <article class="mb-2">
                  <a href="{{ route('front.posts.show',$latestPost) }}" class="d-flex gap-1 align-items-center">
                    <figure class="col-4 radius-medium">
                      <img class="w-p-100 radius-medium" src="{{ '/storage/' . $latestPost->image->uuid . '/' . $latestPost->image->file_name }}" alt="{{ $latestPost->title }}">
                    </figure>
                    <div class="col-8 d-flex flex-column gap-1">
                      <span class="text-truncate text-button-1 color-gray-700 w-p-100">{{ $latestPost->title }}</span>
                      <div class="d-flex justify-content-between align-items-center">
                        @php($jalaliPublishedAt = verta($latestPost->published_at)->format('Y/m/d'))
                        <time class="color-gray-600" datetime="{{ $jalaliPublishedAt }}">{{ $jalaliPublishedAt }}</time>
                        <span class="text-button-1">اطلاعات بیشتر...</span>
                      </div>
                    </div>
                  </a>
                </article>
              @endforeach
            </ul>
          </div>
        @endif

      </aside>

    </section>

    @include('front-layouts.includes.mobile-menu')

  </main>
@endsection

@section('scripts')
<script src="{{ asset('front-assets/js/lightbox.js') }}"></script>
<script> weblogDetailPage() </script>
@endsection
