<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{route('home')}}" target="">
            <img src="{{asset('assets/img/logo/sms logo2.jpg')}}" alt="" class="profile-img rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
            <span class="ms-1 font-weight-bold text-white brand-name" style="font-size: 26px;">ShuleApp</span>
        </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link text-white" href="{{route('home')}}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <!-- Collapsible Section -->
            @if (Auth::user()->usertype == 1 && Auth::user()->status == 1)
            <li class="nav-item">
                <a class="nav-link text-white" href="{{route('Schools.index')}}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-school opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Schools</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{route('register.manager')}}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-tie opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Managers</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-lock opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">User Password Reset</span>
                </a>
            </li>
            @elseif (Auth::user()->usertype == 2 && Auth::user()->status == 1)
            <li class="nav-item">
                <a class="nav-link text-white " href="{{route('Teachers.index')}}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-tie opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Teachers</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white " href="{{route('classes.list')}}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-graduate opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Students</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white " href="{{route('Parents.index')}}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-shield opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Parents</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white " href="{{route('Classes.index')}}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-school opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Classes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white " href="{{route('Subjects.index')}}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-book opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Subject</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white " href="">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-list opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Attendance</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{route('Transportation.index')}}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-bus opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Bus Routine</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white " href="">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-file opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Examination Results</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white " href="">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-lock opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">User Permission</span>
                </a>
            </li>
            @elseif (Auth::user()->usertype == 4 && Auth::user()->status == 1)
            <li class="nav-item">
                <a class="nav-link text-white " href="">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-graduate opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">My Students</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white " href="">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-list opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Attendance Report</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</aside>
