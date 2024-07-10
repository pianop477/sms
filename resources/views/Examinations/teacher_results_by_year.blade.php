@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title text-center">Select Academic Year of Study</h4>
            <div class="list-group">
                @foreach ($groupedData as $year => $results)
                    <a href="{{ route('results.byYear', ['courses' => $courses->id, 'year' => $year]) }}">
                        <button type="button" class="list-group-item list-group-item-action">
                            <h6 class="text-primary"><i class="fas fa-chevron-right"></i> {{ $year }} Results Link</h6>
                        </button>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
