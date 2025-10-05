<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ShuleApp | Reset Password</title>

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
      <h1 class="login-title">Reset Password</h1>
      <p class="login-subtitle">Update New Password</p>
    </div>

    @if(Session::has('error'))
      <div style="background: rgba(239, 68, 68, 0.1); padding: 10px; border-radius: 6px; border-left: 3px solid #ef4444; margin-bottom: 16px; font-size: 13px;">
        <i class="fas fa-exclamation-circle" style="margin-right: 6px;"></i>
        {{ Session::get('error') }}
      </div>
    @endif
    @if (Session::has('success'))
      <div style="background: rgba(16, 185, 129, 0.1); padding: 10px; border-radius: 6px; border-left: 3px solid #10b981; margin-bottom: 16px; font-size: 13px;">
        <i class="fas fa-check-circle" style="margin-right: 6px;"></i>
        {{ Session::get('success') }}
      </div>
    @endif

    <form id="loginForm" method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="form-group">
        <label for="login" class="form-label">Email</label>
        <input type="text" id="login" name="email" value="{{ $email ?? old('email') }}" class="form-control" placeholder="user@example.com" required />
        @error('email')
          <span class="text-danger">{{$message}}</span>
        @enderror
      </div>
      <div class="form-group" style="position: relative;">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" value="{{old('password')}}" class="form-control" placeholder="••••••••" required />
        <button type="button" class="password-toggle" id="togglePassword">
          <i class="fas fa-eye"></i>
        </button>
        @error('password')
          <span class="text-danger">{{$message}}</span>
        @enderror
      </div>
      <div class="form-group" style="position: relative;">
        <label for="password" class="form-label">Confirm Password</label>
        <input type="password" id="confirm_password" name="password_confirmation" value="{{old('password_confirmation')}}"  class="form-control" placeholder="••••••••" required />
        <button type="button" class="password-toggle" id="togglePassword2">
          <i class="fas fa-eye"></i>
        </button>
        @error('password_confirmation')
          <span class="text-danger">{{$message}}</span>
        @enderror
      </div>
      <button type="submit" class="btn btn-primary" id="loginBtn">
        <span id="btnText">Reset Password</span>
      </button>
    </form>
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
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const togglePassword2 = document.getElementById('togglePassword2');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const bioBtn = document.getElementById('bioBtn');
    const bioBtnText = document.getElementById('bioBtnText');
    const setupBioBtn = document.getElementById('setupBioBtn');
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');

    // Password show/hide toggle
    togglePassword.addEventListener('click', function () {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    // Toggle for confirm password
    togglePassword2.addEventListener('click', function () {
        const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
        confirmPasswordInput.type = type;
        this.innerHTML = type === 'password'
        ? '<i class="fas fa-eye"></i>'
        : '<i class="fas fa-eye-slash"></i>';
    });

    // Toast function
    function showToast(message, type = 'success', duration = 3000) {
      const icon = toast.querySelector('i');
      icon.className =
        type === 'error'
          ? 'fas fa-times-circle'
          : type === 'warning'
          ? 'fas fa-exclamation-triangle'
          : 'fas fa-check-circle';

      toastMessage.textContent = message;
      toast.className = 'toast show';
      setTimeout(() => {
        toast.className = 'toast';
      }, duration);
    }

    // Show spinner on normal login
    loginForm.addEventListener('submit', function () {
      loginBtn.innerHTML = '<span class="spinner"></span> Updating...';
      loginBtn.disabled = true;
    });
  });
</script>

@include('SRTDashboard.script')
@include('sweetalert::alert')
</body>
</html>
