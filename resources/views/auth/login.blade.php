<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ShuleApp | Login</title>

  <!-- Your existing styles and fontawesome -->
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
      width: 100%;
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
      bottom: 20px;
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
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-header">
      <h1 class="login-title">Welcome to ShuleApp</h1>
      <p class="login-subtitle">Sign in to continue</p>
    </div>

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

    <div class="divider">or continue with</div>

    <button id="bioBtn" class="bio-btn">
      <i class="fas fa-fingerprint" style="font-size: 1.5rem"></i>
      <span id="bioBtnText">Use Biometric</span>
    </button>

    <a href="#" id="setupBioBtn" class="setup-bio">Set up biometric authentication</a>
  </div>

  <div class="footer">
    &copy; {{ date('Y') }} ShuleApp. All rights reserved.
  </div>

  <div id="toast" class="toast">
    <i class="fas fa-check-circle"></i>
    <span id="toastMessage">Message here</span>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const setupBioBtn = document.getElementById('setupBioBtn');
        const bioBtn = document.getElementById('bioBtn');
        const bioBtnText = document.getElementById('bioBtnText');
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');

        // Check WebAuthn support
        if (!window.PublicKeyCredential) {
            bioBtn.disabled = true;
            bioBtn.style.opacity = '0.7';
            bioBtn.style.cursor = 'not-allowed';
            bioBtnText.textContent = 'Biometrics Not Supported';
            if (setupBioBtn) setupBioBtn.style.display = 'none';
        } else if (!localStorage.getItem('bio_login')) {
            bioBtnText.textContent = 'Set Up Biometrics';
        }

        // Helper functions
        function showToast(message, type = 'success', duration = 3000) {
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

                const username = prompt('Enter your username (email or phone) to register biometrics:');
                if (!username) return;

                try {
                    // Show loading state
                    setupBioBtn.innerHTML = '<span class="spinner"></span> Sending OTP...';
                    setupBioBtn.disabled = true;

                    // Send OTP request
                    const otpResponse = await fetch("/biometric/send-otp", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ username })
                    });

                    const otpData = await otpResponse.json();

                    if (!otpData.success) {
                        throw new Error(otpData.message || 'Failed to send OTP');
                    }

                    // Create OTP modal
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
                            <p style="color: #94a3b8; text-align: center; margin-bottom: 16px;">
                                OTP sent to phone ending with ${otpData.phone.slice(-3)}
                            </p>
                            <input type="text" id="otpInput" placeholder="Enter OTP"
                                style="width: 80%; padding: 10px; margin-bottom: 14px;
                                text-align: center; font-size: 18px; letter-spacing: 4px;"
                                maxlength="5" inputmode="numeric" pattern="\\d*">
                            <button id="verifyOtpBtn" style="width: 100%; padding: 12px; background: #6366f1;
                                color: white; border: none; border-radius: 8px; cursor: pointer;">
                                Verify OTP
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

                    // Handle OTP verification
                    document.getElementById('verifyOtpBtn').addEventListener('click', async () => {
                        const otp = document.getElementById('otpInput').value.trim();
                        if (otp.length !== 5 || !/^\d+$/.test(otp)) {
                            showToast('Please enter a valid OTP', 'error');
                            return;
                        }

                        const verifyResponse = await fetch("/biometric/verify-otp", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ username, otp })
                        });

                        const verifyData = await verifyResponse.json();

                        if (!verifyData.success) {
                            throw new Error(verifyData.message || 'Invalid OTP');
                        }

                        // Proceed with WebAuthn registration
                        await registerBiometricCredential(username);
                        document.body.removeChild(otpModal);
                    });

                    // Handle OTP resend
                    document.getElementById('resendOtp').addEventListener('click', async (e) => {
                        e.preventDefault();
                        await fetch("/biometric/send-otp", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ username })
                        });
                        showToast('OTP resent successfully', 'success');
                    });

                } catch (error) {
                    console.error('Biometric setup error:', error);
                    showToast(error.message, 'error');
                    if (document.getElementById('otpModal')) {
                        document.body.removeChild(document.getElementById('otpModal'));
                    }
                } finally {
                    setupBioBtn.innerHTML = 'Set Up Biometrics';
                    setupBioBtn.disabled = false;
                }
            });
        }

        // WebAuthn Registration
        async function registerBiometricCredential(username) {
            try {
                // 1. Pata options
                const optionsRes = await fetch("/webauthn/register/options", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username })
                });

                const options = await optionsRes.json();

                // 2. Tengeneza credential
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

                // 3. Verify na server
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
                        username: username // Username/phone ya user
                    })
                });

                const result = await verificationRes.json();
                if (result.success) {
                    showToast('Biometric registration successful!', 'success');
                }
            } catch (error) {
                console.error('Registration failed:', error);
                alert('Failed: ' + error.message);
            }
        }

        // Biometric Login
        if (bioBtn) {
            bioBtn.addEventListener('click', async function() {
                const username = localStorage.getItem('bio_login');
                if (!username) {
                    showToast('Please set up biometrics first', 'warning');
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
                            username: localStorage.getItem('bio_login')
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

                    // Verify assertion
                    const verifyRes = await fetch("/webauthn/login/verify", {
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

                    const verifyData = await verifyRes.json();

                    if (verifyData.success) {
                        showToast('Biometric login successful!', 'success');
                        setTimeout(() => {
                            window.location.href = verifyData.redirect || '/dashboard';
                        }, 1000);
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

        // Show spinner on normal login
        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                loginBtn.innerHTML = '<span class="spinner"></span> Signing In...';
                loginBtn.disabled = true;
            });
        }
    });
</script>


@include('SRTDashboard.script')
@include('sweetalert::alert')
</body>
</html>
