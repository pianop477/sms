<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results Expiry Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f7f9fc;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .email-header {
            background: linear-gradient(135deg, #4a6fa5, #2c4d7a);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .message {
            margin-bottom: 25px;
            font-size: 16px;
            color: #34495e;
        }
        .urgent-note {
            background-color: #fff8e6;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            border-radius: 0 4px 4px 0;
        }
        .cta-button {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 14px 0;
            background: linear-gradient(135deg, #4a6fa5, #2c4d7a);
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }
        .cta-button:hover {
            background: linear-gradient(135deg, #3d5d90, #223a5e);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
        }
        .email-footer {
            background-color: #f1f5f9;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #7a8a9e;
        }
        .logo {
            font-weight: bold;
            color: #2c4d7a;
            font-size: 18px;
        }
        .warning-icon {
            color: #e74c3c;
            font-weight: bold;
            font-size: 20px;
            margin-right: 5px;
            vertical-align: middle;
        }
        .countdown {
            font-weight: 600;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Results Expiry Notification</h1>
        </div>

        <div class="email-body">
            <p class="greeting">Dear Teacher,</p>

            <p class="message">We would like to notify you that you have <strong>unsubmitted results</strong> which will expire in <span class="countdown">6 hours</span>.</p>

            <div class="urgent-note">
                <span class="warning-icon">⚠️</span>
                <strong>Urgent Action Required:</strong>
                Kindly review the results or take necessary action before they are automatically deleted from the system.
            </div>

            <p class="message">To avoid losing your work, please submit your results as soon as possible.</p>

            {{-- <a href="#" class="cta-button">Submit Results Now</a> --}}
        </div>

        <div class="email-footer">
            <p>Regards,<br><span class="logo">ShuleApp - Admin</span></p>
            <p>If you need assistance, please contact our support team.</p>
            <p>© {{date('Y')}} ShuleApp. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
