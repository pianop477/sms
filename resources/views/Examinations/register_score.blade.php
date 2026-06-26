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
        /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        /* padding: 20px; */
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
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
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
        padding: 5px;
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

    .info-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        border: 1px solid #dee2e6;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-list li {
        padding: 10px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
    }

    .info-list li:last-child {
        border-bottom: none;
    }

    .info-list strong {
        min-width: 120px;
        display: inline-block;
        color: var(--dark);
    }

    .form-label {
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .required-star {
        color: var(--danger);
    }

    .form-control-custom {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 16px;
        width: 100%;
        transition: all 0.3s;
        background-color: white;
    }

    .form-control-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
    }

    .invalid-feedback {
        font-weight: 600;
        color: var(--danger);
        margin-top: 5px;
    }

    .text-danger small {
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #e9ecef;
    }

    .btn-warning-custom {
        background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
        border: none;
        border-radius: 50px;
        padding: 12px 25px;
        font-weight: 600;
        color: #856404;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-warning-custom:hover {
        background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
        color: #856404;
    }

    .btn-success-custom {
        background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
        border: none;
        border-radius: 50px;
        padding: 12px 25px;
        font-weight: 600;
        color: white;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-success-custom:hover {
        background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        color: white;
    }

    .alert-custom {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.25) 100%);
        border: 1px solid rgba(220, 53, 69, 0.3);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        backdrop-filter: blur(5px);
    }

    .instruction-text {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.25) 100%);
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        margin-bottom: 20px;
        border: 1px solid rgba(255, 193, 7, 0.3);
        font-weight: 600;
    }

    .table-container {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
    }

    .table-custom {
        margin-bottom: 0;
    }

    .table-custom thead th {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        /* padding: 15px; */
        font-weight: 600;
        text-align: center;
    }

    .table-custom tbody td {
        /* padding: 15px; */
        vertical-align: middle;
        border-color: #e9ecef;
    }

    .table-custom tbody tr:nth-child(even) {
        background-color: rgba(78, 84, 200, 0.05);
    }

    .table-custom tbody tr:hover {
        background-color: rgba(78, 84, 200, 0.1);
    }

    .score-input {
        width: auto;
        text-align: center;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        /* padding: 8px 12px; */
        transition: all 0.3s;
    }

    .score-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
    }

    .grade-input {
        width: 60px;
        text-align: center;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        /* padding: 8px 12px; */
        font-weight: bold;
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

    .divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--primary), transparent);
        margin: 20px 0;
        border: none;
    }

    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
        }

        .card-body {
            padding: 5px;
        }

        .header-title {
            font-size: 20px;
        }

        .table-responsive {
            font-size: 14px;
        }

        .score-input,
        .grade-input {
            width: 100%;
        }

        .info-list strong {
            min-width: 100px;
        }
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }
</style>

