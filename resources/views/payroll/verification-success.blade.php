<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Success | ShuleApp</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --secondary: #64748b;
            --success: #059669;
            --accent: #3b82f6;
            --bg-body: #f1f5f9;
            --card-bg: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            display: flex;
            justify-content: center;
            padding: 30px 15px;
            color: var(--primary);
            line-height: 1.5;
        }

        .verification-wrapper {
            max-width: 650px;
            width: 100%;
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            position: relative;
        }

        .header-status {
            background: #f8fafc;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
        }

        .success-icon {
            width: 60px;
            height: 60px;
            background: #dcfce7;
            color: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 13px;
        }

        .success-icon svg {
            width: 34px;
            height: 34px;
        }

        h1 {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .badge {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .content {
            padding: 16px;
        }

        .section-header {
            font-size: 11px;
            font-weight: 700;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-header::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #f1f5f9;
        }

        .emp-info-box {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .info-group label {
            display: block;
            font-size: 10px;
            color: var(--secondary);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-group p {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary);
        }

        .financial-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .financial-table tr td {
            padding: 10px 0;
            border-bottom: 1px solid #f8fafc;
            font-size: 13.5px;
        }

        .label-text {
            color: var(--secondary);
            font-weight: 500;
        }

        .value-text {
            text-align: right;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
        }

        .deduction-label {
            color: #94a3b8;
            padding-left: 10px !important;
        }

        .deduction-value {
            color: #e11d48;
            text-align: right;
            font-weight: 500;
            font-size: 13px;
        }

        .sub-total-row td {
            border-top: 1px solid #e2e8f0;
            padding-top: 15px !important;
            font-weight: 700 !important;
        }

        .total-box {
            background: var(--primary);
            color: white;
            padding: 13px 17px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }

        .total-box span {
            font-weight: 700;
            font-size: 16px;
        }

        .security-meta {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed #e2e8f0;
            font-size: 12px;
            color: var(--secondary);
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .doc-hash {
            font-family: monospace;
            background: #f1f5f9;
            padding: 8px;
            border-radius: 4px;
            font-size: 10px;
            word-break: break-all;
            margin-top: 10px;
            text-align: center;
        }

        .actions {
            margin-top: 22px;
            text-align: center;
        }

        .btn-print {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: 0.2s;
        }

        .btn-print:hover {
            background: #2563eb;
        }

        @media (max-width: 480px) {
            body {
                padding: 8px;
            }

            .emp-info-box {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            h1 {
                font-size: 16px;
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
                color: black;
            }

            .actions {
                display: none;
            }

            .card {
                box-shadow: none;
                border: 1px solid #eee;
                width: 100%;
            }

            .verification-wrapper {
                max-width: 100%;
            }

            .total-box {
                color: black;
            }
            .success-icon {
                color: black;
            }
        }
    </style>
</head>

<body>

    <div class="verification-wrapper">
        <div class="card">
            <div class="header-status">
                <div class="success-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1>Verification Successful</h1>
                <div class="badge">Salary Slip Certified</div>
            </div>

            <div class="content">
                <div class="section-header">Staff Particulars</div>
                <div class="emp-info-box">
                    <div class="info-group">
                        <label>Employee Name</label>
                        <p>{{ strtoupper($data['employee_name']) }}</p>
                    </div>
                    <div class="info-group">
                        <label>Staff ID</label>
                        <p>{{ strtoupper($data['staff_id']) }}</p>
                    </div>
                    <div class="info-group">
                        <label>Type / Department</label>
                        <p>{{ ucfirst($data['staff_type'])}} - {{ucfirst($data['department']) }}</p>
                    </div>
                    <div class="info-group">
                        <label>Payroll Period</label>
                        <p>{{ $data['month'] }}</p>
                    </div>
                </div>

                <div class="section-header">Payment Summary (TZS)</div>
                <table class="financial-table">
                    <tr>
                        <td class="label-text">Basic Salary</td>
                        <td class="value-text">{{ number_format($data['basic_salary'] ?? 0, 0) }}</td>
                    </tr>
                    @if (($data['total_allowances'] ?? 0) > 0)
                        <tr>
                            <td class="label-text">Allowances</td>
                            <td class="value-text">{{ number_format($data['total_allowances'], 0) }}</td>
                        </tr>
                    @endif
                    <tr style="background: #fcfcfc;">
                        <td class="label-text" style="font-weight: 700;">Gross Salary</td>
                        <td class="value-text" style="font-weight: 700;">{{ number_format($data['gross_salary'], 0) }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2"
                            style="padding-top: 15px; font-size: 11px; font-weight: 700; color: #e11d48; text-transform: uppercase;">
                            Statutory Deductions</td>
                    </tr>
                    <tr>
                        <td class="label-text deduction-label">NSSF Contribution</td>
                        <td class="value-text deduction-value">{{ number_format($data['nssf'], 0) }}</td>
                    </tr>
                    <tr>
                        <td class="label-text deduction-label">PAYE Tax</td>
                        <td class="value-text deduction-value">{{ number_format($data['paye'], 0) }}</td>
                    </tr>

                    @if ($data['heslb'] > 0)
                        <tr>
                            <td class="label-text deduction-label">HESLB Loan</td>
                            <td class="value-text deduction-value">{{ number_format($data['heslb'], 0) }}</td>
                        </tr>
                    @endif

                    @if (($data['other_deductions'] ?? 0) > 0)
                        <tr>
                            <td class="label-text deduction-label">Other Deductions</td>
                            <td class="value-text deduction-value">{{ number_format($data['other_deductions'], 0) }}
                            </td>
                        </tr>
                    @endif

                    <tr class="sub-total-row">
                        <td class="label-text" style="color: #e11d48;">Total Deductions</td>
                        <td class="value-text" style="color: #e11d48;">
                            {{ number_format(($data['nssf'] ?? 0) + ($data['paye'] ?? 0) + ($data['heslb'] ?? 0) + ($data['other_deductions'] ?? 0), 0) }}
                        </td>
                    </tr>
                </table>

                <div class="total-box">
                    <small style="font-size: 11px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Take
                        Home (Net Pay)</small>
                    <span>{{ number_format($data['net_salary'], 0) }}</span>
                </div>

                <div class="security-meta">
                    <div class="meta-row">
                        <span>Verified On:</span>
                        <strong>{{ $data['verification_time'] }}</strong>
                    </div>
                    <div class="meta-row">
                        <span>Slip Reference:</span>
                        <strong>#{{ $data['slip_number'] }}</strong>
                    </div>
                </div>

                <div class="actions">
                    <button class="btn-print" onclick="window.print()">
                        Download
                    </button>
                </div>
            </div>
        </div>

        <div
            style="text-align: center; margin-top: 20px; font-size: 10px; color: #94a3b8; letter-spacing: 1px;">
           &copy;{{date('Y')}} Generated by ShuleApp Payroll Engine
        </div>
    </div>

</body>

</html>
