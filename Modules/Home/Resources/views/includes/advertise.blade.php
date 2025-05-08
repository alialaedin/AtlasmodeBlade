<section class="w-p-100 d-flex mt-12">

  @isset($advertisements['advertise_desktop_right'])
    <figure class="col-12 col-lg-6 ps-3">
      <a href="{{ $advertisements['advertise_desktop_right']->link_url }}">
        <img class="w-p-100" src="{{ $advertisements['advertise_desktop_right']->picture_url }}" />
      </a>
    </figure>
  @endisset

  @isset($advertisements['advertise_desktop_left'])
    <figure class="col-12 col-lg-6 pe-3">
        <a href="{{ $advertisements['advertise_desktop_left']->link_url }}">
        <img class="w-p-100" src="{{ $advertisements['advertise_desktop_left']->picture_url }}" />
      </a>
    </figure>
  @endisset

</section>