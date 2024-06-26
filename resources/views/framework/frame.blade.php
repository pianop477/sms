<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shule | App</title>
    <link rel="stylesheet" href="{{asset('custom/css/styles.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fontawesome-free-6.5.2-web/css/all.min.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="icon" type="image/png" href="{{asset('assets/img/favicon/favicon.ico')}}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
    {{--Start of sidebar navigation links  --}}
    @include('framework.sidebar')
    {{-- End of sidebar navigation links --}}
    <div class="main-content" id="main-content">
        <div class="toggle-btn" id="toggle-btn">
            <i class="fas fa-bars"></i>
        </div>

        {{-- top bar start here --}}
        @include('framework.header')
        {{-- top bar end here --}}

        <main class="mt-5">
            {{-- section contents goes here --}}
            @yield('content')
        </main>
    </div>
    <script src="{{asset('custom/js/script.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>


    <!-- Body content -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });

          function  printPage() {
                window.print();
            }

    </script>

    @include('sweetalert::alert')
</body>
</html>
