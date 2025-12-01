<div class="col-md-7 text-end">
    <div id="summarySection">
        @if(isset($bills) && $bills->total() > 0)
            <div class="text-muted">
                Showing {{ $bills->firstItem() }} - {{ $bills->lastItem() }} of {{ $bills->total() }} records
            </div>
        @else
            <div class="text-muted">
                No records found
            </div>
        @endif
    </div>
</div>
