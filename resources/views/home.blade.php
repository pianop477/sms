@if (Auth::user()->usertype == 1)
        @include('dashboard.admin')
    @elseif (Auth::user()->usertype == 2)
        @include('dashboard.manager')
    @elseif (Auth::user()->usertype == 3)
        @include('dashboard.teachers')
    @elseif (Auth::user()->usertype == 4)
        @include('dashboard.parents')
@endif
