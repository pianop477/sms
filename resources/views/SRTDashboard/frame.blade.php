<!doctype html>
<html class="no-js" lang="en">

@include('SRTDashboard.header')

<body class="body-bg">

    <!-- preloader area start -->
    @include('SRTDashboard.preloader')
    <div id="loading-bar" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 4px; background: blue;"></div>
    <!-- preloader area end -->


    <!-- main wrapper start -->
    <div class="horizontal-main-wrapper">

        <!-- main header area start -->

        <!-- main header area end -->
           <div class="no-print">
            @include('SRTDashboard.topnavigation')
           </div>
        <!-- header area start -->


        <div class="header-area header-bottom no-print">

            {{-- navigation links start here --}}
                @include('SRTDashboard.horizontalLinks')
            {{-- navigation links end here --}}
        </div>

        <!-- header area end -->


        <!-- page title area end -->
        <div class="main-content-inner">
            <div class="container">
                @yield('content')

            </div>
        </div>
        <!-- main content area end -->
        <!-- footer area start-->
            <div class="no-print">
                @include('SRTDashboard.footer')
            </div>
        <!-- footer area end-->
    </div>

    <!-- main wrapper start -->
    <!-- offset area start -->
        @include('SRTDashboard.offset')
    <!-- offset area end -->

    <!-- jquery latest version -->
    @include('SRTDashboard.script')

    @include('sweetalert::alert')
</body>

</html>
