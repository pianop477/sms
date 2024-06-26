@extends('SRTDashboard.frame')

@section('content')
<div class="col-lg-12">
    <hr class="dark horizontal py-0">
    <div class="card p-2">
        <div class="card-body">
            @if(isset($datas) && $datas->isNotEmpty())
            <h3 class="text-center text-uppercase">{{Auth::user()->school->school_name}} - {{Auth::user()->school->school_reg_no}}</h3>
                @foreach($datas as $date => $teachers)
                    @foreach($teachers as $teacherId => $attendances)
                    <h5 class="text-center">{{ $date }}</h5>
                        <table class="table table-hover table-responsive-md table-borderless">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Gender</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $index => $attendance)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ str_pad($attendance->studentId, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="text-capitalize">{{ $attendance->first_name }} {{ $attendance->middle_name }} {{ $attendance->last_name }}</td>
                                        <td class="text-capitalize">{{ $attendance->gender[0] }}</td>
                                        <td class="text-capitalize">{{ $attendance->group }}</td>
                                        <td>{{ ucfirst($attendance->attendance_status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p>Teacher: {{ $attendances->first()->teacher_firstname }} {{ $attendances->first()->teacher_lastname }}</p>
                        <p>Total Present (Male): {{ $maleSummary[$date]['present'] }}</p>
                        <p>Total Absent (Male): {{ $maleSummary[$date]['absent'] }}</p>
                        <p>Total Permission (Male): {{ $maleSummary[$date]['permission'] }}</p>
                        <p>Total Present (Female): {{ $femaleSummary[$date]['present'] }}</p>
                        <p>Total Absent (Female): {{ $femaleSummary[$date]['absent'] }}</p>
                        <p>Total Permission (Female): {{ $femaleSummary[$date]['permission'] }}</p>
                        <hr>
                    @endforeach
                @endforeach
            @else
                <p>No data available for the selected period.</p>
            @endif
            <button class="btn btn-primary no-print" onclick="scrollToTopAndPrint(); return false;">Print Attendance</button>
        </div>
    </div>
</div>
<div class="footer mt-5" style="position: fixed; bottom: 0; width: 100%; border-top: 1px solid #ddd; padding-top: 10px;">
    <div class="row">
        <div class="col-8">
            <p class="">Printed by: {{ Auth::user()->email}}</p>
        </div>
        <div class="col-4">
            <p class="">{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_text(270, 770, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0,0,0));
        }
    </script>
</div>
@endsection
@section('styles')
<style>
    @media print {
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        @page {
            margin: 20mm;
        }
        thead {
            display: table-header-group;
        }
        tbody {
            display: table-row-group;
        }
    }
</style>
@endsection
