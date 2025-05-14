@extends('SRTDashboard.frame')
    @section('content')
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <h4 class="header-title text-center"><span class="text-uppercase text-primary">{{$schools->school_name}}</span> Examination Results</h4>
                        </div>
                        <div class="col-2">
                            <a href="{{route('home')}}" class="float-right btn btn-info btn-xs"><i class="fas fa-arrow-circle-left" style=""></i> Back</a>
                        </div>
                    </div>
                    <p class="text-danger">Select year of study</p>
                    <div class="list-group">
                        @if ($groupedData->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Result Records found</h6>
                        </div>
                        @else
                            @foreach ($groupedData as $year => $classes)
                                <a href="{{ route('results.classesByYear', ['school' => Hashids::encode($schools->id), 'year' => $year]) }}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary">>> {{$year}} Results Link</h6>
                                    </button>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection
