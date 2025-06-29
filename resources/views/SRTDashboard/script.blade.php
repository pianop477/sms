
    <script src="{{asset('assets/js/vendor/jquery-2.2.4.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery lazima iwe ya kwanza -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Pakia Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- Pakia Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- bootstrap 4 js -->
    <script src="{{asset('assets/js/popper.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('assets/js/metisMenu.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.slicknav.min.js')}}"></script>
    {{-- <script src="assets/js/jquery.slicknav.min.js"></script> --}}
    <script src="{{asset('assets/js/jquery.slimscroll.min.js')}}"></script>

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
    <script src="{{asset('assets/js/line-chart.js')}}"></script>
    <!-- all pie chart -->
    <script src="{{asset('assets/js/pie-chart.js')}}"></script>
    <!-- all bar chart -->
    <script src="{{asset('assets/js/bar-chart.js')}}"></script>
    <!-- all map chart -->
    <script src="{{asset('assets/js/maps.js')}}"></script>
    <!-- others plugins -->
    <script src="{{asset('assets/js/plugins.js')}}"></script>
    <script src="{{asset('assets/js/scripts.js')}}"></script>

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
                }]
            });

            // Handle select all checkbox
            $('#selectAll').on('click', function() {
                var rows = table.rows({ 'search': 'applied' }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });

            // Handle individual checkboxes
            $('#myTable tbody').on('change', 'input[type="checkbox"]', function() {
                if (!this.checked) {
                    $('#selectAll').prop('checked', false);
                }
            });
        });
    </script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

