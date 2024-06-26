<div class="mainheader-area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="logo">
                    {{-- <a href="{{route('home')}}"><img src="{{asset('assets/img/logo/sms logo2.jpg')}}" alt="logo" class="profile-img rounded-circle" style="width: 40px; height: 80px; object-fit: cover;"></a> ShuleApp --}}
                    <h3>ShuleApp</h3>
                </div>
            </div>
            <div class="col-md-4">
                    <span class="text-uppercase text-white">
                        @if(Auth::user()->school && Auth::user()->school->school_name)
                            {{ Auth::user()->school->school_name }}
                        @else
                            ShuleApp - Admin
                        @endif
                    </span>
            </div>
            <!-- profile info & task notification -->
            <div class="col-md-4 clearfix text-right">
                {{-- <div class="d-md-inline-block d-block mr-md-4">
                    <ul class="notification-area">
                        <li id="full-view"><i class="ti-fullscreen"></i></li>
                        <li id="full-view-exit"><i class="ti-zoom-out"></i></li>
                        <li class="settings-btn">
                            <i class="ti-settings"></i>
                        </li>
                    </ul>
                </div> --}}
                <div class="clearfix d-md-inline-block d-block">
                    <div class="user-profile m-0">
                        {{-- <img class="avatar user-thumb" src="assets/images/author/avatar.png" alt="avatar"> --}}
                        @if (Auth::user()->image)
                            <img src="{{asset('assets/img/profile/' .Auth::user()->image) }}" alt="profile" class="profile-img rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            @if (Auth::user()->gender == 'male')
                                <img class="avatar user-thumb" src="{{asset('assets/img/profile/avatar.jpg')}}" alt="avatar">
                            @else
                                <img class="avatar user-thumb" src="{{asset('assets/img/profile/avatar-female.jpg')}}" alt="avatar">
                        @endif
                        @endif
                        <h4 class="user-name text-capitalize dropdown-toggle" data-toggle="dropdown"> {{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}<i class="fa fa-angle-down"></i></h4>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{route('show.profile')}}"><i class="ti-user"></i> Profile</a>
                            <a class="dropdown-item" href="{{route('change.password')}}"><i class="ti-key"></i> Change Password</a>
                            <form action="{{route('logout')}}" method="POST">
                                @csrf
                                <a class="dropdown-item" href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"><i class="ti-power-off"></i> Log Out</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
