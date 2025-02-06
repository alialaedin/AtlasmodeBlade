
@php
	$id = isset($id) ? $id : null;	
	$class = isset($class) ? $class : null;	
    $param = isset($parameter) ? $parameter : [];
@endphp

@if (isset($type) && $type == 'modal')
    <button 
		class="btn btn-primary btn-sm {{ $class }}" 
		data-target="{{ '#' . $target }}"
        data-toggle="modal" 
		{{ $id }}>
        {{ $title }}
        <i class="fa fa-plus mr-1"></i>
    </button>
@else
    <a   
        href="{{ route($route, $param) }}"   
        class="btn btn-primary btn-sm {{ $class }}"  
        {{ $id }}>  
        {{ $title }}  
        <i class="fa fa-plus"></i>  
    </a>  
@endif
