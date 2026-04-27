{{-- resources/views/payroll/verification-failed.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Failed - ShuleApp</title>
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
        .card {
            max-width: 500px;
            background: white;
            border-radius: 20px;
            padding: 35px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .error-icon {
            width: 80px;
            height: 80px;
            background: #ef4444;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .error-icon svg { width: 40px; height: 40px; color: white; }
        h1 { color: #ef4444; font-size: 28px; margin-bottom: 10px; }
        .message-box {
            background: #fef2f2;
            border-radius: 12px;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #ef4444;
        }
        .message-box p { color: #991b1b; font-size: 14px; }
        .token-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            font-size: 11px;
            color: #6c757d;
            word-break: break-all;
        }
        .footer {
            margin-top: 20px;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #e9ecef;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
        <h1>✗ VERIFICATION FAILED</h1>
        <p>The salary slip could not be verified</p>

        <div class="message-box">
            <p>{{ $message ?? 'Invalid or expired verification token' }}</p>
        </div>

        @if($token)
        <div class="token-info">
            <strong>Token:</strong> {{ substr($token, 0, 30) }}...
        </div>
        @endif

        <div class="footer">
            <p>If you believe this is an error, please contact the finance department</p>
            <p>© {{ date('Y') }} ShuleApp. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
