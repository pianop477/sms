{{-- resources/views/payroll/verification-failed.blade.php --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Failed - ShuleApp</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }

        .failed-header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            padding: 30px;
            text-align: center;
        }

        .failed-icon {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .failed-icon svg {
            width: 40px;
            height: 40px;
        }

        .failed-header h1 {
            color: white;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .content {
            padding: 30px;
            text-align: center;
        }

        .error-message {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .error-message p {
            color: #991b1b;
            font-size: 14px;
        }

        .footer {
            background: #f8fafc;
            padding: 15px 25px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

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
        <div class="failed-header">
            <div class="failed-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h1>✗ VERIFICATION FAILED</h1>
            <p>The salary slip could not be verified</p>
        </div>

        <div class="content">
            <div class="error-message">
                <p>{{ $message ?? 'Invalid or expired verification token' }}</p>
            </div>

            {{-- Link to scanner page --}}
            <div class="mt-4">
                <a href="{{ route('scan.qr') }}" class="btn btn-primary">
                    <i class="fas fa-qrcode"></i> Scan QR Code Again
                </a>
            </div>
        </div>

        <div class="footer">
            <p>If you believe this is an error, please contact the finance department</p>
            <a href="{{ url('/') }}" class="button">Back to Home</a>
        </div>
    </div>
</body>

</html>
