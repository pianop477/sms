<nav class="navbar navbar-main navbar-expand-lg px-2 mx-2 shadow-none border-radius-xl bg-dark fixed-end">
    <div class="container-fluid py-0 px-1">
        <button class="navbar-toggler d-xl-none d-inline-block" type="button" data-bs-toggle="collapse" data-bs-target="#sidenav-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <span class="text-white text-uppercase">
            @if(Auth::user()->school && Auth::user()->school->school_name)
                {{ Auth::user()->school->school_name .' - '.Auth::user()->school->school_reg_no }}
            @else
                ShuleApp - Admin
            @endif
        </span>
        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown">
                @if (Auth::user()->image)
                    <img src="{{asset('assets/img/profile/' .Auth::user()->image) }}" alt="profile" class="profile-img rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                @else
                    <img src="{{ asset('assets/img/profile/avatar-female.jpg') }}" alt="profile" class="profile-img rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                @endif
                <div class="profile-info dropdown ms-2">
                    <a href="#" class="dropdown-toggle text-white text-uppercase" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end gradient-animation" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item text-white" href="{{ route('show.profile') }}"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a class="dropdown-item text-white" href="{{ route('change.password') }}"><i class="fas fa-key"></i> Change Password</a></li>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <li><a class="dropdown-item text-white" href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fas fa-power-off"></i> Logout</a></li>
                        </form>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
