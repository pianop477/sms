@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-center text-uppercase">Generate Attendance report</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{url()->previous()}}" class="float-right btn btn-info"><i class="fas fa-arrow-circle-left" style=""></i> Back</a>
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
