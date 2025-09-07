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
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }

        .header-title {
            font-weight: 600;
            margin: 0;
        }

        .alert-custom {
            border-radius: 8px;
            padding: 20px;
            border: none;
            box-shadow: 0 0.15rem 0.5rem 0 rgba(58, 59, 69, 0.1);
        }

        .btn-action {
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
        }

        .info-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--warning-color);
        }

        @media (max-width: 768px) {
            .header-title {
                font-size: 1.2rem;
            }

            .btn-action {
                width: 100%;
                margin-top: 10px;
            }
        }
    </style>

    <div class="py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-10">
                <h4 class="text-primary fw-bold border-bottom pb-2">
                    <i class="fas fa-chart-line me-2"></i> ATTENDANCE REPORT
                </h4>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{url()->previous()}}" class="btn btn-info btn-action float-right">
                    <i class="fas fa-arrow-circle-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header-custom">
                        <h5 class="header-title text-white text-center">
                            <i class="fas fa-file-export me-2"></i> Generate Attendance Report
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <div class="info-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>

                            <div class="alert alert-warning alert-custom text-center" role="alert">
                                <h5 class="alert-heading mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Notice
                                </h5>
                                <p class="mb-0 lead">{{$message}}</p>
                            </div>

                            <div class="mt-4">
                                <p class="text-muted">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    Please check your input parameters and try again.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
