@extends('SRTDashboard.frame')
@section('content')
<div class="row">
    <!-- table primary start -->
    <div class="col-lg-8 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        @if (isset($message))
                            <h4 class="header-title">{{ $message }}</h4>
                        @else
                            <h4 class="header-title text-center text-uppercase">Learning Subjects for Class: <span class="text-uppercase"><strong>{{$class->class_name}}</strong></span></h4>
                        @endif
                    </div>
                    <div class="col-4">
                        <a href="{{route('home')}}" class="float-right btn btn-info btn-xs"><i class="fas fa-circle-arrow-left"></i> Back</a>
                    </div>
                </div>
                @if (isset($message))
                    <div class="alert alert-warning" role="alert">
                        <h6 class="text-center">{{ $message }}</h6>
                    </div>
                @elseif ($class_course->isEmpty())
                    <div class="alert alert-warning" role="alert">
                        <h6 class="text-center">No any subjects records found</h6>
                    </div>
                @else
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table table-hover table-responsive-md text-center">
                                <thead class="text-uppercase">
                                    <tr class="">
                                        <th scope="col">No</th>
                                        <th scope="col">Subject</th>
                                        <th scope="col">code</th>
                                        <th scope="col">Subject Teacher Name</th>
                                        <th scope="col">Teacher's Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($class_course as $course)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-capitalize">{{ $course->course_name }}</td>
                                            <td class="text-uppercase">{{ $course->course_code }}</td>
                                            <td class="text-capitalize">{{ $course->first_name }} {{ $course->last_name }}</td>
                                            <td>
                                                {{$course->phone}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- table primary end -->
    @if ($myClassTeacher->isEmpty())
        <div class="col-lg-12 mt-5">
            <div class="card" style="background: #e696d5;">
                <div class="card-body text-center">
                    <h5 class="text-uppercase">Class Teacher Particulars</h5>
                    <hr>
                    <h6>No class teacher has been assigned for this class!</h6>
                </div>
            </div>
        </div>
    @else
        @foreach ($myClassTeacher as $classTeacher)
            <div class="col-lg-4 mt-5">
                <div class="card" style="background: #e696d5;">
                    <div class="card-body">
                        <h5 class="text-center text-uppercase">Class Teacher Particulars</h5>
                        <hr>
                        <div class="img-container float-right">
                            @if ($classTeacher->image == NULL)
                                <i class="fas fa-user-tie" style="font-size: 5rem;"></i>
                            @else
                                <img src="{{ asset('assets/img/profile/' . $classTeacher->image) }}"
                                    alt=""
                                    class=""
                                    style="max-width: 100px; border-radius:50px;">
                            @endif
                        </div>
                        <ul class="list-group">
                            <li class="list-group-items">Teacher's Name:
                                <span class="text-uppercase font-weight-bold">
                                    {{ $classTeacher->first_name }} {{ $classTeacher->last_name }}
                                </span>
                            </li>
                            <li class="list-group-items">Gender:
                                <span class="text-uppercase font-weight-bold">
                                    {{ $classTeacher->gender[0] }}
                                </span>
                            </li>
                            <li class="list-group-items">Phone Number:
                                <span class="text-uppercase font-weight-bold">
                                    {{ $classTeacher->phone }}
                                </span>
                            </li>
                            <li class="list-group-items">Class:
                                <span class="text-uppercase font-weight-bold">
                                    {{ $classTeacher->class_name }}
                                </span>
                            </li>
                            <li class="list-group-items">Stream:
                                <span class="text-uppercase font-weight-bold">
                                    {{ $classTeacher->group }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
