@extends('SRTDashboard.frame')
@section('content')
<div class="col-lg-12">
    {{-- first argument======================================================== --}}
    @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 2)
        {{-- school head teacher panel start here --}}

        {{-- display contract status for head teacher --}}
        <div class="row">
            <div class="col-6">
                @if ($contract == null)
                    <p class="alert alert-danger text-capitalize">contract status: <strong>Not applied.</strong> | please apply <a href="{{route('contract.index')}}">here</a></p>
                @else
                    @if($contract->status == 'expired')
                        <p class="text-capitalize alert alert-danger">contract status: <strong>{{$contract->status}}</strong></p>
                        @elseif ($contract->status == 'rejected')
                            <p class="text-capitalize alert alert-secondary">contract status: <strong>{{$contract->status}}</strong> | view <a href="{{route('contract.index')}}">here</a></p>
                        @elseif ($contract->status == 'approved' && $contract->end_date <= now()->addDays(30))
                            <p class="text-capitalize alert alert-warning">contract status: <strong>expiring soon</strong></p>
                        @elseif ($contract->status == 'pending')
                            <p class="text-capitalize alert alert-info">contract status: <strong> {{$contract->status}}</strong> | view <a href="{{route('contract.index')}}"> here</a></p>
                        @else
                        <p class="text-capitalize alert alert-success">contract status: <strong>active</strong> | view contract <a href="{{route('contract.index')}}"> here</a></p>
                    @endif
                @endif
            </div>
            <div class="col-6">
                <p>
                    <a href="{{route('sms.form')}}" class="float-right alert alert-primary animated-colors">
                        <i class="ti-announcement" style=""></i>
                        Public Notice
                    </a>
                </p>
            </div>

            <style>
            @keyframes colorChange {
                0% { background-color: #007bff; } /* Primary Blue */
                25% { background-color: #e47beb; } /* Green */
                50% { background-color: #ffc107; } /* Yellow */
                75% { background-color: #ef1128; } /* Red */
                100% { background-color: #007bff; } /* Back to Blue */
            }

            .animated-colors {
                animation: colorChange 5s infinite alternate;
                transition: background-color 0.5s ease-in-out;
            }
            </style>
        </div>
            <div class="row">
                <div class="col-md-4 mt-3 mb-3">
                    <a href="">
                        <div class="card" style="background: #e176a6;">
                            <div class="{{route('Teachers.index')}}">
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
                    </a>
                </div>
                <div class="col-md-4 mt-md-3 mb-3">
                    <a href="{{route('Parents.index')}}">
                        <div class="card" style="background: #c84fe0;">
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
                    </a>
                </div>
                <div class="col-md-4 mt-md-3 mb-3">
                    <a href="{{route('classes.list')}}">
                        <div class="card" style="background: #098ddf;">
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
                                </div>
                                <canvas id="" height="50"></canvas>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-4 mt-2 mb-3">
                    <a href="">
                        <div class="card" style="background: #9fbc71;">
                            <div class="{{route('courses.index')}}">
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
                    </a>
                </div>
                <div class="col-md-4 mt-md-2 mb-3">
                    <a href="{{route('Classes.index')}}">
                        <div class="card" style="background: #bf950a;">
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
                    </a>
                </div>
                <div class="col-md-4 mt-md-2 mb-3">
                    <a href="{{route('Transportation.index')}}">
                        <div class="card" style="background: #329688;">
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
                    </a>
                </div>
            </div>
        </div>
        <hr class="dark horizontal py-0">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-6 mt-2 mb-3">
                    <div class="card">
                        <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
                        <div id="studentChart" style="width: 100%; height: 400px;"></div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                // Initialize ECharts instance
                                var chartDom = document.getElementById('studentChart');
                                var myChart = echarts.init(chartDom);

                                // Prepare data
                                var chartData = @json($chartData); // Assuming $chartData contains raw data from the backend

                                // Group data by class
                                var groupedData = {};
                                chartData.forEach(item => {
                                    var classCode = item.category.split(' (')[0];
                                    var gender = item.category.includes('Male') ? 'Male' : 'Female';
                                    if (!groupedData[classCode]) {
                                        groupedData[classCode] = { Male: 0, Female: 0 };
                                    }
                                    groupedData[classCode][gender] = item.value;
                                });

                                // Extract x-axis labels and series data
                                var classCodes = Object.keys(groupedData);
                                var maleData = classCodes.map(classCode => groupedData[classCode].Male);
                                var femaleData = classCodes.map(classCode => groupedData[classCode].Female);

                                // Chart options
                                var option = {
                                    title: {
                                        text: 'Student Registration by Gender',
                                        left: 'center'
                                    },
                                    tooltip: {
                                        trigger: 'axis',
                                        axisPointer: {
                                            type: 'shadow'
                                        }
                                    },
                                    legend: {
                                        bottom: 10,
                                        data: ['Male', 'Female']
                                    },
                                    grid: {
                                        left: '3%',
                                        right: '4%',
                                        bottom: '3%',
                                        containLabel: true
                                    },
                                    xAxis: {
                                        type: 'category',
                                        data: classCodes, // Unique class codes
                                        axisLabel: {
                                            rotate: 45
                                        }
                                    },
                                    yAxis: {
                                        type: 'value'
                                    },
                                    series: [
                                        {
                                            name: 'Male',
                                            type: 'bar',
                                            stack: 'total',
                                            label: {
                                                show: true
                                            },
                                            data: maleData // Male data per class
                                        },
                                        {
                                            name: 'Female',
                                            type: 'bar',
                                            stack: 'total',
                                            label: {
                                                show: true
                                            },
                                            data: femaleData // Female data per class
                                        }
                                    ]
                                };

                                // Set options and render the chart
                                myChart.setOption(option);
                            });
                        </script>
                    </div>
                </div>
                <div class="col-md-6 mt-2 mb-3">
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
        <hr class="dark horizontal py-0">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-4 mt-2 mb-3">
                    <div class="card">
                        <div class="card-title mt-2">
                            <h6 class="text-center">Students Registration</h6>
                        </div>
                        <table class="table table-sm table-hover table-responsive-sm table-bordered table-sm" style="background: #e3d39e">
                            @if ($studentsByClass->isEmpty())
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="2" class="text-center">No records Available</td>
                                </tr>
                            </tbody>
                            @else
                            <thead>
                                <tr class="text-center">
                                    <th>Class</th>
                                    @foreach ($studentsByClass as $class)
                                        <th class="text-uppercase">{{$class->class_code}}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td>Students</td>
                                    @foreach ($studentsByClass as $class )
                                        <td>{{$class->student_count}}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                            @endif
                        </table>
                        <hr class="dark horizontal py-0">
                        <div class="card-title">
                            <h6 class="text-center mt-2">Teachers Registration</h6>
                        </div>
                        <table class="table table-sm table-responsive-sm table-bordered table-hover" style="background: #e4abcf;">
                            <thead>
                                <tr class="text-center">
                                    <th>Gender</th>
                                    <th>Number of Teachers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($teacherByGender->isEmpty())
                                <tr>
                                    <td colspan="2" class="text-center">No records Available</td>
                                </tr>
                                @else
                                    @foreach ($teacherByGender as $teacher)
                                    <tr class="text-center">
                                        <td>{{ ucfirst($teacher->gender) }}</td>
                                        <td>{{ $teacher->teacher_count }}</td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4 mt-2 mb-3">
                    <div class="card">
                        <div class="card-title mt-2">
                            <h6 class="text-center">Total Students</h6>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
                        <div style="width: 280px; height: 280px; margin: 0 auto;">
                            <canvas id="genderChart"></canvas>
                        </div>
                        <script>
                            const totalMaleStudents = @json($totalMaleStudents);
                            const totalFemaleStudents = @json($totalFemaleStudents);

                            const ctxGender = document.getElementById('genderChart').getContext('2d');
                            const genderChart = new Chart(ctxGender, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Male Students', 'Female Students'],
                                    datasets: [{
                                        label: 'Student Gender Distribution',
                                        data: [totalMaleStudents, totalFemaleStudents],
                                        backgroundColor: [
                                            'rgba(54, 162, 235, 0.7)', // Blue
                                            'rgba(255, 99, 132, 0.7)'  // Red
                                        ],
                                        borderColor: [
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 99, 132, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const label = context.label || '';
                                                    const value = context.raw || 0;
                                                    return `${label}: ${value}`;
                                                }
                                            }
                                        },
                                        datalabels: {
                                            color: '#fff', // Label text color
                                            formatter: (value, ctxGender) => {
                                                return value; // Display the raw value
                                            },
                                            font: {
                                                weight: 'bold',
                                                size: 12
                                            },
                                            anchor: 'center', // Position inside the curve
                                            align: 'center'
                                        }
                                    }
                                },
                                plugins: [ChartDataLabels] // Register the plugin
                            });
                        </script>
                    </div>
                </div>
                <div class="col-lg-4 mt-2 mb-3">
                    <div class="card">
                        <div class="card-title mt-2">
                            <h6 class="text-center">Today's Attendance: {{\Carbon\Carbon::parse($today)->format('d-m-Y')}}</h6>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
                        <div style="width: 280px; height: 280px; margin: 0 auto;">
                            @if ($attendanceCounts['present'] > 0 || $attendanceCounts['absent'] > 0 || $attendanceCounts['permission'] > 0)
                                <canvas id="attendanceChart" width="400" height="400"></canvas>
                            @else
                                <p class="text-center mt-5 text-danger">No attendance records for today.</p>
                            @endif

                        </div>
                        <script>
                            (function() {
                                // Check if attendance data exists
                                const attendanceData = @json($attendanceCounts);

                                // Ensure data exists and at least one value is greater than zero
                                const hasData = attendanceData.present > 0 || attendanceData.absent > 0 || attendanceData.permission > 0;

                                // Find the canvas element
                                const ctxAttendance = document.getElementById('attendanceChart');

                                if (ctxAttendance && hasData) { // Only proceed if canvas and data are valid
                                    const attendanceChart = new Chart(ctxAttendance.getContext('2d'), {
                                        type: 'doughnut',
                                        data: {
                                            labels: ['Present', 'Absent', 'Permission'],
                                            datasets: [{
                                                label: 'Today\'s Attendance',
                                                data: [attendanceData.present, attendanceData.absent, attendanceData.permission],
                                                backgroundColor: [
                                                    'rgba(75, 192, 192, 0.7)', // Green for Present
                                                    'rgba(255, 99, 132, 0.7)', // Red for Absent
                                                    'rgba(255, 206, 86, 0.7)'  // Yellow for Permission
                                                ],
                                                borderColor: [
                                                    'rgba(75, 192, 192, 1)',
                                                    'rgba(255, 99, 132, 1)',
                                                    'rgba(255, 206, 86, 1)'
                                                ],
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: {
                                                    position: 'top',
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            const label = context.label || '';
                                                            const value = context.raw || 0;
                                                            return `${label}: ${value}`;
                                                        }
                                                    }
                                                },
                                                datalabels: {
                                                    color: '#fff',
                                                    formatter: (value, ctxAttendance) => {
                                                        return value;
                                                    },
                                                    font: {
                                                        weight: 'bold',
                                                        size: 12
                                                    },
                                                    anchor: 'center',
                                                    align: 'center'
                                                }
                                            }
                                        },
                                        plugins: [ChartDataLabels] // Register the plugin
                                    });
                                } else if (!hasData) {
                                    // Optional: Display a message if no attendance data
                                    document.getElementById('attendance-container').innerHTML = `
                                        <p class="text-center text-muted">No attendance records found for today.</p>`;
                                }
                            })();
                        </script>
                    </div>
                </div>
            </div>
        </div>
        {{-- school head teacher panel end here --}}
        {{-- first argument end here ============================================= --}}
    @elseif (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 3)
        {{-- second argument start here =========================================== --}}
        {{-- academic teacher panel start here =================== --}}

        {{-- check for academic contract  --}}
        <div class="row">
            <div class="col-6">
                @if ($contract == null)
                    <p class="alert alert-danger text-capitalize">contract status: <strong>Not applied.</strong> | please apply <a href="{{route('contract.index')}}">here</a></p>
                @else
                    @if($contract->status == 'expired')
                        <p class="text-capitalize alert alert-danger">contract status: <strong>{{$contract->status}}</strong></p>
                        @elseif ($contract->status == 'rejected')
                            <p class="text-capitalize alert alert-secondary">contract status: <strong>{{$contract->status}}</strong> | view <a href="{{route('contract.index')}}">here</a></p>
                        @elseif ($contract->status == 'approved' && $contract->end_date <= now()->addDays(30))
                            <p class="text-capitalize alert alert-warning">contract status: <strong>expiring soon</strong></p>
                        @elseif ($contract->status == 'pending')
                            <p class="text-capitalize alert alert-info">contract status: <strong> {{$contract->status}}</strong> | view <a href="{{route('contract.index')}}"> here</a></p>
                        @else
                        <p class="text-capitalize alert alert-success">contract status: <strong>active</strong> | view contract <a href="{{route('contract.index')}}"> here</a></p>
                    @endif
                @endif
            </div>
            <div class="col-6">
                <p>
                    <a href="{{route('sms.form')}}" class="float-right alert alert-primary animated-colors">
                        <i class="ti-announcement" style=""></i>
                        Public Notice
                    </a>
                </p>
            </div>

            <style>
            @keyframes colorChange {
                0% { background-color: #007bff; } /* Primary Blue */
                25% { background-color: #e47beb; } /* Green */
                50% { background-color: #ffc107; } /* Yellow */
                75% { background-color: #ef1128; } /* Red */
                100% { background-color: #007bff; } /* Back to Blue */
            }

            .animated-colors {
                animation: colorChange 5s infinite alternate;
                transition: background-color 0.5s ease-in-out;
            }
            </style>
        </div>
            <div class="row">
                <div class="col-md-4 mt-3 mb-3">
                    <a href="{{route('Teachers.index')}}">
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
                    </a>
                </div>
                <div class="col-md-4 mt-md-3 mb-3">
                    <a href="{{route('classes.list')}}">
                        <div class="card" style="background: #098ddf">
                            <div class="">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="fas fa-user-graduate"></i> Students</div>
                                    <h2 class="text-white">
                                        @if(count($students) > 999)
                                            1000+
                                        @else
                                            {{count($students)}}
                                        @endif
                                    </h2>
                                </div>
                                <canvas id="" height="50"></canvas>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mt-3 mb-3">
                    <a href="{{route('courses.index')}}">
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
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-4 mt-md-2 mb-3">
                    <a href="{{route('Classes.index')}}">
                        <div class="card" style="background:#bf950a">
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
                    </a>
                </div>
                <div class="col-md-4 mt-2 mb-3">
                    <div class="card" style="background: #b14fbe">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-book"></i> My Course</div>
                                <h2 class="text-white">
                                    @if ( $courses->where('status', 1)->count() > 2)
                                        3+
                                    @else
                                        {{ $courses->where('status', 1)->count() }}
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
        {{-- academic panel end here =========================================== --}}
        {{-- academic teacher its courses records start here ====================== --}}
        <div class="row">
            <div class="col-md-6 mt-0 mb-3">
                <div class="card">
                    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
                    <div id="studentChart" style="width: 100%; height: 400px;"></div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            // Initialize ECharts instance
                            var chartDom = document.getElementById('studentChart');
                            var myChart = echarts.init(chartDom);

                            // Prepare data
                            var chartData = @json($chartData); // Assuming $chartData contains raw data from the backend

                            // Group data by class
                            var groupedData = {};
                            chartData.forEach(item => {
                                var classCode = item.category.split(' (')[0];
                                var gender = item.category.includes('Male') ? 'Male' : 'Female';
                                if (!groupedData[classCode]) {
                                    groupedData[classCode] = { Male: 0, Female: 0 };
                                }
                                groupedData[classCode][gender] = item.value;
                            });

                            // Extract x-axis labels and series data
                            var classCodes = Object.keys(groupedData);
                            var maleData = classCodes.map(classCode => groupedData[classCode].Male);
                            var femaleData = classCodes.map(classCode => groupedData[classCode].Female);

                            // Chart options
                            var option = {
                                title: {
                                    text: 'Student Registration by Gender',
                                    left: 'center'
                                },
                                tooltip: {
                                    trigger: 'axis',
                                    axisPointer: {
                                        type: 'shadow'
                                    }
                                },
                                legend: {
                                    bottom: 10,
                                    data: ['Male', 'Female']
                                },
                                grid: {
                                    left: '3%',
                                    right: '4%',
                                    bottom: '3%',
                                    containLabel: true
                                },
                                xAxis: {
                                    type: 'category',
                                    data: classCodes, // Unique class codes
                                    axisLabel: {
                                        rotate: 45
                                    }
                                },
                                yAxis: {
                                    type: 'value'
                                },
                                series: [
                                    {
                                        name: 'Male',
                                        type: 'bar',
                                        stack: 'total',
                                        label: {
                                            show: true
                                        },
                                        data: maleData // Male data per class
                                    },
                                    {
                                        name: 'Female',
                                        type: 'bar',
                                        stack: 'total',
                                        label: {
                                            show: true
                                        },
                                        data: femaleData // Female data per class
                                    }
                                ]
                            };

                            // Set options and render the chart
                            myChart.setOption(option);
                        });
                    </script>

                </div>

            </div>
            <div class="col-lg-6 mt-0">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-10"><h4 class="header-title text-uppercase text-center"> My Courses</h4></div>
                        </div>
                        <div class="table-responsive-md">
                            <table class="table table-hover text-center" id="">
                                <thead>
                                    <tr class="text-capitalize">
                                        <th>Subject</th>
                                        <th>Class</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($courses->isEmpty())
                                        <tr>
                                            <td colspan="5">
                                                <div class="alert alert-warning text-center">
                                                    No any subject assigned for you
                                                </div>
                                            </td>
                                        </tr>
                                        @else
                                        @foreach ($courses as $course )
                                        <tr>
                                            <td class="text-uppercase">
                                                {{$course->course_name}}
                                            </td>
                                            <td class="text-uppercase">{{$course->class_code}}</td>
                                            <td>
                                                @if ($course->status == 1)
                                                <ul class="d-flex justify-content-center">
                                                    <div class="btn-group" role="group">
                                                        <button id="btnGroupDrop" type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Manage
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                            <a class="dropdown-item" href="{{route('under.construction.page')}}"><i class="ti-pencil-alt"></i> Score</a>
                                                            {{-- <a class="dropdown-item" href="{{route('score.prepare.form', ['id' => Hashids::encode($course->id)])}}"><i class="ti-pencil-alt"></i> Score</a> --}}
                                                            <a class="dropdown-item" href="{{ route('results_byCourse', ['id' => Hashids::encode($course->id)]) }}"><i class="ti-file"></i> Results</a>
                                                        </div>
                                                    </div>
                                                </ul>
                                                @else
                                                    <span class="badge bg-danger text-white">{{_('Blocked')}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="dark horizontal py-0">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-4 mt-2 mb-3">
                    <div class="card">
                        <div class="card-title mt-2">
                            <h6 class="text-center">Students Registration</h6>
                        </div>
                        <table class="table table-sm table-hover table-responsive-sm table-bordered table-sm" style="background: #e3d39e">
                            @if ($studentsByClass->isEmpty())
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="2" class="text-center">No records Available</td>
                                </tr>
                            </tbody>
                            @else
                            <thead>
                                <tr class="text-center">
                                    <th>Class</th>
                                    @foreach ($studentsByClass as $class)
                                        <th class="text-uppercase">{{$class->class_code}}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td>Students</td>
                                    @foreach ($studentsByClass as $class )
                                        <td>{{$class->student_count}}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                            @endif
                        </table>
                        <hr class="dark horizontal py-0">
                        <div class="card-title">
                            <h6 class="text-center mt-2">Teachers Registration</h6>
                        </div>
                        <table class="table table-sm table-responsive-sm table-bordered table-hover" style="background: #e4abcf;">
                            <thead>
                                <tr class="text-center">
                                    <th>Gender</th>
                                    <th>Number of Teachers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($teacherByGender->isEmpty())
                                <tr>
                                    <td colspan="2" class="text-center">No records Available</td>
                                </tr>
                                @else
                                    @foreach ($teacherByGender as $teacher)
                                    <tr class="text-center">
                                        <td>{{ ucfirst($teacher->gender) }}</td>
                                        <td>{{ $teacher->teacher_count }}</td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4 mt-2 mb-3">
                    <div class="card">
                        <div class="card-title mt-2">
                            <h6 class="text-center">Total Students</h6>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
                        <div style="width: 280px; height: 280px; margin: 0 auto;">
                            <canvas id="genderChart"></canvas>
                        </div>
                        <script>
                            const totalMaleStudents = @json($totalMaleStudents);
                            const totalFemaleStudents = @json($totalFemaleStudents);

                            const ctxGender = document.getElementById('genderChart').getContext('2d');
                            const genderChart = new Chart(ctxGender, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Male Students', 'Female Students'],
                                    datasets: [{
                                        label: 'Student Gender Distribution',
                                        data: [totalMaleStudents, totalFemaleStudents],
                                        backgroundColor: [
                                            'rgba(54, 162, 235, 0.7)', // Blue
                                            'rgba(255, 99, 132, 0.7)'  // Red
                                        ],
                                        borderColor: [
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 99, 132, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const label = context.label || '';
                                                    const value = context.raw || 0;
                                                    return `${label}: ${value}`;
                                                }
                                            }
                                        },
                                        datalabels: {
                                            color: '#fff', // Label text color
                                            formatter: (value, ctxGender) => {
                                                return value; // Display the raw value
                                            },
                                            font: {
                                                weight: 'bold',
                                                size: 12
                                            },
                                            anchor: 'center', // Position inside the curve
                                            align: 'center'
                                        }
                                    }
                                },
                                plugins: [ChartDataLabels] // Register the plugin
                            });
                        </script>
                    </div>
                </div>
                <div class="col-lg-4 mt-2 mb-3">
                    <div class="card">
                        <div class="card-title mt-2">
                            <h6 class="text-center">Today's Attendance: {{\Carbon\Carbon::parse($today)->format('d-m-Y')}}</h6>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
                        <div style="width: 280px; height: 280px; margin: 0 auto;">
                            @if ($attendanceCounts['present'] > 0 || $attendanceCounts['absent'] > 0 || $attendanceCounts['permission'] > 0)
                                <canvas id="attendanceChart" width="400" height="400"></canvas>
                            @else
                                <p class="text-center mt-5 text-danger">No records available.</p>
                            @endif

                        </div>
                        <script>
                            (function() {
                                // Check if attendance data exists
                                const attendanceData = @json($attendanceCounts);

                                // Ensure data exists and at least one value is greater than zero
                                const hasData = attendanceData.present > 0 || attendanceData.absent > 0 || attendanceData.permission > 0;

                                // Find the canvas element
                                const ctxAttendance = document.getElementById('attendanceChart');

                                if (ctxAttendance && hasData) { // Only proceed if canvas and data are valid
                                    const attendanceChart = new Chart(ctxAttendance.getContext('2d'), {
                                        type: 'doughnut',
                                        data: {
                                            labels: ['Present', 'Absent', 'Permission'],
                                            datasets: [{
                                                label: 'Today\'s Attendance',
                                                data: [attendanceData.present, attendanceData.absent, attendanceData.permission],
                                                backgroundColor: [
                                                    'rgba(75, 192, 192, 0.7)', // Green for Present
                                                    'rgba(255, 99, 132, 0.7)', // Red for Absent
                                                    'rgba(255, 206, 86, 0.7)'  // Yellow for Permission
                                                ],
                                                borderColor: [
                                                    'rgba(75, 192, 192, 1)',
                                                    'rgba(255, 99, 132, 1)',
                                                    'rgba(255, 206, 86, 1)'
                                                ],
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: {
                                                    position: 'top',
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            const label = context.label || '';
                                                            const value = context.raw || 0;
                                                            return `${label}: ${value}`;
                                                        }
                                                    }
                                                },
                                                datalabels: {
                                                    color: '#fff',
                                                    formatter: (value, ctxAttendance) => {
                                                        return value;
                                                    },
                                                    font: {
                                                        weight: 'bold',
                                                        size: 12
                                                    },
                                                    anchor: 'center',
                                                    align: 'center'
                                                }
                                            }
                                        },
                                        plugins: [ChartDataLabels] // Register the plugin
                                    });
                                } else if (!hasData) {
                                    // Optional: Display a message if no attendance data
                                    document.getElementById('attendance-container').innerHTML = `
                                        <p class="text-center text-muted">No attendance records found for today.</p>`;
                                }
                            })();
                        </script>
                    </div>
                </div>
            </div>
        </div>
        {{-- academic teacher courses records end here============================= --}}
        {{-- end of second argument ==================================================== --}}
    @elseif (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 4)
        {{-- third argument ============================================================ --}}
        {{-- class teacher panel start here ======================================= --}}

        {{-- check for class teacher contract --}}
        @if ($contract == null)
            <p class="alert alert-danger text-capitalize">contract status: <strong>Not applied.</strong> | please apply <a href="{{route('contract.index')}}">here</a></p>
        @else
            @if($contract->status == 'expired')
                <p class="text-capitalize alert alert-danger">contract status: <strong>{{$contract->status}}</strong></p>
            @elseif ($contract->status == 'rejected')
                <p class="text-capitalize alert alert-secondary">contract status: <strong>{{$contract->status}}</strong> | view <a href="{{route('contract.index')}}">here</a></p>
            @elseif ($contract->status == 'approved' && $contract->end_date <= now()->addDays(30))
                <p class="text-capitalize alert alert-warning">contract status: <strong>expiring soon</strong></p>
            @elseif ($contract->status == 'pending')
                <p class="text-capitalize alert alert-info">contract status: <strong> {{$contract->status}}</strong> | view <a href="{{route('contract.index')}}"> here</a></p>
            @else
                <p class="text-capitalize alert alert-success">contract status: <strong>active</strong></p>
            @endif
    @endif
        <div class="row">
                <div class="col-md-4 mt-md-3 mb-3">
                    <a href="{{ route('get.student.list', ['class' => Hashids::encode($myClass->first()->id)]) }}">
                        <div class="card" style="background:#098ddf">
                            <div class="">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="ti-blackboard"></i> My Class</div>
                                    <h2 class="text-white">{{ $myClass->count() }}</h2>
                                </div>
                                <canvas id="" height="50"></canvas>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- second card --}}
                <div class="col-md-4 mt-md-3 mb-3">
                    <div class="card" style="background:#c84fe0">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-user-graduate"></i> Student</div>
                                @foreach ($classData as $data )
                                <h2 class="text-white">{{$data['maleCount'] + $data['femaleCount']}}</h2>
                                @endforeach
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3 mb-3">
                    <div class="card" style="background: #bf950a">
                        <div class="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="ti-book"></i> My Course</div>
                                <h2 class="text-white">
                                    @if ($courses->where('status', 1)->count() > 2)
                                        3+
                                    @else
                                        {{$courses->where('status', 1)->count()}}
                                    @endif
                                </h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </div>
        </div>
        <div class="row">
                <div class="col-lg-6 mt-0">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title text-center text-uppercase"> My Attendance Class</h4>
                            <div class="table-responsive-md">
                                <table class="table table-hover text-center">
                                    <thead>
                                        <tr class="text-capitalize">
                                            <th>Class</th>
                                            <th>Stream</th>
                                            <th class="">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($myClass as $class)
                                            <tr class="">
                                                <td class="text-uppercase">
                                                    <a href="{{ route('get.student.list', ['class' => Hashids::encode($class->id)]) }}">{{ $class->class_name }}</a>
                                                </td>
                                                <td class="text-uppercase text-center">{{ $class->group }}</td>
                                                <td>
                                                    <ul class="d-flex justify-content-center">
                                                        <li class="">
                                                            <a href="{{ route('attendance.get.form', ['class' => Hashids::encode($class->id)]) }}" class="btn btn-info btn-xs p-1">
                                                                <i class="ti-settings"> REPORT</i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            {{-- another table lies here --}}
            <div class="col-lg-6 mt-0">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-10"><h4 class="header-title text-center text-uppercase"> My Courses</h4></div>
                        </div>
                        <div class="table-responsive-md">
                            <table class="table table-hover text-center" id="">
                                <thead>
                                    <tr class="text-capitalize">
                                        <th>Course name</th>
                                        <th>Class</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($courses->isEmpty())
                                        <tr>
                                            <td colspan="5">
                                                <div class="alert alert-warning text-center">
                                                    No any subject assigned for you
                                                </div>
                                            </td>
                                        </tr>
                                        @else
                                        @foreach ($courses as $course )
                                        <tr>
                                            <td class="text-uppercase">
                                                {{$course->course_name}}
                                            </td>
                                            <td class="text-uppercase">{{$course->class_code}}</td>
                                            <td>
                                                @if ($course->status == 1)
                                                <ul class="d-flex justify-content-center">
                                                    <div class="btn-group" role="group">
                                                        <button id="btnGroupDrop" type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Manage
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                            <a class="dropdown-item" href="{{route('under.construction.page')}}"><i class="ti-pencil-alt"></i> Score</a>
                                                            {{-- <a class="dropdown-item" href="{{route('score.prepare.form', ['id' => Hashids::encode($course->id)])}}"><i class="ti-pencil-alt"></i> Score</a> --}}
                                                            <a class="dropdown-item" href="{{ route('results_byCourse', ['id' => Hashids::encode($course->id)]) }}"><i class="ti-file"></i> Results</a>
                                                        </div>
                                                    </div>
                                                </ul>
                                                @elseif ($course->status == 0)
                                                    <span class="badge bg-danger text-white">{{_('Blocked')}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-1">
                <div class="card">
                    <div class="card-title mt-1">
                        <h6 class="text-center">Class Gender Distribution</h6>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
                    <div style="width: 280px; height: 280px; margin: 0 auto;">
                        <canvas id="genderDistributionChart"></canvas>
                    </div>
                    <script>
                        // Use Laravel Blade syntax to pass data dynamically to JavaScript
                        const maleCount = @json($data['maleCount']);
                        const femaleCount = @json($data['femaleCount']);

                        const ctxGender = document.getElementById('genderDistributionChart').getContext('2d');
                        const genderDistributionChart = new Chart(ctxGender, {
                            type: 'doughnut',
                            data: {
                                labels: ['Male Students', 'Female Students'],
                                datasets: [{
                                    label: 'Gender Distribution',
                                    data: [maleCount, femaleCount], // Use the passed data
                                    backgroundColor: [
                                        'rgba(54, 162, 235, 0.7)', // Blue
                                        'rgba(255, 99, 132, 0.7)'  // Red
                                    ],
                                    borderColor: [
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 99, 132, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.label || '';
                                                const value = context.raw || 0;
                                                return `${label}: ${value}`;
                                            }
                                        }
                                    },
                                    datalabels: {
                                        color: '#fff',
                                        formatter: (value, ctxGender) => {
                                            return value; // Display raw value
                                        },
                                        font: {
                                            weight: 'bold',
                                            size: 12
                                        },
                                        anchor: 'center', // Position inside the curve
                                        align: 'center'
                                    }
                                }
                            },
                            plugins: [ChartDataLabels] // Register the plugin
                        });
                    </script>
                </div>
            </div>
        </div>
        <hr>
        {{-- class teacher panel end here ========================================= --}}
        {{-- end of third argument ====================================================== --}}
    @else
        {{-- fourth argument start here ================================================ --}}
        {{-- normal teacher panel start here ========================================== --}}

        {{-- check for normal teachers contract --}}
        @if ($contract == null)
            <p class="alert alert-danger text-capitalize">contract status: <strong>Not applied.</strong> | please apply <a href="{{route('contract.index')}}">here</a></p>
        @else
            @if($contract->status == 'expired')
                <p class="text-capitalize alert alert-danger">contract status: <strong>{{$contract->status}}</strong></p>
            @elseif ($contract->status == 'rejected')
                <p class="text-capitalize alert alert-secondary">contract status: <strong>{{$contract->status}}</strong> | view <a href="{{route('contract.index')}}">here</a></p>
            @elseif ($contract->status == 'approved' && $contract->end_date <= now()->addDays(30))
                <p class="text-capitalize alert alert-warning">contract status: <strong>expiring soon</strong></p>
            @elseif ($contract->status == 'pending')
                <p class="text-capitalize alert alert-info">contract status: <strong> {{$contract->status}}</strong> | view <a href="{{route('contract.index')}}"> here</a></p>
            @else
                <p class="text-capitalize alert alert-success">contract status: <strong>active</strong></p>
            @endif
    @endif
        <div class="row">
            <div class="col-md-4 mt-5 mb-3">
                <div class="card" style="background: #bf950a">
                    <div class="">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <div class="seofct-icon"><i class="ti-book"></i> My Course</div>
                            <h2 class="text-white">
                                @if ($courses->where('status', 1)->count() > 2)
                                    3+
                                @else
                                    {{$courses->where('status', 1)->count()}}
                                @endif
                            </h2>
                        </div>
                        <canvas id="" height="50"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mt-0">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-10"><h4 class="header-title text-center text-uppercase"> My Courses</h4></div>
                        </div>
                        <div class="table-responsive-md">
                            <table class="table table-hover text-center" id="">
                                <thead>
                                    <tr class="text-capitalize">
                                        <th>Course name</th>
                                        <th>Class</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($courses->isEmpty())
                                        <tr>
                                            <td colspan="5">
                                                <div class="alert alert-warning text-center">
                                                    No any subject assigned for you
                                                </div>
                                            </td>
                                        </tr>
                                        @else
                                        @foreach ($courses as $course )
                                        <tr>
                                            <td class="text-uppercase">
                                                {{$course->course_name}}
                                            </td>
                                            <td class="text-uppercase">{{$course->class_code}}</td>
                                            <td>
                                                @if ($course->status == 1)
                                                <ul class="d-flex justify-content-center">
                                                    <div class="btn-group" role="group">
                                                        <button id="btnGroupDrop" type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Manage
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                            <a class="dropdown-item" href="{{route('score.prepare.form', ['id' => Hashids::encode($course->id)])}}"><i class="ti-pencil-alt"></i> Score</a>
                                                            <a class="dropdown-item" href="{{ route('results_byCourse', ['id' => Hashids::encode($course->id)]) }}"><i class="ti-file"></i> Results</a>
                                                        </div>
                                                    </div>
                                                </ul>
                                                @else
                                                    <span class="badge bg-danger text-white">{{_('Blocked')}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    {{-- normal teacher panel end here ============================================ --}}
    @endif
    {{-- end of argument end here ================================================== --}}
</div>
@endsection

