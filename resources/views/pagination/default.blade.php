@if ($paginator->hasPages())
    <nav class="d-print-none">
        <ul class="pages mt-3 d-flex gap-3 justify-content-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page text-center disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-item text-medium-2" style="width: 30px; height: 30px;" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page text-center">
                    <a class="page-item text-medium-2" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page text-center disabled" aria-disabled="true"><span class="page-item text-medium-2">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page text-center active" aria-current="page"><span class="page-item text-medium-2">{{ $page }}</span></li>
                        @else
                            <li class="page text-center"><a class="page-item text-medium-2" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page text-center">
                    <a class="page-item text-medium-2" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page text-center disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-item text-medium-2" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
