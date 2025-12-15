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

    .summary-card {
        transition: transform 0.2s ease-in-out;
        border-left: 4px solid;
    }

    .summary-card:hover {
        transform: translateY(-2px);
    }

    .table th {
        background-color: var(--primary-color);
        color: white;
        font-weight: 600;
        padding: 10px 12px;
        font-size: 0.875rem;
        border: none;
    }

    .table td {
        padding: 10px 12px;
        vertical-align: middle;
        font-size: 0.85rem;
        border-color: #e3e6f0;
    }

    .invoice-row {
        background-color: #f8f9fc;
        font-weight: 600;
    }

    .payment-row {
        background-color: #ffffff;
    }

    .amount-cell {
        font-weight: 600;
        text-align: right;
    }

    .invoice-badge {
        background-color: var(--primary-color);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .payment-badge {
        background-color: var(--success-color);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .form-control-custom {
        border: 1px solid #d1d3e2;
        border-radius: 5px;
        padding: 8px 12px;
        font-size: 0.875rem;
        transition: all 0.3s;
    }

    .form-control-custom:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .text-overdue {
        color: var(--danger-color);
        font-weight: 600;
    }

    .text-due {
        color: var(--success-color);
        font-weight: 600;
    }

    .border-left-primary { border-left-color: var(--primary-color) !important; }
    .border-left-success { border-left-color: var(--success-color) !important; }
    .border-left-warning { border-left-color: var(--warning-color) !important; }
    .border-left-info { border-left-color: var(--info-color) !important; }

    @media (max-width: 768px) {
        .table-responsive-md {
            font-size: 0.8rem;
        }

        .table td, .table th {
            padding: 6px 8px;
        }
    }
</style>

<div class="py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="text-primary fw-bold border-bottom pb-2">
                <i class="fas fa-credit-card me-2"></i>
                Payment History for <span style="text-decoration:underline"><strong>{{ ucwords(strtolower($students->first_name . ' ' . $students->last_name)) }}</strong></span>
            </h4>
        </div>
        <div class="col-md-4 text-md-end align-self-center">
            <a href="{{ route('students.profile', ['student' => Hashids::encode($students->id)]) }}" class="btn btn-secondary btn-sm float-right">
                <i class="fas fa-arrow-left me-1"></i> Back to Profile
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card summary-card border-left-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Billed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalBilled) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card summary-card border-left-success">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Paid
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalPaid) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card summary-card border-left-{{ $totalBalance > 0 ? 'warning' : 'info' }}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-{{ $totalBalance > 0 ? 'warning' : 'info' }} text-uppercase mb-1">
                                Balance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalBalance) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-3">
            <div class="card summary-card border-left-secondary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Total Records
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $paymentRecords->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <div class="row">
        <!-- Filter Section -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header-custom">
                    <h5 class="header-title text-white text-center">
                        <i class="fas fa-filter me-2"></i> Filter Records
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">
                            <i class="fas fa-calendar-alt me-1"></i> Select Academic Year
                        </label>
                        <form method="GET" action="{{ url()->current() }}" id="yearFilterForm">
                            @php
                                $currentYear = (int) date('Y');
                                $start = 2024;
                            @endphp

                            <select name="year" id="selectYear" class="form-control-custom" onchange="this.form.submit()">
                                <option value="">-- All Years --</option>

                                @for ($y = $currentYear; $y >= $start; $y--)
                                    <option value="{{ $y }}" {{ ($selectedYear ?? '') == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </form>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-4">
                        <h6 class="text-dark fw-bold mb-3">Transaction Summary</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Invoices:</span>
                            <span class="badge bg-primary text-white">{{ $paymentRecords->where('type', 'invoice')->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Payments:</span>
                            <span class="badge bg-success text-white">{{ $paymentRecords->where('type', 'payment')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Records Table -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="header-title text-white mb-0">
                        <i class="fas fa-history me-2"></i> Payment Timeline
                    </h5>
                    <span class="badge bg-light text-dark">
                        {{ $paymentRecords->count() }} {{ Str::plural('record', $paymentRecords->count()) }}
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-responsive-md">
                            <thead>
                                <tr>
                                    <th>Control#</th>
                                    <th>Year</th>
                                    <th>Receipt</th>
                                    <th>Service</th>
                                    <th class="text-end">Bill</th>
                                    <th class="text-end">Paid</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentControlNumber = null;
                                @endphp

                                @forelse($paymentRecords as $record)
                                    @if($currentControlNumber !== $record['control_number'])
                                        @php $currentControlNumber = $record['control_number']; @endphp
                                        <!-- Invoice Row -->
                                        <tr class="invoice-row">
                                            <td class="fw-bold text-uppercase">{{ $record['control_number'] }}</td>
                                            <td>{{ $record['academic_year'] }}</td>
                                            <td>
                                                <span class="invoice-badge">INVOICE</span>
                                            </td>
                                            <td class="text-capitalize">{{ $record['service_name'] }}</td>
                                            <td class="amount-cell">{{ number_format($record['amount']) }}</td>
                                            <td class="amount-cell">0</td>
                                            <td>
                                                @if ($record['status'] == 'active')
                                                    <span class="badge bg-primary text-white">{{strtoupper($record['status'])}}</span>
                                                @elseif ($record['status'] == 'cancelled')
                                                    <span class="badge bg-warning text-primary">{{strtoupper($record['status'])}}</span>
                                                @elseif ($record['status'] =='expired')
                                                    <span class="badge bg-danger text-white">{{strtoupper($record['status'])}}</span>
                                                @else
                                                    <span class="badge bg-success text-white">{{strtoupper($record['status'])}}</span>
                                                @endif
                                            </td>
                                            <td>{{\Carbon\Carbon::parse($record['due_date'])->format('d-m-Y')}}</td>
                                        </tr>
                                    @endif

                                    <!-- Payment Rows -->
                                    @if($record['type'] == 'payment')
                                        <tr class="payment-row">
                                            <td class="text-muted small text-uppercase">{{ $record['control_number'] }}</td>
                                            <td class="text-muted small">{{ $record['academic_year'] }}</td>
                                            <td>
                                                <span class="payment-badge">PAYMENT</span>
                                            </td>
                                            <td class="text-muted small text-capitalize">{{ $record['service_name'] }}</td>
                                            <td class="text-muted amount-cell">0</td>
                                            <td class="amount-cell text-success">{{ number_format($record['amount']) }}</td>
                                            <td>
                                                <span class="badge bg-info text-white">{{strtoupper($record['status'])}}</span>
                                            </td>
                                            <td>-</td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-receipt fa-2x mb-3"></i>
                                                <p class="mb-0">
                                                    @if($selectedYear)
                                                        No payment records found for {{ $selectedYear }}
                                                    @else
                                                        No payment records found
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse

                                <!-- Closing Summary -->
                                @if($paymentRecords->count() > 0)
                                    <tr class="table-secondary">
                                        <td colspan="4" class="text-end fw-bold">CLOSING BALANCE:</td>
                                        <td class="amount-cell fw-bold">{{ number_format($totalBilled) }}</td>
                                        <td colspan="" class="amount-cell fw-bold text-success">{{ number_format($totalPaid) }}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr class="table-light">
                                        <td colspan="5" class="text-end fw-bold">OUTSTANDING BALANCE:</td>
                                        <td colspan="" class="amount-cell fw-bold {{ $totalBalance > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($totalBalance) }}
                                        </td>
                                        <td colspan="3"></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            @if($paymentRecords->count() > 0)
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-dark fw-bold mb-3">
                                    <i class="fas fa-info-circle me-2"></i> Transaction Description
                                </h6>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="invoice-badge me-2">INVOICE</span>
                                    <small class="text-muted">- Original bill created</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="payment-badge me-2">PAYMENT</span>
                                    <small class="text-muted">- Payment made against invoice</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Auto-submit form when year changes
        const yearSelect = document.getElementById('selectYear');
        if (yearSelect) {
            yearSelect.addEventListener('change', function() {
                document.getElementById('yearFilterForm').submit();
            });
        }
    });
</script>
@endsection
