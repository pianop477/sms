@extends('SRTDashboard.frame')
@section('content')
  <div class="card card-body mx-3 mx-md-4 mt-n6">
    <div class="row">
      <div class="col-auto">
        <div class="avatar position-relative">
            @if (!empty($parents->image))
                <img src="{{ asset('assets/img/profile/' . $parents->image) }}" alt="profile_image" class="profile-img border-radius-lg shadow-sm" style="width: 150px; object-fit:cover; border-radius:50px;">
            @else
                <i class="fas fa-user-shield text-secondary" style="font-size: 8rem;"></i>
            @endif
        </div>

      </div>
      <div class="col-auto my-auto">
        <div class="h-100">
          <h5 class="mb-1">
            <span class="text-capitalize">{{$parents->first_name. ' '. $parents->last_name}}</span>
          </h5>
          <p class="mb-0 font-weight-normal text-sm">
            @if ($parents->status == 1)
                <span class="badge bg-success text-white">{{_('Active')}}</span>
                @else
                <span class="badge bg-danger text-white">{{_('Blocked')}}</span>
            @endif
          </p>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-2">
        <a href="{{route('Parents.index')}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
      </div>
    </div>
    <hr class="dark horizontal my-0">
    <div class="card mt-1">
        <div class="card-body">
            <h5 class="text-center">Student List</h5>
            <div class="row">
                @foreach ($students as $student)
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                    <div class="p-3 border rounded shadow-sm">
                        <p><strong>Admission Number: </strong><span class="text-uppercase">{{$student->admission_number}}</span></p>
                        <p><strong>Name:</strong>
                            <span class="text-uppercase" style="text-decoration: underline">
                                <a href="{{route('Students.show', ['student' => Hashids::encode($student->id)])}}">{{$student->first_name}} {{$student->middle_name}} {{$student->last_name}} - {{$student->gender}}</a>
                            </span>
                        </p>
                        <p><strong>Class:</strong> <span class="text-uppercase">{{$student->class_name}} - {{$student->class_code}}</span></p>
                        <form action="{{route('Students.destroy', ['student' => Hashids::encode($student->id)])}}" method="POST">
                            @csrf
                            <button class="btn btn-danger btn-xs p-1" onclick="return confirm('Are you sure you want to block {{strtoupper($student->first_name)}} {{strtoupper($student->middle_name)}} {{strtoupper($student->last_name)}}?')">
                                <i class="ti-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <hr class="dark horizontal my-0">
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title">Update Parent's Details</h4>
        </div>
        <form action="{{route('Parents.update', ['parents' => Hashids::encode($parents->id)])}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">First Name</label>
                    <input type="text" name="fname" class="form-control text-uppercase" value="{{$parents->first_name}}" required>
                    @error('fname')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Last Name</label>
                    <input type="text" name="lname" class="form-control text-uppercase" value="{{$parents->last_name}}" required>
                    @error('lname')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Phone</label>
                    <input type="text" name="phone" class="form-control text-capitalize" value="{{$parents->phone}}" required>
                    @error('phone')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Street/Village</label>
                    <input type="text" name="street" class="form-control text-uppercase" value="{{$parents->address}}" required>
                    @error('street')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Gender</label>
                    <select name="gender" id="" class="form-control text-uppercase" required>
                        <option value="{{$parents->gender}}">{{$parents->gender}}</option>
                        <option value="male">male</option>
                        <option value="female">female</option>
                    </select>
                    @error('gender')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Photo</label>
                    <input type="file" name="image" class="form-control text-capitalize" value="{{old('image')}}">
                    @error('image')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
            </div>
            <button type="submit" class="btn btn-success" id="saveButton">Save changes</button>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton"); // Tafuta button kwa ID

            if (!form || !submitButton) return; // Kama form au button haipo, acha script isifanye kazi

            form.addEventListener("submit", function (event) {
                event.preventDefault(); // Zuia submission ya haraka

                // Disable button na badilisha maandishi
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Please Wait...`;

                // Hakikisha form haina errors kabla ya kutuma
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false; // Warudishe button kama kuna errors
                    submitButton.innerHTML = "Save changes";
                    return;
                }

                // Chelewesha submission kidogo ili button ibadilike kwanza
                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
  </div>
@endsection
