@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-center text-uppercase">daily Attendance report</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{route('get.student.list', $class->id)}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                    </div>
                </div>
                <div class="list-group">
                    <div class="alert alert-warning text-center" role="alert">
                        <h6>{{$message}}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection