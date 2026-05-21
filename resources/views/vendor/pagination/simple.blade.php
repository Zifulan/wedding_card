@if ($paginator->hasPages())
    <div class="pagination">
        @if ($paginator->onFirstPage())
            <span style="color:var(--muted); padding:6px 12px; font-size:13px;">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">← Prev</a>
        @endif

        <span style="color:var(--muted); font-size:13px; padding:6px 4px;">
            Page {{ $paginator->currentPage() }}
        </span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Next →</a>
        @else
            <span style="color:var(--muted); padding:6px 12px; font-size:13px;">Next →</span>
        @endif
    </div>
@endif
