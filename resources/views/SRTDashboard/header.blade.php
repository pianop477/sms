<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Shule | App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="Piano">
    <meta name="description" content="ShuleApp is a digital platform for School Management System">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{asset('assets/images/favicon/favicon-16x16.png')}}">
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/metisMenu.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fontawesome-free-6.5.2-web/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/slicknav.min.css')}}">
    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <!-- others css -->
    <link rel="stylesheet" href="{{asset('assets/css/typography.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/default-css.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/styles.css')}}?v={{ filemtime(public_path('assets/css/styles.css')) }}">
    <link rel="stylesheet" href="{{asset('assets/css/responsive.css')}}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    {{-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> --}}
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
    <!-- modernizr css -->
    <script src="{{asset('assets/js/vendor/modernizr-2.8.3.min.js')}}"></script>
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ filemtime(public_path('manifest.json')) }}">
        <!-- Other head content -->
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }
            .print-only {
                display: block;
            }
            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                border-top: 1px solid #ddd;
                padding-top: 10px;
            }
            @page {
                margin: 15mm;
                @top-left {
                        content: none;
                    }
                    @top-right {
                        content: none;
                    }
                    @bottom-left {
                        content: none;
                    }
                    @bottom-right {
                        content: none;
                    }
            }
            thead {
                display: table-header-group;
                background-color: gray; /* Adds a gray background to thead */
            }
            tbody {
                display: table-row-group;
            }
            body {
                color: black; /* Sets text color to black */
            }
            .table {
                border: 1px solid black;
                border-collapse: collapse;
            }
            .table th,
            .table td {
                border: 1px solid black;
            }
            .logo {
                color: inherit; /* Ensure logo color is not changed */
            }
        }
        .print-only {
            display: none;
        }
    </style>

</head>
