@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6f42c1;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }

        .header-title {
            font-weight: 600;
            margin: 0;
        }

        .btn-action {
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
        }

        .report-container {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0.15rem 0.5rem 0 rgba(58, 59, 69, 0.1);
            margin-top: 20px;
            background: white;
        }

        .report-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .download-btn {
            background: linear-gradient(135deg, var(--success-color) 0%, #138a63 100%);
            color: white;
            border: none;
        }

        .print-btn {
            background: linear-gradient(135deg, var(--info-color) 0%, #2a96a5 100%);
            color: white;
            border: none;
        }

        @media (max-width: 768px) {
            .header-title {
                font-size: 1.2rem;
            }

            .btn-action {
                width: 100%;
                margin-top: 10px;
            }

            .report-actions {
                flex-direction: column;
            }

            iframe {
                height: 500px !important;
            }
        }

        @media (max-width: 576px) {
            iframe {
                height: 400px !important;
            }
        }
    </style>

    <div class="py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-10">
                <h4 class="text-primary fw-bold border-bottom pb-2">
                    <i class="fas fa-file-pdf me-2"></i> ATTENDANCE REPORT VIEWER
                </h4>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{route('attendance.get.form', ['class' => Hashids::encode($id[0])])}}" class="btn btn-info btn-action float-right">
                    <i class="fas fa-arrow-circle-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header-custom">
                        <h5 class="header-title text-white text-center">
                            <i class="fas fa-chart-bar me-2"></i> Generated Attendance Report
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info alert-custom">
                            <i class="fas fa-info-circle me-2"></i>
                            Viewing generated attendance report. You can download or print this report using the buttons below.
                        </div>

                        <div class="report-container">
                            <iframe src="{{ $fileUrl }}" width="100%" height="600px" frameborder="0" class="rounded"></iframe>
                        </div>

                        <div class="report-actions">
                            <a href="{{ $fileUrl }}" download="Attendance_Report.pdf" class="btn download-btn btn-action">
                                <i class="fas fa-download me-1"></i> Download PDF
                            </a>
                            <button onclick="printReport()" class="btn print-btn btn-action">
                                <i class="fas fa-print me-1"></i> Print Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printReport() {
            const iframe = document.querySelector('iframe');
            const iframeWindow = iframe.contentWindow;

            iframeWindow.focus();
            iframeWindow.print();
        }

        // Handle iframe load event
        document.querySelector('iframe').addEventListener('load', function() {
            // Add some basic styling to the iframe content
            try {
                const iframeDoc = this.contentDocument || this.contentWindow.document;
                const style = iframeDoc.createElement('style');
                style.textContent = `
                    body {
                        font-family: Arial, sans-serif;
                        padding: 20px;
                    }
                    @media print {
                        body {
                            padding: 0;
                            margin: 0;
                        }
                    }
                `;
                iframeDoc.head.appendChild(style);
            } catch (e) {
                console.log('Could not style iframe content due to cross-origin restrictions');
            }
        });
    </script>
@endsection
