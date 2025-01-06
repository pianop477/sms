@extends('SRTDashboard.frame')

@section('content')
<div class="col-lg-12">
    <div class="row">
        @php
            use App\Models\Student;
            use Illuminate\Support\Facades\Auth;

            $maleStudents = Student::where('gender', 'male')
                                    ->where('status', 1)
                                    ->where('school_id', Auth::user()->school_id)
                                    ->count();

            $femaleStudents = Student::where('gender', 'female')
                                    ->where('status', 1)
                                    ->where('school_id', Auth::user()->school_id)
                                    ->count();
        @endphp

        <div class="col-md-4 mt-5 mb-3">
            <div class="card" style="background: #e176a6">
                <div class="">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <div class="seofct-icon"><i class="fas fa-user-tie"></i> Teachers</div>
                        <h2 class="text-white">
                            @if (count($teachers) > 29)
                                30+
                            @else
                                {{count($teachers)}}
                            @endif
                        </h2>
                    </div>
                    <canvas id="" height="50"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-5 mb-3">
            <div class="card" style="background: #c84fe0">
                <div class="">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <div class="seofct-icon"><i class="fas fa-user-shield"></i> Parents</div>
                        <h2 class="text-white">
                            @if (count($parents) > 999)
                                1000+
                            @else
                                {{count($parents)}}
                            @endif
                        </h2>
                    </div>
                    <canvas id="" height="50"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-5 mb-3">
            <div class="card" style="background: #098ddf">
                <div class="">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <div class="seofct-icon"><i class="fas fa-user-graduate"></i> Students</div>
                        <h2 class="text-white">
                            @if (count($students) > 999)
                                1000+
                            @else
                                {{count($students)}}
                            @endif
                        </h2>
                        <ul style="font-size: 10px;">
                            <li><span class="text-white text-sm">M:
                                <strong>
                                    @if ($maleStudents > 99)
                                        100+
                                    @else
                                        {{$maleStudents}}
                                    @endif
                                </strong></span></li>
                            <li><span class="text-white text-sm">F:
                                <strong>
                                    @if ($femaleStudents > 99)
                                        100+
                                    @else
                                        {{$femaleStudents}}
                                    @endif
                                </strong></span></li>
                        </ul>
                    </div>
                    <canvas id="" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12">
    <div class="row">
        <div class="col-md-4 mt-5 mb-3">
            <div class="card" style="background: #9fbc71">
                <div class="">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <div class="seofct-icon"><i class="ti-book"></i> Open Courses</div>
                        <h2 class="text-white">
                            @if (count($subjects) > 19)
                                20+
                            @else
                                {{count($subjects)}}
                            @endif
                        </h2>
                    </div>
                    <canvas id="" height="50"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-5 mb-3">
            <div class="card" style="background: #bf950a">
                <div class="">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <div class="seofct-icon"><i class="ti-blackboard"></i> Classes</div>
                        <h2 class="text-white">
                            @if (count($classes) > 9)
                                10+
                            @else
                                {{count($classes)}}
                            @endif
                        </h2>
                    </div>
                    <canvas id="" height="50"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-5 mb-3">
            <div class="card" style="background: #329688">
                <div class="">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <div class="seofct-icon"><i class="fas fa-bus"></i> School Buses</div>
                        <h2 class="text-white">
                            @if (count($buses) > 19)
                                20+
                            @else
                                {{count($buses)}}
                            @endif
                        </h2>
                    </div>
                    <canvas id="" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<hr class="dark horizontal py-0">
<div class="col-lg-12">
    <div class="row">
        <div class="col-md-6 mt-5 mb-3">
            <div class="card">
                <div id="studentsChart" style="height: 400px;"></div>
                <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
                <script>
                    const chartDom = document.getElementById('studentsChart');
                    const myChart = echarts.init(chartDom);

                    const option = {
                        title: {
                            text: 'Students by Classes'
                        },
                        tooltip: {},
                        xAxis: {
                            type: 'category',
                            data: @json($studentsByClass->pluck('class_code'))
                        },
                        yAxis: {
                            type: 'value',
                            min: 0, // Minimum value on the y-axis
                            interval: 5, // Set interval to 1
                            axisLabel: {
                                formatter: '{value}' // Format values as integers
                            }
                        },
                        series: [{
                            data: @json($studentsByClass->pluck('student_count')),
                            type: 'bar',
                            itemStyle: {
                                color: '#5470C6'
                            }
                        }]
                    };

                    myChart.setOption(option);
                </script>
            </div>
        </div>
        <div class="col-md-6 mt-5 mb-3">
            <div class="card">
                <div id="qualificationChart" style="width: 100%; height: 400px;"></div>

                <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
                <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
                <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
                <script>
                    am5.ready(function() {
                        // Create root element
                        var root = am5.Root.new("qualificationChart");

                        // Set themes
                        root.setThemes([am5themes_Animated.new(root)]);

                        // Create chart
                        var chart = root.container.children.push(
                            am5percent.PieChart.new(root, {
                                layout: root.verticalLayout
                            })
                        );

                        // Add chart title
                        chart.children.unshift(
                            am5.Label.new(root, {
                                text: "Teachers by Qualifications",
                                fontSize: 20,
                                fontWeight: "bold",
                                textAlign: "center",
                                x: am5.percent(50),
                                centerX: am5.percent(50)
                            })
                        );

                        // Create series
                        var series = chart.series.push(
                            am5percent.PieSeries.new(root, {
                                valueField: "value",
                                categoryField: "category"
                            })
                        );

                        // Add data
                        series.data.setAll([
                            { category: "Masters", value: {{ $qualificationData['masters'] }} },
                            { category: "Degree", value: {{ $qualificationData['bachelor'] }} },
                            { category: "Diploma", value: {{ $qualificationData['diploma'] }} },
                            { category: "Certificate", value: {{ $qualificationData['certificate'] }} }
                        ]);

                        // Add legend
                        chart.children.push(am5.Legend.new(root, {}));

                        // Animate chart
                        series.appear(1000, 100);
                    });
                </script>
            </div>
        </div>
    </div>
</div>

@endsection

