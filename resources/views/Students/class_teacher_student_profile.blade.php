@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-2">
        <div class="row">
            <div class="col-10">
                <h4 class="text-capitalize ">student information</h4>
            </div>
            <div class="col-2">
                <a href="{{ url()->previous() }}" class="btn btn-info float-right btn-xs"><i class="fas fa-arrow-circle-left"></i> Back</a>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3 mt-2">
                <div class="card card-outline" style="border-top: 3px solid blue;">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @if ($students->image == Null)
                                <img src="{{asset('assets/img/students/student.jpg')}}" alt="" class="profile-user img img-fluid rounded-circle" width="300px" height="300px">
                            @else
                                <img src="{{asset('assets/img/students/'. $students->image)}}" alt="" class="profile-user img img-fluid rounded-circle" width="300px" height="300px">
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
                                <span class="float-right text-capitalize">{{$students->gender[0]}}</span>
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
                        {{-- <a href="{{route('students.modify', ['students' => Hashids::encode($students->id)])}}" class="btn btn-block btn-primary" data-toggle="tooltip" data-placement="button" title="Edit"> Edit</a> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-9 mt-2">
                <div class="card">
                    <div class="card-header p-1">
                        <ul class="nav nav-pills flex-column flex-sm-row">
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link active" title="Profile" href="#student" data-toggle="tab"><i class="fas fa-user-graduate"></i> Student Information</a>
                            </li>
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link" href="#parents" title="Parents" data-toggle="tab"><i class="fas fa-user-shield"></i> Parents Information</a>
                            </li>
                            @if ($students->transport_id != Null)
                                <li class="flex-sm-fill text-sm-center nav-item">
                                    <a class="nav-link" href="#transport" title="Transport" data-toggle="tab"><i class="fas fa-bus"></i> Transport Information</a>
                                </li>
                            @endif
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
                                            <td>
                                                {{ucwords(strtolower($students->parent_first_name. ' '. $students->parent_last_name))}}
                                            </td>
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
                                    @else
                                        <tr>
                                            <th colspan="2" class="text-primary"><b>Mothers's Information</b></th>
                                        </tr>
                                        <tr>
                                            <th>Mother's Name</th>
                                            <td>
                                                {{ucwords(strtolower($students->parent_first_name. ' '. $students->parent_last_name))}}
                                            </td>
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
                                    @endif
                                </table>
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
                                            <td class="text-capitalize">{{$students->driver_gender[0]}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>Bus Number</b></th>
                                            <td class="text-capitalize">{{ucwords(strtolower($students->bus_no))}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>School Bus Route</b></th>
                                            <td class="text-capitalize">
                                               {{$students->routine}}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @endif
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
