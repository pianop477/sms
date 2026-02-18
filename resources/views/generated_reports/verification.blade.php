<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Verification System</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            color: #333;
        }

        .verification-container {
            width: 100%;
            max-width: 800px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 20px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.2;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .logo-icon {
            font-size: 36px;
            font-weight: bold;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            font-family: 'Poppins', sans-serif;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .content {
            padding: 40px;
        }

        .status-card {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid;
            transition: transform 0.3s ease;
        }

        .status-card:hover {
            transform: translateY(-5px);
        }

        .status-card.valid {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        .status-card.invalid {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }

        .status-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .status-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .status-icon.valid {
            background: #10b981;
            color: white;
        }

        .status-icon.invalid {
            background: #ef4444;
            color: white;
        }

        .status-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            font-family: 'Poppins', sans-serif;
        }

        .status-message {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.6;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .detail-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .detail-item:hover {
            border-color: #4f46e5;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        }

        .detail-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .detail-value {
            font-size: 16px;
            color: #1f2937;
            font-weight: 500;
        }

        .footer {
            padding: 20px 30px;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .verification-meta {
            font-size: 14px;
            color: #6b7280;
        }

        .verification-id {
            font-family: 'Courier New', monospace;
            background: #e5e7eb;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 13px;
            margin-top: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #4b5563;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .badge-valid {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-invalid {
            background: #fee2e2;
            color: #991b1b;
        }

        .security-note {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #fbbf24;
            border-radius: 12px;
            padding: 15px;
            margin-top: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .security-icon {
            color: #d97706;
            font-size: 20px;
        }

        /* Division Styling */
        .division-i {
            background-color: #75f430 !important;
            color: black !important;
            padding: 5px 15px !important;
            border-radius: 20px !important;
            font-weight: bold !important;
        }
        .division-ii {
            background-color: #99faed !important;
            color: black !important;
            padding: 5px 15px !important;
            border-radius: 20px !important;
            font-weight: bold !important;
        }
        .division-iii {
            background-color: #eddc71 !important;
            color: black !important;
            padding: 5px 15px !important;
            border-radius: 20px !important;
            font-weight: bold !important;
        }
        .division-iv {
            background-color: #b6b0b0 !important;
            color: black !important;
            padding: 5px 15px !important;
            border-radius: 20px !important;
            font-weight: bold !important;
        }
        .division-zero {
            background-color: #eb4b4b !important;
            color: white !important;
            padding: 5px 15px !important;
            border-radius: 20px !important;
            font-weight: bold !important;
        }
        .marking-style-badge {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        @media (max-width: 768px) {
            .header {
                padding: 20px 10px;
            }

            .content {
                padding: 20px 10px;
            }

            .footer {
                padding: 10px;
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                width: 100%;
            }

            .btn {
                flex: 1;
            }
        }

        .timestamp {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 10px;
            margin-bottom: 5px;
            text-align: center;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="verification-container">
        <!-- Header Section -->
        <div class="header">
            <div class="logo">
                <span class="logo-icon">✓</span>
            </div>
            <h2>Report Verification System</h2>
        </div>
        <!-- Content Section -->
        <div class="content">
            @if($valid)
                <!-- Valid Report -->
                <div class="status-card valid">
                    <div class="status-header">
                        <div>
                            <h2 class="status-title"> Verified Successfully
                            </h2>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="details-grid">
                        <!-- Student info -->
                        <div class="detail-item">
                            <div class="detail-label">Student Name</div>
                            <div class="detail-value">
                                <strong>{{ ucwords(strtolower($data['student_name'])) }}</strong>
                                <span class="badge badge-valid"> Verified</span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Admission Number</div>
                            <div class="detail-value"><strong>{{ strtoupper($data['admission_number']) }}</strong></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Class</div>
                            <div class="detail-value"><strong>{{ strtoupper($data['class']) }}</strong></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Report Type</div>
                            <div class="detail-value"><strong>{{ ucwords(strtolower($data['report_type'])) }}</strong></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Academic Term</div>
                            <div class="detail-value"><strong>Term {{ strtoupper($data['term']) }}</strong></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Issuing School</div>
                            <div class="detail-value"><strong>{{ ucwords(strtolower($data['school'])) }}</strong></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Issue Date</div>
                            <div class="detail-value"><strong>{{ \Carbon\Carbon::parse($data['report_date'])->format('d-m-Y') }}</strong></div>
                        </div>
                    </div>

                    <!-- Student Summary Section -->
                    <div class="summary-section" style="margin-top:20px; border-top:1px solid #ccc; padding-top:15px;">
                        <h3>Performance Summary</h3>
                        <div class="details-grid">
                            @if(isset($data['marking_style']) && $data['marking_style'] == 3)
                                <!-- Marking Style 3 (Division System) -->
                                <div class="detail-item">
                                    <div class="detail-label">Aggregate Points</div>
                                    <div class="detail-value">
                                        <strong>{{ $data['aggregate_points'] ?? 'N/A' }} points</strong>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-label">Division</div>
                                    <div class="detail-value">
                                        @if(isset($data['division']))
                                            @if($data['division'] == 'I')
                                                <span class="division-i">DIVISION I</span>
                                            @elseif($data['division'] == 'II')
                                                <span class="division-ii">DIVISION II</span>
                                            @elseif($data['division'] == 'III')
                                                <span class="division-iii">DIVISION III</span>
                                            @elseif($data['division'] == 'IV')
                                                <span class="division-iv">DIVISION IV</span>
                                            @else
                                                <span class="division-zero">DIVISION 0</span>
                                            @endif
                                        @else
                                            <strong>N/A</strong>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <!-- Marking Styles 1 & 2 -->
                                <div class="detail-item">
                                    <div class="detail-label">Total Score</div>
                                    <div class="detail-value"><strong>{{ number_format($data['total_score'], 2) }}</strong></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Average Score</div>
                                    <div class="detail-value"><strong>{{ number_format($data['average_score'], 2) }}</strong></div>
                                </div>
                            @endif

                            <!-- Position (common for all marking styles) -->
                            <div class="detail-item">
                                <div class="detail-label">Position</div>
                                <div class="detail-value">
                                    <strong>{{ $data['student_rank'] ?? 'N/A' }} out of {{ $data['total_students'] ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>

                        @if(isset($data['marking_style']) && $data['marking_style'] == 3)
                        <!-- Division System Explanation -->
                        <div style="margin-top: 20px; background: #f8fafc; border-radius: 10px; padding: 15px; border: 1px solid #e5e7eb;">
                            <h4 style="margin-bottom: 10px; color: #4f46e5;">Division System Information</h4>
                            <div style="font-size: 14px; color: #6b7280;">
                                <p><strong>Grade Points:</strong> A=1, B=2, C=3, D=4, F=5</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Invalid Report -->
                <div class="status-card invalid">
                    <div class="status-header">
                        <div class="status-icon invalid">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h2 class="status-title">Verification Failed</h2>
                            <p class="status-message">{{ $message }}</p>
                        </div>
                    </div>

                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">Verification Status</div>
                            <div class="detail-value">
                                Failed
                                <span class="badge badge-invalid">Invalid</span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Error Type</div>
                            <div class="detail-value">Authentication Error</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Timestamp</div>
                            <div class="detail-value">{{ now()->format('F d, Y - h:i A') }}</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Recommendation</div>
                            <div class="detail-value">Contact the issuing institution for a valid report</div>
                        </div>
                    </div>

                    <!-- Security Warning -->
                    <div class="security-note" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-color: #f87171;">
                        <i class="fas fa-exclamation-circle security-icon" style="color: #dc2626;"></i>
                        <div>
                            <strong>Security Alert:</strong> This report could not be verified. It may be altered, expired.
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <div class="verification-meta">
                <div>&copy; {{ucwords(strtolower($data['school'] ?? 'School'))}} - All Rights Reserved</div>
            </div>
        </div>

        <div class="timestamp">
            Verified on {{ now()->format('d/m/Y h:i:s A') }}
        </div>
    </div>

    <script>
        // Add animation effects
        document.addEventListener('DOMContentLoaded', function() {
            const detailItems = document.querySelectorAll('.detail-item');
            detailItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
                item.style.animation = 'fadeIn 0.5s ease-out forwards';
                item.style.opacity = '0';
            });

            // Add print functionality
            window.addEventListener('beforeprint', () => {
                document.querySelector('.action-buttons').style.display = 'none';
            });

            window.addEventListener('afterprint', () => {
                document.querySelector('.action-buttons').style.display = 'flex';
            });

            // Add keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') window.close();
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
            });
        });
    </script>
</body>
</html>
