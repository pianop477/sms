@extends('SRTDashboard.frame')
    @section('content')
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <h4 class="header-title">Select Year - Attendance Report</h4>
                        </div>
                        <div class="col-2">
                            <a href="{{route('home')}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                        </div>
                    </div>
                    <div class="list-group">
                        @if ($groupedAttendance->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Attendance Records found</h6>
                        </div>
                        @else
                            @foreach ($groupedAttendance as $year => $year )
                                <a href="{{ route('students.show.attendance', ['year' => $year, $student->id])}}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary"><i class="fas fa-chevron-right"></i> Attendance For Academic Year - {{$year}}</h6>
                                    </button>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection
