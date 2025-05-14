@extends('SRTDashboard.frame')
    @section('content')
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <h4 class="header-title text-center">Result Report For: <span class="text-uppercase text-primary"><strong>({{$students->first_name. ' '. $students->middle_name. ' '. $students->last_name}})</strong></span></h4>
                            <span class="text-danger">Select Year of Study - To view Results</span>
                        </div>
                        <div class="col-2">
                            <a href="{{route('home')}}" class="float-right btn btn-info btn-xs"><i class="fas fa-arrow-circle-left" style=""></i> Back</a>
                        </div>
                    </div>
                    <div class="list-group">
                        @if ($groupedData->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Result Records found</h6>
                        </div>
                        @else
                            @foreach ($groupedData as $year => $year )
                                <a href="{{route('result.byType', ['year' => $year, 'student' => Hashids::encode($students->id)])}}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary"><i class="fas fa-chevron-right"></i> Academic Year - {{$year}}</h6>
                                    </button>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection
