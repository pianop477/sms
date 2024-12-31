@extends('SRTDashboard.frame')
    @section('content')
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <h4 class="header-title text-center text-uppercase">Standard seven - graduated students</h4>
                            <span class="text-danger">Select year to view student lists</span>
                        </div>
                        <div class="col-2">
                            <a href="" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                        </div>
                    </div>
                    <div class="list-group">
                        @if ($studentsByYear->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Records found</h6>
                        </div>
                        @else
                            @foreach ($studentsByYear as $year => $students )
                                <a href="{{route('graduate.student.by.year', ['year' => $year])}}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary"><i class="fas fa-chevron-right"></i> Graduation year - {{$year}}</h6>
                                    </button>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection
