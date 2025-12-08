<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ShuleApp | Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="shortcut icon" type="image/png" href="{{asset('assets/img/favicon/favicon.ico')}}">
  <style>
    :root {
      --primary: #6366f1;
      --primary-dark: #4f46e5;
      --accent: #10b981;
      --dark: #0f172a;
      --light: #f8fafc;
      --glass-bg: rgba(255, 255, 255, 0.05);
      --glass-border: rgba(255, 255, 255, 0.15);
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #1e293b, #0f172a);
      color: var(--light);
      min-height: 100vh;
      margin: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-card {
      width: 100%;
      max-width: 320px;
      background: var(--glass-bg);
      backdrop-filter: blur(12px);
      border-radius: 14px;
      border: 1px solid var(--glass-border);
      padding: 26px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      transition: all 0.3s ease;
    }

    .login-card:hover {
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
    }

    .login-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .login-title {
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .login-subtitle {
      font-size: 13px;
      color: rgba(255, 255, 255, 0.6);
    }

    .form-group {
      margin-bottom: 14px;
    }

    .form-label {
      display: block;
      margin-bottom: 5px;
      font-size: 13px;
      font-weight: 500;
      color: rgba(255, 255, 255, 0.8);
    }

    .form-control {
      width: 93%;
      padding: 9px 12px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 8px;
      color: white;
      font-size: 14px;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .form-options {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 18px;
      font-size: 13px;
    }

    .remember-me {
      display: flex;
      align-items: center;
    }

    .remember-me input {
      margin-right: 6px;
      accent-color: var(--primary);
    }

    .forgot-password {
      color: var(--primary);
      text-decoration: none;
    }

    .forgot-password:hover {
      text-decoration: underline;
    }

    .btn {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      border: none;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 20px 0;
      color: rgba(255, 255, 255, 0.3);
      font-size: 12px;
    }

    .divider::before,
    .divider::after {
      content: "";
      flex: 1;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .divider::before {
      margin-right: 10px;
    }

    .divider::after {
      margin-left: 10px;
    }

    .bio-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      font-size: 14px;
      background: rgba(16, 185, 129, 0.1);
      color: var(--accent);
      border: 1px solid rgba(16, 185, 129, 0.2);
      cursor: pointer;
    }

    .setup-bio {
      display: block;
      text-align: center;
      margin-top: 14px;
      font-size: 12px;
      color: rgba(255, 255, 255, 0.5);
    }

    .setup-bio:hover {
      color: var(--accent);
    }

    .password-toggle {
      position: absolute;
      right: 0;
      top: 32px;
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.5);
      cursor: pointer;
    }

    .toast {
      position: fixed;
      top: 20px;
      right: 20px;
      background: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 10px 16px;
      border-radius: 8px;
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 8px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.3s ease;
    }

    .toast.show {
      opacity: 1;
      transform: translateY(0);
    }

    .spinner {
      display: inline-block;
      width: 16px;
      height: 16px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s linear infinite;
      margin-right: 6px;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .footer {
      margin-top: 20px;
      text-align: center;
      font-size: 12px;
      color: rgba(255, 255, 255, 0.4);
    }

    .footer a {
      color: var(--primary);
      text-decoration: none;
    }

    .otp-digit {
      width: 100%;
      height: 48px;
      text-align: center;
      font-size: 18px;
      border-radius: 8px;
      border: 1px solid #334155;
      background: rgba(255,255,255,0.05);
      color: white;
    }

    .otp-container {
      display: flex;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 20px;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 42px;
      height: 22px;
    }

    .switch input {
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
      background-color: rgba(255, 255, 255, 0.2);
      transition: .4s;
      border-radius: 24px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 16px;
      width: 16px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: var(--primary);
    }

    input:checked + .slider:before {
      transform: translateX(20px);
    }

    .setup-bio {
    display: block;
    text-align: center;
    margin-top: 14px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
}

    .setup-bio:hover {
        color: var(--accent);
    }

    /* Style maalum kwa clear button */
    #clearBioBtn:hover {
        color: #f87171;
        text-decoration: underline;
    }
    /* Brand Section - Compact */
    .brand-section {
      text-align: center;
      margin-bottom: 1.5rem;
      flex-shrink: 0;
    }

    .logo {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 0.75rem;
      box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
    }

    .logo i {
      font-size: 1.6rem;
      color: white;
    }
  </style>
</head>
<body>
    <div class="brand-section">
      {{-- <div class="logo">
        <i class="fas fa-graduation-cap"></i>
      </div> --}}
      <h1 class="brand-title">ShuleApp</h1>
      <p class="brand-subtitle">Welcome back, please sign in</p>
    </div>
  <div class="login-card">
    @if(session('error'))
      <div style="background: rgba(239, 68, 68, 0.1); padding: 10px; border-radius: 6px; border-left: 3px solid #ef4444; margin-bottom: 16px; font-size: 13px;">
        <i class="fas fa-exclamation-circle" style="margin-right: 6px;"></i>
        {{ session('error') }}
      </div>
    @endif

    <form id="loginForm" method="POST" action="{{ route('login') }}">
      @csrf
      <div class="form-group">
        <label for="login" class="form-label">Email or Phone</label>
        <input type="text" id="login" name="username" class="form-control" placeholder="user@example.com" required />
      </div>

      <div class="form-group" style="position: relative;">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required />
        <button type="button" class="password-toggle" id="togglePassword">
          <i class="fas fa-eye"></i>
        </button>
      </div>

      <div class="form-options">
        <label class="remember-me">
          <input type="checkbox" name="remember" id="remember" /> Remember me
        </label>
        <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
      </div>

      <button type="submit" class="btn btn-primary" id="loginBtn">
        <span id="btnText">Sign In</span>
      </button>
    </form>

    <!-- Biometric toggle and section -->
    <hr>
    <div class="form-group" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
      <label style="font-size: 13px; color: rgba(255, 255, 255, 0.8);">Enable Biometric Login</label>
      <label class="switch">
        <input type="checkbox" id="biometricToggle">
        <span class="slider round"></span>
      </label>
    </div>

    <div id="biometricSection" style="display: none;">
        <div class="divider">or continue with</div>
        <button id="bioBtn" class="bio-btn">
            <i class="fas fa-fingerprint" style="font-size: 1.5rem"></i>
            <span id="bioBtnText">Use Biometric</span>
        </button>
        <div id="biometricActions" style="display: none; justify-content: center; gap: 10px; margin-top: 10px;">
            <a href="#" id="setupBioBtn" class="setup-bio">Set up biometric authentication</a>
            <a href="#" id="clearBioBtn" class="setup-bio" style="color: #f87171;">Clear Biometric</a>
        </div>
    </div>
  </div>

  <div class="footer">
        @php
            $startYear = 2025;
            $currentYear = date('Y');
        @endphp
    &copy; {{ $startYear == $currentYear ? $startYear : $startYear . ' - ' . $currentYear }} ShuleApp. All rights reserved.
  </div>

  <div id="toast" class="toast">
    <i class="fas fa-check-circle"></i>
    <span id="toastMessage">Message here</span>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize biometric settings from localStorage
        const bioSettings = JSON.parse(localStorage.getItem('bioSettings') || '{"enabled":false,"registered":false,"username":null}');

        // Get all elements
        const biometricToggle = document.getElementById('biometricToggle');
        const biometricSection = document.getElementById('biometricSection');
        const bioBtn = document.getElementById('bioBtn');
        const bioBtnText = document.getElementById('bioBtnText');
        const setupBioBtn = document.getElementById('setupBioBtn');
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const clearBioBtn = document.getElementById('clearBioBtn');
        const biometricActions = document.getElementById('biometricActions');

        // Set initial state
        function updateBiometricUI() {
            if (biometricToggle) biometricToggle.checked = bioSettings.enabled;
            if (biometricSection) biometricSection.style.display = bioSettings.enabled ? 'block' : 'none';
            if (biometricActions) biometricActions.style.display = bioSettings.enabled ? 'flex' : 'none';
            if (bioBtnText) bioBtnText.textContent = bioSettings.registered ? 'Use Biometric' : 'Set Up Biometrics';
            if (setupBioBtn) setupBioBtn.style.display = bioSettings.registered ? 'none' : 'block';
            if (clearBioBtn) clearBioBtn.style.display = bioSettings.registered ? 'block' : 'none';
        }

        // Initialize UI
        updateBiometricUI();

        // Password toggle visibility
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                togglePassword.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
        }

        // Check WebAuthn support
        if (!window.PublicKeyCredential) {
            if (bioBtn) {
                bioBtn.disabled = true;
                bioBtn.style.opacity = '0.7';
                bioBtn.style.cursor = 'not-allowed';
                bioBtnText.textContent = 'Biometrics Not Supported';
            }
            if (setupBioBtn) setupBioBtn.style.display = 'none';
            if (biometricToggle) biometricToggle.disabled = true;
        }

        // Biometric toggle handler
        if (biometricToggle) {
            biometricToggle.addEventListener('change', function(e) {
                bioSettings.enabled = e.target.checked;
                localStorage.setItem('bioSettings', JSON.stringify(bioSettings));
                updateBiometricUI();
            });
        }

        // Helper functions
        function showToast(message, type = 'success', duration = 5000) {
            const icon = toast.querySelector('i');
            icon.className = type === 'error' ? 'fas fa-times-circle' :
                            type === 'warning' ? 'fas fa-exclamation-triangle' :
                            'fas fa-check-circle';

            toastMessage.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), duration);
        }

        function base64urlToUint8Array(base64url) {
            const padding = '='.repeat((4 - (base64url.length % 4)) % 4);
            const base64 = (base64url + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');
            const rawData = atob(base64);
            const buffer = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; i++) {
                buffer[i] = rawData.charCodeAt(i);
            }

            return buffer;
        }

        function uint8ArrayToBase64url(buffer) {
            const bytes = new Uint8Array(buffer);
            let binary = '';
            bytes.forEach(byte => binary += String.fromCharCode(byte));
            return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        }

        // Biometric Setup Flow with OTP
        if (setupBioBtn) {
            setupBioBtn.addEventListener('click', async function(e) {
                e.preventDefault();

                const username = document.getElementById('login').value;
                if (!username) {
                    showToast('Please enter your email or phone first', 'error');
                    return;
                }

                try {
                    // Show loading state
                    setupBioBtn.innerHTML = '<span class="spinner"></span> Sending OTP...';
                    setupBioBtn.disabled = true;

                    // Send OTP request with PWA fallback
                    let otpResponse;
                    try {
                        otpResponse = await fetch("/biometric/send-otp", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ username })
                        });
                    } catch (error) {
                        // Try with full URL if PWA has issues
                        otpResponse = await fetch(window.location.origin + "/biometric/send-otp", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ username })
                        });
                    }

                    const otpData = await otpResponse.json();

                    if (!otpData.success) {
                        throw new Error(otpData.message || 'Failed to send OTP');
                    }

                    // Create OTP modal with individual digit inputs
                    const otpModal = document.createElement('div');
                    otpModal.id = 'otpModal';
                    otpModal.style.cssText = `
                        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                        background: rgba(0,0,0,0.7); z-index: 1000;
                        display: flex; justify-content: center; align-items: center;
                    `;

                    otpModal.innerHTML = `
                        <div style="background: #1e293b; padding: 24px; border-radius: 12px; width: 90%; max-width: 320px;">
                            <h3 style="margin-top: 0; text-align: center; color: white;">Verify OTP</h3>
                            <p style="color: #94a3b8; text-align: center; margin-bottom: 16px;">OTP sent to phone ending with</p>
                            <p style="color: #94a3b8; text-align: center; margin-bottom: 16px;">****-***-${otpData.phone.slice(-3)}</p>
                            <div class="otp-container">
                                ${Array(5).fill().map((_, i) => `
                                    <input type="text" id="otp-digit-${i}" class="otp-digit"
                                        maxlength="1" inputmode="numeric" pattern="\\d*"
                                        style="width: 100%; height: 48px; text-align: center;
                                        font-size: 18px; border-radius: 8px; border: 1px solid #334155;
                                        background: rgba(255,255,255,0.05); color: white;">
                                `).join('')}
                            </div>
                            <button id="verifyOtpBtn" style="width: 100%; padding: 12px; background: #6366f1;
                                color: white; border: none; border-radius: 8px; cursor: pointer;">
                                <span id="verifyOtpBtnText">Verify OTP</span>
                            </button>
                            <p style="text-align: center; margin-top: 12px; font-size: 13px; color: #94a3b8;">
                                Didn't receive OTP? <a href="#" id="resendOtp" style="color: #6366f1;">Resend</a>
                            </p>
                            <button onclick="document.body.removeChild(otpModal)"
                                style="width: 100%; padding: 12px; background: #f87171; color: white;
                                border: none; border-radius: 8px; cursor: pointer; margin-top: 10px;">
                                Cancel
                            </button>
                        </div>
                    `;

                    document.body.appendChild(otpModal);

                    // Handle OTP digit input navigation
                    setTimeout(() => {
                        const otpDigits = document.querySelectorAll('.otp-digit');

                        otpDigits.forEach((digit, index) => {
                            // Auto-focus first digit
                            if (index === 0) digit.focus();

                            // Handle input
                            digit.addEventListener('input', (e) => {
                                if (e.target.value.length === 1) {
                                    if (index < otpDigits.length - 1) {
                                        otpDigits[index + 1].focus();
                                    }
                                }
                            });

                            // Handle backspace
                            digit.addEventListener('keydown', (e) => {
                                if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                                    otpDigits[index - 1].focus();
                                }
                            });
                        });
                    }, 100);

                    // Handle OTP verification
                    document.getElementById('verifyOtpBtn').addEventListener('click', async () => {
                        const otpDigits = document.querySelectorAll('.otp-digit');
                        const otp = Array.from(otpDigits).map(d => d.value).join('');
                        const verifyBtn = document.getElementById('verifyOtpBtn');
                        const verifyBtnText = document.getElementById('verifyOtpBtnText');

                        if (otp.length !== 5 || !/^\d+$/.test(otp)) {
                            showToast('Please enter a valid OTP', 'error');
                            return;
                        }

                        try {
                            // Show loading state
                            verifyBtn.disabled = true;
                            verifyBtnText.innerHTML = '<span class="spinner"></span> Verifying...';

                            let verifyResponse;
                            try {
                                verifyResponse = await fetch("/biometric/verify-otp", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                        // 'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        username: username,
                                        otp: otp,
                                        is_pwa: true // Ongeza flag kwa ajili ya PWA
                                    }),
                                    credentials: 'include'
                                });
                            } catch (error) {
                                // Fallback for PWA
                                verifyResponse = await fetch(window.location.origin + "/biometric/verify-otp", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ username, otp })
                                });
                            }

                            const verifyData = await verifyResponse.json();

                            if (!verifyData.success) {
                                throw new Error(verifyData.message || 'Invalid OTP');
                            }

                            // If we get here, verification succeeded
                            if (verifyData.requires_browser) {
                                showToast('Please complete setup in your browser', 'info');
                                window.open(window.location.origin + '/biometric/setup?token=' + verifyData.token, '_blank');
                            } else {
                                // Proceed with WebAuthn registration
                                await registerBiometricCredential(username);
                                document.body.removeChild(otpModal);
                            }
                        } catch (error) {
                            console.error('OTP verification error:', error);
                            showToast(error.message || 'Verification failed. Please try in browser if using PWA.', 'error');
                        } finally {
                            if (verifyBtn && verifyBtnText) {
                                verifyBtn.disabled = false;
                                verifyBtnText.textContent = 'Verify OTP';
                            }
                        }
                    });

                    // Handle OTP resend
                    document.getElementById('resendOtp').addEventListener('click', async (e) => {
                        e.preventDefault();
                        try {
                            await fetch("/biometric/send-otp", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({ username })
                            });
                            showToast('OTP resent successfully', 'success');
                        } catch (error) {
                            // Fallback for PWA
                            await fetch(window.location.origin + "/biometric/send-otp", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({ username })
                            });
                            showToast('OTP resent successfully', 'success');
                        }
                    });

                } catch (error) {
                    console.error('Biometric setup error:', error);
                    showToast(error.message, 'error');
                    if (document.getElementById('otpModal')) {
                        document.body.removeChild(document.getElementById('otpModal'));
                    }
                } finally {
                    if (setupBioBtn) {
                        setupBioBtn.innerHTML = 'Set Up Biometrics';
                        setupBioBtn.disabled = false;
                    }
                }
            });
        }

        // WebAuthn Registration
        async function registerBiometricCredential(username) {
            try {
                // 1. Get registration options
                const optionsRes = await fetch("/webauthn/register/options", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username })
                });

                const options = await optionsRes.json();

                // 2. Create credential
                const credential = await navigator.credentials.create({
                    publicKey: {
                        ...options,
                        challenge: base64urlToUint8Array(options.challenge),
                        user: {
                            ...options.user,
                            id: base64urlToUint8Array(options.user.id)
                        }
                    }
                });

                // 3. Verify with server
                const verificationRes = await fetch("/webauthn/register/verify", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: credential.id,
                        rawId: uint8ArrayToBase64url(credential.rawId),
                        response: {
                            attestationObject: uint8ArrayToBase64url(credential.response.attestationObject),
                            clientDataJSON: uint8ArrayToBase64url(credential.response.clientDataJSON)
                        },
                        username: username
                    })
                });

                const result = await verificationRes.json();
                if (result.success) {
                    showToast('Biometric registration successful!', 'success');

                    // Update settings
                    bioSettings.registered = true;
                    bioSettings.username = username;
                    localStorage.setItem('bioSettings', JSON.stringify(bioSettings));

                    // Update UI
                    if (bioBtnText) bioBtnText.textContent = 'Use Biometric';
                    if (setupBioBtn) setupBioBtn.style.display = 'none';
                } else {
                    throw new Error(result.message || 'Registration failed');
                }
            } catch (error) {
                console.error('Registration failed:', error);
                showToast('Registration failed: ' + error.message, 'error');
            }
        }

        // Biometric Login
        if (bioBtn) {
            bioBtn.addEventListener('click', async function() {
                const bioSettings = JSON.parse(localStorage.getItem('bioSettings') || '{}');
                const username = bioSettings.username;

                if (!username) {
                    showToast('Click Setup Biometric Authentication below to register your device', 'warning');
                    return;
                }

                const originalContent = bioBtn.innerHTML;
                bioBtn.innerHTML = '<span class="spinner"></span> Authenticating...';
                bioBtn.disabled = true;

                try {
                    // Get authentication options
                    const optionsRes = await fetch("/webauthn/login/options", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            username: username
                        })
                    });

                    if (!optionsRes.ok) throw new Error('Failed to get authentication options');

                    const options = await optionsRes.json();

                    // Convert values
                    const publicKey = {
                        ...options,
                        challenge: base64urlToUint8Array(options.challenge),
                        allowCredentials: options.allowCredentials?.map(cred => ({
                            ...cred,
                            id: base64urlToUint8Array(cred.id),
                            transports: ['internal']
                        })),
                        userVerification: 'required'
                    };

                    // Get assertion
                    const assertion = await navigator.credentials.get({ publicKey });

                    // Verify assertion with PWA fallback
                    let verifyRes;
                    try {
                        verifyRes = await fetch("/webauthn/login/verify", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                id: assertion.id,
                                rawId: uint8ArrayToBase64url(assertion.rawId),
                                type: assertion.type,
                                response: {
                                    authenticatorData: uint8ArrayToBase64url(assertion.response.authenticatorData),
                                    clientDataJSON: uint8ArrayToBase64url(assertion.response.clientDataJSON),
                                    signature: uint8ArrayToBase64url(assertion.response.signature),
                                    userHandle: assertion.response.userHandle ?
                                        uint8ArrayToBase64url(assertion.response.userHandle) : null
                                }
                            })
                        });
                    } catch (error) {
                        // Fallback for PWA
                        verifyRes = await fetch(window.location.origin + "/webauthn/login/verify", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                id: assertion.id,
                                rawId: uint8ArrayToBase64url(assertion.rawId),
                                type: assertion.type,
                                response: {
                                    authenticatorData: uint8ArrayToBase64url(assertion.response.authenticatorData),
                                    clientDataJSON: uint8ArrayToBase64url(assertion.response.clientDataJSON),
                                    signature: uint8ArrayToBase64url(assertion.response.signature),
                                    userHandle: assertion.response.userHandle ?
                                        uint8ArrayToBase64url(assertion.response.userHandle) : null
                                }
                            })
                        });
                    }

                    const verifyData = await verifyRes.json();

                    if (verifyData.success) {
                        showToast('Biometric login successful!', 'success');
                        setTimeout(() => {
                            window.location.href = verifyData.redirect || '/';
                        }, 500);
                    } else {
                        throw new Error(verifyData.message || 'Authentication failed');
                    }

                } catch (error) {
                    console.error('Biometric login error:', error);
                    showToast(error.message || 'Authentication failed', 'error');
                } finally {
                    bioBtn.innerHTML = originalContent;
                    bioBtn.disabled = false;
                }
            });
        }
        // Clear Biometric Functionality
        if (clearBioBtn) {
            clearBioBtn.addEventListener('click', async function(e) {
                e.preventDefault();

                if (!bioSettings.username) {
                    showToast('No biometric data found', 'error');
                    return;
                }

                if (!confirm('Are you sure you want to delete your biometric login data?')) {
                    return;
                }

                try {
                    clearBioBtn.innerHTML = '<span class="spinner"></span> Clearing...';

                    const deleteResponse = await fetch("/webauthn/delete-credentials", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ username: bioSettings.username })
                    });

                    const result = await deleteResponse.json();

                    if (result.success) {
                        // Update settings
                        bioSettings.registered = false;
                        bioSettings.username = null;
                        localStorage.setItem('bioSettings', JSON.stringify(bioSettings));

                        // Update UI
                        updateBiometricUI();
                        showToast('Biometric data deleted successfully', 'success');
                    } else {
                        throw new Error(result.message || 'Failed to delete biometric data');
                    }
                } catch (error) {
                    console.error('Clear biometric error:', error);
                    showToast(error.message || 'Failed to delete biometric data', 'error');
                } finally {
                    if (clearBioBtn) {
                        clearBioBtn.innerHTML = 'Clear Biometric';
                    }
                }
            });
        }

        // Show spinner on normal login
        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                loginBtn.innerHTML = '<span class="spinner"></span> Authenticating...';
                loginBtn.disabled = true;
            });
        }
    });

    // reload page on every 5 minutes
    function simpleLoginPageReload() {
        const currentUrl = window.location.href;

        // Hakikisha tuna-refresh tu kama tupo kwenye ukurasa wa login
        if (currentUrl.includes('/login')) {
            setInterval(() => {
                console.log('Auto-refreshing login page...');
                window.location.reload();
                if (typeof showToast === 'function') {
                    showToast('Login is required', 'info');
                }
            }, 5 * 60 * 1000); // 5 minutes
        }
    }

    document.addEventListener('DOMContentLoaded', simpleLoginPageReload);

  </script>

  @include('sweetalert::alert')
</body>
</html>
