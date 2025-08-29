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
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.85rem;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .form-control:focus, .form-select:focus {
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

        input:checked + .slider {
            background-color: var(--success-color);
        }

        input:checked + .slider:before {
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

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        table thead {
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
            color: white;
        }

        table th, table td {
            text-align: center;
            vertical-align: middle;
            padding: 1rem 0.5rem;
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
    </style>
    <div class="form-container">
        <div class="card shadow-sm">
            <div class="card-header text-white text-center">
                <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> SCHOOL DAILY REPORT</h4>
                <p class="mb-0 mt-1 text-white">School Routine Tracking System</p>
            </div>
            <div class="progress-container">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="step-indicator">
                    <div class="step active">SECTION A: STUDENT'S ATTENDANCES</div>
                    <div class="step">SECTION B: DAILY ACTIVITIES</div>
                </div>
            </div>

            @if ($reports->isEmpty())
                <form id="dailyReportForm" action="{{route('tod.report.store')}}" method="POST">
                    @csrf
                    <!-- Section A - Student Attendance -->
                    <div class="form-section active" id="section-a">
                        <div class="section-title">
                            <i class="fas fa-chart-bar"></i>Attendance Summary
                        </div>

                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>Click the button below to fetch attendance records for the date {{\Carbon\Carbon::parse(now())->format('d-m-Y')}}</div>
                        </div>

                        <div class="d-flex justify-content-center mb-4">
                            <button type="button" id="fetchAttendanceBtn" class="btn btn-primary">
                                <i class="fas fa-sync me-2"></i> Fetch Attendance Records
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Class</th>
                                        <th colspan="3">Registered</th>
                                        <th colspan="3">Attended</th>
                                        <th colspan="3">Absentees</th>
                                        <th colspan="3">Permission</th>
                                    </tr>
                                    <tr>
                                        <th>Boys</th><th>Girls</th><th>Total</th>
                                        <th>Boys</th><th>Girls</th><th>Total</th>
                                        <th>Boys</th><th>Girls</th><th>Total</th>
                                        <th>Boys</th><th>Girls</th><th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="attendanceTableBody">
                                    <tr>
                                        <td colspan="16" class="text-muted text-center py-4">
                                            <i class="fas fa-database me-2"></i> No records fetched yet
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="action-buttons">
                            <button type="button" id="proceedToSectionB" class="btn btn-success float-right">
                                <i class="fas fa-arrow-right me-2"></i> Confirm & Proceed
                            </button>
                        </div>
                    </div>

                    <!-- Section B - Daily Activities -->
                    <div class="form-section" id="section-b">
                        <div class="form-group mb-4">
                            <label for="report_date" class="form-label required-field">
                                <i class="fas fa-calendar-alt"></i>Report Date
                            </label>
                            <input type="date" name="report_date" id="report_date" class="form-control" value="{{ date('Y-m-d') }}" required readonly>
                            <div class="error-message" id="report_date_error">Please select the valid report date.</div>
                        </div>

                        <div class="section-title">
                            <i class="fas fa-clock"></i>Daily Schedule
                        </div>

                        <div class="form-group mb-4">
                            <label for="parade" class="form-label required-field">
                                <i class="fas fa-users"></i>Morning Parade
                            </label>
                            <textarea name="parade" id="parade" rows="3" class="form-control" placeholder="Enter parade details..." required></textarea>
                            <div class="error-message" id="parade_error">Please fill out parade details.</div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="break_time" class="form-label required-field">
                                <i class="fas fa-coffee"></i>Break Time
                            </label>
                            <textarea name="break_time" id="break_time" rows="3" class="form-control" placeholder="Enter break time details..." required></textarea>
                            <div class="error-message" id="break_time_error">Please fill out Break time details. E.g. Breakfast etc.</div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="lunch_time" class="form-label required-field">
                                <i class="fas fa-utensils"></i>Lunch Time
                            </label>
                            <textarea name="lunch_time" id="lunch_time" rows="3" class="form-control" placeholder="Enter lunch time details..." required></textarea>
                            <div class="error-message" id="lunch_time_error">Please fill out Lunch time details E.g. Lunch food etc.</div>
                        </div>

                        <div class="section-title">
                            <i class="fas fa-calendar-check"></i>Attendance & Events
                        </div>

                        <div class="form-group mb-4">
                            <label for="teachers_attendance" class="form-label required-field">
                                <i class="fas fa-chalkboard-teacher"></i>Teachers Attendance
                            </label>
                            <textarea name="teachers_attendance" id="teachers_attendance" rows="3" class="form-control" placeholder="Enter teachers attendance details..." required></textarea>
                            <div class="error-message" id="teachers_attendance_error">Please fill out the teachers attendance details</div>
                        </div>

                        <div class="toggle-container">
                            <span class="toggle-label">Was there a special event that disrupted the school schedule?</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="event_toggle" name="event_toggle">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="form-group mb-4 event-field" id="event_field">
                            <label for="daily_new_event" class="form-label">
                                <i class="fas fa-calendar-plus"></i>Event Details
                            </label>
                            <textarea name="daily_new_event" id="daily_new_event" rows="3" class="form-control" placeholder="Enter event details here...."></textarea>
                        </div>

                        <div class="section-title">
                            <i class="fas fa-comment-dots"></i>Remarks
                        </div>

                        <div class="form-group mb-4">
                            <label for="tod_remarks" class="form-label required-field">
                                <i class="fas fa-sticky-note"></i>Teacher on Duty Remarks
                            </label>
                            <textarea name="tod_remarks" id="tod_remarks" rows="3" class="form-control" placeholder="Enter your remarks..." required></textarea>
                            <div class="error-message" id="tod_remarks_error">Please fill out your overall general remarks for the day schedule</div>
                        </div>

                        <div class="action-buttons">
                            <button type="button" id="backToSectionA" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Attendance
                            </button>
                            <button type="reset" class="btn btn-outline-secondary" id="resetFormBtn">
                                <i class="fas fa-redo-alt me-2"></i> Reset Form
                            </button>
                            <button type="submit" class="btn btn-success float-right">
                                <i class="fas fa-paper-plane me-2"></i> Submit Final Report
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="alert alert-success text-center my-4 fs-4" role="alert">
                    <i class="fas fa-check-square-o me-2"></i>
                    Daily School report already submitted for the date ({{ \Carbon\Carbon::parse(now())->format('d-m-Y') }}).
                </div>
            @endif
            <div class="card-footer text-muted text-center py-3">
                <small><i class="fas fa-info-circle me-1"></i> Once submitted, your report will be uneditable.</small>
            </div>
        </div>
    </div>
    <div class="notification error-notification" id="errorNotification">
        <i class="fas fa-exclamation-circle me-2"></i> <span id="errorMessage">Please fill out all required fields before you proceed.</span>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
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
            const notification = document.getElementById('saveNotification');
            const errorNotification = document.getElementById('errorNotification');
            const errorMessage = document.getElementById('errorMessage');

            // Required fields
            const requiredFields = [
                'report_date',
                'parade',
                'break_time',
                'lunch_time',
                'teachers_attendance',
                'tod_remarks'
            ];

            // Event toggle functionality
            eventToggle.addEventListener('change', function() {
                eventField.style.display = this.checked ? 'block' : 'none';
            });

            // Reset form
            resetBtn.addEventListener('click', function() {
                // Hide all error messages and remove error borders
                document.querySelectorAll('.error-message').forEach(el => {
                    el.style.display = 'none';
                });
                document.querySelectorAll('.form-control').forEach(el => {
                    el.classList.remove('error-border');
                });

                // Hide event field if shown
                eventField.style.display = 'none';
                eventToggle.checked = false;

                // Reset date to today
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('report_date').value = today;
            });

            // Proceed to Section B
            proceedBtn.addEventListener('click', function() {
                // Validate if attendance data has been fetched
                if (attendanceTableBody.querySelector('input')) {
                    // Switch to Section B
                    sectionA.classList.remove('active');
                    sectionB.classList.add('active');

                    // Update progress
                    progressBar.style.width = '100%';
                    steps[0].classList.remove('active');
                    steps[1].classList.add('active');
                } else {
                    // Show error notification
                    errorMessage.textContent = 'Please fetch attendance records before proceeding.';
                    errorNotification.classList.add('show');
                    setTimeout(() => {
                        errorNotification.classList.remove('show');
                    }, 3000);

                    // Highlight fetch button
                    fetchBtn.classList.add('btn-danger');
                    setTimeout(() => {
                        fetchBtn.classList.remove('btn-danger');
                    }, 2000);
                }
            });

            // Back to Section A
            backBtn.addEventListener('click', function() {
                sectionB.classList.remove('active');
                sectionA.classList.add('active');

                // Update progress
                progressBar.style.width = '50%';
                steps[1].classList.remove('active');
                steps[0].classList.add('active');
            });

            // Add validation on input change
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', function() {
                        validateField(fieldId);
                    });
                }
            });

            // Validate a single field
            function validateField(fieldId) {
                const field = document.getElementById(fieldId);
                const errorElement = document.getElementById(fieldId + '_error');

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

            // Validate all required fields
            function validateAllFields() {
                let isValid = true;

                requiredFields.forEach(fieldId => {
                    if (!validateField(fieldId)) {
                        isValid = false;
                    }
                });

                return isValid;
            }

            // Form submission
            document.getElementById('dailyReportForm').addEventListener('submit', function(e) {
                // Validate all required fields before submission
                if (!validateAllFields()) {
                    e.preventDefault();

                    // Show error notification
                    errorNotification.classList.add('show');
                    setTimeout(() => {
                        errorNotification.classList.remove('show');
                    }, 3000);

                    // Scroll to first error
                    const firstErrorField = document.querySelector('.error-border');
                    if (firstErrorField) {
                        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } else {
                    // Show success notification
                    notification.classList.add('show');
                    setTimeout(() => {
                        notification.classList.remove('show');
                    }, 3000);
                }
            });

            // Fetch attendance data
            fetchBtn.addEventListener('click', async function() {
                fetchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Fetching, Please wait.....';
                fetchBtn.disabled = true;

                try {
                    const res = await fetch(`/api/attendance/fetch`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();

                    attendanceTableBody.innerHTML = "";

                    if (data.length === 0) {
                        attendanceTableBody.innerHTML = `
                            <tr>
                                <td colspan="16" class="text-muted text-center py-4">
                                    <i class="fas fa-database me-2"></i> No attendance records found for today.
                                </td>
                            </tr>`;
                    } else {
                        data.forEach(record => {
                            const isTotal = record.class_code === "TOTAL";

                            function highlightIfZero(value) {
                                return value === 0 ? 'class="table-danger"' : '';
                            }

                            attendanceTableBody.innerHTML += `
                                <tr ${isTotal ? 'class="table-secondary fw-bold"' : ''}>
                                    <td style="text-transform:uppercase">${record.class_code} ${record.stream ?? ''}</td>
                                    <td>${record.registered_boys}${!isTotal ? `<input type="hidden" name="attendance[${record.class_id}][registered_boys]" value="${record.registered_boys}">` : ''}</td>
                                    <td>${record.registered_girls}${!isTotal ? `<input type="hidden" name="attendance[${record.class_id}][registered_girls]" value="${record.registered_girls}">` : ''}</td>
                                    <td>${Number(record.registered_boys) + Number(record.registered_girls)}</td>
                                    <td ${highlightIfZero(record.attended_boys)}>${record.attended_boys}${!isTotal ? `<input type="hidden" name="attendance[${record.class_id}][present_boys]" value="${record.attended_boys}">` : ''}</td>
                                    <td ${highlightIfZero(record.attended_girls)}>${record.attended_girls}${!isTotal ? `<input type="hidden" name="attendance[${record.class_id}][present_girls]" value="${record.attended_girls}">` : ''}</td>
                                    <td ${highlightIfZero(record.attended_boys + record.attended_girls)}>${record.attended_boys + record.attended_girls}</td>
                                    <td ${highlightIfZero(record.absent_boys)}>${record.absent_boys}${!isTotal ? `<input type="hidden" name="attendance[${record.class_id}][absent_boys]" value="${record.absent_boys}">` : ''}</td>
                                    <td ${highlightIfZero(record.absent_girls)}>${record.absent_girls}${!isTotal ? `<input type="hidden" name="attendance[${record.class_id}][absent_girls]" value="${record.absent_girls}">` : ''}</td>
                                    <td ${highlightIfZero(record.absent_boys + record.absent_girls)}>${record.absent_boys + record.absent_girls}</td>
                                    <td ${highlightIfZero(record.permission_boys)}>${record.permission_boys}${!isTotal ? `<input type="hidden" name="attendance[${record.class_id}][permission_boys]" value="${record.permission_boys}">` : ''}</td>
                                    <td ${highlightIfZero(record.permission_girls)}>${record.permission_girls}${!isTotal ? `<input type="hidden" name="attendance[${record.class_id}][permission_girls]" value="${record.permission_girls}">` : ''}</td>
                                    <td ${highlightIfZero(record.permission_boys + record.permission_girls)}>${record.permission_boys + record.permission_girls}</td>
                                    ${!isTotal ? `<input type="hidden" name="attendance[${record.class_id}][group]" value="${record.stream || ''}">` : ''}
                                </tr>`;
                        });
                    }
                } catch (err) {
                    console.error(err);
                    errorMessage.textContent = 'Error fetching attendance records.';
                    errorNotification.classList.add('show');
                    setTimeout(() => {
                        errorNotification.classList.remove('show');
                    }, 3000);
                } finally {
                    fetchBtn.innerHTML = '<i class="fas fa-sync me-2"></i> Fetch Attendance Records';
                    fetchBtn.disabled = false;
                }
            });
        });
    </script>
@endsection
