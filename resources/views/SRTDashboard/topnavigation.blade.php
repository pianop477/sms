<div class="mainheader-area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                @if(Auth::user()->school && Auth::user()->school->school_name)
                    <!-- Logo itaonekana kwenye skrini kubwa pekee -->
                    <img src="{{asset('assets/img/logo/'. Auth::user()->school->logo)}}" alt="" class="rounded-circle d-none d-md-inline" style="max-width:50px; object-fit:cover; border-radius: 50px;">
                    <!-- Jina la shule litabaki kwa skrini ndogo na kubwa -->
                    <a href="{{route('home')}}" class="navbar-brand font-weight-bold text-capitalize d-block text-center d-md-none" style="font-size: 20px;">
                        {{ Auth::user()->school->school_name }}
                    </a>
                    <a href="{{route('home')}}" class="navbar-brand font-weight-bold text-capitalize d-none d-md-inline" style="font-size: 25px;">{{ Auth::user()->school->school_name }}</a>
                @else
                    <img src="{{asset('assets/img/logo/logo.png')}}" alt="" class="rounded-circle d-none d-md-inline" style="max-width:50px; object-fit:cover; border-radius: 50px;">
                    <a href="{{route('home')}}" class="navbar-brand font-weight-bold d-inline d-md-none" style="font-size: 20px;">ShuleApp - Admin</a>
                    <a href="{{route('home')}}" class="navbar-brand font-weight-bold d-none d-md-inline">ShuleApp - Admin</a>
                @endif
            </div>

            <!-- profile info & task notification -->
            <div class="col-md-6 clearfix text-md-right">
                <div class="clearfix d-md-inline-block d-block">
                    <div class="clearfix d-md-inline-block d-block">
                        <div class="user-profile m-0 d-flex align-items-between">
                            @if (Auth::user()->image)
                                <img src="{{asset('assets/img/profile/' .Auth::user()->image) }}" alt="profile" class="profile-img rounded-circle" style="width: 40px; object-fit: cover;">
                            @else
                                @if (Auth::user()->gender == 'male')
                                    <img class="avatar user-thumb" src="{{asset('assets/img/profile/avatar.jpg')}}" alt="avatar">
                                @else
                                    <img class="avatar user-thumb" src="{{asset('assets/img/profile/avatar-female.jpg')}}" alt="avatar">
                                @endif
                            @endif
                            <h4 class="user-name text-capitalize dropdown-toggle" data-toggle="dropdown" style="margin-left: 10px;">
                                Hello, {{ Auth::user()->first_name }}! <i class="fa fa-angle-down"></i>
                            </h4>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{route('show.profile')}}"><i class="ti-user"></i> Profile</a>
                                <a class="dropdown-item" href="{{route('change.password')}}"><i class="ti-key"></i> Change Password</a>
                                <form action="{{route('logout')}}" method="POST">
                                    @csrf
                                    <a class="dropdown-item" href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"><i class="ti-power-off"></i> Sign Out</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
