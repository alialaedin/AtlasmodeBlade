@if (isset($sliders['desktop']) && $sliders['desktop']->isNotEmpty())
  <div class="swiper main-swiper d-none d-lg-block">
    <div class="swiper-wrapper">
      @foreach ($sliders['desktop'] as $desktopSlider)
        <div class="swiper-slide">
          <figure>
            <a href="{{ $desktopSlider->link_url }}" class="main-banner radius-large overflow-hidden">
              <img 
                class="w-p-100 h-p-100 radius-large" 
                src="{{ '/storage/' . $desktopSlider->image->uuid . '/' . $desktopSlider->image->file_name }}" 
                alt="{{ $desktopSlider->title }}"
              />
            </a>
          </figure>
        </div>
      @endforeach
    </div>
    <div class="swiper-pagination"></div>
  </div>
@endif

@if (isset($sliders['mobile']) && $sliders['mobile']->isNotEmpty())
  <div class="swiper main-swiper d-lg-none">
    <div class="swiper-wrapper">
      @foreach ($sliders['mobile'] as $mobileSlider)
        <div class="swiper-slide">
          <figure>
            <a href="{{ $mobileSlider->link_url }}" class="main-banner radius-large overflow-hidden">
              <img 
                class="w-p-100 h-p-100 radius-large" 
                src="{{ '/storage/' . $mobileSlider->image->uuid . '/' . $mobileSlider->image->file_name }}" 
                alt="{{ $mobileSlider->title }}"
              />
            </a>
          </figure>
        </div>
      @endforeach
    </div>
    <div class="swiper-pagination"></div>
  </div>
@endif
