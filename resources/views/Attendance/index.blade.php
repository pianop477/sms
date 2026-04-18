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
    .student-entry:has(input[value="present"]:checked) { border-left-color: var(--present-color); background: #f0fdfa; }
    .student-entry:has(input[value="absent"]:checked) { border-left-color: var(--absent-color); background: #fff1f2; }
    .student-entry:has(input[value="permission"]:checked) { border-left-color: var(--permit-color); background: #fffbeb; }

    .student-entry:hover {
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
    }

    .student-num { font-weight: 800; color: #ced4da; font-size: 0.9rem; }
    .student-name { font-weight: 700; font-size: 1.05rem; color: #1a1c23; letter-spacing: -0.3px; }
    .student-meta { font-size: 0.8rem; color: #8d99ae; font-weight: 500; }

    /* Modern Toggle Buttons */
    .toggle-pill {
        display: flex;
        background: #f1f3f5;
        padding: 4px;
        border-radius: 12px;
        width: fit-content;
        margin-left: auto;
    }

    .toggle-item { display: none; }
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

    .toggle-item[value="present"]:checked + .toggle-label { background: var(--present-color); color: white; }
    .toggle-item[value="absent"]:checked + .toggle-label { background: var(--absent-color); color: white; }
    .toggle-item[value="permission"]:checked + .toggle-label { background: var(--permit-color); color: white; }

    /* Responsive Mobile Grid */
    @media (max-width: 992px) {
        .student-entry {
            grid-template-columns: 1fr;
            text-align: center;
            padding: 1.5rem;
            gap: 12px;
        }
        .toggle-pill { margin: 0 auto; }
        .student-num { display: none; }
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
</style>

<div class="container py-4">
    {{-- Top Header --}}
    <div class="titan-header d-flex justify-content-between align-items-end p-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius:12px;">
        <div>
            <span class="badge bg-primary mb-2 px-3 py-2 rounded-pill">ATTENDANCE MODULE</span>
            <h1 class="fw-800 display-6 m-0 text-white">{{ strtoupper($student_class->class_name) }}</h1>
            <p class="text-white mt-1">Manage and track daily student Attendance</p>
        </div>
        <div class="d-none d-md-block text-end">
            <h5 class="fw-bold mb-0 text-white">{{ \Carbon\Carbon::parse($selectedDate)->format('l') }}</h5>
            <p class="text-white small mb-0">{{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</p>
        </div>
    </div>

    @if(!$attendanceExists)
    {{-- Floating Action Bar --}}
    <div class="action-bar px-4">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center bg-light rounded-pill px-3 py-1">
                <i class="fas fa-users text-muted me-2 small"></i>
                <span class="fw-bold small">{{ count($studentList) }} Students</span>
            </div>
            <div id="liveStats" class="small fw-bold text-success d-none d-md-block">
                <span id="pCount">0</span> marked present
            </div>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <form action="{{ route('get.student.list', ['class' => Hashids::encode($myClass->first()->id)]) }}" method="GET" id="dateForm">
                <input type="date" name="attendance_date" value="{{ $selectedDate }}" class="form-control form-control-sm border-0 bg-transparent fw-bold text-primary" onchange="this.form.submit()">
            </form>
            <div class="vr mx-2 text-muted opacity-25 d-none d-md-block"></div>
            <button class="btn btn-link text-dark text-decoration-none fw-bold small d-none d-md-block" id="markAllBtn">
                Select All
            </button>
            <button form="mainForm" type="submit" class="btn btn-titan shadow-sm">
                <i class="fas fa-send"></i> Submit
            </button>
        </div>
    </div>

    {{-- Main Form List --}}
    <form id="mainForm" action="{{ route('store.attendance', ['student_class' => Hashids::encode($student_class->id)]) }}" method="POST">
        @csrf
        <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

        @foreach ($studentList as $student)
        <div class="student-entry">
            <div class="student-num">#{{ $loop->iteration }}</div>

            <div class="text-start">
                <div class="student-name">{{ ucwords(strtolower($student->first_name . ' ' . $student->last_name)) }}</div>
                <div class="student-meta">{{ $student->admission_number }} • {{ strtoupper($student->gender) }}</div>
            </div>

            <div class="d-none d-lg-block text-center">
                <span class="badge rounded-pill bg-light text-dark border px-3">Stream {{ strtoupper($student->group) }}</span>
            </div>

            <div class="attendance-action">
                <div class="toggle-pill">
                    <input type="radio" name="attendance_status[{{ $student->id }}]" id="p-{{ $student->id }}" value="present" class="toggle-item status-input" required>
                    <label class="toggle-label" for="p-{{ $student->id }}">PRESENT</label>

                    <input type="radio" name="attendance_status[{{ $student->id }}]" id="a-{{ $student->id }}" value="absent" class="toggle-item status-input">
                    <label class="toggle-label" for="a-{{ $student->id }}">ABSENT</label>

                    <input type="radio" name="attendance_status[{{ $student->id }}]" id="l-{{ $student->id }}" value="permission" class="toggle-item status-input">
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
        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 mx-auto" style="width: 80px; height: 80px;">
            <i class="fas fa-check fa-2x"></i>
        </div>
        <h2 class="fw-800">Mission Accomplished!</h2>
        <p class="text-muted">The attendance report for this session has already been synchronized with the database.</p>
        <div class="mt-4">
            <a href="{{ route('home') }}" class="btn btn-titan px-5">Back to Dashboard</a>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const inputs = document.querySelectorAll('.status-input');
    const pCountLabel = document.getElementById('pCount');
    const markAllBtn = document.getElementById('markAllBtn');

    function updateStats() {
        const count = document.querySelectorAll('.status-input[value="present"]:checked').length;
        pCountLabel.innerText = count;
    }

    inputs.forEach(input => {
        input.addEventListener('change', updateStats);
    });

    markAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.status-input[value="present"]').forEach(radio => {
            radio.checked = true;
        });
        updateStats();
        Swal.fire({
            toast: true, position: 'bottom-end', icon: 'success', title: 'Smart-selected all as present', showConfirmButton: false, timer: 2000
        });
    });

    // Form confirmation
    document.getElementById('mainForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Finalize Attendance?',
            text: "This action will lock today's records.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4361ee',
            confirmButtonText: 'Confirm & Sync'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>

@endsection
