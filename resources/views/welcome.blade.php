<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Piano">
    <title>ShuleApp</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/favicon-16x16.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            scroll-behavior: smooth;
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .counter-box {
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            box-shadow: 10px 10px 30px #d9d9d9, -10px -10px 30px #ffffff;
        }

        .typing-container {
            display: inline-block;
            position: relative;
        }

        .typing-text {
            border-right: 3px solid #3b82f6;
            white-space: nowrap;
            overflow: hidden;
            animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
        }

        @keyframes typing {
            from {
                width: 0
            }

            to {
                width: 100%
            }
        }

        @keyframes blink-caret {

            from,
            to {
                border-color: transparent
            }

            50% {
                border-color: #3b82f6
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Fix for mobile overflow */
        .mobile-container {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Counter animation styles */
        .counter-animate {
            display: inline-block;
            min-width: 40px;
        }

        /* Staggered animation for stats */
        .stats-item {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .stats-item.animated {
            opacity: 1;
            transform: translateY(0);
        }

        /* Pulse animation for important numbers */
        @keyframes pulse-glow {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }

        .pulse-glow {
            animation: pulse-glow 2s infinite;
        }

        /* Number counting animation */
        @keyframes countUp {
            from {
                opacity: 0;
                transform: scale(0.5);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .counting {
            animation: countUp 1s ease-out forwards;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-blue-50 mobile-container">

    <!-- Language Selector -->
    <div class="fixed top-4 right-4 z-50">
        <div class="flex items-center space-x-2 bg-white/90 backdrop-blur-sm rounded-full px-4 py-2 shadow-lg">
            <i class="fas fa-globe text-blue-600"></i>
            <select id="language-selector" class="bg-transparent border-0 focus:ring-0 text-sm">
                <option value="en" selected>English</option>
                <option value="sw">Kiswahili</option>
            </select>
        </div>
    </div>

    <!-- Header -->
    <header class="fixed top-0 w-full bg-white/90 backdrop-blur-md shadow-md z-40">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <!-- Logo -->
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('storage/logo/logo.png') }}" alt="ShuleApp Logo"
                            class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-bold text-blue-700">ShuleApp</div>
                    </div>
                </div>

                <nav class="hidden md:flex space-x-6 lg:space-x-8">
                    <a href="#home"
                        class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition text-sm lg:text-base">
                        <i class="fas fa-home"></i>
                        <span id="nav-home">Home</span>
                    </a>
                    <a href="#features"
                        class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition text-sm lg:text-base">
                        <i class="fas fa-star"></i>
                        <span id="nav-features">Features</span>
                    </a>
                    <a href="#stats"
                        class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition text-sm lg:text-base">
                        <i class="fas fa-chart-line"></i>
                        <span id="nav-stats">Stats</span>
                    </a>
                    <a href="#contact"
                        class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition text-sm lg:text-base">
                        <i class="fas fa-phone"></i>
                        <span id="nav-contact">Contact</span>
                    </a>
                </nav>

                <div class="flex items-center space-x-3 sm:space-x-4">
                    <!-- Live Time Counter -->
                    <div
                        class="hidden md:block text-xs sm:text-sm bg-blue-50 text-blue-700 px-2 sm:px-3 py-1 rounded-full">
                        <i class="fas fa-clock mr-1"></i>
                        <span id="live-time">00:00:00</span>
                    </div>

                    <a href="{{ route('login') }}"
                        class="hidden md:block gradient-bg text-white px-4 sm:px-6 py-2 rounded-full font-semibold hover:shadow-lg transition-all duration-300 pulse text-sm sm:text-base">
                        <span id="nav-login">Login Now</span>
                    </a>
                    <!-- Mobile Menu Button -->
                    <button id="menu-toggle" class="md:hidden focus:outline-none text-gray-700">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-md p-4 sm:p-6 space-y-3 shadow-lg">
            <a href="#home"
                class="block text-gray-700 font-medium py-2 border-b border-gray-100 flex items-center space-x-2">
                <i class="fas fa-home w-5"></i>
                <span id="mobile-nav-home">Home</span>
            </a>
            <a href="#features"
                class="block text-gray-700 font-medium py-2 border-b border-gray-100 flex items-center space-x-2">
                <i class="fas fa-star w-5"></i>
                <span id="mobile-nav-features">Features</span>
            </a>
            <a href="#stats"
                class="block text-gray-700 font-medium py-2 border-b border-gray-100 flex items-center space-x-2">
                <i class="fas fa-chart-line w-5"></i>
                <span id="mobile-nav-stats">Stats</span>
            </a>
            <a href="#contact"
                class="block text-gray-700 font-medium py-2 border-b border-gray-100 flex items-center space-x-2">
                <i class="fas fa-phone w-5"></i>
                <span id="mobile-nav-contact">Contact</span>
            </a>
            <a href="{{ route('login') }}"
                class="block gradient-bg text-white text-center py-3 rounded-lg font-semibold mt-2">
                <span id="mobile-nav-login">Login Now</span>
            </a>
        </div>
    </header>

    <!-- Hero Section - FIXED -->
    <section id="home" class="min-h-screen relative overflow-hidden pt-16 sm:pt-20">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('assets/images/bg/bg-2.jpeg') }}" alt="School Background"
                class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/70 to-purple-900/70"></div>
        </div>

        <div
            class="w-full px-4 sm:px-6 lg:px-8 h-full flex flex-col lg:flex-row items-center justify-center relative z-10 pt-20 lg:pt-32">
            <div class="lg:w-1/2 text-white mb-8 lg:mb-0">
                <h1 class="text-3xl sm:text-4xl md:text-4xl lg:text-3xl font-bold mb-4 sm:mb-6 leading-tight">
                    <span id="hero-title-1">Transform </span>
                    <div class="typing-container">
                        <span class="text-yellow-300 typing-text" id="hero-title-2">Your School Management</span>
                    </div>
                </h1>

                <p class="text-lg sm:text-xl md:text-2xl mb-6 sm:mb-8 text-blue-100" id="hero-description">
                    Altimate solution to run your school efficiently and with top-level security.
                </p>

                <!-- Current Time Display -->
                <div
                    class="mb-6 sm:mb-8 p-3 sm:p-4 bg-white/10 backdrop-blur-sm rounded-xl inline-block max-w-full overflow-hidden">
                    <div class="flex items-center justify-center space-x-3 sm:space-x-4">
                        <div class="text-center">
                            <div class="text-2xl sm:text-3xl font-bold text-green-300" id="current-hours">00</div>
                            <div class="text-xs sm:text-sm text-blue-100" id="time-label-hours">Hours</div>
                        </div>
                        <div class="text-xl sm:text-2xl text-white">:</div>
                        <div class="text-center">
                            <div class="text-2xl sm:text-3xl font-bold text-green-300" id="current-minutes">00</div>
                            <div class="text-xs sm:text-sm text-blue-100" id="time-label-minutes">Minutes</div>
                        </div>
                        <div class="text-xl sm:text-2xl text-white">:</div>
                        <div class="text-center">
                            <div class="text-2xl sm:text-3xl font-bold text-green-300" id="current-seconds">00</div>
                            <div class="text-xs sm:text-sm text-blue-100" id="time-label-seconds">Seconds</div>
                        </div>
                    </div>
                    <div class="text-center mt-1 sm:mt-2 text-blue-100 text-xs sm:text-sm">
                        <i class="fas fa-clock mr-1 sm:mr-2"></i><span id="current-time-label">Current Time
                            (EAT)</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-6 sm:mb-8">
                    <a href="{{ route('login') }}"
                        class="gradient-bg text-white px-6 sm:px-8 py-3 sm:py-4 rounded-full text-base sm:text-lg font-semibold hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-center">
                        <i class="fas fa-sign-in-alt mr-2"></i><span id="hero-button-1">Login now</span>
                    </a>
                    <a href="#features"
                        class="bg-white/20 backdrop-blur-sm text-white px-6 sm:px-8 py-3 sm:py-4 rounded-full text-base sm:text-lg font-semibold hover:bg-white/30 transition-all duration-300 text-center">
                        <i class="fas fa-play-circle mr-2"></i><span id="hero-button-2">View Features</span>
                    </a>
                </div>

                <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-6 mt-6 sm:mt-8">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-300 text-lg sm:text-xl mr-2"></i>
                        <span class="text-base sm:text-lg text-yellow-300" id="hero-benefit-1">No Setup Costs</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-300 text-lg sm:text-xl mr-2"></i>
                        <span class="text-base sm:text-lg text-yellow-300" id="hero-benefit-2">Free Training</span>
                    </div>
                </div>
            </div>

            <div class="lg:w-1/2 flex justify-center mt-8 lg:mt-0">
                <div class="relative w-full max-w-md sm:max-w-lg px-2">
                    <div
                        class="absolute -top-4 -left-4 w-16 h-16 sm:-top-6 sm:-left-6 sm:w-24 sm:h-24 bg-yellow-400 rounded-full opacity-20">
                    </div>
                    <div
                        class="absolute -bottom-4 -right-4 w-20 h-20 sm:-bottom-6 sm:-right-6 sm:w-32 sm:h-32 bg-purple-500 rounded-full opacity-20">
                    </div>
                    <div
                        class="relative bg-white/10 backdrop-blur-lg rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-2xl border border-white/20">
                        <div class="text-center mb-4 sm:mb-6">
                            <div class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-2"
                                id="stats-title-schools">Schools Using ShuleApp</div>
                            <div class="text-4xl sm:text-5xl font-bold text-yellow-300" id="live-counter">3</div>
                            <div class="text-white mt-1 sm:mt-2 text-sm sm:text-base" id="stats-subtitle-schools">
                                Currently using and growing daily</div>
                        </div>

                        <div class="space-y-3 sm:space-y-4">
                            <div class="flex items-center justify-between bg-white/10 p-3 sm:p-4 rounded-xl">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                        <i class="fas fa-users text-white text-sm sm:text-base"></i>
                                    </div>
                                    <div class="text-white">
                                        <div class="font-bold text-sm sm:text-base" id="stats-label-students">Students
                                        </div>
                                        <div class="text-xs sm:text-sm" id="stats-sub-students">Registered</div>
                                    </div>
                                </div>
                                <div class="text-xl sm:text-2xl font-bold text-white">1,000+</div>
                            </div>

                            <div class="flex items-center justify-between bg-white/10 p-3 sm:p-4 rounded-xl">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 sm:w-10 sm:h-10 bg-green-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                        <i class="fas fa-chalkboard-teacher text-white text-sm sm:text-base"></i>
                                    </div>
                                    <div class="text-white">
                                        <div class="font-bold text-sm sm:text-base" id="stats-label-teachers">Teachers
                                        </div>
                                        <div class="text-xs sm:text-sm" id="stats-sub-teachers">Using System</div>
                                    </div>
                                </div>
                                <div class="text-xl sm:text-2xl font-bold text-white">50+</div>
                            </div>

                            <div class="flex items-center justify-between bg-white/10 p-3 sm:p-4 rounded-xl">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                        <i class="fas fa-user-friends text-white text-sm sm:text-base"></i>
                                    </div>
                                    <div class="text-white">
                                        <div class="font-bold text-sm sm:text-base" id="stats-label-parents">Parents
                                        </div>
                                        <div class="text-xs sm:text-sm" id="stats-sub-parents">Connected</div>
                                    </div>
                                </div>
                                <div class="text-xl sm:text-2xl font-bold text-white">1,000+</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wave Divider -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full">
                <path fill="#ffffff" fill-opacity="1"
                    d="M0,224L48,218.7C96,213,192,203,288,181.3C384,160,480,128,576,138.7C672,149,768,203,864,202.7C960,203,1056,149,1152,138.7C1248,128,1344,160,1392,176L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
                </path>
            </svg>
        </div>
    </section>

    <!-- Features Section - ALREADY GOOD -->
    <section id="features" class="py-16 sm:py-20 bg-white relative">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 sm:mb-16">
                <div
                    class="inline-block gradient-bg text-white px-4 sm:px-6 py-2 rounded-full font-semibold mb-3 sm:mb-4">
                    <i class="fas fa-crown mr-2"></i><span id="features-tagline">Top Features</span>
                </div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-4 sm:mb-6 text-gray-800"
                    id="features-title">How ShuleApp Helps You?</h2>
                <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto" id="features-description">We give you
                    all tools you need to run your school efficiently in one integrated system</p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-12 sm:mb-16">

                <!-- Feature 1 -->
                <div
                    class="feature-card bg-gradient-to-br from-white to-blue-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div
                        class="w-12 h-12 sm:w-16 sm:h-16 gradient-bg rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <i class="fas fa-users-cog text-white text-xl sm:text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-1-title">User
                        Management</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-1-desc">Manage all
                        stakeholders: teachers, students, parents, drivers, staff and administrators in one dashboard.
                    </p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-1-point-1">User registration and
                                access</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-1-point-2">Different roles and
                                permissions</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-1-point-3">Contact information</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 2 -->
                <div
                    class="feature-card bg-gradient-to-br from-white to-green-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div
                        class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <i class="fas fa-graduation-cap text-white text-xl sm:text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-2-title">Academic
                        Management</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-2-desc">Manage classes,
                        subjects, exams, results and holiday packages easily and accurately.</p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-2-point-1">Class and subject setup</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-2-point-2">Exam results and analysis</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-2-point-3">Holiday Packages</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 3 -->
                <div
                    class="feature-card bg-gradient-to-br from-white to-yellow-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div
                        class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <i class="fas fa-clipboard-check text-white text-xl sm:text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-3-title">
                        Attendance Management</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-3-desc">Track student
                        attendance, daily reports and teacher duty rosters in real-time.</p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-3-point-1">Student and teacher
                                attendance</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-3-point-2">Daily school reports</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-3-point-3">Teacher duty rosters</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 4 -->
                <div
                    class="feature-card bg-gradient-to-br from-white to-purple-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div
                        class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <i class="fas fa-file-contract text-white text-xl sm:text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-4-title">Contract
                        Management</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-4-desc">Manage employee
                        contracts and keep records for future reference easily.</p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-4-point-1">Employee contracts</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-4-point-2">Historical records</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-4-point-3">Expiry notifications</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 5 -->
                <div
                    class="feature-card bg-gradient-to-br from-white to-red-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div
                        class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-red-500 to-pink-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <i class="fas fa-sms text-white text-xl sm:text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-5-title">Bulk SMS
                        Announcements</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-5-desc">Send announcements
                        to parents, teachers and all staff within one minute with single message.</p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-5-point-1">Messages to parents &
                                teachers</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-5-point-2">Announcements within 1
                                minute</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-5-point-3">Emergency alerts</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 6 -->
                <div
                    class="feature-card bg-gradient-to-br from-white to-indigo-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div
                        class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6">
                        <i class="fas fa-money-bill-wave text-white text-xl sm:text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-6-title">
                        Financial Management</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-6-desc">Track daily
                        expenses, school fee payments and invoices easily with comprehensive reports.</p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-6-point-1">Daily expense tracking</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-6-point-2">School fee payments</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i>
                            <span class="text-sm sm:text-base" id="feature-6-point-3">Invoice tracking</span>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Call to Action -->
            <div
                class="gradient-bg rounded-2xl sm:rounded-3xl p-8 sm:p-10 text-center text-white shadow-2xl mt-8 sm:mt-12">
                <h3 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6" id="cta-title">Ready to Transform Your School?
                </h3>
                <p class="text-lg sm:text-xl mb-6 sm:mb-8 max-w-2xl mx-auto" id="cta-description">Be among the first
                    schools to use ShuleApp and simplify your daily management</p>
                <a href="{{ route('login') }}"
                    class="inline-block bg-white text-blue-700 px-8 sm:px-10 py-3 sm:py-4 rounded-full text-lg sm:text-xl font-bold hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-play mr-3"></i><span id="cta-button">Start Free Trial</span>
                </a>
                <p class="mt-4 sm:mt-6 text-blue-100 text-sm sm:text-base"><i class="fas fa-clock mr-2"></i><span
                        id="cta-note">Registration takes few minutes</span></p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-16 sm:py-20 bg-gradient-to-br from-gray-900 to-blue-900 text-white">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-12 sm:mb-16" id="stats-title">ShuleApp Growth
                Statistics</h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 mb-12 sm:mb-16">

                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl">
                    <div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-blue-600" id="stat1">0</div>
                    <div class="text-lg sm:text-xl font-semibold mb-2 text-gray-700" id="stats-label-1">School</div>
                    <p class="text-gray-600 text-sm sm:text-base" id="stats-desc-1">Currently using our system</p>
                    <div class="mt-3 sm:mt-4">
                        <i class="fas fa-school text-2xl sm:text-3xl text-blue-500"></i>
                    </div>
                </div>

                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl">
                    <div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-green-600">1,000+</div>
                    <div class="text-lg sm:text-xl font-semibold mb-2 text-gray-700" id="stats-label-2">Students</div>
                    <p class="text-gray-600 text-sm sm:text-base" id="stats-desc-2">Registered in system</p>
                    <div class="mt-3 sm:mt-4">
                        <i class="fas fa-user-graduate text-2xl sm:text-3xl text-green-500"></i>
                    </div>
                </div>

                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl">
                    <div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-purple-600">50+</div>
                    <div class="text-lg sm:text-xl font-semibold mb-2 text-gray-700" id="stats-label-3">Teachers</div>
                    <p class="text-gray-600 text-sm sm:text-base" id="stats-desc-3">Using the platform</p>
                    <div class="mt-3 sm:mt-4">
                        <i class="fas fa-chalkboard-teacher text-2xl sm:text-3xl text-purple-500"></i>
                    </div>
                </div>

                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl">
                    <div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-red-600">1,000+</div>
                    <div class="text-lg sm:text-xl font-semibold mb-2 text-gray-700" id="stats-label-4">Parents</div>
                    <p class="text-gray-600 text-sm sm:text-base" id="stats-desc-4">Connected to portal</p>
                    <div class="mt-3 sm:mt-4">
                        <i class="fas fa-user-friends text-2xl sm:text-3xl text-red-500"></i>
                    </div>
                </div>

            </div>

            <div class="bg-white/10 backdrop-blur-sm rounded-xl sm:rounded-2xl p-6 sm:p-8 max-w-4xl mx-auto">
                <h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6" id="why-title">Why Choose ShuleApp?</h3>
                <div class="grid md:grid-cols-3 gap-4 sm:gap-6">
                    <div class="p-4 sm:p-6 bg-white/5 rounded-xl">
                        <i class="fas fa-bolt text-2xl sm:text-3xl text-yellow-400 mb-3 sm:mb-4"></i>
                        <h4 class="font-bold text-lg sm:text-xl mb-2" id="why-1-title">Fast & Easy</h4>
                        <p class="text-blue-100 text-sm sm:text-base" id="why-1-desc">Start using within few hours</p>
                    </div>
                    <div class="p-4 sm:p-6 bg-white/5 rounded-xl">
                        <i class="fas fa-shield-alt text-2xl sm:text-3xl text-green-400 mb-3 sm:mb-4"></i>
                        <h4 class="font-bold text-lg sm:text-xl mb-2" id="why-2-title">100% Secure</h4>
                        <p class="text-blue-100 text-sm sm:text-base" id="why-2-desc">Your data stored with highest
                            standards</p>
                    </div>
                    <div class="p-4 sm:p-6 bg-white/5 rounded-xl">
                        <i class="fas fa-headset text-2xl sm:text-3xl text-purple-400 mb-3 sm:mb-4"></i>
                        <h4 class="font-bold text-lg sm:text-xl mb-2" id="why-3-title">24/7 Support</h4>
                        <p class="text-blue-100 text-sm sm:text-base" id="why-3-desc">Our team ready to help anytime
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 sm:py-20 bg-gradient-to-b from-white to-blue-50">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12 sm:mb-16">
                    <div
                        class="inline-block gradient-bg text-white px-4 sm:px-6 py-2 rounded-full font-semibold mb-3 sm:mb-4">
                        <i class="fas fa-comments mr-2"></i><span id="contact-tagline">Contact Us</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4 sm:mb-6 text-gray-800"
                        id="contact-title">Get In Touch</h2>
                    <p class="text-lg sm:text-xl text-gray-600" id="contact-description">We're happy to listen and
                        answer any questions you have</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-8 sm:gap-12">
                    <div class="bg-white rounded-xl sm:rounded-3xl shadow-xl sm:shadow-2xl p-6 sm:p-8 md:p-10">
                        <h3 class="text-xl sm:text-2xl font-bold mb-6 sm:mb-8 text-gray-800" id="form-title">Send Your
                            Message</h3>
                        <form class="space-y-4 sm:space-y-6 needs-validation" novalidate
                            action="{{ route('send.feedback.message') . '#contact' }}" method="POST"
                            id="contactForm">
                            @csrf
                            <div class="grid md:grid-cols-2 gap-4 sm:gap-6">
                                <div>
                                    <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base"
                                        id="form-label-name">Full Name</label>
                                    <input type="text" name="name" placeholder="Your name"
                                        value="{{ old('name') }}"
                                        class="w-full border border-gray-300 p-3 sm:p-4 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm sm:text-base">
                                    @error('name')
                                        <span class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base"
                                        id="form-label-phone">Phone Number</label>
                                    <input type="text" name="phone" placeholder="Phone number"
                                        value="{{ old('phone') }}"
                                        class="w-full border border-gray-300 p-3 sm:p-4 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm sm:text-base">
                                    @error('phone')
                                        <span class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base"
                                    id="form-label-message">Your Message</label>
                                <textarea placeholder="Write your message here..." name="message"
                                    class="w-full border border-gray-300 p-3 sm:p-4 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm sm:text-base"
                                    rows="4" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <span class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit"
                                class="gradient-bg w-full text-white py-3 sm:py-4 rounded-xl text-base sm:text-lg font-semibold hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                                id="saveButton">
                                <i class="fas fa-paper-plane mr-2 sm:mr-3"></i><span id="form-button">Send
                                    Message</span>
                            </button>
                        </form>
                    </div>

                    <div class="space-y-6 sm:space-y-8">
                        <div
                            class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl sm:rounded-3xl p-6 sm:p-8 md:p-10 text-white shadow-xl sm:shadow-2xl">
                            <h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6" id="contact-info-title">Quick
                                Contacts</h3>
                            <div class="space-y-4 sm:space-y-6">
                                <div class="flex items-start">
                                    <div
                                        class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center mr-3 sm:mr-4">
                                        <i class="fas fa-phone text-lg sm:text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-base sm:text-lg" id="contact-phone-label">Support
                                            Phone</h4>
                                        <a href="tel:+255678669000"
                                            class="text-xl sm:text-2xl font-bold hover:text-yellow-300 transition block mt-1">+255
                                            678 669 000</a>
                                        <p class="text-blue-100 mt-1 text-sm sm:text-base" id="contact-phone-note">
                                            Monday - Sunday, 24 Hours</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div
                                        class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center mr-3 sm:mr-4">
                                        <i class="fas fa-envelope text-lg sm:text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-base sm:text-lg" id="contact-email-label">Email
                                            Address</h4>
                                        <a href="mailto:support@shuleapp.co.tz"
                                            class="text-lg sm:text-xl hover:text-yellow-300 transition block mt-1">pianop477@gmail.com</a>
                                        <p class="text-blue-100 mt-1 text-sm sm:text-base" id="contact-email-note">We
                                            reply within 24 hours</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div
                                        class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center mr-3 sm:mr-4">
                                        <i class="fas fa-map-marker-alt text-lg sm:text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-base sm:text-lg" id="contact-location-label">
                                            Location</h4>
                                        <p class="text-lg sm:text-xl">Kikuyu, Dodoma</p>
                                        <p class="text-blue-100 mt-1 text-sm sm:text-base" id="contact-location-note">
                                            Tanzania</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl sm:rounded-3xl shadow-xl p-6 sm:p-8">
                            <h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-gray-800" id="faq-title">
                                Frequently Asked Questions</h3>
                            <div class="space-y-3 sm:space-y-4">
                                <div class="border-l-4 border-blue-500 pl-3 sm:pl-4 py-1 sm:py-2">
                                    <h4 class="font-bold text-gray-800 text-sm sm:text-base" id="faq-1-q">Can I try
                                        before paying?</h4>
                                    <p class="text-gray-600 mt-1 text-xs sm:text-sm" id="faq-1-a">Yes! We offer
                                        30-day free trial with no charges.</p>
                                </div>

                                <div class="border-l-4 border-green-500 pl-3 sm:pl-4 py-1 sm:py-2">
                                    <h4 class="font-bold text-gray-800 text-sm sm:text-base" id="faq-2-q">Does
                                        system work online only?</h4>
                                    <p class="text-gray-600 mt-1 text-xs sm:text-sm" id="faq-2-a">Yes We offer
                                        online service options based on our system needs.</p>
                                </div>

                                <div class="border-l-4 border-purple-500 pl-3 sm:pl-4 py-1 sm:py-2">
                                    <h4 class="font-bold text-gray-800 text-sm sm:text-base" id="faq-3-q">There is
                                        internal training once I join with ShuleApp</h4>
                                    <p class="text-gray-600 mt-1 text-xs sm:text-sm" id="faq-3-a">Yes, The team will
                                        provide internal training as much as you will be satisfied</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer - FIXED -->
    <footer class="bg-gray-900 text-white py-8 sm:py-12">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-6 sm:gap-8 md:gap-10 mb-8 sm:mb-10">
                <div class="md:col-span-2 lg:col-span-1">
                    <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('storage/logo/logo.png') }}" alt="ShuleApp Logo"
                                class="w-full h-full object-contain">
                        </div>
                        <div>
                            <div class="text-xl sm:text-2xl font-bold">ShuleApp</div>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm sm:text-base" id="footer-description">We help schools have better
                        management, accuracy and modern system at affordable cost.</p>
                </div>

                <div>
                    <h4 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4" id="footer-links-title">Important Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('welcome') }}"
                                class="text-gray-400 hover:text-white transition text-sm sm:text-base block py-1"
                                id="footer-link-home">Home</a></li>
                        <li><a href="#features"
                                class="text-gray-400 hover:text-white transition text-sm sm:text-base block py-1"
                                id="footer-link-features">Features</a></li>
                        <li><a href="#contact"
                                class="text-gray-400 hover:text-white transition text-sm sm:text-base block py-1"
                                id="footer-link-contact">Contact Us</a></li>
                        <li><a href="{{ route('login') }}"
                                class="text-gray-400 hover:text-white transition text-sm sm:text-base block py-1"
                                id="footer-link-login">Login</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4" id="footer-services-title">Our Services</h4>
                    <ul class="space-y-2">
                        <li class="text-gray-400 text-sm sm:text-base" id="footer-service-1">Academic Management</li>
                        <li class="text-gray-400 text-sm sm:text-base" id="footer-service-2">Financial Management</li>
                        <li class="text-gray-400 text-sm sm:text-base" id="footer-service-3">SMS Messaging</li>
                        <li class="text-gray-400 text-sm sm:text-base" id="footer-service-4">Parent Portal</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4" id="footer-follow-title">Follow Us</h4>
                    <div class="flex space-x-3 sm:space-x-4 mb-4 sm:mb-6">
                        <a href="#"
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition">
                            <i class="fab fa-facebook-f text-sm sm:text-base"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-400 transition">
                            <i class="fab fa-twitter text-sm sm:text-base"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-pink-600 transition">
                            <i class="fab fa-instagram text-sm sm:text-base"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-700 transition">
                            <i class="fab fa-linkedin-in text-sm sm:text-base"></i>
                        </a>
                    </div>
                    <p class="text-gray-400 text-sm sm:text-base mb-2" id="footer-whatsapp-text">Chat on WhatsApp</p>
                    <a href="https://wa.me/255678669000"
                        class="inline-flex items-center text-green-400 hover:text-green-300 mt-1">
                        <i class="fab fa-whatsapp text-xl sm:text-2xl mr-2 sm:mr-3"></i>
                        <span class="text-sm sm:text-base">+255 678 669 000</span>
                    </a>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-6 sm:pt-8 text-center">
                @php
                    $startYear = 2025;
                    $currentYear = date('Y');
                @endphp
                <p class="text-gray-400 text-xs sm:text-sm md:text-base">
                    ©{{ $startYear == $currentYear ? $startYear : $startYear . ' - ' . $currentYear }} ShuleApp. All
                    Rights Reserved
                </p>
            </div>
        </div>
    </footer>

    @include('sweetalert::alert')
    <script>
        // ScrollReveal configurations
        const scrollRevealConfig = {
            // General animations
            origin: 'bottom',
            distance: '50px',
            duration: 1000,
            delay: 200,
            easing: 'ease-in-out',
            reset: true,
            viewFactor: 0.2
        };

        // Initialize ScrollReveal with different configurations
        ScrollReveal().reveal('header', {
            ...scrollRevealConfig,
            origin: 'top',
            distance: '80px'
        });

        // Hero section animations
        ScrollReveal().reveal('#home .typing-container', {
            delay: 300,
            duration: 1500,
            distance: '0px',
            opacity: 0,
            scale: 0.9,
            easing: 'cubic-bezier(0.5, 0, 0, 1)'
        });

        // Stats in hero section - FIXED
        ScrollReveal().reveal('#home .relative.bg-white\\/10', {
            delay: 500,
            duration: 1500,
            distance: '50px',
            origin: 'right',
            opacity: 0,
            scale: 0.9
        });

        // Animate individual stat cards in hero
        ScrollReveal().reveal('#home .flex.items-center.justify-between.bg-white\\/10', {
            delay: 600,
            interval: 200,
            duration: 1200,
            distance: '30px',
            origin: 'bottom',
            opacity: 0
        });

        // Live counter in hero
        ScrollReveal().reveal('#live-counter', {
            delay: 400,
            duration: 2000,
            scale: 0,
            opacity: 0,
            easing: 'ease-out'
        });

        // Current time display
        ScrollReveal().reveal('#home .p-3.bg-white\\/10', {
            delay: 400,
            duration: 1500,
            distance: '50px',
            origin: 'left',
            opacity: 0
        });

        // Feature cards animation
        ScrollReveal().reveal('.feature-card', {
            delay: 200,
            interval: 100,
            duration: 1000,
            distance: '30px',
            origin: 'bottom',
            opacity: 0,
            scale: 0.95
        });

        // Stats section counter boxes - FIXED
        ScrollReveal().reveal('.counter-box', {
            delay: 200,
            interval: 150,
            duration: 1200,
            distance: '50px',
            origin: 'bottom',
            opacity: 0,
            scale: 0.9
        });

        // Why choose ShuleApp cards
        ScrollReveal().reveal('#stats .bg-white\\/5', {
            delay: 300,
            interval: 100,
            duration: 1000,
            distance: '30px',
            origin: 'bottom',
            opacity: 0
        });

        // Contact form animations
        ScrollReveal().reveal('#contact .bg-white.rounded-xl', {
            delay: 300,
            duration: 1200,
            distance: '50px',
            origin: 'left',
            opacity: 0,
            scale: 0.95
        });

        ScrollReveal().reveal('#contact .bg-gradient-to-r', {
            delay: 400,
            duration: 1200,
            distance: '50px',
            origin: 'right',
            opacity: 0,
            scale: 0.95
        });

        ScrollReveal().reveal('#contact .bg-white.rounded-xl.shadow-xl', {
            delay: 500,
            duration: 1200,
            distance: '50px',
            origin: 'right',
            opacity: 0,
            scale: 0.95
        });

        // Footer animations
        ScrollReveal().reveal('footer > div > div', {
            delay: 200,
            interval: 100,
            duration: 1000,
            distance: '30px',
            origin: 'bottom',
            opacity: 0
        });

        // Counter animation function - IMPROVED
        function animateCounter(elementId, finalValue, duration = 2000) {
            const element = document.getElementById(elementId);
            if (!element) return;

            const isInHeroSection = element.closest('#home');
            let start = 0;
            const increment = finalValue / (duration / 16);

            const timer = setInterval(() => {
                start += increment;
                if (start >= finalValue) {
                    if (isInHeroSection) {
                        // For hero section, show "+" for large numbers
                        element.textContent = finalValue > 50 ? `${finalValue}+` : finalValue;
                    } else {
                        element.textContent = finalValue;
                    }
                    clearInterval(timer);
                } else {
                    if (isInHeroSection) {
                        element.textContent = Math.floor(start) + (start > 50 ? '+' : '');
                    } else {
                        element.textContent = Math.floor(start);
                    }
                }
            }, 16);
        }

        // Initialize animations on load
        document.addEventListener('DOMContentLoaded', function() {
            // Animate hero section stats immediately
            setTimeout(() => {
                // Hero section counter
                const heroCounter = document.getElementById('live-counter');
                if (heroCounter) {
                    heroCounter.style.opacity = '1';
                    heroCounter.style.transform = 'scale(1)';
                }

                // Animate hero stats (Students, Teachers, Parents)
                const heroStats = document.querySelectorAll(
                    '#home .flex.items-center.justify-between.bg-white\\/10 .text-xl');
                heroStats.forEach((stat, index) => {
                    setTimeout(() => {
                        stat.style.opacity = '1';
                        stat.style.transform = 'translateY(0)';
                    }, 600 + (index * 200));
                });
            }, 100);

            // Animate stats section when visible
            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Animate all counters in stats section
                        animateCounter('stat1', 3, 1000);

                        // You can add more counters here if needed
                        // animateCounter('stat2', 1000, 1500);
                        // animateCounter('stat3', 50, 1200);
                        // animateCounter('stat4', 1000, 1500);

                        statsObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.3,
                rootMargin: '0px 0px -100px 0px'
            });

            const statsSection = document.getElementById('stats');
            if (statsSection) statsObserver.observe(statsSection);

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;

                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });

        // Language translations
        const translations = {
            en: {
                // Navigation
                'nav-home': 'Home',
                'nav-features': 'Features',
                'nav-stats': 'Stats',
                'nav-contact': 'Contact',
                'nav-login': 'Login Now',
                'mobile-nav-home': 'Home',
                'mobile-nav-features': 'Features',
                'mobile-nav-stats': 'Stats',
                'mobile-nav-contact': 'Contact',
                'mobile-nav-login': 'Login Now',

                // Hero Section
                'hero-title-1': 'Transform ',
                'hero-title-2': 'Your School Management',
                'hero-description': 'Altimate solution to run your school efficiently and with top-level security.',
                'time-label-hours': 'Hours',
                'time-label-minutes': 'Minutes',
                'time-label-seconds': 'Seconds',
                'current-time-label': 'Current Time (EAT)',
                'hero-button-1': 'Login Now',
                'hero-button-2': 'View Features',
                'hero-benefit-1': 'No Setup Costs',
                'hero-benefit-2': 'Free Training',

                // Stats Cards
                'stats-title-schools': 'Schools Using ShuleApp',
                'stats-subtitle-schools': 'Currently using and growing daily',
                'stats-label-students': 'Students',
                'stats-sub-students': 'Registered',
                'stats-label-teachers': 'Teachers',
                'stats-sub-teachers': 'Using System',
                'stats-label-parents': 'Parents',
                'stats-sub-parents': 'Connected',

                // Features Section
                'features-tagline': 'Top Features',
                'features-title': 'How ShuleApp Helps You?',
                'features-description': 'We give you all tools you need to run your school efficiently in one integrated system',

                // Feature 1
                'feature-1-title': 'User Management',
                'feature-1-desc': 'Manage all stakeholders: teachers, students, parents, drivers, staff and administrators in one dashboard.',
                'feature-1-point-1': 'User registration and access',
                'feature-1-point-2': 'Different roles and permissions',
                'feature-1-point-3': 'Contact information',

                // Feature 2
                'feature-2-title': 'Academic Management',
                'feature-2-desc': 'Manage classes, subjects, exams, results and holiday packages easily and accurately.',
                'feature-2-point-1': 'Class and subject setup',
                'feature-2-point-2': 'Exam results and analysis',
                'feature-2-point-3': 'Holiday Packages',

                // Feature 3
                'feature-3-title': 'Attendance Management',
                'feature-3-desc': 'Track student attendance, daily reports and teacher duty rosters in real-time.',
                'feature-3-point-1': 'Student and teacher attendance',
                'feature-3-point-2': 'Daily school reports',
                'feature-3-point-3': 'Teacher duty rosters',

                // Feature 4
                'feature-4-title': 'Contract Management',
                'feature-4-desc': 'Manage employee contracts and keep records for future reference easily.',
                'feature-4-point-1': 'Employee contracts',
                'feature-4-point-2': 'Historical records',
                'feature-4-point-3': 'Expiry notifications',

                // Feature 5
                'feature-5-title': 'Bulk SMS Announcements',
                'feature-5-desc': 'Send announcements to parents, teachers and all staff within one minute with single message.',
                'feature-5-point-1': 'Messages to parents & teachers',
                'feature-5-point-2': 'Announcements within 1 minute',
                'feature-5-point-3': 'Emergency alerts',

                // Feature 6
                'feature-6-title': 'Financial Management',
                'feature-6-desc': 'Track daily expenses, school fee payments and invoices easily with comprehensive reports.',
                'feature-6-point-1': 'Daily expense tracking',
                'feature-6-point-2': 'School fee payments',
                'feature-6-point-3': 'Invoice tracking',

                // CTA
                'cta-title': 'Ready to Transform Your School?',
                'cta-description': 'Be among the first schools to use ShuleApp and simplify your daily management',
                'cta-button': 'Start Free Trial',
                'cta-note': 'Registration takes few minutes',

                // Stats Section
                'stats-title': 'ShuleApp Growth Statistics',
                'stats-label-1': 'School',
                'stats-desc-1': 'Currently using our system',
                'stats-label-2': 'Students',
                'stats-desc-2': 'Registered in system',
                'stats-label-3': 'Teachers',
                'stats-desc-3': 'Using the platform',
                'stats-label-4': 'Parents',
                'stats-desc-4': 'Connected to portal',
                'why-title': 'Why Choose ShuleApp?',
                'why-1-title': 'Fast & Easy',
                'why-1-desc': 'Start using within few hours',
                'why-2-title': '100% Secure',
                'why-2-desc': 'Your data stored with highest standards',
                'why-3-title': '24/7 Support',
                'why-3-desc': 'Our team ready to help anytime',

                // Contact Section
                'contact-tagline': 'Contact Us',
                'contact-title': 'Get In Touch',
                'contact-description': "We're happy to listen and answer any questions you have",
                'form-title': 'Send Your Message',
                'form-label-name': 'Full Name',
                'form-label-phone': 'Phone Number',
                'form-label-message': 'Your Message',
                'form-button': 'Send Message',
                'contact-info-title': 'Quick Contacts',
                'contact-phone-label': 'Support Phone',
                'contact-phone-note': 'Monday - Sunday, 24 Hours',
                'contact-email-label': 'Email Address',
                'contact-email-note': 'We reply within 24 hours',
                'contact-location-label': 'Location',
                'contact-location-note': 'Tanzania',
                'faq-title': 'Frequently Asked Questions',
                'faq-1-q': 'Can I try before paying?',
                'faq-1-a': 'Yes! We offer 30-day free trial with no charges.',
                'faq-2-q': 'Does system work online only?',
                'faq-2-a': 'Yes We offer online service options based on our system needs.',
                'faq-3-q': 'There is internal training once I join with ShuleApp?',
                'faq-3-a': 'Yes, The team will provide internal training as much as you will be satisfied',

                // Footer
                'footer-description': 'We help schools have better management, accuracy and modern system at affordable cost.',
                'footer-links-title': 'Important Links',
                'footer-link-home': 'Home',
                'footer-link-features': 'Features',
                'footer-link-contact': 'Contact Us',
                'footer-link-login': 'Login',
                'footer-services-title': 'Our Services',
                'footer-service-1': 'Academic Management',
                'footer-service-2': 'Financial Management',
                'footer-service-3': 'SMS Messaging',
                'footer-service-4': 'Parent Portal',
                'footer-follow-title': 'Follow Us',
                'footer-whatsapp-text': 'Chat on WhatsApp',
            },
            sw: {
                // Navigation
                'nav-home': 'Nyumbani',
                'nav-features': 'Vipengele',
                'nav-stats': 'Takwimu',
                'nav-contact': 'Wasiliana',
                'nav-login': 'Ingia Sasa',
                'mobile-nav-home': 'Nyumbani',
                'mobile-nav-features': 'Vipengele',
                'mobile-nav-stats': 'Takwimu',
                'mobile-nav-contact': 'Wasiliana',
                'mobile-nav-login': 'Ingia Sasa',

                // Hero Section
                'hero-title-1': 'Badilisha ',
                'hero-title-2': 'Usimamizi wa Shule Yako',
                'hero-description': 'Suluhisho kamili la kuendesha shule yako kwa ufanisi na usalama wa hali ya juu.',
                'time-label-hours': 'Masaa',
                'time-label-minutes': 'Dakika',
                'time-label-seconds': 'Sekunde',
                'current-time-label': 'Sasa Hivi (EAT)',
                'hero-button-1': 'Ingia Sasa',
                'hero-button-2': 'Ona Vipengele',
                'hero-benefit-1': 'Hakuna Gharama za Uanzishaji',
                'hero-benefit-2': 'Mafunzo ya Bure',

                // Stats Cards
                'stats-title-schools': 'Shule Zinazotumia ShuleApp',
                'stats-subtitle-schools': 'Zinatumia sasa na zinakua kila siku',
                'stats-label-students': 'Wanafunzi',
                'stats-sub-students': 'Waliosajiliwa',
                'stats-label-teachers': 'Walimu',
                'stats-sub-teachers': 'Wanatumia Mfumo',
                'stats-label-parents': 'Wazazi',
                'stats-sub-parents': 'Wameunganishwa',

                // Features Section
                'features-tagline': 'Vipengele Bora',
                'features-title': 'ShuleApp Inakusaidia Kwa Nini?',
                'features-description': 'Tunakupa zana zote unazohitaji kuendesha shule yako kwa ufanisi kwenye mfumo mmoja uliounganishwa',

                // Feature 1
                'feature-1-title': 'Usimamizi wa Watumiaji',
                'feature-1-desc': 'Fuatilia: walimu, wanafunzi, wazazi, dereva, wafanyakasi na watendaji katika eneo moja la usimamizi.',
                'feature-1-point-1': 'Usajili na ufikiaji wa watumiaji',
                'feature-1-point-2': 'Majukumu na ruhusa tofauti',
                'feature-1-point-3': 'Taarifa za mawasiliano',

                // Feature 2
                'feature-2-title': 'Usimamizi wa Masomo',
                'feature-2-desc': 'Fuatilia madarasa, masomo, mitihani, matokeo kwa urahisi na usahihi.',
                'feature-2-point-1': 'Usanidi wa madarasa na masomo',
                'feature-2-point-2': 'Matokeo ya mitihani na uchambuzi',
                'feature-2-point-3': 'Ratiba za likizo na vipindi',

                // Feature 3
                'feature-3-title': 'Usimamizi wa Mahudhurio',
                'feature-3-desc': 'Fuatilia mahudhurio ya wanafunzi, ripoti za kila siku na ratiba za majukumu ya walimu kwa wakati halisi.',
                'feature-3-point-1': 'Mahudhurio ya wanafunzi na walimu',
                'feature-3-point-2': 'Ripoti za shule za kila siku',
                'feature-3-point-3': 'Ratiba za majukumu ya walimu',

                // Feature 4
                'feature-4-title': 'Usimamizi wa Mikataba',
                'feature-4-desc': 'Dhibiti Mikataba ya wafanyakazi wako na uhifadhi kumbukumbu kwa marejeleo ya baadaye kwa urahisi.',
                'feature-4-point-1': 'Mitakaba yaa wafanyakazi',
                'feature-4-point-2': 'Kumbukumbu za kihistoria',
                'feature-4-point-3': 'Taarifa za kuisha kwa muda wa Mkataba',

                // Feature 5
                'feature-5-title': 'Tangazo kwa SMS',
                'feature-5-desc': 'Tuma tangazo kwa wazazi, walimu na wafanyakazi wote ndani ya dakika moja kwa ujumbe mmoja.',
                'feature-5-point-1': 'Ujumbe kwa wazazi na walimu',
                'feature-5-point-2': 'Tangazo ndani ya dakika moja',
                'feature-5-point-3': 'Ujumbe wa dharura',

                // Feature 6
                'feature-6-title': 'Usimamizi wa Fedha',
                'feature-6-desc': 'Fuatilia matumizi ya kila siku, malipo ya ada ya shule na ankara kwa urahisi na ripoti kamili.',
                'feature-6-point-1': 'Matumizi ya kila siku',
                'feature-6-point-2': 'Malipo ya ada ya shule',
                'feature-6-point-3': 'Ufuatiliaji wa ankara',

                // CTA
                'cta-title': 'Tayari Kubadilisha Shule Yako?',
                'cta-description': 'Jiunge na shule za kwanza kutumia ShuleApp na rahisisha usimamizi wako wa kila siku',
                'cta-button': 'Anza Bila Malipo',
                'cta-note': 'Usajili huchukua dakika 5 tu',

                // Stats Section
                'stats-title': 'Takwimu za Ukuaji wa ShuleApp',
                'stats-label-1': 'Shule',
                'stats-desc-1': 'Inatumia mfumo wetu sasa',
                'stats-label-2': 'Wanafunzi',
                'stats-desc-2': 'Waliyosajiliwa kwenye mfumo',
                'stats-label-3': 'Walimu',
                'stats-desc-3': 'Wanatumia mfumo',
                'stats-label-4': 'Wazazi',
                'stats-desc-4': 'Wameunganishwa kwenye portal',
                'why-title': 'Kwa Nini Kuchagua ShuleApp?',
                'why-1-title': 'Haraka na Rahisi',
                'why-1-desc': 'Anza kuitumia ndani ya masaa machache tu',
                'why-2-title': 'Salama Kabisa',
                'why-2-desc': 'Data yako imehifadhiwa kwa viwango vya juu',
                'why-3-title': 'Msaada 24/7',
                'why-3-desc': 'Timu yetu iko tayari kukusaidia kila wakati',

                // Contact Section
                'contact-tagline': 'Wasiliana Nasi',
                'contact-title': 'Tuongee',
                'contact-description': 'Tuna furaha kukusikiliza na kukujibu maswali yako yoyote',
                'form-title': 'Tuma Ujumbe Wako',
                'form-label-name': 'Jina Kamili',
                'form-label-phone': 'Namba ya Simu',
                'form-label-message': 'Ujumbe Wako',
                'form-button': 'Tuma Ujumbe',
                'contact-info-title': 'Mawasiliano ya Haraka',
                'contact-phone-label': 'Simu ya Msaada',
                'contact-phone-note': 'Juma moja, Masaa 24',
                'contact-email-label': 'Barua Pepe',
                'contact-email-note': 'Tunajibu ndani ya masaa 24',
                'contact-location-label': 'Eneo',
                'contact-location-note': 'Tanzania',
                'faq-title': 'Maswali Yanayoulizwa Mara Kwa Mara',
                'faq-1-q': 'Je, naweza kujaribu kabla ya kulipia?',
                'faq-1-a': 'Ndio! Tunatoa kipindi cha majaribio cha siku 30 bila malipo.',
                'faq-2-q': 'Mfumo unafanya kazi mtandaoni tu?',
                'faq-2-a': 'Tunatoa chaguo la mtandaoni pekee hii ni kulingana na mahitaji ya kimfumo.',
                'faq-3-q': 'Je, mnatoa mafunzo kwa watumiaji wapya wanapojiunga?',
                'faq-3-a': 'Ndio, timu ipo tayari kutoa mafunzo kwa watumiaji wapya.',

                // Footer
                'footer-description': 'Tunasaidia shule kuwa na usimamizi bora, usahihi na wa kisasa kwa gharama nafuu.',
                'footer-links-title': 'Viungo Muhimu',
                'footer-link-home': 'Nyumbani',
                'footer-link-features': 'Vipengele',
                'footer-link-contact': 'Wasiliana Nasi',
                'footer-link-login': 'Ingia',
                'footer-services-title': 'Huduma Zetu',
                'footer-service-1': 'Usimamizi wa Masomo',
                'footer-service-2': 'Usimamizi wa Fedha',
                'footer-service-3': 'Ujumbe wa SMS',
                'footer-service-4': 'Portal ya Wazazi',
                'footer-follow-title': 'Tufuate',
                'footer-whatsapp-text': 'Jiunge Nasi WhatsApp',
            }
        };

        // Current language
        let currentLang = 'en';

        // Function to change language
        function changeLanguage(lang) {
            currentLang = lang;
            document.getElementById('language-selector').value = lang;

            // Update all text elements
            Object.keys(translations[lang]).forEach(key => {
                const element = document.getElementById(key);
                if (element) {
                    element.textContent = translations[lang][key];
                }
            });

            // Save to localStorage
            localStorage.setItem('shuleapp_lang', lang);

            // Update typing animation
            const typingElement = document.querySelector('.typing-text');
            if (typingElement && typingElement.id === 'hero-title-2') {
                typingElement.style.animation = 'none';
                setTimeout(() => {
                    typingElement.style.animation =
                        'typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite';
                }, 10);
            }
        }

        // Mobile menu toggle
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Close mobile menu when clicking a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobile-menu').classList.add('hidden');
            });
        });

        // Live time counter
        function updateLiveTime() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');

            // Update header time
            document.getElementById('live-time').textContent = `${hours}:${minutes}:${seconds}`;

            // Update current time display
            document.getElementById('current-hours').textContent = hours;
            document.getElementById('current-minutes').textContent = minutes;
            document.getElementById('current-seconds').textContent = seconds;
        }

        // Initialize live time
        setInterval(updateLiveTime, 1000);
        updateLiveTime();

        // Language selector change
        document.getElementById('language-selector').addEventListener('change', function() {
            changeLanguage(this.value);
        });

        // Load saved language
        document.addEventListener('DOMContentLoaded', function() {
            const savedLang = localStorage.getItem('shuleapp_lang') || 'en';
            changeLanguage(savedLang);

            // Animate stats counter - FIXED
            function animateCounter(elementId, finalValue, duration = 2000) {
                const element = document.getElementById(elementId);
                if (!element) return;

                let start = 0;
                const increment = finalValue / (duration / 16);
                const timer = setInterval(() => {
                    start += increment;
                    if (start >= finalValue) {
                        element.textContent = finalValue.toLocaleString();
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(start).toLocaleString();
                    }
                }, 16);
            }

            // Animate school counter - TRIGGER ON SCROLL
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Animate counter when stats section is visible
                        animateCounter('stat1', 3, 1000);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.5
            });

            const statsSection = document.getElementById('stats');
            if (statsSection) observer.observe(statsSection);

            // Typing animation with multiple phrases
            const typingElement = document.querySelector('.typing-text');
            if (typingElement) {
                const phrases = currentLang === 'en' ? [
                    "Your School Management",
                    "Academic Performance",
                    "Financial Tracking",
                    "Attendance Monitoring"
                ] : [
                    "Usimamizi wa Shule Yako",
                    "Utendaji wa Kimasomo",
                    "Ufuatiliaji wa Fedha",
                    "Ufuatiliaji wa Mahudhurio"
                ];

                let phraseIndex = 0;
                let charIndex = 0;
                let isDeleting = false;
                let isEnd = false;

                function typeEffect() {
                    const currentPhrase = phrases[phraseIndex];

                    if (isDeleting) {
                        // Deleting text
                        typingElement.textContent = currentPhrase.substring(0, charIndex - 1);
                        charIndex--;
                    } else {
                        // Typing text
                        typingElement.textContent = currentPhrase.substring(0, charIndex + 1);
                        charIndex++;
                    }

                    if (!isDeleting && charIndex === currentPhrase.length) {
                        // Finished typing, wait then start deleting
                        isEnd = true;
                        setTimeout(() => {
                            isDeleting = true;
                            typeEffect();
                        }, 1500);
                    } else if (isDeleting && charIndex === 0) {
                        // Finished deleting, move to next phrase
                        isDeleting = false;
                        phraseIndex = (phraseIndex + 1) % phrases.length;
                        setTimeout(typeEffect, 500);
                    } else {
                        // Continue typing or deleting
                        const speed = isDeleting ? 50 : 100;
                        setTimeout(typeEffect, speed);
                    }
                }

                // Start typing effect after initial animation
                setTimeout(typeEffect, 3500);
            }
        });

        // Scroll animations
        ScrollReveal().reveal('section', {
            delay: 200,
            distance: '50px',
            duration: 1000,
            easing: 'ease-in-out',
            origin: 'bottom'
        });

        // Form submission handler
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("contactForm");
            const submitButton = document.getElementById("saveButton");

            if (!form || !submitButton) return;

            form.addEventListener("submit", function(event) {
                event.preventDefault();

                // Validate form
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    return;
                }

                // Disable button and show loading
                submitButton.disabled = true;
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML =
                    `<span class="inline-block w-5 h-5 border-4 border-t-4 border-white rounded-full animate-spin"></span> ${currentLang === 'en' ? 'Sending...' : 'Inatumwa...'}`;

                // Submit the form after delay
                setTimeout(() => {
                    form.submit();
                }, 1500);
            });
        });
    </script>
</body>

</html>
