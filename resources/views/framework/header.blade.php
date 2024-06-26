<div class="row position-sticky" style="z-index: 1">
    <div class="col-8">
        <span class="text-black text-uppercase">
            @if(Auth::user()->school && Auth::user()->school->school_name)
                {{ Auth::user()->school->school_name .' - '.Auth::user()->school->school_reg_no }}
            @else
                ShuleApp - Admin
            @endif
        </span>
    </div>
    <div class="col-4">
        <header>
            <div class="user-wrapper" id="user-wrapper">
                    @if (Auth::user()->image)
                        <img src="{{asset('assets/img/profile/' .Auth::user()->image) }}" alt="profile" class="profile-img rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                    @else
                        @if (Auth::user()->gender == 'male')
                            <img src="{{ asset('assets/img/profile/avatar.jpg') }}" alt="profile" class="profile-img rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <img src="{{ asset('assets/img/profile/avatar-female.jpg') }}" alt="profile" class="profile-img rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                        @endif
                    @endif
                <div>
                    <span class="text-uppercase">{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }} </span>
                </div>
                <!-- <i class="fas fa-chevron-down"></i> -->
            </div>
            <div class="user-menu" id="user-menu">
                <ul>
                    <li><a href="{{ route('show.profile') }}"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="{{ route('change.password') }}"><i class="fas fa-key"></i> Change Password</a></li>
                    <form action="{{route('logout')}}" method="POST">
                        @csrf
                        <li><a href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fas fa-power-off"></i> Logout</a></li>
                    </form>
                </ul>
            </div>
        </header>
    </div>
</div>
<hr class="bg-dark mt-0">
