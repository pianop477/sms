@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title text-center">Select Examination Type for {{ $year }}</h4>
            <div class="list-group">
                @foreach ($examTypes as $examType)
                    <a href="{{ route('results.byExamType', ['courses' => $courses->id, 'year' => $year, 'examType' => $examType->first()->exam_type_id]) }}">
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
