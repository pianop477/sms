@extends('SRTDashboard.frame')
@section('content')
  <div class="card card-body mx-3 mx-md-4 mt-n6">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-10">
                    <h4 class="header-title">Update Teacher's Details</h4>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('teacher.profile', Hashids::encode($teachers->id))}}" class="float-right btn btn-info btn-xs"><i class="fas fa-arrow-circle-left"></i> Back</a>
                </div>
            </div>
        </div>
        <form action="{{route('Update.teachers', ['teachers' => Hashids::encode($teachers->id)])}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">First Name</label>
                    <input type="text" name="fname" class="form-control text-uppercase" value="{{$teachers->first_name}}" required>
                    @error('fname')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Last Name</label>
                    <input type="text" name="lname" class="form-control text-uppercase" value="{{$teachers->last_name}}" required>
                    @error('lname')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Phone</label>
                    <input type="text" name="phone" class="form-control text-capitalize" value="{{$teachers->phone}}" required>
                    @error('phone')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Date of Birth</label>
                    <input type="date" name="dob" class="form-control text-capitalize" value="{{$teachers->dob}}" required min="{{\Carbon\Carbon::now()->subYears(60)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(18)->format('Y-m-d')}}">
                    @error('dob')
                    <div class="text-danger">{{$message}}</div>
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
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Street/Village</label>
                    <input type="text" name="street" class="form-control text-uppercase" value="{{$teachers->address}}" required>
                    @error('street')
                    <div class="text-danger">{{$message}}</div>
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
                    <div class="text-danger">{{$message}}</div>
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
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Email</label>
                    <input type="email" name="email" class="form-control" value="{{old('email', $teachers->email)}}">
                    @error('email')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Passport Size <span class="text-danger text-sm">Maximum 1MB</span></label>
                    <input type="file" name="image" class="form-control text-capitalize" value="{{old('image')}}">
                    @error('image')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-success">Save changes</button>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector(".needs-validation");
            const submitButton = form.querySelector('button[type="submit"]');

            form.addEventListener("submit", function() {
                submitButton.disabled = true;
                submitButton.innerHTML = "Saving..."; // Optional: Badilisha maandishi
            });
        });
    </script>
  </div>

@endsection
