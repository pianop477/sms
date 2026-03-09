<script src="{{ asset('assets/js/vendor/jquery-2.2.4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery lazima iwe ya kwanza -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Pakia Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- Pakia Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<!-- bootstrap 4 js -->
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.slicknav.min.js') }}"></script>
{{-- <script src="assets/js/jquery.slicknav.min.js"></script> --}}
<script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>

<!-- start chart js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<!-- start highcharts js -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<!-- start amcharts -->
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/ammap.js"></script>
<script src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
<!-- all line chart activation -->
<script src="{{ asset('assets/js/line-chart.js') }}"></script>
<!-- all pie chart -->
<script src="{{ asset('assets/js/pie-chart.js') }}"></script>
<!-- all bar chart -->
<script src="{{ asset('assets/js/bar-chart.js') }}"></script>
<!-- all map chart -->
<script src="{{ asset('assets/js/maps.js') }}"></script>
<!-- others plugins -->
<script src="{{ asset('assets/js/plugins.js') }}"></script>
<script src="{{ asset('assets/js/scripts.js') }}?v={{ filemtime(public_path('assets/js/scripts.js')) }}"></script>

<script>
    function scrollToTopAndPrint() {
        window.scrollTo(0, 0);
        setTimeout(() => {
            window.print();
        }, 1000);
    }
</script>
<script>
    $(document).ready(function() {
    var table = $('#myTable').DataTable({
        stateSave: true,
        columnDefs: [{
            orderable: false,
            targets: 0
        }],
        // Safisha data zilizohifadhiwa kwenye state kabla ya kuinitialize
        stateLoadParams: function(settings, data) {
            if (data.checkedRows) {
                delete data.checkedRows;
            }
        }
    });

    // Object ya kuhifadhi rows zilizochaguliwa
    var selectedRows = new Set();

    // Handle select all checkbox
    $('#selectAll').on('click', function() {
        var isChecked = this.checked;

        if (isChecked) {
            // Get ALL rows (including those not on current page)
            table.rows({ search: 'applied' }).every(function() {
                var rowId = $(this.node()).find('input[name="student[]"]').val();
                selectedRows.add(rowId);
            });
        } else {
            // Clear all selections
            selectedRows.clear();
        }

        // Update checkboxes on current page
        table.rows({ page: 'current' }).every(function() {
            var checkbox = $(this.node()).find('input[name="student[]"]');
            var rowId = checkbox.val();
            checkbox.prop('checked', isChecked);

            // Update selectedRows set
            if (isChecked) {
                selectedRows.add(rowId);
            } else if (selectedRows.has(rowId)) {
                selectedRows.delete(rowId);
            }
        });

        updateSelectedCount();
        updateFormInputs();
    });

    // Handle individual checkboxes
    $('#myTable tbody').on('change', 'input[name="student[]"]', function() {
        var rowId = $(this).val();

        if (this.checked) {
            selectedRows.add(rowId);
        } else {
            selectedRows.delete(rowId);
            $('#selectAll').prop('checked', false);
        }

        updateSelectedCount();
        updateFormInputs();
    });

    // Function to update selected count
    function updateSelectedCount() {
        $('#selectedCount').text(selectedRows.size + ' students selected');
    }

    // Function to update hidden inputs in the form
    function updateFormInputs() {
        // Clear existing hidden inputs
        $('#batchForm input[name="student[]"][type="hidden"]').remove();

        // Add new hidden inputs for all selected rows
        selectedRows.forEach(function(studentId) {
            $('#batchForm').append(
                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'student[]')
                    .val(studentId)
            );
        });
    }

    // Update checkboxes when page changes
    table.on('draw', function() {
        // Update checkboxes on current page based on selectedRows
        table.rows({ page: 'current' }).every(function() {
            var checkbox = $(this.node()).find('input[name="student[]"]');
            var rowId = checkbox.val();
            checkbox.prop('checked', selectedRows.has(rowId));
        });

        // Update selectAll checkbox state
        var currentPageRows = table.rows({ page: 'current', search: 'applied' }).count();
        var currentPageSelected = table.rows({ page: 'current', search: 'applied' })
            .nodes().to$().find('input[name="student[]"]:checked').length;

        $('#selectAll').prop('checked',
            currentPageSelected === currentPageRows && currentPageRows > 0
        );

        updateSelectedCount();
    });

    // Handle form submission
    $('#batchForm').on('submit', function(e) {
        if (selectedRows.size === 0) {
            e.preventDefault();
            alert('Please select at least one student');
            return false;
        }

        // Confirm before submitting
        if (!confirm(`Are you sure you want to update ${selectedRows.size} student(s)?`)) {
            e.preventDefault();
            return false;
        }

        // Update hidden inputs before submit
        updateFormInputs();
        return true;
    });
});
</script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
