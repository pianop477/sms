{{-- resources/views/Examinations/teacher_results.blade.php --}}
@extends('SRTDashboard.frame')

@section('content')
    <div class="row">
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <h3 class="header-title">Examination Results</h3>
                        </div>
                        <div class="col-4">
                            <a href="{{route('score.prepare.form', $courses->id)}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                        </div>
                    </div>
                    @if ($groupedResults->isEmpty())
                        <div class="alert alert-warning text-center">
                            <h5>No Results Records Available</h5>
                        </div>
                        @else
                            @foreach ($groupedResults as $year => $year )
                                <a href="{{ route('exams.byYear', ['year' => $year]) }}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary"> <i class="fas fa-chevron-right"></i> {{$year}} Results Link</h6>
                                    </button>
                                </a>
                            @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
