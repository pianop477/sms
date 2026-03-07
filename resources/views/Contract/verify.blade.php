<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Verification | {{ $school->school_name ?? 'School' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        @media print {
            size: A4;
            margin-top: 4mm;
        }

        .verification-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Dynamic Header Colors based on Status */
        .verification-header {
            @php
                $isActive = ($contract->is_active ?? false) && now()->lessThan($contract->end_date);
                $headerGradient = $isActive
                    ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)'
                    : 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)';
                $badgeIcon = $isActive ? 'fa-check-circle' : 'fa-exclamation-triangle';
                $badgeText = $isActive ? 'ACTIVE CONTRACT' : 'EXPIRED CONTRACT';
                $badgeColor = $isActive ? '#10b981' : '#ef4444';
            @endphp
            background: {{ $headerGradient }};
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .verification-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            background: {{ $headerGradient }};
            filter: blur(10px);
            opacity: 0.5;
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.2);
            padding: 10px 25px;
            border-radius: 50px;
            margin-bottom: 20px;
            backdrop-filter: blur(5px);
        }

        .verified-badge i {
            font-size: 24px;
        }

        .verification-header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .verification-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .verification-body {
            padding: 40px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Dynamic Card Border Colors */
        .info-card {
            background: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            border-left: 4px solid {{ $badgeColor }};
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .info-card.contract {
            border-left-color: #3b82f6;
        }

        .info-card.school {
            border-left-color: #8b5cf6;
        }

        .info-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-title i {
            font-size: 20px;
            color: {{ $badgeColor }};
        }

        .info-title h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-size: 0.9rem;
        }

        .info-value {
            color: #0f172a;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Dynamic Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-expired {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Expiry Warning */
        .expiry-warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 12px 15px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .expiry-warning i {
            color: #d97706;
            font-size: 1.2rem;
        }

        .expiry-warning p {
            color: #92400e;
            margin: 0;
            font-size: 0.9rem;
        }

        .verification-footer {
            background: #f1f5f9;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .verification-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            color: #64748b;
            font-size: 0.9rem;
        }

        .verification-meta i {
            margin-right: 5px;
        }

        .btn-print {
            background: white;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-print:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-2px);
        }

        .staff-type-badge {
            background: #dbeafe;
            color: #1e40af;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 8px;
        }

        @media (max-width: 640px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .verification-footer {
                flex-direction: column;
                text-align: center;
            }

            .verification-meta {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    @php
        // Check if contract is active (status = activated AND end_date not passed)
        $isActive = ($contract->status == 'activated' && now()->lessThan($contract->end_date));
        $isExpired = ($contract->status == 'activated' && now()->greaterThan($contract->end_date));
        $contractStatus = $isActive ? 'Active' : ($isExpired ? 'Expired' : ucfirst($contract->status));

        // Dynamic colors based on status
        $statusColor = $isActive ? '#10b981' : ($isExpired ? '#ef4444' : '#6b7280');
        $statusBgColor = $isActive ? '#dcfce7' : ($isExpired ? '#fee2e2' : '#f3f4f6');
        $statusTextColor = $isActive ? '#166534' : ($isExpired ? '#991b1b' : '#374151');
        $badgeIcon = $isActive ? 'fa-check-circle' : ($isExpired ? 'fa-exclamation-triangle' : 'fa-clock');
        $badgeText = $isActive ? 'ACTIVE CONTRACT' : ($isExpired ? 'EXPIRED CONTRACT' : strtoupper($contract->status) . ' CONTRACT');
        $headerGradient = $isActive
            ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)'
            : ($isExpired
                ? 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)'
                : 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)');
    @endphp

    <div class="verification-card">
        <!-- Header with Dynamic Colors -->
        <div class="verification-header" style="background: {{ $headerGradient }};">
            <div class="verified-badge">
                <i class="fas {{ $badgeIcon }}"></i>
                <span>{{ $badgeText }}</span>
            </div>
            <h1>
                <i class="fas fa-file-contract mr-2"></i>
                Contract Verification
            </h1>
            <p>This contract has been digitally verified and is authentic</p>
        </div>

        <!-- Body -->
        <div class="verification-body">
            <div class="info-grid">
                <!-- Staff Information -->
                <div class="info-card" style="border-left-color: {{ $statusColor }};">
                    <div class="info-title">
                        <i class="fas fa-user-tie" style="color: {{ $statusColor }};"></i>
                        <h3>Staff Information</h3>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Full Name:</span>
                        <span class="info-value">
                            {{ ucfirst($applicant['first_name'] ?? 'Unknown') }} {{ ucfirst($applicant['last_name'] ?? '' )}}
                            <span class="staff-type-badge">{{ ucfirst($applicant['staff_type'] ?? 'Staff') }}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Staff ID:</span>
                        <span class="info-value">{{ strtoupper($applicant['staff_id'] ?? $contract->applicant_id) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $applicant['phone'] ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Gender:</span>
                        <span class="info-value">{{ ucfirst($applicant['gender'] ?? 'Not specified') }}</span>
                    </div>
                </div>

                <!-- Contract Information -->
                <div class="info-card contract">
                    <div class="info-title">
                        <i class="fas fa-file-signature" style="color: #3b82f6;"></i>
                        <h3>Contract Information</h3>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Contract Type:</span>
                        <span class="info-value">
                            <span class="badge" style="background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 20px;">
                                {{ ucfirst($contract->contract_type) }}
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Duration:</span>
                        <span class="info-value">{{ $contract->duration ?? 'N/A' }} months</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Start Date:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($contract->start_date)->format('d M Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">End Date:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($contract->end_date)->format('d M Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Issuer</span>
                        <span class="info-value">{{ ucfirst($contract->approved_by ?? 'Not specified') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Activated at:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($contract->activated_at)->format('d M Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            <span class="status-badge" style="background: {{ $statusBgColor }}; color: {{ $statusTextColor }};">
                                <i class="fas fa-circle" style="font-size: 8px; color: {{ $statusColor }};"></i>
                                {{ $contractStatus }}
                            </span>
                        </span>
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="info-card">
                    <div class="info-title">
                        <i class="fas fa-coins" style="color: #d97706;"></i>
                        <h3>Financial Details</h3>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Basic Salary:</span>
                        <span class="info-value">TZS {{ number_format($contract->basic_salary ?? 0) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Allowances:</span>
                        <span class="info-value">TZS {{ number_format($contract->allowances ?? 0) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Net Pay:</span>
                        <span class="info-value"><strong>TZS {{ number_format(($contract->basic_salary ?? 0) + ($contract->allowances ?? 0)) }}</strong></span>
                    </div>
                    <p class="text-center text-sm text-warning mt-3">PAYE Tax not Applicable</p>
                </div>

                <!-- School Information -->
                <div class="info-card school">
                    <div class="info-title">
                        <i class="fas fa-school" style="color: #8b5cf6;"></i>
                        <h3>School Information</h3>
                    </div>
                    <div class="info-row">
                        <span class="info-label">School Name:</span>
                        <span class="info-value">{{ strtoupper($school->school_name ?? 'N/A') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value">{{ strtoupper($school->postal_address ?? 'N/A') }}</span>
                    </div>
                </div>
            </div>

            <!-- Expiry Warning (only show for expired contracts) -->
            @if($isExpired)
                <div class="expiry-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>
                        <strong>This contract has expired on {{ \Carbon\Carbon::parse($contract->end_date)->format('d M Y') }}.</strong>
                        Please contact the school administration for renewal or further instructions.
                    </p>
                </div>
            @endif

            <!-- Active Contract Info -->
            @if($isActive)
                @php
                    $daysRemaining = now()->diffInDays($contract->end_date, false);
                @endphp
                <div class="expiry-warning" style="background: #dcfce7; border-color: #059669;">
                    <i class="fas fa-info-circle" style="color: #059669;"></i>
                    <p style="color: #166534;">
                        <strong>Contract is active.</strong>
                        @if($daysRemaining > 0)
                            {{ $daysRemaining }} days remaining until expiration.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="verification-footer">
            <div class="verification-meta">
                <span><i class="fas fa-fingerprint"></i> Digital Signature Verified</span>
                {{-- <span><i class="fas fa-lock"></i> Blockchain Secured</span> --}}
            </div>
            <a href="#" onclick="window.print(); return false;" class="btn-print">
                <i class="fas fa-print"></i> Print Verification
            </a>
        </div>
    </div>

    <!-- Print styles -->
    <style type="text/css" media="print">
        body {
            background: white;
            padding: 0;
        }
        .verification-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }
        .btn-print {
            display: none;
        }
        .verification-header {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .status-badge, .expiry-warning {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    </style>
</body>
</html>
