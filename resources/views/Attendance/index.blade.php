@extends('SRTDashboard.frame')
@section('content')

    {{-- Modern Typography --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-hex: #4361ee;
            --present-color: #2ec4b6;
            --absent-color: #e71d36;
            --permit-color: #ff9f1c;
            --bg-main: #f8f9fe;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: #2b2d42;
        }

        /* Minimalist Header */
        .titan-header {
            padding: 2rem 0;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 2rem;
        }

        /* Floating Action Bar */
        .action-bar {
            position: sticky;
            top: 20px;
            z-index: 1020;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 100px;
            padding: 0.6rem 1.5rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Row System */
        .student-entry {
            background: white;
            border-radius: 14px;
            margin-bottom: 10px;
            padding: 1rem 1.5rem;
            display: grid;
            grid-template-columns: 0.5fr 2.5fr 1fr 2fr;
            align-items: center;
            transition: all 0.25s ease;
            border-left: 5px solid transparent;
        }

        /* Dynamic Row States */
        .student-entry:has(input[value="present"]:checked) {
            border-left-color: var(--present-color);
            background: #f0fdfa;
        }

        .student-entry:has(input[value="absent"]:checked) {
            border-left-color: var(--absent-color);
            background: #fff1f2;
        }

        .student-entry:has(input[value="permission"]:checked) {
            border-left-color: var(--permit-color);
            background: #fffbeb;
        }

        /* Highlight unselected students */
        .student-entry.unselected {
            border-left-color: #ff6b6b;
            background: #fff5f5;
            box-shadow: 0 0 0 2px #ff6b6b inset;
        }

        .student-entry:hover {
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        }

        .student-num {
            font-weight: 800;
            color: #ced4da;
            font-size: 0.9rem;
        }

        .student-name {
            font-weight: 700;
            font-size: 1.05rem;
            color: #1a1c23;
            letter-spacing: -0.3px;
        }

        .student-meta {
            font-size: 0.8rem;
            color: #8d99ae;
            font-weight: 500;
        }

        /* Modern Toggle Buttons */
        .toggle-pill {
            display: flex;
            background: #f1f3f5;
            padding: 4px;
            border-radius: 12px;
            width: fit-content;
            margin-left: auto;
        }

        .toggle-item {
            display: none;
        }

        .toggle-label {
            padding: 8px 18px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            margin-bottom: 0;
            color: #adb5bd;
        }

        .toggle-item[value="present"]:checked+.toggle-label {
            background: var(--present-color);
            color: white;
        }

        .toggle-item[value="absent"]:checked+.toggle-label {
            background: var(--absent-color);
            color: white;
        }

        .toggle-item[value="permission"]:checked+.toggle-label {
            background: var(--permit-color);
            color: white;
        }

        /* Warning indicator */
        .missing-warning {
            position: relative;
        }

        .missing-warning::after {
            content: "⚠️";
            position: absolute;
            right: -25px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
        }

        /* Responsive Mobile Grid */
        @media (max-width: 992px) {
            .student-entry {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 1.5rem;
                gap: 12px;
                position: relative;
            }

            .toggle-pill {
                margin: 0 auto;
            }

            .student-num {
                display: none;
            }
        }

        /* Buttons */
        .btn-titan {
            background: var(--primary-hex);
            color: white;
            border-radius: 50px;
            padding: 10px 28px;
            font-weight: 700;
            border: none;
            transition: 0.3s;
        }

        .btn-titan:hover {
            background: #334bc9;
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }

        .btn-titan:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Mobile Select All Button */
        .select-all-mobile {
            display: none;
        }

        @media (max-width: 768px) {
            .select-all-desktop {
                display: none;
            }

            .select-all-mobile {
                display: inline-flex;
            }

            .action-bar {
                border-radius: 20px;
                justify-content: center;
            }
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .loading-spinner {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }
    </style>

    <div class="container py-4">
        {{-- Top Header --}}
        <div class="titan-header d-flex justify-content-between align-items-end p-3"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius:12px;">
            <div>
                <span class="badge bg-primary mb-2 px-3 py-2 rounded-pill">ATTENDANCE MODULE</span>
                <h1 class="fw-800 display-6 m-0 text-white">{{ strtoupper($student_class->class_name) }}</h1>
                <p class="text-white mt-1">Manage and track daily student Attendance</p>
            </div>
            <div class="text-end">
                <h5 class="fw-bold mb-0 text-white">{{ \Carbon\Carbon::parse($selectedDate)->format('l') }}</h5>
                <p class="text-white small mb-0">{{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</p>
            </div>
        </div>

        @if (!$attendanceExists)
            {{-- Floating Action Bar --}}
            <div class="action-bar px-4">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="d-flex align-items-center bg-light rounded-pill px-3 py-1">
                        <i class="fas fa-users text-muted me-2 small"></i>
                        <span class="fw-bold small">{{ count($studentList) }} Students</span>
                    </div>
                    <div id="liveStats" class="small fw-bold text-success">
                        <span id="pCount">0</span> marked present |
                        <span id="unselectedCount" class="text-danger">0</span> pending
                    </div>
                </div>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <form action="{{ route('get.student.list', ['class' => Hashids::encode($myClass->first()->id)]) }}"
                        method="GET" id="dateForm" class="me-2">
                        <input type="date" name="attendance_date" value="{{ $selectedDate }}"
                            class="form-control form-control-sm border-0 bg-transparent fw-bold text-primary"
                            onchange="this.form.submit()">
                    </form>

                    <!-- Desktop Select All Button -->
                    <button class="btn btn-link text-dark text-decoration-none fw-bold small select-all-desktop"
                        id="markAllBtn" type="button">
                        <i class="fas fa-check-double"></i> Select All
                    </button>

                    <!-- Mobile Select All Button -->
                    <button class="btn btn-outline-success btn-sm select-all-mobile"
                        id="markAllBtnMobile" type="button">
                        <i class="fas fa-check-double"></i> All Present
                    </button>

                    <button type="button" id="submitBtn" class="btn btn-titan shadow-sm">
                        <i class="fas fa-send"></i> Submit Report
                    </button>
                </div>
            </div>

            {{-- Loading Overlay --}}
            <div class="loading-overlay" id="loadingOverlay">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0">Submitting attendance...</p>
                </div>
            </div>

            {{-- Main Form List --}}
            <form id="mainForm"
                action="{{ route('store.attendance', ['student_class' => Hashids::encode($student_class->id)]) }}"
                method="POST">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

                @foreach ($studentList as $student)
                    <div class="student-entry" data-student-id="{{ $student->id }}">
                        <div class="student-num">#{{ $loop->iteration }}</div>

                        <div class="text-start">
                            <div class="student-name">
                                {{ ucwords(strtolower($student->first_name . ' ' . $student->last_name)) }}
                            </div>
                            <div class="student-meta">{{ $student->admission_number }} •
                                {{ strtoupper($student->gender) }}</div>
                        </div>

                        <div class="d-none d-lg-block text-center">
                            <span class="badge rounded-pill bg-light text-dark border px-3">Stream
                                {{ strtoupper($student->group) }}</span>
                        </div>

                        <div class="attendance-action">
                            <div class="toggle-pill">
                                <input type="radio" name="attendance_status[{{ $student->id }}]"
                                    id="p-{{ $student->id }}" value="present" class="toggle-item status-input">
                                <label class="toggle-label" for="p-{{ $student->id }}">PRESENT</label>

                                <input type="radio" name="attendance_status[{{ $student->id }}]"
                                    id="a-{{ $student->id }}" value="absent" class="toggle-item status-input">
                                <label class="toggle-label" for="a-{{ $student->id }}">ABSENT</label>

                                <input type="radio" name="attendance_status[{{ $student->id }}]"
                                    id="l-{{ $student->id }}" value="permission" class="toggle-item status-input">
                                <label class="toggle-label" for="l-{{ $student->id }}">PERMIT</label>
                            </div>
                        </div>

                        <input type="hidden" name="student_id[]" value="{{ $student->id }}">
                        <input type="hidden" name="group[{{ $student->id }}]" value="{{ $student->group }}">
                    </div>
                @endforeach
            </form>
        @else
            <div class="card border-0 shadow-lg text-center p-5 rounded-4">
                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 mx-auto"
                    style="width: 80px; height: 80px;">
                    <i class="fas fa-check fa-2x"></i>
                </div>
                <h2 class="fw-800">Mission Accomplished!</h2>
                <p class="text-muted">The attendance report for this session has already been synchronized with the
                    database.</p>
                <div class="mt-4">
                    <a href="{{ route('home') }}" class="btn btn-titan px-5">Back to Dashboard</a>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll('.status-input');
            const pCountLabel = document.getElementById('pCount');
            const unselectedCountSpan = document.getElementById('unselectedCount');
            const markAllBtn = document.getElementById('markAllBtn');
            const markAllBtnMobile = document.getElementById('markAllBtnMobile');
            const submitBtn = document.getElementById('submitBtn');
            const mainForm = document.getElementById('mainForm');
            const loadingOverlay = document.getElementById('loadingOverlay');

            // Function to update statistics and highlight unselected students
            function updateStats() {
                const presentCount = document.querySelectorAll('.status-input[value="present"]:checked').length;
                const absentCount = document.querySelectorAll('.status-input[value="absent"]:checked').length;
                const permitCount = document.querySelectorAll('.status-input[value="permission"]:checked').length;
                const totalSelected = presentCount + absentCount + permitCount;
                const totalStudents = {{ count($studentList) }};
                const unselectedCount = totalStudents - totalSelected;

                pCountLabel.innerText = presentCount;
                if (unselectedCountSpan) {
                    unselectedCountSpan.innerText = unselectedCount;
                }

                // Highlight unselected student rows
                document.querySelectorAll('.student-entry').forEach(entry => {
                    const radioGroup = entry.querySelectorAll('.status-input');
                    let isSelected = false;
                    radioGroup.forEach(radio => {
                        if (radio.checked) isSelected = true;
                    });

                    if (!isSelected) {
                        entry.classList.add('unselected');
                    } else {
                        entry.classList.remove('unselected');
                    }
                });

                // Enable/disable submit button based on all selections
                if (submitBtn) {
                    if (unselectedCount === 0 && totalStudents > 0) {
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '1';
                    } else {
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.6';
                    }
                }
            }

            // Add change event to all radio inputs
            inputs.forEach(input => {
                input.addEventListener('change', updateStats);
            });

            // Select All function
            function selectAllPresent() {
                document.querySelectorAll('.status-input[value="present"]').forEach(radio => {
                    radio.checked = true;
                });
                updateStats();

                // Remove unselected class from all rows
                document.querySelectorAll('.student-entry').forEach(entry => {
                    entry.classList.remove('unselected');
                });

                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: 'All students marked as present',
                    showConfirmButton: false,
                    timer: 2000
                });
            }

            // Desktop Select All
            if (markAllBtn) {
                markAllBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectAllPresent();
                });
            }

            // Mobile Select All
            if (markAllBtnMobile) {
                markAllBtnMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectAllPresent();
                });
            }

            // Form submission with validation
            if (submitBtn && mainForm) {
                submitBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const totalStudents = {{ count($studentList) }};
                    const presentCount = document.querySelectorAll('.status-input[value="present"]:checked').length;
                    const absentCount = document.querySelectorAll('.status-input[value="absent"]:checked').length;
                    const permitCount = document.querySelectorAll('.status-input[value="permission"]:checked').length;
                    const totalSelected = presentCount + absentCount + permitCount;

                    if (totalSelected < totalStudents) {
                        const remaining = totalStudents - totalSelected;
                        Swal.fire({
                            title: 'Incomplete Attendance!',
                            html: `You have <strong>${remaining}</strong> student(s) without attendance status.<br><br>Please mark attendance for all students before submitting.`,
                            icon: 'warning',
                            confirmButtonColor: '#4361ee',
                            confirmButtonText: 'Go Back'
                        });
                        return;
                    }

                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Finalize Attendance?',
                        html: `<strong>Present:</strong> ${presentCount}<br>
                               <strong>Absent:</strong> ${absentCount}<br>
                               <strong>On Permit:</strong> ${permitCount}<br><br>
                               This action will lock today's records.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#4361ee',
                        cancelButtonColor: '#e71d36',
                        confirmButtonText: 'Yes, Submit Report',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading overlay
                            loadingOverlay.style.display = 'flex';

                            // Submit the form
                            mainForm.submit();
                        }
                    });
                });
            }

            // Initial stats update
            updateStats();
        });
    </script>

@endsection
