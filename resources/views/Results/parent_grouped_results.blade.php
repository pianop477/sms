@extends('SRTDashboard.frame')
    @section('content')
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <h4 class="header-title text-center">Taarifa ya Matokeo ya: <span class="text-uppercase text-primary"><strong>({{$student->first_name. ' '. $student->middle_name. ' '. $student->last_name}})</strong></span></h4>
                            <span class="text-danger">Chagua Mwaka wa Masomo - Kuona Matokeo</span>
                        </div>
                        <div class="col-2">
                            <a href="{{route('home')}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                        </div>
                    </div>
                    <div class="list-group">
                        @if ($groupedData->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Result Records found</h6>
                        </div>
                        @else
                            @foreach ($groupedData as $year => $year )
                                <a href="{{route('result.byType', ['year' => $year, $student->id])}}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary"><i class="fas fa-chevron-right"></i> Mwaka wa Masomo - {{$year}}</h6>
                                    </button>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection
