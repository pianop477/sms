<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Shule | App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/metisMenu.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fontawesome-free-6.5.2-web/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/slicknav.min.css')}}">
    <link rel="icon" type="image/png" href="{{asset('assets/img/favicon/favicon.png')}}">
    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <!-- others css -->
    <link rel="stylesheet" href="{{asset('assets/css/typography.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/default-css.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/styles.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/responsive.css')}}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- modernizr css -->
    <script src="{{asset('assets/js/vendor/modernizr-2.8.3.min.js')}}"></script>
    <style>
        @media print {
            * {
                color: black;
            }
            .attendance {
                border: 2px solid black;
            }
            .attendance-date{
                background-color: gray;
            }
            .teacher-details {
                border: 2px solid black;
            }
            .no-print {
                display: none;
            }
            .print-only {
                display: block;
            }
            .table {
                border: 1px solid black;
            }
            .table-attendance {
                border: 1px solid black;
            }
        }
        .print-only {
            display: none;
        }
    </style>

    <script>
        function addPrefix() {
            var prefix = "P.O BOX "; // Example prefix
            var userInput = document.getElementById('userInput').value;
            var prefixedInput = prefix + userInput;
            document.getElementById('userInput').value = prefixedInput;
        }
    </script>
</head>
