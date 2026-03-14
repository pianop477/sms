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
    }

    .password-page-header {
        background: var(--gradient-1);
        border-radius: 25px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .password-page-header::before {
        content: '🔒';
        font-size: 8rem;
        position: absolute;
        right: 20px;
        bottom: -20px;
        opacity: 0.2;
        transform: rotate(15deg);
    }

    .password-page-header h4 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .password-page-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    .modern-card {
        background: white;
        border-radius: 25px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.2);
    }

    .card-body {
        padding: 2.5rem;
    }

    .requirements-card {
        background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
        border-radius: 20px;
        padding: 1.8rem;
        margin-bottom: 2.5rem;
        border-left: 5px solid var(--primary);
        position: relative;
        overflow: hidden;
    }

    .requirements-card::after {
        content: '⚡';
        font-size: 4rem;
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.1;
    }

    .requirements-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 1.2rem;
    }

    .requirements-title i {
        font-size: 1.8rem;
        color: var(--primary);
    }

    .requirements-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .requirements-list li {
        flex: 1 1 auto;
        min-width: 250px;
        padding: 0.8rem 1.2rem;
        background: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        color: var(--dark);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border: 1px solid rgba(67, 97, 238, 0.1);
    }

    .requirements-list li i {
        font-size: 1.2rem;
        color: var(--primary);
    }

    .requirements-list li.valid {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-color: #28a745;
    }

    .requirements-list li.valid i {
        color: #28a745;
    }

    .password-strength-meter {
        margin: 1.5rem 0;
        background: #e9ecef;
        border-radius: 30px;
        height: 10px;
        overflow: hidden;
        position: relative;
    }

    .password-strength-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #f72585, #b5179e, #7209b7, #3f37c9, #4361ee);
        border-radius: 30px;
        transition: width 0.5s ease;
        position: relative;
        overflow: hidden;
    }

    .password-strength-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .strength-text {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .strength-badge {
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .strength-badge.weak {
        background: #f8d7da;
        color: #721c24;
    }

    .strength-badge.medium {
        background: #fff3cd;
        color: #856404;
    }

    .strength-badge.strong {
        background: #d4edda;
        color: #155724;
    }

    .strength-badge.very-strong {
        background: #c3e6cb;
        color: #0b5e42;
    }

    .form-group-modern {
        margin-bottom: 2rem;
        position: relative;
    }

    .form-label-modern {
        display: block;
        margin-bottom: 0.8rem;
        font-weight: 600;
        color: var(--dark);
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-label-modern i {
        margin-right: 8px;
        color: var(--primary);
        font-size: 1.1rem;
    }

    .input-group-modern {
        display: flex;
        align-items: stretch;
        position: relative;
    }

    .input-group-modern .form-control-modern {
        flex: 1;
        padding: 1rem 1.2rem;
        font-size: 1rem;
        border: 2px solid #eef2f6;
        border-radius: 15px 0 0 15px;
        background-color: #fafbfc;
        transition: all 0.3s ease;
        color: var(--dark);
        font-family: 'Courier New', monospace;
        letter-spacing: 2px;
    }

    .input-group-modern .form-control-modern:focus {
        outline: none;
        border-color: var(--primary);
        background-color: white;
        box-shadow: 0 5px 20px rgba(67, 97, 238, 0.1);
    }

    .input-group-modern .input-group-text-modern {
        padding: 0 1.5rem;
        background: white;
        border: 2px solid #eef2f6;
        border-left: none;
        border-radius: 0 15px 15px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #6c757d;
    }

    .input-group-modern .input-group-text-modern:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .input-group-modern .input-group-text-modern i {
        font-size: 1.2rem;
    }

    .password-feedback {
        margin-top: 0.5rem;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .password-feedback.valid-feedback-custom {
        color: #28a745;
    }

    .password-feedback.invalid-feedback-custom {
        color: var(--danger);
    }

    .btn-modern {
        padding: 1rem 2.5rem;
        border-radius: 15px;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        position: relative;
        overflow: hidden;
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
        width: 300px;
        height: 300px;
    }

    .btn-success-modern {
        background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
        color: white;
        box-shadow: 0 10px 20px rgba(76, 201, 240, 0.3);
    }

    .btn-success-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(76, 201, 240, 0.4);
    }

    .btn-success-modern:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .floating-effect {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .error-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 15px 40px rgba(247, 37, 133, 0.15);
        padding: 1rem 1.5rem;
        border-left: 4px solid var(--danger);
        z-index: 9999;
        animation: slideIn 0.3s ease;
        max-width: 350px;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .error-toast .toast-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .error-toast .toast-icon {
        width: 40px;
        height: 40px;
        background: var(--danger);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    .error-toast .toast-message {
        flex: 1;
        font-size: 0.95rem;
        color: var(--dark);
    }

    .error-toast .toast-close {
        cursor: pointer;
        color: #6c757d;
        transition: color 0.3s ease;
    }

    .error-toast .toast-close:hover {
        color: var(--danger);
    }

    .row-custom {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }

        .password-page-header h4 {
            font-size: 1.5rem;
        }

        .requirements-list li {
            min-width: 100%;
        }

        .btn-modern {
            width: 100%;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
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
                        <span>Minimum 8 characters long</span>
                    </li>
                    <li id="reqLetter">
                        <i class="fas fa-circle"></i>
                        <span>Contains at least one letter</span>
                    </li>
                    <li id="reqNumber">
                        <i class="fas fa-circle"></i>
                        <span>Contains at least one number</span>
                    </li>
                    <li id="reqMatch">
                        <i class="fas fa-circle"></i>
                        <span>Passwords match</span>
                    </li>
                </ul>

                <!-- Password Strength Meter -->
                <div class="password-strength-meter">
                    <div class="password-strength-fill" id="strengthFill"></div>
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

<!-- JavaScript -->
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
            strengthBadge.className = 'strength-badge weak';
        } else if (strength < 70) {
            strengthBadge.textContent = 'Medium';
            strengthBadge.className = 'strength-badge medium';
        } else if (strength < 90) {
            strengthBadge.textContent = 'Strong';
            strengthBadge.className = 'strength-badge strong';
        } else {
            strengthBadge.textContent = 'Very Strong';
            strengthBadge.className = 'strength-badge very-strong';
        }

        // Update feedback
        if (password.length > 0) {
            if (!hasLength) {
                newPasswordFeedback.innerHTML = '<i class="fas fa-exclamation-circle"></i> Password too short';
                newPasswordFeedback.className = 'password-feedback invalid-feedback-custom';
            } else if (!hasLetter || !hasNumber) {
                newPasswordFeedback.innerHTML = '<i class="fas fa-exclamation-circle"></i> Must contain letters and numbers';
                newPasswordFeedback.className = 'password-feedback invalid-feedback-custom';
            } else {
                newPasswordFeedback.innerHTML = '<i class="fas fa-check-circle"></i> Password meets requirements';
                newPasswordFeedback.className = 'password-feedback valid-feedback-custom';
            }
        } else {
            newPasswordFeedback.innerHTML = '';
        }

        // Confirm password feedback
        if (confirm.length > 0) {
            if (hasMatch) {
                confirmPasswordFeedback.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
                confirmPasswordFeedback.className = 'password-feedback valid-feedback-custom';
            } else {
                confirmPasswordFeedback.innerHTML = '<i class="fas fa-exclamation-circle"></i> Passwords do not match';
                confirmPasswordFeedback.className = 'password-feedback invalid-feedback-custom';
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
        } else {
            element.classList.remove('valid');
            icon.classList.remove('fa-check-circle');
            icon.classList.add('fa-circle');
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
            <div class="toast-content">
                <div class="toast-icon">
                    <i class="fas fa-exclamation"></i>
                </div>
                <div class="toast-message">
                    <strong>Validation Error</strong><br>
                    <small>${message}</small>
                </div>
                <div class="toast-close" onclick="this.parentElement.parentElement.remove()">
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
