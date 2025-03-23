@if (Auth::check())
    @if (Auth::user()->usertype == 1)
        @include('dashboard.admin')
    @elseif (Auth::user()->usertype == 2)
        @include('dashboard.manager')
    @elseif (Auth::user()->usertype == 3)
        @include('dashboard.teachers')
    @elseif (Auth::user()->usertype == 4)
        @include('dashboard.parents')
    @else
        @include('error.unauthorized')  <!-- You can create an unauthorized page -->
    @endif
@else
    @include('auth.login')  <!-- Ensure that user is logged in -->
@endif

