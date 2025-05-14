@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-center text-uppercase">Select Examination Type - For Year {{$year}}</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{ route('results.index', ['student' => Hashids::encode($students->id)]) }}" class="float-right btn btn-info btn-xs">
                            <i class="fas fa-arrow-circle-left"></i> Back
                        </a>
                    </div>
                </div>
                <p class="text-danger">Select Examination type</p>
                <div class="list-group">
                    @if ($paginated->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Result Records found for {{$year}}</h6>
                        </div>
                    @else
                        @foreach ($paginated  as $item)
                            @if ($item['type'] === 'exam')
                                <a href="{{ route('result.byMonth', ['student' => Hashids::encode($students->id), 'year' => $year, 'exam_type' => Hashids::encode($item['id'])]) }}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary text-capitalize">
                                            <i class="fas fa-file-alt"></i>
                                            {{ $item['label'] }}
                                            <span class="badge bg-primary text-sm text-white">Single</span>
                                        </h6>
                                    </button>
                                </a>
                            @else
                                <a href="{{route('student.combined.report', ['school' => Hashids::encode($students->school_id), 'year' => $year, 'class' => Hashids::encode($students->class_id), 'report' => Hashids::encode($item['id']), 'student' => Hashids::encode($students->id)])}}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-success text-capitalize">
                                            <i class="fas fa-file-pdf"></i>
                                            {{ $item['label'] }}
                                            <span class="badge bg-success text-sm text-white">Combined</span>
                                        </h6>
                                    </button>
                                </a>
                            @endif

                        @endforeach
                        <div class="d-flex justify-content-center">
                            {{$paginated->links('vendor.pagination.bootstrap-5')}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
