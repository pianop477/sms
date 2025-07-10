@extends('SRTDashboard.frame')
@section('content')
<div class="container mt-4">
    <div class="card shadow" style="">
        <div class="card-body">
            {{-- Header Section --}}
            <div class="row align-items-center border-bottom pb-3 mb-3">
                <div class="col-md-2">
                    <img src="{{ asset('assets/img/logo/' .$schoolInfo->logo) }}" alt="School Logo" width="80">
                </div>
                <div class="col-md-8 text-center">
                    <h4 class="mb-1 text-uppercase">{{_('the united republic of tanzania')}}</h4>
                    <h4 class="mb-1 text-uppercase">{{_('president office - tamisemi')}}</h4>
                    <h5 class="mb-1 text-uppercase">{{$schoolInfo->school_name}}</h5>
                    <h6 class="mb-1 text-capitalize">{{$schoolInfo->postal_address}}, {{$schoolInfo->postal_name}} - {{$schoolInfo->country}}</h6>
                    <h6 class="pt-3 text-capitalize">Student's Academic Report</h6>
                    <p class="mb-0 text-center text-capitalize"><strong>{{ $reports->title }} Report - {{\Carbon\Carbon::parse($reports->created_at)->format('d/m/Y')}}</strong></p>
                </div>
                <div class="col-md-2">
                    @if($student->image == null)
                        <img src="{{ asset('assets/img/students/student.jpg') }}" alt="Student" width="300px" height="300px" class="rounded-circle">
                        <p class="text-muted">Student Photo</p>
                    @else
                    <img src="{{asset('assets/img/students/' . $student->image)}}" alt="Student" width="150px" height="150px" class="rounded-circle">
                    <p class="text-muted">Student Photo</p>
                    @endif
                </div>
            </div>

            {{-- Student Details --}}
            <p style="padding: 3px; background:;rgb(187, 163, 56);" class="text-center">Student's Information</p>
            <div class="row mb-3">
                <div class="col-md-5">
                   <strong>Admission Number:</strong><span class="text-uppercase"> {{ $student->admission_number }}</span><br>
                   <strong>Student Name:</strong><span class="text-uppercase"> {{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</span><br>
                    <strong>Gender:</strong><span class="text-uppercase"> {{ ucfirst($student->gender) }}</span>
                </div>
                <div class="col-md-5">
                    <strong>Class:</strong><span class="text-uppercase"> {{ $student->class_name }}</span><br>
                    <strong>Stream:</strong><span class="text-uppercase"> {{ $student->group }}</span><br>
                    <strong>Term:</strong><span class="text-uppercase"> {{ $reports->term }}</span>
                </div>
                {{-- Action Buttons --}}
                <div class="col-md-2">
                    <a href="{{route('students.combined.report', ['school' => $school, 'year' => $year, 'class' => $class, 'report' => $report])}}" class="btn btn-secondary btn-xs float-right"><i class="fas fa-arrow-circle-left"></i> Back</a>
                    <a href="{{route('download.combined.report', ['school'=>$school, 'year'=>$year, 'class' => $class, 'report' => $report, 'student' => Hashids::encode($studentId)])}}" class="btn btn-info btn-xs float-right"><i class="fas fa-download"></i> Download PDF</a>
                </div>
                {{-- Report Table --}}
            </div>
            <div class="table-responsive">
                @if ($reports->combine_option === 'individual')
                    <table class="table table-bordered table-striped" style="">
                        <thead class="">
                            <tr>
                                <th rowspan="2">Subject Name (Code)</th>
                                <th rowspan="2">Teacher</th>
                                <th colspan="{{ count($examHeaders) }}" class="text-center text-capitalize">Examination Scores</th>
                                <th rowspan="2" class="text-center">Total</th>
                                <th rowspan="2" class="text-center">Avg</th>
                                <th rowspan="2" class="text-center">Grade</th>
                                <th rowspan="2" class="text-center">Rank</th>
                                <th rowspan="2" class="text-center">Remarks</th>
                            </tr>
                            <tr>
                                @foreach($examHeaders as $exam)
                                    <th class="compact-header text-center">
                                        <span style="text-transform: uppercase;" class="text-sm">{{ $exam['abbr'] }}</span><br>
                                        <small>{{ \Carbon\Carbon::parse($exam['date'])->format('d M Y') }}</small>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($finalData as $subject)
                                <tr>
                                    <td class="text-capitalize">{{ ucwords(strtolower($subject['subjectName'])) }} <span class="text-uppercase">({{ $subject['subjectCode'] }})</span></td>
                                    <td class="text-capitalize">{{ucwords(strtolower($subject['teacher']))}}</td>

                                    @if($combineOption == 'individual')
                                        {{-- Display all exam dates for each exam type --}}
                                        @foreach($examHeaders as $exam)
                                            <td class="exam-score text-center">{{ $subject['examScores'][$exam['abbr'].'_'.$exam['date']] ?? 'X' }}</td>
                                        @endforeach
                                    @else
                                        {{-- For sum or average, just show the combined score per exam type --}}
                                        @foreach($examHeaders as $abbr)
                                            <td class="text-center">
                                                {{ $subject['examScores'][$abbr] ?? '-' }}
                                            </td>
                                        @endforeach
                                    @endif

                                    <td class="text-center">{{ $subject['total'] }}</td>
                                    <td class="text-center">{{ number_format($subject['average'], 2) }}</td>
                                    <td class="text-center">
                                       @if ($results->first()->marking_style === 1)
                                            @if ($subject['average'] >= 40.5) A
                                            @elseif ($subject['average'] >= 30.5) B
                                            @elseif ($subject['average'] >= 20.5) C
                                            @elseif ($subject['average'] >= 10.5) D
                                            @else E @endif
                                        @else
                                            @if ($subject['average'] >= 80.5) A
                                            @elseif ($subject['average'] >= 60.5) B
                                            @elseif ($subject['average'] >= 40.5) C
                                            @elseif ($subject['average'] >= 20.5) D
                                            @else E @endif
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $subject['position'] }}</td>
                                    <td class="text-center" style="font-style: italic">
                                        @if ($results->first()->marking_style === 1)
                                            @if ($subject['average'] >= 40.5) Excellent
                                            @elseif ($subject['average'] >= 30.5) Good
                                            @elseif ($subject['average'] >= 20.5) Pass
                                            @elseif ($subject['average'] >= 10.5) Poor
                                            @else Fail @endif
                                        @else
                                            @if ($subject['average'] >= 80.5) Excellent
                                            @elseif ($subject['average'] >= 60.5) Good
                                            @elseif ($subject['average'] >= 40.5) Pass
                                            @elseif ($subject['average'] >= 20.5) Poor
                                            @else Fail @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                                <tr>
                                    <td colspan="" class="font-weight-bold">Exam Averages</td>
                                    <td></td>
                                    @foreach ($examHeaders as $exam)
                                        <td class="text-center font-weight-bold">
                                            {{ number_format($examAverages[$exam['abbr'].'_'.$exam['date']] ?? 0, 2) }}
                                        </td>
                                    @endforeach
                                    <td class="text-center font-weight-bold">{{ number_format($sumOfAverages, 2) }}</td>
                                    <td class="text-center font-weight-bold">{{ number_format($studentGeneralAverage, 2) }}</td>
                                    <td class="text-center font-weight-bold">
                                    </td>
                                    <td></td>
                                    <td class="text-center" style="font-style: italic">

                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="" class="font-weight-bold">Exam Grades</td>
                                    <td></td>
                                    @foreach ($examHeaders as $exam)
                                        @php
                                            $examKey = $exam['abbr'].'_'.$exam['date'];
                                            $averageScore = $examAverages[$examKey] ?? 0;
                                        @endphp
                                        <td class="text-center">
                                            @if ($results->first()->marking_style === 1)
                                                @if ($averageScore >= 40.5) A
                                                @elseif ($averageScore >= 30.5) B
                                                @elseif ($averageScore >= 20.5) C
                                                @elseif ($averageScore >= 10.5) D
                                                @else E @endif
                                            @else
                                                @if ($averageScore >= 80.5) A
                                                @elseif ($averageScore >= 60.5) B
                                                @elseif ($averageScore >= 40.5) C
                                                @elseif ($averageScore >= 20.5) D
                                                @else E @endif
                                            @endif
                                        </td>
                                    @endforeach
                                    <td></td>
                                    <td class="text-center" style="">
                                        @if ($results->first()->marking_style === 1)
                                            @if ($studentGeneralAverage >= 40.5)
                                                    A
                                                @elseif ($studentGeneralAverage >= 30.5)
                                                    B
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    C
                                                @elseif ($studentGeneralAverage >= 10.5)
                                                    D
                                                @else
                                                    E
                                                @endif
                                            @else
                                                @if ($studentGeneralAverage >= 80.5)
                                                    A
                                                @elseif ($studentGeneralAverage >= 60.5)
                                                    B
                                                @elseif ($studentGeneralAverage >= 40.5)
                                                    C
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    D
                                                @else
                                                    E
                                            @endif
                                        @endif
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="{{count($examHeaders) + 7}}" class="text-center font-weight-bold">Overall Performance Summary</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        General Average: <span class="font-weight-bold">{{ number_format($studentGeneralAverage, 3) }}</span>
                                    </td>
                                    <td colspan="2" class="text-center">
                                        Grade: <span class="font-weight-bold">
                                            @if ($results->first()->marking_style === 1)
                                                @if ($studentGeneralAverage >= 40.5)
                                                    "A"
                                                @elseif ($studentGeneralAverage >= 30.5)
                                                    "B"
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    "C"
                                                @elseif ($studentGeneralAverage >= 10.5)
                                                    "D"
                                                @else
                                                    "E"
                                                @endif
                                            @else
                                                @if ($studentGeneralAverage >= 80.5)
                                                    "A"
                                                @elseif ($studentGeneralAverage >= 60.5)
                                                    "B"
                                                @elseif ($studentGeneralAverage >= 40.5)
                                                    "C"
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    "D"
                                                @else
                                                    "E"
                                                @endif
                                            @endif
                                    </td>
                                    <td colspan="3">
                                        Position: <span class="font-weight-bold" style="text-decoration:underline">{{ $generalPosition }}</span> out of <span class="font-weight-bold" style="text-decoration:underline">{{ $totalStudents }}</span>
                                    </td>
                                    <td colspan="{{count($examHeaders)}}" class="text-center">
                                        General Remarks:
                                            @if ($results->first()->marking_style === 1)
                                                @if ($studentGeneralAverage >= 40.5)
                                                    <span class="font-weight-bold" style="background: rgb(117, 244, 48); padding: 2px;">EXCELLENT</span>
                                                @elseif ($studentGeneralAverage >= 30.5)
                                                    <span class="font-weight-bold" style="background: rgb(153, 250, 237); padding: 2px;">GOOD</span>
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    <span class="font-weight-bold" style="background: rgb(237, 220, 113); padding: 2px;">PASS</span>
                                                @elseif ($studentGeneralAverage >= 10.5)
                                                    <span class="font-weight-bold" style="background:rgb(182, 176, 176) ; padding: 2xp;">POOR</span>
                                                @else
                                                    <span class="font-weight-bold" style="background:rgb(235, 75, 75) ; padding: 2px;">FAIL</span>
                                                @endif
                                            @else
                                                @if ($studentGeneralAverage >= 80.5)
                                                    <span class="font-weight-bold" style="background: rgb(117, 244, 48); padding: 2px;">EXCELLENT</span>
                                                @elseif ($studentGeneralAverage >= 60.5)
                                                    <span class="font-weight-bold" style="background: rgb(153, 250, 237); padding: 2px;">GOOD</span>
                                                @elseif ($studentGeneralAverage >= 40.5)
                                                    <span class="font-weight-bold" style="background: rgb(237, 220, 113); padding: 2px;">PASS</span>
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    <span class="font-weight-bold" style="background:rgb(182, 176, 176) ; padding: 2xp;">POOR</span>
                                                @else
                                                    <span class="font-weight-bold" style="background:rgb(235, 75, 75) ; padding: 2px;">FAIL</span>
                                                @endif
                                            @endif
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                @elseif ($reports->combine_option === 'sum')
                    <table class="table table-bordered table-striped" style="">
                        <thead class="">
                            <tr>
                                <th rowspan="2">Subject Name (Code)</th>
                                <th rowspan="2">Teacher</th>
                                <th rowspan="2" class="text-center">Total</th>
                                <th rowspan="2" class="text-center">Avg</th>
                                <th rowspan="2" class="text-center">Grade</th>
                                <th rowspan="2" class="text-center">Rank</th>
                                <th rowspan="2" class="text-center">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($finalData as $subject)
                                <tr>
                                    <td class="text-capitalize">{{ ucwords(strtolower($subject['subjectName'])) }} <span class="text-uppercase">({{ $subject['subjectCode'] }})</span></td>
                                    <td class="text-capitalize">{{ucwords(strtolower($subject['teacher']))}}</td>
                                    <td class="text-center">{{ $subject['total'] }}</td>
                                    <td class="text-center">{{ number_format($subject['average'], 2) }}</td>
                                    <td class="text-center">
                                        @if ($results->first()->marking_style === 1)
                                            @if ($subject['average'] >= 40.5)
                                            A
                                            @elseif ($subject['average'] >= 30.5)
                                                B
                                            @elseif ($subject['average'] >= 20.5)
                                                C
                                            @elseif ($subject['average'] >= 10.5)
                                                D
                                            @else
                                                E
                                            @endif
                                        @else
                                            @if ($subject['average'] >= 80.5)
                                                A
                                            @elseif ($subject['average'] >= 60.5)
                                                B
                                            @elseif ($subject['average'] >= 40.5)
                                                C
                                            @elseif ($subject['average'] >= 20.5)
                                                D
                                            @else
                                                E
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $subject['position'] }}</td>
                                    <td class="text-center" style="font-style: italic">
                                        @if ($results->first()->marking_style === 1)
                                            @if ($subject['average'] >= 40.5)
                                            Excellent
                                            @elseif ($subject['average'] >= 30.5)
                                            Good
                                            @elseif ($subject['average'] >= 20.5)
                                                Pass
                                            @elseif ($subject['average'] >= 10.5)
                                                Poor
                                            @else
                                                Fail
                                            @endif
                                        @else
                                            @if ($subject['average'] >= 80.5)
                                                Excellent
                                            @elseif ($subject['average'] >= 60.5)
                                                Good
                                            @elseif ($subject['average'] >= 40.5)
                                                Pass
                                            @elseif ($subject['average'] >= 20.5)
                                                Poor
                                            @else
                                                Fail
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                                <tr>
                                    <td colspan="7" class="text-center font-weight-bold">Overall Performance Summary</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        Total Marks: <span class="font-weight-bold">{{ $totalScoreForStudent }}</span>
                                    </td>
                                    <td colspan="">
                                        General Average: <span class="font-weight-bold">{{ number_format($studentGeneralAverage, 3) }}</span>
                                    </td>
                                    <td colspan="" class="text-center">
                                        Grade: <span class="font-weight-bold">
                                            @if ($results->first()->marking_style === 1)
                                                @if ($studentGeneralAverage >= 40.5)
                                                    "A"
                                                @elseif ($studentGeneralAverage >= 30.5)
                                                    "B"
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    "C"
                                                @elseif ($studentGeneralAverage >= 10.5)
                                                    "D"
                                                @else
                                                    "E"
                                                @endif
                                            @else
                                                @if ($studentGeneralAverage >= 80.5)
                                                    "A"
                                                @elseif ($studentGeneralAverage >= 60.5)
                                                    "B"
                                                @elseif ($studentGeneralAverage >= 40.5)
                                                    "C"
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    "D"
                                                @else
                                                    "E"
                                                @endif
                                            @endif
                                    </td>
                                    <td colspan="">
                                        Position: <span class="font-weight-bold" style="text-decoration:underline">{{ $generalPosition }}</span> out of <span class="font-weight-bold" style="text-decoration:underline">{{ $totalStudents }}</span>
                                    </td>
                                    <td colspan="2" class="text-center">
                                        General Remarks:
                                            @if ($results->first()->marking_style === 1)
                                                @if ($studentGeneralAverage >= 40.5)
                                                    <span class="font-weight-bold" style="background: rgb(117, 244, 48); padding: 2px;">EXCELLENT</span>
                                                @elseif ($studentGeneralAverage >= 30.5)
                                                    <span class="font-weight-bold" style="background: rgb(153, 250, 237); padding: 2px;">GOOD</span>
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    <span class="font-weight-bold" style="background: rgb(237, 220, 113); padding: 2px;">PASS</span>
                                                @elseif ($studentGeneralAverage >= 10.5)
                                                    <span class="font-weight-bold" style="background:rgb(182, 176, 176) ; padding: 2xp;">POOR</span>
                                                @else
                                                    <span class="font-weight-bold" style="background:rgb(235, 75, 75) ; padding: 2px;">FAIL</span>
                                                @endif
                                            @else
                                                @if ($studentGeneralAverage >= 80.5)
                                                    <span class="font-weight-bold" style="background: rgb(117, 244, 48); padding: 2px;">EXCELLENT</span>
                                                @elseif ($studentGeneralAverage >= 60.5)
                                                    <span class="font-weight-bold" style="background: rgb(153, 250, 237); padding: 2px;">GOOD</span>
                                                @elseif ($studentGeneralAverage >= 40.5)
                                                    <span class="font-weight-bold" style="background: rgb(237, 220, 113); padding: 2px;">PASS</span>
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    <span class="font-weight-bold" style="background:rgb(182, 176, 176) ; padding: 2xp;">POOR</span>
                                                @else
                                                    <span class="font-weight-bold" style="background:rgb(235, 75, 75) ; padding: 2px;">FAIL</span>
                                                @endif
                                            @endif
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                @else
                    <table class="table table-bordered table-striped" style="">
                        <thead class="">
                            <tr>
                                <th rowspan="2">Subject Name (Code)</th>
                                <th rowspan="2">Teacher</th>
                                <th rowspan="2" class="text-center">Average</th>
                                <th rowspan="2" class="text-center">Grade</th>
                                <th rowspan="2" class="text-center">Rank</th>
                                <th rowspan="2" class="text-center">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($finalData as $subject)
                                <tr>
                                    <td class="text-capitalize">{{ ucwords(strtolower($subject['subjectName'])) }} <span class="text-uppercase">({{ $subject['subjectCode'] }})</span></td>
                                    <td class="text-capitalize">{{ucwords(strtolower($subject['teacher']))}}</td>
                                    <td class="text-center">{{ number_format($subject['average'], 2) }}</td>
                                    <td class="text-center">
                                        @if ($results->first()->marking_style === 1)
                                            @if ($subject['average'] >= 40.5)
                                            A
                                            @elseif ($subject['average'] >= 30.5)
                                                B
                                            @elseif ($subject['average'] >= 20.5)
                                                C
                                            @elseif ($subject['average'] >= 10.5)
                                                D
                                            @else
                                                E
                                            @endif
                                        @else
                                            @if ($subject['average'] >= 80.5)
                                                A
                                            @elseif ($subject['average'] >= 60.5)
                                                B
                                            @elseif ($subject['average'] >= 40.5)
                                                C
                                            @elseif ($subject['average'] >= 20.5)
                                                D
                                            @else
                                                E
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $subject['position'] }}</td>
                                    <td class="text-center" style="font-style: italic">
                                        @if ($results->first()->marking_style === 1)
                                            @if ($subject['average'] >= 40.5)
                                            Excellent
                                            @elseif ($subject['average'] >= 30.5)
                                            Good
                                            @elseif ($subject['average'] >= 20.5)
                                                Pass
                                            @elseif ($subject['average'] >= 10.5)
                                                Poor
                                            @else
                                                Fail
                                            @endif
                                        @else
                                            @if ($subject['average'] >= 80.5)
                                                Excellent
                                            @elseif ($subject['average'] >= 60.5)
                                                Good
                                            @elseif ($subject['average'] >= 40.5)
                                                Pass
                                            @elseif ($subject['average'] >= 20.5)
                                                Poor
                                            @else
                                                Fail
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                                <tr>
                                    <td colspan="6" class="text-center font-weight-bold">Overall Performance Summary</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        General Average: <span class="font-weight-bold">{{ number_format($studentGeneralAverage, 3) }}</span>
                                    </td>
                                    <td colspan="" class="text-center">
                                        Grade: <span class="font-weight-bold">
                                            @if ($results->first()->marking_style === 1)
                                                @if ($studentGeneralAverage >= 40.5)
                                                    "A"
                                                @elseif ($studentGeneralAverage >= 30.5)
                                                    "B"
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    "C"
                                                @elseif ($studentGeneralAverage >= 10.5)
                                                    "D"
                                                @else
                                                    "E"
                                                @endif
                                            @else
                                                @if ($studentGeneralAverage >= 80.5)
                                                    "A"
                                                @elseif ($studentGeneralAverage >= 60.5)
                                                    "B"
                                                @elseif ($studentGeneralAverage >= 40.5)
                                                    "C"
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    "D"
                                                @else
                                                    "E"
                                                @endif
                                            @endif
                                    </td>
                                    <td colspan="">
                                        Position: <span class="font-weight-bold" style="text-decoration:underline">{{ $generalPosition }}</span> out of <span class="font-weight-bold" style="text-decoration:underline">{{ $totalStudents }}</span>
                                    </td>
                                    <td colspan="2" class="text-center">
                                        General Remarks:
                                            @if ($results->first()->marking_style === 1)
                                                @if ($studentGeneralAverage >= 40.5)
                                                    <span class="font-weight-bold" style="background: rgb(117, 244, 48); padding: 2px;">EXCELLENT</span>
                                                @elseif ($studentGeneralAverage >= 30.5)
                                                    <span class="font-weight-bold" style="background: rgb(153, 250, 237); padding: 2px;">GOOD</span>
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    <span class="font-weight-bold" style="background: rgb(237, 220, 113); padding: 2px;">PASS</span>
                                                @elseif ($studentGeneralAverage >= 10.5)
                                                    <span class="font-weight-bold" style="background:rgb(182, 176, 176) ; padding: 2xp;">POOR</span>
                                                @else
                                                    <span class="font-weight-bold" style="background:rgb(235, 75, 75) ; padding: 2px;">FAIL</span>
                                                @endif
                                            @else
                                                @if ($studentGeneralAverage >= 80.5)
                                                    <span class="font-weight-bold" style="background: rgb(117, 244, 48); padding: 2px;">EXCELLENT</span>
                                                @elseif ($studentGeneralAverage >= 60.5)
                                                    <span class="font-weight-bold" style="background: rgb(153, 250, 237); padding: 2px;">GOOD</span>
                                                @elseif ($studentGeneralAverage >= 40.5)
                                                    <span class="font-weight-bold" style="background: rgb(237, 220, 113); padding: 2px;">PASS</span>
                                                @elseif ($studentGeneralAverage >= 20.5)
                                                    <span class="font-weight-bold" style="background:rgb(182, 176, 176) ; padding: 2xp;">POOR</span>
                                                @else
                                                    <span class="font-weight-bold" style="background:rgb(235, 75, 75) ; padding: 2px;">FAIL</span>
                                                @endif
                                            @endif
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
