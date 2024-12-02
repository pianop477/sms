@extends('SRTDashboard.frame')
@section('content')
<div class="row">
    <!-- table primary start -->
    <div class="col-lg-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        @if (isset($message))
                            <h4 class="header-title">{{ $message }}</h4>
                        @else
                            <h4 class="header-title">Orodha ya Masomo kwa Darasa la: <span class="text-uppercase"><strong>{{$class->class_name}}</strong></span></h4>
                        @endif
                    </div>
                    <div class="col-4">
                        <a href="{{route('home')}}" class="float-right"><i class="fas fa-circle-arrow-left text-secondary"></i> Back</a>
                    </div>
                </div>
                @if (isset($message))
                    <div class="alert alert-warning" role="alert">
                        <h6 class="text-center">{{ $message }}</h6>
                    </div>
                @elseif ($class_course->isEmpty())
                    <div class="alert alert-warning" role="alert">
                        <h6 class="text-center">Hakuna Taarifa ya masomo yaliyo sajiliwa kwenye darasa hili</h6>
                    </div>
                @else
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-responsive-md text-center">
                                <thead class="text-uppercase">
                                    <tr class="">
                                        <th scope="col">#</th>
                                        <th scope="col">Somo</th>
                                        <th scope="col">Ufupisho</th>
                                        <th scope="col">Jina la Mwalimu wa Somo</th>
                                        <th scope="col">Na. simu ya Mwalimu</th>
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
</div>
@endsection
