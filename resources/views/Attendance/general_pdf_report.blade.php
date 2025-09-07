@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --success: #28a745;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px; */
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 5px 10px;
            position: relative;
            overflow: hidden;
        }

        .card-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            transform: rotate(30deg);
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            font-size: 24px;
        }

        .card-body {
            padding: 10px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 1;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .report-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            height: 600px;
        }

        .report-iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 15px;
        }

        .report-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-download {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-download:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-print {
            background: linear-gradient(135deg, var(--info) 0%, #17a2b8 100%);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-print:hover {
            background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(23, 162, 184, 0.3);
            color: white;
            text-decoration: none;
        }

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        .loading-overlay {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 20px;
            }

            .report-actions {
                flex-direction: column;
            }

            .report-container {
                height: 500px;
            }
        }
    </style>

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title text-white">
                            <i class="fas fa-file-pdf me-2"></i> Class Attendance Report
                        </h4>
                        <p class="mb-0 text-white-50"> View and manage attendance reports</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('attendance.fill.form')}}" class="btn btn-back float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-chart-bar floating-icons"></i>
            </div>

            <div class="card-body">
                <div class="report-container">
                    <iframe src="{{ $fileUrl }}" class="report-iframe" id="reportFrame" onload="hideLoader()"></iframe>
                    <div id="loader" class="loading-overlay">
                        <div class="spinner"></div>
                    </div>
                </div>

                <div class="report-actions">
                    <a href="{{ $fileUrl }}" download="Attendance_Report.pdf" class="btn-download">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                    <button class="btn-print" onclick="printReport()">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show loader initially
            document.getElementById('loader').style.display = 'flex';

            // Set iframe load event
            document.getElementById('reportFrame').onload = function() {
                hideLoader();
            };
        });

        function hideLoader() {
            document.getElementById('loader').style.display = 'none';
        }

        function printReport() {
            const iframe = document.getElementById('reportFrame');
            const iframeWindow = iframe.contentWindow;

            iframeWindow.focus();
            iframeWindow.print();
        }

        // Handle iframe loading errors
        document.getElementById('reportFrame').addEventListener('error', function() {
            hideLoader();
            alert('Error loading the report. Please try again.');
        });
    </script>
@endsection
