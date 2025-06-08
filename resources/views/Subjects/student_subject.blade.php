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
                            <table class="table table-hover table-responsive-md">
                                <thead class="text-uppercase">
                                    <tr class="">
                                        <th scope="col">No</th>
                                        <th scope="col" class="">Subject</th>
                                        <th scope="col" class="">code</th>
                                        <th scope="col">Subject Teacher</th>
                                        <th scope="col">Teacher's Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                <h5 class="text-center text-uppercase">Class Teacher Details</h5>
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
                                        {{ ucwords(strtolower($classTeacher->first_name. ' '. $classTeacher->last_name)) }}
                                    </span>
                                </li>
                                <li class="list-group-item">Gender:
                                    <span class="text-capitalize font-weight-bold">
                                        {{ $classTeacher->gender }}
                                    </span>
                                </li>
                                <li class="list-group-item">Call:
                                    <span class="font-weight-bold">
                                        <i class="fas fa-phone"></i>
                                        <a href="tel:{{ $classTeacher->phone }}" class="text-decoration-none text-dark">
                                            {{ $classTeacher->phone }}
                                        </a>
                                    </span>
                                </li>
                                <li class="list-group-item">Class:
                                    <span class="text-uppercase font-weight-bold">
                                        {{ ucwords(strtolower($classTeacher->class_name)) }} - {{ ucwords(strtoupper($classTeacher->group)) }}
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
