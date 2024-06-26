@extends('SRTDashboard.frame')
    @section('content')
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <h4 class="header-title">{{$year}} Examination Results for <span class="text-uppercase text-primary"><strong>({{$student->first_name. ' '. $student->middle_name. ' '. $student->last_name}})</strong></span></h4>
                        </div>
                        <div class="col-2">
                            <a href="{{route('results.index', $student->id)}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                        </div>
                    </div>
                    <div class="list-group">
                        @if ($examType->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Result Records found</h6>
                        </div>
                        @else
                            @foreach ($examType as $exam )
                                <a href="{{ route('results.student.get', ['year' => $year, 'type' => $exam->exam_type, $student->id]) }}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary text-capitalize"><i class="fas fa-chevron-right"></i> {{$exam->exam_type}} - {{ DateTime::createFromFormat('!m', $exam->exam_month)->format('F') }}</h6>
                                    </button>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection
