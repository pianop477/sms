@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --info: #17a2b8;
            --success: #28a745;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin-top: 20px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px 25px;
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
            display: flex;
            align-items: center;
        }

        .date-highlight {
            color: #ffd700;
            font-weight: 600;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            background: rgba(0, 0, 0, 0.2);
            padding: 5px 15px;
            border-radius: 50px;
            margin-left: 15px;
        }

        .card-body {
            padding: 25px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 8px 16px;
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

        .pdf-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.5);
            background: white;
            position: relative;
        }

        .pdf-controls {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #dee2e6;
        }

        .pdf-title {
            font-weight: 600;
            color: var(--primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pdf-actions {
            display: flex;
            gap: 10px;
        }

        .pdf-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .pdf-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .pdf-btn-download {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
        }

        .pdf-btn-print {
            background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%);
        }

        .pdf-viewer {
            width: 100%;
            height: 800px;
            border: none;
        }

        .floating-icons {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 50px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            border-radius: 15px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .pdf-controls {
                flex-direction: column;
                gap: 15px;
            }

            .pdf-actions {
                width: 100%;
                justify-content: center;
            }

            .header-title {
                font-size: 20px;
            }

            .pdf-viewer {
                height: 500px;
            }
        }

        .info-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 50px;
            padding: 5px 15px;
            font-size: 14px;
            font-weight: 600;
            margin-left: 15px;
        }
    </style>
    <div class="container">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <h4 class="header-title text-white">
                            <i class="fas fa-file-pdf"></i>
                            Examination Results for
                            <span class="date-highlight">{{ $date }}</span>
                        </h4>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('results.byExamType', ['course' => Hashids::encode($id[0]), 'year' => $year, 'examType' => Hashids::encode($exam_id[0])])}}" class="btn btn-back btn-xs float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-chart-bar floating-icons"></i>
            </div>
            <div class="card-body">
                <div class="pdf-container">
                    <div class="pdf-controls">
                        <h5 class="pdf-title">
                            <i class="fas fa-graduation-cap"></i>
                            Results Document
                            <span class="info-badge">PDF</span>
                        </h5>
                        <div class="pdf-actions">
                            <a href="{{ $pdfUrl }}" download="Exam_Results_{{ $date }}.pdf" class="pdf-btn pdf-btn-download">
                                <i class="fas fa-download"></i> Download
                            </a>
                            <button onclick="printPDF()" class="pdf-btn pdf-btn-print">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>

                    <div class="position-relative">
                        <div class="loading-overlay" id="pdfLoading">
                            <div class="spinner"></div>
                        </div>
                        <iframe src="{{ $pdfUrl }}" class="pdf-viewer" id="pdfViewer" onload="hideLoader()"></iframe>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="alert alert-info" role="alert">
                        <h5><i class="fas fa-info-circle me-2"></i>Results Information</h5>
                        <p class="mb-1"><strong>Date:</strong> {{ $date }}</p>
                        <p class="mb-1"><strong>Academic Year:</strong> {{ $year }}</p>
                        <p class="mb-0"><strong>Document Type:</strong> Examination Results PDF</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function hideLoader() {
            document.getElementById('pdfLoading').style.display = 'none';
        }

        function printPDF() {
            const pdfFrame = document.getElementById('pdfViewer');
            pdfFrame.contentWindow.print();
        }

        // Show loader if PDF takes time to load
        setTimeout(hideLoader, 5000);

        document.addEventListener("DOMContentLoaded", function () {
            // Add animation to the PDF container
            const pdfContainer = document.querySelector('.pdf-container');
            pdfContainer.style.transform = 'translateY(20px)';
            pdfContainer.style.opacity = '0';

            setTimeout(() => {
                pdfContainer.style.transition = 'all 0.5s ease';
                pdfContainer.style.transform = 'translateY(0)';
                pdfContainer.style.opacity = '1';
            }, 300);
        });
    </script>
@endsection
