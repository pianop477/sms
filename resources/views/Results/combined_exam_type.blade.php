@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-center text-uppercase">Select Combined Report - For Year {{$year}}</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{route('results.examTypesByClass', ['school' => $school, 'year' => $year, 'class' => $class])}}" class="float-right btn btn-info btn-xs">
                            <i class="fas fa-arrow-circle-left" style=";"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="list-group">
                    @if ($combinedResults->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Result Records found for {{$year}}</h6>
                        </div>
                    @else
                        @foreach ($groupedReportName as $exam => $combinedResults)
                            <a href="{{route('combinedResults.byMonth', ['class' => $class, 'year' => $year, 'school' => $school->id, 'exam' => $exam])}}">
                                <button type="button" class="list-group-item list-group-item-action">
                                    <h6 class="text-primary text-capitalize"><i class="fas fa-chevron-right"></i> {{$exam}}</h6>
                                </button>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
