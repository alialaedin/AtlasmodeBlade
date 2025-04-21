@isset($advertisements['advertise_desktop_right'])
<a href="{{ $advertisements['advertise_desktop_right']->link_url }}" class="main-banner px-xl-4">
  <figure class="radius-large overflow-hidden">
    <img 
      class="w-p-50 h-p-100 d-none d-lg-block" 
      src="{{ $advertisements['advertise_desktop_right']->picture_url }}" 
    />
  </figure>
</a>
@endisset

@isset($advertisements['advertise_desktop_left'])
<a href="{{ $advertisements['advertise_desktop_left']->link_url }}" class="main-banner px-xl-4">
  <figure class="radius-large overflow-hidden">
    <img 
      class="w-p-50 h-p-100 d-none d-lg-block" 
      src="{{ $advertisements['advertise_desktop_left']->picture_url }}" 
    />
  </figure>
</a>
@endisset

@isset($advertisements['advertise_mobile_top'])
<a href="{{ $advertisements['advertise_mobile_top']->link_url }}" class="main-banner px-xl-4">
  <figure class="radius-large overflow-hidden">
    <img 
      class="w-p-50 h-p-100 d-block d-lg-none" 
      src="{{ $advertisements['advertise_mobile_top']->picture_url }}" 
    />
  </figure>
</a>
@endisset

@isset($advertisements['advertise_mobile_bottom'])
<a href="{{ $advertisements['advertise_mobile_bottom']->link_url }}" class="main-banner px-xl-4">
  <figure class="radius-large overflow-hidden">
    <img 
      class="w-p-50 h-p-100 d-block d-lg-none" 
      src="{{ $advertisements['advertise_mobile_bottom']->picture_url }}" 
    />
  </figure>
</a>
@endisset