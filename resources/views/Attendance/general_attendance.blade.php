@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f94144;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                radial-gradient(circle at 70% 30%, rgba(67, 97, 238, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 30% 70%, rgba(63, 55, 201, 0.1) 0%, transparent 30%);
            animation: rotate 60s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            25% {
                transform: translate(100px, -100px) scale(1.2);
            }

            50% {
                transform: translate(200px, 0) scale(0.8);
            }

            75% {
                transform: translate(100px, 100px) scale(1.1);
            }
        }

        /* Main Container */
        .dashboard-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* Modern Card */
        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-bottom: 30px;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        /* Card Header */
        .card-header-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 20px 25px;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-header-modern::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 60%);
            /* animation: rotate 20s linear infinite; */
        }

        .header-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Back Button */
        .btn-back-modern {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            z-index: 1;
        }

        .btn-back-modern:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            color: white;
        }

        /* Card Body */
        .card-body-modern {
            padding: 25px;
        }

        /* Form Section */
        .form-section-modern {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.7);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .form-group-modern {
            margin-bottom: 15px;
        }

        .form-label-modern {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .required-star {
            color: var(--danger);
        }

        .form-control-modern {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control-modern:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        /* Generate Button */
        .btn-generate-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: var(--shadow-md);
        }

        .btn-generate-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.3);
        }

        .btn-generate-modern:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Loading State */
        .loading-overlay {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
            background: white;
            border-radius: 20px;
            border: 1px solid #e9ecef;
        }

        .spinner-modern {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Results Container - Now with iframe */
        .results-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid #e9ecef;
            min-height: 500px;
            height: 700px;
            animation: slideUp 0.5s ease-out;
            display: flex;
            flex-direction: column;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Fixed header for actions */
        .report-actions-fixed {
            padding: 20px 25px;
            background: white;
            border-bottom: 1px solid #e9ecef;
            flex-shrink: 0;
        }

        /* Iframe container */
        .iframe-container {
            flex: 1;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        #reportIframe {
            width: 100%;
            height: 100%;
            border: none;
            background: white;
        }

        /* Report Actions */
        .report-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-download {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            color: white;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn-download:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .btn-print {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            color: white;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-print:hover {
            background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(23, 162, 184, 0.3);
        }

        .btn-fullscreen {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            color: white;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-fullscreen:hover {
            background: linear-gradient(135deg, #5a6268 0%, #545b62 100%);
            transform: translateY(-2px);
        }

        /* Error Message */
        .error-message {
            background: #fee2e2;
            border-left: 4px solid var(--danger);
            color: #991b1b;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .report-actions {
                flex-direction: column;
            }

            .results-container {
                height: 500px;
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1c2c 0%, #2a2d4a 100%);
            }

            .modern-card {
                background: rgba(33, 37, 41, 0.95);
            }

            .form-section-modern {
                background: linear-gradient(135deg, #2b3035 0%, #343a40 100%);
            }

            .form-label-modern {
                color: #e9ecef;
            }

            .form-control-modern {
                background: #2b3035;
                border-color: #495057;
                color: #e9ecef;
            }

            .results-container {
                background: #2b3035;
                border-color: #495057;
            }

            #reportIframe {
                background: #2b3035;
            }

            .loading-overlay {
                background: #2b3035;
                color: #e9ecef;
            }

            .error-message {
                background: #4a1e1e;
                color: #fecaca;
            }
        }
    </style>

    <div class="animated-bg"></div>
    <div class="particles"></div>

    <div class="dashboard-container">
        <!-- Main Card -->
        <div class="modern-card">
            <div class="card-header-modern">
                <h4 class="header-title">
                    <i class="fas fa-calendar-check"></i>
                    <span>Class Attendance Report</span>
                </h4>
                <a href="{{ route('attendance.fill.form') }}" class="btn-back-modern">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>

            <div class="card-body-modern">
                <!-- Form Section -->
                <div class="form-section-modern">
                    <div class="form-grid">
                        <!-- Class Selection -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-chalkboard text-primary"></i>
                                Class <span class="required-star">*</span>
                            </label>
                            <select id="classSelect" class="form-control-modern" required>
                                <option value="">-- Select Class --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Stream Selection -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-stream text-primary"></i>
                                Stream
                            </label>
                            <select id="streamSelect" class="form-control-modern">
                                <option value="all">All Streams</option>
                                @foreach ($streams ?? [] as $stream)
                                    <option value="{{ $stream->id }}">{{ $stream->stream_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-calendar-alt text-primary"></i>
                                Start Date <span class="required-star">*</span>
                            </label>
                            <input type="date" id="startDate" class="form-control-modern" required
                                max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                value="{{ \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}">
                        </div>

                        <!-- End Date -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-calendar-check text-primary"></i>
                                End Date <span class="required-star">*</span>
                            </label>
                            <input type="date" id="endDate" class="form-control-modern" required
                                max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn-generate-modern" id="generateReportBtn">
                            <i class="fas fa-sync-alt"></i>
                            <span>Generate Report</span>
                        </button>
                    </div>
                </div>

                <!-- Error Container -->
                <div id="errorContainer" class="error-message" style="display: none;"></div>

                <!-- Loading State -->
                <div id="loadingState" class="loading-overlay" style="display: none;">
                    <div class="text-center">
                        <div class="spinner-modern"></div>
                        <p class="text-muted mt-3">Generating report, please wait...</p>
                    </div>
                </div>

                <!-- Results Container - Now with iframe -->
                <div id="resultsContainer" class="results-container" style="display: none;">
                    <div class="report-actions-fixed no-print">
                        <div class="report-actions">
                            {{-- <button class="btn-download" onclick="downloadReport()">
                                <i class="fas fa-download"></i> Download PDF
                            </button> --}}
                            <button class="btn-print" onclick="printReport()">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                            <button class="btn-fullscreen" onclick="toggleFullscreen()">
                                <i class="fas fa-expand"></i> Fullscreen
                            </button>
                        </div>
                    </div>
                    <div class="iframe-container">
                        <iframe id="reportIframe" srcdoc="" sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-modals"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Create floating particles
            createParticles();

            // DOM Elements
            const classSelect = document.getElementById('classSelect');
            const streamSelect = document.getElementById('streamSelect');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            const generateBtn = document.getElementById('generateReportBtn');
            const resultsContainer = document.getElementById('resultsContainer');
            const loadingState = document.getElementById('loadingState');
            const errorContainer = document.getElementById('errorContainer');
            const reportIframe = document.getElementById('reportIframe');

            // Debounce timer
            let debounceTimer;

            // Date validation
            function validateDates() {
                if (startDate.value && endDate.value) {
                    if (new Date(startDate.value) > new Date(endDate.value)) {
                        showError('End date cannot be before start date');
                        return false;
                    }
                }
                hideError();
                return true;
            }

            // Show error
            function showError(message) {
                errorContainer.textContent = message;
                errorContainer.style.display = 'block';
                resultsContainer.style.display = 'none';
                loadingState.style.display = 'none';
            }

            // Hide error
            function hideError() {
                errorContainer.style.display = 'none';
            }

            // Show loading
            function showLoading() {
                loadingState.style.display = 'flex';
                resultsContainer.style.display = 'none';
                errorContainer.style.display = 'none';
                generateBtn.disabled = true;
                generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
            }

            // Hide loading
            function hideLoading() {
                loadingState.style.display = 'none';
                generateBtn.disabled = false;
                generateBtn.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Generate Report';
            }

            // Function to process HTML and add auto-increment index
            function processHTMLWithIndex(html) {
                // Create a temporary container to parse the HTML
                const temp = document.createElement('div');
                temp.innerHTML = html;

                // Find all tables with attendance-table class
                const tables = temp.querySelectorAll('.attendance-table');

                tables.forEach(table => {
                    const tbody = table.querySelector('tbody');
                    if (tbody) {
                        const rows = tbody.querySelectorAll('tr');
                        rows.forEach((row, index) => {
                            // Find the first td (should be the # column)
                            const firstTd = row.querySelector('td:first-child');
                            if (firstTd && firstTd.classList.contains('col-number')) {
                                // Replace with auto-increment index (starting from 1)
                                firstTd.textContent = index + 1;
                            }
                        });
                    }
                });

                return temp.innerHTML;
            }

            // Generate complete HTML document for iframe
            function generateIframeContent(htmlContent) {
                return `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Attendance Report</title>
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
                        <style>
                            * {
                                margin: 0;
                                padding: 0;
                                box-sizing: border-box;
                            }

                            body {
                                font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
                                background: white;
                                padding: 20px;
                                line-height: 1.6;
                            }

                            .report-content {
                                max-width: 1400px;
                                margin: 0 auto;
                            }

                            .month-section {
                                margin-bottom: 30px;
                                padding: 20px;
                                background: white;
                                border-radius: 16px;
                                border: 1px solid #e9ecef;
                            }

                            .month-section:last-child {
                                margin-bottom: 0;
                            }

                            .time-duration-header {
                                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                                padding: 12px 20px;
                                border-left: 4px solid #4361ee;
                                margin-bottom: 20px;
                                font-weight: 600;
                                border-radius: 8px;
                                font-size: 1rem;
                            }

                            .summary-section {
                                margin-bottom: 20px;
                            }

                            .summary-content {
                                display: flex;
                                gap: 20px;
                                flex-wrap: wrap;
                            }

                            .course-details {
                                flex: 2;
                                background: #f8f9fa;
                                padding: 20px;
                                border-radius: 12px;
                            }

                            .course-details p {
                                margin: 8px 0;
                                font-size: 0.95rem;
                            }

                            .grade-summary {
                                flex: 1;
                                background: #f8f9fa;
                                padding: 20px;
                                border-radius: 12px;
                                text-align: center;
                            }

                            .summary-header {
                                font-size: 1.1rem;
                                font-weight: 600;
                                color: #4361ee;
                                margin: 20px 0 15px;
                                padding-bottom: 8px;
                                border-bottom: 2px solid #4361ee;
                            }

                            .table-container {
                                overflow-x: auto;
                                border-radius: 12px;
                                border: 1px solid #e9ecef;
                            }

                            .attendance-table {
                                width: 100%;
                                border-collapse: collapse;
                            }

                            .attendance-table th {
                                background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
                                color: white;
                                padding: 12px 8px;
                                font-size: 0.85rem;
                                white-space: nowrap;
                            }

                            .attendance-table td {
                                padding: 10px 5px;
                                border: 1px solid #e9ecef;
                                font-size: 0.9rem;
                            }

                            .attendance-table tbody tr:hover {
                                background: #f8f9fa;
                            }

                            .attendance-present {
                                color: #28a745;
                                font-weight: 600;
                            }

                            .attendance-absent {
                                color: #dc3545;
                                font-weight: 600;
                            }

                            .attendance-permission {
                                color: #17a2b8;
                                font-weight: 600;
                            }

                            .col-name {
                                min-width: 200px;
                                text-align: left;
                            }

                            .col-number {
                                width: 50px;
                                text-align: center;
                            }

                            .col-gender {
                                width: 60px;
                                text-align: center;
                            }

                            .col-stream {
                                width: 70px;
                                text-align: center;
                            }

                            .col-date {
                                width: 50px;
                                text-align: center;
                            }

                            .progress {
                                height: 24px;
                                background-color: #e9ecef;
                                border-radius: 12px;
                                overflow: hidden;
                                margin: 15px 0;
                            }

                            .progress-bar {
                                height: 100%;
                                background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
                                border-radius: 12px;
                                color: white;
                                font-size: 0.8rem;
                                line-height: 24px;
                                text-align: center;
                            }

                            .legend {
                                display: flex;
                                gap: 20px;
                                padding: 12px;
                                background: #f8f9fa;
                                border-radius: 8px;
                                font-size: 0.85rem;
                                margin-top: 15px;
                            }

                            .legend-item {
                                display: flex;
                                align-items: center;
                                gap: 6px;
                            }

                            .bold {
                                font-weight: 700;
                            }

                            /* Print styles */
                            @media print {
                                body {
                                    padding: 0;
                                }

                                @page {
                                    size: A4 landscape;
                                    // orientation: landscape;
                                }

                                .month-section {
                                    break-inside: avoid;
                                    page-break-inside: avoid;
                                }

                                .no-print {
                                    display: none !important;
                                }
                            }

                            /* Dark Mode */
                            @media (prefers-color-scheme: dark) {
                                body {
                                    background: #2b3035;
                                    color: #e9ecef;
                                }

                                .month-section {
                                    background: #2b3035;
                                    border-color: #495057;
                                }

                                .attendance-table td {
                                    color: #e9ecef;
                                    border-color: #495057;
                                }

                                .attendance-table tbody tr:hover {
                                    background: #343a40;
                                }

                                .course-details,
                                .grade-summary {
                                    background: #343a40;
                                    color: #e9ecef;
                                }

                                .course-details p {
                                    color: #e9ecef;
                                }

                                .time-duration-header {
                                    background: linear-gradient(135deg, #343a40 0%, #2b3035 100%);
                                    color: #e9ecef;
                                }

                                .legend {
                                    background: #343a40;
                                    color: #e9ecef;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="report-content">
                            ${htmlContent}
                        </div>
                    </body>
                    </html>
                `;
            }

            // Display attendance report in iframe
            function displayAttendanceReport(data) {
                // Process HTML to replace IDs with auto-increment index
                const processedHtml = processHTMLWithIndex(data.html);

                // Generate complete HTML document for iframe
                const iframeContent = generateIframeContent(processedHtml);

                // Set iframe content
                reportIframe.srcdoc = iframeContent;

                // Show results container
                resultsContainer.style.display = 'flex';
                resultsContainer.style.flexDirection = 'column';
            }

            // Generate report function
            async function generateReport() {
                // Validate required fields
                if (!classSelect.value) {
                    showError('Please select a class');
                    return;
                }

                if (!startDate.value || !endDate.value) {
                    showError('Please select start and end dates');
                    return;
                }

                if (!validateDates()) {
                    return;
                }

                // Show loading
                showLoading();

                try {
                    // Prepare form data
                    const formData = new FormData();
                    formData.append('class', classSelect.value);
                    formData.append('stream', streamSelect.value);
                    formData.append('start', startDate.value);
                    formData.append('end', endDate.value);
                    formData.append('_token', '{{ csrf_token() }}');

                    // Make AJAX request
                    const response = await fetch('{{ route('class.attendance.report') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    // Check if response is OK
                    if (!response.ok) {
                        const text = await response.text();
                        console.error('Server response:', text);
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    // Try to parse JSON
                    let data;
                    try {
                        data = await response.json();
                    } catch (e) {
                        console.error('Failed to parse JSON:', e);
                        throw new Error('Server returned invalid JSON');
                    }

                    if (data.success) {
                        // Display the HTML content in iframe
                        displayAttendanceReport(data);
                        hideError();
                    } else {
                        showError(data.message || 'No attendance records found');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showError('Failed to generate report. Please try again. ' + error.message);
                } finally {
                    hideLoading();
                }
            }

            // Auto-generate on field change with debounce
            function handleFieldChange() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    if (classSelect.value && startDate.value && endDate.value) {
                        generateReport();
                    }
                }, 800);
            }

            // Add event listeners
            classSelect.addEventListener('change', handleFieldChange);
            streamSelect.addEventListener('change', handleFieldChange);
            startDate.addEventListener('change', handleFieldChange);
            endDate.addEventListener('change', handleFieldChange);
            generateBtn.addEventListener('click', handleFieldChange);

            // Create floating particles
            function createParticles() {
                const container = document.querySelector('.particles');
                if (!container) return;

                for (let i = 0; i < 30; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.width = Math.random() * 10 + 3 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 20 + 's';
                    particle.style.animationDuration = Math.random() * 10 + 15 + 's';
                    container.appendChild(particle);
                }
            }

            // Auto-generate on page load
            if (classSelect.value && startDate.value && endDate.value) {
                setTimeout(generateReport, 500);
            }
        });

        // Print report function
        function printReport() {
            const iframe = document.getElementById('reportIframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.print();
            }
        }

        // Download report as PDF
        function downloadReport() {
            const iframe = document.getElementById('reportIframe');
            if (!iframe || !iframe.contentWindow) return;

            const iframeDocument = iframe.contentWindow.document;
            const element = iframeDocument.body;

            // Check if html2pdf is available in parent window
            if (typeof html2pdf === 'undefined') {
                // Load html2pdf library dynamically if not available
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                script.onload = function() {
                    generatePDF(element);
                };
                document.head.appendChild(script);
            } else {
                generatePDF(element);
            }
        }

        function generatePDF(element) {
            const opt = {
                margin: [0.5, 0.5, 0.5, 0.5],
                filename: 'attendance_report.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, letterRendering: true },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(opt).from(element).save();
        }

        // Toggle fullscreen for iframe
        function toggleFullscreen() {
            const iframe = document.getElementById('reportIframe');
            if (iframe) {
                if (iframe.requestFullscreen) {
                    iframe.requestFullscreen();
                } else if (iframe.webkitRequestFullscreen) {
                    iframe.webkitRequestFullscreen();
                } else if (iframe.msRequestFullscreen) {
                    iframe.msRequestFullscreen();
                }
            }
        }
    </script>

    <!-- Add html2pdf library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
@endsection
