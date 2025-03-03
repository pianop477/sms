@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase text-center">Student Lists - {{$transport->driver_name}}</h4>
                    </div>
                    <div class="col-2">
                        @if ($students->isNotEmpty())
                            <h6 class="text-left">
                                <a href="{{route('transport.export', ['trans' => Hashids::encode($transport->id)])}}" target="_blank" class="btn btn-primary btn-xs float-right"><i class="fas fa-cloud-arrow-down"></i> Export</a>
                            </h6>
                        @endif
                    </div>
                    <div class="col-2">
                       <a href="{{route('Transportation.index')}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Student Full name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Class</th>
                                    <th scope="col">Stream</th>
                                    <th scope="col">Parent Phone</th>
                                    <th scope="col">Street</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$student->first_name}} {{$student->middle_name}} {{$student->last_name}}</td>
                                        <td class="text-uppercase">{{$student->gender[0]}}</td>
                                        <td class="text-uppercase">{{$student->class_code}}</td>
                                        <td class="text-uppercase">{{$student->group}}</td>
                                        <td>{{$student->phone}}</td>
                                        <td class="text-capitalize">{{$student->address}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
