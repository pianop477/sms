@extends('SRTDashboard.frame')

@section('content')
<style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #6f42c1;
        --success-color: #1cc88a;
        --info-color: #36b9cc;
        --warning-color: #f6c23e;
        --danger-color: #e74a3b;
        --light-color: #f8f9fc;
        --dark-color: #5a5c69;
    }

    body {
        background-color: #f8f9fc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0.5rem 1.5rem 0 rgba(58, 59, 69, 0.1);
        margin-bottom: 20px;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 2rem 0 rgba(58, 59, 69, 0.15);
    }

    /* Statistics Cards Styling */
    .stat-card {
        border-radius: 15px;
        border: none;
        position: relative;
        overflow: hidden;
        transition: all 0.4s ease;
        min-height: 140px;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.175);
    }

    .stat-card .card-body {
        position: relative;
        z-index: 2;
        padding: 1.5rem;
    }

    .stat-card .card-icon {
        position: absolute;
        right: 20px;
        top: 20px;
        opacity: 0.2;
        font-size: 4rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover .card-icon {
        opacity: 0.3;
        transform: scale(1.1);
    }

    .stat-card .card-title {
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        opacity: 0.9;
    }

    .stat-card .card-value {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0;
    }

    /* Gradient Backgrounds for Cards */
    .bg-teacher {
        background: linear-gradient(135deg, #e176a6 0%, #d04a88 100%);
    }

    .bg-parent {
        background: linear-gradient(135deg, #c84fe0 0%, #9c27b0 100%);
    }

    .bg-student {
        background: linear-gradient(135deg, #098ddf 0%, #0568a8 100%);
    }

    .bg-course {
        background: linear-gradient(135deg, #9fbc71 0%, #689f38 100%);
    }

    .bg-class {
        background: linear-gradient(135deg, #bf950a 0%, #ff9800 100%);
    }

    .bg-bus {
        background: linear-gradient(135deg, #329688 0%, #00796b 100%);
    }

    /* Chart Container Styles */
    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 0.5rem 1.5rem rgba(58, 59, 69, 0.1);
        border: none;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .chart-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 2rem rgba(58, 59, 69, 0.15);
    }

    .chart-wrapper {
        flex: 1;
        min-height: 300px;
        position: relative;
        width: 100%;
    }

    .chart-wrapper canvas,
    .chart-wrapper .chart-canvas {
        width: 100% !important;
        height: 100% !important;
    }

    .chart-header {
        border-bottom: 2px solid #f8f9fc;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .chart-title {
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 5px;
        font-size: 1.1rem;
    }

    .chart-subtitle {
        color: #6c757d;
        font-size: 0.875rem;
    }

    /* Table Styles */
    .dashboard-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }

    .dashboard-table thead {
        background: linear-gradient(135deg, var(--primary-color) 0%, #2e59d9 100%);
        color: white;
    }

    .dashboard-table th {
        padding: 15px 12px;
        font-weight: 700;
        vertical-align: middle;
        border: none;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dashboard-table td {
        padding: 12px 10px;
        vertical-align: middle;
        border-bottom: 1px solid #e3e6f0;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .stat-card .card-value {
            font-size: 1.5rem;
        }

        .stat-card .card-icon {
            font-size: 2.5rem;
        }

        .chart-wrapper {
            min-height: 250px;
        }
    }
</style>

<div class="py-4">
    <div class="row">
        <!-- Quick Stats Summary -->
        <div class="col-lg-12 mb-4">
            <div class="row">
                <!-- Teachers Card -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <a href="{{route('Teachers.index')}}" class="text-decoration-none">
                        <div class="stat-card bg-teacher text-white">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="card-title">Teachers</div>
                                        <div class="card-value">
                                            @if (count($teachers) > 99) 100+ @else {{count($teachers)}} @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-tie card-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Parents Card -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <a href="{{route('Parents.index')}}" class="text-decoration-none">
                        <div class="stat-card bg-parent text-white">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="card-title">Parents</div>
                                        <div class="card-value">
                                            @if (count($parents) > 1999) 2000+ @else {{count($parents)}} @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-friends card-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Students Card -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <a href="{{route('classes.list')}}" class="text-decoration-none">
                        <div class="stat-card bg-student text-white">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="card-title">Students</div>
                                        <div class="card-value">
                                            @if (count($students) > 1999) 2000+ @else {{count($students)}} @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-graduate card-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Courses Card -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <a href="{{route('courses.index')}}" class="text-decoration-none">
                        <div class="stat-card bg-course text-white">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="card-title">Open Courses</div>
                                        <div class="card-value">
                                            @if (count($subjects) > 49) 50+ @else {{count($subjects)}} @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="ti-book card-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Classes Card -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <a href="{{route('Classes.index')}}" class="text-decoration-none">
                        <div class="stat-card bg-class text-white">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="card-title">Classes</div>
                                        <div class="card-value">
                                            @if (count($classes) > 49) 50+ @else {{count($classes)}} @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="ti-blackboard card-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- School Buses Card -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <a href="{{route('Transportation.index')}}" class="text-decoration-none">
                        <div class="stat-card bg-bus text-white">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="card-title">School Buses</div>
                                        <div class="card-value">
                                            @if (count($buses) > 49) 50+ @else {{count($buses)}} @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-bus card-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Analytics Charts Section -->
        <div class="col-lg-12 mb-4">
            <div class="row">
                <!-- Student Registration Chart -->
                <div class="col-xl-8 mb-4">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="fas fa-chart-bar me-2"></i> Student Registration by Class & Gender
                            </h5>
                            <p class="chart-subtitle">Distribution of students across classes</p>
                        </div>
                        <div class="chart-wrapper">
                            <div id="studentChart" class="chart-canvas"></div>
                        </div>
                    </div>
                </div>

                <!-- Teacher Qualifications Chart -->
                <div class="col-xl-4 mb-4">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="fas fa-chart-pie me-2"></i> Teacher Qualifications
                            </h5>
                            <p class="chart-subtitle">Educational background overview</p>
                        </div>
                        <div class="chart-wrapper">
                            <div id="qualificationChart" class="chart-canvas"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Analytics Section -->
        <div class="col-lg-12 mb-4">
            <div class="row">
                <!-- Gender Distribution -->
                <div class="col-xl-4 mb-4">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="fas fa-venus-mars me-2"></i> Student Gender Distribution
                            </h5>
                            <p class="chart-subtitle">Male vs Female students ratio</p>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Today's Attendance -->
                <div class="col-xl-4 mb-4">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="fas fa-calendar-check me-2"></i> Today's Attendance
                            </h5>
                            <p class="chart-subtitle">{{\Carbon\Carbon::parse($today)->format('d-m-Y')}}</p>
                        </div>
                        <div class="chart-wrapper">
                            @if ($attendanceCounts['present'] > 0 || $attendanceCounts['absent'] > 0 || $attendanceCounts['permission'] > 0)
                                <canvas id="attendanceChart"></canvas>
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <p class="text-muted text-center">No attendance records for today</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Tables -->
                <div class="col-xl-4 mb-4">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="fas fa-table me-2"></i> Quick Overview
                            </h5>
                            <p class="chart-subtitle">Registration statistics</p>
                        </div>
                        <div class="row">
                            <!-- Students by Class -->
                            <div class="col-12 mb-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="card-title text-center mb-3 text-primary">Students by Class</h6>
                                        @if ($studentsByClass->isEmpty())
                                            <p class="text-center text-muted mb-0">No records available</p>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-sm dashboard-table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Class</th>
                                                            <th class="text-end">Count</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($studentsByClass as $class)
                                                        <tr>
                                                            <td class="fw-semibold">{{$class->class_code}}</td>
                                                            <td class="text-end">{{$class->student_count}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Teachers by Gender -->
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="card-title text-center mb-3 text-primary">Teachers by Gender</h6>
                                        @if ($teacherByGender->isEmpty())
                                            <p class="text-center text-muted mb-0">No records available</p>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-sm dashboard-table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Gender</th>
                                                            <th class="text-end">Count</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($teacherByGender as $teacher)
                                                        <tr>
                                                            <td class="fw-semibold text-capitalize">{{$teacher->gender}}</td>
                                                            <td class="text-end">{{$teacher->teacher_count}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Student Registration Chart (ECharts)
        var chartDom = document.getElementById('studentChart');
        if (chartDom) {
            var myChart = echarts.init(chartDom);
            var chartData = @json($chartData);

            var groupedData = {};
            chartData.forEach(item => {
                var classCode = item.category.split(' (')[0];
                var gender = item.category.includes('Male') ? 'Male' : 'Female';
                if (!groupedData[classCode]) {
                    groupedData[classCode] = { Male: 0, Female: 0 };
                }
                groupedData[classCode][gender] = item.value;
            });

            var classCodes = Object.keys(groupedData);
            var maleData = classCodes.map(classCode => groupedData[classCode].Male);
            var femaleData = classCodes.map(classCode => groupedData[classCode].Female);

            var option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'shadow' }
                },
                legend: {
                    data: ['Male', 'Female'],
                    bottom: 10
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: classCodes,
                    axisLabel: { rotate: 45 }
                },
                yAxis: { type: 'value' },
                series: [
                    {
                        name: 'Male',
                        type: 'bar',
                        stack: 'total',
                        emphasis: { focus: 'series' },
                        data: maleData
                    },
                    {
                        name: 'Female',
                        type: 'bar',
                        stack: 'total',
                        emphasis: { focus: 'series' },
                        data: femaleData
                    }
                ]
            };
            myChart.setOption(option);
        }

        // Teacher Qualifications Chart (amCharts)
        if (document.getElementById('qualificationChart')) {
            am5.ready(function() {
                var root = am5.Root.new("qualificationChart");
                root.setThemes([am5themes_Animated.new(root)]);

                var chart = root.container.children.push(
                    am5percent.PieChart.new(root, { layout: root.verticalLayout })
                );

                var series = chart.series.push(
                    am5percent.PieSeries.new(root, {
                        valueField: "value",
                        categoryField: "category"
                    })
                );

                series.data.setAll([
                    { category: "Masters", value: {{ $qualificationData['masters'] }} },
                    { category: "Degree", value: {{ $qualificationData['bachelor'] }} },
                    { category: "Diploma", value: {{ $qualificationData['diploma'] }} },
                    { category: "Certificate", value: {{ $qualificationData['certificate'] }} }
                ]);

                chart.children.push(am5.Legend.new(root, {}));
                series.appear(1000, 100);
            });
        }

        // Gender Distribution Chart
        const genderCtx = document.getElementById('genderChart');
        if (genderCtx) {
            const totalMaleStudents = @json($totalMaleStudents);
            const totalFemaleStudents = @json($totalFemaleStudents);

            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Male Students', 'Female Students'],
                    datasets: [{
                        data: [totalMaleStudents, totalFemaleStudents],
                        backgroundColor: ['#4e73df', '#e74a3b'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return `${context.label}: ${context.parsed} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        // Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart');
        if (attendanceCtx) {
            const attendanceData = @json($attendanceCounts);
            const hasData = attendanceData.present > 0 || attendanceData.absent > 0 || attendanceData.permission > 0;

            if (hasData) {
                new Chart(attendanceCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Present', 'Absent', 'Permission'],
                        datasets: [{
                            data: [attendanceData.present, attendanceData.absent, attendanceData.permission],
                            backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        }

        // Authorization check
        @if (Auth::user()->usertype != 2)
            window.location.href = '/error-page';
        @endif
    });
</script>
@endsection
