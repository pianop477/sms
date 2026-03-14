{{-- resources/views/Contract/gateway_spa.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShuleApp | Contract Gateway</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ filemtime(public_path('manifest.json')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-16x16.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-32 x 32.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-192 x 192.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-512 x 512.png') }}">
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.5.2-web/css/all.css') }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            /* Added padding for mobile */
        }

        .gateway-container {
            width: 100%;
            max-width: 480px;
            /* Limit maximum width */
            margin: 0 auto;
        }

        .gateway-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            padding: 30px 20px;
            /* Reduced padding for mobile */
            animation: slideUp 0.5s ease;
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

        .gateway-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .gateway-header h2 {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: clamp(1.3rem, 6vw, 1.8rem);
            /* Responsive font size */
            line-height: 1.3;
            word-break: break-word;
        }

        .gateway-header p {
            color: #718096;
            font-size: 0.95rem;
        }

        .gateway-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .gateway-icon i {
            font-size: 40px;
            color: white;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* OTP Input Section - Fixed for mobile */
        .otp-input-group {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 16px 0 20px;
            flex-wrap: wrap;
            /* Allows wrapping on very small screens */
        }

        .otp-input {
            width: 48px;
            height: 56px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            transition: all 0.3s;
            background: #fafbfc;
            -moz-appearance: textfield;
            appearance: textfield;
        }

        .otp-input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .timer {
            text-align: center;
            color: #718096;
            margin: 15px 0;
            font-size: 0.9rem;
        }

        .timer .time {
            font-weight: 700;
            color: #667eea;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            line-height: 1.5;
            word-break: break-word;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #fc8181;
        }

        .alert-info {
            background: #bee3f8;
            color: #2c5282;
            border: 1px solid #90cdf4;
        }

        .staff-info {
            background: #f7fafc;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .staff-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .staff-info-item i {
            width: 24px;
            color: #667eea;
            margin-right: 10px;
        }

        .resend-link {
            text-align: center;
            margin-top: 15px;
        }

        .resend-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        .resend-link a:hover {
            text-decoration: underline;
        }

        .resend-link a.disabled {
            color: #cbd5e0;
            cursor: not-allowed;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .footer {
            text-align: center;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.85rem;
            padding: 0 10px;
        }

        .footer p {
            line-height: 1.5;
        }

        .footer a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            opacity: 0.9;
            transition: opacity 0.3s;
            padding: 4px 8px;
            display: inline-block;
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        @media (max-width: 340px) {
            .otp-input {
                width: 35px;
                height: 42px;
                font-size: 18px;
            }

            .gateway-card {
                padding: 20px 12px;
                /* Even smaller for very small phones */
            }
        }

        /* Responsive OTP inputs */
        @media (max-width: 400px) {
            .otp-input-group {
                gap: 5px;
            }

            .otp-input {
                width: 40px;
                height: 48px;
                font-size: 20px;
                border-radius: 10px;
            }

            .gateway-icon {
                width: 60px;
                height: 60px;
            }

            .gateway-icon i {
                font-size: 30px;
            }

            .gateway-card {
                padding: 25px 16px;
                /* Smaller padding on small phones */
                border-radius: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="gateway-container">
        <div class="gateway-card" id="gateway-card">
            <div class="gateway-header">
                <div class="gateway-icon">
                    <i class="fas fa-id-card" id="current-icon"></i>
                </div>
                <h2 id="current-title">Contracts Gateway System</h2>
                <p id="current-subtitle" style="font-weight: bold; color:#1155a9; font-style:italic;">Mikataba Yako, Udhibiti ni Wako</p>
            </div>

            <!-- Alert Messages -->
            <div id="alert-message" class="alert" style="display: none;"></div>

            <!-- Step 1: Staff ID Form -->
            <div id="step-staff-id" class="section active">
                <form id="staff-id-form">
                    <div class="form-group">
                        <label class="form-label">Ingiza Namba ya Utambulisho</label>
                        <input type="text" id="staff-id-input" class="form-control text-uppercase"
                            placeholder="Ingiza ID" maxlength="12" required>
                        <small id="staff-id-error" class="text-danger" style="display: none;"></small>
                    </div>
                    <button type="submit" id="staff-id-submit" class="btn-submit">
                        <span class="spinner" id="staff-id-spinner" style="display: none;"></span>
                        <span id="staff-id-text">Hakiki ID</span>
                    </button>
                </form>
            </div>

            <!-- Step 2: OTP Verification -->
            <div id="step-otp" class="section">
                <div class="staff-info" id="staff-info">
                    <div class="staff-info-item">
                        <i class="fas fa-user"></i>
                        <span><strong id="staff-name"></strong></span>
                    </div>
                    <div class="staff-info-item">
                        <i class="fas fa-phone"></i>
                        <span id="staff-phone"></span>
                    </div>
                    <div class="staff-info-item">
                        <i class="fas fa-tag"></i>
                        <span id="staff-type"></span>
                    </div>
                </div>

                <form id="otp-form">
                    <div class="form-group">
                        <label class="form-label">Ingiza Msimbo OTP</label>
                        <div class="otp-input-group" id="otp-inputs">
                            <!-- OTP inputs will be generated by JavaScript -->
                        </div>
                    </div>

                    <div class="timer">
                        <i class="far fa-clock mr-1"></i>
                        Muda uliobaki: <span class="time" id="otp-timer">0:00</span>
                    </div>

                    <button type="submit" id="otp-submit" class="btn-submit" disabled>
                        <span class="spinner" id="otp-spinner" style="display: none;"></span>
                        <span id="otp-text">Hakiki OTP</span>
                    </button>

                    <div class="resend-link">
                        <a href="#" id="resend-otp-link">Tuma upya OTP</a>
                    </div>
                </form>
            </div>

            <!-- Loading State -->
            <div id="step-loading" class="section text-center py-4">
                <div class="spinner" style="width: 40px; height: 40px;"></div>
                <p class="mt-3" id="loading-message">Inachakata, subiri...</p>
            </div>
        </div>

        <div class="footer">
            @php
                $startYear = 2025;
                $currentYear = date('Y');
            @endphp
            <p><a href="{{ route('welcome') }}"><i class="fas fa-home"></i> Nyumbani</a></p>
            <p>© {{ $startYear == $currentYear ? $startYear : $startYear . ' - ' . $currentYear }} ShuleApp. Haki zote zimehifadhiwa</p>
        </div>
    </div>

    {{-- <script src="{{asset('assets/js/js.js')}}"></script> --}}
    <script>
        (function() {
            'use strict';

            // State
            let currentStep = 'staff-id';
            let tempToken = null;
            let otpId = null;
            let otpTimer = 0;
            let otpTimerInterval = null;
            let resendTimer = 0;
            let resendTimerInterval = null;
            let loading = false;

            // DOM Elements
            const elements = {
                steps: {
                    staffId: document.getElementById('step-staff-id'),
                    otp: document.getElementById('step-otp'),
                    loading: document.getElementById('step-loading')
                },
                icon: document.getElementById('current-icon'),
                title: document.getElementById('current-title'),
                subtitle: document.getElementById('current-subtitle'),
                alert: document.getElementById('alert-message'),

                // Staff ID form
                staffIdForm: document.getElementById('staff-id-form'),
                staffIdInput: document.getElementById('staff-id-input'),
                staffIdError: document.getElementById('staff-id-error'),
                staffIdSubmit: document.getElementById('staff-id-submit'),
                staffIdSpinner: document.getElementById('staff-id-spinner'),
                staffIdText: document.getElementById('staff-id-text'),

                // OTP section
                staffName: document.getElementById('staff-name'),
                staffPhone: document.getElementById('staff-phone'),
                staffType: document.getElementById('staff-type'),
                otpInputs: document.getElementById('otp-inputs'),
                otpTimer: document.getElementById('otp-timer'),
                otpForm: document.getElementById('otp-form'),
                otpSubmit: document.getElementById('otp-submit'),
                otpSpinner: document.getElementById('otp-spinner'),
                otpText: document.getElementById('otp-text'),
                resendLink: document.getElementById('resend-otp-link'),

                // Loading
                loadingMessage: document.getElementById('loading-message')
            };

            // Initialize
            function init() {
                console.log('Initializing gateway...');
                showStep('staff-id');
                setupEventListeners();
                generateOtpInputs();
            }

            // Setup event listeners
            function setupEventListeners() {
                elements.staffIdForm.addEventListener('submit', submitStaffId);
                elements.otpForm.addEventListener('submit', verifyOtp);
                elements.resendLink.addEventListener('click', resendOtp);
            }

            // Generate OTP inputs
            function generateOtpInputs() {
                let html = '';
                for (let i = 0; i < 6; i++) {
                    html += `<input type="text" class="otp-input" maxlength="1" data-index="${i}">`;
                }
                elements.otpInputs.innerHTML = html;

                // Add event listeners to OTP inputs
                document.querySelectorAll('.otp-input').forEach(input => {
                    input.addEventListener('input', handleOtpInput);
                    input.addEventListener('keydown', handleOtpBackspace);
                });
            }

            // Handle OTP input
            function handleOtpInput(e) {
                const input = e.target;
                input.value = input.value.replace(/[^0-9]/g, '');

                if (input.value && input.dataset.index < 5) {
                    const nextInput = document.querySelector(
                        `.otp-input[data-index="${parseInt(input.dataset.index) + 1}"]`);
                    if (nextInput) nextInput.focus();
                }

                updateOtpSubmitButton();
            }

            // Handle OTP backspace
            function handleOtpBackspace(e) {
                const input = e.target;

                if (e.key === 'Backspace' && !input.value && input.dataset.index > 0) {
                    const prevInput = document.querySelector(
                        `.otp-input[data-index="${parseInt(input.dataset.index) - 1}"]`);
                    if (prevInput) {
                        prevInput.focus();
                        prevInput.value = '';
                    }
                }

                updateOtpSubmitButton();
            }

            // Update OTP submit button state
            function updateOtpSubmitButton() {
                const inputs = document.querySelectorAll('.otp-input');
                const allFilled = Array.from(inputs).every(input => input.value.length === 1);
                elements.otpSubmit.disabled = !allFilled || loading;
            }

            // Get OTP code from inputs
            function getOtpCode() {
                const inputs = document.querySelectorAll('.otp-input');
                return Array.from(inputs).map(input => input.value).join('');
            }

            // Clear OTP inputs
            function clearOtpInputs() {
                document.querySelectorAll('.otp-input').forEach(input => input.value = '');
                updateOtpSubmitButton();
            }

            // Show step
            function showStep(step) {
                currentStep = step;

                // Hide all steps
                Object.values(elements.steps).forEach(el => el.classList.remove('active'));

                // Show current step
                if (step === 'staff-id') elements.steps.staffId.classList.add('active');
                else if (step === 'otp') elements.steps.otp.classList.add('active');
                else if (step === 'loading') elements.steps.loading.classList.add('active');

                // Update header
                updateHeader(step);
            }

            // Update header based on step
            function updateHeader(step) {
                const headers = {
                    'staff-id': {
                        icon: 'fa-id-card',
                        title: 'Contracts Gateway System',
                        subtitle: 'Mikataba Yako, Udhibiti ni Wako'
                    },
                    'otp': {
                        icon: 'fa-mobile-alt',
                        title: 'Hakiki Utambulisho wako',
                        subtitle: 'Ingiza Msimbo wa OTP, angalia Simu'
                    },
                    'loading': {
                        icon: 'fa-spinner fa-pulse',
                        title: 'Tafadhali Subiri',
                        subtitle: elements.loadingMessage.textContent
                    }
                };

                const header = headers[step] || headers['staff-id'];
                elements.icon.className = `fas ${header.icon}`;
                elements.title.textContent = header.title;
                elements.subtitle.textContent = header.subtitle;
            }

            // Show alert
            function showAlert(message, type = 'info') {
                elements.alert.textContent = message;
                elements.alert.className = `alert alert-${type}`;
                elements.alert.style.display = 'block';

                setTimeout(() => {
                    elements.alert.style.display = 'none';
                }, 5000);
            }

            // Set loading state
            function setLoading(isLoading, step = currentStep) {
                loading = isLoading;

                if (step === 'staff-id') {
                    elements.staffIdSubmit.disabled = isLoading;
                    elements.staffIdSpinner.style.display = isLoading ? 'inline-block' : 'none';
                    elements.staffIdText.textContent = isLoading ? 'Inahakiki...' : 'Hakiki ID';
                    elements.staffIdInput.disabled = isLoading;
                } else if (step === 'otp') {
                    elements.otpSubmit.disabled = isLoading || getOtpCode().length < 6;
                    elements.otpSpinner.style.display = isLoading ? 'inline-block' : 'none';
                    elements.otpText.textContent = isLoading ? 'Inahakiki...' : 'Hakiki OTP';
                    document.querySelectorAll('.otp-input').forEach(input => input.disabled = isLoading);
                }
            }

            // Start OTP timer
            function startOtpTimer(seconds) {
                if (otpTimerInterval) clearInterval(otpTimerInterval);

                otpTimer = seconds;
                updateOtpTimerDisplay();

                otpTimerInterval = setInterval(() => {
                    otpTimer--;
                    updateOtpTimerDisplay();

                    if (otpTimer <= 0) {
                        clearInterval(otpTimerInterval);
                        showAlert('OTP Imekwisha muda wake. Tafadhali omba tena.', 'error');
                    }
                }, 1000);
            }

            // Update OTP timer display
            function updateOtpTimerDisplay() {
                const mins = Math.floor(otpTimer / 60);
                const secs = otpTimer % 60;
                elements.otpTimer.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
            }

            // Start resend timer
            function startResendTimer() {
                if (resendTimerInterval) clearInterval(resendTimerInterval);

                resendTimer = 30;
                elements.resendLink.classList.add('disabled');
                elements.resendLink.textContent = `Resend in ${resendTimer}s`;

                resendTimerInterval = setInterval(() => {
                    resendTimer--;
                    elements.resendLink.textContent = `Resend in ${resendTimer}s`;

                    if (resendTimer <= 0) {
                        clearInterval(resendTimerInterval);
                        elements.resendLink.classList.remove('disabled');
                        elements.resendLink.textContent = 'Tuma OTP';
                    }
                }, 1000);
            }

            // Submit Staff ID
            async function submitStaffId(e) {
                e.preventDefault();

                const staffId = elements.staffIdInput.value.trim();

                if (!staffId) {
                    elements.staffIdError.textContent = 'Utambulisho hautambuliki';
                    elements.staffIdError.style.display = 'block';
                    return;
                }

                elements.staffIdError.style.display = 'none';
                setLoading(true, 'staff-id');

                try {
                    const response = await fetch('/contract-gateway/api/verify-staff', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            staff_id: staffId
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        if (data.data.has_active_session) {
                            localStorage.setItem('contract_auth_token', data.data.auth_token);
                            showAlert('Session ipo, tafadhali subiri...', 'success');

                            setTimeout(() => {
                                window.location.href = data.data.redirect_to || '/contracts/dashboard';
                            }, 1500);
                        } else {
                            tempToken = data.data.temp_token;

                            // Update staff info
                            elements.staffName.textContent = data.data.staff_name;
                            elements.staffPhone.textContent = data.data.phone_masked;
                            elements.staffType.textContent = data.data.staff_type;

                            showStep('loading');
                            elements.loadingMessage.textContent = 'Tuma OTP...';

                            await requestOtp();
                        }
                    } else {
                        showAlert(data.message, 'error');
                        setLoading(false, 'staff-id');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Network error. Please try again.', 'error');
                    setLoading(false, 'staff-id');
                }
            }

            // Request OTP
            async function requestOtp() {
                try {
                    const response = await fetch('/contract-gateway/api/request-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            temp_token: tempToken
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        otpId = data.data.otp_id;
                        startOtpTimer(data.data.expires_in);
                        startResendTimer();
                        clearOtpInputs();
                        showStep('otp');
                        showAlert('OTP sent successfully! Check your phone.', 'success');
                        setLoading(false, 'otp');

                        // Focus first OTP input
                        setTimeout(() => {
                            document.querySelector('.otp-input')?.focus();
                        }, 100);
                    } else {
                        showAlert(data.message, 'error');
                        showStep('staff-id');
                        setLoading(false, 'staff-id');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Failed to send OTP. Please try again.', 'error');
                    showStep('staff-id');
                    setLoading(false, 'staff-id');
                }
            }

            // Verify OTP
            async function verifyOtp(e) {
                e.preventDefault();

                const otpCode = getOtpCode();

                if (otpCode.length < 6) return;

                setLoading(true, 'otp');

                try {
                    const response = await fetch('/contract-gateway/api/verify-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            otp_id: otpId,
                            otp_code: otpCode,
                            temp_token: tempToken
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        localStorage.setItem('contract_auth_token', data.data.auth_token);
                        showAlert('Verification successful! Redirecting...', 'success');

                        setTimeout(() => {
                            window.location.href = '/contracts/dashboard';
                        }, 1500);
                    } else {
                        showAlert(data.message, 'error');
                        clearOtpInputs();
                        setLoading(false, 'otp');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Verification failed. Please try again.', 'error');
                    setLoading(false, 'otp');
                }
            }

            // Resend OTP
            async function resendOtp(e) {
                e.preventDefault();

                if (e.target.classList.contains('disabled')) return;

                setLoading(true, 'loading');
                elements.loadingMessage.textContent = 'Resending OTP...';
                showStep('loading');

                try {
                    const response = await fetch('/contract-gateway/api/resend-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            temp_token: tempToken
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        otpId = data.data.otp_id;
                        startOtpTimer(data.data.expires_in);
                        startResendTimer();
                        clearOtpInputs();
                        showStep('otp');
                        showAlert('OTP resent successfully!', 'success');
                        setLoading(false, 'otp');
                    } else {
                        showAlert(data.message, 'error');
                        showStep('otp');
                        setLoading(false, 'otp');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Failed to resend OTP. Please try again.', 'error');
                    showStep('otp');
                    setLoading(false, 'otp');
                }
            }

            // Start the app
            document.addEventListener('DOMContentLoaded', init);
        })();
    </script>
</body>

</html>
