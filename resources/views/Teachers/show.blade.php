@extends('SRTDashboard.frame')
@section('content')
  <div class="card card-body mx-3 mx-md-4 mt-n6">
    <div class="row">
      <div class="col-auto">
        <div class="avatar position-relative">
            @if (!empty($teachers->image))
                <img src="{{ asset('assets/img/profile/' . $teachers->image) }}" alt="profile_image" class="profile-img border-radius-lg shadow-sm" style="width: 150px; object-fit:cover; border-radius: 50px;">
            @else
                <i class="fas fa-user-tie text-secondary" style="font-size: 8rem;"></i>
            @endif
        </div>

      </div>
      <div class="col-auto my-auto">
        <div class="h-100">
          <h5 class="mb-1">
            <span class="text-capitalize">{{$teachers->first_name. ' '. $teachers->last_name}}</span>
            <p class="mb-2">
                Member ID: <span class="text-uppercase">{{$teachers->school_reg_no.'/'.$teachers->joined.'/'. $teachers->member_id}}</span>
            </p>
          </h5>
          <p class="mb-0 font-weight-normal text-sm"> Qualification:
            @if ($teachers->qualification == 1)
                <span class="badge bg-success text-white">{{_('Masters')}}</span>
                @elseif($teachers->qualification == 2)
                <span class="badge bg-primary text-white">{{_('Bachelor')}}</span>
                @elseif ($teachers->qualification == 3)
                <span class="badge bg-info text-white">{{_('Diploma')}}</span>
                @else
                <span class="badge bg-secondary text-white">{{_('Cerificate')}}</span>
            @endif
          </p>
          <p class="text-sm">
            Job Title:
            @if ($teachers->usertype == 3)
                <span  class="text-capitalize text-white badge bg-secondary"> Teacher - {{$teachers->role_name}}</span>
            @endif
          </p>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-2">
        <a href="{{ route('Teachers.index')}}" class="float-right btn-link"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
      </div>
    </div>
    <hr class="dark horizontal my-0">
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title">Update Teacher's Details</h4>
        </div>
        <form action="{{route('Update.teachers', $teachers->id)}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">First Name</label>
                    <input type="text" name="fname" class="form-control text-uppercase" value="{{$teachers->first_name}}" required>
                    @error('fname')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Last Name</label>
                    <input type="text" name="lname" class="form-control text-uppercase" value="{{$teachers->last_name}}" required>
                    @error('lname')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Phone</label>
                    <input type="text" name="phone" class="form-control text-capitalize" value="{{$teachers->phone}}" required>
                    @error('phone')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Date of Birth</label>
                    <input type="date" name="dob" class="form-control text-capitalize" value="{{$teachers->dob}}" required>
                    @error('dob')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Qualification</label>
                    <select name="qualification" id="" class="form-control text-uppercase" required>
                        <option value="{{$teachers->qualification}}">@if ($teachers->qualification ==1) {{_('Masters')}}
                            @elseif ($teachers->qualification == 2) {{_('Degree')}}
                            @elseif ($teachers->qualification == 3) {{_('Diploma')}}
                        @else
                            {{_('Certificate')}}
                        @endif</option>
                        <option value="1">masters</option>
                        <option value="2">degree</option>
                        <option value="3">diploma</option>
                        <option value="4">masters</option>
                    </select>
                    @error('qualification')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Street/Village</label>
                    <input type="text" name="street" class="form-control text-uppercase" value="{{$teachers->address}}" required>
                    @error('street')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Gender</label>
                    <select name="gender" id="" class="form-control text-uppercase" required>
                        <option value="{{$teachers->gender}}">{{$teachers->gender}}</option>
                        <option value="male">male</option>
                        <option value="female">female</option>
                    </select>
                    @error('gender')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Member Since</label>
                    <select name="joined_at" id="" class="form-control text-uppercase" required>
                        <option value="{{$teachers->joined}}" selected>{{$teachers->joined}}</option>
                        @for ($year = date('Y'); $year >= 2000; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                    @error('joined')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Passport Size</label>
                    <input type="file" name="image" class="form-control text-capitalize" value="{{old('image')}}">
                    @error('image')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
  </div>

@endsection
