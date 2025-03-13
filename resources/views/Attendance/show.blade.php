@extends('SRTDashboard.frame')
@section('content')
<div class="col-lg-12 mt-5 align-content-between">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title">Student Name: <span class="text-uppercase text-primary">{{ $firstRecord->student_firstname }} {{ $firstRecord->student_middlename }} {{ $firstRecord->student_lastname }}</span></h4>
                </div>
                <div class="col-2">
                    <a href="{{route('attendance.byYear', ['student' => Hashids::encode($students->id)])}}"><i class="fa-solid fa-circle-arrow-left text-secondary" style="font-size: 2rem"></i></a>
                </div>
            </div>
            <div class="single-table">
                <div class="table-responsive-lg">
                    <table class="table table-bordered text-center" id="myTable">
                        <thead class="text-capitalize bg-primary">
                            <tr class="text-white">
                                <th>Day</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($groupedData->isNotEmpty())
                                @foreach ($groupedData as $week => $records)
                                    <tr>
                                        <td colspan="3" class="bg-light text-center">Week {{ $week }}</td>
                                    </tr>
                                    @foreach ($records as $item)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($item->attendance_date)->format('D') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->attendance_date)->format('M d, Y') }}</td>
                                            <td>
                                                @if ($item->attendance_status == 'present')
                                                    <span class="badge bg-success text-capitalize text-white">Present</span>
                                                @elseif ($item->attendance_status == 'absent')
                                                    <span class="badge bg-danger text-capitalize text-white">Absent</span>
                                                @else
                                                    <span class="badge bg-warning text-capitalize text-white">Permission</span>
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
            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
                {{ $groupedData->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
