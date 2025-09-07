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

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0.15rem 0.5rem 0 rgba(58, 59, 69, 0.1);
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 12px 15px;
            text-align: center;
            vertical-align: middle;
        }

        .table td {
            padding: 12px 15px;
            vertical-align: middle;
            text-align: center;
        }

        .badge-status {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
        }

        .btn-action {
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }

        .month-header {
            color: var(--primary-color);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }

            .header-title {
                font-size: 1.1rem;
            }

            .table th, .table td {
                padding: 8px 10px;
                font-size: 0.9rem;
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
                <h4 class="text-primary fw-bold border-bottom pb-2">
                    <i class="fas fa-file-contract me-2"></i> APPROVED CONTRACTS
                </h4>
                <p class="month-header mb-0 mt-2">
                    <i class="fas fa-calendar me-2"></i> {{$month}} {{$year}}
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{route('contract.by.months', ['year' => $year])}}" class="btn btn-info btn-action float-right">
                    <i class="fas fa-arrow-circle-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header-custom">
                        <h5 class="header-title text-white text-center">
                            <i class="fas fa-list-check me-2"></i> Contracts List
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($allContracts->isEmpty())
                            <div class="alert alert-warning text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                <h5>No Contracts Found</h5>
                                <p class="mb-0">There are no approved contracts for {{$month}} {{$year}}</p>
                            </div>
                        @else
                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table table-hover" id="myTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Teacher's Name</th>
                                            <th scope="col">Contract Type</th>
                                            <th scope="col">Approved At</th>
                                            <th scope="col">Expires At</th>
                                            <th scope="col">Duration</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allContracts as $row )
                                            <tr>
                                                <td class="fw-bold">{{strtoupper($row->member_id)}}</td>
                                                <td class="text-capitalize">{{$row->first_name}} {{$row->last_name}}</td>
                                                <td class="text-capitalize">{{$row->contract_type}}</td>
                                                <td>
                                                    <span class="text-primary">
                                                        <i class="fas fa-calendar-check me-1"></i>
                                                        {{\Carbon\Carbon::parse($row->approved_at)->format('d-m-Y H:i')}}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-info">
                                                        <i class="fas fa-hourglass-end me-1"></i>
                                                        {{\Carbon\Carbon::parse($row->end_date)->format('d-m-Y H:i')}}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge-status bg-secondary text-white">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{$row->duration}} Months
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($row->duration > 0)
                                                        <span class="badge-status bg-success text-white">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            {{$row->status}}
                                                        </span>
                                                    @else
                                                        <span class="badge-status bg-danger text-white">
                                                            <i class="fas fa-times-circle me-1"></i>
                                                            {{$row->status}}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <p class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Showing {{$allContracts->count()}} approved contracts
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
