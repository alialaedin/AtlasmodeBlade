@if ($specialCategories->isNotEmpty())
<section class="categories mt-8">
  <div class="swiper categories-swiper">
    <div class="swiper-wrapper">
      @foreach ($specialCategories as $category)
        <div class="swiper-slide">
          <a 
            href="{{ route('front.products.index', ['category_id' => $category->id]) }}" 
            class="w-p-100 d-flex flex-column overflow-hidden align-items-center justify-content-center"
          >
            @if ($category->image)
              <figure>
                <img 
                  class="w-p-100 h-p-100 radius-medium" 
                  src="{{ '/storage/' . $category->image->uuid . '/' . $category->image->file_name }}" 
                  alt="{{ $category->slug }}"
                />
              </figure>
            @endif
            <span class="image-title w-p-100 position-relative text-center overflow-hidden radius-small py-2 text-medium-strong">
              {{ $category->title }}
            </span>
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif
