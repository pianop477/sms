{{-- resources/views/emails/service_expiry_reminder.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Service Expiry Reminder</title>
    <style>
        /* Email Reset Styles - Inline CSS ni bora kwa email */
        .ExternalClass, .ReadMsgBody {
            width: 100%;
            background-color: #f4f4f7;
        }

        body, table, td, p, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545e;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7; font-family: 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; font-size: 16px; line-height: 1.6; color: #51545e;">

    <!-- Main Container -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f7; width: 100%;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <!-- Email Container (max-width: 600px) -->
                <table width="100%" max-width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.08); overflow: hidden;">

                    <!-- Header with Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%); padding: 40px 30px; text-align: center;">
                            <img src="{{ asset('assets/images/logo-white.png') }}" alt="{{ config('app.name') }}" style="max-width: 150px; height: auto; margin-bottom: 20px;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">Service Expiry Reminder</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 15px 0 0 0; font-size: 16px;">Your subscription requires attention</p>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px 30px;">

                            <!-- Hello Message -->
                            <h2 style="color: #2c3e50; font-size: 24px; font-weight: 600; margin: 0 0 20px 0;">
                                Hello, <span style="color: #4e73df;">{{ ucwords(strtolower($admin->first_name)) }}</span>
                            </h2>

                            <!-- Alert Message -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: {{ $daysLeft <= 7 ? '#fee9e7' : ($daysLeft <= 30 ? '#fff3d6' : '#e3f7e9') }}; border-radius: 16px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="50" valign="top" style="padding-right: 15px;">
                                                    @if($daysLeft <= 7)
                                                        <img src="https://img.icons8.com/color/48/000000/high-priority.png" width="48" height="48" style="display: block;">
                                                    @elseif($daysLeft <= 30)
                                                        <img src="https://img.icons8.com/color/48/000000/warning-shield.png" width="48" height="48" style="display: block;">
                                                    @else
                                                        <img src="https://img.icons8.com/color/48/000000/checkmark.png" width="48" height="48" style="display: block;">
                                                    @endif
                                                </td>
                                                <td valign="middle">
                                                    <p style="margin: 0; font-size: 18px; font-weight: 600; color: {{ $daysLeft <= 7 ? '#e74a3b' : ($daysLeft <= 30 ? '#f6c23e' : '#1cc88a') }};">
                                                        <strong>{{ $daysLeft }} days remaining</strong>
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #5a5c69;">
                                                        @if($daysLeft <= 7)
                                                            ⚠️ Your service will expire very soon! Renew immediately to avoid interruption.
                                                        @elseif($daysLeft <= 30)
                                                            ⏰ Your service is expiring soon. Please plan for renewal.
                                                        @else
                                                            ✅ Your service is in good standing.
                                                        @endif
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Service Details Card -->
                            <h3 style="color: #2c3e50; font-size: 18px; font-weight: 600; margin: 30px 0 15px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                                <span style="border-bottom: 3px solid #4e73df; padding-bottom: 5px;">Service Details</span>
                            </h3>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8f9fc; border-radius: 16px;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <!-- School Name -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 15px;">
                                            <tr>
                                                <td width="120" style="padding: 8px 0; color: #858796; font-weight: 500;">School Name:</td>
                                                <td style="padding: 8px 0; color: #2c3e50; font-weight: 600;">{{ $school->school_name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #858796;">Registration No:</td>
                                                <td style="padding: 8px 0; color: #2c3e50; font-weight: 600;">{{ $school->school_reg_no }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #858796;">Start Date:</td>
                                                <td style="padding: 8px 0; color: #2c3e50;">
                                                    <strong>{{ \Carbon\Carbon::parse($school->service_start_date)->format('d F Y') }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #858796;">End Date:</td>
                                                <td style="padding: 8px 0; color: #2c3e50;">
                                                    <strong style="color: {{ $daysLeft <= 7 ? '#e74a3b' : ($daysLeft <= 30 ? '#f6c23e' : '#1cc88a') }};">
                                                        {{ \Carbon\Carbon::parse($school->service_end_date)->format('d F Y') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #858796;">Duration:</td>
                                                <td style="padding: 8px 0; color: #2c3e50;">{{ $school->service_duration }} year(s)</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #858796;">Status:</td>
                                                <td style="padding: 8px 0;">
                                                    <span style="background-color: {{ $daysLeft <= 7 ? '#e74a3b' : ($daysLeft <= 30 ? '#f6c23e' : '#1cc88a') }}; color: #ffffff; padding: 5px 15px; border-radius: 30px; font-size: 14px; font-weight: 600; display: inline-block;">
                                                        @if($daysLeft <= 7)
                                                            Critical
                                                        @elseif($daysLeft <= 30)
                                                            Expiring Soon
                                                        @else
                                                            Active
                                                        @endif
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Progress Bar -->
                                        @php
                                            $totalDays = \Carbon\Carbon::parse($school->service_start_date)->diffInDays(\Carbon\Carbon::parse($school->service_end_date));
                                            $daysUsed = \Carbon\Carbon::parse($school->service_start_date)->diffInDays(\Carbon\Carbon::now());
                                            $progressPercentage = min(100, ($daysUsed / $totalDays) * 100);
                                        @endphp
                                        <div style="margin-top: 20px;">
                                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                                <span style="font-size: 14px; color: #858796;">Service Progress</span>
                                                <span style="font-size: 14px; font-weight: 600; color: #2c3e50;">{{ round($progressPercentage) }}% Used</span>
                                            </div>
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #e3e6f0; border-radius: 10px; height: 8px;">
                                                <tr>
                                                    <td width="{{ $progressPercentage }}%" style="background: linear-gradient(90deg, #4e73df, #6f42c1); border-radius: 10px; height: 8px;"></td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Countdown Timer (Static for email) -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <table cellpadding="0" cellspacing="0" border="0" style="background-color: #f8f9fc; border-radius: 16px; padding: 20px;">
                                            <tr>
                                                <td align="center" style="padding-bottom: 15px;">
                                                    <span style="color: #858796; font-size: 16px;">Time Remaining</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            @php
                                                                $days = floor($daysLeft);
                                                                $hours = floor((\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($school->service_end_date)) % 24));
                                                                $minutes = floor((\Carbon\Carbon::now()->diffInMinutes(\Carbon\Carbon::parse($school->service_end_date)) % 60));
                                                                $seconds = floor((\Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($school->service_end_date)) % 60));
                                                            @endphp

                                                            <!-- Days -->
                                                            <td style="padding: 0 10px; text-align: center;">
                                                                <div style="background: linear-gradient(135deg, #4e73df, #6f42c1); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin-bottom: 5px;">
                                                                    {{ str_pad($days, 2, '0', STR_PAD_LEFT) }}
                                                                </div>
                                                                <span style="color: #858796; font-size: 12px; text-transform: uppercase;">Days</span>
                                                            </td>

                                                            <!-- Hours -->
                                                            <td style="padding: 0 10px; text-align: center;">
                                                                <div style="background: linear-gradient(135deg, #36b9cc, #1cc88a); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin-bottom: 5px;">
                                                                    {{ str_pad($hours, 2, '0', STR_PAD_LEFT) }}
                                                                </div>
                                                                <span style="color: #858796; font-size: 12px; text-transform: uppercase;">Hours</span>
                                                            </td>

                                                            <!-- Minutes -->
                                                            <td style="padding: 0 10px; text-align: center;">
                                                                <div style="background: linear-gradient(135deg, #f6c23e, #f4a100); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin-bottom: 5px;">
                                                                    {{ str_pad($minutes, 2, '0', STR_PAD_LEFT) }}
                                                                </div>
                                                                <span style="color: #858796; font-size: 12px; text-transform: uppercase;">Mins</span>
                                                            </td>

                                                            <!-- Seconds -->
                                                            <td style="padding: 0 10px; text-align: center;">
                                                                <div style="background: linear-gradient(135deg, #e74a3b, #c92100); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin-bottom: 5px;">
                                                                    {{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}
                                                                </div>
                                                                <span style="color: #858796; font-size: 12px; text-transform: uppercase;">Secs</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="border-radius: 50px; background: linear-gradient(135deg, #4e73df, #6f42c1);" bgcolor="#4e73df">
                                                    <a href="{{ route('renew.service') }}" style="display: inline-block; padding: 15px 40px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 18px; border-radius: 50px;">Renew Service Now</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Additional Info -->
                            <p style="margin: 30px 0 0 0; color: #858796; font-size: 14px; text-align: center;">
                                <img src="https://img.icons8.com/ios/50/4e73df/help.png" width="16" height="16" style="vertical-align: middle; margin-right: 5px;">
                                Need help? <a href="{{ route('support') }}" style="color: #4e73df; text-decoration: underline;">Contact Support</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fc; padding: 30px; text-align: center; border-top: 1px solid #e3e6f0;">
                            <p style="margin: 0 0 15px 0; color: #858796; font-size: 14px;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                            <p style="margin: 0; color: #858796; font-size: 12px;">
                                This is an automated message, please do not reply to this email.<br>
                                If you have any questions, please contact our support team.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
