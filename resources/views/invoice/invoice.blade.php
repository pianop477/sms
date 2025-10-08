@extends('SRTDashboard.frame')
@section('content')
    <style>
        :root {
            --primary: #2c5aa0;
            --secondary: #1e3a8a;
            --accent: #3b82f6;
            --light-bg: #f8fafc;
            --border: #e2e8f0;
            --text: #1e293b;
            --text-light: #64748b;
        }

        body {
            background: #f1f5f9;
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .modern-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .invoice-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px;
            position: relative;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company-info h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .company-info .tagline {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .invoice-meta {
            text-align: right;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        .invoice-meta h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .invoice-body {
            padding: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-card {
            background: var(--light-bg);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid var(--accent);
        }

        .info-card h3 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .info-item {
            display: flex;
            justify-content: between;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: 600;
            color: var(--text);
            min-width: 120px;
        }

        .info-value {
            color: var(--text-light);
        }

        .service-period {
            background: var(--light-bg);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid var(--border);
        }

        .service-period h3 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .period-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .invoice-table thead {
            background: var(--primary);
            color: white;
        }

        .invoice-table th {
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .invoice-table td {
            padding: 15px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .invoice-table tbody tr:hover {
            background: #f8fafc;
        }

        .amount-input {
            width: 120px;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.9rem;
            text-align: center;
        }

        .amount-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .total-row {
            background: var(--light-bg) !important;
            font-weight: 600;
        }

        .total-amount {
            font-size: 1.3rem;
            color: var(--primary);
            font-weight: 700;
        }

        .payment-section {
            margin-top: 40px;
        }

        .payment-section h3 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.3rem;
            font-weight: 600;
            text-align: center;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .payment-method {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .payment-method h4 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding: 20px;
            background: var(--light-bg);
            border-radius: 8px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-print {
            background: var(--primary);
            color: white;
        }

        .btn-print:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .btn-send {
            background: #10b981;
            color: white;
        }

        .btn-send:hover {
            background: #059669;
            transform: translateY(-2px);
        }

        .invoice-footer {
            background: var(--light-bg);
            padding: 20px;
            border-top: 1px solid var(--border);
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* PRINT STYLES */
        @media print {
            @page {
                margin: 15mm;
                size: A4;
            }

            body {
                background: white !important;
                color: black !important;
                font-size: 12pt;
                font-family: 'Arial', 'Helvetica', sans-serif;
                line-height: 1.4;
            }

            .dashboard-container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .modern-card {
                box-shadow: none !important;
                border: 1px solid #000 !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }

            .invoice-header {
                background: #f8f9fa !important;
                color: black !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 20px !important;
            }

            .header-content {
                display: flex !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
            }

            .company-info h1 {
                color: black !important;
                font-size: 24pt !important;
            }

            .invoice-meta {
                background: #e9ecef !important;
                color: black !important;
                padding: 15px !important;
            }

            .invoice-body {
                padding: 20px !important;
            }

            .info-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 20px !important;
            }

            .info-card {
                background: #f8f9fa !important;
                border-left: 4px solid #000 !important;
            }

            .service-period {
                background: #f8f9fa !important;
                border: 1px solid #000 !important;
            }

            .invoice-table {
                border: 1px solid #000 !important;
            }

            .invoice-table thead {
                background: #e9ecef !important;
                color: black !important;
            }

            .invoice-table th,
            .invoice-table td {
                border: 1px solid #000 !important;
                padding: 10px 8px !important;
            }

            .amount-input {
                border: 1px solid #000 !important;
                background: white !important;
            }

            .payment-methods {
                grid-template-columns: 1fr 1fr !important;
            }

            .payment-method {
                border: 1px solid #000 !important;
                background: white !important;
            }

            .action-buttons,
            .btn {
                display: none !important;
            }

            .invoice-footer {
                background: #f8f9fa !important;
                border-top: 1px solid #000 !important;
            }

            /* Ensure good print layout */
            .modern-card {
                page-break-inside: avoid;
            }

            .invoice-table {
                page-break-inside: avoid;
            }

            .payment-section {
                page-break-inside: avoid;
            }
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 10px;
            }

            .header-content {
                flex-direction: column;
                gap: 20px;
            }

            .invoice-meta {
                text-align: left;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .payment-methods {
                grid-template-columns: 1fr;
            }

            .period-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .footer-content {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>

    <div class="dashboard-container">
        <div class="modern-card">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <div class="header-content">
                    <div class="company-info">
                        <h1>INVOICE</h1>
                        <p class="tagline">SHULEAPP - School Management System</p>
                        <div style="margin-top: 15px; background: rgba(255,255,255,0.1); padding: 10px; border-radius: 6px; color:white">
                            <p style="margin: 2px 0;">{{ ucwords(strtolower(Auth::user()->first_name)) }} {{ ucwords(strtolower(Auth::user()->last_name ))}}</p>
                            <p style="margin: 2px 0;">{{ Auth::user()->email }}</p>
                            <p style="margin: 2px 0;">{{ Auth::user()->phone }}</p>
                        </div>
                    </div>
                    <div class="invoice-meta">
                        <h2>INV-{{ \Carbon\Carbon::now()->format('Ymd-His') }}</h2>
                        <div class="info-item">
                            <span>Invoice Date: </span>
                            <strong> {{\Carbon\Carbon::now()->format('d M Y')}}</strong>
                        </div>
                        <div class="info-item">
                            <span>Due Date: </span>
                            <strong> {{ \Carbon\Carbon::now()->addMonth()->format('d M Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Body -->
            <div class="invoice-body">
                <!-- Client and Invoice Info -->
                <div class="info-grid">
                    <div class="info-card">
                        <h3>Billed To</h3>
                        <div class="info-item">
                            <strong>{{ ucwords(strtolower($schools->school_name)) }}</strong>
                        </div>
                        <div class="info-item">
                            <span>Address:</span>
                            <span>{{ ucwords(strtolower($schools->postal_address)) }} - {{ ucwords(strtolower($schools->postal_name)) }}</span>
                        </div>
                        <div class="info-item">
                            <span>Country:</span>
                            <span>{{ ucwords(strtolower($schools->country)) }}</span>
                        </div>
                        <div class="info-item">
                            <span>Email:</span>
                            <span>{{ $managers->first()->email }}</span>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3>Service Time Duration</h3>
                        <div class="info-item">
                            <span>Start Date :</span>
                            <span>{{ \Carbon\Carbon::parse($schools->service_start_date)->format('d M Y') ?? 'Not set' }}</span>
                        </div>
                        <div class="info-item">
                            <span>End Date: </span>
                            <span>{{ \Carbon\Carbon::parse($schools->service_end_date)->format('d M Y') ?? 'Not set' }}</span>
                        </div>
                        <div class="info-item">
                            <span>Duration: </span>
                            <span>{{ \Carbon\Carbon::parse($schools->service_start_date)->diffInMonths(\Carbon\Carbon::parse($schools->service_end_date)) }} months</span>
                        </div>
                    </div>
                </div>

                <!-- Invoice Table -->
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Time Duration</th>
                            <th>No. of Students</th>
                            <th>Unit Cost (TZS)</th>
                            <th>Total (TZS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <strong>Service Cost for Year - {{ \Carbon\Carbon::now()->format('Y') }}</strong>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($schools->service_start_date)->format('d/m/Y') ?? '-' }} -
                                {{ \Carbon\Carbon::parse($schools->service_end_date)->format('d/m/Y') ?? '-' }}
                            </td>
                            <td style="text-align: center;">
                                <strong>{{ count($students) }}</strong>
                            </td>
                            <td style="text-align: center;">
                                <input type="number" id="unit_cost" class="amount-input"
                                       placeholder="0" min="0" value=""
                                       oninput="calculateTotal()">
                            </td>
                            <td style="text-align: center;" class="total-amount" id="total_cost">
                                0
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="5" style="text-align: right; padding-right: 20px;">
                                <strong>TOTAL AMOUNT:</strong>
                            </td>
                            <td style="text-align: center;" class="total-amount" id="total_balance">
                                TZS 0
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Payment Methods -->
                <div class="payment-section">
                    <h3>Payment Methods</h3>
                    <div class="payment-methods">
                        <div class="payment-method">
                            <h4>🏦 Bank Transfer</h4>
                            <p><strong>Bank:</strong> NMB Bank</p>
                            <p><strong>Account Number:</strong> 50510028891</p>
                            <p><strong>Account Name:</strong> Frank Mathias Masaka</p>
                        </div>
                        <div class="payment-method">
                            <h4>📱 Mobile Payment</h4>
                            <p><strong>Provider:</strong> Tigo Pesa</p>
                            <p><strong>Merchant Number:</strong> 15966786</p>
                            <p><strong>Merchant Name:</strong> Piano Shop</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons no-print">
                    <button class="btn btn-print" onclick="scrollToTopAndPrint()">
                        🖨️ Print Invoice
                    </button>
                    <a href="{{ route('admin.send.invoice', ['school' => Hashids::encode($schools->id)]) }}" class="btn btn-send">
                        ✉️ Send Invoice
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="invoice-footer">
                <div class="footer-content">
                    <div>
                        <strong>Printed by:</strong> {{ Auth::user()->email }}
                    </div>
                    <div>
                        <strong>Printed on:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateTotal() {
            let unitCost = parseFloat(document.getElementById("unit_cost").value) || 0;
            let totalStudents = {{ count($students) }};
            let total = unitCost * totalStudents;

            document.getElementById("total_cost").textContent = total.toLocaleString();
            document.getElementById("total_balance").textContent = 'TZS ' + total.toLocaleString();
        }

        function scrollToTopAndPrint() {
            window.scrollTo(0, 0);
            setTimeout(() => {
                window.print();
            }, 500);
        }

        // Auto-focus on unit cost input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('unit_cost').focus();
        });
    </script>
@endsection
