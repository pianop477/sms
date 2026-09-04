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

        --shadow-sm: 0 5px 15px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 10px 25px rgba(0, 0, 0, 0.10);
        --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.20);
    }

    /* =========================================================
       PAGE WRAPPER
       ========================================================= */

    .class-teacher-edit-page {
        position: relative;
        min-height: 100%;
        isolation: isolate;
    }

    /* =========================================================
       ANIMATED BACKGROUND
       IMPORTANT:
       This is a visual layer only.
       pointer-events: none prevents it from blocking navigation,
       buttons, forms, links and other interactive elements.
       ========================================================= */

    .class-teacher-edit-page .animated-bg {
        position: fixed;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: -1;
    }

    .class-teacher-edit-page .animated-bg::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        pointer-events: none;

        background:
            radial-gradient(
                circle at 70% 30%,
                rgba(255, 255, 255, 0.15) 0%,
                transparent 30%
            ),
            radial-gradient(
                circle at 30% 70%,
                rgba(255, 255, 255, 0.15) 0%,
                transparent 30%
            );

        animation: rotateBackground 60s linear infinite;
    }

    @keyframes rotateBackground {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* =========================================================
       PARTICLES
       ========================================================= */

    .class-teacher-edit-page .particles {
        position: fixed;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: -1;
    }

    .class-teacher-edit-page .particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.10);
        border-radius: 50%;
        pointer-events: none;
        animation: floatParticle 20s infinite;
    }

    @keyframes floatParticle {
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

    /* =========================================================
       MAIN CONTENT
       ========================================================= */

    .class-teacher-edit-page .edit-container {
        position: relative;
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }

    /* =========================================================
       MODERN CARD
       ========================================================= */

    .class-teacher-edit-page .modern-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);

        border: 1px solid rgba(255, 255, 255, 0.50);
        border-radius: 32px;

        box-shadow: var(--shadow-lg);
        overflow: hidden;

        animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* =========================================================
       CARD HEADER
       ========================================================= */

    .class-teacher-edit-page .card-header-modern {
        position: relative;
        overflow: hidden;

        padding: 30px 35px;

        background: linear-gradient(
            135deg,
            var(--primary) 0%,
            var(--secondary) 100%
        );
    }

    .class-teacher-edit-page .card-header-modern::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        pointer-events: none;

        background: radial-gradient(
            circle,
            rgba(255, 255, 255, 0.18) 0%,
            transparent 60%
        );
    }

    .class-teacher-edit-page .card-header-modern::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;

        pointer-events: none;

        background: linear-gradient(
            90deg,
            var(--warning),
            var(--success),
            var(--accent)
        );
    }

    .class-teacher-edit-page .header-content {
        position: relative;

        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;

        gap: 20px;
    }

    .class-teacher-edit-page .header-title {
        color: #fff;
        margin: 0;
    }

    .class-teacher-edit-page .header-title h3 {
        display: flex;
        align-items: center;
        gap: 10px;

        margin: 0;

        color: #fff;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .class-teacher-edit-page .header-icon {
        width: 50px;
        height: 50px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        background: rgba(255, 255, 255, 0.20);
        border: 1px solid rgba(255, 255, 255, 0.30);
        border-radius: 15px;

        font-size: 24px;

        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }

    /* =========================================================
       BACK BUTTON
       ========================================================= */

    .class-teacher-edit-page .btn-back-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;

        position: relative;
        overflow: hidden;

        padding: 12px 25px;

        color: #fff;
        text-decoration: none;
        font-weight: 600;

        border: 1px solid rgba(255, 255, 255, 0.30);
        border-radius: 50px;

        background: rgba(255, 255, 255, 0.20);

        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);

        transition:
            transform 0.3s ease,
            background 0.3s ease,
            box-shadow 0.3s ease;
    }

    .class-teacher-edit-page .btn-back-modern:hover {
        color: #fff;
        text-decoration: none;

        background: rgba(255, 255, 255, 0.30);
        transform: translateY(-3px);

        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.20);
    }

    /* =========================================================
       CARD BODY
       ========================================================= */

    .class-teacher-edit-page .card-body-modern {
        padding: 40px;
    }

    /* =========================================================
       INFORMATION CARD
       ========================================================= */

    .class-teacher-edit-page .info-card {
        position: relative;
        overflow: hidden;

        margin-bottom: 30px;
        padding: 25px;

        background: linear-gradient(
            135deg,
            #f8f9fa 0%,
            #e9ecef 100%
        );

        border: 1px solid rgba(255, 255, 255, 0.70);
        border-radius: 25px;
    }

    .class-teacher-edit-page .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;

        width: 5px;
        height: 100%;

        pointer-events: none;

        background: linear-gradient(
            180deg,
            var(--primary),
            var(--secondary)
        );
    }

    .class-teacher-edit-page .info-title {
        display: flex;
        align-items: center;
        gap: 10px;

        margin-bottom: 20px;

        color: var(--dark);
        font-weight: 700;
    }

    .class-teacher-edit-page .info-grid {
        display: grid;
        grid-template-columns: repeat(
            auto-fit,
            minmax(200px, 1fr)
        );

        gap: 20px;
    }

    .class-teacher-edit-page .info-item {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .class-teacher-edit-page .info-icon {
        width: 45px;
        height: 45px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        color: #fff;
        font-size: 20px;

        background: linear-gradient(
            135deg,
            var(--primary),
            var(--secondary)
        );

        border-radius: 12px;
    }

    .class-teacher-edit-page .info-content {
        min-width: 0;
        flex: 1;
    }

    .class-teacher-edit-page .info-label {
        margin-bottom: 3px;

        color: #6c757d;
        font-size: 0.85rem;
    }

    .class-teacher-edit-page .info-value {
        color: var(--dark);
        font-size: 1.05rem;
        font-weight: 700;
        word-break: break-word;
    }

    /* =========================================================
       FORM
       ========================================================= */

    .class-teacher-edit-page .form-section-modern {
        padding: 30px;

        background: linear-gradient(
            135deg,
            #f8f9fa 0%,
            #e9ecef 100%
        );

        border: 1px solid rgba(255, 255, 255, 0.70);
        border-radius: 25px;
    }

    .class-teacher-edit-page .form-group-modern {
        margin-bottom: 25px;
    }

    .class-teacher-edit-page .form-label-modern {
        display: flex;
        align-items: center;
        gap: 8px;

        margin-bottom: 8px;

        color: var(--dark);
        font-weight: 600;
    }

    .class-teacher-edit-page .required-star {
        color: var(--danger);
        font-size: 1.1rem;
    }

    .class-teacher-edit-page .form-control-modern {
        width: 100%;
        min-height: 54px;

        padding: 14px 18px;

        background: #fff;

        border: 2px solid #e9ecef;
        border-radius: 15px;

        color: #495057;
        font-size: 1rem;

        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease,
            transform 0.2s ease;
    }

    .class-teacher-edit-page .form-control-modern:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.10);
        outline: none;
    }

    .class-teacher-edit-page .form-control-modern:disabled {
        background: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
    }

    select.form-control-modern {
        appearance: auto;
        -webkit-appearance: auto;
    }

    /* =========================================================
       SELECT2
       ========================================================= */

    .select2-container {
        width: 100% !important;
    }

    .select2-container--default
    .select2-selection--single {
        height: 54px !important;

        border: 2px solid #e9ecef !important;
        border-radius: 15px !important;

        padding: 12px 18px !important;

        background: #fff !important;
    }

    .select2-container--default
    .select2-selection--single
    .select2-selection__rendered {
        padding-left: 0 !important;

        color: #495057 !important;
        font-size: 1rem !important;
        line-height: 26px !important;
    }

    .select2-container--default
    .select2-selection--single
    .select2-selection__arrow {
        height: 50px !important;
        right: 15px !important;
    }

    .select2-dropdown {
        border: 2px solid #e9ecef !important;
        border-radius: 15px !important;

        overflow: hidden;

        box-shadow: var(--shadow-md);
    }

    .select2-results__option {
        padding: 12px 15px !important;
        font-size: 0.95rem;
    }

    .select2-container--default
    .select2-results__option--highlighted.select2-results__option--selectable {
        background: linear-gradient(
            135deg,
            var(--primary),
            var(--secondary)
        ) !important;
    }

    /* =========================================================
       VALIDATION
       ========================================================= */

    .class-teacher-edit-page .invalid-feedback-modern {
        display: none;
        align-items: center;
        gap: 5px;

        margin-top: 7px;

        color: var(--danger);
        font-size: 0.85rem;
    }

    .was-validated
    .form-control-modern:invalid
    ~ .invalid-feedback-modern {
        display: flex;
    }

    /* =========================================================
       SAVE BUTTON
       ========================================================= */

    .class-teacher-edit-page .btn-save-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;

        min-width: 200px;

        padding: 16px 40px;

        color: #fff;

        border: 0;
        border-radius: 50px;

        background: linear-gradient(
            135deg,
            #25b9dc,
            #4cc9f0
        );

        box-shadow: var(--shadow-md);

        font-size: 1.05rem;
        font-weight: 700;

        cursor: pointer;

        transition:
            transform 0.3s ease,
            box-shadow 0.3s ease,
            opacity 0.3s ease;
    }

    .class-teacher-edit-page .btn-save-modern:hover:not(:disabled) {
        color: #fff;
        transform: translateY(-4px);

        box-shadow: 0 15px 30px rgba(76, 201, 240, 0.35);
    }

    .class-teacher-edit-page .btn-save-modern:disabled {
        opacity: 0.70;
        cursor: not-allowed;
        transform: none;
    }

    /* =========================================================
       LOADING OVERLAY
       Only visible when form is actually submitting.
       ========================================================= */

    .class-teacher-loading {
        position: fixed;
        inset: 0;

        display: none;
        align-items: center;
        justify-content: center;

        background: rgba(15, 23, 42, 0.25);

        z-index: 2000;
    }

    .class-teacher-loading.is-active {
        display: flex;
    }

    .class-teacher-loading-spinner {
        width: 60px;
        height: 60px;

        border: 5px solid rgba(255, 255, 255, 0.35);
        border-top-color: var(--primary);

        border-radius: 50%;

        animation: spinLoader 0.9s linear infinite;
    }

    @keyframes spinLoader {
        to {
            transform: rotate(360deg);
        }
    }

    /* =========================================================
       TOAST
       ========================================================= */

    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;

        display: flex;
        align-items: center;
        gap: 12px;

        max-width: min(420px, calc(100vw - 40px));

        padding: 15px 20px;

        background: #fff;

        border-left: 5px solid var(--success);
        border-radius: 12px;

        box-shadow: var(--shadow-lg);

        transform: translateX(calc(100% + 30px));
        opacity: 0;

        transition:
            transform 0.3s ease,
            opacity 0.3s ease;

        z-index: 2100;
    }

    .toast-notification.show {
        transform: translateX(0);
        opacity: 1;
    }

    .toast-success {
        border-left-color: #28a745;
    }

    .toast-error {
        border-left-color: var(--danger);
    }

    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 768px) {
        .class-teacher-edit-page .edit-container {
            margin: 20px auto;
        }

        .class-teacher-edit-page .card-body-modern {
            padding: 25px;
        }

        .class-teacher-edit-page .header-content {
            flex-direction: column;
            text-align: center;
        }

        .class-teacher-edit-page .header-title h3 {
            justify-content: center;
            font-size: 1.5rem;
        }

        .class-teacher-edit-page .info-grid {
            grid-template-columns: 1fr;
        }

        .class-teacher-edit-page .btn-save-modern {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .class-teacher-edit-page .edit-container {
            padding: 0 12px;
        }

        .class-teacher-edit-page .card-header-modern {
            padding: 20px;
        }

        .class-teacher-edit-page .card-body-modern {
            padding: 20px;
        }

        .class-teacher-edit-page .form-section-modern {
            padding: 20px;
        }

        .class-teacher-edit-page .header-title h3 {
            font-size: 1.2rem;
        }

        .class-teacher-edit-page .info-item {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="class-teacher-edit-page">

    {{-- Visual background only --}}
    <div class="animated-bg" aria-hidden="true"></div>

    {{-- Visual particles only --}}
    <div class="particles" aria-hidden="true"></div>

    {{-- Loading overlay --}}
    <div
        class="class-teacher-loading"
        id="loadingOverlay"
        aria-hidden="true"
    >
        <div
            class="class-teacher-loading-spinner"
            role="status"
            aria-label="Saving changes"
        ></div>
    </div>

    <div class="edit-container">

        <div class="modern-card">

            {{-- Header --}}
            <div class="card-header-modern">

                <div class="header-content">

                    <div class="header-title">
                        <h3>
                            <span class="header-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </span>

                            Edit Class Teacher Assignment
                        </h3>
                    </div>

                    <a
                        href="{{ route('Class.Teachers', [
                            'class' => Hashids::encode($classTeacher->class_id)
                        ]) }}"
                        class="btn-back-modern"
                    >
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </a>

                </div>

            </div>

            {{-- Body --}}
            <div class="card-body-modern">

                {{-- Class Information --}}
                <div class="info-card">

                    <h6 class="info-title">
                        <i class="fas fa-info-circle text-primary"></i>
                        Current Class Information
                    </h6>

                    <div class="info-grid">

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-chalkboard"></i>
                            </div>

                            <div class="info-content">
                                <div class="info-label">
                                    Class Name
                                </div>

                                <div class="info-value text-uppercase">
                                    {{ $classTeacher->class_name }}
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-barcode"></i>
                            </div>

                            <div class="info-content">
                                <div class="info-label">
                                    Class Code
                                </div>

                                <div class="info-value text-uppercase">
                                    {{ $classTeacher->class_code }}
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>

                            <div class="info-content">
                                <div class="info-label">
                                    Stream
                                </div>

                                <div class="info-value text-uppercase">
                                    {{ $classTeacher->group }}
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>

                            <div class="info-content">
                                <div class="info-label">
                                    Current Teacher
                                </div>

                                <div class="info-value">
                                    {{ ucwords(strtolower($classTeacher->first_name)) }}
                                    {{ ucwords(strtolower($classTeacher->last_name)) }}
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- Edit Form --}}
                <form
                    action="{{ route('roles.update.class.teacher', [
                        'classTeacher' => Hashids::encode($classTeacher->id)
                    ]) }}"
                    method="POST"
                    id="classTeacherForm"
                    class="needs-validation"
                    novalidate
                >
                    @csrf
                    @method('PUT')

                    <div class="form-section-modern">

                        <div class="row">

                            {{-- Class Name --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        <i class="fas fa-chalkboard text-primary"></i>
                                        Class Name
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control-modern text-uppercase"
                                        value="{{ $classTeacher->class_name }}"
                                        disabled
                                    >

                                </div>

                            </div>

                            {{-- Class Code --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        <i class="fas fa-barcode text-primary"></i>
                                        Class Code
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control-modern text-uppercase"
                                        value="{{ $classTeacher->class_code }}"
                                        disabled
                                    >

                                </div>

                            </div>

                            {{-- Stream --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        <i class="fas fa-layer-group text-primary"></i>
                                        Stream
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control-modern text-uppercase"
                                        value="{{ $classTeacher->group }}"
                                        disabled
                                    >

                                </div>

                            </div>

                            {{-- Teacher --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label
                                        for="teacherSelect"
                                        class="form-label-modern"
                                    >
                                        <i class="fas fa-user-tie text-primary"></i>
                                        Select New Teacher

                                        <span class="required-star">*</span>
                                    </label>

                                    <select
                                        name="teacher"
                                        id="teacherSelect"
                                        class="form-control-modern"
                                        required
                                    >
                                        <option
                                            value="{{ $classTeacher->teacher_id }}"
                                            selected
                                        >
                                            {{ ucwords(strtolower($classTeacher->first_name)) }}
                                            {{ ucwords(strtolower($classTeacher->last_name)) }}
                                            (Current)
                                        </option>

                                        @forelse ($teachers as $teacher)

                                            @if ($teacher->id != $classTeacher->teacher_id)

                                                <option value="{{ $teacher->id }}">
                                                    {{ ucwords(strtolower($teacher->first_name)) }}
                                                    {{ ucwords(strtolower($teacher->last_name)) }}
                                                </option>

                                            @endif

                                        @empty

                                            <option value="" disabled>
                                                No other teachers available
                                            </option>

                                        @endforelse

                                    </select>

                                    <div class="invalid-feedback-modern">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Please select a teacher.
                                    </div>

                                    @error('teacher')
                                        <div
                                            class="invalid-feedback-modern"
                                            style="display: flex;"
                                        >
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                        </div>

                        {{-- Help text --}}
                        <div class="mt-4 p-3 bg-light rounded">
                            <small class="text-muted">
                                <i class="fas fa-lightbulb me-2 text-warning"></i>
                                Select a new teacher from the list above.
                                The current teacher will be replaced.
                            </small>
                        </div>

                    </div>

                    {{-- Submit --}}
                    <div class="text-center mt-4">

                        <button
                            class="btn-save-modern"
                            type="submit"
                            id="saveButton"
                        >
                            <i class="fas fa-save"></i>
                            <span>Save Changes</span>
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const page = document.querySelector('.class-teacher-edit-page');
    const particlesContainer = page?.querySelector('.particles');

    const form = document.getElementById('classTeacherForm');
    const submitButton = document.getElementById('saveButton');
    const loadingOverlay = document.getElementById('loadingOverlay');

    /* =========================================================
       CREATE PARTICLES
       ========================================================= */

    if (particlesContainer) {
        const particleCount = 30;

        for (let i = 0; i < particleCount; i++) {

            const particle = document.createElement('div');

            particle.className = 'particle';

            const size = Math.random() * 10 + 3;

            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;

            particle.style.left = `${Math.random() * 100}%`;
            particle.style.top = `${Math.random() * 100}%`;

            particle.style.animationDelay =
                `${Math.random() * 20}s`;

            particle.style.animationDuration =
                `${Math.random() * 10 + 15}s`;

            particlesContainer.appendChild(particle);
        }
    }


    /* =========================================================
       INITIALIZE SELECT2
       ========================================================= */

    if (
        typeof window.jQuery !== 'undefined' &&
        typeof jQuery.fn.select2 !== 'undefined'
    ) {
        jQuery('#teacherSelect').select2({
            placeholder: 'Search or select teacher...',
            width: '100%'
        });
    }


    /* =========================================================
       FORM SUBMISSION
       ========================================================= */

    if (form && submitButton) {

        form.addEventListener('submit', function (event) {

            if (!form.checkValidity()) {

                event.preventDefault();
                event.stopPropagation();

                form.classList.add('was-validated');

                const firstInvalid =
                    form.querySelector(':invalid');

                if (firstInvalid) {
                    firstInvalid.focus();
                }

                showToast(
                    'Please select a teacher.',
                    'error'
                );

                return;
            }

            /* Allow normal Laravel form submission.
               Prevent accidental double submission. */

            submitButton.disabled = true;

            submitButton.innerHTML = `
                <i class="fas fa-spinner fa-spin"></i>
                <span>Saving Changes...</span>
            `;

            if (loadingOverlay) {
                loadingOverlay.classList.add('is-active');
                loadingOverlay.setAttribute(
                    'aria-hidden',
                    'false'
                );
            }
        });
    }


    /* =========================================================
       TOAST
       ========================================================= */

    function showToast(message, type = 'success') {

        const existingToast =
            document.querySelector('.toast-notification');

        if (existingToast) {
            existingToast.remove();
        }

        const toast = document.createElement('div');

        toast.className =
            `toast-notification toast-${type}`;

        const icon =
            type === 'success'
                ? 'fa-check-circle'
                : 'fa-exclamation-circle';

        toast.innerHTML = `
            <i class="fas ${icon} fa-lg"></i>
            <span></span>
        `;

        toast.querySelector('span').textContent = message;

        document.body.appendChild(toast);

        requestAnimationFrame(function () {
            toast.classList.add('show');
        });

        setTimeout(function () {

            toast.classList.remove('show');

            setTimeout(function () {
                toast.remove();
            }, 300);

        }, 3000);
    }


    /* =========================================================
       RESET PAGE STATE
       ========================================================= */

    window.addEventListener('pageshow', function () {

        if (submitButton) {

            submitButton.disabled = false;

            submitButton.innerHTML = `
                <i class="fas fa-save"></i>
                <span>Save Changes</span>
            `;
        }

        if (loadingOverlay) {
            loadingOverlay.classList.remove('is-active');
            loadingOverlay.setAttribute(
                'aria-hidden',
                'true'
            );
        }
    });

});
</script>

@endsection