@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-center text-uppercase">Select Month - to get Results for {{$year}}</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{ route('result.byType', ['student' => $students->id, 'year' => $year]) }}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                    </div>
                </div>
                <div class="list-group">
                    @if ($months->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>There is no result records for year {{$year}}</h6>
                        </div>
                    @else
                        @foreach ($months as $month)
                            <a href="{{route('results.student.get', ['student' => Hashids::encode($students->id), 'year' => $year, 'type' => Hashids::encode($exam_id), 'month' => $month->month])}}" target="_blank">
                                <button type="button" class="list-group-item list-group-item-action">
                                    <h6 class="text-primary"><i class="fas fa-chevron-right"></i> {{ \Carbon\Carbon::create()->month($month->month)->format('F') }}</h6>
                                </button>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
