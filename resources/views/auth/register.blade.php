<!doctype html>
<html class="no-js" lang="en">
@include('SRTDashboard.header')
<body>
    @include('SRTDashboard.preloader')
    <!-- preloader area end -->
    <!-- login area start -->
    <div class="login-area login-bg">
        <div class="container">
            <div class="login-box ptb--100">
                <form role="form" method="POST" action="{{route('users.create')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="login-form-head">
                        <h4>ShuleApp</h4>
                        <p>Fomu ya Kujisajili Wazazi</p>
                    </div>
                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputName1">Jina la Kwanza</label>
                            <input type="text" id="exampleInputName1" name="fname" value="{{old('fname')}}">
                            <i class="ti-user"></i>
                            @error('fname')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputName1">Jina la Mwisho</label>
                            <input type="text" id="exampleInputName1" name="lname" value="{{old('lname')}}">
                            <i class="ti-user"></i>
                            @error('lname')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Barua pepe</label>
                            <input type="email" id="exampleInputEmail1" name="email" value="{{old('email')}}">
                            <i class="ti-email"></i>
                            @error('email')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <select name="gender" id="exampleInputEmail1" class="form-control">
                                <option value="">--Chagua Jinsia--</option>
                                <option value="male">Mwanaume</option>
                                <option value="female">Mwanamke</option>
                            </select>
                            @error('email')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Namba ya simu</label>
                            <input type="tel" id="exampleInputEmail1" name="phone" value="{{old('phone')}}">
                            <i class="ti-mobile"></i>
                            @error('phone')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Mtaa/Kijiji unachoishi</label>
                            <input type="text" id="exampleInputEmail1" name="street" value="{{old('street')}}">
                            <i class="ti-location-pin"></i>
                            @error('street')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Neno la siri</label>
                            <input type="password" id="exampleInputPassword1" name="password">
                            <i class="ti-lock"></i>
                            @error('password')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword2">Rudia Neno la siri</label>
                            <input type="password" id="exampleInputPassword2" name="password_confirmation">
                            <i class="ti-lock"></i>
                            @error('password_confirmation')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <select name="school" id="exampleInputEmail1" class="form-control text-capitalize">
                                <option value="">--Chagua Jina la Shule--</option>
                                @if ($schools->isEmpty())
                                    <option value="" class="text-danger">no schools found</option>
                                @else
                                    @foreach ($schools as $school )
                                        <option value="{{$school->id}}">{{$school->school_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('school')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit">Hifadhi Taarifa <i class="ti-arrow-right"></i></button>
                        </div>
                        <div class="form-footer text-center mt-4">
                            <p class="text-muted">Tayari una akaunti? <a href="{{route('login')}}">Ingia hapa</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- login area end -->
    @include('SRTDashboard.script')
    @include('sweetalert::alert')
</body>

</html>
