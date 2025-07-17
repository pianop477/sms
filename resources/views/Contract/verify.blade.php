<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contract Verification Receipt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .receipt-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            border-top: 5px solid #2c3e50;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }
        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 5px;
        }
        .status-icon {
            font-size: 48px;
            margin: 15px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80px;
            width: 80px;
            border-radius: 50%;
            margin-left: auto;
            margin-right: auto;
        }
        .icon-active {
            color: #2e7d32;
            background-color: #e8f5e9;
            border: 3px solid #2e7d32;
        }
        .icon-pending {
            color: #ff8f00;
            background-color: #fff8e1;
            border: 3px solid #ff8f00;
        }
        .icon-expired {
            color: #c62828;
            background-color: #ffebee;
            border: 3px solid #c62828;
        }
        .icon-rejected {
            color: #616161;
            background-color: #f5f5f5;
            border: 3px solid #616161;
        }
        .icon-invalid {
            color: #616161;
            background-color: #f5f5f5;
            border: 3px dashed #616161;
        }
        .verification-details {
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            text-align: right;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            display: inline-block;
        }
        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .status-pending {
            background-color: #fff8e1;
            color: #ff8f00;
        }
        .status-expired {
            background-color: #ffebee;
            color: #c62828;
        }
        .status-rejected {
            background-color: #f5f5f5;
            color: #616161;
        }
        .status-invalid {
            background-color: #f5f5f5;
            color: #616161;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            color: #777;
            font-size: 12px;
        }
        .btn-ok {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
        }
        .btn-ok:hover {
            background-color: #1a252f;
        }
        .timestamp {
            text-align: center;
            color: #777;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .divider {
            height: 1px;
            background-color: #eee;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <div class="logo">CONTRACT VERIFICATION</div>

            <!-- Status Icon -->
            @if ($contract->status === 'expired')
                <div class="status-icon icon-expired">
                    <i class="fas fa-calendar-times"></i>
                </div>
            @elseif ($contract->status === 'pending')
                <div class="status-icon icon-pending">
                    <i class="fas fa-clock"></i>
                </div>
            @elseif ($contract->status === 'approved')
                <div class="status-icon icon-active">
                    <i class="fas fa-check-circle"></i>
                </div>
            @elseif ($contract->status === 'rejected')
                <div class="status-icon icon-rejected">
                    <i class="fas fa-times-circle"></i>
                </div>
            @else
                <div class="status-icon icon-invalid">
                    <i class="fas fa-question-circle"></i>
                </div>
            @endif
        </div>

        <div class="timestamp">
            Verified on: {{ now()->format('d M Y, H:i') }}
        </div>

        <div class="divider"></div>

        <div class="verification-details">
            <div class="detail-row">
                <span class="detail-label">Contract Type:</span>
                <span class="detail-value">{{ ucwords(strtolower($contract->contract_type)) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Member ID:</span>
                <span class="detail-value">{{ strtoupper($contract->member_id) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Employee Name:</span>
                <span class="detail-value">{{ ucwords(strtolower($contract->first_name)) }} {{ ucwords(strtolower($contract->last_name)) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Contract Status:</span>
                <span class="detail-value">
                    @if ($contract->status === 'expired')
                        <span class="status-badge status-expired">EXPIRED</span>
                    @elseif ($contract->status === 'pending')
                        <span class="status-badge status-pending">PENDING APPROVAL</span>
                    @elseif ($contract->status === 'approved')
                        <span class="status-badge status-active">ACTIVE</span>
                    @elseif ($contract->status === 'rejected')
                        <span class="status-badge status-rejected">REJECTED</span>
                    @else
                        <span class="status-badge status-invalid">INVALID</span>
                    @endif
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Expire on: </span>
                <span class="detail-value">{{$contract->end_date}}</span>
            </div>
        </div>

        <a href="{{route('welcome')}}" class="btn-ok">
            RETURN TO HOME
        </a>
        <div class="footer">
            &copy; {{ ucwords(strtolower($contract->school_name)) }} – {{ date('Y') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
