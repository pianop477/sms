@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-center">Examination Results - <span class="text-primary">{{$year}}</span></h4>
                    </div>
                    <div class="col-2">
                        <a href="{{route('results.general', $school->id)}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                    </div>
                </div>
                <p class="text-danger">Select Class you want to view results</p>
                <div class="list-group">
                    @if ($groupedByClass->isEmpty())
                    <div class="alert alert-warning text-center" role="alert">
                        <h6>No Result Records found</h6>
                    </div>
                    @else
                        @foreach ($groupedByClass as $class_id => $results)
                                <a href="{{ route('results.examTypesByClass', ['school' => $school->id, 'year' => $year, 'class' => $class_id]) }}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary"><span class="text-uppercase">>> {{ $results->first()->class_name }}</span> - Link</h6>
                                    </button>
                                </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
