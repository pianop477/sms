@extends('SRTDashboard.frame')
@section('content')
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient-start: #4361ee;
            --gradient-end: #3a0ca3;
            --card-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 4px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--card-shadow);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .glass-card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-5px);
        }

        .invoice-header {
            background: gray;
            color: white;
            border-radius: 24px 24px 0 0;
            padding: 3px;
            position: relative;
            overflow: hidden;
        }

        .invoice-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .logo-container {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 5px
            display: inline-block;
            backdrop-filter: blur(10px);
        }

        .invoice-logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .invoice-details {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 5px;
            margin: -4px 4px 4px 4px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .billed-to {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), rgba(58, 12, 163, 0.1));
            border-radius: 16px;
            padding: 2px;
            border: 2px solid rgba(67, 97, 238, 0.2);
        }

        .invoice-table {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .invoice-table thead {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
        }

        .invoice-table th {
            padding: 4px;
            font-weight: 600;
            border: none;
        }

        .invoice-table td {
            padding: 4px;
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
            vertical-align: middle;
        }

        .invoice-table tbody tr {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .invoice-table tbody tr:hover {
            background: rgba(67, 97, 238, 0.05);
            transform: translateX(5px);
        }

        .form-control {
            border: 2px solid rgba(67, 97, 238, 0.2);
            border-radius: 12px;
            padding: 5px 3px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.3rem rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--success), #0f9d58);
            border: none;
            border-radius: 16px;
            padding: 10px 15px;
            color: white;
            font-weight: 600;
            transition: all 0.4s ease;
            box-shadow: 0 15px 35px rgba(28, 200, 138, 0.3);
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 25px 45px rgba(28, 200, 138, 0.4);
            color: white;
        }

        .btn-print {
            background: linear-gradient(135deg, var(--info), var(--primary));
            border: none;
            border-radius: 16px;
            padding: 10px 15px;
            color: white;
            font-weight: 600;
            transition: all 0.4s ease;
            box-shadow: 0 15px 35px rgba(67, 97, 238, 0.3);
        }

        .btn-print:hover {
            transform: translateY(-3px);
            box-shadow: 0 25px 45px rgba(67, 97, 238, 0.4);
            color: white;
        }

        .payment-method {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 8px;
            margin: 4px 0;
            border: 2px solid rgba(67, 97, 238, 0.1);
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .total-amount {
            background: linear-gradient(135deg, var(--warning), #f77f00);
            color: white;
            padding: 4px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 20;
            text-align: center;
            box-shadow: 0 10px 30px rgba(247, 127, 0, 0.3);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 4px;
            }

            .invoice-header {
                padding: 5px;
            }

            .invoice-details {
                margin: -4px 4px 4px 4px;
                padding: 6px;
            }

            .invoice-table th,
            .invoice-table td {
                padding: 4px;
            }

            .btn-modern,
            .btn-print {
                width: 100%;
                margin-bottom: 4px;
            }
        }

        /* Animation classes */
        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .slide-in {
            animation: slideInRight 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media print {

            @page {
                margin: 6mm;
            }
            .btn-print, .btn-modern {
                display: none;
            }
            .dashboard-container {
                padding: 0;
            }
            body {
                color: black;
                font-size: 12px;
                background: white;
                font-family: 'Arial', 'Helvetica', 'sans-serif';
            }

            .invoice-table {
                color: black;
                border: none;
            }
            .billed-to {
                color: black;
                border: none;
                padding: 1px;
            }

            .payment-method {
                border: none;
                font-size: 11px;
                color: black;
            }

            .badge {
                color: black;
                font-size: 12px;
                border: none;
            }

            .glass-card {
                font-size: 10px;
                margin-top: 4px;
            }
            .invoice-header {
                background: none;
                color: black;
                text-align: center;
            }
            .invoice-details {
                background: none;
                color: black;
                box-shadow: none;
            }
            .form-control {
                border: none;
                color: black;
            }

             table {
                border-collapse: collapse; /* ili borders zishikamane vizuri */
                width: 100%;
            }
            table, th, td {
                border: 1px solid black; /* border nyeusi ya 1px */
            }

        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .amount-display {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            padding: 2px;
            background: rgba(67, 97, 238, 0.1);
            border-radius: 16px;
            margin: 2px 0;
        }

        .student-count {
            background: linear-gradient(135deg, rgba(76, 201, 240, 0.1), rgba(72, 149, 239, 0.1));
            padding: 2px 4px;
            border-radius: 20px;
            font-weight: 600;
            color: var(--info);
        }

        .invoice-header {
                background: #d9ddec !important;
                -webkit-print-color-adjust: exact;
            }
    </style>

    <div class="dashboard-container">
        <!-- Invoice Header -->
        <div class="glass-card fade-in">
            <div class="invoice-header">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="logo-container">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="ShuleApp Logo" class="invoice-logo">
                        </div>
                    </div>
                    <div class="col-md-9 text-end">
                        <h4 class="display-4 fw-bold mb-2 text-dark">INVOICE</h4>
                        <p class="lead mb-1 opacity-90 text-dark">SHULEAPP - ADMINISTRATOR</p>
                        <p class="mb-1 text-capitalize text-dark">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                        <p class="mb-1 text-dark">{{ Auth::user()->email }}</p>
                        <p class="mb-0 text-dark">{{ Auth::user()->phone }}</p>
                    </div>
                </div>
            </div>
            <!-- Invoice Details -->
            <div class="invoice-details">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="billed-to">
                            <h4 class="text-primary mb-3">
                                <i class="fas fa-building me-2"></i>Billed To
                            </h4>
                            <h5 class="text-uppercase text-primary fw-bold">{{ $schools->school_name }}</h5>
                            <p class="text-capitalize mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                {{ $schools->postal_address }} - {{ $schools->postal_name }}
                            </p>
                            <p class="text-capitalize mb-1">
                                <i class="fas fa-globe me-2"></i>
                                {{ $schools->country }}
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-envelope me-2"></i>
                                {{ $managers->first()->email }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="bg-light p-4 rounded-3">
                            <h4 class="text-primary mb-3">
                                <i class="fas fa-receipt me-2"></i>Invoice Details
                            </h4>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Invoice Date:</span>
                                <span>{{\Carbon\Carbon::now()->format('d M Y')}}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Due Date:</span>
                                <span>{{ \Carbon\Carbon::now()->addMonth()->format('d M Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Invoice #:</span>
                                <span>INV-{{ \Carbon\Carbon::now()->format('Ymd-His') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Details -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-light p-2 rounded-3">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Service Period
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Start Date:</strong>
                                        {{ \Carbon\Carbon::parse($schools->service_start_date)->format('d M Y') ?? 'Not set' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>End Date:</strong>
                                        {{ \Carbon\Carbon::parse($schools->service_end_date)->format('d M Y') ?? 'Not set' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Table -->
                <div class="invoice-table fade-in">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-left">Description</th>
                                <th class="text-left">Time Duration</th>
                                <th class="text-center">No. of Students</th>
                                <th class="text-center">Unit Cost (TZS)</th>
                                <th class="text-center">Total (TZS)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="slide-in">
                                <td class="text-center fw-bold">1</td>
                                <td class="text-left">
                                    {{-- <i class="fas fa-cogs me-2 text-primary"></i> --}}
                                    Service Cost for {{ \Carbon\Carbon::now()->format('Y') }}
                                </td>
                                <td class="text-left">
                                    <span class="badge bg-primary text-white">
                                        {{ \Carbon\Carbon::parse($schools->service_start_date)->format('d/m/Y') ?? '-' }} -
                                        {{ \Carbon\Carbon::parse($schools->service_end_date)->format('d/m/Y') ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="student-count">
                                        <i class="fas fa-users me-1"></i>
                                        {{ count($students) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <input type="number" id="unit_cost" class="form-control text-center"
                                           placeholder="Enter Amount" min="0" value=""
                                           oninput="calculateTotal()">
                                </td>
                                <td class="text-center fw-bold amount-display" id="total_cost">
                                    0
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end fw-bold fs-5">Total Balance:</td>
                                <td class="text-center">
                                    <div class="total-amount" id="total_balance">
                                        TZS 0
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Payment Methods -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h4 class="text-primary text-center mb-4">
                            <i class="fas fa-credit-card me-2"></i>Payment Methods
                        </h4>
                        <div class="payment-method">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-university me-2"></i>Bank Transfer
                            </h5>
                            <p class="mb-1"><strong>Bank:</strong> NMB Bank</p>
                            <p class="mb-1"><strong>Account Number:</strong> 50510028891</p>
                            <p class="mb-0"><strong>Account Name:</strong> Frank Mathias Masaka</p>
                        </div>
                        <div class="payment-method">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-mobile-alt me-2"></i>Mobile Payment
                            </h5>
                            <p class="mb-1"><strong>Provider:</strong> Tigo Pesa</p>
                            <p class="mb-1"><strong>Merchant Number:</strong> 15966786</p>
                            <p class="mb-0"><strong>Merchant Name:</strong> Piano Shop</p>
                        </div>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <button class="btn-print me-3" onclick="scrollToTopAndPrint(); return false;">
                            <i class="fas fa-print me-2"></i>Print Invoice
                        </button>
                        <a href="{{ route('admin.send.invoice', ['school' => Hashids::encode($schools->id)]) }}" class="btn-modern">
                            <i class="fas fa-paper-plane me-2"></i>Send Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="glass-card mt-2 p-2 text-center">
        <div class="row">
            <div class="col-md-6 text-start">
                <small class="text-muted">
                    <i class="fas fa-user me-1"></i>Printed by: {{ Auth::user()->email }}
                </small>
            </div>
            <div class="col-md-6 text-end">
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>Printed on: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}
                </small>
            </div>
        </div>
    </div>

    <script>
        function calculateTotal() {
            let unitCost = parseFloat($("#unit_cost").val()) || 0;
            let totalStudents = {{ count($students) }};
            let total = unitCost * totalStudents;

            // Format numbers with commas
            $("#total_cost").text(total.toLocaleString());
            $("#total_balance").html(`TZS ${total.toLocaleString()}`);
        }

        function scrollToTopAndPrint() {
            window.scrollTo(0, 0);
            setTimeout(() => {
                window.print();
            }, 500);
        }

        // Add GSAP animations if available
        if (typeof gsap !== 'undefined') {
            gsap.from('.fade-in', {
                duration: 1,
                y: 30,
                opacity: 0,
                stagger: 0.2,
                ease: "power3.out"
            });

            gsap.from('.slide-in', {
                duration: 0.8,
                x: 50,
                opacity: 0,
                stagger: 0.1,
                ease: "power2.out"
            });
        }

        // Auto-focus on unit cost input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('unit_cost').focus();
        });
    </script>

    <style>
        @media print {

        }
    </style>
@endsection
