@props([
  'model',
  'route',
  'hasTitle' => false
])

<button 
  type="button" 
  class="btn btn-danger btn-sm text-white" 
  data-original-title="حذف" 
  onclick="confirmDelete('delete-{{ $model->id }}')" >
  @if ($hasTitle)
    <span>حذف</span>
  @endif
  <i class="fa fa-trash-o {{ $hasTitle ? 'mr-1' : '' }}"></i>
</button>

<form
  action="{{ route($route,$model)}}"
  id="delete-{{$model->id}}"
  method="POST"
  class="d-none">
  @csrf
  @method("DELETE")
</form>
