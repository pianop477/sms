@extends('SRTDashboard.frame')
@section('content')
<div class="col-lg-8 mt-5 align-content-between">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title">Attendance For: <span class="text-uppercase text-primary">{{$student->first_name. ' '. $student->middle_name. ' '.$student->last_name}}</span></h4>
                </div>
                <div class="col-2">
                    <a href="{{route('attendance.byYear', $student->id)}}"><i class="fa-solid fa-circle-arrow-left text-secondary" style="font-size: 2rem"></i></a>
                </div>
            </div>
            <div class="single-table">
                <div class="table-responsive-lg">
                    <table class="table" id="myTable">
                        <thead class="text-capitalize bg-info">
                            <tr class="text-white">
                                <th>Day</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($attendance->isNotEmpty())
                                @foreach ($attendance as $week => $records)
                                    <tr>
                                        <td colspan="3" class="bg-light text-center">Week {{ $week }}</td>
                                    </tr>
                                    @foreach ($records as $item)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($item->attendance_date)->format('D') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->attendance_date)->format('M d, Y') }}</td>
                                            <td>
                                                @if ($item->attendance_status == 'present')
                                                    <span class="badge bg-success text-capitalize text-white">{{$item->attendance_status}}</span>
                                                @elseif ($item->attendance_status == 'absent')
                                                    <span class="badge bg-danger text-capitalize text-white">{{$item->attendance_status}}</span>
                                                @else
                                                    <span class="badge bg-warning text-capitalize text-white">{{$item->attendance_status}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-white text-center">
                                        <div class="alert alert-warning" role="alert">
                                            No Attendance Records Submitted for your Children!
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection