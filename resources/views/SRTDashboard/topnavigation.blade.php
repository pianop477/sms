<div class="mainheader-area py-2 shadow-sm bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            {{-- Logo & School Name --}}
            <div class="d-flex align-items-center mb-2 mb-md-0">
                @if(Auth::user()->school && Auth::user()->school->school_name)
                    <img src="{{ asset('assets/img/logo/' . Auth::user()->school->logo) }}"
                         alt="Logo"
                         class="rounded-circle d-none d-md-block me-2"
                         style="width:50px; height:50px; object-fit:cover;">
                    <span class="fw-bold text-capitalize d-block d-md-none text-center w-100" style="font-size: 18px; color:darkblue;">
                        {{ Auth::user()->school->school_name }}
                    </span>
                    <a href="{{ route('home') }}"
                       class="navbar-brand fw-bold text-capitalize d-none d-md-inline-block ms-2"
                       style="font-size: 22px; color:darkblue;">
                        {{ Auth::user()->school->school_name }}
                    </a>
                @else
                    <img src="{{ asset('assets/img/logo/logo.png') }}"
                         alt="Logo"
                         class="rounded-circle d-none d-md-block me-2"
                         style="width:50px; height:50px; object-fit:cover;">
                    <span class="fw-bold text-center d-block d-md-none w-100" style="font-size: 18px; color:darkblue;">
                        ShuleApp - Admin
                    </span>
                    <a href="{{ route('home') }}"
                       class="navbar-brand fw-bold d-none d-md-inline-block ms-2"
                       style="font-size: 22px; color:darkblue;">
                        ShuleApp - Admin
                    </a>
                @endif
            </div>

            {{-- User Profile --}}
            <div class="dropdown d-flex align-items-center">
                @if (Auth::user()->image)
                    <img src="{{ asset('assets/img/profile/' . Auth::user()->image) }}"
                         alt="Profile"
                         class="rounded-circle"
                         style="width:40px; height:40px; object-fit:cover;">
                @else
                    <img src="{{ asset('assets/img/profile/' . (Auth::user()->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg')) }}"
                         alt="Avatar"
                         class="rounded-circle"
                         style="width:40px; height:40px; object-fit:cover;">
                @endif

                <a class="dropdown-toggle text-decoration-none ms-2 fw-semibold text-dark"
                   href="#"
                   role="button"
                   id="userDropdown"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                    Hi, {{ ucwords(strtolower(Auth::user()->first_name)) }} <i class=""></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="{{ route('show.profile') }}"><i class="ti-user me-2"></i> Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('change.password') }}"><i class="ti-key me-2"></i> Change Password</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="ti-power-off me-2"></i> Sign Out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
