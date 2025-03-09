@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-uppercase text-center">Parents list</h4>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target=".bd-example-modal-lg">
                            <i class="fas fa-user-plus"></i> New Parent
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"> Parents Registration Form</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('Parents.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <p class="text-danger">A. Parent or Guardian Information</p>
                                            <hr class="dark horizontal py-0">
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">First name</label>
                                                    <input type="text" name="fname" class="form-control" id="validationCustom01" placeholder="" value="{{old('fname')}}" required="">
                                                    @error('fname')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom02">Last name</label>
                                                    <input type="text" name="lname" class="form-control" id="validationCustom02" placeholder="" required="" value="{{old('lname')}}">
                                                    @error('lname')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Email</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                        </div>
                                                        <input type="email" name="email" class="form-control" id="validationCustomUsername" placeholder=" " aria-describedby="inputGroupPrepend" value="{{old('email')}}">
                                                        @error('email')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Gender</label>
                                                    <select name="gender" id="validationCustom01" class="form-control text-capitalize" required>
                                                        <option value="">-- select Parent gender --</option>
                                                        <option value="male">male</option>
                                                        <option value="female">female</option>
                                                    </select>
                                                    @error('gender')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom02">Mobile Phone</label>
                                                    <input type="text" name="phone" class="form-control" id="validationCustom02" placeholder="" required="" value="{{old('phone')}}">
                                                    @error('phone')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Street/Village</label>
                                                    <div class="input-group">
                                                        <input type="text" name="street" class="form-control" id="validationCustom02" value="{{old('street')}}" placeholder="" required>
                                                        @error('street')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="dark horizontal py-0">
                                            <p class="text-danger">B. Student Information</p>
                                            <hr class="dark horizontal py-0">
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomerUsername">Student First name</label>
                                                    <input type="text" name="student_first_name" class="form-control" id="validationCustomer02" value="{{old('student_first_name')}}" placeholder="" required="">
                                                    @error('student_first_name')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomerUsername">Student Middle name</label>
                                                    <input type="text" name="student_middle_name" class="form-control" id="validationCustomer02" value="{{old('student_middle_name')}}" placeholder="" required="">
                                                    @error('student_middle_name')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomerUsername">Student Last name</label>
                                                    <input type="text" name="student_last_name" class="form-control" id="validationCustomer02" value="{{old('student_last_name')}}" placeholder="" required="">
                                                    @error('student_last_name')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="validationCustom01">Student Gender</label>
                                                    <select name="student_gender" id="validationCustom01" class="form-control text-capitalize" required>
                                                        <option value="">-- select Student gender --</option>
                                                        <option value="male">male</option>
                                                        <option value="female">female</option>
                                                    </select>
                                                    @error('student_gender')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="validationCustomerUsername">Student Date of Birth</label>
                                                    <input type="date" name="dob" class="form-control" id="validationCustomer02" value="{{old('dob')}}" required="" min="{{\Carbon\Carbon::now()->subYears(14)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(3)->format('Y-m-d')}}">
                                                    @error('dob')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="validationCustom01">Student Class</label>
                                                    <select name="class" id="validationCustom01" class="form-control text-capitalize" required>
                                                        <option value="">-- select Student Class --</option>
                                                        {{-- classes --}}
                                                        @if ($classes->isEmpty())
                                                            <option value="" disabled class="text-danger">No classes found</option>
                                                        @else
                                                            @foreach ($classes as $class )
                                                                <option value="{{$class->id}}" class="text-uppercase">{{$class->class_name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('class')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="validationCustomerUsername">Class Stream</label>
                                                    <select name="group" id="validationCustom02" required class="form-control">
                                                        <option value="">--Select Stream--</option>
                                                        <option value="a">A</option>
                                                        <option value="b">B</option>
                                                        <option value="c">C</option>
                                                    </select>
                                                    {{-- <input type="text" name="group" class="form-control" id="validationCustomer02" value="{{old('dob')}}" placeholder="Class stream A, B or C" required=""> --}}
                                                    @error('group')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom01">Student Bus Number</label>
                                                    <select name="bus_no" id="validationCustom01" class="form-control text-uppercase">
                                                        <option value="">-- select Student Bus --</option>
                                                        {{-- bus number --}}
                                                        @if ($buses->isEmpty())
                                                            <option value="" class="text-danger" disabled>No buses found</option>
                                                        @else
                                                            @foreach ($buses as $bus )
                                                                <option value="{{$bus->id}}">bus no. {{$bus->bus_no}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('bus_no')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustomerUsername">Photo <span class="text-sm text-danger">Maximum 512KB - Blue background</span></label>
                                                    <input type="file" name="passport" class="form-control" id="validationCustomer02" value="{{old('dob')}}" placeholder="Student Photo">
                                                    @error('passport')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" id="saveButton" class="btn btn-success">Save</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Parent's Name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parents as $parent )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$parent->first_name. ' '. $parent->last_name}}</td>
                                        <td class="text-capitalize">{{$parent->gender[0]}}</td>
                                        <td>{{$parent->phone}}</td>
                                        <td>{{$parent->email ?? 'null'}}</td>
                                        <td>
                                            @if ($parent->status ==1)
                                                <span class="badge bg-success text-white">{{_('Active')}}</span>
                                                @else
                                                <span class="badge bg-danger text-white">{{_('Blocked')}}</span>
                                            @endif
                                        </td>
                                        @if ($parent->status == 1)
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3"><a href="{{route('Parents.edit', ['parent' => Hashids::encode($parent->id)])}}" class="text-primary"><i class="fa fa-eye"></i></a></li>
                                                <li class="mr-3">
                                                    <form action="{{route('Update.parents.status', ['parent' => Hashids::encode($parent->id)])}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Block {{strtoupper($parent->first_name)}} {{strtoupper($parent->last_name)}}?')"><i class="fas fa-ban text-info"></i></button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{route('Parents.remove', ['parent' => Hashids::encode($parent->id)])}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button class="btn btn-link p-0" onclick="return confirm('Are you sure you want to delete {{strtoupper($parent->first_name)}} {{strtoupper($parent->last_name)}} Permanently?')"><i class="ti-trash text-danger"></i></button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                        @else
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <form action="{{route('restore.parents.status', ['parent' => Hashids::encode($parent->id)])}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Unblock {{strtoupper($parent->first_name)}} {{strtoupper($parent->last_name)}}?')"><i class="ti-reload text-success"></i></button>
                                                    </form>
                                                </li>
                                                <li><a href="{{route('Parents.remove', ['parent' => Hashids::encode($parent->id)])}}" onclick="return confirm('Are you sure you want to delete {{strtoupper($parent->first_name)}} {{strtoupper($parent->last_name)}} Permanently?')" class="text-danger"><i class="ti-trash"></i></a></li>
                                            </ul>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
                    submitButton.innerHTML = "Save";
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
