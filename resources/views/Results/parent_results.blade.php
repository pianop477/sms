@extends('SRTDashboard.frame')
    @section('content')
        <div class="row">
            <div class="col-md-12">
                <div class="text-uppercase text-center">
                    <div class="row">
                        <div class="col-10">
                            <h4>{{Auth::user()->school->school_name}}</h4>
                            <h5>{{$type}} Results Report</h5>
                            <h6>{{ DateTime::createFromFormat('!m', $results->first()->exam_month)->format('F') }} - {{$year}}</h6>
                        </div>
                        <div class="col-1">
                            <a href="{{route('result.byType', ['year' => $year, $student->id])}}" class="float-right no-print"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                        </div>
                        <div class="col-1">
                            <a href="#" onclick="scrollToTopAndPrint(); return false;" class="no-print"><i class="ti-printer text-secondary" style="font-size: 2rem;"></i></a>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <ul>
                            <li>
                                Student Name: <span class="text-uppercase font-weight-bold"> {{$results->first()->first_name}} {{$results->first()->middle_name}} {{$results->first()->last_name}}</span>
                            </li>
                            <li>
                                Gender: <span class="text-uppercase font-weight-bold">{{$results->first()->gender}}</span>
                            </li>
                            <li>
                                Class: <span class="text-uppercase font-weight-bold"> {{$results->first()->class_name}} ({{$results->first()->class_code}})</span>
                            </li>
                            <li>
                                Stream: <span class="text-uppercase font-weight-bold">{{$results->first()->group}}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul>
                            <li>
                                Examination Type: <span class="text-uppercase font-weight-bold"> {{$type}}</span>
                            </li>
                            <li>
                                Examination Date: <span class="text-uppercase font-weight-bold"> {{ \Carbon\Carbon::parse($results->first()->exam_date)->format('d-M-Y')}}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-4">
                        <ul>
                            <li>
                                Term: <span class="text-uppercase font-weight-bold">{{$results->first()->Exam_term}}</span>
                            </li>
                            <li>
                                Year of Study: <span class="text-uppercase font-weight-bold">{{$year}}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="container">
                    <table class="table table-hover table-bordered border-1">
                        <tr>
                            <thead>
                                <th colspan="5" class="text-uppercase text-center" style="background-color: gray;">Student Results Records</th>
                            </thead>
                        </tr>
                        <tr>
                            <thead>
                                <tr class="text-uppercase">
                                    <th>#</th>
                                    <th>Course Name</th>
                                    <th>Marks</th>
                                    <th>Grade</th>
                                    <th>Teacher's Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $result)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$result->course_name}}</td>
                                        <td class="text-capitalize">{{$result->score}}</td>
                                        <td class="text-capitalize">
                                            @if ($result->score <= 10 && $result->score >= 0)
                                                <span class="text-danger font-weight-bold">E</span>
                                            @elseif ($result->score <= 20 && $result->score >= 11)
                                                <span class="text-secondary font-weight-bold">D</span>
                                            @elseif ($result->score <= 30 && $result->score >= 21)
                                                <span class="text-info font-weight-bold">C</span>
                                            @elseif ($result->score <= 40 && $result->score >= 31)
                                                <span class="text-primary font-weight-bold">B</span>
                                            @else
                                                <span class="text-success font-weight-bold">A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($result->score <= 10 && $result->score >= 0)
                                                <i class="font-weight-bold">Failed</i>
                                            @elseif ($result->score <= 20 && $result->score >= 11)
                                                <i class="font-weight-bold">Poor</i>
                                            @elseif ($result->score <= 30 && $result->score >= 21)
                                                <i class="font-weight-bold">Average</i>
                                            @elseif ($result->score <= 40 && $result->score >= 31)
                                                <i class="font-weight-bold">Good</i>
                                            @else
                                                <i class="font-weight-bold">Excellent</i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tr>
                                <th colspan="2" class="text-center">
                                    Total Marks: <strong>{{$summary['total_marks']}}</strong>

                                </th>
                                <th class="text-center" colspan="2">
                                    General Average: <strong>{{number_format($summary['average'], 4)}}</strong>
                                    @if ($summary['average'] <= 10 && $summary['average'] >= 0)
                                                <span class="text-danger font-weight-bold">E</span>
                                            @elseif ($summary['average'] <= 20 && $summary['average'] >= 11)
                                                <span class="text-secondary font-weight-bold">D</span>
                                            @elseif ($summary['average'] <= 30 && $summary['average'] >= 21)
                                                <span class="text-info font-weight-bold">C</span>
                                            @elseif ($summary['average'] <= 40 && $summary['average'] >= 31)
                                                <span class="text-primary font-weight-bold">B</span>
                                            @else
                                                <span class="text-success font-weight-bold">A</span>
                                            @endif
                                </th>
                                <th>
                                    @if ($summary['average'] <= 10 && $summary['average'] >= 0)
                                                <i class="font-weight-bold text-danger">Failed</i>
                                            @elseif ($summary['average'] <= 20 && $summary['average'] >= 11)
                                                <i class="font-weight-bold text-secondary">Poor</i>
                                            @elseif ($summary['average'] <= 30 && $summary['average'] >= 21)
                                                <i class="font-weight-bold text-info">Average</i>
                                            @elseif ($summary['average'] <= 40 && $summary['average'] >= 31)
                                                <i class="font-weight-bold text-primary">Good</i>
                                            @else
                                                <i class="font-weight-bold text-success">Excellent</i>
                                            @endif
                                </th>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-center">
                                    <span class="alert alert-success" style="background-color: lightgreen">Position: <strong>{{$currentStudentPosition}}</strong> out of <strong>{{count($positions)}}</strong></span>
                                </td>
                            </tr>
                        </tr>
                    </table>
                    <hr>
                    <div class="text-center font-weight-bold">
                        <p class="text-uppercase font-weight-bold"><i>End of Report</i></p>
                        <hr>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
<div class="footer mt-5" style="position: fixed; bottom: 0; width: 100%; text-align: center; border-top: 1px solid #ddd; padding-top: 10px;">
    <p class="">Printed by: {{ Auth::user()->email}} on {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
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
