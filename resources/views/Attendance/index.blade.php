@extends('SRTDashboard.frame')
@section('content')

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

    .titan-header {
        padding: 2rem 0;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }

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

    .class-selector {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .class-selector select {
        border: 1px solid #e9ecef;
        border-radius: 50px;
        padding: 6px 16px;
        font-weight: 600;
        background: white;
        color: #2b2d42;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .class-selector select:focus {
        outline: none;
        border-color: var(--primary-hex);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    }

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

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
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

    input[type="date"]:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Debugging banner - remove in production */
    .debug-banner {
        background: #fff3cd;
        color: #856404;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 13px;
        display: none;
    }
</style>

<div class="container py-4">
    {{-- Top Header --}}
    <div class="titan-header d-flex justify-content-between align-items-end p-3"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius:12px;">
        <div>
            <span class="badge bg-primary mb-2 px-3 py-2 rounded-pill">ATTENDANCE MODULE</span>
            <h1 class="fw-800 display-6 m-0 text-white">
                @if($myClasses->count() > 1)
                    <span id="classDisplayName">{{ strtoupper($student_class->class_name) }}</span>
                @else
                    {{ strtoupper($student_class->class_name) }}
                @endif
            </h1>
            <p class="text-white mt-1">Manage and track daily student Attendance</p>
        </div>
        <div class="text-end">
            <h5 class="fw-bold mb-0 text-white">{{ \Carbon\Carbon::parse($selectedDate)->format('l') }}</h5>
            <p class="text-white small mb-0">{{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</p>
        </div>
    </div>

    {{-- Filter Bar - Always visible --}}
    <div class="action-bar px-4">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <!-- Class Selector Dropdown (Always visible) -->
            <div class="class-selector">
                <i class="fas fa-chalkboard text-muted"></i>
                <select id="classSelector" onchange="switchClass(this.value)">
                    @foreach($myClasses as $cls)
                        @php
                            $grade = $cls->grade;
                            $encodedClassTeacherId = \Vinkla\Hashids\Facades\Hashids::encode($cls->id);
                        @endphp
                        <option value="{{ $encodedClassTeacherId }}"
                            {{ $selectedClassTeacher->id == $cls->id ? 'selected' : '' }}>
                            {{ strtoupper($grade->class_name ?? 'Unknown') }} ({{ strtoupper($cls->group) }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date Picker --}}
            <form action="{{ route('get.student.list', ['class' => $class]) }}"
                method="GET" id="dateForm" class="me-2">
                <input type="date"
                       name="attendance_date"
                       value="{{ $selectedDate }}"
                       min="{{ $minDate }}"
                       max="{{ $maxDate }}"
                       class="form-control form-control-sm border-0 bg-transparent fw-bold text-primary"
                       onchange="validateAndSubmit(this)">
            </form>

            @if (!$attendanceExists)
                <div class="d-flex align-items-center bg-light rounded-pill px-3 py-1">
                    <i class="fas fa-users text-muted me-2 small"></i>
                    <span class="fw-bold small">{{ count($studentList) }} Students</span>
                </div>
                <div id="liveStats" class="small fw-bold text-success">
                    <span id="pCount">0</span> marked present |
                    <span id="unselectedCount" class="text-danger">0</span> pending
                </div>
            @endif

            {{-- Status badge --}}
            @if($attendanceExists)
                <span class="badge bg-success text-white px-3 py-2">
                    <i class="fas fa-check-circle me-1"></i> Attendance Submitted
                </span>
            @endif
        </div>

        <div class="d-flex gap-2 align-items-center flex-wrap">
            @if (!$attendanceExists)
                <!-- Select All Buttons -->
                <button class="btn btn-link text-dark text-decoration-none fw-bold small select-all-desktop"
                    id="markAllBtn" type="button">
                    <i class="fas fa-check-double"></i> Select All Present
                </button>

                <button class="btn btn-outline-success btn-sm select-all-mobile"
                    id="markAllBtnMobile" type="button">
                    <i class="fas fa-check-double"></i> All Present
                </button>

                <button type="button" id="submitBtn" class="btn btn-titan shadow-sm" disabled>
                    <i class="fas fa-send"></i> Submit Report
                </button>
            @else
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Dashboard
                </a>
            @endif
        </div>
    </div>

    {{-- Main Content --}}
    @if (!$attendanceExists)
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
            <h2 class="fw-800">Attendance Already Submitted!</h2>
            <p class="text-muted">The attendance report for <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('l, F d, Y') }}</strong> has already been synchronized with the database.</p>
            <div class="mt-3">
                <small class="text-muted">You can still change the date or class using the filters above.</small>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Debugging - show class info
    document.addEventListener("DOMContentLoaded", function() {
        const debugBanner = document.getElementById('debugBanner');
        const debugInfo = document.getElementById('debugInfo');

        // Show debug info (remove in production)
        const selectedClassId = "{{ $class }}";
        const selectedDate = "{{ $selectedDate }}";
        const attendanceExists = {{ $attendanceExists ? 'true' : 'false' }};
        const studentCount = {{ count($studentList) }};

        debugInfo.innerHTML = `
            Class: ${selectedClassId} |
            Date: ${selectedDate} |
            Attendance: ${attendanceExists ? 'Submitted' : 'Not Submitted'} |
            Students: ${studentCount}
        `;
        debugBanner.style.display = 'block';

        // Auto-hide debug after 5 seconds
        setTimeout(() => {
            debugBanner.style.display = 'none';
        }, 5000);
    });

    // Switch class function - FIXED
    function switchClass(classTeacherId) {
        // Get current date from the form
        const dateInput = document.querySelector('input[name="attendance_date"]');
        const currentDate = dateInput ? dateInput.value : '';

        // Build URL with class teacher ID as route parameter
        let url = "{{ route('get.student.list', ['class' => '__CLASS_ID__']) }}";
        url = url.replace('__CLASS_ID__', classTeacherId);

        // Add date parameter if exists
        if (currentDate) {
            url += '?attendance_date=' + currentDate;
        }

        console.log('Switching to class:', classTeacherId, 'URL:', url);
        window.location.href = url;
    }

    // Date validation
    function validateAndSubmit(input) {
        const selectedDate = new Date(input.value + 'T00:00:00');
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate.getDay() === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date',
                text: 'Attendance cannot be taken on Sundays (school holiday). Please select another date.',
                confirmButtonColor: '#4361ee'
            });
            input.value = '';
            return false;
        }

        const diffDays = Math.floor((today - selectedDate) / (1000 * 60 * 60 * 24));
        if (diffDays > 7) {
            Swal.fire({
                icon: 'error',
                title: 'Date Too Old',
                text: 'You can only take attendance for dates within the last 7 days.',
                confirmButtonColor: '#4361ee'
            });
            input.value = '';
            return false;
        }

        input.form.submit();
    }

    // Rest of your JavaScript (updateStats, selectAll, submit, etc.)
    document.addEventListener("DOMContentLoaded", function() {
        const inputs = document.querySelectorAll('.status-input');
        const pCountLabel = document.getElementById('pCount');
        const unselectedCountSpan = document.getElementById('unselectedCount');
        const markAllBtn = document.getElementById('markAllBtn');
        const markAllBtnMobile = document.getElementById('markAllBtnMobile');
        const submitBtn = document.getElementById('submitBtn');
        const mainForm = document.getElementById('mainForm');
        const loadingOverlay = document.getElementById('loadingOverlay');

        function updateStats() {
            if (!inputs.length) return;

            const presentCount = document.querySelectorAll('.status-input[value="present"]:checked').length;
            const absentCount = document.querySelectorAll('.status-input[value="absent"]:checked').length;
            const permitCount = document.querySelectorAll('.status-input[value="permission"]:checked').length;
            const totalSelected = presentCount + absentCount + permitCount;
            const totalStudents = {{ count($studentList) }};
            const unselectedCount = totalStudents - totalSelected;

            if (pCountLabel) pCountLabel.innerText = presentCount;
            if (unselectedCountSpan) unselectedCountSpan.innerText = unselectedCount;

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

        inputs.forEach(input => {
            input.addEventListener('change', updateStats);
        });

        function selectAllPresent() {
            document.querySelectorAll('.status-input[value="present"]').forEach(radio => {
                radio.checked = true;
            });
            updateStats();

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

        if (markAllBtn) {
            markAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                selectAllPresent();
            });
        }

        if (markAllBtnMobile) {
            markAllBtnMobile.addEventListener('click', function(e) {
                e.preventDefault();
                selectAllPresent();
            });
        }

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
                        loadingOverlay.style.display = 'flex';
                        mainForm.submit();
                    }
                });
            });
        }

        if (inputs.length) updateStats();
    });
</script>

@endsection
