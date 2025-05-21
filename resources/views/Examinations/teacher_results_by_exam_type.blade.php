@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title text-center">Select Examination Type for {{ $year }}</h4>
                </div>
                <div class="col-2">
                    <a href="{{route('results_byCourse', ['id' => Hashids::encode($class_course->id)])}}" class="float-right btn btn-xs btn-info"><i class="fas fa-arrow-circle-left"></i> Back</a>
                </div>
            </div>
            <p class="text-danger">Click to select Examination type</p>
            <div class="list-group">
                @foreach ($examTypes as $examType)
                    <a href="{{ route('results.byExamType', ['course' => Hashids::encode($class_course->id), 'year' => $year, 'examType' => Hashids::encode($examType->first()->exam_type_id)]) }}">
                        <button type="button" class="list-group-item list-group-item-action">
                            <h6 class="text-primary text-capitalize"><i class="fas fa-chevron-right"></i> {{ $examType->first()->exam_type }}</h6>
                        </button>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
