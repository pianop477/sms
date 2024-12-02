@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title text-center">Select Academic Year of Study</h4>
                </div>
                <div class="col-2">
                    <a href="{{route('home')}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                </div>
            </div>
            @if ($groupedData->isEmpty())
                <div class="alert alert-warning text-center" role="alert">
                    <p>No Results records found!</p>
                </div>
                @else
                <div class="list-group">
                    @foreach ($groupedData as $year => $results)
                        <a href="{{ route('results.byYear', ['course' => $class_course->course_id, 'year' => $year]) }}">
                            <button type="button" class="list-group-item list-group-item-action">
                                <h6 class="text-primary"><i class="fas fa-chevron-right"></i> {{ $year }} Results Link</h6>
                            </button>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
