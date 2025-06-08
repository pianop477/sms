@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-2">
        <div class="row">
            <div class="col-10">
                <h4 class="text-capitalize ">student information</h4>
            </div>
            <div class="col-2">
                <a href="{{route('home')}}" class="btn btn-info float-right btn-xs"><i class="fas fa-arrow-circle-left"></i> Back</a>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3 mt-2">
                <div class="card card-outline" style="border-top: 3px solid blue;">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @if ($students->image == Null)
                                <img src="{{asset('assets/img/students/student.jpg')}}" alt="" class="profile-user img img-fluid rounded-circle" style="width: 100px;">
                            @else
                                <img src="{{asset('assets/img/students/'. $students->image)}}" alt="" class="profile-user img img-fluid rounded-circle" style="width: 100px;">
                            @endif
                        </div>
                        <h6 class="profile-username text-center text-primary text-uppercase">
                            <b>{{ucwords(strtolower($students->first_name. ' '. $students->middle_name. ' '. $students->last_name))}}</b>
                        </h6>
                        <p class="text-muted text-center">
                            <b>Admission #: <span class="text-uppercase"> {{$students->admission_number}}</span></b>
                        </p>
                        <br>
                        <br>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item">
                                <b>Gender</b>
                                <span class="float-right text-capitalize">{{$students->gender}}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Stream</b>
                                <span class="float-right text-capitalize">{{$students->group}}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Status</b>
                                @if ($students->status === 1)
                                    <span class="float-right badge badge-success text-capitalize">Active</span>
                                @else
                                    <span class="float-right badge badge-secondary text-capitalize">Inactive</span>
                                @endif
                            </li>
                        </ul>
                        <a href="{{route('parent.edit.student', ['students' => Hashids::encode($students->id)])}}" class="btn btn-block btn-primary" data-toggle="tooltip" data-placement="button" title="Edit"> Edit</a>
                    </div>
                </div>
            </div>
            <div class="col-md-9 mt-2">
                <div class="card">
                    <div class="card-header p-1">
                        <ul class="nav nav-pills flex-column flex-sm-row">
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link active" title="Profile" href="#student" data-toggle="tab"><i class="fas fa-user-graduate"></i> Student</a>
                            </li>
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link" href="#parents" title="Parents" data-toggle="tab"><i class="fas fa-user-shield"></i> Parents info</a>
                            </li>
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link" href="#subjects" title="Subjects" data-toggle="tab"><i class="ti-book"></i> Subjects</a>
                            </li>
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link" href="#attendance" title="Attendance" data-toggle="tab"><i class="fas fa-calendar-check"></i> Attendances</a>
                            </li>
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link" href="#results" title="Results" data-toggle="tab"><i class="fas fa-chart-bar"></i> Results</a>
                            </li>
                            @if ($students->transport_id != Null)
                                <li class="flex-sm-fill text-sm-center nav-item">
                                    <a class="nav-link" href="#transport" title="Transport" data-toggle="tab"><i class="fas fa-bus"></i> Transport</a>
                                </li>
                            @endif
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link" href="#package" title="Package" data-toggle="tab"><i class="fas fa-layer-group"></i> Packages</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            {{-- profile tab pane --}}
                            <div class="active tab-pane" id="student">
                                <table class="table table-condensed table-responsive-md">
                                    <tbody>
                                        <tr>
                                            <th><b>Class</b></th>
                                            <td class="text-uppercase">{{$students->class_name}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>Date of Birth</b></th>
                                            <td>{{\Carbon\Carbon::parse($students->dob)->format('d-m-Y')}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>Registration Date</b></th>
                                            <td>{{\Carbon\Carbon::parse($students->created_at)->format('d-m-Y')}}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-location-dot"></i> <b>Street Address</b></th>
                                            <td class="text-capitalize">{{ucwords(strtolower($students->address))}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>School Bus</b></th>
                                            <td class="text-capitalize">
                                                @if ($students->transport_id == Null)
                                                    <span class="">No</span>
                                                @else
                                                    <span class="">Yes</span>

                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- parent tab pane --}}
                            <div class="tab-pane" id="parents">
                                <p class="text-center"><strong>Parents/Guardian Details</strong></span></p>
                                <table class="table table-condensed table-responsive-md">
                                    @if ($students->parent_gender == 'male')
                                        <tr>
                                            <th colspan="2" class="text-primary"><b>Father's Information</b></th>
                                        </tr>
                                        <tr>
                                            <th>Father's Name</th>
                                            <td>{{ucwords(strtolower($students->parent_first_name. ' '. $students->parent_last_name))}}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-phone"></i> Phone</th>
                                            <td>
                                                <a href="tel:{{ $students->phone }}" class="">
                                                    {{ $students->phone }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-envelope"></i> Email</th>
                                            <td>
                                                @if ($students->email == NULL)
                                                    <span class="text-danger">No email provided</span>
                                                @else
                                                    <a href="mailto:{{ $students->parent_email }}" class="text-decoration-none text-dark">
                                                        {{ $students->email }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-location-dot"></i> Street Address</th>
                                            <td class="text-capitalize">{{ucwords(strtolower($students->address))}}</td>
                                        </tr>
                                        <tr>
                                            <th>Registration Date</th>
                                            <td>{{\Carbon\Carbon::parse($students->parent_created_at)->format('d-m-Y')}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <th colspan="2" class="text-primary"><b>Mothers's Information</b></th>
                                        </tr>
                                        <tr>
                                            <th>Mother's Name</th>
                                            <td>{{ucwords(strtolower($students->parent_first_name. ' '. $students->parent_last_name))}}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-phone"></i> Phone</th>
                                            <td>
                                                <a href="tel:{{ $students->parent_phone }}" class="">
                                                    {{ $students->phone }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-envelope"></i> Email</th>
                                            <td>
                                                @if ($students->email == NULL)
                                                    <span class="text-danger">No email provided</span>
                                                @else
                                                    <a href="mailto:{{ $students->parent_email }}" class="text-decoration-none text-dark">
                                                        {{ $students->email }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-location-dot"></i> Street Address</th>
                                            <td class="text-capitalize">{{ucwords(strtolower($students->address))}}</td>
                                        </tr>
                                        <tr>
                                            <th>Registration Date</th>
                                            <td>{{\Carbon\Carbon::parse($students->parent_created_at)->format('d-m-Y')}}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            {{-- subjects tab pane --}}
                            <div class="tab-pane" id="subjects">
                                <p>Subject Teachers for: <span class="text-uppercase"><strong>{{$class->class_name}}</strong></span></p>
                                <table class="table table-condensed table-responsive-md">
                                    <thead>
                                        <th scope="col">#</th>
                                        <th scope="col" class="">Subject</th>
                                        <th scope="col" class="">Code</th>
                                        <th scope="col">Teacher</th>
                                        <th scope="col">Phone</th>
                                    </thead>
                                    <tbody>
                                        @if ($class_course->isEmpty())
                                           <tr>
                                                <td colspan="5" class="text-danger text-center">No Available subjects assigned!</td>
                                            </tr>
                                        @else
                                            @foreach ($class_course as $course)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-capitalize">{{ ucwords(strtolower($course->course_name)) }}</td>
                                                <td class="text-uppercase">{{ $course->course_code }}</td>
                                                <td class="d-flex align-items-center">
                                                    @if (!empty($course->image) && file_exists(public_path('assets/img/profile/' . $course->image)))
                                                        <img src="{{ asset('assets/img/profile/' . $course->image) }}"
                                                            alt="Profile Picture"
                                                            class="rounded-circle"
                                                            style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                                    @else
                                                        <i class="fas fa-user-tie rounded-circle bg-secondary d-flex justify-content-center align-items-center"
                                                        style="width: 40px; height: 40px; font-size: 20px; color: white;"></i>
                                                    @endif
                                                    <span class="text-capitalize ms-2" style="margin-left: 5px">{{ ucwords(strtolower($course->first_name. ' '. $course->last_name)) }}</span>
                                                </td>
                                                <td class="">
                                                    <i class="fas fa-phone"></i>
                                                    <a href="tel:{{ $course->teacher_phone }}" class="text-decoration-none text-dark">
                                                        {{ $course->teacher_phone }}
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <hr>
                                <p class="text-uppercase"><b>Class Teacher</b></p>
                                <ul class="list-group list-group-flush">
                                    @if ($myClassTeacher->isEmpty())
                                        <li class="list-group-item text-center text-danger">
                                            No class teacher assigned!
                                        </li>
                                    @else
                                        @foreach ($myClassTeacher as $classTeacher)
                                            <div class="d-flex align-items-center">
                                                <div class="img-container mr-5">
                                                    @if ($classTeacher->image == NULL)
                                                        <i class="fas fa-user-tie" style="font-size: 5rem;"></i>
                                                    @else
                                                        <img src="{{ asset('assets/img/profile/' . $classTeacher->image) }}"
                                                            alt=""
                                                            style="max-width: 80px; border-radius:50px;">
                                                    @endif
                                                </div>
                                                <ul class="list-group w-100">
                                                    <li class="list-group-item">Name:
                                                        <span class="text-uppercase font-weight-bold float-right">
                                                            {{ ucwords(strtolower($classTeacher->first_name. ' '. $classTeacher->last_name)) }}
                                                        </span>
                                                    </li>
                                                    <li class="list-group-item">Gender:
                                                        <span class="text-capitalize font-weight-bold float-right">
                                                            {{ ucwords(strtolower($classTeacher->gender)) }}
                                                        </span>
                                                    </li>
                                                    <li class="list-group-item">Phone:
                                                        <span class="font-weight-bold float-right">
                                                            <i class="fas fa-phone"></i>
                                                            <a href="tel:{{ $classTeacher->phone }}" class="text-decoration-none text-dark">
                                                                {{ $classTeacher->phone }}
                                                            </a>
                                                        </span>
                                                    </li>
                                                    <li class="list-group-item">Class:
                                                        <span class="text-uppercase font-weight-bold float-right">
                                                            {{ $classTeacher->class_name }} - {{ $classTeacher->group }}
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                            @if (!$loop->last)
                                                <hr> {{-- Separator between teachers --}}
                                            @endif
                                        @endforeach
                                    @endif
                                </ul>
                            </div>

                            {{-- attendance tab pane --}}
                            <div class="tab-pane" id="attendance">
                                <p>Attendance for: <span class="text-uppercase"><strong>{{$students->first_name. ' '. $students->last_name}}</strong></span></p>
                                <ul class="list-group">
                                    <a href="{{route('attendance.byYear', ['student' => Hashids::encode($students->id)])}}">
                                        <li class="list-group-item">
                                            <h6 class="text-primary">>> Attendance Reports</h6>
                                        </li>
                                    </a>
                                </ul>
                            </div>

                            {{-- results tab pane --}}
                            <div class="tab-pane" id="results">
                                <p>Results Report for: <span class="text-uppercase"><strong>{{$students->first_name. ' '. $students->last_name}}</strong></span></p>
                                <ul class="list-group">
                                    <a href="{{route('results.index', ['student' => Hashids::encode($students->id)])}}">
                                        <li class="list-group-item">
                                            <h6 class="text-primary">>> Results Reports</h6>
                                        </li>
                                    </a>
                                </ul>
                            </div>

                            {{-- transport tab-pane --}}
                            @if ($students->transport_id != Null)
                            <div class="tab-pane" id="transport">
                                <table class="table table-condensed table-responsive-md">
                                    <tbody>
                                        <tr>
                                            <th><b>Driver Name</b></th>
                                            <td class="">{{ucwords(strtolower($students->driver_name))}}</td>
                                        </tr>
                                        <tr>
                                            <th><b><i class="fas fa-phone"></i> Phone</b></th>
                                            <td>
                                                <a href="tel:{{$students->driver_phone}}">
                                                    {{$students->driver_phone}}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><b>Gender</b></th>
                                            <td class="text-capitalize">{{ucwords(strtolower($students->driver_gender))}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>Bus Number</b></th>
                                            <td class="text-capitalize">{{ucwords(strtolower($students->bus_no))}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>School Bus Route</b></th>
                                            <td class="text-capitalize">
                                               {{ucwords(strtolower($students->routine))}}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            {{-- holiday package tab-pane --}}
                            <div class="tab-pane" id="package">
                                <p class="text-center text-primary">Holiday Package for <span class="text-uppercase"><strong>{{$class->class_name}}</strong></span></p>
                                <table class="table table-responsive-md table-striped table-bordered table-hover">
                                    <thead>
                                        <tr class="text-capitalize">
                                            <th scope="col">title</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">term</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">released at</th>
                                            <th scope="col">Expire on</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($packages->isEmpty())
                                            <tr>
                                                <td colspan="9" class="text-danger text-center">No Holiday Package Available!</td>
                                            </tr>
                                        @else
                                            @foreach ($packages as $item )
                                                <tr class="text-capitalize">
                                                    <td>{{ucwords(strtolower($item->title))}}</td>
                                                    <td>{{ucwords(strtolower($item->description))}}</td>
                                                    <td>term {{ucwords(strtolower($item->term))}}</td>
                                                    <td>
                                                        @if ($item->is_active == true)
                                                            <span class="badge badge-success">Active</i></span>
                                                        @else
                                                            <span class="badge badge-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>{{$item->release_date ?? 'Null'}}</td>
                                                    <td>{{$item->due_date ?? 'Null'}}</td>
                                                    <td>
                                                        @if ($item->is_active == true)
                                                        <a href="{{route('student.holiday.package', ['id' => Hashids::encode($item->id), 'preview' => true])}}" target="_blank" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Download Package" onclick="return confirm('Are you sure you want to download this package?')">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                        @else
                                                        <a href="" data-toggle="tooltip" data-placement="top" title="Package locked" class="btn btn-xs btn-danger disabled">
                                                            <i class="fas fa-lock"></i> Locked
                                                        </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

</script>
@endsection
