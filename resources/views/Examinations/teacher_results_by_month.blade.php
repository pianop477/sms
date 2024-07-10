@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title text-center">Select Month for {{ $year }}</h4>
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
