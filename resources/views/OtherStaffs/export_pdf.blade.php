<!DOCTYPE html>
<html>
<head>
    <title>Staff Report - {{ $school->school_name }}</title>
    <style>
        /* PDF-optimized professional styling */
        @page {
            margin: 1cm;
            size: A4 portrait;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #2c3e50;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Header with improved layout */
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
            font-size: 10px;
            color: #7f8c8d;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        /* Report Title Section */
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

        /* Report Summary */
        .report-summary {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            border-left: 4px solid #3498db;
            margin-bottom: 12px;
            font-size: 10px;
        }

        /* Enhanced Table Styles - FIXED */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 5px 0;
            font-size: 9px;
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
            font-size: 8px;
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

        /* Enhanced Status styling */
        .status-completed {
            color: #27ae60;
            font-weight: 700;
            background: #d5f4e6;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            display: inline-block;
        }

        .status-pending {
            color: #f39c12;
            font-weight: 700;
            background: #fef5e7;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            display: inline-block;
        }

        .status-failed {
            color: #e74c3c;
            font-weight: 700;
            background: #fdeaea;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            display: inline-block;
        }

        /* Amount styling */
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

        /* Summary row with enhanced styling - FIXED */
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

        /* Professional Footer */
        .report-footer {
            margin-top: 20px;
            position: fixed;
            bottom: 0;
            width: 100%;
            padding-top: 10px;
            border-top: 2px solid #bdc3c7;
            font-size: 8px;
            color: #7f8c8d;
            text-align: center;
        }

        .footer-content {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Page break handling */
        .page-break {
            page-break-after: always;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        /* Column width optimizations */
        .col-number { width: 4%; }
        .col-date { width: 9%; }
        .col-reference { width: 11%; }
        .col-category { width: 11%; }
        .col-description { width: 24%; }
        .col-amount { width: 11%; }
        .col-status { width: 9%; }
        .col-payment { width: 11%; }
    </style>
</head>
<body>
    <!-- Header Section with improved layout -->
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
        <h1>NON-TEACHING STAFF MEMBERS</h1>
    </div>

    <!-- Transactions Table with optimized column widths -->
    <table>
        <thead>
            <tr>
                <th class="col-number">#</th>
                <th class="col-date">Full Name</th>
                <th class="col-reference">Gender</th>
                <th class="col-category">Phone</th>
                <th class="col-description">Email</th>
                <th class="col-payment">Job Title</th>
                <th class="col-status">Dob</th>
                <th class="col-amount text-right">Address</th>
                <th class="col-status">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($combinedStaffs as $t)
                <tr class="avoid-break">
                    <td class="text-center col-number">{{ $loop->iteration }}</td>
                    <td class="col-date">{{$t->job_title == 'driver' ? ucwords(strtolower($t->driver_name)) : ucwords(strtolower($t->first_name. ' '. $t->last_name)) }}</td>
                    <td class="col-reference">{{ strtoupper($t->gender) }}</td>
                    <td class="col-category">{{ ucwords(strtolower($t->phone)) ?? "N/A" }}</td>
                    <td class="col-description">{{ $t->email ?? 'N/A' }}</td>
                    <td class="amount col-amount">{{ ucwords(strtolower($t->job_title)) ?? "N/A"}}</td>
                    <td class="amount col-amount">{{ \Carbon\Carbon::parse($t->date_of_birth)->format('d-m-Y')}}</td>
                    <td class="amount col-amount">{{ucwords(strtolower($t->street_address)) ?? "N/A"}}</td>
                    <td class="col-status">
                        @php
                            if($t->status == 1) {
                                $statusClass = 'status-completed';
                                $statusText = 'Active';
                            } else {
                                $statusClass = 'status-failed';
                                $statusText = 'Inactive';
                            }
                        @endphp
                        <span class="{{ $statusClass }}">{{ucwords(strtolower($statusText))}}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Professional Footer -->
    <div class="report-footer">
        <div class="footer-content">
            <strong>&copy;{{ ucwords(strtolower($school->school_name)) }}</strong> |
            Generated on {{ \Carbon\Carbon::now()->format('F d, Y \\a\\t H:i:s') }}
        </div>
    </div>
</body>
</html>
