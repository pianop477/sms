@extends('SRTDashboard.frame')

@section('content')
<div class="card mt-2">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4 class="header-title text-uppercase">{{strtoupper($results->first()->class_name)}} {{$results->first()->exam_type}} Results -  ({{\Carbon\Carbon::parse($date)->format('d-m-Y')}})</h4>
            </div>
            <div class="col-md-2">
                <a href="{{route('results.monthsByExamType', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($class_id), 'examType' => Hashids::encode($exam_id), 'month' => $month])}}" class="btn btn-info btn-xs float-right">
                    <i class="fas fa-arrow-circle-left"></i> Back
                </a>
            </div>
            <div class="col-md-2">
                <a href="{{ url()->current() }}?export_excel=1" class="btn btn-success btn-xs float-right">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>
        <iframe src="{{ $fileUrl }}" width="100%" height="600px"></iframe>
    </div>
</div>

@endsection
