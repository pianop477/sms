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
            --shadow-sm: 0 5px 15px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.2);
            --shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 30%);
            pointer-events: none;
            z-index: 0;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            25% {
                transform: translate(-2%, -2%) scale(1.05);
            }

            75% {
                transform: translate(2%, 2%) scale(0.95);
            }
        }

        /* Floating Shapes */
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: floatShape 15s infinite;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 400px;
            height: 400px;
            bottom: -200px;
            left: -200px;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 200px;
            height: 200px;
            top: 50%;
            right: 10%;
            animation-delay: -2s;
        }

        @keyframes floatShape {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            33% {
                transform: translate(30px, -30px) rotate(120deg);
            }

            66% {
                transform: translate(-30px, 30px) rotate(240deg);
            }
        }

        /* Main Container */
        .glass-container {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Main Card */
        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        /* Card Header */
        .card-header-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 40px;
            position: relative;
            overflow: hidden;
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

        .card-header-modern::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.1) 100%);
            pointer-events: none;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-title {
            color: white;
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0;
            line-height: 1.3;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            animation: slideInLeft 0.8s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .title-icon {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            text-align: center;
            line-height: 50px;
            margin-right: 15px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .year-highlight {
            color: #ffd700;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 15px;
            border-radius: 50px;
            display: inline-block;
            margin-left: 10px;
        }

        /* Back Button */
        .btn-back-modern {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
            font-size: 0.95rem;
        }

        .btn-back-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-back-modern:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .btn-back-modern:hover::before {
            left: 100%;
        }

        /* Card Body */
        .card-body-modern {
            padding: 40px;
        }

        /* Instruction Card */
        .instruction-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 24px;
            padding: 10px 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 8px solid var(--danger);
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .instruction-icon {
            width: 50px;
            height: 50px;
            background: var(--danger);
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .instruction-text {
            flex: 1;
        }

        .instruction-text h5 {
            color: var(--dark);
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 1.2rem;
        }

        .instruction-text p {
            color: #6c757d;
            margin: 0;
            font-size: 1rem;
        }

        /* Month Grid */
        .month-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Month Card */
        .month-card-modern {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stagger animation for month cards */
        .month-card-modern:nth-child(1) { animation-delay: 0.1s; }
        .month-card-modern:nth-child(2) { animation-delay: 0.2s; }
        .month-card-modern:nth-child(3) { animation-delay: 0.3s; }
        .month-card-modern:nth-child(4) { animation-delay: 0.4s; }
        .month-card-modern:nth-child(5) { animation-delay: 0.5s; }
        .month-card-modern:nth-child(6) { animation-delay: 0.6s; }
        .month-card-modern:nth-child(7) { animation-delay: 0.7s; }
        .month-card-modern:nth-child(8) { animation-delay: 0.8s; }
        .month-card-modern:nth-child(9) { animation-delay: 0.9s; }
        .month-card-modern:nth-child(10) { animation-delay: 1.0s; }
        .month-card-modern:nth-child(11) { animation-delay: 1.1s; }
        .month-card-modern:nth-child(12) { animation-delay: 1.2s; }

        .month-card-modern:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-3px);
        }

        /* Month Header */
        .month-header-modern {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 22px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .month-card-modern.active .month-header-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-bottom-color: rgba(255, 255, 255, 0.2);
        }

        .month-header-modern:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        }

        .month-card-modern.active .month-header-modern:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--secondary) 100%);
        }

        .month-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .month-title h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.4rem;
            transition: color 0.3s ease;
        }

        .month-card-modern.active .month-title h4 {
            color: white;
        }

        .month-icon-wrapper {
            width: 40px;
            height: 40px;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .month-card-modern.active .month-icon-wrapper {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        .month-icon-wrapper i {
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .month-card-modern.active .month-icon-wrapper i {
            color: white;
        }

        .month-badge-modern {
            background: var(--primary);
            color: white;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .month-card-modern.active .month-badge-modern {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Date List */
        .date-list-modern {
            padding: 25px 30px;
            display: none;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .month-card-modern.active .date-list-modern {
            display: block;
        }

        /* Date Instruction */
        .date-instruction-modern {
            background: rgba(247, 23, 53, 0.1);
            border-left: 4px solid var(--danger);
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            color: var(--danger);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-instruction-modern i {
            font-size: 20px;
        }

        /* Date Item */
        .date-item-modern {
            background: #f8f9fa;
            border-radius: 18px;
            padding: 18px 25px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .date-item-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-1);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .date-item-modern:hover {
            transform: translateX(5px);
            box-shadow: var(--shadow-md);
            background: white;
        }

        .date-item-modern:hover::before {
            opacity: 1;
        }

        .date-info-modern {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
        }

        .date-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-3);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }

        .date-details h6 {
            margin: 0;
            font-weight: 600;
            color: var(--dark);
            font-size: 1.1rem;
        }

        .date-details small {
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 5px;
        }

        .date-link-modern {
            text-decoration: none;
            color: var(--success);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            border-radius: 50px;
            background: rgba(40, 167, 69, 0.1);
            transition: all 0.3s ease;
        }

        .date-link-modern:hover {
            background: var(--success);
            color: white;
            transform: translateY(-2px);
            text-decoration: none;
        }

        /* Empty State */
        .empty-state-modern {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #fff9e6 0%, #ffeeb5 100%);
            border-radius: 24px;
            border-left: 8px solid var(--warning);
            animation: fadeIn 1s ease-out;
        }

        .empty-state-modern i {
            font-size: 80px;
            color: var(--warning);
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        .empty-state-modern h5 {
            color: #856404;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .empty-state-modern p {
            color: #856404;
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* Loading Spinner */
        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s ease-in-out infinite;
            z-index: 9999;
            display: none;
        }

        @keyframes spin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .header-title {
                font-size: 1.8rem;
            }

            .card-body-modern {
                padding: 30px 20px;
            }
        }

        @media (max-width: 768px) {
            .glass-container {
                margin: 20px auto;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-title {
                font-size: 1.5rem;
            }

            .instruction-card {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .month-header-modern {
                padding: 18px 20px;
            }

            .month-title h4 {
                font-size: 1.2rem;
            }

            .date-item-modern {
                flex-direction: column;
                text-align: center;
            }

            .date-info-modern {
                flex-direction: column;
                text-align: center;
            }

            .year-highlight {
                display: block;
                margin: 10px 0 0 0;
            }
        }

        @media (max-width: 576px) {
            .card-header-modern {
                padding: 20px;
            }

            .header-title {
                font-size: 1.3rem;
            }

            .month-title {
                flex-direction: column;
                text-align: center;
            }

            .month-badge-modern {
                display: none;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .modern-card {
                background: rgba(33, 37, 41, 0.95);
            }

            .month-card-modern {
                background: #2b3035;
                border-color: #495057;
            }

            .month-header-modern {
                background: linear-gradient(135deg, #2b3035 0%, #343a40 100%);
            }

            .month-title h4 {
                color: #e9ecef;
            }

            .date-item-modern {
                background: #343a40;
                border-color: #495057;
            }

            .date-details h6 {
                color: #e9ecef;
            }

            .date-details small {
                color: #adb5bd;
            }

            .instruction-card {
                background: linear-gradient(135deg, #2b3035 0%, #343a40 100%);
            }

            .instruction-text h5 {
                color: #e9ecef;
            }

            .instruction-text p {
                color: #adb5bd;
            }

            .date-instruction-modern {
                background: rgba(247, 23, 53, 0.2);
                color: #fecaca;
            }
        }
    </style>

    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="loading-spinner" id="loadingSpinner"></div>

    <div class="glass-container">
        <div class="modern-card">
            <div class="card-header-modern">
                <div class="header-content">
                    <h3 class="header-title">
                        <span class="title-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <span>Select Month</span>
                        <span class="year-highlight">{{ $year }}</span>
                    </h3>
                    <a href="{{ route('result.byType', ['student' => Hashids::encode($students->id), 'year' => $year]) }}"
                       class="btn-back-modern">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </a>
                </div>
            </div>

            <div class="card-body-modern">
                <!-- Instruction Card -->
                <div class="instruction-card">
                    <div class="instruction-icon">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <div class="instruction-text">
                        <h5>Results Explorer</h5>
                        <p>Click on any month to select date and view result.</p>
                    </div>
                </div>

                @if ($months->isEmpty())
                    <div class="empty-state-modern">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h5>No Result Records Found!</h5>
                        <p>There are no result records for year {{ $year }}</p>
                    </div>
                @else
                    <div class="month-grid">
                        @foreach ($months as $month => $dates)
                            <div class="month-card-modern">
                                <div class="month-header-modern" data-month="{{ Str::slug($month) }}">
                                    <div class="month-title">
                                        <div class="month-icon-wrapper">
                                            <i class="fas fa-chevron-right"></i>
                                        </div>
                                        <h4>
                                            <i class="fas fa-calendar-alt me-2" style="color: var(--primary);"></i>
                                            {{ \Carbon\Carbon::parse($month)->format('F') }} {{ $year }}
                                        </h4>
                                    </div>
                                    <div class="month-badge-modern">
                                        <i class="fas fa-file-alt me-2"></i>
                                        {{ count($dates) }} {{ Str::plural('Date', count($dates)) }}
                                    </div>
                                </div>

                                <div id="{{ Str::slug($month) }}" class="date-list-modern">
                                    <div class="date-instruction-modern">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Choose a date to view your result</span>
                                    </div>

                                    @foreach ($dates as $date => $results)
                                        <div class="date-item-modern">
                                            <div class="date-info-modern">
                                                <div class="date-icon">
                                                    <i class="fas fa-file-pdf"></i>
                                                </div>
                                                <div class="date-details">
                                                    <h6>{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</h6>
                                                    <small>
                                                        <i class="fas fa-calendar-day me-1"></i>
                                                        {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}
                                                    </small>
                                                </div>
                                            </div>

                                            <a href="{{ route('results.student.get', [
                                                'student' => Hashids::encode($students->id),
                                                'year' => $year,
                                                'exam_id' => Hashids::encode($exam_id),
                                                'month' => $month,
                                                'date' => $date
                                            ]) }}"
                                               target=""
                                               class="date-link-modern">
                                                <i class="fas fa-download"></i>
                                                <span>View Result</span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Toggle functionality with smooth animation
            const monthHeaders = document.querySelectorAll(".month-header-modern");

            monthHeaders.forEach(header => {
                header.addEventListener("click", function(e) {
                    // Don't toggle if clicking on buttons or links inside header
                    if (e.target.closest('a') || e.target.closest('button')) {
                        return;
                    }

                    const monthCard = this.closest(".month-card-modern");
                    const monthDiv = document.getElementById(this.dataset.month);

                    // Close all other months
                    if (!monthCard.classList.contains('active')) {
                        document.querySelectorAll(".month-card-modern.active").forEach(card => {
                            card.classList.remove('active');
                            const otherDiv = document.getElementById(card.querySelector(
                                '.month-header-modern').dataset.month);
                            if (otherDiv) {
                                otherDiv.style.display = 'none';
                            }
                        });
                    }

                    // Toggle current month
                    monthCard.classList.toggle("active");

                    if (monthDiv.style.display === "block") {
                        monthDiv.style.display = "none";
                    } else {
                        monthDiv.style.display = "block";
                        // Smooth scroll to the opened month
                        monthCard.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    }
                });
            });

            // Show loading spinner on link clicks
            const dateLinks = document.querySelectorAll('.date-link-modern');
            dateLinks.forEach(link => {
                link.addEventListener('click', function() {
                    document.getElementById('loadingSpinner').style.display = 'block';
                });
            });

            // Add hover effect to date items
            const dateItems = document.querySelectorAll('.date-item-modern');
            dateItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    // Close all open months
                    document.querySelectorAll(".month-card-modern.active").forEach(card => {
                        card.classList.remove('active');
                        const monthDiv = document.getElementById(card.querySelector(
                            '.month-header-modern').dataset.month);
                        if (monthDiv) {
                            monthDiv.style.display = 'none';
                        }
                    });
                }
            });
        });
    </script>
@endsection
