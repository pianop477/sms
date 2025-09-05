@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <!-- Quick Stats Summary -->
    <!-- Stats Cards Row 1 -->
    <div class="col-lg-12">
        <div class="row">
            <!-- School Card -->
            <div class="col-md-4 mt-3 mb-3">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #93dad6 0%, #5ec4bf 100%);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75">Schools</h6>
                                <h2 class="text-white mb-0">{{count($schools)}}</h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="fas fa-university fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="" class="text-white small d-flex align-items-center">
                                View All <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teacher Card -->
            <div class="col-md-4 mt-3 mb-3">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #e176a6 0%, #d04a88 100%);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75">Teachers</h6>
                                <h2 class="text-white mb-0">
                                    @if (count($teachers) > 99) 100+ @else {{count($teachers)}} @endif
                                </h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="fas fa-user-tie fa-2x text-pink"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="#" class="text-white small d-flex align-items-center">
                                View All <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Card -->
            <div class="col-md-4 mt-3 mb-3">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #098ddf 0%, #0568a8 100%);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75">Students</h6>
                                <h2 class="text-white mb-0">
                                    @if (count($students) > 1999) 2000+ @else {{count($students)}} @endif
                                </h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="fas fa-user-graduate fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="#" class="text-white small d-flex align-items-center">
                                View All <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row 2 -->
    <div class="col-lg-12">
        <div class="row">
            <!-- Parent Card -->
            <div class="col-md-4 mt-3 mb-3">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #c84fe0 0%, #9c27b0 100%);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75">Parents</h6>
                                <h2 class="text-white mb-0">
                                    @if (count($parents) > 1999) 2000+ @else {{count($parents)}} @endif
                                </h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="fas fa-user-friends fa-2x text-purple"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="#" class="text-white small d-flex align-items-center">
                                View All <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses Card -->
            <div class="col-md-4 mt-3 mb-3">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #9fbc71 0%, #689f38 100%);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75">Courses</h6>
                                <h2 class="text-white mb-0">
                                    @if (count($subjects) > 99) 100+ @else {{count($subjects)}} @endif
                                </h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="ti-book fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="#" class="text-white small d-flex align-items-center">
                                View All <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classes Card -->
            <div class="col-md-4 mt-3 mb-3">
                <div class="card border-0 shadow-sm rounded-lg card-hover" style="background: linear-gradient(135deg, #bf950a 0%, #ff9800 100%);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase small opacity-75">Classes</h6>
                                <h2 class="text-white mb-0">
                                    @if (count($classes) > 49) 50+ @else {{count($classes)}} @endif
                                </h2>
                            </div>
                            <div class="bg-white rounded-circle p-3">
                                <i class="ti-blackboard fa-2x text-dark"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="#" class="text-white small d-flex align-items-center">
                                View All <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Schools Growth</h6>
                </div>
                <div class="card-body">
                    <canvas id="schoolsGrowthChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">User Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="userDistributionChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Schools Table -->
    <div class="col-12 mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="header-title mb-0">Registered Institutions</h4>
                    <a href="{{route('Schools.index')}}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0" id="schoolsTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">Institute Name</th>
                                <th width="15%">Admission#</th>
                                <th width="25%">Address</th>
                                <th width="15%">Status</th>
                                <th width="15%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schools as $school)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>
                                    <a href="{{route('schools.show', ['school' => Hashids::encode($school->id)])}}"
                                       class="text-primary fw-bold">
                                        {{ucwords(strtolower($school->school_name))}}
                                    </a>
                                </td>
                                <td>{{strtoupper($school->school_reg_no)}}</td>
                                <td>{{ucwords(strtolower($school->postal_address))}} - {{ucwords(strtolower($school->postal_name))}}</td>
                                <td>
                                    @if ($school->status == 1)
                                    <span class="badge bg-success-lighten text-success">Active</span>
                                    @elseif($school->status == 2)
                                    <span class="badge bg-warning-lighten text-warning">Unpaid</span>
                                    @else
                                    <span class="badge bg-secondary-lighten text-danger">Closed</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{route('schools.edit', ['school' => Hashids::encode($school->id)])}}"
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="Edit">
                                            <i class="ti-pencil"></i>
                                        </a>

                                        @if ($school->status == 1)
                                        <form action="{{route('deactivate.status', ['school' => Hashids::encode($school->id)])}}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-warning"
                                                    data-bs-toggle="tooltip"
                                                    title="Deactivate"
                                                    onclick="return confirm('Are you sure you want to block this school?')">
                                                <i class="ti-na text-dark"></i>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{route('activate.status', ['school' => Hashids::encode($school->id)])}}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-success"
                                                    data-bs-toggle="tooltip"
                                                    title="Activate"
                                                    onclick="return confirm('Are you sure you want to unblock this school?')">
                                                <i class="ti-reload"></i>
                                            </button>
                                        </form>
                                        @endif

                                        <form action="{{route('schools.destroy', ['school' => Hashids::encode($school->id)])}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="tooltip"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this school?')">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
            justify-content: center; /* Changed from space-between to center */
            align-items: center; /* Uncommented and added to center vertically */
            text-align: center;
            padding-left: 15px; /* Changed from 50% to 15px */
            position: relative;
            border-bottom: 1px solid #f1f1f1;
            width: 100%; /* Added to ensure full width */
        }
        .table-responsive td::before {
            display: none; /* Removed the data-label pseudo-element */
        }
        .btn-group {
            display: flex;
            gap: 5px;
            justify-content: center; /* Center the buttons */
            width: 100%;
        }

        /* Additional rule for the action button cell */
        .table-responsive td.text-center {
            justify-content: center;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Schools Growth Chart
        const ctx1 = document.getElementById('schoolsGrowthChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'New Schools',
                    data: [12, 19, 3, 5, 2, 3, 7, 8, 10, 15, 20, 25, 30],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // User Distribution Chart
        const ctx2 = document.getElementById('userDistributionChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Students', 'Teachers', 'Parents'],
                datasets: [{
                    data: [{{count($students)}}, {{count($teachers)}}, {{count($parents)}}],
                    backgroundColor: [
                        '#098ddf',
                        '#e176a6',
                        '#c84fe0'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    });

    // Authorization check
    @if (Auth::user()->usertype != 1)
        window.location.href = '/error-page';
    @endif
</script>
@endsection
