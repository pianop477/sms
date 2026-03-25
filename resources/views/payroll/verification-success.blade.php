{{-- resources/views/payroll/verification-success.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Slip Verified - ShuleApp</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', 'DejaVu Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .verification-card {
            max-width: 550px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .success-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 25px;
            text-align: center;
        }
        .success-icon {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .success-icon svg { width: 40px; height: 40px; }
        .success-header h1 {
            color: white;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .success-header p { color: rgba(255,255,255,0.9); font-size: 14px; }
        .content { padding: 25px; }
        .info-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
        }
        .info-value {
            font-size: 14px;
            font-weight: 700;
            color: #1a3e6f;
        }
        .amount { font-size: 20px; font-weight: 800; color: #10b981; }
        .verification-badge {
            background: #e8f0fe;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
        }
        .footer {
            background: #f8fafc;
            padding: 15px 25px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p { font-size: 11px; color: #6c757d; }
        .button {
            display: inline-block;
            background: #1a3e6f;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="verification-card">
        <div class="success-header">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1>✓ VERIFIED</h1>
            <p>This salary slip is authentic and valid</p>
        </div>

        <div class="content">
            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">Slip Number</span>
                    <span class="info-value">{{ $data['slip_number'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Employee Name</span>
                    <span class="info-value">{{ strtoupper($data['employee_name']) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Staff ID</span>
                    <span class="info-value">{{ $data['staff_id'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Staff Type</span>
                    <span class="info-value">{{ ucfirst($data['staff_type']) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payroll Month</span>
                    <span class="info-value">{{ $data['month'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Gross Salary</span>
                    <span class="info-value amount">TZS {{ number_format($data['gross_salary'], 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Net Salary</span>
                    <span class="info-value amount">TZS {{ number_format($data['net_salary'], 2) }}</span>
                </div>
            </div>

            <div class="verification-badge">
                <p>
                    <strong>✓ Verified on {{ $data['verification_time'] }}</strong><br>
                    This document has been verified {{ $data['verification_count'] }} time(s)
                </p>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer-generated verification from ShuleApp Finance System</p>
            <a href="{{ url('/') }}" class="button">Back to Home</a>
        </div>
    </div>
</body>
</html>
