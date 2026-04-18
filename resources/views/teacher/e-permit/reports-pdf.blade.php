{{-- resources/views/teacher/e-permit/reports-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Permit Reports - {{ now()->format('d/m/Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .header h1 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            color: #64748b;
            font-size: 10px;
        }

        .stats-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .stats-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
        }

        .stats-table .stat-label {
            font-weight: bold;
            background: #f8fafc;
            width: 30%;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th {
            background: #667eea;
            color: white;
            padding: 10px;
            font-size: 11px;
            text-align: left;
            border: 1px solid #5a67d8;
        }

        .data-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }

        .data-table tr:nth-child(even) {
            background: #f8fafc;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }

        .badge-approved {
            background: #22c55e;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        .badge-rejected {
            background: #ef4444;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        .badge-completed {
            background: #3b82f6;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        .badge-pending {
            background: #f59e0b;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        @page {
            size: landscape;
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>e-Permit Management Report</h1>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
        @if($filters['date_from'] || $filters['date_to'] || $filters['status'] != 'all')
            <p>
                Filters:
                @if($filters['date_from']) From: {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }} @endif
                @if($filters['date_to']) To: {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }} @endif
                @if($filters['status'] != 'all') Status: {{ ucfirst($filters['status']) }} @endif
            </p>
        @endif
    </div>

    <!-- Statistics -->
    <table class="stats-table">
        <tr>
            <td class="stat-label">Total Permits</td>
            <td><strong>{{ $stats['total'] }}</strong></td>
            <td class="stat-label">Approved</td>
            <td><strong style="color: #22c55e;">{{ $stats['approved'] }}</strong></td>
        </tr>
        <tr>
            <td class="stat-label">Pending</td>
            <td><strong style="color: #f59e0b;">{{ $stats['pending'] }}</strong></td>
            <td class="stat-label">Rejected</td>
            <td><strong style="color: #ef4444;">{{ $stats['rejected'] }}</strong></td>
        </tr>
        <tr>
            <td class="stat-label">Completed</td>
            <td><strong style="color: #3b82f6;">{{ $stats['completed'] }}</strong></td>
            <td class="stat-label">Success Rate</td>
            <td>
                @php
                    $successRate = $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0;
                @endphp
                <strong>{{ $successRate }}%</strong>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Permit Number</th>
                <th>Student Name</th>
                <th>Student ID</th>
                <th>Class</th>
                <th>Guardian Name</th>
                <th>Departure Date</th>
                <th>Expected Return</th>
                <th>Status</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permits as $index => $permit)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $permit->permit_number }}</td>
                <td>{{ ucwords(strtolower($permit->student->first_name . ' ' . $permit->student->last_name)) }}</td>
                <td>{{ strtoupper($permit->student->admission_number) }}</td>
                <td>{{ strtoupper($permit->student->class->class_name ?? 'N/A') }}</td>
                <td>{{ ucwords(strtolower($permit->guardian_name)) }}</td>
                <td>{{ $permit->departure_date->format('d/m/Y') }}</td>
                <td>{{ $permit->expected_return_date->format('d/m/Y') }}</td>
                <td>
                    @php
                        $badgeClass = match($permit->status) {
                            'approved' => 'badge-approved',
                            'rejected' => 'badge-rejected',
                            'completed' => 'badge-completed',
                            default => 'badge-pending'
                        };
                        $statusText = match($permit->status) {
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            'completed' => 'Completed',
                            default => ucfirst($permit->status)
                        };
                    @endphp
                    <span class="{{ $badgeClass }}">{{ $statusText }}</span>
                </td>
                <td>{{ $permit->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated by ShuleApp e-Permit System Module.</p>
    </div>
</body>
</html>
