@extends('SRTDashboard.frame')

@section('content')
<style>
    :root {
        --primary: #4361ee;
        --primary-light: #4895ef;
        --secondary: #3f37c9;
        --success: #4cc9f0;
        --danger: #f72585;
        --warning: #f8961e;
        --dark: #1e1e2f;
        --light: #f8f9fa;
        --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    body {
        background: #f5f7fb;
        overflow-x: hidden;
    }

    /* Container */
    .container-fluid {
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
    }

    /* Header */
    .password-page-header {
        background: var(--gradient-1);
        border-radius: 25px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
        color: white;
        position: relative;
        overflow: hidden;
    }

    @media (min-width: 768px) {
        .password-page-header {
            padding: 2rem;
        }
    }

    .password-page-header::before {
        content: '🔒';
        font-size: 6rem;
        position: absolute;
        right: 10px;
        bottom: -10px;
        opacity: 0.2;
        transform: rotate(15deg);
    }

    @media (min-width: 768px) {
        .password-page-header::before {
            font-size: 8rem;
            right: 20px;
            bottom: -20px;
        }
    }

    .password-page-header h4 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        word-wrap: break-word;
    }

    @media (min-width: 768px) {
        .password-page-header h4 {
            font-size: 2rem;
        }
    }

    .password-page-header p {
        font-size: 0.95rem;
        opacity: 0.9;
        margin: 0;
    }

    @media (min-width: 768px) {
        .password-page-header p {
            font-size: 1.1rem;
        }
    }

    /* Modern Card */
    .modern-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }

    @media (min-width: 768px) {
        .modern-card {
            border-radius: 25px;
        }
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.2);
    }

    /* Card Body - REDUCED PADDING */
    .card-body {
        padding: 1rem; /* REDUCED from 1.5rem */
    }

    @media (min-width: 576px) {
        .card-body {
            padding: 1.2rem; /* REDUCED from 2rem */
        }
    }

    @media (min-width: 768px) {
        .card-body {
            padding: 1.5rem; /* REDUCED from 2.5rem */
        }
    }

    /* ============= FIXED REQUIREMENTS CARD - MORE COMPACT ============= */
    .requirements-card {
        background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
        border-radius: 12px; /* REDUCED from 15px */
        padding: 0.8rem; /* REDUCED from 1.2rem */
        margin-bottom: 1rem; /* REDUCED from 1.8rem */
        border-left: 4px solid var(--primary); /* REDUCED from 5px */
        position: relative;
        overflow: hidden;
    }

    @media (min-width: 768px) {
        .requirements-card {
            border-radius: 15px; /* REDUCED from 20px */
            padding: 1rem; /* REDUCED from 1.8rem */
            margin-bottom: 1.2rem; /* REDUCED from 2.5rem */
        }
    }

    .requirements-card::after {
        content: '⚡';
        font-size: 2.5rem; /* REDUCED from 3rem */
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.1;
    }

    @media (min-width: 768px) {
        .requirements-card::after {
            font-size: 3rem; /* REDUCED from 4rem */
            right: 15px; /* REDUCED from 20px */
        }
    }

    .requirements-title {
        display: flex;
        align-items: center;
        gap: 6px; /* REDUCED from 8px */
        font-size: 0.9rem; /* REDUCED from 1.1rem */
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.6rem; /* REDUCED from 1rem */
        flex-wrap: wrap;
    }

    @media (min-width: 768px) {
        .requirements-title {
            gap: 8px; /* REDUCED from 10px */
            font-size: 1rem; /* REDUCED from 1.3rem */
            margin-bottom: 0.8rem; /* REDUCED from 1.2rem */
        }
    }

    .requirements-title i {
        font-size: 1.2rem; /* REDUCED from 1.5rem */
        color: var(--primary);
    }

    /* Requirements List - MORE COMPACT */
    .requirements-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.4rem; /* REDUCED from 0.8rem */
    }

    @media (min-width: 480px) {
        .requirements-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.6rem; /* REDUCED from 1rem */
        }
    }

    @media (min-width: 768px) {
        .requirements-list {
            display: flex;
            flex-wrap: wrap;
            flex-direction: row;
            gap: 0.6rem; /* REDUCED from 1rem */
        }
    }

    .requirements-list li {
        flex: 1 1 auto;
        padding: 0.5rem 0.8rem; /* REDUCED from 0.8rem 1rem */
        background: white;
        border-radius: 8px; /* REDUCED from 10px */
        display: flex;
        align-items: center;
        gap: 6px; /* REDUCED from 8px */
        font-size: 0.8rem; /* REDUCED from 0.9rem */
        color: var(--dark);
        box-shadow: 0 3px 8px rgba(0,0,0,0.05); /* REDUCED shadow */
        border: 1px solid rgba(67, 97, 238, 0.1);
        width: 100%;
    }

    @media (min-width: 768px) {
        .requirements-list li {
            min-width: 200px; /* REDUCED from 250px */
            padding: 0.6rem 1rem; /* REDUCED from 0.8rem 1.2rem */
            font-size: 0.85rem; /* REDUCED from 1rem */
        }
    }

    .requirements-list li i {
        font-size: 0.9rem; /* REDUCED from 1.1rem */
        min-width: 16px; /* REDUCED from 20px */
    }

    .requirements-list li.valid {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-color: #28a745;
    }

    /* Password Strength Meter - MORE COMPACT */
    .password-strength-meter {
        margin: 0.8rem 0 0.4rem 0; /* REDUCED from 1.2rem 0 */
        background: #e9ecef;
        border-radius: 30px;
        height: 6px; /* REDUCED from 8px */
        overflow: hidden;
        position: relative;
    }

    @media (min-width: 768px) {
        .password-strength-meter {
            margin: 1rem 0 0.5rem 0; /* REDUCED from 1.5rem 0 */
            height: 8px; /* REDUCED from 10px */
        }
    }

    .strength-text {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.2rem; /* REDUCED from 0.5rem */
        font-size: 0.75rem; /* REDUCED from 0.85rem */
        color: #6c757d;
        flex-wrap: wrap;
        gap: 5px;
    }

    .strength-badge {
        padding: 0.15rem 0.6rem; /* REDUCED from 0.2rem 0.8rem */
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.7rem; /* REDUCED from 0.8rem */
        white-space: nowrap;
    }

    @media (min-width: 768px) {
        .strength-badge {
            padding: 0.2rem 0.8rem; /* REDUCED from 0.3rem 1rem */
            font-size: 0.75rem; /* REDUCED from 0.85rem */
        }
    }

    /* Form Grid - Responsive */
    .row-custom {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.8rem; /* REDUCED from 1.2rem */
        margin-bottom: 1rem; /* REDUCED from 1.5rem */
    }

    @media (min-width: 576px) {
        .row-custom {
            gap: 1rem; /* REDUCED from 1.5rem */
        }
    }

    @media (min-width: 992px) {
        .row-custom {
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem; /* REDUCED from 1.5rem */
        }
    }

    /* Form Groups - MORE COMPACT */
    .form-group-modern {
        margin-bottom: 0.8rem; /* REDUCED from 1.5rem */
        position: relative;
        width: 100%;
    }

    @media (min-width: 992px) {
        .form-group-modern {
            margin-bottom: 1rem; /* REDUCED from 2rem */
        }
    }

    .form-label-modern {
        display: block;
        margin-bottom: 0.3rem; /* REDUCED from 0.5rem */
        font-weight: 600;
        color: var(--dark);
        font-size: 0.75rem; /* REDUCED from 0.85rem */
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (min-width: 768px) {
        .form-label-modern {
            margin-bottom: 0.4rem; /* REDUCED from 0.8rem */
            font-size: 0.8rem; /* REDUCED from 0.95rem */
        }
    }

    .form-label-modern i {
        margin-right: 5px;
        color: var(--primary);
        font-size: 0.9rem; /* REDUCED from 1rem */
    }

    /* Input Group - FIXED */
    .input-group-modern {
        display: flex;
        align-items: stretch;
        position: relative;
        width: 100%;
        flex-wrap: nowrap;
    }

    .input-group-modern .form-control-modern {
        flex: 1 1 0;
        min-width: 0;
        width: auto;
        padding: 0.6rem 0.8rem; /* REDUCED from 0.8rem 1rem */
        font-size: 0.85rem; /* REDUCED from 0.9rem */
        border: 2px solid #eef2f6;
        border-radius: 8px 0 0 8px; /* REDUCED from 10px 0 0 10px */
        background-color: #fafbfc;
        transition: all 0.3s ease;
        color: var(--dark);
        font-family: 'Courier New', monospace;
        letter-spacing: 1px;
    }

    @media (min-width: 768px) {
        .input-group-modern .form-control-modern {
            padding: 0.8rem 1rem; /* REDUCED from 1rem 1.2rem */
            font-size: 0.9rem; /* REDUCED from 1rem */
            letter-spacing: 1.5px; /* REDUCED from 2px */
        }
    }

    .input-group-modern .form-control-modern:focus {
        outline: none;
        border-color: var(--primary);
        background-color: white;
        box-shadow: 0 3px 12px rgba(67, 97, 238, 0.1); /* REDUCED shadow */
    }

    .input-group-modern .input-group-text-modern {
        flex: 0 0 auto;
        width: auto;
        padding: 0 0.8rem; /* REDUCED from 0 1rem */
        background: white;
        border: 2px solid #eef2f6;
        border-left: none;
        border-radius: 0 8px 8px 0; /* REDUCED from 0 10px 10px 0 */
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #6c757d;
        white-space: nowrap;
    }

    @media (min-width: 768px) {
        .input-group-modern .input-group-text-modern {
            padding: 0 1rem; /* REDUCED from 0 1.5rem */
        }
    }

    .input-group-modern .input-group-text-modern:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .input-group-modern .input-group-text-modern i {
        font-size: 0.9rem; /* REDUCED from 1rem */
    }

    @media (min-width: 768px) {
        .input-group-modern .input-group-text-modern i {
            font-size: 1rem; /* REDUCED from 1.2rem */
        }
    }

    /* Small screens */
    @media (max-width: 400px) {
        .input-group-modern {
            flex-wrap: wrap;
        }

        .input-group-modern .form-control-modern {
            width: 100%;
            border-radius: 8px 8px 0 0;
            border-right: 2px solid #eef2f6;
        }

        .input-group-modern .input-group-text-modern {
            width: 100%;
            border-radius: 0 0 8px 8px;
            border-left: 2px solid #eef2f6;
            border-top: none;
            padding: 0.4rem;
        }
    }

    /* Password Feedback - MORE COMPACT */
    .password-feedback {
        margin-top: 0.2rem; /* REDUCED from 0.5rem */
        font-size: 0.7rem; /* REDUCED from 0.8rem */
        display: flex;
        align-items: center;
        gap: 4px; /* REDUCED from 5px */
        word-wrap: break-word;
    }

    @media (min-width: 768px) {
        .password-feedback {
            margin-top: 0.3rem; /* REDUCED from 0.5rem */
            font-size: 0.75rem; /* REDUCED from 0.85rem */
        }
    }

    /* Action Buttons - MORE COMPACT */
    .d-flex.justify-content-end.gap-3.mt-5 {
        display: flex;
        flex-direction: column;
        gap: 0.6rem !important; /* REDUCED from 0.8rem */
        margin-top: 1rem !important; /* REDUCED from 2rem */
    }

    @media (min-width: 480px) {
        .d-flex.justify-content-end.gap-3.mt-5 {
            flex-direction: row;
            justify-content: flex-end;
            gap: 0.8rem !important; /* REDUCED from 1rem */
            margin-top: 1.2rem !important; /* REDUCED from 2rem */
        }
    }

    .btn-modern {
        padding: 0.6rem 1.2rem; /* REDUCED from 0.8rem 1.5rem */
        border-radius: 8px; /* REDUCED from 10px */
        font-weight: 600;
        font-size: 0.8rem; /* REDUCED from 0.9rem */
        letter-spacing: 0.5px;
        text-transform: uppercase;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px; /* REDUCED from 8px */
        position: relative;
        overflow: hidden;
        width: 100%;
        text-decoration: none;
    }

    @media (min-width: 480px) {
        .btn-modern {
            width: auto;
            padding: 0.7rem 1.5rem; /* REDUCED from 1rem 2rem */
            font-size: 0.85rem; /* REDUCED from 1rem */
        }
    }

    @media (min-width: 768px) {
        .btn-modern {
            padding: 0.8rem 2rem; /* REDUCED from 1rem 2.5rem */
            border-radius: 10px; /* REDUCED from 15px */
        }
    }

    .btn-modern::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-modern:hover::before {
        width: 250px; /* REDUCED from 300px */
        height: 250px; /* REDUCED from 300px */
    }

    .btn-success-modern {
        background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
        color: white;
        box-shadow: 0 8px 16px rgba(76, 201, 240, 0.3); /* REDUCED shadow */
    }

    /* Error Toast */
    .error-toast {
        position: fixed;
        top: 10px;
        right: 10px;
        left: 10px;
        background: white;
        border-radius: 8px; /* REDUCED from 10px */
        box-shadow: 0 10px 30px rgba(247, 37, 133, 0.15); /* REDUCED shadow */
        padding: 0.6rem 0.8rem; /* REDUCED from 0.8rem 1rem */
        border-left: 4px solid var(--danger);
        z-index: 9999;
        animation: slideIn 0.3s ease;
        max-width: none;
    }

    @media (min-width: 576px) {
        .error-toast {
            top: 20px;
            right: 20px;
            left: auto;
            border-radius: 10px; /* REDUCED from 15px */
            padding: 0.8rem 1.2rem; /* REDUCED from 1rem 1.5rem */
            max-width: 320px; /* REDUCED from 350px */
        }
    }

    /* Extra Small Devices */
    @media (max-width: 360px) {
        .password-page-header h4 {
            font-size: 1.2rem;
        }

        .requirements-title {
            font-size: 0.9rem;
        }

        .requirements-list li {
            font-size: 0.7rem;
            padding: 0.5rem;
        }

        .btn-modern {
            padding: 0.6rem 0.9rem;
            font-size: 0.75rem;
        }
    }

    /* Landscape mode fix */
    @media (max-height: 500px) and (orientation: landscape) {
        .password-page-header {
            padding: 0.8rem;
        }

        .card-body {
            padding: 0.8rem;
        }

        .requirements-card {
            padding: 0.6rem;
        }
    }

    /* Optional: Sticky requirements on desktop */
    @media (min-width: 992px) {
        .requirements-card {
            position: sticky;
            top: 10px;
            z-index: 10;
        }
    }
</style>

<!-- HTML imebaki sawa kabisa -->
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3 py-md-4">
    <!-- Page Header -->
    <div class="password-page-header d-flex justify-content-between align-items-center">
        <div>
            <h4>
                <i class="fas fa-lock me-2"></i>
                Change Password
            </h4>
            <p class="text-white">Secure your account with a strong password</p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="modern-card">
        <div class="card-body">
            <!-- Password Requirements with Live Validation -->
            <div class="requirements-card">
                <div class="requirements-title">
                    <i class="fas fa-shield-alt"></i>
                    <span>Password Requirements</span>
                </div>
                <ul class="requirements-list" id="requirementsList">
                    <li id="reqLength">
                        <i class="fas fa-circle"></i>
                        <span>Minimum 8 characters</span>
                    </li>
                    <li id="reqLetter">
                        <i class="fas fa-circle"></i>
                        <span>At least one letter</span>
                    </li>
                    <li id="reqNumber">
                        <i class="fas fa-circle"></i>
                        <span>At least one number</span>
                    </li>
                    <li id="reqMatch">
                        <i class="fas fa-circle"></i>
                        <span>Passwords match</span>
                    </li>
                </ul>

                <!-- Password Strength Meter -->
                <div class="password-strength-meter">
                    <div class="password-strength-fill" id="strengthFill" style="width: 0%; height: 100%; background: linear-gradient(90deg, #f72585, #b5179e); transition: width 0.3s ease;"></div>
                </div>
                <div class="strength-text">
                    <span>Password Strength:</span>
                    <span class="strength-badge" id="strengthBadge">Not entered</span>
                </div>
            </div>

            <form class="needs-validation" novalidate action="{{route('change.new.password')}}" method="POST" id="passwordForm">
                @csrf

                <div class="row-custom">
                    <!-- Current Password -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-key"></i>
                            Current Password
                        </label>
                        <div class="input-group-modern">
                            <input type="password"
                                   name="current_password"
                                   class="form-control-modern"
                                   id="currentPassword"
                                   placeholder="Enter current password"
                                   value="{{old('current_password')}}"
                                   required>
                            <span class="input-group-text-modern toggle-password" data-target="currentPassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        @error('current_password')
                            <div class="password-feedback invalid-feedback-custom">
                                <i class="fas fa-exclamation-circle"></i>
                                {{$message}}
                            </div>
                        @enderror
                        @if (Session::has('error'))
                            <div class="password-feedback invalid-feedback-custom">
                                <i class="fas fa-exclamation-circle"></i>
                                {{Session::get('error')}}
                            </div>
                        @endif
                    </div>

                    <!-- New Password -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-lock"></i>
                            New Password
                        </label>
                        <div class="input-group-modern">
                            <input type="password"
                                   name="new_password"
                                   class="form-control-modern"
                                   id="newPassword"
                                   placeholder="Enter new password"
                                   value="{{old('new_password')}}"
                                   required>
                            <span class="input-group-text-modern toggle-password" data-target="newPassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="password-feedback" id="newPasswordFeedback"></div>
                        @error('new_password')
                            <div class="password-feedback invalid-feedback-custom">
                                <i class="fas fa-exclamation-circle"></i>
                                {{$message}}
                            </div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-check-circle"></i>
                            Confirm Password
                        </label>
                        <div class="input-group-modern">
                            <input type="password"
                                   name="confirm_password"
                                   class="form-control-modern"
                                   id="confirmPassword"
                                   placeholder="Confirm new password"
                                   value="{{old('confirm_password')}}"
                                   required>
                            <span class="input-group-text-modern toggle-password" data-target="confirmPassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="password-feedback" id="confirmPasswordFeedback"></div>
                        @error('confirm_password')
                            <div class="password-feedback invalid-feedback-custom">
                                <i class="fas fa-exclamation-circle"></i>
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-3 mt-5">
                    <a href="{{url()->previous()}}" class="btn-modern" style="background: #e9ecef; color: #495057;">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                    <button class="btn-modern btn-success-modern" id="saveButton" type="submit">
                        <i class="fas fa-save me-2"></i>
                        <span>Update Password</span>
                        <i class="fas fa-arrow-right ms-2 floating-effect"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript imebaki sawa kabisa -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById("passwordForm");
    const submitButton = document.getElementById("saveButton");

    // Password fields
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');

    // Requirement elements
    const reqLength = document.getElementById('reqLength');
    const reqLetter = document.getElementById('reqLetter');
    const reqNumber = document.getElementById('reqNumber');
    const reqMatch = document.getElementById('reqMatch');

    // Strength elements
    const strengthFill = document.getElementById('strengthFill');
    const strengthBadge = document.getElementById('strengthBadge');

    // Feedback elements
    const newPasswordFeedback = document.getElementById('newPasswordFeedback');
    const confirmPasswordFeedback = document.getElementById('confirmPasswordFeedback');

    if (!form || !submitButton) return;

    // Toggle password visibility
    document.querySelectorAll(".toggle-password").forEach(item => {
        item.addEventListener("click", function() {
            let input = document.getElementById(this.getAttribute("data-target"));
            let icon = this.querySelector('i');

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Password validation function
    function validatePassword() {
        const password = newPassword.value;
        const confirm = confirmPassword.value;

        // Check requirements
        const hasLength = password.length >= 8;
        const hasLetter = /[A-Za-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasMatch = password === confirm && password !== '';

        // Update requirement icons
        updateRequirement(reqLength, hasLength);
        updateRequirement(reqLetter, hasLetter);
        updateRequirement(reqNumber, hasNumber);
        updateRequirement(reqMatch, hasMatch);

        // Calculate strength
        let strength = 0;
        if (hasLength) strength += 25;
        if (hasLetter) strength += 25;
        if (hasNumber) strength += 25;
        if (password.length >= 12) strength += 15;
        if (/[!@#$%^&*]/.test(password)) strength += 10;

        // Cap at 100
        strength = Math.min(strength, 100);

        // Update strength meter
        strengthFill.style.width = strength + '%';

        // Update strength badge
        if (strength === 0) {
            strengthBadge.textContent = 'Not entered';
            strengthBadge.className = 'strength-badge';
        } else if (strength < 40) {
            strengthBadge.textContent = 'Weak';
            strengthBadge.className = 'strength-badge';
            strengthBadge.style.backgroundColor = '#f72585';
            strengthBadge.style.color = 'white';
        } else if (strength < 70) {
            strengthBadge.textContent = 'Medium';
            strengthBadge.className = 'strength-badge';
            strengthBadge.style.backgroundColor = '#f8961e';
            strengthBadge.style.color = 'white';
        } else if (strength < 90) {
            strengthBadge.textContent = 'Strong';
            strengthBadge.className = 'strength-badge';
            strengthBadge.style.backgroundColor = '#4cc9f0';
            strengthBadge.style.color = 'white';
        } else {
            strengthBadge.textContent = 'Very Strong';
            strengthBadge.className = 'strength-badge';
            strengthBadge.style.backgroundColor = '#28a745';
            strengthBadge.style.color = 'white';
        }

        // Update feedback
        if (password.length > 0) {
            if (!hasLength) {
                newPasswordFeedback.innerHTML = '<i class="fas fa-exclamation-circle"></i> Password too short';
                newPasswordFeedback.className = 'password-feedback invalid-feedback-custom';
                newPasswordFeedback.style.color = '#f72585';
            } else if (!hasLetter || !hasNumber) {
                newPasswordFeedback.innerHTML = '<i class="fas fa-exclamation-circle"></i> Must contain letters and numbers';
                newPasswordFeedback.className = 'password-feedback invalid-feedback-custom';
                newPasswordFeedback.style.color = '#f72585';
            } else {
                newPasswordFeedback.innerHTML = '<i class="fas fa-check-circle"></i> Password meets requirements';
                newPasswordFeedback.className = 'password-feedback valid-feedback-custom';
                newPasswordFeedback.style.color = '#28a745';
            }
        } else {
            newPasswordFeedback.innerHTML = '';
        }

        // Confirm password feedback
        if (confirm.length > 0) {
            if (hasMatch) {
                confirmPasswordFeedback.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
                confirmPasswordFeedback.className = 'password-feedback valid-feedback-custom';
                confirmPasswordFeedback.style.color = '#28a745';
            } else {
                confirmPasswordFeedback.innerHTML = '<i class="fas fa-exclamation-circle"></i> Passwords do not match';
                confirmPasswordFeedback.className = 'password-feedback invalid-feedback-custom';
                confirmPasswordFeedback.style.color = '#f72585';
            }
        } else {
            confirmPasswordFeedback.innerHTML = '';
        }

        return hasLength && hasLetter && hasNumber && hasMatch;
    }

    function updateRequirement(element, isValid) {
        const icon = element.querySelector('i');
        if (isValid) {
            element.classList.add('valid');
            icon.classList.remove('fa-circle');
            icon.classList.add('fa-check-circle');
            icon.style.color = '#28a745';
        } else {
            element.classList.remove('valid');
            icon.classList.remove('fa-check-circle');
            icon.classList.add('fa-circle');
            icon.style.color = '#6c757d';
        }
    }

    // Add input event listeners
    newPassword.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);

    // Form submission handling
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Validate all fields
        const isValid = validatePassword();

        if (!isValid) {
            // Show error toast
            showErrorToast('Please meet all password requirements');

            // Scroll to requirements
            document.querySelector('.requirements-card').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            return;
        }

        // Check if form is valid
        if (!form.checkValidity()) {
            form.classList.add("was-validated");

            // Find first invalid field
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }

            return;
        }

        // Disable button and submit
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm text-white me-2" role="status"></span>
            <span>Updating Password...</span>
        `;

        // Submit the form
        setTimeout(() => {
            form.submit();
        }, 500);
    });

    // Error toast function
    function showErrorToast(message) {
        // Remove existing toast
        const existingToast = document.querySelector('.error-toast');
        if (existingToast) {
            existingToast.remove();
        }

        // Create new toast
        const toast = document.createElement('div');
        toast.className = 'error-toast';
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 30px; height: 30px; background: #f72585; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="fas fa-exclamation"></i>
                </div>
                <div style="flex: 1;">
                    <strong style="color: #1e1e2f; font-size: 0.9rem;">Validation Error</strong>
                    <div style="color: #6c757d; font-size: 0.8rem; margin-top: 2px;">${message}</div>
                </div>
                <div onclick="this.parentElement.parentElement.remove()" style="cursor: pointer; color: #6c757d;">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        `;

        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }

    // Initial validation if fields have values
    if (newPassword.value || confirmPassword.value) {
        validatePassword();
    }

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
});
</script>
@endsection
