@props([
  'isModal' => false,
  'target' => '',
  'hasTitle' => false,
  'model',
  'route' => null
])

@if ($isModal)
  <button 
    class="btn btn-sm btn-icon btn-warning text-white" 
    data-target="{{ '#' . $target }}"
    data-toggle="modal" 
    style="margin-inline: 1px">
    @if ($hasTitle)
      <span>ویرایش</span>
    @endif
    <i class="fa fa-pencil mr-1 {{ $hasTitle ? 'mr-1' : '' }}"></i>
  </button>
@else
  <a
    href="{{ route($route, $model) }}"
    class="btn btn-sm btn-icon btn-warning text-white"
    data-toggle="tooltip"
    style="margin-inline: 1px"
    data-original-title="ویرایش">
    @if ($hasTitle)
      <span>ویرایش</span>
    @endif
    <i class="fa fa-pencil {{ $hasTitle ? 'mr-1' : '' }}"></i>
  </a>
@endif

