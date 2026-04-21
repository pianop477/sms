<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Shule | App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="Piano">
    <meta name="description" content="ShuleApp is a digital platform for School Management System">

    <!-- ============ PWA META TAGS ============ -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2C3E50">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ShuleApp">
    <link rel="apple-touch-icon" href="{{ asset('icons/new_icon2.png') }}">

    <!-- ============ FAVICONS ============ -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon/new_favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon/new_favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/images/favicon/new_favicon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('assets/images/favicon/new_favicon-512x512.png') }}">

    <!-- ============ CACHE CONTROL ============ -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate, private">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- ============ CSS STYLESHEETS ============ -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/metisMenu.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.5.2-web/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slicknav.min.css') }}">

    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />

    <!-- others css -->
    <link rel="stylesheet" href="{{ asset('assets/css/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/default-css.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <!-- ============ JAVASCRIPT LIBRARIES ============ -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{ asset('assets/js/vendor/modernizr-2.8.3.min.js') }}"></script>

    <!-- Toastr CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- ============ PRINT STYLES ============ -->
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
                background-color: gray;
            }

            tbody {
                display: table-row-group;
            }

            body {
                color: black;
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
                color: inherit;
            }
        }

        .print-only {
            display: none;
        }

        /* ============ PWA BANNER STYLES ============ */
        @keyframes slideUp {
            from {
                transform: translateX(-50%) translateY(100px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
            to {
                transform: translateX(-50%) translateY(100px);
                opacity: 0;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Mobile optimization for PWA banners */
        @media (max-width: 768px) {
            #pwa-install-banner > div,
            #pwa-update-banner > div {
                width: calc(100vw - 40px);
                max-width: 350px;
            }
        }
    </style>
</head>
