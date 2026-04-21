<!-- ============ JQUERY - Lazima iwe kwanza (MOJA TU) ============ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- ============ BOOTSTRAP 5 (Badala ya mchanganyiko wa Bootstrap 4 & 5) ============ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ============ SELECT2 ============ -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- ============ OTHER LIBRARIES ============ -->
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.slicknav.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>

<!-- ============ CHART LIBRARIES ============ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<!-- ============ AMCHARTS ============ -->
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/ammap.js"></script>
<script src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>

<!-- ============ CHART ACTIVATIONS ============ -->
<script src="{{ asset('assets/js/line-chart.js') }}"></script>
<script src="{{ asset('assets/js/pie-chart.js') }}"></script>
<script src="{{ asset('assets/js/bar-chart.js') }}"></script>
<script src="{{ asset('assets/js/maps.js') }}"></script>

<!-- ============ DATATABLES ============ -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<!-- ============ OTHER PLUGINS ============ -->
<script src="{{ asset('assets/js/plugins.js') }}"></script>
<script src="{{ asset('assets/js/scripts.js') }}?v={{ time() }}"></script>

<!-- ============ CUSTOM SCRIPTS ============ -->
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
        // Initialize DataTable
        var table = $('#myTable').DataTable({
            stateSave: true,
            columnDefs: [{
                orderable: false,
                targets: 0
            }],
            stateLoadParams: function(settings, data) {
                if (data.checkedRows) {
                    delete data.checkedRows;
                }
            }
        });

        // Object for selected rows
        var selectedRows = new Set();

        // Handle select all checkbox
        $('#selectAll').on('click', function() {
            var isChecked = this.checked;

            if (isChecked) {
                table.rows({ search: 'applied' }).every(function() {
                    var rowId = $(this.node()).find('input[name="student[]"]').val();
                    if (rowId) selectedRows.add(rowId);
                });
            } else {
                selectedRows.clear();
            }

            table.rows({ page: 'current' }).every(function() {
                var checkbox = $(this.node()).find('input[name="student[]"]');
                var rowId = checkbox.val();
                checkbox.prop('checked', isChecked);
                if (isChecked && rowId) {
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

        function updateSelectedCount() {
            $('#selectedCount').text(selectedRows.size + ' students selected');
        }

        function updateFormInputs() {
            $('#batchForm input[name="student[]"][type="hidden"]').remove();
            selectedRows.forEach(function(studentId) {
                $('#batchForm').append(
                    $('<input>').attr('type', 'hidden').attr('name', 'student[]').val(studentId)
                );
            });
        }

        table.on('draw', function() {
            table.rows({ page: 'current' }).every(function() {
                var checkbox = $(this.node()).find('input[name="student[]"]');
                var rowId = checkbox.val();
                checkbox.prop('checked', selectedRows.has(rowId));
            });

            var currentPageRows = table.rows({ page: 'current', search: 'applied' }).count();
            var currentPageSelected = table.rows({ page: 'current', search: 'applied' })
                .nodes().to$().find('input[name="student[]"]:checked').length;

            $('#selectAll').prop('checked', currentPageSelected === currentPageRows && currentPageRows > 0);
            updateSelectedCount();
        });

        $('#batchForm').on('submit', function(e) {
            if (selectedRows.size === 0) {
                e.preventDefault();
                alert('Please select at least one student');
                return false;
            }
            if (!confirm(`Are you sure you want to update ${selectedRows.size} student(s)?`)) {
                e.preventDefault();
                return false;
            }
            updateFormInputs();
            return true;
        });

        // CSRF Token refresh
        function refreshCsrfToken() {
            fetch('/csrf-token', {
                cache: 'no-store',
                headers: { 'Cache-Control': 'no-cache, no-store, must-revalidate' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.token) {
                    let metaToken = document.querySelector('meta[name="csrf-token"]');
                    if (metaToken) metaToken.setAttribute('content', data.token);

                    document.querySelectorAll('input[name="_token"]').forEach(input => {
                        input.value = data.token;
                    });
                }
            })
            .catch(error => console.log('CSRF refresh failed:', error));
        }

        setInterval(refreshCsrfToken, 4 * 60 * 1000);
        document.addEventListener('DOMContentLoaded', refreshCsrfToken);

        // Clear caches on logout
        document.addEventListener('click', function(e) {
            if (e.target.closest('form[action*="logout"]') ||
                e.target.closest('a[href*="logout"]') ||
                (e.target.type === 'submit' && e.target.form && e.target.form.action.includes('logout'))) {

                e.preventDefault();

                if ('caches' in window) {
                    caches.keys().then(function(names) {
                        names.forEach(name => {
                            if (name.includes('shuleapp-cache') || name.includes('gatepass-tokens')) {
                                caches.delete(name);
                            }
                        });
                    });
                }

                localStorage.clear();
                sessionStorage.clear();

                setTimeout(() => {
                    if (e.target.closest('form')) {
                        e.target.closest('form').submit();
                    } else if (e.target.form) {
                        e.target.form.submit();
                    }
                }, 100);
            }
        });
    });
</script>
