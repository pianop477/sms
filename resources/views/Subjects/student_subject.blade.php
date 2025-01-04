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
                            <h4 class="header-title text-center text-uppercase">Orodha ya Masomo kwa Darasa la: <span class="text-uppercase"><strong>{{$class->class_name}}</strong></span></h4>
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
                        <h6 class="text-center">Hakuna Taarifa ya masomo yaliyo sajiliwa kwenye darasa hili</h6>
                    </div>
                @else
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table table-hover table-responsive-md text-center">
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
    <div class="col-lg-4 mt-5">
        <div class="card" style="background: #e696d5;">
            <div class="card-body">
                <h5 class="text-center text-uppercase">Taarifa za Mwalimu wa Darasa</h5>
                <hr>
                @if ($myClassTeacher->isEmpty())
                    <h6 class="text-center">Hakuna mwalimu wa darasa aliyeteuliwa kwa darasa hili!</h6>
                @else
                    <div class="img-container float-right">
                        @if ($myClassTeacher->first()->image == NULL)
                        <i class="fas fa-user-tie" style="font-size: 5rem;"></i>
                        @else
                            <img src="{{asset('assets/img/profile/'.$myClassTeacher->first()->image)}}" alt="" class="" style="max-width: 100px; border-radius:50px;">
                        @endif
                    </div>
                    <ul class="list-group">
                        @foreach ($myClassTeacher as $classTeacher )

                        @endforeach
                        <li class="list-group-items">Jina la Mwalimu:  <span class="text-uppercase font-weight-bold">{{$classTeacher->first_name}} {{$classTeacher->last_name}}</span></li>
                        <li class="list-group-items">Jinsia:  <span class="text-uppercase font-weight-bold">@if ($classTeacher->gender =='female') {{"KE"}} @else {{"ME"}} @endif</span></li>
                        <li class="list-group-items">Namba ya Simu:  <span class="text-uppercase font-weight-bold">{{$classTeacher->phone}}</span></li>
                        <li class="list-group-items">Darasa:  <span class="text-uppercase font-weight-bold">{{$classTeacher->class_name}}</span></li>
                        <li class="list-group-items">Mkondo:  <span class="text-uppercase font-weight-bold">{{$classTeacher->group}}</span></li>
                    </ul>
                @endif
            </div>

        </div>
    </div>

</div>
@endsection
