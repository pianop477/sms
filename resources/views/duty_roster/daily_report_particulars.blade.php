@extends('SRTDashboard.frame')
@section('content')
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #ecf0f1;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --error-color: #e74c3c;
            --text-color: #2c3e50;
            --border-color: #bdc3c7;
            --light-bg: #f8f9fa;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            min-height: 100vh;
            padding: 20px 0;
        }

        .form-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: white;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
            border-bottom: none;
            padding: 1.8rem;
            position: relative;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), var(--success-color));
        }

        .progress-container {
            padding: 0 2rem;
            margin-top: -15px;
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        .progress {
            height: 10px;
            border-radius: 5px;
            background-color: var(--secondary-color);
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--accent-color), var(--success-color));
            transition: width 0.5s ease;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .step {
            text-align: center;
            font-weight: 600;
            color: var(--text-color);
            font-size: 0.9rem;
        }

        .step.active {
            color: var(--accent-color);
        }

        .step.completed {
            color: var(--success-color);
        }

        .form-section {
            display: none;
            padding: 2rem;
            animation: fadeIn 0.5s ease;
        }

        .form-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control,
        .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.85rem;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.7rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 8px;
            color: var(--accent-color);
        }

        .required-field::after {
            content: "*";
            color: var(--error-color);
            margin-left: 4px;
        }

        .btn-primary {
            background: linear-gradient(to right, var(--accent-color), #2980b9);
            border: none;
            padding: 0.85rem 2.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-outline-secondary {
            border: 2px solid var(--border-color);
            padding: 0.85rem 2.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
        }

        .toggle-container {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            background-color: var(--light-bg);
            padding: 1rem;
            border-radius: 8px;
        }

        .toggle-label {
            margin-right: 15px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: var(--success-color);
        }

        input:checked+.slider:before {
            transform: translateX(30px);
        }

        .event-field {
            display: none;
            transition: all 0.5s ease;
        }

        .card-footer {
            background-color: var(--secondary-color);
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
        }

        .section-title {
            border-left: 4px solid var(--accent-color);
            padding-left: 15px;
            margin: 2rem 0 1.5rem 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: var(--accent-color);
        }

        table th {
            font-weight: 600;
            font-size: 0.9rem;
        }

        table tbody tr:nth-child(even) {
            background-color: var(--light-bg);
        }

        table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
            transition: background-color 0.3s;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .btn-success {
            background: linear-gradient(to right, var(--success-color), #2ecc71);
            border: none;
            padding: 0.85rem 2.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 0px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            background: var(--success-color);
            color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            transform: translateX(100%);
            transition: transform 0.5s ease;
            z-index: 1000;
        }

        .notification.show {
            transform: translateX(0);
        }

        .error-notification {
            background: var(--error-color);
        }

        .error-border {
            border: 1px solid var(--error-color) !important;
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }

        .table-danger {
            background-color: rgba(231, 76, 60, 0.2) !important;
            color: var(--error-color);
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .card-header {
                padding: 1.2rem;
            }

            .form-section {
                padding: 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .action-buttons button {
                width: 100%;
            }

            .step-indicator {
                flex-direction: column;
                gap: 5px;
            }

            .step {
                font-size: 0.8rem;
            }
        }

        /* =========================================================
               ATTENDANCE TABLE
               ========================================================= */

        .attendance-table-container {
            width: 100%;
            max-width: 100%;
            margin-top: 1rem;
            border: 1px solid #dfe6ec;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.055);
            overflow: hidden;
        }

        .attendance-scroll-hint {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 8px 12px;
            background: linear-gradient(90deg, #eef7ff, #f4fbff);
            border-bottom: 1px solid #dceaf5;
            color: #2980b9;
            font-size: 0.72rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .attendance-scroll-hint i {
            font-size: 0.8rem;
            animation: attendanceSwipeHint 1.5s ease-in-out infinite;
        }

        @keyframes attendanceSwipeHint {

            0%,
            100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(5px);
            }
        }

        .attendance-table-scroll {
            position: relative;
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            touch-action: pan-x pan-y;
            max-height: 65vh;
            scrollbar-width: thin;
            scrollbar-color: #3498db #edf2f7;
        }

        .attendance-table-scroll::-webkit-scrollbar {
            width: 7px;
            height: 9px;
        }

        .attendance-table-scroll::-webkit-scrollbar-track {
            background: #edf2f7;
        }

        .attendance-table-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, var(--accent-color), var(--success-color));
            border-radius: 20px;
        }

        .attendance-table-scroll::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, #2980b9, #27ae60);
        }

        .attendance-table {
            width: max-content;
            min-width: 100%;
            margin: 0 !important;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            font-size: 0.82rem;
        }

        .attendance-table th,
        .attendance-table td {
            min-width: 72px;
            padding: 10px 13px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .attendance-table .attendance-class-column,
        .attendance-table tbody td:first-child {
            width: 120px;
            min-width: 120px;
        }

        .attendance-table thead th {
            position: sticky;
            top: 0;
            z-index: 20;
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.15);
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.2px;
            text-align: center;
            vertical-align: middle;
        }

        .attendance-table thead tr:first-child th {
            height: 44px;
        }

        .attendance-table thead tr:nth-child(2) th {
            height: 40px;
            background: linear-gradient(135deg, #34495e, #243342);
            font-size: 0.7rem;
        }

        .attendance-table .attendance-class-column {
            position: sticky;
            left: 0;
            z-index: 30;
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
        }

        .attendance-table tbody td:first-child {
            position: sticky;
            left: 0;
            z-index: 10;
            background: #ffffff;
            font-weight: 700;
            text-align: left;
            box-shadow: 4px 0 8px rgba(0, 0, 0, 0.06);
        }

        .attendance-table tbody td {
            color: var(--text-color);
            background: #ffffff;
            border-color: #e5e9ed;
            font-size: 0.8rem;
        }

        .attendance-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .attendance-table tbody tr:nth-child(even) td:first-child {
            background: #f8fafc;
        }

        .attendance-table tbody tr:hover td {
            background: rgba(52, 152, 219, 0.08);
        }

        .attendance-table tbody tr:hover td:first-child {
            background: #eef7ff;
        }

        .attendance-table tbody tr.table-secondary td {
            background: #e9ecef !important;
            color: #212529;
            font-weight: 700;
        }

        .attendance-table tbody tr.table-secondary td:first-child {
            background: #e9ecef !important;
        }

        .attendance-table tbody td.table-danger {
            background: rgba(231, 76, 60, 0.16) !important;
            color: var(--error-color);
            font-weight: 700;
        }

        .attendance-table tbody td:first-child.table-danger {
            background: rgba(231, 76, 60, 0.16) !important;
        }

        .attendance-empty-state {
            padding: 28px 15px !important;
            color: #6c757d;
            text-align: center !important;
            background: #ffffff !important;
            font-size: 0.85rem !important;
        }

        @media (max-width: 768px) {
            .attendance-table-container {
                margin-top: 0.75rem;
                border-radius: 10px;
            }

            .attendance-table-scroll {
                overflow-x: auto;
                max-height: 60vh;
            }

            .attendance-table {
                min-width: 920px;
            }

            .attendance-table th,
            .attendance-table td {
                min-width: 68px;
                padding: 9px 10px;
                font-size: 0.72rem;
            }

            .attendance-table .attendance-class-column,
            .attendance-table tbody td:first-child {
                width: 105px;
                min-width: 105px;
            }

            .attendance-table thead tr:first-child th {
                height: 40px;
                font-size: 0.68rem;
            }

            .attendance-table thead tr:nth-child(2) th {
                height: 36px;
                font-size: 0.64rem;
            }

            .attendance-table tbody td {
                font-size: 0.72rem;
            }
        }

        @media (max-width: 480px) {
            .attendance-table-container {
                width: calc(100% + 2px);
            }

            .attendance-table-scroll {
                max-height: 58vh;
            }

            .attendance-table {
                min-width: 880px;
            }

            .attendance-table th,
            .attendance-table td {
                min-width: 64px;
                padding: 8px 9px;
            }

            .attendance-table .attendance-class-column,
            .attendance-table tbody td:first-child {
                width: 100px;
                min-width: 100px;
            }

            .attendance-table thead tr:first-child th {
                font-size: 0.64rem;
            }

            .attendance-table thead tr:nth-child(2) th {
                font-size: 0.6rem;
            }

            .attendance-table tbody td {
                font-size: 0.68rem;
            }
        }

        @media (max-width: 360px) {
            .attendance-table {
                min-width: 850px;
            }

            .attendance-table th,
            .attendance-table td {
                min-width: 61px;
                padding: 8px 8px;
            }

            .attendance-table .attendance-class-column,
            .attendance-table tbody td:first-child {
                width: 94px;
                min-width: 94px;
            }
        }

        /* =========================================================
               MISSING ATTENDANCE MODAL
               ========================================================= */
        #missingAttendanceModal .modal-header {
            border-bottom: none;
        }

        #missingAttendanceModal .modal-content {
            border-radius: 14px;
            border: none;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        #missingAttendanceModal .modal-body {
            padding: 1.5rem;
        }

        #missingAttendanceModal .missing-list {
            max-height: 220px;
            overflow-y: auto;
            border: 1px solid #f1c40f;
            background: #fffdf5;
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 1rem;
        }

        #missingAttendanceModal .missing-list li {
            padding: 4px 0;
            font-weight: 600;
            color: #7d5a00;
            border-bottom: 1px dashed #f7e6a1;
        }

        #missingAttendanceModal .missing-list li:last-child {
            border-bottom: none;
        }

        #missingAttendanceModal .missing-list::-webkit-scrollbar {
            width: 6px;
        }

        #missingAttendanceModal .missing-list::-webkit-scrollbar-thumb {
            background: #f39c12;
            border-radius: 20px;
        }
    </style>

    <div class="form-container">
        <div class="card shadow-sm">
            <div class="card-header text-white text-center">
                <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> SCHOOL DAILY REPORT</h4>
                <p class="mb-0 mt-1 text-white">School Routine Tracking System</p>
            </div>

            <div class="progress-container">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0"
                        aria-valuemax="100"></div>
                </div>
                <div class="step-indicator">
                    <div class="step active">SECTION A: STUDENT'S ATTENDANCES</div>
                    <div class="step">SECTION B: DAILY ACTIVITIES</div>
                </div>
            </div>

            @if ($reports->isEmpty())
                <form id="dailyReportForm" action="{{ route('tod.report.store') }}" method="POST">
                    @csrf

                    <!-- Section A - Student Attendance -->
                    <div class="form-section active" id="section-a">
                        <div class="section-title">
                            <i class="fas fa-chart-bar"></i>Attendance Summary
                        </div>

                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>Click the button below to fetch attendance records for the date
                                {{ \Carbon\Carbon::parse(now())->format('d-m-Y') }}</div>
                        </div>

                        <div class="d-flex justify-content-center mb-4">
                            <button type="button" id="fetchAttendanceBtn" class="btn btn-primary">
                                <i class="fas fa-sync me-2"></i> Fetch Attendance
                            </button>
                        </div>

                        <div class="attendance-table-container">
                            <div class="attendance-scroll-hint d-md-none">
                                <i class="fas fa-arrows-alt-h"></i>
                                <span>Swipe horizontally to view all attendance columns</span>
                            </div>

                            <div class="attendance-table-scroll">
                                <table class="table table-bordered attendance-table">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="attendance-class-column">Class</th>
                                            <th colspan="3">Registered</th>
                                            <th colspan="3">Attended</th>
                                            <th colspan="3">Absentees</th>
                                            <th colspan="3">Permission</th>
                                        </tr>
                                        <tr>
                                            <th>Boys</th>
                                            <th>Girls</th>
                                            <th>Total</th>
                                            <th>Boys</th>
                                            <th>Girls</th>
                                            <th>Total</th>
                                            <th>Boys</th>
                                            <th>Girls</th>
                                            <th>Total</th>
                                            <th>Boys</th>
                                            <th>Girls</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendanceTableBody">
                                        <tr>
                                            <td colspan="13" class="attendance-empty-state">
                                                <i class="fas fa-database me-2"></i>
                                                No attendance records available yet
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button type="button" id="proceedToSectionB" class="btn btn-success float-left">
                                <i class="fas fa-arrow-right me-2"></i> Confirm
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-danger float-right">
                                <i class="fas fa-close me-2"></i> Cancel
                            </a>
                        </div>
                    </div>

                    <!-- Section B - Daily Activities -->
                    <div class="form-section" id="section-b">
                        <div class="form-group mb-4">
                            <label for="report_date" class="form-label required-field">
                                <i class="fas fa-calendar-alt"></i>Report Date
                            </label>
                            <input type="date" name="report_date" id="report_date" class="form-control"
                                value="{{ date('Y-m-d') }}" required readonly>
                            <div class="error-message" id="report_date_error">Please select the valid report date.</div>
                        </div>

                        <div class="section-title">
                            <i class="fas fa-clock"></i>Daily Schedule
                        </div>

                        <div class="form-group mb-4">
                            <label for="parade" class="form-label required-field">
                                <i class="fas fa-users"></i>Morning Parade
                            </label>
                            <textarea name="parade" id="parade" rows="3" class="form-control" placeholder="Enter parade details..."
                                required></textarea>
                            <div class="error-message" id="parade_error">Please fill out parade details.</div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="break_time" class="form-label required-field">
                                <i class="fas fa-coffee"></i>Break Time
                            </label>
                            <textarea name="break_time" id="break_time" rows="3" class="form-control"
                                placeholder="Enter break time details..." required></textarea>
                            <div class="error-message" id="break_time_error">Please fill out Break time details. E.g.
                                Breakfast etc.</div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="lunch_time" class="form-label required-field">
                                <i class="fas fa-utensils"></i>Lunch Time
                            </label>
                            <textarea name="lunch_time" id="lunch_time" rows="3" class="form-control"
                                placeholder="Enter lunch time details..." required></textarea>
                            <div class="error-message" id="lunch_time_error">Please fill out Lunch time details E.g. Lunch
                                food etc.</div>
                        </div>

                        <div class="section-title">
                            <i class="fas fa-calendar-check"></i>Attendance & Events
                        </div>

                        <div class="form-group mb-4">
                            <label for="teachers_attendance" class="form-label required-field">
                                <i class="fas fa-chalkboard-teacher"></i>Teachers Attendance
                            </label>
                            <textarea name="teachers_attendance" id="teachers_attendance" rows="3" class="form-control"
                                placeholder="Enter teachers attendance details..." required></textarea>
                            <div class="error-message" id="teachers_attendance_error">Please fill out the teachers
                                attendance details</div>
                        </div>

                        <div class="toggle-container">
                            <span class="toggle-label">Any special event that disrupted normal school timetable?</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="event_toggle" name="event_toggle">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="form-group mb-4 event-field" id="event_field">
                            <label for="daily_new_event" class="form-label">
                                <i class="fas fa-calendar-plus"></i>Event Details
                            </label>
                            <textarea name="daily_new_event" id="daily_new_event" rows="3" class="form-control"
                                placeholder="Enter event details here...."></textarea>
                        </div>

                        <div class="section-title">
                            <i class="fas fa-comment-dots"></i>Remarks
                        </div>

                        <div class="form-group mb-4">
                            <label for="tod_remarks" class="form-label required-field">
                                <i class="fas fa-sticky-note"></i>Teacher on Duty Remarks
                            </label>
                            <textarea name="tod_remarks" id="tod_remarks" rows="3" class="form-control"
                                placeholder="Enter your remarks..." required></textarea>
                            <div class="error-message" id="tod_remarks_error">Please fill out your overall general remarks
                                for the day schedule</div>
                        </div>

                        <div class="action-buttons">
                            <button type="button" id="backToSectionA" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Attendance
                            </button>
                            <button type="reset" class="btn btn-outline-secondary" id="resetFormBtn">
                                <i class="fas fa-redo-alt me-2"></i> Reset Form
                            </button>
                            <button type="submit" class="btn btn-success float-right"
                                onclick="return confirm('Are you sure you want to submit final report?')">
                                <i class="fas fa-paper-plane me-2"></i> Submit Final Report
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="alert alert-success text-center my-4 fs-4" role="alert">
                    
                    <p>
                        <i class="fas fa-check-square-o me-2"></i>
                        Daily School report already submitted for the date
                    ({{ \Carbon\Carbon::parse(now())->format('d-m-Y') }}).
                    </p>
                </div>
            @endif

            <div class="card-footer text-muted text-center py-3">
                <small><i class="fas fa-info-circle me-1"></i> Once submitted, your report will be uneditable.</small>
            </div>
        </div>
    </div>

    <div class="notification error-notification" id="errorNotification">
        <i class="fas fa-exclamation-circle me-2"></i> <span id="errorMessage">Please fill out all required fields before
            you proceed.</span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ============================================
            // Elements
            // ============================================
            const sectionA = document.getElementById('section-a');
            const sectionB = document.getElementById('section-b');
            const proceedBtn = document.getElementById('proceedToSectionB');
            const backBtn = document.getElementById('backToSectionA');
            const resetBtn = document.getElementById('resetFormBtn');
            const progressBar = document.querySelector('.progress-bar');
            const steps = document.querySelectorAll('.step');
            const eventToggle = document.getElementById('event_toggle');
            const eventField = document.getElementById('event_field');
            const fetchBtn = document.getElementById('fetchAttendanceBtn');
            const attendanceTableBody = document.getElementById('attendanceTableBody');
            const errorNotification = document.getElementById('errorNotification');
            const errorMessage = document.getElementById('errorMessage');

            // State
            let attendanceFetched = false;
            let missingAttendanceModalInstance = null;

            // Required fields for Section B
            const requiredFields = [
                'report_date',
                'parade',
                'break_time',
                'lunch_time',
                'teachers_attendance',
                'tod_remarks'
            ];

            // ============================================
            // Event toggle
            // ============================================
            eventToggle.addEventListener('change', function() {
                eventField.style.display = this.checked ? 'block' : 'none';
            });

            // ============================================
            // Reset form
            // ============================================
            resetBtn.addEventListener('click', function() {
                document.querySelectorAll('.error-message').forEach(el => {
                    el.style.display = 'none';
                });
                document.querySelectorAll('.form-control').forEach(el => {
                    el.classList.remove('error-border');
                });

                eventField.style.display = 'none';
                eventToggle.checked = false;

                const today = new Date().toISOString().split('T')[0];
                document.getElementById('report_date').value = today;
            });

            // ============================================
            // Proceed to Section B
            // ============================================
            proceedBtn.addEventListener('click', function() {
                if (!attendanceFetched) {
                    errorMessage.textContent = 'Please fetch attendance records before proceeding.';
                    errorNotification.classList.add('show');
                    setTimeout(() => {
                        errorNotification.classList.remove('show');
                    }, 3000);

                    fetchBtn.classList.add('btn-danger');
                    setTimeout(() => {
                        fetchBtn.classList.remove('btn-danger');
                    }, 2000);
                    return;
                }

                const missingClasses = getClassesWithMissingAttendance();

                if (missingClasses.length > 0) {
                    showMissingAttendancePrompt(missingClasses);
                } else {
                    goToSectionB();
                }
            });

            // ============================================
            // Back to Section A
            // ============================================
            backBtn.addEventListener('click', function() {
                sectionB.classList.remove('active');
                sectionA.classList.add('active');

                progressBar.style.width = '50%';
                steps[1].classList.remove('active');
                steps[0].classList.remove('completed');
                steps[0].classList.add('active');
            });

            // ============================================
            // Field validation (Section B)
            // ============================================
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', function() {
                        validateField(fieldId);
                    });
                }
            });

            function validateField(fieldId) {
                const field = document.getElementById(fieldId);
                const errorElement = document.getElementById(fieldId + '_error');

                if (!field || !errorElement) return true;

                if (!field.value.trim()) {
                    field.classList.add('error-border');
                    errorElement.style.display = 'block';
                    return false;
                } else {
                    field.classList.remove('error-border');
                    errorElement.style.display = 'none';
                    return true;
                }
            }

            function validateAllFields() {
                let isValid = true;
                requiredFields.forEach(fieldId => {
                    if (!validateField(fieldId)) {
                        isValid = false;
                    }
                });
                return isValid;
            }

            // ============================================
            // Form submission
            // ============================================
            document.getElementById('dailyReportForm').addEventListener('submit', function(e) {
                if (!validateAllFields()) {
                    e.preventDefault();

                    errorNotification.classList.add('show');
                    setTimeout(() => {
                        errorNotification.classList.remove('show');
                    }, 3000);

                    const firstErrorField = document.querySelector('.error-border');
                    if (firstErrorField) {
                        firstErrorField.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });

            // ============================================
            // Fetch attendance data
            // ============================================
            fetchBtn.addEventListener('click', async function() {
                fetchBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-2"></i> Retrieving, Please wait.....';
                fetchBtn.disabled = true;

                try {
                    const res = await fetch(`/api/attendance/fetch`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();

                    attendanceTableBody.innerHTML = '';

                    // Filter classes with registered students
                    const filteredData = data.filter(record => {
                        if (record.class_code === 'TOTAL') return true;
                        const totalRegistered = Number(record.registered_boys) + Number(record
                            .registered_girls);
                        return totalRegistered > 0;
                    });

                    const realRecords = filteredData.filter(r => r.class_code !== 'TOTAL');

                    if (realRecords.length === 0) {
                        attendanceTableBody.innerHTML = `
                            <tr>
                                <td colspan="13" class="attendance-empty-state">
                                    <i class="fas fa-database me-2"></i> No attendance records found for today.
                                </td>
                            </tr>`;
                        attendanceFetched = false;
                    } else {
                        // Recalculate totals
                        const totals = {
                            registered_boys: 0,
                            registered_girls: 0,
                            attended_boys: 0,
                            attended_girls: 0,
                            absent_boys: 0,
                            absent_girls: 0,
                            permission_boys: 0,
                            permission_girls: 0,
                        };

                        realRecords.forEach(r => {
                            totals.registered_boys += Number(r.registered_boys) || 0;
                            totals.registered_girls += Number(r.registered_girls) || 0;
                            totals.attended_boys += Number(r.attended_boys) || 0;
                            totals.attended_girls += Number(r.attended_girls) || 0;
                            totals.absent_boys += Number(r.absent_boys) || 0;
                            totals.absent_girls += Number(r.absent_girls) || 0;
                            totals.permission_boys += Number(r.permission_boys) || 0;
                            totals.permission_girls += Number(r.permission_girls) || 0;
                        });

                        // Render real records
                        realRecords.forEach(record => {
                            function highlightIfZero(value) {
                                return Number(value) === 0 ? 'class="table-danger"' : '';
                            }

                            attendanceTableBody.innerHTML += `
                                <tr>
                                    <td style="text-transform:uppercase">${record.class_code} ${record.stream ?? ''}</td>
                                    <td>${record.registered_boys}<input type="hidden" name="attendance[${record.class_id}_${record.stream}][registered_boys]" value="${record.registered_boys}"></td>
                                    <td>${record.registered_girls}<input type="hidden" name="attendance[${record.class_id}_${record.stream}][registered_girls]" value="${record.registered_girls}"></td>
                                    <td>${Number(record.registered_boys) + Number(record.registered_girls)}</td>
                                    <td ${highlightIfZero(record.attended_boys)}>${record.attended_boys}<input type="hidden" name="attendance[${record.class_id}_${record.stream}][present_boys]" value="${record.attended_boys}"></td>
                                    <td ${highlightIfZero(record.attended_girls)}>${record.attended_girls}<input type="hidden" name="attendance[${record.class_id}_${record.stream}][present_girls]" value="${record.attended_girls}"></td>
                                    <td ${highlightIfZero(Number(record.attended_boys) + Number(record.attended_girls))}>${Number(record.attended_boys) + Number(record.attended_girls)}</td>
                                    <td ${highlightIfZero(record.absent_boys)}>${record.absent_boys}<input type="hidden" name="attendance[${record.class_id}_${record.stream}][absent_boys]" value="${record.absent_boys}"></td>
                                    <td ${highlightIfZero(record.absent_girls)}>${record.absent_girls}<input type="hidden" name="attendance[${record.class_id}_${record.stream}][absent_girls]" value="${record.absent_girls}"></td>
                                    <td ${highlightIfZero(Number(record.absent_boys) + Number(record.absent_girls))}>${Number(record.absent_boys) + Number(record.absent_girls)}</td>
                                    <td ${highlightIfZero(record.permission_boys)}>${record.permission_boys}<input type="hidden" name="attendance[${record.class_id}_${record.stream}][permission_boys]" value="${record.permission_boys}"></td>
                                    <td ${highlightIfZero(record.permission_girls)}>${record.permission_girls}<input type="hidden" name="attendance[${record.class_id}_${record.stream}][permission_girls]" value="${record.permission_girls}"></td>
                                    <td ${highlightIfZero(Number(record.permission_boys) + Number(record.permission_girls))}>${Number(record.permission_boys) + Number(record.permission_girls)}</td>
                                    <input type="hidden" name="attendance[${record.class_id}_${record.stream}][group]" value="${record.stream || ''}">
                                    <input type="hidden" name="attendance[${record.class_id}_${record.stream}][class_id]" value="${record.class_id}">
                                </tr>`;
                        });

                        // Append TOTAL
                        attendanceTableBody.innerHTML += `
                            <tr class="table-secondary fw-bold">
                                <td>TOTAL</td>
                                <td>${totals.registered_boys}</td>
                                <td>${totals.registered_girls}</td>
                                <td>${totals.registered_boys + totals.registered_girls}</td>
                                <td>${totals.attended_boys}</td>
                                <td>${totals.attended_girls}</td>
                                <td>${totals.attended_boys + totals.attended_girls}</td>
                                <td>${totals.absent_boys}</td>
                                <td>${totals.absent_girls}</td>
                                <td>${totals.absent_boys + totals.absent_girls}</td>
                                <td>${totals.permission_boys}</td>
                                <td>${totals.permission_girls}</td>
                                <td>${totals.permission_boys + totals.permission_girls}</td>
                            </tr>`;

                        attendanceFetched = true;
                    }
                } catch (err) {
                    console.error(err);
                    errorMessage.textContent = 'Error fetching attendance records.';
                    errorNotification.classList.add('show');
                    setTimeout(() => {
                        errorNotification.classList.remove('show');
                    }, 3000);
                    attendanceFetched = false;
                } finally {
                    fetchBtn.innerHTML = '<i class="fas fa-sync me-2"></i> Fetch Attendance';
                    fetchBtn.disabled = false;
                }
            });

            // ============================================
            // Helper: Get classes with missing attendance
            // ============================================
            function getClassesWithMissingAttendance() {
                const missing = [];
                const rows = attendanceTableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    if (row.classList.contains('table-secondary')) return;
                    if (row.querySelector('.attendance-empty-state')) return;

                    const cells = row.querySelectorAll('td');
                    if (cells.length < 13) return;

                    const className = cells[0].textContent.trim();
                    const regBoys = parseInt(cells[1].textContent.trim()) || 0;
                    const regGirls = parseInt(cells[2].textContent.trim()) || 0;
                    const totalRegistered = regBoys + regGirls;

                    if (totalRegistered === 0) return;

                    const attendedBoys = parseInt(cells[4].textContent.trim()) || 0;
                    const attendedGirls = parseInt(cells[5].textContent.trim()) || 0;
                    const absentBoys = parseInt(cells[7].textContent.trim()) || 0;
                    const absentGirls = parseInt(cells[8].textContent.trim()) || 0;
                    const permBoys = parseInt(cells[10].textContent.trim()) || 0;
                    const permGirls = parseInt(cells[11].textContent.trim()) || 0;

                    const hasAttendanceData =
                        attendedBoys > 0 || attendedGirls > 0 ||
                        absentBoys > 0 || absentGirls > 0 ||
                        permBoys > 0 || permGirls > 0;

                    if (!hasAttendanceData) {
                        missing.push(className);
                    }
                });

                return missing;
            }

            // ============================================
            // Helper: Go to Section B
            // ============================================
            function goToSectionB() {
                sectionA.classList.remove('active');
                sectionB.classList.add('active');

                progressBar.style.width = '100%';
                steps[0].classList.remove('active');
                steps[0].classList.add('completed');
                steps[1].classList.add('active');

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // ============================================
            // Prompt: Missing Attendance
            // ============================================
            function showMissingAttendancePrompt(missingClasses) {
                const listHtml = missingClasses
                    .map(c => `<li><i class="fas fa-exclamation-circle me-2"></i>${c}</li>`)
                    .join('');

                const modalHtml = `
                    <div class="modal fade" id="missingAttendanceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Missing Attendance Data
                                    </h5>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-2">
                                        The following classes have not submitted today's Attendance!
                                    </p>
                                    <ul class="list-unstyled missing-list">
                                        ${listHtml}
                                    </ul>
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Skip to Proceed or re-fetch attendance again?
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" id="cancelMissingAttendance">
                                        <i class="fas fa-times me-1"></i> Re-fetch
                                    </button>
                                    <button type="button" class="btn btn-success" id="skipMissingAttendance">
                                        <i class="fas fa-forward me-1"></i> Proceed
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                const existing = document.getElementById('missingAttendanceModal');
                if (existing) existing.remove();

                document.body.insertAdjacentHTML('beforeend', modalHtml);

                const modalEl = document.getElementById('missingAttendanceModal');
                missingAttendanceModalInstance = new bootstrap.Modal(modalEl, {
                    backdrop: 'static',
                    keyboard: false
                });

                document.getElementById('skipMissingAttendance').addEventListener('click', function() {
                    missingAttendanceModalInstance.hide();
                    goToSectionB();
                });

                document.getElementById('cancelMissingAttendance').addEventListener('click', function() {
                    missingAttendanceModalInstance.hide();
                    fetchBtn.classList.add('btn-danger');
                    setTimeout(() => {
                        fetchBtn.classList.remove('btn-danger');
                    }, 2000);
                });

                modalEl.addEventListener('hidden.bs.modal', function() {
                    modalEl.remove();
                    missingAttendanceModalInstance = null;
                }, {
                    once: true
                });

                missingAttendanceModalInstance.show();
            }
        });
    </script>
@endsection
