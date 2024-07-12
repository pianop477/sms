@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title text-center">Select Month for {{ $year }}</h4>
                </div>
                <div class="col-2">
                    <a href="{{route('results.byYear', ['year'=>$year, 'courses' => $courses->id])}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                </div>
            </div>
            <div class="list-group">
                @foreach ($months as $month => $results)
                    <a href="{{ route('results.byMonth', ['courses' => $courses->id, 'year' => $year, 'examType' => $examType, 'month' => $month]) }}">
                        <button type="button" class="list-group-item list-group-item-action">
                            <h6 class="text-primary"><i class="fas fa-chevron-right"></i> {{ $month }} - {{$year}}</h6>
                        </button>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
