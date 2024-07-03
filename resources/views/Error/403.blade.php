<!doctype html>
<html class="no-js" lang="en">

    @include('SRTDashboard.header')

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- error area start -->
    <div class="error-area ptb--100 text-center">
        <div class="container">
            <div class="error-content p-1">
                <h4 class="text-danger">Error 404</h4>
                <p class="text-danger p-3">Ooops! You don't have permission to access this Page!</p>
                <p><i class="fas fa-ban text-danger text-center" style="font-size: 2.5rem;"></i></p>
                <a href="{{url()->previous()}}" class="btn btn-sm"><i class="ti-angle-double-left"></i> Go Back</a>
            </div>
        </div>
    </div>
    @include('SRTDashboard.script')
</body>

</html>
