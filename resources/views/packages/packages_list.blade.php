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
            padding: 20px;
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
            padding: 25px 30px;
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
            padding: 30px;
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

        .year-badge {
            background: linear-gradient(135deg, var(--info) 0%, #17a2b8 100%);
            color: white;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .class-badge {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: 10px;
            margin-bottom: 20px;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
            text-align: center;
            font-size: 14px;
            white-space: nowrap;
        }

        .table-custom tbody td {
            padding: 15px 12px;
            vertical-align: middle;
            border-color: #e9ecef;
            text-align: center;
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
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-danger-custom {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: white;
            border-radius: 50px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 50px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            color: #856404;
            border-radius: 50px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 12px;
        }

        .action-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .action-list li {
            display: inline-block;
        }

        .action-btn {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 16px;
            transition: all 0.3s;
            padding: 8px;
            border-radius: 5px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn:hover {
            background-color: rgba(78, 84, 200, 0.1);
            transform: scale(1.1);
            color: var(--secondary);
        }

        .action-btn-danger:hover {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }

        .action-btn-info:hover {
            background-color: rgba(23, 162, 184, 0.1);
            color: var(--info);
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 18px;
            margin: 0;
            font-weight: 500;
        }

        @media (max-width: 1200px) {
            .table-container {
                overflow-x: auto;
            }

            .table-custom {
                min-width: 1000px;
            }
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 20px;
            }

            .action-list {
                flex-wrap: wrap;
                gap: 8px;
            }

            .action-btn {
                width: 32px;
                height: 32px;
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
