<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="author" content="Piano">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShuleApp - Smart School Management System</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-16x16.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-32x32.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-192x192.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-512x512.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        /* Dark/Light Mode Variables */
        :root {
            --bg-primary: #f3f4f6;
            --bg-secondary: #ffffff;
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --card-bg: #ffffff;
            --border-color: #e5e7eb;
            --header-bg: rgba(255, 255, 255, 0.9);
            --input-bg: #ffffff;
        }

        body.dark-mode {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --card-bg: #1e293b;
            --border-color: #334155;
            --header-bg: rgba(15, 23, 42, 0.9);
            --input-bg: #1e293b;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
            scroll-behavior: smooth;
        }

        /* Header styles */
        header {
            background: var(--header-bg) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
        }

        header .text-blue-700,
        header .text-gray-700 {
            color: var(--text-primary) !important;
        }

        /* Card styles */
        .feature-card,
        .counter-box,
        .bg-white,
        .login-panel .bg-white {
            background: var(--card-bg) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-color) !important;
        }

        .text-gray-600,
        .text-gray-700,
        .text-gray-800 {
            color: var(--text-secondary) !important;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .counter-box {
            box-shadow: 10px 10px 30px rgba(0,0,0,0.1), -10px -10px 30px rgba(255,255,255,0.05);
        }

        body.dark-mode .counter-box {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            box-shadow: 10px 10px 30px rgba(0,0,0,0.3), -10px -10px 30px rgba(255,255,255,0.05);
        }

        .typing-container {
            display: inline-block;
            position: relative;
        }

        .typing-text {
            border-right: 3px solid #fbbf24;
            white-space: nowrap;
            overflow: hidden;
            animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
        }

        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }

        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: #fbbf24 }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Login Modal/Slide-in Panel Styles */
        .login-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .login-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .login-panel {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            max-width: 450px;
            height: 100%;
            background: var(--bg-secondary);
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            overflow-y: auto;
        }

        .login-panel.active {
            right: 0;
        }

        .login-panel .form-control {
            width: 100%;
            padding: 12px 16px;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 14px;
        }

        .login-panel .form-control:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .login-panel .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            width: 100%;
            cursor: pointer;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .bio-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .bio-btn:hover:not(:disabled) {
            background: rgba(16, 185, 129, 0.2);
            transform: translateY(-2px);
        }

        .bio-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            z-index: 10;
        }

        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            padding: 12px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1100;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            border: 1px solid var(--border-color);
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* OTP Modal Styles */
        .otp-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(8px);
            z-index: 1100;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .otp-container {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin: 20px 0;
        }

        .otp-digit {
            width: 55px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: var(--input-bg);
            color: var(--text-primary);
        }

        /* Switch toggle */
        .switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 24px;
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
            background-color: #cbd5e1;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #6366f1;
        }

        input:checked + .slider:before {
            transform: translateX(24px);
        }

        body.dark-mode .slider {
            background-color: #334155;
        }

        .close-panel {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--bg-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .close-panel:hover {
            transform: rotate(90deg);
        }

        /* Theme toggle button */
        .theme-toggle {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
        }

        /* Form switch links */
        .form-switch-link {
            color: #6366f1;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
        }

        .form-switch-link:hover {
            text-decoration: underline;
        }

        .error-message {
            background: rgba(245, 86, 86, 0.1);
            padding: 10px;
            border-radius: 8px;
            border-left: 3px solid #ef4444;
            margin-bottom: 16px;
            font-size: 13px;
            color: #e16969;
        }

        .success-message {
            background: rgba(16, 185, 129, 0.1);
            padding: 10px;
            border-radius: 8px;
            border-left: 3px solid #10b981;
            margin-bottom: 16px;
            font-size: 13px;
            color: #6ee7b7;
        }

        @media (max-width: 640px) {
            .login-panel {
                max-width: 100%;
            }

            .otp-digit {
                width: 45px;
                height: 50px;
                font-size: 20px;
            }
        }

        .logo-img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

    <!-- Login Overlay & Panel -->
    <div id="loginOverlay" class="login-overlay"></div>
    <div id="loginPanel" class="login-panel">
        <div class="close-panel" id="closeLoginPanel">
            <i class="fas fa-times"></i>
        </div>

        <div class="p-6 md:p-8">
            <!-- Login Form (Default View) -->
            <div id="loginFormContainer">
                <div class="text-center mb-6">
                    <div class="flex items-center justify-center mx-auto mb-4">
                        <img src="{{ asset('storage/logo/new_logo.png') }}" alt="Logo" class="logo-img">
                    </div>
                    <h2 class="text-2xl font-bold mb-2" style="color: var(--text-primary);">Welcome Back!</h2>
                    <p class="text-sm" style="color: var(--text-secondary);">Sign in to access your dashboard</p>
                </div>

                <!-- Display Laravel validation errors -->
                @if ($errors->any())
                    <div class="error-message mb-4">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                @if (session('error'))
                    <div class="error-message mb-4">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="success-message mb-4">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('status') }}
                    </div>
                @endif

                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="login-username" class="form-label">Email or Phone Number</label>
                        <input type="text" id="login-username" name="username" class="form-control"
                               placeholder="user@example.com or 075XXXXXXX" value="{{ old('username') }}" required>
                    </div>

                    <div class="mb-4" style="position: relative;">
                        <label for="login-password" class="form-label">Password</label>
                        <input type="password" id="login-password" name="password" class="form-control" placeholder="********" required>
                        <button type="button" class="password-toggle" id="toggleLoginPassword">
                            <i class="fas fa-eye mt-8"></i>
                        </button>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <label class="flex items-center gap-2 text-sm" style="color: var(--text-secondary);">
                            <input type="checkbox" name="remember" id="remember" class="rounded" {{ old('remember') ? 'checked' : '' }}>
                            <span>Remember me</span>
                        </label>
                        <a href="#" id="showForgotPassword" class="text-sm text-indigo-400 hover:text-indigo-300 form-switch-link">Forgot password?</a>
                    </div>

                    <button type="submit" id="loginSubmitBtn" class="btn-primary">
                        <span id="loginBtnText">Sign In</span>
                    </button>
                </form>

                <div class="relative my-6 text-center">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t" style="border-color: var(--border-color);"></div>
                    </div>
                    <div class="relative px-4 text-sm" style="background: var(--bg-secondary); color: var(--text-secondary); display: inline-block;">
                        or continue with
                    </div>
                </div>

                <!-- Biometric Section -->
                <div class="mb-4 flex items-center justify-between">
                    <label class="text-sm" style="color: var(--text-secondary);">
                        <i class="fas fa-fingerprint mr-2"></i>Enable Biometric Login
                    </label>
                    <label class="switch">
                        <input type="checkbox" id="biometricToggle">
                        <span class="slider round"></span>
                    </label>
                </div>

                <div id="biometricSection" style="display: none;">
                    <button id="bioBtn" class="bio-btn w-full mb-3">
                        <i class="fas fa-fingerprint text-xl"></i>
                        <span id="bioBtnText">Set Up Biometrics</span>
                    </button>
                    <div id="biometricActions" style="display: none; justify-content: center;">
                        <a href="#" id="clearBioBtn" class="text-red-400 hover:text-red-300 text-sm">
                            <i class="fas fa-trash-alt mr-1"></i> Clear Biometric Data
                        </a>
                    </div>
                </div>
            </div>

            <!-- Forgot Password Form (Hidden by default) -->
            <div id="forgotPasswordContainer" style="display: none;">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-key text-white text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2" style="color: var(--text-primary);">Reset Password</h2>
                    <p class="text-sm" style="color: var(--text-secondary);">Enter your email to receive reset link</p>
                </div>

                <div id="resetStatusMessage"></div>

                <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="reset-email" class="form-label">Email Address</label>
                        <input type="email" id="reset-email" name="email" class="form-control"
                               placeholder="user@example.com" value="{{ old('email') }}" required>
                    </div>

                    <button type="submit" id="resetSubmitBtn" class="btn-primary">
                        <span id="resetBtnText">Send Password Reset Link</span>
                    </button>
                </form>

                <div class="text-center mt-6">
                    <a href="#" id="showLoginForm" class="form-switch-link text-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Sign In
                    </a>
                </div>
            </div>

            <div class="mt-6 pt-4 text-center">
                <p class="text-xs" style="color: var(--text-secondary);">© 2025 ShuleApp. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Language & Theme Selectors -->
    <div class="fixed top-4 right-4 z-50 flex gap-2">
        <div class="theme-toggle" id="themeToggleBtn" title="Toggle Dark/Light Mode">
            <i id="themeIcon" class="fas fa-moon text-blue-600"></i>
        </div>
        <div class="flex items-center space-x-2 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-full px-4 py-2 shadow-lg">
            <i class="fas fa-globe text-blue-600 dark:text-blue-400"></i>
            <select id="language-selector" class="bg-transparent border-0 focus:ring-0 text-sm dark:text-white">
                <option value="en" selected>English</option>
                <option value="sw">Kiswahili</option>
            </select>
        </div>
    </div>

    <!-- Header -->
    <header class="fixed top-0 w-full shadow-md z-40">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('storage/logo/new_logo.png') }}" alt="ShuleApp Logo" class="w-full h-full object-contain" onerror="this.src='https://placehold.co/60x60/3b82f6/white?text=S'">
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-bold text-blue-700 dark:text-blue-400">ShuleApp
                            <p class="text-xs" style="color: var(--text-secondary);"><i>Empowering Education</i></p>
                        </div>
                    </div>
                </div>
                <nav class="hidden md:flex space-x-6 lg:space-x-8">
                    <a href="#home" class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition" style="color: var(--text-primary);"><i class="fas fa-home"></i><span id="nav-home">Home</span></a>
                    <a href="#features" class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition" style="color: var(--text-primary);"><i class="fas fa-star"></i><span id="nav-features">Features</span></a>
                    <a href="#stats" class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition" style="color: var(--text-primary);"><i class="fas fa-chart-line"></i><span id="nav-stats">Stats</span></a>
                    <a href="#contact" class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition" style="color: var(--text-primary);"><i class="fas fa-phone"></i><span id="nav-contact">Contact</span></a>
                </nav>
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="hidden md:block text-xs sm:text-sm px-2 sm:px-3 py-1 rounded-full" style="background: var(--bg-primary); color: var(--text-primary); border: 1px solid var(--border-color);">
                        <i class="fas fa-clock mr-1"></i><span id="live-time">00:00:00</span>
                    </div>
                    <button id="menu-toggle" class="md:hidden focus:outline-none" style="color: var(--text-primary);"><i class="fas fa-bars text-2xl"></i></button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden p-4 sm:p-6 space-y-3 shadow-lg" style="background: var(--bg-secondary); border-top: 1px solid var(--border-color);">
            <a href="#home" class="block font-medium py-2 border-b" style="color: var(--text-primary); border-color: var(--border-color);"><i class="fas fa-home w-5"></i><span id="mobile-nav-home">Home</span></a>
            <a href="#features" class="block font-medium py-2 border-b" style="color: var(--text-primary); border-color: var(--border-color);"><i class="fas fa-star w-5"></i><span id="mobile-nav-features">Features</span></a>
            <a href="#stats" class="block font-medium py-2 border-b" style="color: var(--text-primary); border-color: var(--border-color);"><i class="fas fa-chart-line w-5"></i><span id="mobile-nav-stats">Stats</span></a>
            <a href="#contact" class="block font-medium py-2 border-b" style="color: var(--text-primary); border-color: var(--border-color);"><i class="fas fa-phone w-5"></i><span id="mobile-nav-contact">Contact</span></a>
            <button id="mobileShowLoginBtn" class="w-full gradient-bg text-white text-center py-3 rounded-lg font-semibold mt-2">
                <i class="fas fa-sign-in-alt mr-2"></i><span id="mobile-nav-login">Login Now</span>
            </button>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="min-h-screen relative overflow-hidden pt-16 sm:pt-20">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('assets/images/bg/bg-2.jpeg') }}" alt="School Background" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1600'">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/70 to-purple-900/70"></div>
        </div>
        <div class="w-full px-4 sm:px-6 lg:px-8 h-full flex flex-col lg:flex-row items-center justify-center relative z-10 pt-20 lg:pt-32">
            <div class="lg:w-1/2 text-white mb-8 lg:mb-0">
                <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-4 sm:mb-6 leading-tight">
                    <span id="hero-title-1">Transform </span>
                    <div class="typing-container"><span class="text-yellow-300 typing-text" id="hero-title-2">Your School Management</span></div>
                </h1>
                <p class="text-lg sm:text-xl mb-6 sm:mb-8 text-blue-100" id="hero-description">Ultimate solution to run your school efficiently and with top-level security.</p>

                <div class="mb-6 sm:mb-8 p-3 sm:p-4 bg-white/10 backdrop-blur-sm rounded-xl inline-block max-w-full overflow-hidden">
                    <div class="flex items-center justify-center space-x-3 sm:space-x-4">
                        <div class="text-center"><div class="text-2xl sm:text-3xl font-bold text-green-300" id="current-hours">00</div><div class="text-xs sm:text-sm text-blue-100" id="time-label-hours">Hours</div></div>
                        <div class="text-xl sm:text-2xl text-white">:</div>
                        <div class="text-center"><div class="text-2xl sm:text-3xl font-bold text-green-300" id="current-minutes">00</div><div class="text-xs sm:text-sm text-blue-100" id="time-label-minutes">Minutes</div></div>
                        <div class="text-xl sm:text-2xl text-white">:</div>
                        <div class="text-center"><div class="text-2xl sm:text-3xl font-bold text-green-300" id="current-seconds">00</div><div class="text-xs sm:text-sm text-blue-100" id="time-label-seconds">Seconds</div></div>
                    </div>
                    <div class="text-center mt-1 sm:mt-2 text-blue-100 text-xs sm:text-sm"><i class="fas fa-clock mr-1 sm:mr-2"></i><span id="current-time-label">Current Time (EAT)</span></div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-6 sm:mb-8">
                    <button id="heroShowLoginBtn" class="gradient-bg text-white px-6 sm:px-8 py-3 sm:py-4 rounded-full text-base sm:text-lg font-semibold hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-center">
                        <i class="fas fa-sign-in-alt mr-2"></i><span id="hero-button-1">Login now</span>
                    </button>
                    <a href="#features" class="bg-white/20 backdrop-blur-sm text-white px-6 sm:px-8 py-3 sm:py-4 rounded-full text-base sm:text-lg font-semibold hover:bg-white/30 transition-all duration-300 text-center"><i class="fas fa-play-circle mr-2"></i><span id="hero-button-2">View Features</span></a>
                </div>
                <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-6 mt-6 sm:mt-8">
                    <div class="flex items-center"><i class="fas fa-check-circle text-green-300 text-lg sm:text-xl mr-2"></i><span class="text-base sm:text-lg text-yellow-300" id="hero-benefit-1">No Setup Costs</span></div>
                    <div class="flex items-center"><i class="fas fa-check-circle text-green-300 text-lg sm:text-xl mr-2"></i><span class="text-base sm:text-lg text-yellow-300" id="hero-benefit-2">Free Training</span></div>
                </div>
            </div>
            <div class="lg:w-1/2 flex justify-center mt-8 lg:mt-0">
                <div class="relative w-full max-w-md sm:max-w-lg px-2">
                    <div class="absolute -top-4 -left-4 w-16 h-16 sm:-top-6 sm:-left-6 sm:w-24 sm:h-24 bg-yellow-400 rounded-full opacity-20"></div>
                    <div class="absolute -bottom-4 -right-4 w-20 h-20 sm:-bottom-6 sm:-right-6 sm:w-32 sm:h-32 bg-purple-500 rounded-full opacity-20"></div>
                    <div class="relative bg-white/10 backdrop-blur-lg rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-2xl border border-white/20">
                        <div class="text-center mb-4 sm:mb-6">
                            <div class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-2" id="stats-title-schools">Schools Using ShuleApp</div>
                            <div class="text-3xl sm:text-4xl font-bold text-yellow-300" id="live-counter">3</div>
                            <div class="text-white mt-1 sm:mt-2 text-sm sm:text-base" id="stats-subtitle-schools">Currently using and growing daily</div>
                        </div>
                        <div class="space-y-3 sm:space-y-4">
                            <div class="flex items-center justify-between bg-white/10 p-3 sm:p-4 rounded-xl"><div class="flex items-center"><div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3"><i class="fas fa-users text-white"></i></div><div><div class="font-bold text-sm sm:text-base text-white" id="stats-label-students">Students</div><div class="text-xs sm:text-sm text-blue-100" id="stats-sub-students">Registered</div></div></div><div class="text-xl sm:text-2xl font-bold text-white">1,500+</div></div>
                            <div class="flex items-center justify-between bg-white/10 p-3 sm:p-4 rounded-xl"><div class="flex items-center"><div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3"><i class="fas fa-chalkboard-teacher text-white"></i></div><div><div class="font-bold text-sm sm:text-base text-white" id="stats-label-teachers">Teachers</div><div class="text-xs sm:text-sm text-blue-100" id="stats-sub-teachers">Using System</div></div></div><div class="text-xl sm:text-2xl font-bold text-white">100+</div></div>
                            <div class="flex items-center justify-between bg-white/10 p-3 sm:p-4 rounded-xl"><div class="flex items-center"><div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3"><i class="fas fa-user-friends text-white"></i></div><div><div class="font-bold text-sm sm:text-base text-white" id="stats-label-parents">Parents</div><div class="text-xs sm:text-sm text-blue-100" id="stats-sub-parents">Connected</div></div></div><div class="text-xl sm:text-2xl font-bold text-white">1,500+</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 sm:py-20 relative">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 sm:mb-16">
                <div class="inline-block gradient-bg text-white px-4 sm:px-6 py-2 rounded-full font-semibold mb-3 sm:mb-4"><i class="fas fa-crown mr-2"></i><span id="features-tagline">Complete System Modules</span></div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-4 sm:mb-6" style="color: var(--text-primary);" id="features-title">All ShuleApp Modules</h2>
                <p class="text-lg sm:text-xl" style="color: var(--text-secondary);" id="features-description">9 core modules with 20+ sub-modules to run your school perfectly</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-12 sm:mb-16">
                <!-- Feature 1 -->
                <div class="feature-card p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border transition-all duration-500" style="background: var(--card-bg); border-color: var(--border-color);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 gradient-bg rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-users-cog text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="feature-1-title">Users Management</h3>
                    <p class="mb-4 sm:mb-6 text-sm sm:text-base" style="color: var(--text-secondary);" id="feature-1-desc">Complete role-based access for all school stakeholders.</p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-1-point-1">Teachers</span></li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-1-point-2">Students</span></li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-1-point-3">Parents</span></li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-1-point-4">Accountant</span></li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-1-point-5">Non-teaching Staff</span></li>
                    </ul>
                </div>
                <!-- Feature 2 -->
                <div class="feature-card p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border transition-all duration-500" style="background: var(--card-bg); border-color: var(--border-color);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-graduation-cap text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="feature-2-title">Academic Management</h3>
                    <p class="mb-4 sm:mb-6 text-sm sm:text-base" style="color: var(--text-secondary);" id="feature-2-desc">Full control over classes, courses, assessments and results.</p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-2-point-1">Class Management</span></li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-2-point-2">Course Management</span></li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-2-point-3">Assessment Management</span></li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-2-point-4">Results Management</span></li>
                    </ul>
                </div>
                <!-- Feature 3 -->
                <div class="feature-card p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border transition-all duration-500" style="background: var(--card-bg); border-color: var(--border-color);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-chart-line text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="feature-3-title">Reports & Analytics</h3>
                    <p class="mb-4 sm:mb-6 text-sm sm:text-base" style="color: var(--text-secondary);" id="feature-3-desc">Comprehensive reports for data-driven decisions.</p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-3-point-1">Attendance Report</span></li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-3-point-2">Daily School Report</span></li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-3-point-3">Holiday Package</span></li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span style="color: var(--text-primary);" id="feature-3-point-4">Graduates</span></li>
                    </ul>
                </div>
                <!-- Feature 4 -->
                <div class="feature-card p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border transition-all duration-500" style="background: var(--card-bg); border-color: var(--border-color);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-file-contract text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="feature-4-title">Contract System</h3>
                    <p class="mb-4 sm:mb-6 text-sm sm:text-base" style="color: var(--text-secondary);" id="feature-4-desc">Digital employee contract management with expiry alerts.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-4-point-1">Employee Contracts</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-4-point-2">Contract Renewals</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-4-point-3">Expiry Notifications</span></li></ul>
                </div>
                <!-- Feature 5 -->
                <div class="feature-card p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border transition-all duration-500" style="background: var(--card-bg); border-color: var(--border-color);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-red-500 to-rose-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-clipboard-list text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="feature-5-title">Duty Roster System</h3>
                    <p class="mb-4 sm:mb-6 text-sm sm:text-base" style="color: var(--text-secondary);" id="feature-5-desc">Manage teacher and staff duty schedules efficiently.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-5-point-1">Shift Scheduling</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-5-point-2">Daily Assignments</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-5-point-3">Supervision Planning</span></li></ul>
                </div>
                <!-- Feature 6 -->
                <div class="feature-card p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border transition-all duration-500" style="background: var(--card-bg); border-color: var(--border-color);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-sms text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="feature-6-title">Bulk SMS Messaging</h3>
                    <p class="mb-4 sm:mb-6 text-sm sm:text-base" style="color: var(--text-secondary);" id="feature-6-desc">Send instant announcements to parents, teachers and staff.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-6-point-1">Parent Announcements</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-6-point-2">Emergency Alerts</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-6-point-3">Fee Reminders</span></li></ul>
                </div>
                <!-- Feature 7 -->
                <div class="feature-card p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border transition-all duration-500" style="background: var(--card-bg); border-color: var(--border-color);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-money-bill-wave text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="feature-7-title">Financial System</h3>
                    <p class="mb-4 sm:mb-6 text-sm sm:text-base" style="color: var(--text-secondary);" id="feature-7-desc">Complete financial tracking for your school.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-7-point-1">Expenditure Management</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-7-point-2">Bills Payment (School Fees)</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-7-point-3">Payroll Management</span></li></ul>
                </div>
                <!-- Feature 8 -->
                <div class="feature-card p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border transition-all duration-500" style="background: var(--card-bg); border-color: var(--border-color);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-qrcode text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="feature-8-title">Gate Pass Verification</h3>
                    <p class="mb-4 sm:mb-6 text-sm sm:text-base" style="color: var(--text-secondary);" id="feature-8-desc">Secure Paid students entry management.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-8-point-1">Token Verification</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-8-point-2">Real-time Logs</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-8-point-3">Security Reports</span></li></ul>
                </div>
                <!-- Feature 9 -->
                <div class="feature-card p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border transition-all duration-500" style="background: var(--card-bg); border-color: var(--border-color);">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-passport text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="feature-9-title">e-Permit System</h3>
                    <p class="mb-4 sm:mb-6 text-sm sm:text-base" style="color: var(--text-secondary);" id="feature-9-desc">Digital permits for students leaving school premises.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-9-point-1">Real-time Approval</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-9-point-2">Real-time Tracking</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-9-point-3">Permit History</span></li></ul>
                </div>
            </div>

            <div class="gradient-bg rounded-2xl sm:rounded-3xl p-8 sm:p-10 text-center text-white shadow-2xl mt-8 sm:mt-12">
                <h3 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6" id="cta-title">Ready to Transform Your School?</h3>
                <p class="text-lg sm:text-xl mb-6 sm:mb-8 max-w-2xl mx-auto" id="cta-description">Be among the first schools to use ShuleApp and simplify your daily management</p>
                <button id="ctaShowLoginBtn" class="inline-block bg-white text-blue-700 px-8 sm:px-10 py-3 sm:py-4 rounded-full text-lg sm:text-xl font-bold hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-play mr-3"></i><span id="cta-button">Start Free Trial</span>
                </button>
                <p class="mt-4 sm:mt-6 text-blue-100 text-sm sm:text-base"><i class="fas fa-clock mr-2"></i><span id="cta-note">Registration takes few minutes</span></p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-16 sm:py-20 text-white" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-12 sm:mb-16" id="stats-title-growth">ShuleApp Growth Statistics</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 mb-12 sm:mb-16">
                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl"><div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-blue-600 dark:text-blue-400 counter-number" id="stat1">0</div><div class="text-lg sm:text-xl font-semibold mb-2" style="color: var(--text-primary);" id="stats-label-1">School</div><p style="color: var(--text-secondary);" id="stats-desc-1">Currently using our system</p><div class="mt-3"><i class="fas fa-school text-3xl text-blue-500"></i></div></div>
                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl"><div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-green-600 dark:text-green-400">1,500+</div><div class="text-lg sm:text-xl font-semibold mb-2" style="color: var(--text-primary);" id="stats-label-2">Students</div><p style="color: var(--text-secondary);" id="stats-desc-2">Registered in system</p><div class="mt-3"><i class="fas fa-user-graduate text-3xl text-green-500"></i></div></div>
                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl"><div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-purple-600 dark:text-purple-400">100+</div><div class="text-lg sm:text-xl font-semibold mb-2" style="color: var(--text-primary);" id="stats-label-3">Teachers</div><p style="color: var(--text-secondary);" id="stats-desc-3">Using the platform</p><div class="mt-3"><i class="fas fa-chalkboard-teacher text-3xl text-purple-500"></i></div></div>
                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl"><div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-red-600 dark:text-red-400">1,500+</div><div class="text-lg sm:text-xl font-semibold mb-2" style="color: var(--text-primary);" id="stats-label-4">Parents</div><p style="color: var(--text-secondary);" id="stats-desc-4">Connected to portal</p><div class="mt-3"><i class="fas fa-user-friends text-3xl text-red-500"></i></div></div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl sm:rounded-2xl p-6 sm:p-8 max-w-4xl mx-auto"><h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6" id="why-title">Why Choose ShuleApp?</h3><div class="grid md:grid-cols-3 gap-4 sm:gap-6"><div class="p-4 bg-white/5 rounded-xl"><i class="fas fa-bolt text-3xl text-yellow-400 mb-3"></i><h4 class="font-bold text-lg mb-2" id="why-1-title">Fast & Easy</h4><p class="text-blue-100 text-sm" id="why-1-desc">Start using within few hours</p></div><div class="p-4 bg-white/5 rounded-xl"><i class="fas fa-shield-alt text-3xl text-green-400 mb-3"></i><h4 class="font-bold text-lg mb-2" id="why-2-title">100% Secure</h4><p class="text-blue-100 text-sm" id="why-2-desc">Your data stored with highest standards</p></div><div class="p-4 bg-white/5 rounded-xl"><i class="fas fa-headset text-3xl text-purple-400 mb-3"></i><h4 class="font-bold text-lg mb-2" id="why-3-title">24/7 Support</h4><p class="text-blue-100 text-sm" id="why-3-desc">Our team ready to help anytime</p></div></div></div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 sm:py-20">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12 sm:mb-16"><div class="inline-block gradient-bg text-white px-4 sm:px-6 py-2 rounded-full font-semibold mb-3 sm:mb-4"><i class="fas fa-comments mr-2"></i><span id="contact-tagline">Contact Us</span></div><h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4 sm:mb-6" style="color: var(--text-primary);" id="contact-title">Get In Touch</h2><p class="text-lg sm:text-xl" style="color: var(--text-secondary);" id="contact-description">We're happy to listen and answer any questions you have</p></div>
                <div class="grid lg:grid-cols-2 gap-8 sm:gap-12">
                    <div class="rounded-xl sm:rounded-3xl shadow-xl sm:shadow-2xl p-6 sm:p-8 md:p-10" style="background: var(--card-bg);"><h3 class="text-xl sm:text-2xl font-bold mb-6 sm:mb-8" style="color: var(--text-primary);" id="form-title">Send Your Message</h3><form class="space-y-4 sm:space-y-6"><div class="grid md:grid-cols-2 gap-4 sm:gap-6"><div><label class="block mb-2 font-medium" style="color: var(--text-secondary);" id="form-label-name">Full Name</label><input type="text" placeholder="Your name" class="w-full border p-3 sm:p-4 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"></div><div><label class="block mb-2 font-medium" style="color: var(--text-secondary);" id="form-label-phone">Phone Number</label><input type="text" placeholder="Phone number" class="w-full border p-3 sm:p-4 rounded-xl focus:ring-2 focus:ring-blue-500 transition" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"></div></div><div><label class="block mb-2 font-medium" style="color: var(--text-secondary);" id="form-label-message">Your Message</label><textarea placeholder="Write your message here..." rows="4" class="w-full border p-3 sm:p-4 rounded-xl focus:ring-2 focus:ring-blue-500 transition" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"></textarea></div><button class="gradient-bg w-full text-white py-3 sm:py-4 rounded-xl text-base sm:text-lg font-semibold hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"><i class="fas fa-paper-plane mr-2 sm:mr-3"></i><span id="form-button">Send Message</span></button></form></div>
                    <div class="space-y-6 sm:space-y-8"><div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl sm:rounded-3xl p-6 sm:p-8 md:p-10 text-white shadow-xl sm:shadow-2xl"><h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6" id="contact-info-title">Quick Contacts</h3><div class="space-y-4 sm:space-y-6"><div class="flex items-start"><div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center mr-3 sm:mr-4"><i class="fas fa-phone text-lg sm:text-xl"></i></div><div><h4 class="font-bold text-base sm:text-lg" id="contact-phone-label">Support Phone</h4><a href="tel:+255678669000" class="text-xl sm:text-2xl font-bold hover:text-yellow-300 transition block mt-1">+255 678 669 000</a><p class="text-blue-100 mt-1 text-sm sm:text-base" id="contact-phone-note">Monday - Sunday, 24 Hours</p></div></div><div class="flex items-start"><div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center mr-3 sm:mr-4"><i class="fas fa-envelope text-lg sm:text-xl"></i></div><div><h4 class="font-bold text-base sm:text-lg" id="contact-email-label">Email Address</h4><a href="mailto:pianop477@gmail.com" class="text-lg sm:text-xl hover:text-yellow-300 transition block mt-1">pianop477@gmail.com</a><p class="text-blue-100 mt-1 text-sm sm:text-base" id="contact-email-note">We reply within 24 hours</p></div></div><div class="flex items-start"><div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center mr-3 sm:mr-4"><i class="fas fa-map-marker-alt text-lg sm:text-xl"></i></div><div><h4 class="font-bold text-base sm:text-lg" id="contact-location-label">Location</h4><p class="text-lg sm:text-xl">Dodoma, Tanzania</p></div></div></div></div><div class="rounded-xl sm:rounded-3xl shadow-xl p-6 sm:p-8" style="background: var(--card-bg);"><h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6" style="color: var(--text-primary);" id="faq-title">Frequently Asked Questions</h3><div class="space-y-3 sm:space-y-4"><div class="border-l-4 border-blue-500 pl-3 sm:pl-4 py-1 sm:py-2"><h4 class="font-bold text-sm sm:text-base" style="color: var(--text-primary);" id="faq-1-q">Can I try before paying?</h4><p class="mt-1 text-xs sm:text-sm" style="color: var(--text-secondary);" id="faq-1-a">Yes! We offer 30-day free trial with no charges.</p></div><div class="border-l-4 border-green-500 pl-3 sm:pl-4 py-1 sm:py-2"><h4 class="font-bold text-sm sm:text-base" style="color: var(--text-primary);" id="faq-2-q">Does system work online only?</h4><p class="mt-1 text-xs sm:text-sm" style="color: var(--text-secondary);" id="faq-2-a">Yes We offer online service options based on our system needs.</p></div><div class="border-l-4 border-purple-500 pl-3 sm:pl-4 py-1 sm:py-2"><h4 class="font-bold text-sm sm:text-base" style="color: var(--text-primary);" id="faq-3-q">There is internal training once I join with ShuleApp</h4><p class="mt-1 text-xs sm:text-sm" style="color: var(--text-secondary);" id="faq-3-a">Yes, The team will provide internal training as much as you will be satisfied</p></div></div></div></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 sm:py-12" style="background: var(--bg-secondary); border-top: 1px solid var(--border-color);">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-6 sm:gap-8 md:gap-10 mb-8 sm:mb-10">
                <div class="md:col-span-2 lg:col-span-1"><div class="flex items-center space-x-3 mb-4 sm:mb-6"><div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl overflow-hidden"><img src="{{ asset('storage/logo/new_logo.png') }}" alt="ShuleApp Logo" class="w-full h-full object-contain"></div><div><div class="text-xl sm:text-2xl font-bold" style="color: var(--text-primary);">ShuleApp</div></div></div><p class="text-sm sm:text-base" style="color: var(--text-secondary);" id="footer-description">We help schools have better management, accuracy and modern system at affordable cost.</p></div>
                <div><h4 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="footer-links-title">Important Links</h4><ul class="space-y-2"><li><a href="{{ route('login') }}" class="transition text-sm sm:text-base block py-1" style="color: var(--text-secondary);" id="footer-link-login">Login</a></li><li><a href="{{ route('contract.gateway.init') }}" class="transition text-sm sm:text-base block py-1" style="color: var(--text-secondary);" id="footer-link-contract">Contracts Gateway</a></li><li><a href="{{ route('tokens.verify') }}" class="transition text-sm sm:text-base block py-1" style="color: var(--text-secondary);" id="footer-link-token">Gate Pass Verifier</a></li><li><a href="{{ route('parent.e-permit.student-form') }}" class="transition text-sm sm:text-base block py-1" style="color: var(--text-secondary);" id="footer-link-pass">e-Permit System</a></li></ul></div>
                <div><h4 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="footer-services-title">Our Services</h4><ul class="space-y-2"><li class="text-sm sm:text-base" style="color: var(--text-secondary);" id="footer-service-1">Academic Management</li><li class="text-sm sm:text-base" style="color: var(--text-secondary);" id="footer-service-2">Financial Management</li><li class="text-sm sm:text-base" style="color: var(--text-secondary);" id="footer-service-3">SMS Messaging</li><li class="text-sm sm:text-base" style="color: var(--text-secondary);" id="footer-service-4">Parent Portal</li></ul></div>
                <div><h4 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4" style="color: var(--text-primary);" id="footer-follow-title">Follow Us</h4><div class="flex space-x-3 sm:space-x-4 mb-4 sm:mb-6"><a href="#" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center transition" style="background: var(--bg-primary); color: var(--text-primary);"><i class="fab fa-facebook-f"></i></a><a href="#" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center transition" style="background: var(--bg-primary); color: var(--text-primary);"><i class="fab fa-twitter"></i></a><a href="#" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center transition" style="background: var(--bg-primary); color: var(--text-primary);"><i class="fab fa-instagram"></i></a><a href="#" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center transition" style="background: var(--bg-primary); color: var(--text-primary);"><i class="fab fa-linkedin-in"></i></a></div><p class="text-sm sm:text-base mb-2" style="color: var(--text-secondary);" id="footer-whatsapp-text">Chat on WhatsApp</p><a href="https://wa.me/255678669000" class="inline-flex items-center text-green-400 hover:text-green-300 mt-1" target="_blank"><i class="fab fa-whatsapp text-xl sm:text-2xl mr-2 sm:mr-3"></i><span class="text-sm sm:text-base">+255 678 669 000</span></a></div>
            </div>
            <div class="border-t pt-6 sm:pt-8 text-center" style="border-color: var(--border-color);"><p class="text-xs sm:text-sm md:text-base" style="color: var(--text-secondary);">© 2025 ShuleApp. All Rights Reserved</p></div>
        </div>
    </footer>

    <!-- Toast Notification -->
    <div id="toastNotification" class="toast-notification">
        <i class="fas fa-check-circle"></i>
        <span id="toastMessage"></span>
    </div>

    @include('sweetalert::alert')

    <script>
        // ==================== DARK/LIGHT MODE ====================
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        function applyTheme(theme) {
            if (theme === 'dark') {
                document.body.classList.add('dark-mode');
                const themeIcon = document.getElementById('themeIcon');
                if (themeIcon) themeIcon.className = 'fas fa-sun text-yellow-400';
                localStorage.setItem('theme', 'dark');
            } else {
                document.body.classList.remove('dark-mode');
                const themeIcon = document.getElementById('themeIcon');
                if (themeIcon) themeIcon.className = 'fas fa-moon text-blue-600';
                localStorage.setItem('theme', 'light');
            }
        }

        if (savedTheme) applyTheme(savedTheme);
        else if (prefersDark) applyTheme('dark');
        else applyTheme('light');

        const themeToggleBtn = document.getElementById('themeToggleBtn');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = localStorage.getItem('theme') || 'light';
                applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
            });
        }

        // ==================== LANGUAGE TRANSLATIONS ====================
        const translations = {
            en: {
                'nav-home':'Home','nav-features':'Features','nav-stats':'Stats','nav-contact':'Contact','nav-login':'Login Now',
                'hero-title-1':'Transform ','hero-title-2':'Your School Management','hero-description':'Ultimate solution to run your school efficiently.',
                'time-label-hours':'Hours','time-label-minutes':'Minutes','time-label-seconds':'Seconds','current-time-label':'Current Time (EAT)',
                'hero-button-1':'Login now','hero-button-2':'View Features','hero-benefit-1':'No Setup Costs','hero-benefit-2':'Free Training',
                'stats-title-schools':'Schools Using ShuleApp','stats-subtitle-schools':'Currently using and growing daily',
                'stats-label-students':'Students','stats-sub-students':'Registered','stats-label-teachers':'Teachers',
                'stats-sub-teachers':'Using System','stats-label-parents':'Parents','stats-sub-parents':'Connected',
                'features-tagline':'Complete System Modules','features-title':'All ShuleApp Modules','features-description':'9 core modules with 20+ sub-modules',
                'feature-1-title':'Users Management','feature-1-desc':'Complete role-based access.','feature-1-point-1':'Teachers','feature-1-point-2':'Students',
                'feature-1-point-3':'Parents','feature-1-point-4':'Accountant','feature-1-point-5':'Non-teaching Staff',
                'feature-2-title':'Academic Management','feature-2-desc':'Full control over classes and results.','feature-2-point-1':'Class Management',
                'feature-2-point-2':'Course Management','feature-2-point-3':'Assessment Management','feature-2-point-4':'Results Management',
                'feature-3-title':'Reports & Analytics','feature-3-desc':'Comprehensive reports.','feature-3-point-1':'Attendance Report',
                'feature-3-point-2':'Daily School Report','feature-3-point-3':'Holiday Package','feature-3-point-4':'Graduates',
                'feature-4-title':'Contract System','feature-4-desc':'Digital contract management.','feature-4-point-1':'Employee Contracts',
                'feature-4-point-2':'Contract Renewals','feature-4-point-3':'Expiry Notifications',
                'feature-5-title':'Duty Roster System','feature-5-desc':'Manage staff duty schedules.','feature-5-point-1':'Shift Scheduling',
                'feature-5-point-2':'Daily Assignments','feature-5-point-3':'Supervision Planning',
                'feature-6-title':'Bulk SMS Messaging','feature-6-desc':'Send instant announcements.','feature-6-point-1':'Parent Announcements',
                'feature-6-point-2':'Emergency Alerts','feature-6-point-3':'Fee Reminders',
                'feature-7-title':'Financial System','feature-7-desc':'Complete financial tracking.','feature-7-point-1':'Expenditure Management',
                'feature-7-point-2':'Bills Payment','feature-7-point-3':'Payroll Management',
                'feature-8-title':'Gate Pass Verification','feature-8-desc':'Secure entry management.','feature-8-point-1':'Token Verification',
                'feature-8-point-2':'Real-time Logs','feature-8-point-3':'Security Reports',
                'feature-9-title':'e-Permit System','feature-9-desc':'Digital permits for students.','feature-9-point-1':'Real-time Approval',
                'feature-9-point-2':'Real-time Tracking','feature-9-point-3':'Permit History',
                'cta-title':'Ready to Transform Your School?','cta-description':'Be among the first schools to use ShuleApp',
                'cta-button':'Start Free Trial','cta-note':'Registration takes few minutes',
                'stats-title-growth':'ShuleApp Growth Statistics','stats-label-1':'School','stats-desc-1':'Currently using',
                'stats-label-2':'Students','stats-desc-2':'Registered','stats-label-3':'Teachers','stats-desc-3':'Using platform',
                'stats-label-4':'Parents','stats-desc-4':'Connected','why-title':'Why Choose ShuleApp?','why-1-title':'Fast & Easy',
                'why-1-desc':'Start using within few hours','why-2-title':'100% Secure','why-2-desc':'Your data stored with highest standards',
                'why-3-title':'24/7 Support','why-3-desc':'Our team ready to help anytime',
                'contact-tagline':'Contact Us','contact-title':'Get In Touch','contact-description':'We\'re happy to listen and answer any questions',
                'form-title':'Send Your Message','form-label-name':'Full Name','form-label-phone':'Phone Number','form-label-message':'Your Message',
                'form-button':'Send Message','contact-info-title':'Quick Contacts','contact-phone-label':'Support Phone',
                'contact-phone-note':'Monday - Sunday, 24 Hours','contact-email-label':'Email Address','contact-email-note':'We reply within 24 hours',
                'contact-location-label':'Location','faq-title':'Frequently Asked Questions','faq-1-q':'Can I try before paying?',
                'faq-1-a':'Yes! We offer 30-day free trial.','faq-2-q':'Does system work online only?','faq-2-a':'Yes, fully online system.',
                'faq-3-q':'Is internal training provided?','faq-3-a':'Yes, the team will provide internal training.',
                'footer-description':'We help schools have better management at affordable cost.','footer-links-title':'Important Links',
                'footer-link-login':'Login','footer-link-contract':'Contracts Gateway','footer-link-token':'Gate Pass Verifier',
                'footer-link-pass':'e-Permit System','footer-services-title':'Our Services','footer-service-1':'Academic Management',
                'footer-service-2':'Financial Management','footer-service-3':'SMS Messaging','footer-service-4':'Parent Portal',
                'footer-follow-title':'Follow Us','footer-whatsapp-text':'Chat on WhatsApp'
            },
            sw: {
                'nav-home':'Nyumbani','nav-features':'Vipengele','nav-stats':'Takwimu','nav-contact':'Wasiliana','nav-login':'Ingia Sasa',
                'hero-title-1':'Badilisha ','hero-title-2':'Usimamizi wa Shule','hero-description':'Suluhisho kamili la kuendesha shule yako kwa ufanisi.',
                'time-label-hours':'Masaa','time-label-minutes':'Dakika','time-label-seconds':'Sekunde','current-time-label':'Sasa Hivi (EAT)',
                'hero-button-1':'Ingia Sasa','hero-button-2':'Ona Vipengele','hero-benefit-1':'Hakuna Gharama za Kuanzisha','hero-benefit-2':'Mafunzo Bure',
                'stats-title-schools':'Shule Zinazotumia ShuleApp','stats-subtitle-schools':'Zinatumia sasa na zinakua','stats-label-students':'Wanafunzi',
                'stats-sub-students':'Waliosajiliwa','stats-label-teachers':'Walimu','stats-sub-teachers':'Wanatumia Mfumo','stats-label-parents':'Wazazi',
                'stats-sub-parents':'Wameunganishwa','features-tagline':'Moduli Kamili za Mfumo','features-title':'Moduli Zote za ShuleApp',
                'features-description':'Moduli 9 na zaidi ya 20 ndogo','feature-1-title':'Usimamizi wa Watumiaji','feature-1-desc':'Udhibiti kamili kwa majukumu.',
                'feature-1-point-1':'Walimu','feature-1-point-2':'Wanafunzi','feature-1-point-3':'Wazazi','feature-1-point-4':'Mhasibu','feature-1-point-5':'Wafanyakazi Wasio Walimu',
                'feature-2-title':'Usimamizi wa Masomo','feature-2-desc':'Udhibiti kamili wa masomo na matokeo.','feature-2-point-1':'Usimamizi wa Madarasa',
                'feature-2-point-2':'Usimamizi wa Kozi','feature-2-point-3':'Usimamizi wa Tathmini','feature-2-point-4':'Usimamizi wa Matokeo',
                'feature-3-title':'Ripoti na Takwimu','feature-3-desc':'Ripoti za kina.','feature-3-point-1':'Ripoti ya Mahudhurio','feature-3-point-2':'Ripoti ya Kila Siku',
                'feature-3-point-3':'Vifurushi vya Likizo','feature-3-point-4':'Wahitimu','feature-4-title':'Mfumo wa Mikataba','feature-4-desc':'Usimamizi wa mikataba.',
                'feature-4-point-1':'Mikataba ya Wafanyakazi','feature-4-point-2':'Omba Mkataba Mpya','feature-4-point-3':'Taarifa za Kuisha',
                'feature-5-title':'Mfumo wa Ratiba','feature-5-desc':'Dhibiti ratiba za wafanyakazi.','feature-5-point-1':'Ratiba za Zamu','feature-5-point-2':'Kazi za Kila Siku',
                'feature-5-point-3':'Mipango ya Usimamizi','feature-6-title':'Ujumbe Mfupi kwa Wingi','feature-6-desc':'Tuma matangazo ya papo kwa papo.',
                'feature-6-point-1':'Matangazo kwa Wazazi','feature-6-point-2':'Taarifa za Dharula','feature-6-point-3':'Kumbusha Ada',
                'feature-7-title':'Mfumo wa Fedha','feature-7-desc':'Ufuatiliaji kamili wa fedha.','feature-7-point-1':'Usimamizi wa Matumizi','feature-7-point-2':'Malipo ya Ada',
                'feature-7-point-3':'Usimamizi wa Mishahara','feature-8-title':'Uhakiki wa Geti Pass','feature-8-desc':'Usimamizi salama wa kuingia.',
                'feature-8-point-1':'Uhakiki wa Token','feature-8-point-2':'Kumbukumbu za Wakati Halisi','feature-8-point-3':'Ripoti za Usalama',
                'feature-9-title':'Mfumo wa Kibali','feature-9-desc':'Vibali vya kidijitali kwa wanafunzi.','feature-9-point-1':'Uidhinishwaji wa papo',
                'feature-9-point-2':'Ufuatiliaji wa Wakati Halisi','feature-9-point-3':'Historia ya Vibali',
                'cta-title':'Tayari Kubadilisha Shule Yako?','cta-description':'Jiunge na shule za kwanza kutumia ShuleApp','cta-button':'Anza Bila Malipo',
                'cta-note':'Usajili huchukua dakika chache','stats-title-growth':'Takwimu za Ukuaji','stats-label-1':'Shule','stats-desc-1':'Inatumia sasa',
                'stats-label-2':'Wanafunzi','stats-desc-2':'Waliosajiliwa','stats-label-3':'Walimu','stats-desc-3':'Wanatumia mfumo','stats-label-4':'Wazazi',
                'stats-desc-4':'Wameunganishwa','why-title':'Kwa Nini Kuchagua ShuleApp?','why-1-title':'Haraka na Rahisi','why-1-desc':'Anza kuitumia ndani ya masaa',
                'why-2-title':'Salama Kabisa','why-2-desc':'Data yako imehifadhiwa kwa viwango','why-3-title':'Msaada 24/7','why-3-desc':'Timu yetu iko tayari',
                'contact-tagline':'Wasiliana Nasi','contact-title':'Tuongee','contact-description':'Tuna furaha kukusikiliza na kukujibu maswali',
                'form-title':'Tuma Ujumbe Wako','form-label-name':'Jina Kamili','form-label-phone':'Namba ya Simu','form-label-message':'Ujumbe Wako',
                'form-button':'Tuma Ujumbe','contact-info-title':'Mawasiliano ya Haraka','contact-phone-label':'Simu ya Msaada',
                'contact-phone-note':'Juma zima, Masaa 24','contact-email-label':'Barua Pepe','contact-email-note':'Tunajibu ndani ya masaa 24',
                'contact-location-label':'Eneo','faq-title':'Maswali Yanayoulizwa','faq-1-q':'Je, naweza kujaribu kabla ya kulipia?','faq-1-a':'Ndio! Tunatoa kipindi cha majaribio.',
                'faq-2-q':'Mfumo unafanya kazi mtandaoni tu?','faq-2-a':'Ndio, tunatoa chaguo la mtandaoni pekee.','faq-3-q':'Je, mnatoa mafunzo kwa watumiaji wapya?',
                'faq-3-a':'Ndio, timu ipo tayari kutoa mafunzo.','footer-description':'Tunasaidia shule kuwa na usimamizi bora kwa gharama nafuu.',
                'footer-links-title':'Viungo Muhimu','footer-link-login':'Ingia','footer-link-contract':'Dirisha la Mikataba','footer-link-token':'Kithibitisho cha Geti Pass',
                'footer-link-pass':'Mfumo wa Kibali','footer-services-title':'Huduma Zetu','footer-service-1':'Usimamizi wa Masomo',
                'footer-service-2':'Usimamizi wa Fedha','footer-service-3':'Ujumbe wa SMS','footer-service-4':'Portal ya Wazazi',
                'footer-follow-title':'Tufuate','footer-whatsapp-text':'Jiunge Nasi WhatsApp'
            }
        };

        let currentLang = 'en';

        function changeLanguage(lang) {
            currentLang = lang;
            const selector = document.getElementById('language-selector');
            if (selector) selector.value = lang;
            Object.keys(translations[lang]).forEach(key => {
                let el = document.getElementById(key);
                if(el) el.textContent = translations[lang][key];
            });
            localStorage.setItem('shuleapp_lang', lang);
        }

        const langSelector = document.getElementById('language-selector');
        if (langSelector) langSelector.addEventListener('change', e => changeLanguage(e.target.value));
        let savedLang = localStorage.getItem('shuleapp_lang') || 'en';
        changeLanguage(savedLang);

        // ==================== MOBILE MENU ====================
        const menuToggle = document.getElementById('menu-toggle');
        if (menuToggle) {
            menuToggle.addEventListener('click', function(){
                document.getElementById('mobile-menu').classList.toggle('hidden');
            });
        }

        document.querySelectorAll('#mobile-menu a, #mobile-menu button').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobile-menu').classList.add('hidden');
            });
        });

        // ==================== LIVE TIME ====================
        function updateLiveTime(){
            let d=new Date();
            let h=d.getHours().toString().padStart(2,'0');
            let m=d.getMinutes().toString().padStart(2,'0');
            let s=d.getSeconds().toString().padStart(2,'0');
            const liveTime = document.getElementById('live-time');
            if (liveTime) liveTime.textContent=`${h}:${m}:${s}`;
            const currentHours = document.getElementById('current-hours');
            const currentMinutes = document.getElementById('current-minutes');
            const currentSeconds = document.getElementById('current-seconds');
            if (currentHours) currentHours.textContent=h;
            if (currentMinutes) currentMinutes.textContent=m;
            if (currentSeconds) currentSeconds.textContent=s;
        }

        setInterval(updateLiveTime,1000);
        updateLiveTime();

        // ==================== COUNTER ANIMATION ====================
        function animateCounter(id,val){
            let el=document.getElementById(id);
            if(!el) return;
            let start=0, inc=val/60, timer=setInterval(()=>{
                start+=inc;
                if(start>=val){
                    el.textContent=val;
                    clearInterval(timer);
                }else el.textContent=Math.floor(start);
            },20);
        }

        let statsObs = new IntersectionObserver((e)=>{
            e.forEach(entry=>{
                if(entry.isIntersecting){
                    animateCounter('stat1',3);
                    statsObs.unobserve(entry.target);
                }
            });
        },{threshold:0.3});

        let statsSec = document.getElementById('stats');
        if(statsSec) statsObs.observe(statsSec);

        // ==================== SCROLL REVEAL ====================
        if (typeof ScrollReveal !== 'undefined') {
            ScrollReveal().reveal('.feature-card',{delay:100,interval:100,origin:'bottom',distance:'30px'});
        }

        // ==================== TYPING ANIMATION ====================
        setTimeout(() => {
            const typingEl = document.querySelector('.typing-text');
            if(typingEl){
                const phrases = currentLang === 'en' ?
                    ['Your School Management','Academic Performance','Financial Tracking','Attendance Monitoring','Contracts Management'] :
                    ['Usimamizi wa Shule','Utendaji wa Masomo','Ufuatiliaji wa Fedha','Mahudhurio','Usimamizi wa Mikataba'];
                let idx=0, charIdx=0, del=false;
                function type(){
                    let cur=phrases[idx];
                    if(del){
                        typingEl.textContent=cur.substring(0,charIdx-1);
                        charIdx--;
                    }else{
                        typingEl.textContent=cur.substring(0,charIdx+1);
                        charIdx++;
                    }
                    if(!del && charIdx===cur.length){
                        del=true;
                        setTimeout(type,1500);
                    }else if(del && charIdx===0){
                        del=false;
                        idx=(idx+1)%phrases.length;
                        setTimeout(type,500);
                    }else{
                        setTimeout(type,del?50:100);
                    }
                }
                setTimeout(type,1000);
            }
        },500);

        // ==================== LOGIN FORM - WITH LOADING STATE ====================
        const loginForm = document.getElementById('loginForm');
        const loginSubmitBtn = document.getElementById('loginSubmitBtn');
        const loginBtnText = document.getElementById('loginBtnText');

        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                // Show loading spinner on button when submitting
                if (loginSubmitBtn) {
                    loginSubmitBtn.disabled = true;
                    if (loginBtnText) {
                        loginBtnText.innerHTML = '<span class="spinner"></span> Authenticating...';
                    }
                }
                // Form will submit normally - page will reload
                // The spinner will show until page reloads
            });
        }

        // ==================== LOGIN PANEL - STAYS OPEN ON ERROR ====================
        const loginOverlay = document.getElementById('loginOverlay');
        const loginPanel = document.getElementById('loginPanel');
        const showLoginBtns = ['showLoginBtn', 'mobileShowLoginBtn', 'heroShowLoginBtn', 'ctaShowLoginBtn'];
        const closePanelBtn = document.getElementById('closeLoginPanel');

        const loginFormContainer = document.getElementById('loginFormContainer');
        const forgotPasswordContainer = document.getElementById('forgotPasswordContainer');
        const showForgotPassword = document.getElementById('showForgotPassword');
        const showLoginForm = document.getElementById('showLoginForm');

        function showLoginPanel() {
            if (loginOverlay) loginOverlay.classList.add('active');
            if (loginPanel) loginPanel.classList.add('active');
            document.body.style.overflow = 'hidden';
            if (loginFormContainer && forgotPasswordContainer) {
                loginFormContainer.style.display = 'block';
                forgotPasswordContainer.style.display = 'none';
            }
            sessionStorage.setItem('loginPanelOpen', 'true');
        }

        function hideLoginPanel() {
            if (loginOverlay) loginOverlay.classList.remove('active');
            if (loginPanel) loginPanel.classList.remove('active');
            document.body.style.overflow = '';
            sessionStorage.removeItem('loginPanelOpen');
        }

        function checkAndRestorePanel() {
            const hasLoginErrors = document.querySelector('.error-message') !== null;
            const hasResetStatus = document.querySelector('.success-message') !== null;
            const panelWasOpen = sessionStorage.getItem('loginPanelOpen') === 'true';

            if (hasLoginErrors || hasResetStatus || panelWasOpen) {
                showLoginPanel();
            }
        }

        showLoginBtns.forEach(btnId => {
            const btn = document.getElementById(btnId);
            if(btn) btn.addEventListener('click', showLoginPanel);
        });

        if (closePanelBtn) closePanelBtn.addEventListener('click', hideLoginPanel);
        if (loginOverlay) loginOverlay.addEventListener('click', hideLoginPanel);

        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape' && loginPanel && loginPanel.classList.contains('active')) {
                hideLoginPanel();
            }
        });

        if (showForgotPassword) {
            showForgotPassword.addEventListener('click', function(e) {
                e.preventDefault();
                if (loginFormContainer) loginFormContainer.style.display = 'none';
                if (forgotPasswordContainer) forgotPasswordContainer.style.display = 'block';
                const statusDiv = document.getElementById('resetStatusMessage');
                if (statusDiv) statusDiv.innerHTML = '';
            });
        }

        if (showLoginForm) {
            showLoginForm.addEventListener('click', function(e) {
                e.preventDefault();
                if (forgotPasswordContainer) forgotPasswordContainer.style.display = 'none';
                if (loginFormContainer) loginFormContainer.style.display = 'block';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkAndRestorePanel();
        });

        // ==================== PASSWORD TOGGLE ====================
        const toggleLoginPassword = document.getElementById('toggleLoginPassword');
        const loginPassword = document.getElementById('login-password');

        if (toggleLoginPassword && loginPassword) {
            toggleLoginPassword.addEventListener('click', function() {
                const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                loginPassword.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye mt-8"></i>' : '<i class="fas fa-eye-slash mt-8"></i>';
            });
        }

        // ==================== TOAST NOTIFICATION ====================
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const toastMessage = document.getElementById('toastMessage');
            if (!toast || !toastMessage) return;
            const icon = toast.querySelector('i');
            if (icon) icon.className = type === 'error' ? 'fas fa-times-circle' : 'fas fa-check-circle';
            toastMessage.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 5000);
        }

        // ==================== FORGOT PASSWORD FORM ====================
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        const resetSubmitBtn = document.getElementById('resetSubmitBtn');
        const resetBtnText = document.getElementById('resetBtnText');
        const resetStatusMessage = document.getElementById('resetStatusMessage');

        if (forgotPasswordForm) {
            forgotPasswordForm.addEventListener('submit', function() {
                if (resetSubmitBtn) {
                    resetSubmitBtn.disabled = true;
                    if (resetBtnText) resetBtnText.innerHTML = '<span class="spinner"></span> Sending...';
                }
            });
        }

        // ==================== BIOMETRIC FUNCTIONALITY ====================
        const isWebAuthnSupported = window.PublicKeyCredential !== undefined;
        let bioSettings = JSON.parse(localStorage.getItem('bioSettings') || '{"enabled":false,"registered":false,"username":null}');

        const biometricToggle = document.getElementById('biometricToggle');
        const biometricSection = document.getElementById('biometricSection');
        const bioBtn = document.getElementById('bioBtn');
        const bioBtnText = document.getElementById('bioBtnText');
        const clearBioBtn = document.getElementById('clearBioBtn');
        const biometricActions = document.getElementById('biometricActions');

        function updateBiometricUI() {
            if(biometricToggle) biometricToggle.checked = bioSettings.enabled;
            if(biometricSection) biometricSection.style.display = bioSettings.enabled ? 'block' : 'none';
            if(biometricActions) biometricActions.style.display = bioSettings.registered ? 'flex' : 'none';
            if(bioBtnText) bioBtnText.textContent = bioSettings.registered ? 'Use Biometric' : 'Set Up Biometrics';
            if(clearBioBtn) clearBioBtn.style.display = bioSettings.registered ? 'block' : 'none';
        }

        if(!isWebAuthnSupported) {
            if(biometricToggle) biometricToggle.disabled = true;
            if(bioBtn) bioBtn.disabled = true;
        }

        if(biometricToggle) {
            biometricToggle.addEventListener('change', function(e) {
                bioSettings.enabled = e.target.checked;
                localStorage.setItem('bioSettings', JSON.stringify(bioSettings));
                updateBiometricUI();
                if (e.target.checked && !bioSettings.registered) {
                    showToast('Please set up biometrics by clicking "Set Up Biometrics" button', 'info');
                }
            });
        }

        updateBiometricUI();

        function base64urlToUint8Array(base64url) {
            const padding = '='.repeat((4 - (base64url.length % 4)) % 4);
            const base64 = (base64url + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = atob(base64);
            const buffer = new Uint8Array(rawData.length);
            for(let i = 0; i < rawData.length; i++) buffer[i] = rawData.charCodeAt(i);
            return buffer;
        }

        function uint8ArrayToBase64url(buffer) {
            const bytes = new Uint8Array(buffer);
            let binary = '';
            bytes.forEach(byte => binary += String.fromCharCode(byte));
            return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        }

        async function performBiometricSetup() {
            const usernameInput = document.getElementById('login-username');
            const username = usernameInput ? usernameInput.value.trim() : '';

            if(!username) {
                showToast('Please enter your email or phone number first', 'error');
                usernameInput?.focus();
                return;
            }

            try {
                bioBtn.innerHTML = '<span class="spinner"></span> Sending OTP...';
                bioBtn.disabled = true;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const otpResponse = await fetch("/biometric/send-otp", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ username })
                });

                const otpData = await otpResponse.json();
                if(!otpData.success) throw new Error(otpData.message || 'Failed to send OTP');

                const otpModal = document.createElement('div');
                otpModal.className = 'otp-modal';
                otpModal.innerHTML = `
                    <div style="background: var(--bg-secondary); padding: 28px; border-radius: 20px; width: 90%; max-width: 380px;">
                        <div style="text-align: center; margin-bottom: 20px;">
                            <i class="fas fa-fingerprint" style="font-size: 48px; color: #6366f1;"></i>
                        </div>
                        <h3 style="color: var(--text-primary); text-align: center; margin-bottom: 10px;">Verify Your Identity</h3>
                        <p style="color: var(--text-secondary); text-align: center; margin-bottom: 20px;">Enter the 5-digit code sent to your phone</p>
                        <div class="otp-container">
                            ${Array(5).fill().map((_, i) => `<input type="text" id="otp-digit-${i}" class="otp-digit" maxlength="1" inputmode="numeric" pattern="\\d*">`).join('')}
                        </div>
                        <button id="verifyOtpBtn" style="width: 100%; padding: 14px; background: #6366f1; color: white; border: none; border-radius: 12px; cursor: pointer; font-weight: 600;">
                            <span id="verifyOtpBtnText">Verify & Register Biometric</span>
                        </button>
                        <button id="cancelOtpBtn" style="width: 100%; padding: 12px; background: #ef4444; color: white; border: none; border-radius: 12px; cursor: pointer; margin-top: 12px;">Cancel</button>
                    </div>
                `;
                document.body.appendChild(otpModal);

                setTimeout(() => {
                    const otpDigits = document.querySelectorAll('.otp-digit');
                    if(otpDigits[0]) otpDigits[0].focus();
                    otpDigits.forEach((digit, index) => {
                        digit.addEventListener('input', (e) => {
                            if(e.target.value.length === 1 && index < otpDigits.length - 1) otpDigits[index + 1].focus();
                        });
                        digit.addEventListener('keydown', (e) => {
                            if(e.key === 'Backspace' && e.target.value === '' && index > 0) otpDigits[index - 1].focus();
                        });
                    });
                }, 100);

                const verifyBtn = document.getElementById('verifyOtpBtn');
                const verifyBtnText = document.getElementById('verifyOtpBtnText');

                verifyBtn.addEventListener('click', async () => {
                    const otpDigits = document.querySelectorAll('.otp-digit');
                    const otp = Array.from(otpDigits).map(d => d.value).join('');
                    if(otp.length !== 5 || !/^\d+$/.test(otp)) {
                        showToast('Please enter a valid 5-digit OTP', 'error');
                        return;
                    }

                    try {
                        verifyBtn.disabled = true;
                        verifyBtnText.innerHTML = '<span class="spinner"></span> Verifying...';

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        const verifyResponse = await fetch("/biometric/verify-otp", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ username, otp })
                        });

                        const verifyData = await verifyResponse.json();
                        if(!verifyData.success) throw new Error(verifyData.message || 'Invalid OTP');

                        await registerBiometricCredential(username);
                        otpModal.remove();
                    } catch(error) {
                        showToast(error.message || 'Verification failed', 'error');
                        verifyBtn.disabled = false;
                        verifyBtnText.textContent = 'Verify & Register Biometric';
                    }
                });

                document.getElementById('cancelOtpBtn').addEventListener('click', () => otpModal.remove());
            } catch(error) {
                showToast(error.message, 'error');
            } finally {
                bioBtn.innerHTML = '<i class="fas fa-fingerprint text-xl"></i><span id="bioBtnText">Set Up Biometrics</span>';
                bioBtn.disabled = false;
            }
        }

        async function performBiometricLogin() {
            const storedUsername = bioSettings.username;
            if(!storedUsername) {
                showToast('Biometric setup incomplete. Please set up again.', 'error');
                bioSettings.registered = false;
                bioSettings.username = null;
                localStorage.setItem('bioSettings', JSON.stringify(bioSettings));
                updateBiometricUI();
                return;
            }

            const originalContent = bioBtn.innerHTML;
            bioBtn.innerHTML = '<span class="spinner"></span> Authenticating...';
            bioBtn.disabled = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const optionsRes = await fetch("/webauthn/login/options", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ username: storedUsername })
                });

                if(!optionsRes.ok) throw new Error('Failed to get authentication options');
                const options = await optionsRes.json();

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

                const assertion = await navigator.credentials.get({ publicKey });

                const verifyRes = await fetch("/webauthn/login/verify", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
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
                            userHandle: assertion.response.userHandle ? uint8ArrayToBase64url(assertion.response.userHandle) : null
                        }
                    })
                });

                const verifyData = await verifyRes.json();
                if(verifyData.success) {
                    showToast('Login successful! Redirecting...', 'success');
                    setTimeout(() => window.location.href = verifyData.redirect || '/dashboard', 1000);
                } else {
                    throw new Error(verifyData.message || 'Authentication failed');
                }
            } catch(error) {
                showToast(error.message || 'Authentication failed. Please use password login.', 'error');
            } finally {
                bioBtn.innerHTML = originalContent;
                bioBtn.disabled = false;
            }
        }

        if(bioBtn) {
            bioBtn.addEventListener('click', async function() {
                if (bioSettings.registered && bioSettings.username) {
                    await performBiometricLogin();
                } else {
                    await performBiometricSetup();
                }
            });
        }

        async function registerBiometricCredential(username) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                showToast('Please use your fingerprint/face ID to register', 'info');

                const optionsRes = await fetch("/webauthn/register/options", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username })
                });

                const options = await optionsRes.json();
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

                const verificationRes = await fetch("/webauthn/register/verify", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
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
                if(result.success) {
                    showToast('Biometric registration successful!', 'success');
                    bioSettings.registered = true;
                    bioSettings.username = username;
                    localStorage.setItem('bioSettings', JSON.stringify(bioSettings));
                    updateBiometricUI();
                    const bioBtnSpan = document.querySelector('#bioBtn span');
                    if (bioBtnSpan) bioBtnSpan.textContent = 'Use Biometric';
                } else {
                    throw new Error(result.message || 'Registration failed');
                }
            } catch(error) {
                if (error.name === 'NotAllowedError') {
                    showToast('Biometric registration cancelled or not allowed', 'error');
                } else if (error.name === 'NotSupportedError') {
                    showToast('Your device does not support biometric authentication', 'error');
                } else {
                    showToast('Registration failed: ' + error.message, 'error');
                }
                throw error;
            }
        }

        if(clearBioBtn) {
            clearBioBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                if(!bioSettings.username) {
                    showToast('No biometric data found', 'error');
                    return;
                }
                if(!confirm('Are you sure you want to delete your biometric login data?')) return;

                try {
                    clearBioBtn.innerHTML = '<span class="spinner"></span> Clearing...';
                    clearBioBtn.disabled = true;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    const deleteResponse = await fetch("/webauthn/delete-credentials", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ username: bioSettings.username })
                    });

                    const result = await deleteResponse.json();
                    if(result.success) {
                        bioSettings.registered = false;
                        bioSettings.username = null;
                        localStorage.setItem('bioSettings', JSON.stringify(bioSettings));
                        updateBiometricUI();
                        showToast('Biometric data deleted successfully', 'success');
                        const bioBtnSpan = document.querySelector('#bioBtn span');
                        if (bioBtnSpan) bioBtnSpan.textContent = 'Set Up Biometrics';
                    } else {
                        throw new Error(result.message || 'Failed to delete biometric data');
                    }
                } catch(error) {
                    showToast(error.message || 'Failed to delete biometric data', 'error');
                } finally {
                    clearBioBtn.innerHTML = 'Clear Biometric Data';
                    clearBioBtn.disabled = false;
                }
            });
        }
    </script>
</body>
</html>