<div class="">
    <div class="glass-card">
        <div class="card-header-custom">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="header-title text-white">
                        <i class="fas fa-clipboard-list me-2"></i> Students Result Form
                    </h4>
                    <p class="mb-0 text-white"> Submit examination scores for students</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('score.prepare.form', ['id' => $id]) }}" class="btn btn-back float-right">
                        <i class="fas fa-arrow-circle-left me-1"></i> Back
                    </a>
                </div>
            </div>
            <i class="fas fa-graduation-cap floating-icons"></i>
        </div>

        <div class="card-body">
            <div class="info-section">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="info-list">
                            <li>
                                <i class="fas fa-chalkboard-teacher text-primary mr-2"></i>
                                <strong> Class Code:</strong>
                                <span class="text-uppercase">{{ $className }}</span>
                            </li>
                            <li>
                                <i class="fas fa-book text-primary mr-2"></i>
                                <strong> Course Code:</strong>
                                <span class="text-uppercase">{{ $courseName }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="info-list">
                            <li>
                                <i class="fas fa-file-alt text-primary mr-2"></i>
                                <strong> Exam Type:</strong>
                                <span class="text-uppercase">{{ $examName }}</span>
                            </li>
                            <li>
                                <i class="fas fa-calendar-day text-primary mr-2"></i>
                                <strong> Exam Date:</strong>
                                <span class="text-uppercase">{{ \Carbon\Carbon::parse($examDate)->format('d-M-Y')
                                    }}</span>
                            </li>
                            <li>
                                <i class="fas fa-calendar-alt text-primary mr-2"></i>
                                <strong> Term:</strong>
                                <span class="text-uppercase">{{ $term }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            @if ($errors->any())
            <div class="alert-custom">
                <strong><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="instruction-text">
                <i class="fas fa-info-circle me-2"></i> Enter score from 0 to {{ $marking_style == 1 ? '50' : '100' }}
                correctly
            </div>

            <form id="scoreForm" action="{{ route('exams.store.score', ['id' => $id]) }}" method="POST"
                class="needs-validation" novalidate>
                @csrf

                <input type="hidden" name="course_id" value="{{ $courseId }}">
                <input type="hidden" name="class_id" value="{{ $classId }}">
                <input type="hidden" name="teacher_id" value="{{ $teacherId }}">
                <input type="hidden" name="school_id" value="{{ $schoolId }}">
                <input type="hidden" name="term" value="{{ $term }}">
                <input type="hidden" name="marking_style" value="{{ $marking_style }}">
                <input type="hidden" name="submission_token" value="{{ md5(uniqid(rand(), true)) }}">

                <div class="info-section">
                    <div class="row">
                        <!-- Examination Type -->
                        <div class="col-md-6 mb-4">
                            <label for="exam_id" class="form-label">
                                <i class="fas fa-file-alt text-primary"></i>
                                Examination Type <span class="required-star">*</span>
                            </label>
                            <select name="exam_id" id="exam_id" class="form-control-custom text-capitalize" required>
                                <option value="">-- Select Exam type --</option>
                                @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}" {{ $exam->id == $examTypeId ? 'selected' : '' }}>
                                    {{ $exam->exam_type }}
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please select an examination type
                            </div>
                        </div>

                        <!-- Exam Date -->
                        <div class="col-md-6 mb-4">
                            <label for="exam_date" class="form-label">
                                <i class="fas fa-calendar-day text-primary"></i>
                                Uploading Date <span class="required-star">*</span>
                            </label>
                            <input type="date" name="exam_date" class="form-control-custom" id="exam_date" required
                                value="{{ \Carbon\Carbon::parse($examDate)->format('Y-m-d') }}"
                                min="{{ \Carbon\Carbon::now()->subYears(1)->format('Y-m-d') }}"
                                max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            <div class="invalid-feedback">
                                Please provide a valid exam date
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-custom table-responsive-md">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="45%">Student Name</th>
                                    <th width="30%">Score</th>
                                    <th width="20%">Grade</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                @forelse ($students as $student)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <input type="hidden" name="students[{{ $loop->index }}][student_id]"
                                        value="{{ $student->id }}">
                                    <td class="text-capitalize">
                                        {{-- <i class="fas fa-user-graduate me-2 text-primary"></i> --}}
                                        {{ ucwords(strtolower($student->first_name)) }}
                                        {{ ucwords(strtolower($student->middle_name)) }}
                                        {{ ucwords(strtolower($student->last_name)) }}
                                    </td>
                                    <td class="text-center">
                                        <input type="number" class="form-control score-input"
                                            name="students[{{ $loop->index }}][score]" placeholder="Score"
                                            value="{{ old('students.' . $loop->index . '.score') }}" min="0"
                                            max="{{ $marking_style == 1 ? '50' : '100' }}">
                                    </td>
                                    <td class="text-center">
                                        <input type="text" disabled name="students[{{ $loop->index }}][grade]"
                                            class="form-control grade-input">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>No student records
                                            found!
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="action-buttons">
                    <!-- Save to Draft -->
                    <button type="submit" class="btn btn-warning-custom" name="action" value="save" id="saveButton">
                        <i class="fas fa-save"></i> Save as Draft
                    </button>

                    <!-- Submit Final Results -->
                    <button type="submit" class="btn btn-success-custom pulse-animation" name="action" value="submit"
                        id="submitButton">
                        <i class="fas fa-check"></i> Submit Final Results
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    // ============================================
    // FORM VALIDATION (with button handling)
    // ============================================
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                        enableButtons();
                        var invalidElements = form.querySelectorAll(':invalid');
                        if (invalidElements.length > 0) {
                            invalidElements[0].scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    } else {
                        disableButtons();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    // ============================================
    // GRADE CALCULATION
    // ============================================
    function calculateGrade(score, markingStyle) {
        let grade = '', bgColor = '';
        if (isNaN(score)) {
            grade = 'Abs'; bgColor = 'orange';
        } else if (markingStyle == 1) {
            if (score >= 41 && score <= 50) { grade = 'A'; bgColor = '#97e897'; }
            else if (score >= 31 && score <= 40) { grade = 'B'; bgColor = '#4edcdc'; }
            else if (score >= 21 && score <= 30) { grade = 'C'; bgColor = '#e9f0aa'; }
            else if (score >= 11 && score <= 20) { grade = 'D'; bgColor = '#ef8f8f'; }
            else if (score >= 0 && score <= 10) { grade = 'E'; bgColor = '#ebc4f3'; }
            else { grade = 'Error'; bgColor = 'red'; }
        } else if (markingStyle == 2) {
            if (score >= 81 && score <= 100) { grade = 'A'; bgColor = '#97e897'; }
            else if (score >= 61 && score <= 80) { grade = 'B'; bgColor = '#4edcdc'; }
            else if (score >= 41 && score <= 60) { grade = 'C'; bgColor = '#e9f0aa'; }
            else if (score >= 21 && score <= 40) { grade = 'D'; bgColor = '#ef8f8f'; }
            else if (score >= 0 && score <= 20) { grade = 'E'; bgColor = '#ebc4f3'; }
            else { grade = 'Error'; bgColor = 'red'; }
        } else if (markingStyle == 3) {
            if (score >= 75 && score <= 100) { grade = 'A'; bgColor = '#97e897'; }
            else if (score >= 65 && score <= 74) { grade = 'B'; bgColor = '#4edcdc'; }
            else if (score >= 45 && score <= 64) { grade = 'C'; bgColor = '#e9f0aa'; }
            else if (score >= 30 && score <= 44) { grade = 'D'; bgColor = '#ef8f8f'; }
            else if (score >= 0 && score <= 29) { grade = 'F'; bgColor = '#ebc4f3'; }
            else { grade = 'Error'; bgColor = 'red'; }
        }
        return { grade, bgColor };
    }

    // ============================================
    // LOCAL STORAGE – SAVE / LOAD / TOAST / CLEAR
    // ============================================
    const scoreInputs = document.querySelectorAll('.score-input');
    const gradeInputs = document.querySelectorAll('.grade-input');
    const markingStyle = {{ $marking_style }};

    // UNIQUE KEY: includes exam_id and exam_date to separate different exams
    function getStorageKey() {
        const courseId = document.querySelector('input[name="course_id"]')?.value || 'unknown';
        const classId = document.querySelector('input[name="class_id"]')?.value || 'unknown';
        const examId = document.querySelector('select[name="exam_id"]')?.value || 'unknown';
        const examDate = document.querySelector('input[name="exam_date"]')?.value || 'unknown';
        return `register_scores_${courseId}_${classId}_${examId}_${examDate}`;
    }

    function saveScoreToLocalStorage(studentId, score) {
        const storageKey = getStorageKey();
        let savedData = {};
        const existing = localStorage.getItem(storageKey);
        if (existing) savedData = JSON.parse(existing);
        savedData[studentId] = { score: score, timestamp: Date.now() };
        localStorage.setItem(storageKey, JSON.stringify(savedData));
        showToast('Score saved locally!', 'success');
    }

    function loadScoresFromLocalStorage() {
        const storageKey = getStorageKey();
        const saved = localStorage.getItem(storageKey);
        if (!saved) return false;
        try {
            const savedData = JSON.parse(saved);
            let restoredCount = 0;
            const now = Date.now();
            for (const [studentId, data] of Object.entries(savedData)) {
                if (now - data.timestamp < 86400000) { // 24 hours
                    const input = document.querySelector(`input[name="students[${studentId}][score]"]`);
                    if (input && !input.value) {
                        input.value = data.score;
                        restoredCount++;
                        const index = Array.from(scoreInputs).indexOf(input);
                        if (index !== -1) updateGrade(input, index);
                    }
                }
            }
            if (restoredCount > 0) {
                showToast(`Restored ${restoredCount} unsaved score(s) from previous session`, 'info');
            }
            return restoredCount > 0;
        } catch (e) {
            return false;
        }
    }

    function clearLocalStorage() {
        const storageKey = getStorageKey();
        localStorage.removeItem(storageKey);
        console.log('Local storage cleared for register form');
    }

    function showToast(message, type = 'info') {
        let toastDiv = document.getElementById('autoSaveToast');
        if (!toastDiv) {
            toastDiv = document.createElement('div');
            toastDiv.id = 'autoSaveToast';
            toastDiv.style.cssText =
                'position: fixed; bottom: 20px; right: 20px; z-index: 9999; background: #28a745; color: white; padding: 12px 20px; border-radius: 8px; font-size: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); display: none;';
            document.body.appendChild(toastDiv);
        }
        const colors = { success: '#28a745', error: '#dc3545', info: '#17a2b8', warning: '#ffc107' };
        toastDiv.style.backgroundColor = colors[type] || colors.info;
        toastDiv.innerHTML = message;
        toastDiv.style.display = 'block';
        setTimeout(() => { toastDiv.style.display = 'none'; }, 3000);
    }

    // ============================================
    // GRADE UPDATE & SCORE INPUT HANDLERS
    // ============================================
    function updateGrade(input, index) {
        const score = parseFloat(input.value);
        const { grade, bgColor } = calculateGrade(score, markingStyle);
        if (gradeInputs[index]) {
            gradeInputs[index].value = grade;
            gradeInputs[index].style.backgroundColor = bgColor;
        }
    }

    scoreInputs.forEach((input, index) => {
        if (input.value) updateGrade(input, index);
        input.addEventListener('input', () => {
            updateGrade(input, index);
            const maxValue = markingStyle == 1 ? 50 : 100;
            if (input.value > maxValue) input.value = maxValue;
            if (input.value < 0) input.value = 0;
            updateGrade(input, index);
        });
        input.addEventListener('blur', function() {
            const studentIdMatch = this.name.match(/students\[(\d+)\]\[score\]/);
            if (studentIdMatch && this.value) {
                saveScoreToLocalStorage(studentIdMatch[1], this.value);
            }
        });
    });

    // ============================================
    // BUTTON DISABLE / ENABLE (with hidden action)
    // ============================================
    let clickedAction = null;

    function disableButtons() {
        const saveBtn = document.getElementById('saveButton');
        const submitBtn = document.getElementById('submitButton');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        }
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        }
        let actionInput = document.querySelector('input[name="action"]');
        if (!actionInput) {
            actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            document.getElementById('scoreForm').appendChild(actionInput);
        }
        actionInput.value = clickedAction;
    }

    function enableButtons() {
        const saveBtn = document.getElementById('saveButton');
        const submitBtn = document.getElementById('submitButton');
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Save as Draft';
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Submit Final Results';
        }
        const actionInput = document.querySelector('input[name="action"]');
        if (actionInput) actionInput.remove();
        clickedAction = null;
    }

    // ============================================
    // CONFIRMATION DIALOGS – clear only on final submit
    // ============================================
    const saveButton = document.querySelector('button[name="action"][value="save"]');
    const submitButton = document.querySelector('button[name="action"][value="submit"]');

    if (saveButton) {
        saveButton.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to save results temporarily?')) {
                e.preventDefault();
                enableButtons();
                return;
            }
            clickedAction = 'save';
            // DO NOT clear localStorage
        });
    }

    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to submit the final results? No editing will be allowed after submission.')) {
                e.preventDefault();
                enableButtons();
                return;
            }
            clickedAction = 'submit';
            // Clear localStorage because user confirmed final submission
            clearLocalStorage();
        });
    }

    // ============================================
    // LOAD SAVED SCORES ON PAGE LOAD
    // ============================================
    loadScoresFromLocalStorage();
});
</script>
@endsection
