@props([
  'title' => null,
  'model',
  'route'
])

<a
  href="{{ route($route, $model) }}"
  class="btn btn-sm btn-icon btn-dark text-white"
  data-toggle="tooltip"
  style="margin-inline: 1px"
  data-original-title="نمایش">
  @if ($title)
    <span>{{ $title }}</span>
  @endif
  <i class="fa fa-eye {{ $title ? 'mr-1' : '' }}"></i>
</a>