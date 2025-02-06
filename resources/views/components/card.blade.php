<div id="{{ isset($id) ? $id : null }}" class="card {{ isset($class) ? $class : null }}">
    @isset($cardTitle)
        <div class="card-header border-0">
            <p class="card-title font-weight-bold">{{ $cardTitle }}</p>
            {{ isset($cardOptions) ? $cardOptions : null }}
        </div>
    @endisset
    <div class="card-body">
        {{ $cardBody }}
    </div>
</div>