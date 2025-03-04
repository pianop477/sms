@extends('SRTDashboard.frame')
@section('content')
<div class="row">
    <!-- table primary start -->
    <div class="col-md-8 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        @if (isset($message))
                            <h4 class="header-title">{{ $message }}</h4>
                        @else
                            <h4 class="header-title text-center text-uppercase">Subject Teachers for: <span class="text-uppercase"><strong>{{$class->class_name}}</strong></span></h4>
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
    <div class="col-sm-4 mt-5">
        <div class="card" style="background: #e696d5;">
            <div class="card-body">
                <h5 class="text-center text-uppercase">Class Teacher Particulars</h5>
                <hr>

                @if ($myClassTeacher->isEmpty())
                    <h6 class="text-center">No class teacher has been assigned for this class!</h6>
                @else
                    @foreach ($myClassTeacher as $classTeacher)
                        <div class="d-flex align-items-center">
                            <div class="img-container mr-3">
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
                                    <span class="text-uppercase font-weight-bold">
                                        {{ $classTeacher->first_name }} {{ $classTeacher->last_name }}
                                    </span>
                                </li>
                                <li class="list-group-item">Gender:
                                    <span class="text-uppercase font-weight-bold">
                                        {{ $classTeacher->gender[0] }}
                                    </span>
                                </li>
                                <li class="list-group-item">Phone:
                                    <span class="text-uppercase font-weight-bold">
                                        {{ $classTeacher->phone }}
                                    </span>
                                </li>
                                <li class="list-group-item">Class:
                                    <span class="text-uppercase font-weight-bold">
                                        {{ $classTeacher->class_name }}
                                    </span>
                                </li>
                                <li class="list-group-item">Stream:
                                    <span class="text-uppercase font-weight-bold">
                                        {{ $classTeacher->group }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        @if (!$loop->last)
                            <hr> {{-- Separator between teachers --}}
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
