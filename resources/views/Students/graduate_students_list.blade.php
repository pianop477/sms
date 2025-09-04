@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --success: #28a745;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px; */
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 5px 10px;
            position: relative;
            overflow: hidden;
        }

        .card-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            transform: rotate(30deg);
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            font-size: 24px;
        }

        .card-body {
            padding: 10px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 1;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .batch-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .batch-header:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .batch-header.collapsed {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .batch-title {
            font-weight: 700;
            font-size: 18px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .batch-icon {
            transition: transform 0.3s;
        }

        .batch-header.collapsed .batch-icon {
            transform: rotate(-90deg);
        }

        .batch-content {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .batch-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .student-count {
            background: linear-gradient(135deg, var(--info) 0%, #17a2b8 100%);
            color: white;
            border-radius: 50px;
            padding: 8px 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-pdf {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            border: none;
            border-radius: 50px;
            padding: 8px 16px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-pdf:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-revert {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            border: none;
            border-radius: 50px;
            padding: 8px 16px;
            font-weight: 600;
            color: #856404;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
        }

        .btn-revert:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .table-custom {
            margin-bottom: 0;
            width: 100%;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            text-align: left;
            font-size: 14px;
            position: sticky;
            top: 0;
        }

        .table-custom tbody td {
            padding: 12px;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: rgba(78, 84, 200, 0.05);
        }

        .table-custom tbody tr:hover {
            background-color: rgba(78, 84, 200, 0.1);
        }

        .badge-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            border-radius: 50px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 12px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 16px;
            margin: 0;
            font-weight: 500;
        }

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 20px;
            }

            .batch-actions {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }

            .table-responsive {
                font-size: 14px;
            }
        }
    </style>

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title text-white">
                            <i class="fas fa-graduation-cap me-2"></i> Graduate Students Batches
                        </h4>
                        <p class="mb-0 text-white-50"> View and manage graduation batches</p>
                    </div>
                </div>
                <i class="fas fa-users-graduate floating-icons"></i>
            </div>

            <div class="card-body">
                @if ($graduationYears->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-graduation-cap"></i>
                        <p>No graduation batches found.</p>
                    </div>
                @else
                    @foreach ($graduationYears as $gradYear)
                        <div class="batch-item mb-4">
                            <div class="batch-header collapsed" data-toggle="collapse" data-target="#batch-{{ $gradYear }}" aria-expanded="false">
                                <h5 class="batch-title">
                                    <i class="fas fa-graduation-cap batch-icon"></i>
                                    Graduation Batch {{ $gradYear }}
                                </h5>
                                <i class="fas fa-chevron-down"></i>
                            </div>

                            <div id="batch-{{ $gradYear }}" class="collapse">
                                <div class="batch-content">
                                    @php $batchStudents = $GraduatedStudents->where('graduated_at', $gradYear); @endphp

                                    @if($batchStudents->isNotEmpty())
                                        <div class="batch-actions">
                                            <span class="student-count">
                                                <i class="fas fa-users"></i> Graduated Students: {{ $batchStudents->count() }}
                                            </span>

                                            <div class="action-buttons">
                                                <a href="{{ route('graduate.students.export', ['year' => $gradYear]) }}"
                                                   target="_blank"
                                                   class="btn-pdf">
                                                    <i class="fas fa-file-pdf"></i> Export PDF
                                                </a>

                                                <form action="{{route('revert.student.batch', ['year' => $gradYear])}}" method="POST" id="revertForm-{{$gradYear}}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="button" class="btn-revert"
                                                            onclick="if(confirm('Are you sure you want to revert this entire batch? This action cannot be undone.')) { document.getElementById('revertForm-{{$gradYear}}').submit(); }">
                                                        <i class="fas fa-refresh"></i>
                                                        Revert Batch
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="table-container">
                                            <div class="table-responsive">
                                                <table class="table table-custom">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Admission #</th>
                                                            <th>First Name</th>
                                                            <th>Middle Name</th>
                                                            <th>Last Name</th>
                                                            <th>Gender</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($batchStudents as $key => $s)
                                                            <tr>
                                                                <td>{{ $key+1 }}</td>
                                                                <td class="text-uppercase">{{ $s->admission_number }}</td>
                                                                <td class="text-capitalize">{{ $s->first_name }}</td>
                                                                <td class="text-capitalize">{{ $s->middle_name }}</td>
                                                                <td class="text-capitalize">{{ $s->last_name }}</td>
                                                                <td class="text-capitalize">{{ $s->gender }}</td>
                                                                <td>
                                                                    <span class="badge-success-custom">Graduated</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <i class="fas fa-info-circle"></i>
                                            <p> No graduate students in this batch.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth animation to batch headers
            $('.batch-header').on('click', function() {
                $(this).toggleClass('collapsed');
                $(this).find('.batch-icon').toggleClass('fa-chevron-down fa-chevron-up');
            });

            // Initialize all batches as collapsed
            $('.collapse').collapse({ toggle: false });
        });
    </script>
@endsection
