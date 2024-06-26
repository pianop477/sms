
<!DOCTYPE html>
<html lang="en">

{{-- links starts here --}}
    @include('layouts.header')
{{-- links end here --}}

<body class="g-sidenav-show  bg-gray-200">
  {{-- sidenavigation start here --}}
    @include('layouts.sidenavigation')
  {{-- sidenavigation end here --}}
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- topNavbar start here -->
        @include('layouts.topnav')
    <!-- End of topNavbar -->
    <div class="container-fluid py-4">
        @yield('content')
      {{-- footer contents lies here --}}
        @include('layouts.footer')
      {{-- end of footer contents --}}
    </div>
  </main>

  {{-- end of main content --}}


  {{-- plugins settings lies here --}}
    @include('layouts.offset')
  {{-- end of plugins settings/offset --}}


  <!--   Core JS Files   -->
    @include('layouts.coreJs')
  {{-- end of core JS files --}}
</body>

</html>
