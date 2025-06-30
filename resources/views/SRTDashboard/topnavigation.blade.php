<div class="mainheader-area py-2 shadow-sm bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            {{-- Logo & School Name --}}
            <div class="d-flex align-items-center mb-2 mb-md-0">
                @php
                    $schoolName = Auth::user()->school && Auth::user()->school->school_name
                                  ? Auth::user()->school->school_name
                                  : 'ShuleApp - Admin';
                    $logoPath = Auth::user()->school && Auth::user()->school->logo
                                ? 'assets/img/logo/' . Auth::user()->school->logo
                                : 'assets/img/logo/logo.png';
                @endphp

                <img src="{{ asset($logoPath) }}"
                     alt="Logo"
                     class="rounded-circle me-2"
                     style="width:50px; height:50px; object-fit:cover;">

                {{-- Large devices --}}
                <a href="{{ route('home') }}"
                   class="navbar-brand fw-bold text-capitalize d-none d-md-inline-block"
                   style="font-size: 22px; color:darkblue;">
                    {{ $schoolName }}
                </a>
                {{-- Small devices --}}
                <span class="fw-bold text-capitalize text-center w-100 d-block d-md-none"
                      style="font-size: 22px; color:darkblue;">
                    {{ $schoolName }}
                </span>
            </div>

            {{-- User Profile --}}
            <div class="dropdown d-flex align-items-center ms-md-auto">
                @php
                    $profileImg = Auth::user()->image
                                  ? 'assets/img/profile/' . Auth::user()->image
                                  : (Auth::user()->gender == 'male'
                                     ? 'assets/img/profile/avatar.jpg'
                                     : 'assets/img/profile/avatar-female.jpg');
                @endphp

                <img src="{{ asset($profileImg) }}"
                     alt="Profile"
                     class="rounded-circle"
                     style="width:40px; height:40px; object-fit:cover;">

                {{-- Dropdown toggle --}}
                <a class="dropdown-toggle text-decoration-none ms-2 fw-semibold text-dark"
                   href="#"
                   role="button"
                   id="userDropdown"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                    Hi, {{ ucwords(strtolower(Auth::user()->first_name)) }}
                </a>

                {{-- Dropdown menu --}}
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
