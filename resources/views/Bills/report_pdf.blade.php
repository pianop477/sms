<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bills Report - {{ $school->school_name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0.6cm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 8.5px;
            color: #2c3e50;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

        /* Original Color Identity Header */
        .header {
            border-bottom: 3px solid #3498db; /* Your Original Blue */
            padding-bottom: 12px;
            margin-bottom: 15px;
            width: 100%;
        }

        .logo-container {
            width: 80px;
            vertical-align: middle;
        }

        .logo {
            max-width: 75px;
            max-height: 65px;
        }

        .school-info {
            text-align: center;
            vertical-align: middle;
        }

        .school-name {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
        }

        /* High-Visibility Summary Box */
        .summary-box {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .summary-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .metric-title {
            font-size: 7px;
            text-transform: uppercase;
            color: #7f8c8d;
            font-weight: bold;
        }

        .metric-value {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
        }

        /* 14-Column Grid Control */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .data-table th {
            background: #34495e !important; /* Your Original Header Color */
            color: white !important;
            border: 1px solid #2c3e50;
            padding: 6px 2px;
            font-size: 7px;
            text-transform: uppercase;
            text-align: left;
        }

        .data-table td {
            border: 1px solid #bdc3c7;
            padding: 4px 2px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .data-table tr:nth-child(even) {
            background-color: #f2f7fb;
        }

        /* Precise Column Ratios (Total 100%) */
        .c-idx  { width: 2.5%; text-align: center; }
        .c-ctrl { width: 8.5%; font-family: 'monospace'; }
        .c-adm  { width: 7.5%; }
        .c-name { width: 12%; }
        .c-lvl  { width: 5%; text-align: center; }
        .c-yr   { width: 4.5%; text-align: center; }
        .c-svc  { width: 9%; }
        .c-amt  { width: 8.5%; text-align: right; font-weight: 600; }
        .c-stat { width: 7.5%; text-align: center; }
        .c-note { width: 9%; font-size: 7px; color: #7f8c8d; }
        .c-date { width: 6.5%; text-align: center; }

        /* Original Status Badge Logic */
        .status-badge {
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: 700;
            font-size: 7px;
            display: inline-block;
        }
        .fullpaid { color: #27ae60; background: #d5f4e6; }
        .expired  { color: #e74c3c; background: #fdeaea; }
        .active   { color: #2980b9; background: #e8f4fd; }
        .cancelled{ color: #f39c12; background: #fef5e7; }

        .total-row {
            background: #34495e !important;
            color: white !important;
            font-weight: bold;
        }

        footer {
            position: fixed;
            bottom: -15px;
            width: 100%;
            text-align: center;
            font-size: 7.5px;
            color: #7f8c8d;
            border-top: 1px solid #bdc3c7;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table width="100%" style="border:none;">
            <tr>
                <td class="logo-container" style="border:none;">
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
                        <img src="{{ $logoBase64 }}" class="logo">
                    @else
                        <div style="width:70px; height:50px; background:#ecf0f1; border:1px solid #bdc3c7; text-align:center; line-height:25px; font-size:8px;">LOGO</div>
                    @endif
                </td>
                <td class="school-info" style="border:none;">
                    <div class="school-name">{{ $school->school_name }}</div>
                    <div style="font-size: 10px; font-weight: bold; margin-top: 2px;">BILLS REGISTRY REPORT</div>
                    <div style="font-size: 8px; color: #7f8c8d;">{{ $school->postal_address }}, {{ $school->postal_name }}</div>
                </td>
                <td width="80px" style="border:none; text-align:right; font-size:7px;">
                    PERIOD:<br>
                    <strong>{{ date('d/m/Y', strtotime($start_date)) }}</strong><br>
                    TO<br>
                    <strong>{{ date('d/m/Y', strtotime($end_date)) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <table class="summary-grid">
            <tr>
                <td><span class="metric-title">Total Bills</span><br><span class="metric-value">{{ count($bills) }}</span></td>
                <td><span class="metric-title">Billed Amount</span><br><span class="metric-value">{{ number_format($total_billed, 2) }}</span></td>
                <td><span class="metric-title">Paid Amount</span><br><span class="metric-value" style="color:#27ae60;">{{ number_format($total_paid, 2) }}</span></td>
                <td style="text-align:right;"><span class="metric-title">Outstanding Balance</span><br><span class="metric-value" style="color:#e74c3c;">{{ number_format($total_balance, 2) }}</span></td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="c-idx">#</th>
                <th class="c-ctrl">Control #</th>
                <th class="c-adm">Adm #</th>
                <th class="c-name">Student Name</th>
                <th class="c-lvl">Level</th>
                <th class="c-yr">Year</th>
                <th class="c-svc">Service</th>
                <th class="c-amt">Billed</th>
                <th class="c-amt">Paid</th>
                <th class="c-amt">Balance</th>
                <th class="c-stat">Status</th>
                <th class="c-note">Description</th>
                <th class="c-date">Issued</th>
                <th class="c-date">Expires</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bills as $bill)
                <tr>
                    <td class="c-idx">{{ $loop->iteration }}</td>
                    <td class="c-ctrl">{{ strtoupper($bill['control_number']) }}</td>
                    <td class="c-adm">{{ strtoupper($bill['admission']) }}</td>
                    <td class="c-name">{{ ucwords(strtolower($bill['student_name'])) }}</td>
                    <td class="c-lvl">{{ $bill['level'] }}</td>
                    <td class="c-yr">{{ $bill['academic_year'] }}</td>
                    <td class="c-svc">{{ $bill['service_name'] }}</td>
                    <td class="c-amt">{{ number_format($bill['billed_amount'], 0) }}</td>
                    <td class="c-amt">{{ number_format($bill['paid_amount'], 0) }}</td>
                    <td class="c-amt">{{ number_format($bill['balance'], 0) }}</td>
                    <td class="c-stat">
                        @php $s = str_replace(' ', '', strtolower($bill['status'])); @endphp
                        <span class="status-badge {{ $s }}">{{ strtoupper($bill['status']) }}</span>
                    </td>
                    <td class="c-note">{{ $bill['description'] }}</td>
                    <td class="c-date">{{ date('d/m/y', strtotime($bill['issued_at'])) }}</td>
                    <td class="c-date">{{ $bill['expires_at'] ? date('d/m/y', strtotime($bill['expires_at'])) : 'N/A' }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="7" style="text-align:right; border-right:none;">AGGREGATE TOTALS</td>
                <td class="c-amt" style="border-left:none;">{{ number_format($total_billed, 0) }}</td>
                <td class="c-amt">{{ number_format($total_paid, 0) }}</td>
                <td class="c-amt">{{ number_format($total_balance, 0) }}</td>
                <td colspan="4" style="text-align:center;">REPORT FINALIZED</td>
            </tr>
        </tbody>
    </table>

    <footer>
        <strong>&copy; {{ $school->school_name }}</strong> |
        Generated: {{ date('d F, Y H:i') }}
        {{-- Page <span style="font-weight:bold;">{PAGENO}</span> --}}
    </footer>

</body>
</html>
