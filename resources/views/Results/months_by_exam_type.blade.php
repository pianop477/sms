@extends('SRTDashboard.frame')
    @section('content')
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <h4 class="header-title">Select Month</h4>
                        </div>
                        <div class="col-2">
                            <a href="{{route('results.examTypesByClass', [$school->id, 'year' => $year, 'class' => $class])}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                        </div>
                    </div>
                    <div class="list-group">
                        @if ($groupedByMonth->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Result Records found</h6>
                        </div>
                        @else
                            @foreach ($groupedByMonth as $month => $results )
                                <a href="{{ route('results.resultsByMonth', ['school' => $school->id, 'year' => $year, 'class' => $class, 'examType' => $examType, 'month' => $month]) }}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary text-capitalize"><i class="fas fa-chevron-right"></i> {{ $month }}</h6>
                                    </button>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection
