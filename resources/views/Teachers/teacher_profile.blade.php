@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-2">
        <div class="row">
            <div class="col-10">
                <h4 class="text-capitalize ">Teacher information</h4>
            </div>
            <div class="col-2">
                <a href="{{ route('Teachers.index') }}" class="btn btn-info float-right btn-xs"><i class="fas fa-arrow-circle-left"></i> Back</a>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3 mt-2">
                <div class="card card-outline" style="border-top: 3px solid blue;">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @if ($teachers->image == NULL)
                                @if ($teachers->gender == 'male')
                                    <img src="{{asset('assets/img/profile/avatar.jpg')}}" alt="" class="profile-user img img-fluid rounded-circle" style="width: 100px;">
                                @else
                                    <img src="{{asset('assets/img/profile/avatar-female.jpg')}}" alt="" class="profile-user img img-fluid rounded-circle" style="width: 100px;">
                                @endif
                            @else
                                <img src="{{asset('assets/img/profile/'. $teachers->image)}}" alt="" class="profile-user img img-fluid rounded-circle" style="width: 100px;">
                            @endif
                        </div>
                        <h6 class="profile-username text-center text-primary text-uppercase">
                            <b>{{ucwords(strtolower($teachers->first_name. ' '. $teachers->last_name))}}</b>
                        </h6>
                        <p class="text-muted text-center">
                            <b>Member ID #: <span class="text-uppercase"> {{$teachers->member_id}}</span></b>
                        </p>
                        <br>
                        <br>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item">
                                <b>Gender</b>
                                <span class="float-right text-capitalize">{{$teachers->gender[0]}}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Job Title</b>
                                <span class="float-right text-capitalize">{{_('Teacher')}}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Status</b>
                                @if ($teachers->status === 1)
                                    <span class="float-right badge badge-success text-capitalize">Active</span>
                                @else
                                    <span class="float-right badge badge-secondary text-capitalize">Inactive</span>
                                @endif
                            </li>
                        </ul>
                        <a href="{{route('Teachers.show.profile', ['teacher' => Hashids::encode($teachers->id)])}}" class="btn btn-block btn-primary" data-toggle="tooltip" data-placement="button" title="Edit"> Edit</a>
                    </div>
                </div>
            </div>
            <div class="col-md-9 mt-2">
                <div class="card">
                    <div class="card-header p-1">
                        <ul class="nav nav-pills flex-column flex-sm-row">
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link active" title="Profile" href="#teacher" data-toggle="tab"><i class="fas fa-user-tie"></i> Profile</a>
                            </li>
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link" href="#qualification" title="Qualification" data-toggle="tab"><i class="fas fa-award"></i> Qualification</a>
                            </li>
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link" href="#subjects" title="Subjects" data-toggle="tab"><i class="ti-book"></i> Teaching Subjects</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            {{-- profile tab pane --}}
                            <div class="active tab-pane" id="teacher">
                                <p class="text-center text-primary">Personal Particulars</p>
                                <table class="table table-condensed table-responsive-md">
                                    <tbody>
                                        <tr>
                                            <th>Role</th>
                                            <td class="text-capitalize">
                                                <span class="badge bg-primary text-white">{{$teachers->role_name}}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><b><i class="fas fa-phone"></i> Phone</b></th>
                                            <td>{{$teachers->phone}}</td>
                                        </tr>
                                        <tr>
                                            <th><b><i class="fas fa-envelope"></i> Email</b></th>
                                            @if ($teachers->email == Null)
                                                <td class="text-danger">Email not Provided</td>
                                            @else
                                                <td>{{$teachers->email}}</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <th><b>Date of Birth</b></th>
                                            <td>{{\Carbon\Carbon::parse($teachers->dob)->format('d-m-Y')}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>Joined Since</b></th>
                                            <td>{{$teachers->joined}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>Registration Date</b></th>
                                            <td>{{\Carbon\Carbon::parse($teachers->created_at)->format('d-m-Y')}}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-location-dot"></i> <b>Street Address</b></th>
                                            <td class="text-capitalize">{{ucwords(strtolower($teachers->address))}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- parent tab pane --}}
                            <div class="tab-pane" id="qualification">
                                <p class="text-center text-dark"><strong>Qualification Information</strong></span></p>
                                <table class="table table-condensed table-responsive-md">
                                    <tbody>
                                        <th>Qualification</th>
                                        <td>
                                            @if ($teachers->qualification == 1)
                                                <span class="badge bg-success text-white">Masters Degree</span>
                                            @elseif($teachers->qualification == 2)
                                                <span class="badge bg-primary text-white">Bachelor Degree</span>
                                            @elseif($teachers->qualification == 3)
                                                <span class="badge bg-warning">Diploma</span>
                                            @else
                                                <span class="badge bg-secondary text-white">Certificate</span>
                                            @endif
                                        </td>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane" id="subjects">
                                <p class="text-dark text-center">Teaching subject information</p>
                                <table class="table table-condensed table-responsive-md">
                                    <thead>
                                        <th>#</th>
                                        <th>Class</th>
                                        <th>Course</th>
                                    </thead>
                                    <tbody>
                                        @if ($subjects->isEmpty())
                                        <tr>
                                            <td colspan="3" class="text-danger text-center">No courses assigned for you</td>
                                        </tr>
                                        @else
                                            @foreach ($subjects as $subject)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ucwords(strtoupper($subject->class_name))}} - {{ucwords(strtoupper($subject->class_code))}}</td>
                                                    <td>{{ucwords(strtoupper($subject->course_name))}} - {{ucwords(strtoupper($subject->course_code))}}</td>
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
