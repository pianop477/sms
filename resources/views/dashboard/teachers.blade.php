@extends('SRTDashboard.frame')
@section('content')
<div class="col-lg-12">
    {{-- first argument======================================================== --}}
    @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 2)
        {{-- school head teacher panel start here --}}

        {{-- display contract status for head teacher --}}
        <div class="row">
            <div class="col-md-12">
                @if ($contract == null)
                <div class="alert alert-danger alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                    <strong>Contract Status:</strong> Not applied.
                    <a href="{{route('contract.index')}}" class="alert-link">Apply here</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @else
                @if($contract->status == 'expired')
                    <div class="alert alert-danger alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Expired
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif ($contract->status == 'rejected')
                    <div class="alert alert-secondary alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Rejected |
                        <a href="{{route('contract.index')}}" class="alert-link">View details</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif ($contract->status == 'approved' && $contract->end_date <= now()->addDays(30))
                    <div class="alert alert-warning alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Expiring soon ({{ $contract->end_date->format('d/m/Y') }})
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif ($contract->status == 'pending')
                    <div class="alert alert-info alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Pending |
                        <a href="{{route('contract.index')}}" class="alert-link">View details</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @else
                    <div class="alert alert-success alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Active (Expires at: {{ $contract->end_date }}) |
                        <a href="{{route('contract.index')}}" class="alert-link">View contract</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endif
            </div>
        </div>
            <div class="row">
            <!-- Stats Cards -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #e176a6 0%, #d04a88 100%);">
                    <a href="{{route('Teachers.index')}}" class="text-decoration-none">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> Teachers</h6>
                                    <h2 class="text-white mb-0">
                                        @if (count($teachers) > 99) 100+ @else {{count($teachers)}} @endif
                                    </h2>
                                </div>
                                <div class="bg-white rounded-circle p-3">
                                    <i class="fas fa-user-tie fa-2x text-pink"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-white small d-flex align-items-center">
                                    View All <i class="fas fa-arrow-right ms-2"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #c84fe0 0%, #9c27b0 100%);">
                    <a href="{{route('Parents.index')}}" class="text-decoration-none">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> Parents</h6>
                                    <h2 class="text-white mb-0">
                                        @if (count($parents) > 1999) 2000+ @else {{count($parents)}} @endif
                                    </h2>
                                </div>
                                <div class="bg-white rounded-circle p-3">
                                    <i class="fas fa-user-friends fa-2x text-purple"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-white small d-flex align-items-center">
                                    View All <i class="fas fa-arrow-right ms-2"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #098ddf 0%, #0568a8 100%);">
                    <a href="{{route('classes.list')}}" class="text-decoration-none">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> Students</h6>
                                    <h2 class="text-white mb-0">
                                        @if (count($students) > 1999) 2000+ @else {{count($students)}} @endif
                                    </h2>
                                </div>
                                <div class="bg-white rounded-circle p-3">
                                    <i class="fas fa-user-graduate fa-2x text-info"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-white small d-flex align-items-center">
                                    View All <i class="fas fa-arrow-right ms-2"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Second Row of Cards -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #9fbc71 0%, #689f38 100%);">
                    <a href="{{route('courses.index')}}" class="text-decoration-none">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> Open Courses</h6>
                                    <h2 class="text-white mb-0">
                                        @if (count($subjects) > 49) 50+ @else {{count($subjects)}} @endif
                                    </h2>
                                </div>
                                <div class="bg-white rounded-circle p-3">
                                    <i class="ti-book fa-2x text-success"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-white small d-flex align-items-center">
                                    View All <i class="fas fa-arrow-right ms-2"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #bf950a 0%, #ff9800 100%);">
                    <a href="{{route('Classes.index')}}" class="text-decoration-none">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> Classes</h6>
                                    <h2 class="text-white mb-0">
                                        @if (count($classes) > 49) 50+ @else {{count($classes)}} @endif
                                    </h2>
                                </div>
                                <div class="bg-white rounded-circle p-3">
                                    <i class="ti-blackboard fa-2x text-dark"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-white small d-flex align-items-center">
                                    View All <i class="fas fa-arrow-right ms-2"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #329688 0%, #00796b 100%);">
                    <a href="{{route('Transportation.index')}}" class="text-decoration-none">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> School Buses</h6>
                                    <h2 class="text-white mb-0">
                                        @if (count($buses) > 49) 50+ @else {{count($buses)}} @endif
                                    </h2>
                                </div>
                                <div class="bg-white rounded-circle p-3">
                                    <i class="fas fa-bus fa-2x text-teal"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-white small d-flex align-items-center">
                                    View All <i class="fas fa-arrow-right ms-2"></i>
                                </span>
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
                                        text: 'Student Registration',
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
                                        text: "Qualifications",
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
                        <div class="card-title mt-2 border-bottom">
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
                        <div class="card-title mt-2 border-bottom">
                            <h6 class="text-center">Students Registration</h6>
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
                                    labels: ['Boys', 'Girls'],
                                    datasets: [{
                                        label: 'Students Enrollment',
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
                        <div class="card-title mt-2 border-bottom">
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
            <div class="col-12">
                @if ($contract == null)
                <div class="alert alert-danger alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                    <strong>Contract Status:</strong> Not applied.
                    <a href="{{route('contract.index')}}" class="alert-link">Apply here</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @else
                @if($contract->status == 'expired')
                    <div class="alert alert-danger alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Expired
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif ($contract->status == 'rejected')
                    <div class="alert alert-secondary alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Rejected |
                        <a href="{{route('contract.index')}}" class="alert-link">View details</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif ($contract->status == 'approved' && $contract->end_date <= now()->addDays(30))
                    <div class="alert alert-warning alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Expiring soon ({{ $contract->end_date->format('d/m/Y') }})
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif ($contract->status == 'pending')
                    <div class="alert alert-info alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Pending |
                        <a href="{{route('contract.index')}}" class="alert-link">View details</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @else
                    <div class="alert alert-success alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Active (Expires at: {{$contract->end_date }}) |
                        <a href="{{route('contract.index')}}" class="alert-link">View contract</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endif
            </div>
            {{-- show tod roster details --}}
                @php
                    // Get today's date
                    $today = \Carbon\Carbon::now()->format('Y-m-d');
                    $user = auth()->user();
                    $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
                    $myDuty = \App\Models\TodRoster::where('teacher_id', $teacher->id)->where('status', 'active')->first();
                @endphp
                @if ($myDuty)
                    <div class="alert alert-info alert-dismissible fade show" style="border-right: 3px solid #982d9c;">
                        <p class="text-right">You are on Duty this week! Please fill out
                            <strong>
                                <a href="{{route('tod.report.create')}}" onclick="return confirm('Are you sure?')"><i class="fas fa-edit"></i> Daily Report here</a>
                            </strong>
                        </p>
                    </div>
                @endif
        </div>
            <div class="row">
        <!-- Stats Cards for Academic Teacher -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #e176a6 0%, #d04a88 100%);">
                <a href="{{route('Teachers.index')}}" class="text-decoration-none">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> Teachers</h6>
                                <h2 class="text-white mb-0">
                                    @if (count($teachers) > 99) 100+ @else {{count($teachers)}} @endif
                                </h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="fas fa-user-tie fa-2x text-pink"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-white small d-flex align-items-center">
                                View All <i class="fas fa-arrow-right ms-2"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #098ddf 0%, #0568a8 100%);">
                <a href="{{route('classes.list')}}" class="text-decoration-none">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> Students</h6>
                                <h2 class="text-white mb-0">
                                    @if(count($students) > 1999) 2000+ @else {{count($students)}} @endif
                                </h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="fas fa-user-graduate fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-white small d-flex align-items-center">
                                View All <i class="fas fa-arrow-right ms-2"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #9fbc71 0%, #689f38 100%);">
                <a href="{{route('courses.index')}}" class="text-decoration-none">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> Open Courses</h6>
                                <h2 class="text-white mb-0">
                                    @if (count($subjects) > 49) 50+ @else {{count($subjects)}} @endif
                                </h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="ti-book fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-white small d-flex align-items-center">
                                View All <i class="fas fa-arrow-right ms-2"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Second Row for Academic Teacher -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #bf950a 0%, #ff9800 100%);">
                <a href="{{route('Classes.index')}}" class="text-decoration-none">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> Classes</h6>
                                <h2 class="text-white mb-0">
                                    @if (count($classes) > 49) 50+ @else {{count($classes)}} @endif
                                </h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="ti-blackboard fa-2x text-dark"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-white small d-flex align-items-center">
                                View All <i class="fas fa-arrow-right ms-2"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #b14fbe 0%, #8e24aa 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> My Courses</h6>
                            <h2 class="text-white mb-0">
                                {{ $courses->where('status', 1)->count() }}
                            </h2>
                        </div>
                        <div class="bg-white rounded-circle p-3">
                            <i class="ti-book fa-2x text-purple"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-white small d-flex align-items-center">
                            View All <i class="fas fa-arrow-right ms-2"></i>
                        </span>
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
                                    text: 'Student Registration',
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
                            <div class="col-10"><h4 class="header-title text-capitalize text-center">my teaching subjects</h4></div>
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
                                            <td class="text-capitalize">
                                                {{ucwords(strtolower($course->course_name))}}
                                            </td>
                                            <td class="text-uppercase">{{strtoupper($course->class_code)}}</td>
                                            <td>
                                                @if ($course->status == 1)
                                                <ul class="d-flex justify-content-center">
                                                    <div class="btn-group" role="group">
                                                        <button id="btnGroupDrop" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Manage
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                            {{-- <a class="dropdown-item" href="{{route('under.construction.page')}}"><i class="ti-pencil-alt"></i> Score</a> --}}
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
        <hr class="dark horizontal py-0">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-4 mt-2 mb-3">
                    <div class="card">
                        <div class="card-title mt-2 border-bottom">
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
                        <div class="card-title border-bottom mt-2">
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
                        <div class="card-title mt-2 border-bottom">
                            <h6 class="text-center">Students Enrollment</h6>
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
                                    labels: ['Boys', 'Girls'],
                                    datasets: [{
                                        label: 'Student Enrollment',
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
                        <div class="card-title mt-2 border-bottom">
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
                <div class="alert alert-danger alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                    <strong>Contract Status:</strong> Not applied.
                    <a href="{{route('contract.index')}}" class="alert-link">Apply here</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @else
                @if($contract->status == 'expired')
                    <div class="alert alert-danger alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Expired
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif ($contract->status == 'rejected')
                    <div class="alert alert-secondary alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Rejected |
                        <a href="{{route('contract.index')}}" class="alert-link">View details</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif ($contract->status == 'approved' && $contract->end_date <= now()->addDays(30))
                    <div class="alert alert-warning alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Expiring soon ({{ $contract->end_date->format('d/m/Y') }})
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif ($contract->status == 'pending')
                    <div class="alert alert-info alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Pending |
                        <a href="{{route('contract.index')}}" class="alert-link">View details</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @else
                    <div class="alert alert-success alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                        <strong>Contract Status:</strong> Active (Expires at: {{$contract->end_date}}) |
                        <a href="{{route('contract.index')}}" class="alert-link">View contract</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endif
                @php
                    // Get today's date
                    $today = \Carbon\Carbon::now()->format('Y-m-d');
                    $user = auth()->user();
                    $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
                    $myDuty = \App\Models\TodRoster::where('teacher_id', $teacher->id)->where('status', 'active')->first();
                @endphp
                @if ($myDuty)
                    <div class="alert alert-info alert-dismissible fade show" style="border-right: 3px solid #982d9c;">
                        <p class="text-right">You are on Duty this week! Please fill out
                            <strong>
                                <a href="{{route('tod.report.create')}}" onclick="return confirm('Are you sure?')"><i class="fas fa-edit"></i> Daily Report here</a>
                            </strong>
                        </p>
                    </div>
                @endif
        <div class="row">
        <!-- Stats Cards for Class Teacher -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #098ddf 0%, #0568a8 100%);">
                <a href="{{ route('get.student.list', ['class' => Hashids::encode($myClass->first()->id)]) }}" class="text-decoration-none">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> My Attendance</h6>
                                <h2 class="text-white mb-0">{{ $myClass->count() }}</h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="fas fa-user-check fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-white small d-flex align-items-center">
                                View All <i class="fas fa-arrow-right ms-2"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #c84fe0 0%, #9c27b0 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> Students</h6>
                            @foreach ($classData as $data )
                            <h2 class="text-white mb-0">{{$data['maleCount'] + $data['femaleCount']}}</h2>
                            @endforeach
                        </div>
                        <div class="bg-white rounded-circle p-3">
                            <i class="fas fa-users fa-2x text-purple"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-white small d-flex align-items-center">
                            View All <i class="fas fa-arrow-right ms-2"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #bf950a 0%, #ff9800 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> My Courses</h6>
                            <h2 class="text-white mb-0">
                                {{$courses->where('status', 1)->count()}}
                            </h2>
                        </div>
                        <div class="bg-white rounded-circle p-3">
                            <i class="ti-book fa-2x text-dark"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-white small d-flex align-items-center">
                            View All <i class="fas fa-arrow-right ms-2"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="row">
                <div class="col-lg-6 mt-0">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title text-center text-capitalize"> My Attendance Class</h4>
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
                                                            <a href="{{ route('attendance.get.form', ['class' => Hashids::encode($class->id)]) }}" class="btn btn-info p-1">
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
                            <div class="col-10"><h4 class="header-title text-center text-capitalize">my teaching subjects</h4></div>
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
                                            <td class="text-capitalize">
                                                {{ucwords(strtolower($course->course_name))}}
                                            </td>
                                            <td class="text-uppercase">{{strtoupper($course->class_code)}}</td>
                                            <td>
                                                @if ($course->status == 1)
                                                <ul class="d-flex justify-content-center">
                                                    <div class="btn-group" role="group">
                                                        <button id="btnGroupDrop" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Manage
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                            {{-- <a class="dropdown-item" href="{{route('under.construction.page')}}"><i class="ti-pencil-alt"></i> Score</a> --}}
                                                            <a class="dropdown-item" href="{{route('score.prepare.form', ['id' => Hashids::encode($course->id)])}}"><i class="ti-pencil-alt"></i> Score</a>
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
                    <div class="card-title mt-1 border-bottom">
                        <h6 class="text-center">Students Enrollment</h6>
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
                                labels: ['Boys', 'Girls'],
                                datasets: [{
                                    label: 'Students Enrollment',
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
            <div class="col-lg-6 mt-1">
                <div class="card">
                    <div class="card-title mt-1 border-bottom">
                        <h6 class="text-center">Today's Attendance: {{\Carbon\Carbon::today()->format('d-m-Y')}}</h6>
                        {{-- <p class="text-center font-style-italic">Today is: {{\Carbon\Carbon::today()->format('d-m-Y')}}</p> --}}
                    </div>
                       @if (!empty($attendanceCount) && is_array($attendanceCount))
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                            <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
                            <div style="width: 300px; height: 260px; margin: 0 auto;">
                                <canvas id="attendanceChart"></canvas>
                            </div>
                            <script>
                                const attendanceData = @json($attendanceCount);

                                if (attendanceData && attendanceData.male && attendanceData.female) {
                                    const ctx = document.getElementById('attendanceChart').getContext('2d');

                                    const malePresent = attendanceData.male.present || 0;
                                    const femalePresent = attendanceData.female.present || 0;
                                    const maleAbsent = attendanceData.male.absent || 0;
                                    const femaleAbsent = attendanceData.female.absent || 0;
                                    const malePermission = attendanceData.male.permission || 0;
                                    const femalePermission = attendanceData.female.permission || 0;

                                    const totalPresent = malePresent + femalePresent;
                                    const totalAbsent = maleAbsent + femaleAbsent;
                                    const totalPermission = malePermission + femalePermission;

                                    const attendanceChart = new Chart(ctx, {
                                        type: 'doughnut',
                                        data: {
                                            labels: [
                                                `Pres (B: ${malePresent}, G: ${femalePresent})`,
                                                `Abs (B: ${maleAbsent}, G: ${femaleAbsent})`,
                                                `Perm (B: ${malePermission}, G: ${femalePermission})`
                                            ],
                                            datasets: [{
                                                label: 'Total Attendance',
                                                data: [totalPresent, totalAbsent, totalPermission],
                                                backgroundColor: [
                                                    'rgba(75, 192, 192, 0.6)',
                                                    'rgba(255, 159, 64, 0.6)',
                                                    'rgba(255, 99, 132, 0.7)'
                                                ],
                                                borderColor: [
                                                    'rgba(75, 192, 192, 1)',
                                                    'rgba(255, 159, 64, 1)',
                                                    'rgba(255, 99, 132, 0.7)'
                                                ],
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            plugins: {
                                                legend: {
                                                    position: 'top'
                                                },
                                                datalabels: {
                                                    color: '#000',
                                                    font: {
                                                        weight: 'bold',
                                                        size: 12
                                                    },
                                                    formatter: function(value, context) {
                                                        return `${context.chart.data.labels[context.dataIndex]} = ${value}`;
                                                    }
                                                }
                                            }
                                        },
                                        plugins: [ChartDataLabels]
                                    });
                                }
                            </script>
                        @else
                            <p class="text-center mt-5 text-danger">No attendance records available.</p>
                        @endif
                    </div>
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
            <div class="alert alert-danger alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                <strong>Contract Status:</strong> Not applied.
                <a href="{{route('contract.index')}}" class="alert-link">Apply here</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @else
            @if($contract->status == 'expired')
                <div class="alert alert-danger alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                    <strong>Contract Status:</strong> Expired
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif ($contract->status == 'rejected')
                <div class="alert alert-secondary alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                    <strong>Contract Status:</strong> Rejected |
                    <a href="{{route('contract.index')}}" class="alert-link">View details</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif ($contract->status == 'approved' && $contract->end_date <= now()->addDays(30))
                <div class="alert alert-warning alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                    <strong>Contract Status:</strong> Expiring soon ({{ $contract->end_date->format('d/m/Y') }})
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif ($contract->status == 'pending')
                <div class="alert alert-info alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                    <strong>Contract Status:</strong> Pending |
                    <a href="{{route('contract.index')}}" class="alert-link">View details</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @else
                <div class="alert alert-success alert-dismissible fade show" style="border-left: 3px solid #0c68a1;">
                    <strong>Contract Status:</strong> Active (Expires at: {{ $contract->end_date }}) |
                    <a href="{{route('contract.index')}}" class="alert-link">View contract</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endif
                @php
                    // Get today's date
                    $today = \Carbon\Carbon::now()->format('Y-m-d');
                    $user = auth()->user();
                    $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
                    $myDuty = \App\Models\TodRoster::where('teacher_id', $teacher->id)->where('status', 'active')->first();
                @endphp
                @if ($myDuty)
                    <div class="alert alert-info alert-dismissible fade show" style="border-right: 3px solid #982d9c;">
                        <p class="text-right">You are on Duty this week! Please fill out
                            <strong>
                                <a href="{{route('tod.report.create')}}" onclick="return confirm('Are you sure?')"><i class="fas fa-edit"></i> Daily Report here</a>
                            </strong>
                        </p>
                    </div>
                @endif
        <div class="row">
            <!-- Stats Card for Normal Teacher -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #bf950a 0%, #ff9800 100%);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75"><i class=""></i> My Courses</h6>
                                <h2 class="text-white mb-0">
                                    {{$courses->where('status', 1)->count()}}
                                </h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="ti-book fa-2x text-dark"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                        <span class="text-white small d-flex align-items-center">
                            View All <i class="fas fa-arrow-right ms-2"></i>
                        </span>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mt-0">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-10"><h4 class="header-title text-center text-capitalize">my teaching subjects</h4></div>
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
                                                    No any subject assigned for you!
                                                </div>
                                            </td>
                                        </tr>
                                        @else
                                        @foreach ($courses as $course )
                                        <tr>
                                            <td class="text-capitalize">
                                                {{ucwords(strtolower($course->course_name))}}
                                            </td>
                                            <td class="text-uppercase">{{strtoupper($course->class_code)}}</td>
                                            <td>
                                                @if ($course->status == 1)
                                                <ul class="d-flex justify-content-center">
                                                    <div class="btn-group" role="group">
                                                        <button id="btnGroupDrop" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Manage
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                            {{-- <a class="dropdown-item" href="{{route('under.construction.page')}}"><i class="ti-pencil-alt"></i> Score</a> --}}
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
<style>
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .bg-success-lighten {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .bg-warning-lighten {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-secondary-lighten {
        background-color: rgba(108, 117, 125, 0.1);
    }
    .table-centered td, .table-centered th {
        vertical-align: middle;
    }
    @media (max-width: 768px) {
        .table-responsive {
            border: 0;
        }
        .table-responsive thead {
            display: none;
        }
        .table-responsive tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .table-responsive td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: right;
            padding-left: 50%;
            position: relative;
            border-bottom: 1px solid #f1f1f1;
        }
        .table-responsive td::before {
            content: attr(data-label);
            position: absolute;
            left: 15px;
            font-weight: bold;
            text-align: left;
        }
        .btn-group {
            display: flex;
            gap: 5px;
        }
    }
</style>
@endsection

