@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-center text-uppercase">students generated report - {{$reports->title}} ({{$myReportData->first()->class_code}})</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{route('results.examTypesByClass', ['school' => Hashids::encode($reports->school_id), 'year' => $year, 'class' => Hashids::encode($reports->class_id)])}}" class="float-right btn btn-info btn-xs"><i class="fas fa-arrow-circle-left"></i> Back</a>
                    </div>
                </div>
                <table class="table table-responsive-md table-hover table-striped" id="myTable">
                    <thead>
                        <tr class="text-capitalize">
                            <th class="text-center">Adm #</th>
                            <th>Student Name</th>
                            <th>Phone</th>
                            <th>Score</th>
                            <th>Total</th>
                            <th>Average</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($myReportData->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center text-danger">No Students records found</td>
                            </tr>
                        @else
                            @foreach ($myReportData as $student)
                                <tr>
                                    <td class="text-center text-uppercase">{{ $student->admission_number }}</td>
                                    <td class="text-capitalize">{{ ucwords(strtolower($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name)) }}</td>
                                    <td>{{ $student->phone }}</td>
                                    <td>
                                        @php
                                            $total = 0;
                                            $count = 0;
                                        @endphp

                                        @foreach ($reports->exam_dates as $exam_date)
                                            <div class="mb-2">
                                                <strong>{{ \Carbon\Carbon::parse($exam_date)->format('d M, Y') }}</strong>
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @foreach ($allScores[$student->student_id] ?? [] as $course_id => $dates)
                                                        @if (isset($dates[$exam_date]))
                                                            @php
                                                                $score = $dates[$exam_date][0]->score;
                                                                $course = $dates[$exam_date][0]->course_code;
                                                                $total += $score;
                                                                $count++;
                                                            @endphp
                                                            <div class="me-1 mb-1">
                                                                <small>{{ $course }}</small><br>
                                                                <input type="number" value="{{ $score }}" class="form-control form-control-sm" style="width: 60px;" readonly>
                                                            </div>
                                                        @else
                                                            <div class="me-1 mb-1">
                                                                <small>Null</small><br>
                                                                <input type="text" value="ABS" class="form-control form-control-sm text-danger" style="width: 60px;" readonly>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </td>

                                    <td>{{ $total }}</td>
                                    <td>{{ $count > 0 ? round($total / $count, 2) : '-' }}</td>
                                    <td>
                                        <ul class="d-flex justify-content-center list-unstyled mb-0">
                                            <li class="mr-2">
                                                <a href="{{route('students.report', ['school' => Hashids::encode($student->student_school_id), 'year' => $year, 'class' => Hashids::encode($student->student_class_id), 'report' => Hashids::encode($reports->id), 'student' => Hashids::encode($student->studentId)])}}" title="Preview Report" class="btn btn-success btn-xs" onclick="return confirm('Are you sure you want to preview report?')">Preview</a>
                                            </li>
                                            <li>
                                                <form action="{{route('send.sms.combine.report', ['school'=> Hashids::encode($student->student_school_id), 'year'=> $year, 'class'=>Hashids::encode($student->student_class_id), 'report'=> Hashids::encode($reports->id), 'student'=>Hashids::encode($student->studentId)])}}" method="POST">
                                                    @csrf
                                                    <button type="submit" title="Send SMS" class="btn btn-warning btn-xs"  onclick="return confirm('Are you sure you want to Re-send SMS?')">SMS</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <script>

                </script>
            </div>
        </div>
    </div>
@endsection
