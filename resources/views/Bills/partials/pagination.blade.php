<!-- resources/views/Bills/partials/pagination.blade.php -->
@if ($bills->hasPages())
    @php
        $searchParam = request()->has('search') ? '&search=' . request('search') : '';
        $yearParam = request()->has('year') ? '&year=' . request('year') : '';
        $allParams = $searchParam . $yearParam;
    @endphp

    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($bills->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link"
                       href="{{ $bills->previousPageUrl() }}{{ $allParams }}"
                       rel="prev"
                       aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($bills->links()->elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $bills->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link"
                                   href="{{ $url }}{{ !Str::contains($url, 'search=') ? $searchParam : '' }}{{ !Str::contains($url, 'year=') ? $yearParam : '' }}">
                                   {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($bills->hasMorePages())
                <li class="page-item">
                    <a class="page-link"
                       href="{{ $bills->nextPageUrl() }}{{ $allParams }}"
                       rel="next"
                       aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
