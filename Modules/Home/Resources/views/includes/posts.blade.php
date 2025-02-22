@if ($posts->isNotEmpty())
  <section class="blogs d-flex flex-column gap-lg-6 gap-7 mt-12">
    <!-- Title -->
    <div class="d-flex align-items-center justify-content-between">
      <h2 class="h4-strong color-gray-900">پست ها</h2>
      <a href="./weblog-list.html" class="see-more pb-1 text-medium-strong color-gray-900">مشاهده بیشتر</a>
    </div>
    <div class="grid gap-2 gap-lg-3">
      @foreach ($posts as $post)
        <div class="g-col-lg-3 g-col-6">
          <article class="blog-cart">
            <a href="./weblog-details.html" class="d-flex flex-column overflow-hidden position-relative">
              <!-- Img -->
              <figure class="blog-img overflow-hidden radius-medium position-relative">
                <img 
                  class="main-img w-p-100 h-p-100 radius-medium" 
                  loading="lazy"
                  src="{{ asset('front-assets/images/homePage/panbe.jpg') }}" 
                  alt="{{ $post->slug ?? $post->title }}"
                />
              </figure>
              <div class="blog-details d-flex flex-column gap-1 px-1 mt-1">
                <!-- Title -->
                <span class="title px-3 radius-u text-button-1 color-gray-700 bg-secondary-100">{{ $post->category->name }}</span>
                <!-- Little Explaintion -->
                <h5 class="text-medium text-truncate">{{ $post->title }}</h5>
                <!-- Publication Date -->
                <div class="d-flex align-items-center text-button-1 color-gray-700">
                  <span>تاریخ انتشار:</span>
                  @php
                    $jalaliPublishedAt = verta($post->published_at)->format('Y/m/d');
                  @endphp
                  <time datetime="{{ $jalaliPublishedAt }}">{{ $jalaliPublishedAt }}</time>
                </div>
              </div>
            </a>
          </article>
        </div>
      @endforeach
    </div>
  </section>
@endif
