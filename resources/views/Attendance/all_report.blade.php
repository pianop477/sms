<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report - {{ $school->school_name ?? 'School' }}</title>
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            background: white;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Page Settings */
        @page {
            size: A4 landscape;
            margin: 1.2cm 0.8cm 1.2cm 0.8cm;

            @top-center {
                content: "Attendance Report";
                font-size: 8px;
                color: #64748b;
                margin-bottom: 5px;
            }

            @bottom-center {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 8px;
                color: #64748b;
                margin-top: 5px;
            }
        }

        /* Header Section */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #2563eb;
            position: relative;
        }

        .school-name {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .report-title {
            font-size: 14px;
            font-weight: 600;
            color: #2563eb;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .report-period {
            font-size: 11px;
            color: #475569;
            font-weight: 500;
        }

        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #e2e8f0;
        }

        .info-section {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-row {
            display: flex;
            align-items: baseline;
            font-size: 11px;
        }

        .info-label {
            font-weight: 600;
            color: #475569;
            width: 100px;
        }

        .info-value {
            font-weight: 500;
            color: #1e293b;
        }

        .stats-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-left: 2px solid #e2e8f0;
            padding-left: 15px;
        }

        .stats-title {
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stats-percentage {
            font-size: 28px;
            font-weight: 700;
            color: #2563eb;
            line-height: 1.2;
        }

        .stats-percentage small {
            font-size: 12px;
            font-weight: 400;
            color: #64748b;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            border-radius: 4px;
        }

        /* Month Section */
        .month-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .month-header {
            background: linear-gradient(90deg, #f1f5f9, #ffffff);
            padding: 10px 12px;
            border-left: 4px solid #2563eb;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 12px;
            color: #1e293b;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 0 4px 4px 0;
        }

        .month-badge {
            background: #2563eb;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 500;
        }

        /* Table Styles */
        .table-wrapper {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .attendance-table th {
            background: #1e293b;
            color: white;
            font-weight: 600;
            padding: 8px 4px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            font-size: 8px;
        }

        .attendance-table td {
            padding: 6px 4px;
            border: 1px solid #e2e8f0;
            text-align: center;
            vertical-align: middle;
        }

        .attendance-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .attendance-table tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* Column Widths */
        .col-number {
            width: 30px;
            font-weight: 500;
            color: #64748b;
        }

        .col-name {
            text-align: left !important;
            padding-left: 8px !important;
            font-weight: 500;
            min-width: 150px;
        }

        .col-gender {
            width: 35px;
            font-weight: 600;
        }

        .col-stream {
            width: 35px;
            font-weight: 600;
        }

        .col-date {
            width: 30px;
            font-weight: 500;
        }

        /* Attendance Status Colors */
        .status-present {
            color: #059669;
            font-weight: 700;
            background: #ecfdf5;
            border-radius: 4px;
            padding: 2px 0;
        }

        .status-absent {
            color: #dc2626;
            font-weight: 700;
            background: #fef2f2;
            border-radius: 4px;
            padding: 2px 0;
        }

        .status-permission {
            color: #2563eb;
            font-weight: 700;
            background: #eff6ff;
            border-radius: 4px;
            padding: 2px 0;
        }

        /* Legend */
        .legend-section {
            display: flex;
            gap: 20px;
            padding: 10px 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-top: 15px;
            font-size: 9px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        .dot-present {
            background: #059669;
        }

        .dot-absent {
            background: #dc2626;
        }

        .dot-permission {
            background: #2563eb;
        }

        /* Footer */
        .report-footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5e1;
            font-size: 8px;
            color: #64748b;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left {
            display: flex;
            gap: 20px;
        }

        .footer-right {
            text-align: right;
        }

        /* Utility Classes */
        .text-uppercase {
            text-transform: uppercase;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .font-bold {
            font-weight: 700;
        }

        .text-primary {
            color: #2563eb;
        }

        .text-success {
            color: #059669;
        }

        .text-danger {
            color: #dc2626;
        }

        /* Page Break Control */
        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* Print-specific styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .attendance-table th {
                background: #1e293b !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .status-present {
                background: #ecfdf5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .status-absent {
                background: #fef2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .status-permission {
                background: #eff6ff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .progress-fill {
                background: #2563eb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="report-header">
        <div class="school-name">{{ $school->school_name ?? 'School Name' }}</div>
        <div class="report-title">Class Attendance Report</div>
        <div class="report-period">
            {{ Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ Carbon\Carbon::parse($endDate)->format('d F Y') }}
        </div>
    </div>

    @foreach ($datas as $date => $attendances)
        @php
            $firstRecord = $attendances->first();
            $totalStudents = $attendances->count();
            $presentCount = $attendances->where('attendance_status', 'present')->count();
            $attendanceRate = $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100) : 0;
        @endphp

        <div class="month-section no-break">
            <!-- Summary Cards -->
            <div class="summary-grid">
                <div class="info-section">
                    <div class="info-row">
                        <span class="info-label">Class:</span>
                        <span class="info-value text-uppercase">{{ $firstRecord->class_name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date:</span>
                        <span class="info-value">{{ Carbon\Carbon::parse($date)->format('l, d F Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Stream:</span>
                        <span class="info-value">Stream {{ strtoupper($firstRecord->group ?? 'All') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Students:</span>
                        <span class="info-value font-bold">{{ $totalStudents }}</span>
                    </div>
                </div>

                <div class="stats-section">
                    <div class="stats-title">Attendance Rate</div>
                    <div class="stats-percentage">
                        {{ $attendanceRate }}<small>%</small>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $attendanceRate }}%;"></div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="table-wrapper">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th class="col-number">#</th>
                            <th class="col-name">Student Name</th>
                            <th class="col-gender">G</th>
                            <th class="col-stream">S</th>
                            <th class="col-date">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $index => $attendance)
                            @php
                                $statusClass = match($attendance->attendance_status) {
                                    'present' => 'status-present',
                                    'absent' => 'status-absent',
                                    'permission' => 'status-permission',
                                    default => ''
                                };

                                $statusSymbol = match($attendance->attendance_status) {
                                    'present' => 'P',
                                    'absent' => 'A',
                                    'permission' => '*',
                                    default => '?'
                                };
                            @endphp
                            <tr>
                                <td class="col-number">{{ $index + 1 }}</td>
                                <td class="col-name">
                                    {{ ucwords(strtolower($attendance->first_name . ' ' . $attendance->middle_name . ' ' . $attendance->last_name)) }}
                                </td>
                                <td class="col-gender">{{ strtoupper(substr($attendance->gender, 0, 1)) }}</td>
                                <td class="col-stream">{{ strtoupper($attendance->group ?? '-') }}</td>
                                <td class="col-date {{ $statusClass }}">{{ $statusSymbol }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <!-- Legend -->
    <div class="legend-section no-break">
        <div class="legend-item">
            <span class="legend-dot dot-present"></span>
            <span>Present (P)</span>
        </div>
        <div class="legend-item">
            <span class="legend-dot dot-absent"></span>
            <span>Absent (A)</span>
        </div>
        <div class="legend-item">
            <span class="legend-dot dot-permission"></span>
            <span>Permission (*)</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="report-footer">
        <div class="footer-left">
            <span>Generated by: {{ $school->school_name ?? 'School System' }}</span>
            <span>Report ID: ATT-{{ date('Ymd') }}-{{ rand(1000, 9999) }}</span>
        </div>
        <div class="footer-right">
            Printed: {{ now()->format('d M Y H:i:s') }}
        </div>
    </div>
</body>
</html>
