<div class="sidebar" id="sidebar">
    <div class="brand">
        {{-- <img src="{{asset('custom/logo/sms logo2.png')}}" alt="" class=""> --}}
        <img src="{{asset('assets/img/logo/sms logo2.jpg')}}" alt="" class="profile-img rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
        <h3>ShuleApp</h3>
    </div>
    <hr class="dark horizontal my-0">
    <ul class="nav-links">
        <li>
            <a href="{{route('home')}}" class="active">
                <i class="fas fa-dashboard"></i>
                <span class="link-name">Dashboard</span>
            </a>
        </li>
        @if (Auth::user()->usertype == 1 && Auth::user()->status == 1)
        <li>
            <a href="{{route('register.manager')}}">
                <i class="fas fa-users"></i>
                <span class="link-name">Managers</span>
            </a>
        </li>
        <li>
            <a href="{{route('Schools.index')}}">
                <i class="fas fa-building"></i>
                <span class="link-name">Schools</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fas fa-lock"></i>
                <span class="link-name">User Password Reset</span>
            </a>
        </li>
        @elseif (Auth::user()->usertype == 2 && Auth::user()->status == 1)
        <li>
            <a href="{{route('Parents.index')}}">
                <i class="fas fa-user-shield"></i>
                <span class="link-name">Parents</span>
                {{-- <span class="badge"></span> --}}
            </a>
        </li>
        <li>
            <a href="{{route('classes.list')}}">
                <i class="fas fa-user-graduate"></i>
                <span class="link-name">Students</span>
                {{-- <span class="badge"></span> --}}
            </a>
        </li>
        <li>
            <a href="{{route('Teachers.index')}}">
                <i class="fas fa-user-tie"></i>
                <span class="link-name">Teachers</span>
                {{-- <span class="badge"></span> --}}
            </a>
        </li>
        <li>
            <a href="{{route('Subjects.index')}}">
                <i class="fas fa-book"></i>
                <span class="link-name">Courses</span>
                {{-- <span class="badge"></span> --}}
            </a>
        </li>
        <li>
            <a href="{{route('Classes.index')}}">
                <i class="fas fa-file"></i>
                <span class="link-name">Classes</span>
                {{-- <span class="badge"></span> --}}
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fas fa-list"></i>
                <span class="link-name">Attendance</span>
                {{-- <span class="badge"></span> --}}
            </a>
        </li>
        <li>
            <a href="{{route('Transportation.index')}}">
                <i class="fas fa-bus"></i>
                <span class="link-name">Bus Routine</span>
                {{-- <span class="badge"></span> --}}
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fas fa-lock"></i>
                <span class="link-name">User Password Reset</span>
                {{-- <span class="badge"></span> --}}
            </a>
        </li>
        {{-- <li>
            <a href="#">
                <i class="fas fa-exchange-alt"></i>
                <span class="link-name">Change Request</span>
                <span class="badge new">New</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="link-name">Reported Issues</span>
                <span class="badge">0</span>
            </a>
        </li> --}}
        @endif

    </ul>

    <div class="footer position-fixed bottom-0 z-index-1">
        <hr class="bg-dark">
        <p class="text-white text-center">&copy; {{date('Y')}} ShuleApp.</p>
    </div>
</div>
