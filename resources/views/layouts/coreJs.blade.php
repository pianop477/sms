<script src="{{asset('assets/js/core/popper.min.js')}}"></script>
  <script src="{{asset('assets/js/core/bootstrap.min.js')}}"></script>
  <script src="{{asset('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
  <script src="{{asset('assets/js/plugins/smooth-scrollbar.min.js')}}"></script>
  <script src="{{asset('assets/js/plugins/chartjs.min.js')}}"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

        //toggler button responsiveness
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.navbar-toggler');
            const sidebar = document.querySelector('.sidenav');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        });

  </script>
  <!-- Github buttons -->
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{asset('assets/js/material-dashboard.min.js?v=3.1.0')}}"></script>
  <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

  <!-- Include DataTables JS -->
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
  <script src="{{asset('assets/js/datatabe.js')}}"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
