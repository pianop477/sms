@if (Auth::user()->usertype == 1 && Auth::user()->status == 1)
        @include('dashboard.admin')
    @elseif (Auth::user()->usertype == 2 && Auth::user()->status == 1)
        @include('dashboard.manager')
    @elseif (Auth::user()->usertype == 3 && Auth::user()->status == 1)
        @include('dashboard.teachers')
    @elseif (Auth::user()->usertype == 4 && Auth::user()->status == 1)
        @include('dashboard.parents')
@endif
