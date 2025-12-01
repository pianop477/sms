<!-- Pagination Section -->
<div id="paginationSection">
    @if(isset($bills) && $bills->hasPages())
        <div class="mt-4">
            {{ $bills->links() }}
        </div>
    @endif
</div>
