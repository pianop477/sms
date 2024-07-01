@extends('SRTDashboard.frame')
@section('content')
<div class="row">
    <!-- table primary start -->
    <div class="col-lg-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        @if (isset($message))
                            <h4 class="header-title">{{ $message }}</h4>
                        @else
                            <h4 class="header-title">Courses List for <span class="text-uppercase"><strong>{{$class->class_name}}</strong></span></h4>
                        @endif
                    </div>
                    <div class="col-2">
                        <a href="{{ route('courses.index') }}"><i class="fas fa-circle-arrow-left text-secondary" style="font-size: 2rem;"></i></a>
                    </div>
                </div>
                @if (isset($message))
                    <div class="alert alert-warning" role="alert">
                        <h6 class="text-center">{{ $message }}</h6>
                    </div>
                @elseif ($courses->isEmpty())
                    <div class="alert alert-warning" role="alert">
                        <h6 class="text-center">No courses assigned for this class</h6>
                    </div>
                @else
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-uppercase bg-primary">
                                    <tr class="text-white">
                                        <th scope="col">#</th>
                                        <th scope="col">Course Name</th>
                                        <th scope="col">Course Code</th>
                                        <th scope="col">Subject Teacher</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($courses as $course)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-capitalize">{{ $course->course_name }}</td>
                                            <td class="text-uppercase">{{ $course->course_code }}</td>
                                            <td class="text-capitalize">{{ $course->first_name }} {{ $course->last_name }}</td>
                                            <td>
                                                @if ($course->status == 1)
                                                    <span class="badge bg-success text-white">{{ __('Open') }}</span>
                                                @else
                                                    <span class="badge bg-danger text-white">{{ __('Closed') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <ul class="d-flex justify-content-center">
                                                    @if ($course->status == 1)
                                                    <li class="mr-3">
                                                        <a href="{{route('courses.assign', $course->id)}}"><i class="ti-pencil text-primary"></i></a>
                                                    </li>
                                                        <li class="mr-3">
                                                            <form action="{{route('courses.block', $course->id)}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class=" btn btn-link p-0" onclick="return confirm('Are you sure you want to Block {{strtoupper($course->course_name)}} Course?')"><i class="ti-na text-secondary"></i></button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            {{-- <a href="{{route('courses.delete', $course->id)}}" onclick="return confirm('Are you sure you want to Delete {{strtoupper($course->course_name)}} Course permanently?')"><i class="ti-trash text-danger"></i></a> --}}
                                                        </li>
                                                    @else
                                                        <li>
                                                            <form action="{{route('courses.unblock', $course->id)}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Unblock {{strtoupper($course->course_name)}} Course?')"><i class="ti-share-alt text-success"></i></button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
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
</div>
@endsection
