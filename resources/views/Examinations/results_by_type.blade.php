{{-- resources/views/Examinations/results_by_type.blade.php --}}
@extends('SRTDashboard.frame')
<style>
    @media screen and (max-width: 570px) {
        .table {
            width: 100px; /* or adjust to an appropriate value */
        }
    }
</style>

@section('content')
    <div class="row">
        <div class="col-md-12 mt-5">
            <div class="text-center text-uppercase">
                <h4>united republic of tanzania</h4>
                <h4>{{_('ministry of education, science and technology (president office - tamisemi)')}}</h4>
                <h5>{{Auth::user()->school->school_name}}</h5>
                <h5>{{ $type }} Results - {{ DateTime::createFromFormat('!m', $results->first()->exam_month)->format('F') }}, {{$year}}</h5>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-10">
                            <ul>
                                <li>
                                    Course Name: <span class="text-uppercase"><strong>{{$summary['course_name'] }}</strong></span>
                                </li>
                                <li>
                                    Class Name: <span class="text-uppercase"><strong>{{ $summary['class_name'] }}</strong></span>
                                </li>
                                <li>
                                    Examination Date: <span><strong>{{\Carbon\Carbon::parse($results->first()->exam_date)->format('d-m-Y')}}</strong></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-2">
                            <ul class="d-flex">
                                <li class="mr-3">
                                    <button class="btn btn-primary no-print" onclick="scrollToTopAndPrint(); return false;">Print</button>
                                </li>
                                <li>
                                    <a href="{{ route('exams.byYear', ['year' => $year])}}" class="btn btn-danger btn-sm no-print">Cancel</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-8">
                            <table class="table table-bordered table-responsive-sm">
                                <tr>
                                    <th colspan="7" class="text-center">Evaluation Summary</th>
                                </tr>
                                <tr>
                                    <th>Grades</th>
                                    <th>A</th>
                                    <th>B</th>
                                    <th>C</th>
                                    <th>D</th>
                                    <th>E</th>
                                    <th>Average</th>
                                </tr>
                                <tr>
                                    <td>Male</td>
                                    <td>{{$summary['grades']['male_A']}}</td>
                                    <td>{{$summary['grades']['male_B']}}</td>
                                    <td>{{$summary['grades']['male_C']}}</td>
                                    <td>{{$summary['grades']['male_D']}}</td>
                                    <td>{{$summary['grades']['male_E']}}</td>
                                    <td rowspan="2" class="text-center">
                                        <strong>{{number_format($summary['average_score'], 4)}}</strong>
                                        @if ($summary['average_score'] <= 10)
                                            <span class="text-danger"><strong>E</strong></span>
                                        @elseif($summary['average_score'] <= 20)
                                            <span class="text-secondary"><strong>D</strong></span>
                                        @elseif ($summary['average_score'] <= 30)
                                            <span class="text-warning"><strong>C</strong></span>
                                        @elseif ($summary['average_score'] <= 40)
                                            <span class="text-primary"><strong>B</strong></span>
                                        @else
                                            <span class="text-success"><strong>A</strong></span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Female</td>
                                    <td>{{$summary['grades']['female_A']}}</td>
                                    <td>{{$summary['grades']['female_B']}}</td>
                                    <td>{{$summary['grades']['female_C']}}</td>
                                    <td>{{$summary['grades']['female_D']}}</td>
                                    <td>{{$summary['grades']['female_E']}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            <table class="table table-bordered table-responsive-sm">
                                <tr>
                                     <th colspan="2" class="text-center">Number of Students</th>
                                </tr>
                                <tr>
                                     <td>M</td>
                                     <td>{{ $summary['total_male'] }}</td>
                                </tr>
                                <tr>
                                     <td>F</td>
                                     <td>{{ $summary['total_female'] }}</td>
                                </tr>
                                <tr>
                                     <td>Total</td>
                                     <td>{{$summary['total_male'] + $summary['total_female']}}</td>
                                </tr>
                             </table>
                        </div>
                    </div>
                    <hr>
                    <h4 class="text-uppercase text-center">Students Examination Scores</h4>
                    <table class="table table-responsive-md table-hover table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>Adm No</th>
                                <th>Student Name</th>
                                <th>Gender</th>
                                <th>Stream</th>
                                <th>Scores</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                                <tbody>
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ str_pad($result->studentId, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="text-uppercase">{{ $result->first_name }} {{ $result->middle_name }} {{ $result->last_name }}</td>
                                        <td class="text-uppercase text-center">{{ substr($result->gender, 0, 1) }}</td>
                                        <td class="text-uppercase text-center">{{$result->group}}</td>
                                        <td class="text-center">{{ $result->score }}</td>
                                        <td class="text-center">
                                            @if($result->score <= 10)
                                                E
                                            @elseif($result->score <= 20)
                                                D
                                            @elseif($result->score <= 30)
                                                C
                                            @elseif($result->score <= 40)
                                                B
                                            @else
                                                A
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
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
