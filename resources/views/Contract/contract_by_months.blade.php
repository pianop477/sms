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

        .list-group-item {
            border: 1px solid #e3e6f0;
            padding: 12px 20px;
            transition: all 0.3s;
            border-radius: 5px;
            margin-bottom: 8px;
        }

        .list-group-item:hover {
            background-color: #f8f9fc;
            border-color: var(--primary-color);
            transform: translateX(5px);
        }

        .list-group-item-action {
            color: var(--dark-color);
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .list-group-item-action i {
            transition: transform 0.3s;
        }

        .list-group-item-action:hover i {
            transform: translateX(3px);
        }

        .btn-action {
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
        }

        .alert-custom {
            border-radius: 8px;
            padding: 15px 20px;
        }

        .month-badge {
            background-color: var(--primary-color);
            color: white;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 0.8rem;
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
            <div class="col-md-8">
                <h4 class="text-primary fw-bold border-bottom pb-2">APPROVED CONTRACTS FOR {{ $year }}</h4>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{route('contract.management')}}" class="btn btn-info btn-action float-right">
                    <i class="fas fa-arrow-circle-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header-custom">
                        <h5 class="header-title text-white text-center">
                            <i class="fas fa-calendar-alt me-2"></i> Select Month
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($contractsByMonth->isEmpty())
                        <div class="alert alert-danger alert-custom text-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>No Records Available</strong>
                            <p class="mb-0 mt-2">There are no approved contracts for {{ $year }}</p>
                        </div>
                        @else
                        <div class="mb-4">
                            <p class="text-danger fw-bold mb-1">
                                <i class="fas fa-info-circle me-1"></i> Select a month to view approved contracts
                            </p>
                            <p class="text-muted small">Click on a month to view all contracts approved in that month</p>
                        </div>

                        <div class="list-group">
                            @foreach ($contractsByMonth as $month => $contracts )
                                <a href="{{route('contract.approved.all', ['year' => $year, 'month' => $month])}}"
                                   class="list-group-item list-group-item-action">
                                    <div>
                                        <i class="fas fa-folder-open me-2 text-primary"></i>
                                        <span class="fw-bold">{{\Carbon\Carbon::parse($month)->format('F')}}</span>
                                    </div>
                                    <span class="month-badge">{{$contracts->count()}} contracts</span>
                                </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
