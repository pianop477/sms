@extends('SRTDashboard.frame')
    @section('content')
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <h4 class="header-title">Select Examination Type</h4>
                        </div>
                        <div class="col-2">
                            <a href="{{route('results.classesByYear', [$school->id, 'year'=>$year])}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                        </div>
                    </div>
                    <p class="text-danger">Select Examination type to view results</p>
                    <div class="list-group">
                        @if ($groupedByExamType->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Result Records found</h6>
                        </div>
                        @else
                            @foreach ($groupedByExamType as $exam_type_id => $results )
                                <a href="{{ route('results.monthsByExamType', ['school' => $school->id, 'year' => $year, 'class' => $class, 'examType' => $exam_type_id]) }}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary text-capitalize">>> {{ $results->first()->exam_type }}</h6>
                                    </button>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection
