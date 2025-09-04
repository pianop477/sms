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
            padding: 8px 16px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
        }

        .list-group-item-custom {
            border: none;
            border-radius: 15px;
            padding: 20px 25px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .list-group-item-custom:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            transform: translateX(10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .class-name {
            font-weight: 700;
            color: var(--primary);
            margin: 0;
            font-size: 18px;
        }

        .class-link {
            text-decoration: none;
            color: inherit;
            display: block;
            width: 100%;
        }

        .class-link:hover {
            text-decoration: none;
            color: inherit;
        }

        .arrow-icon {
            background: var(--primary);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .list-group-item-custom:hover .arrow-icon {
            transform: translateX(5px);
            background: var(--secondary);
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

        .instruction-text {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.25) 100%);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 193, 7, 0.3);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
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

            .list-group-item-custom {
                padding: 15px 20px;
            }

            .class-name {
                font-size: 16px;
            }
        }
    </style>

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title text-white">
                            <i class="fas fa-chalkboard me-2"></i> Holiday Packages by Class
                        </h4>
                        <p class="mb-0 text-white-50"> Select a class to view available packages</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('package.byYear')}}" class="btn btn-back float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-graduation-cap floating-icons"></i>
            </div>

            <div class="card-body">
                <div class="year-badge">
                    <i class="fas fa-calendar-alt me-2"></i> Year: {{ $year }}
                </div>

                <div class="instruction-text">
                    <i class="fas fa-info-circle text-warning"></i>
                    Select a class to view holiday packages
                </div>

                @if ($classGroups->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>No holiday packages available for this year!</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach ($classGroups as $class => $package)
                            <a href="{{route('packages.list', ['year' => $year, 'class' => Hashids::encode($package->first()->grade_id)])}}" class="class-link">
                                <div class="list-group-item-custom">
                                    <h6 class="class-name text-uppercase">
                                        <i class="fas fa-chalkboard-teacher me-2"></i> {{ $class }}
                                    </h6>
                                    <div class="arrow-icon">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
