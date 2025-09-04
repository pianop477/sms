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
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px; */
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
            padding: 5px;
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

        .form-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }

        .form-label {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .required-star {
            color: var(--danger);
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .select2-container--default .select2-selection--single {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px;
            height: auto;
            background-color: white;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .flatpickr-input {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            background-color: white;
        }

        .flatpickr-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .invalid-feedback {
            font-weight: 600;
            color: var(--danger);
            margin-top: 5px;
        }

        .text-danger small {
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #3f43b5 0%, #7a80f9 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            color: #856404;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-warning-custom:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-success-custom:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .info-alert {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.15) 0%, rgba(23, 162, 184, 0.25) 100%);
            border: 1px solid rgba(23, 162, 184, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            backdrop-filter: blur(5px);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            /* padding: 10px; */
            font-weight: 600;
            text-align: center;
        }

        .table-custom tbody td {
            /* padding: 10px; */
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: rgba(78, 84, 200, 0.05);
        }

        .table-custom tbody tr:hover {
            background-color: rgba(78, 84, 200, 0.1);
        }

        .score-input {
            width: auto;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            transition: all 0.3s;
        }

        .score-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .grade-input {
            width: 60px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            font-weight: bold;
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

        .instruction-text {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.25) 100%);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 193, 7, 0.3);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .card-body {
                padding: 5px;
            }

            .header-title {
                font-size: 20px;
            }

            .table-responsive {
                font-size: 14px;
            }

            .score-input, .grade-input {
                width: 100%;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(78, 84, 200, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0);
            }
        }
    </style>

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title text-white">
                            <i class="fas fa-list-alt me-2"></i> Holiday Packages List
                        </h4>
                        <p class="mb-0 text-white-50"> Manage holiday packages for selected class and year</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('package.byClass', ['year' => $year])}}" class="btn btn-back float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-gift floating-icons"></i>
            </div>

            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center mb-4">
                    <span class="year-badge">
                        <i class="fas fa-calendar-alt"></i> Year: {{ $year }}
                    </span>
                    @if(isset($className))
                    <span class="class-badge">
                        <i class="fas fa-chalkboard"></i> Class: {{ $className }}
                    </span>
                    @endif
                </div>

                @if ($packages->isEmpty())
                    <div class="table-container">
                        <table class="table table-custom table-responsive-md" id="myTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Class</th>
                                    <th>Term</th>
                                    <th>Issued by</th>
                                    <th>Status</th>
                                    <th>Issued at</th>
                                    <th>Released at</th>
                                    <th>Downloads</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-calendar-times"></i>
                                            <p>No holiday packages available!</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-custom table-responsive-md" id="myTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Class</th>
                                        <th>Term</th>
                                        <th>Issued by</th>
                                        <th>Status</th>
                                        <th>Issued at</th>
                                        <th>Released at</th>
                                        <th>Downloads</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($packages as $recent)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td class="text-capitalize">{{$recent->title}}</td>
                                            <td class="text-uppercase">{{$recent->class_code}}</td>
                                            <td>Term {{$recent->term}}</td>
                                            <td class="text-capitalize">{{$recent->first_name}}. {{$recent->last_name[0]}}.</td>
                                            <td>
                                                @if ($recent->is_active == true)
                                                    <span class="badge-success-custom">Active <i class="fas fa-unlock"></i></span>
                                                @else
                                                    <span class="badge-danger-custom">Locked <i class="fas fa-lock"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($recent->created_at ?? $recent->updated_at)->format('d M Y, H:i') }}
                                            </td>
                                            <td>
                                                @if($recent->release_date)
                                                    {{ \Carbon\Carbon::parse($recent->release_date)->format('d M Y, H:i') }}
                                                @else
                                                    <span class="badge-warning-custom">Not Released</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge-primary-custom">{{$recent->download_count}}</span>
                                            </td>
                                            <td>
                                                <ul class="action-list">
                                                    @if ($recent->is_active == true)
                                                        <li>
                                                            <form action="{{route('deactivate.holiday.package', ['id' => Hashids::encode($recent->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="action-btn" title="Deactivate" onclick="return confirm('Are you sure you want to deactivate this package?')">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <form action="{{route('activate.holiday.package', ['id' => Hashids::encode($recent->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="action-btn" title="Activate" onclick="return confirm('Are you sure you want to activate this package?')">
                                                                    <i class="fas fa-eye-slash"></i>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a href="{{route('download.holiday.package', ['id' => Hashids::encode($recent->id), 'preview' => true])}}"
                                                           class="action-btn action-btn-info"
                                                           title="Download"
                                                           target="_blank"
                                                           onclick="return confirm('Are you sure you want to download this package?')">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('delete.holiday.package', ['id' => Hashids::encode($recent->id)])}}"
                                                           class="action-btn action-btn-danger"
                                                           title="Delete"
                                                           onclick="return confirm('Are you sure you want to delete this package?')">
                                                            <i class="fas fa-trash"></i>
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
                @endif
            </div>
        </div>
    </div>
@endsection
