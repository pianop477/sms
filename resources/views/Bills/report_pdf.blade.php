<!DOCTYPE html>
<html>
<head>
    <title>Bills Report - {{ $school->school_name }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 landscape;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #2c3e50;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .header {
            border-bottom: 3px solid #3498db;
            padding-bottom: 12px;
            margin-bottom: 15px;
            display: table;
            width: 100%;
        }

        .logo-container {
            display: table-cell;
            vertical-align: middle;
            width: 80px;
        }

        .logo {
            max-width: 70px;
            max-height: 60px;
            display: block;
        }

        .logo-placeholder {
            width: 70px;
            height: 50px;
            border: 1px solid #bdc3c7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #7f8c8d;
            background: #ecf0f1;
            border-radius: 4px;
        }

        .school-info {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding-left: 15px;
        }

        .school-name {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 3px;
            color: #2c3e50;
            letter-spacing: 0.5px;
        }

        .school-address {
            font-size: 9px;
            color: #7f8c8d;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .report-title {
            text-align: center;
            margin: 15px 0 12px 0;
            padding: 8px 0;
            background: linear-gradient(to right, #f8f9fa, #ecf0f1, #f8f9fa);
            border-radius: 4px;
        }

        .report-title h1 {
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 3px 0;
            color: #2c3e50;
            letter-spacing: 0.5px;
        }

        .report-period {
            font-size: 10px;
            color: #7f8c8d;
            font-weight: 600;
        }

        .report-summary {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            border-left: 4px solid #3498db;
            margin-bottom: 12px;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 5px 0;
            font-size: 8px;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th {
            background: #34495e !important;
            color: white !important;
            border: 1px solid #2c3e50 !important;
            padding: 6px 4px !important;
            text-align: left;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 7px;
            letter-spacing: 0.3px;
        }

        td {
            border: 1px solid #bdc3c7;
            padding: 5px 4px;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .status-fullpaid {
            color: #27ae60;
            font-weight: 700;
            background: #d5f4e6;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            display: inline-block;
        }

        .status-expired {
            color: #e74c3c;
            font-weight: 700;
            background: #fdeaea;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            display: inline-block;
        }

        .status-cancelled {
            color: #f39c12;
            font-weight: 700;
            background: #fef5e7;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            display: inline-block;
        }
        .status-active {
            color: #2980b9;
            font-weight: 700;
            background: #e8f4fd;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            display: inline-block;
        }
        .status-overpaid {
            color: #8e44ad;
            font-weight: 700;
            background: #f3e8fd;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            display: inline-block;
        }

        .amount {
            text-align: right;
            font-family: 'DejaVu Sans Mono', 'Courier New', monospace;
            font-weight: 600;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: 700;
        }

        .summary-row {
            background: #2c3e50 !important;
            color: white !important;
            font-weight: 700;
        }

        .summary-row td {
            border-color: #2c3e50;
            background: #2c3e50 !important;
            color: white !important;
        }

        .report-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #bdc3c7;
            font-size: 8px;
            color: #7f8c8d;
            text-align: center;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        /* Column widths for bills report */
        .col-number { width: 3%; }
        .col-control { width: 8%; }
        .col-admission { width: 8% }
        .col-student { width: 13%; }
        .col-level { width: 6%; }
        .col-year { width: 6%; }
        .col-billed { width: 8%; }
        .col-paid { width: 8%; }
        .col-balance { width: 8%; }
        .col-status { width: 6%; }
        .col-issued { width: 8%; }
        .col-expires { width: 8%; }
        .col-service { width: 10%; }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="logo-container">
            @php
                $logoBase64 = null;
                if (!empty($school->logo)) {
                    $logoFile = storage_path('app/public/logo/' . $school->logo);
                    if (file_exists($logoFile)) {
                        $type = pathinfo($logoFile, PATHINFO_EXTENSION);
                        $data = file_get_contents($logoFile);
                        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                }
            @endphp

            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="School Logo" class="logo">
            @else
                <div class="logo-placeholder">
                    SCHOOL<br>LOGO
                </div>
            @endif
        </div>

        <div class="school-info">
            <div class="school-name">{{ strtoupper($school->school_name) }}</div>
            <div class="school-address">{{ ucwords(strtolower($school->postal_address)) }}, {{ ucwords(strtolower($school->postal_name)) }}</div>
            <div class="school-address">{{ ucwords(strtolower($school->country)) }}</div>
        </div>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        <h1>BILLS REPORT</h1>
        <div class="report-period">
            Reporting Period: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
        </div>
    </div>

    <!-- Report Summary -->
    <div class="report-summary">
        <strong>Report Overview:</strong>
        Total Bills: {{ count($bills) }} |
        Total Billed: {{ number_format($total_billed) }} |
        Total Paid: {{ number_format($total_paid) }} |
        Total Balance: {{ number_format($total_balance) }}
    </div>

    <!-- Bills Table -->
    <table>
        <thead>
            <tr>
                <th class="col-number">#</th>
                <th class="col-control">Control #</th>
                <th class="col-admission">Admission #</th>
                <th class="col-student">Student Name</th>
                <th class="col-level">Level</th>
                <th class="col-year">Year</th>
                <th class="col-service">Service</th>
                <th class="col-billed text-right">Billed Amount</th>
                <th class="col-paid text-right">Paid Amount</th>
                <th class="col-balance text-right">Balance</th>
                <th class="col-status">Status</th>
                <th class="col-issued">Issued At</th>
                <th class="col-expires">Expires At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bills as $bill)
                <tr class="avoid-break">
                    <td class="text-center col-number">{{ $loop->iteration }}</td>
                    <td class="col-control">{{ strtoupper($bill['control_number']) }}</td>
                    <td class="col-admission">{{strtoupper($bill['admission'])}}</td>
                    <td class="col-student">{{ $bill['student_name'] }}</td>
                    <td class="col-level">{{ $bill['level'] }}</td>
                    <td class="col-year">{{ $bill['academic_year'] }}</td>
                    <td class="col-service">{{ $bill['service_name'] }}</td>
                    <td class="amount col-billed">{{ number_format($bill['billed_amount']) }}</td>
                    <td class="amount col-paid">{{ number_format($bill['paid_amount']) }}</td>
                    <td class="amount col-balance">{{ number_format($bill['balance']) }}</td>
                    <td class="col-status">
                        @if ($bill['status'] == 'active')
                            <span class="status-active">{{ strtoupper($bill['status']) }}</span>
                        @elseif ($bill['status'] == 'cancelled')
                            <span class="status-cancelled">{{ strtoupper($bill['status']) }}</span>
                        @elseif ($bill['status'] == 'expired')
                            <span class="status-expired">{{ strtoupper($bill['status']) }}</span>
                        @elseif ($bill['status'] == 'full paid')
                            <span class="status-fullpaid">{{ strtoupper($bill['status']) }}</span>
                        @else
                            <span class="status-overpaid">{{ strtoupper($bill['status']) }}</span>
                        @endif
                    </td>
                    <td class="col-issued">{{ \Carbon\Carbon::parse($bill['issued_at'])->format('d-m-Y') }}</td>
                    <td class="col-expires">
                        @if ($bill['expires_at'])
                            {{ \Carbon\Carbon::parse($bill['expires_at'])->format('d-m-Y') }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @endforeach

            <!-- Summary Row -->
            <tr class="summary-row">
                <td colspan="7" class="text-right text-bold">GRAND TOTALS:</td>
                <td class="amount text-bold">{{ number_format($total_billed) }}</td>
                <td class="amount text-bold">{{ number_format($total_paid) }}</td>
                <td class="amount text-bold">{{ number_format($total_balance) }}</td>
                <td colspan="3" class="text-center">End of Report</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="report-footer">
        <div class="footer-content">
            <strong>&copy;{{ ucwords(strtolower($school->school_name)) }}</strong> |
            Generated on {{ \Carbon\Carbon::now()->format('F d, Y \\a\\t H:i:s') }}
        </div>
    </div>
</body>
</html>
