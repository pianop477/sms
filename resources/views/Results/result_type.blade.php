@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-center text-uppercase">Chagua aina ya jaribio/Mtihani - Mwaka {{$year}}</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{ route('results.index', $student->id) }}" class="float-right">
                            <i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i>
                        </a>
                    </div>
                </div>
                <div class="list-group">
                    @if ($examTypes->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Result Records found for {{$year}}</h6>
                        </div>
                    @else
                        @foreach ($examTypes as $exam)
                            <a href="{{ route('result.byMonth', ['student' => $student->id, 'year' => $year, 'exam_type' => $exam->exam_id]) }}">
                                <button type="button" class="list-group-item list-group-item-action">
                                    <h6 class="text-primary text-capitalize"><i class="fas fa-chevron-right"></i> {{$exam->exam_type}}</h6>
                                </button>
                            </a>
                        @endforeach
                        <div class="d-flex justify-content-center">
                            {{$examTypes->links('vendor.pagination.bootstrap-5')}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
