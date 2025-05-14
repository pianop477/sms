@extends('SRTDashboard.frame')

@section('content')
<div class="card mt-2">
    <div class="card-body">
        <div class="row">
            <div class="col-md-10">
                <h4 class="header-title text-capitalize">{{$reports->title }} - {{$year}}</h4>
            </div>
            <div class="col-md-2">
                <a href="{{route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class])}}" class="btn btn-info btn-xs float-right">
                    <i class="fas fa-arrow-circle-left"></i> Back
                </a>
            </div>
        </div>
        <iframe src="{{ $fileUrl }}" width="100%" height="600px"></iframe>
    </div>
</div>

@endsection
