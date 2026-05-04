<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="author" content="Piano">
    <title>ShuleApp</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-16x16.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-32 x 32.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-192 x 192.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-512 x 512.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate, private">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-store">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { scroll-behavior: smooth; font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .feature-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); }
        .counter-box { background: linear-gradient(145deg, #ffffff, #f0f0f0); box-shadow: 10px 10px 30px #d9d9d9, -10px -10px 30px #ffffff; }
        .typing-container { display: inline-block; position: relative; }
        .typing-text { border-right: 3px solid #fbbf24; white-space: nowrap; overflow: hidden; animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite; }
        @keyframes typing { from { width: 0 } to { width: 100% } }
        @keyframes blink-caret { from, to { border-color: transparent } 50% { border-color: #fbbf24 } }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
        .mobile-container { width: 100%; max-width: 100%; overflow-x: hidden; }
        .fade-in { opacity: 0; transform: translateY(20px); transition: opacity 0.8s ease, transform 0.8s ease; }
        .fade-in.visible { opacity: 1; transform: translateY(0); }
        @keyframes pulse-glow { 0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); } 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); } }
        .pulse-glow { animation: pulse-glow 2s infinite; }
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
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('storage/logo/new_logo.png') }}" alt="ShuleApp Logo" class="w-full h-full object-contain" onerror="this.src='https://placehold.co/60x60/3b82f6/white?text=S'">
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-bold text-blue-700">ShuleApp
                            <p class="text-sm text-muted text-dark" style="font-size: 9px;"><i>Empowering Education</i></p>
                        </div>
                    </div>
                </div>
                <nav class="hidden md:flex space-x-6 lg:space-x-8">
                    <a href="#home" class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition"><i class="fas fa-home"></i><span id="nav-home">Home</span></a>
                    <a href="#features" class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition"><i class="fas fa-star"></i><span id="nav-features">Features</span></a>
                    <a href="#stats" class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition"><i class="fas fa-chart-line"></i><span id="nav-stats">Stats</span></a>
                    <a href="#contact" class="hover:text-blue-600 font-semibold flex items-center space-x-1 transition"><i class="fas fa-phone"></i><span id="nav-contact">Contact</span></a>
                </nav>
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="hidden md:block text-xs sm:text-sm bg-blue-50 text-blue-700 px-2 sm:px-3 py-1 rounded-full"><i class="fas fa-clock mr-1"></i><span id="live-time">00:00:00</span></div>
                    <a href="{{ route('login') }}" class="hidden md:block gradient-bg text-white px-4 sm:px-6 py-2 rounded-full font-semibold hover:shadow-lg transition-all duration-300 pulse"><span id="nav-login">Login Now</span></a>
                    <button id="menu-toggle" class="md:hidden focus:outline-none text-gray-700"><i class="fas fa-bars text-2xl"></i></button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-md p-4 sm:p-6 space-y-3 shadow-lg">
            <a href="#home" class="block text-gray-700 font-medium py-2 border-b border-gray-100"><i class="fas fa-home w-5"></i><span id="mobile-nav-home">Home</span></a>
            <a href="#features" class="block text-gray-700 font-medium py-2 border-b border-gray-100"><i class="fas fa-star w-5"></i><span id="mobile-nav-features">Features</span></a>
            <a href="#stats" class="block text-gray-700 font-medium py-2 border-b border-gray-100"><i class="fas fa-chart-line w-5"></i><span id="mobile-nav-stats">Stats</span></a>
            <a href="#contact" class="block text-gray-700 font-medium py-2 border-b border-gray-100"><i class="fas fa-phone w-5"></i><span id="mobile-nav-contact">Contact</span></a>
            <a href="{{ route('login') }}" class="block gradient-bg text-white text-center py-3 rounded-lg font-semibold mt-2"><span id="mobile-nav-login">Login Now</span></a>
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
                <h1 class="text-2xl sm:text-2xl md:text-2xl lg:text-2xl font-bold mb-4 sm:mb-6 leading-tight">
                    <span id="hero-title-1">Transform </span>
                    <div class="typing-container"><span class="text-yellow-300 typing-text" id="hero-title-2">Your School Management</span></div>
                </h1>
                <p class="text-lg sm:text-xl md:text-1xl mb-6 sm:mb-8 text-blue-100" id="hero-description">Ultimate solution to run your school efficiently and with top-level security.</p>

                <!-- Current Time Display - RESTORED -->
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
                    <a href="{{ route('login') }}" class="gradient-bg text-white px-6 sm:px-8 py-3 sm:py-4 rounded-full text-base sm:text-lg font-semibold hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-center"><i class="fas fa-sign-in-alt mr-2"></i><span id="hero-button-1">Login now</span></a>
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
                            <div class="text-3xl sm:text-3xl font-bold text-yellow-300" id="live-counter">3</div>
                            <div class="text-white mt-1 sm:mt-2 text-sm sm:text-base" id="stats-subtitle-schools">Currently using and growing daily</div>
                        </div>
                        <div class="space-y-3 sm:space-y-4">
                            <div class="flex items-center justify-between bg-white/10 p-3 sm:p-4 rounded-xl"><div class="flex items-center"><div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3"><i class="fas fa-users text-white"></i></div><div><div class="font-bold text-sm sm:text-base" id="stats-label-students">Students</div><div class="text-xs sm:text-sm" id="stats-sub-students">Registered</div></div></div><div class="text-xl sm:text-2xl font-bold text-white">1,500+</div></div>
                            <div class="flex items-center justify-between bg-white/10 p-3 sm:p-4 rounded-xl"><div class="flex items-center"><div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3"><i class="fas fa-chalkboard-teacher text-white"></i></div><div><div class="font-bold text-sm sm:text-base" id="stats-label-teachers">Teachers</div><div class="text-xs sm:text-sm" id="stats-sub-teachers">Using System</div></div></div><div class="text-xl sm:text-2xl font-bold text-white">100+</div></div>
                            <div class="flex items-center justify-between bg-white/10 p-3 sm:p-4 rounded-xl"><div class="flex items-center"><div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3"><i class="fas fa-user-friends text-white"></i></div><div><div class="font-bold text-sm sm:text-base" id="stats-label-parents">Parents</div><div class="text-xs sm:text-sm" id="stats-sub-parents">Connected</div></div></div><div class="text-xl sm:text-2xl font-bold text-white">1,500+</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section - ALL 9 MODULES -->
    <section id="features" class="py-16 sm:py-20 bg-white relative">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 sm:mb-16">
                <div class="inline-block gradient-bg text-white px-4 sm:px-6 py-2 rounded-full font-semibold mb-3 sm:mb-4"><i class="fas fa-crown mr-2"></i><span id="features-tagline">Complete System Modules</span></div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-4 sm:mb-6 text-gray-800" id="features-title">All ShuleApp Modules</h2>
                <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto" id="features-description">9 core modules with 20+ sub-modules to run your school perfectly</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-12 sm:mb-16">
                <!-- Modules 1-9 (same as before, keeping all features) -->
                <div class="feature-card bg-gradient-to-br from-white to-blue-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 gradient-bg rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-users-cog text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-1-title">Users Management</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-1-desc">Complete role-based access for all school stakeholders.</p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-1-point-1">Teachers</span></li>
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-1-point-2">Students</span></li>
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-1-point-3">Parents</span></li>
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-1-point-4">Accountant</span></li>
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-1-point-5">Non-teaching Staff</span></li>
                    </ul>
                </div>
                <div class="feature-card bg-gradient-to-br from-white to-green-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-graduation-cap text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-2-title">Academic Management</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-2-desc">Full control over classes, courses, assessments and results.</p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-2-point-1">Class Management</span></li>
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-2-point-2">Course Management</span></li>
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-2-point-3">Assessment Management</span></li>
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-2-point-4">Results Management</span></li>
                    </ul>
                </div>
                <div class="feature-card bg-gradient-to-br from-white to-yellow-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-chart-line text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-3-title">Reports & Analytics</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-3-desc">Comprehensive reports for data-driven decisions.</p>
                    <ul class="space-y-1 sm:space-y-2">
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-3-point-1">Attendance Report</span></li>
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-3-point-2">Daily School Report</span></li>
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-3-point-3">Holiday Package</span></li>
                        <li class="flex items-center text-gray-700"><i class="fas fa-check-circle text-green-500 mr-2 sm:mr-3 text-sm"></i><span class="text-sm sm:text-base" id="feature-3-point-4">Graduates</span></li>
                    </ul>
                </div>
                <div class="feature-card bg-gradient-to-br from-white to-purple-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-file-contract text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-4-title">Contract System</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-4-desc">Digital employee contract management with expiry alerts.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-4-point-1">Employee Contracts</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-4-point-2">Contract Renewals</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-4-point-3">Expiry Notifications</span></li></ul>
                </div>
                <div class="feature-card bg-gradient-to-br from-white to-red-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-red-500 to-rose-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-clipboard-list text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-5-title">Duty Roster System</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-5-desc">Manage teacher and staff duty schedules efficiently.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-5-point-1">Shift Scheduling</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-5-point-2">Daily Assignments</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-5-point-3">Supervision Planning</span></li></ul>
                </div>
                <div class="feature-card bg-gradient-to-br from-white to-indigo-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-sms text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-6-title">Bulk SMS Messaging</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-6-desc">Send instant announcements to parents, teachers and staff.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-6-point-1">Parent Announcements</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-6-point-2">Emergency Alerts</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-6-point-3">Fee Reminders</span></li></ul>
                </div>
                <div class="feature-card bg-gradient-to-br from-white to-emerald-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-money-bill-wave text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-7-title">Financial System</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-7-desc">Complete financial tracking for your school.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-7-point-1">Expenditure Management</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-7-point-2">Bills Payment (School Fees)</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-7-point-3">Payroll Management</span></li></ul>
                </div>
                <div class="feature-card bg-gradient-to-br from-white to-amber-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-qrcode text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-8-title">Gate Pass Verification</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-8-desc">Secure Paid students entry management.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-8-point-1">Token Verification</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-8-point-2">Real-time Logs</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-8-point-3">Security Reports</span></li></ul>
                </div>
                <div class="feature-card bg-gradient-to-br from-white to-cyan-50 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 transition-all duration-500">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6"><i class="fas fa-passport text-white text-xl sm:text-2xl"></i></div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-gray-800" id="feature-9-title">e-Permit System</h3>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base" id="feature-9-desc">Digital permits for students leaving school premises.</p>
                    <ul class="space-y-1 sm:space-y-2"><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-9-point-1">Real-time Approval</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-9-point-2">Real-time Tracking</span></li><li><i class="fas fa-check-circle text-green-500 mr-2"></i><span id="feature-9-point-3">Permit History</span></li></ul>
                </div>
            </div>

            <div class="gradient-bg rounded-2xl sm:rounded-3xl p-8 sm:p-10 text-center text-white shadow-2xl mt-8 sm:mt-12">
                <h3 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6" id="cta-title">Ready to Transform Your School?</h3>
                <p class="text-lg sm:text-xl mb-6 sm:mb-8 max-w-2xl mx-auto" id="cta-description">Be among the first schools to use ShuleApp and simplify your daily management</p>
                <a href="{{ route('login') }}" class="inline-block bg-white text-blue-700 px-8 sm:px-10 py-3 sm:py-4 rounded-full text-lg sm:text-xl font-bold hover:shadow-2xl transition-all duration-300 transform hover:scale-105"><i class="fas fa-play mr-3"></i><span id="cta-button">Start Free Trial</span></a>
                <p class="mt-4 sm:mt-6 text-blue-100 text-sm sm:text-base"><i class="fas fa-clock mr-2"></i><span id="cta-note">Registration takes few minutes</span></p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-16 sm:py-20 bg-gradient-to-br from-gray-900 to-blue-900 text-white">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-12 sm:mb-16" id="stats-title-growth">ShuleApp Growth Statistics</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 mb-12 sm:mb-16">
                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl"><div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-blue-600 counter-number" id="stat1">0</div><div class="text-lg sm:text-xl font-semibold mb-2 text-gray-700" id="stats-label-1">School</div><p class="text-gray-600 text-sm sm:text-base" id="stats-desc-1">Currently using our system</p><div class="mt-3"><i class="fas fa-school text-3xl text-blue-500"></i></div></div>
                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl"><div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-green-600">1,500+</div><div class="text-lg sm:text-xl font-semibold mb-2 text-gray-700" id="stats-label-2">Students</div><p class="text-gray-600 text-sm sm:text-base" id="stats-desc-2">Registered in system</p><div class="mt-3"><i class="fas fa-user-graduate text-3xl text-green-500"></i></div></div>
                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl"><div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-purple-600">100+</div><div class="text-lg sm:text-xl font-semibold mb-2 text-gray-700" id="stats-label-3">Teachers</div><p class="text-gray-600 text-sm sm:text-base" id="stats-desc-3">Using the platform</p><div class="mt-3"><i class="fas fa-chalkboard-teacher text-3xl text-purple-500"></i></div></div>
                <div class="counter-box p-6 sm:p-8 rounded-xl sm:rounded-2xl"><div class="text-4xl sm:text-5xl font-bold mb-3 sm:mb-4 text-red-600">1,500+</div><div class="text-lg sm:text-xl font-semibold mb-2 text-gray-700" id="stats-label-4">Parents</div><p class="text-gray-600 text-sm sm:text-base" id="stats-desc-4">Connected to portal</p><div class="mt-3"><i class="fas fa-user-friends text-3xl text-red-500"></i></div></div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl sm:rounded-2xl p-6 sm:p-8 max-w-4xl mx-auto"><h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6" id="why-title">Why Choose ShuleApp?</h3><div class="grid md:grid-cols-3 gap-4 sm:gap-6"><div class="p-4 bg-white/5 rounded-xl"><i class="fas fa-bolt text-3xl text-yellow-400 mb-3"></i><h4 class="font-bold text-lg mb-2" id="why-1-title">Fast & Easy</h4><p class="text-blue-100 text-sm" id="why-1-desc">Start using within few hours</p></div><div class="p-4 bg-white/5 rounded-xl"><i class="fas fa-shield-alt text-3xl text-green-400 mb-3"></i><h4 class="font-bold text-lg mb-2" id="why-2-title">100% Secure</h4><p class="text-blue-100 text-sm" id="why-2-desc">Your data stored with highest standards</p></div><div class="p-4 bg-white/5 rounded-xl"><i class="fas fa-headset text-3xl text-purple-400 mb-3"></i><h4 class="font-bold text-lg mb-2" id="why-3-title">24/7 Support</h4><p class="text-blue-100 text-sm" id="why-3-desc">Our team ready to help anytime</p></div></div></div>
        </div>
    </section>

    <!-- Contact Section - ORIGINAL STYLING RESTORED -->
    <section id="contact" class="py-16 sm:py-20 bg-gradient-to-b from-white to-blue-50">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12 sm:mb-16"><div class="inline-block gradient-bg text-white px-4 sm:px-6 py-2 rounded-full font-semibold mb-3 sm:mb-4"><i class="fas fa-comments mr-2"></i><span id="contact-tagline">Contact Us</span></div><h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4 sm:mb-6 text-gray-800" id="contact-title">Get In Touch</h2><p class="text-lg sm:text-xl text-gray-600" id="contact-description">We're happy to listen and answer any questions you have</p></div>
                <div class="grid lg:grid-cols-2 gap-8 sm:gap-12">
                    <div class="bg-white rounded-xl sm:rounded-3xl shadow-xl sm:shadow-2xl p-6 sm:p-8 md:p-10"><h3 class="text-xl sm:text-2xl font-bold mb-6 sm:mb-8 text-gray-800" id="form-title">Send Your Message</h3><form class="space-y-4 sm:space-y-6"><div class="grid md:grid-cols-2 gap-4 sm:gap-6"><div><label class="block text-gray-700 mb-2 font-medium" id="form-label-name">Full Name</label><input type="text" placeholder="Your name" class="w-full border border-gray-300 p-3 sm:p-4 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"></div><div><label class="block text-gray-700 mb-2 font-medium" id="form-label-phone">Phone Number</label><input type="text" placeholder="Phone number" class="w-full border border-gray-300 p-3 sm:p-4 rounded-xl focus:ring-2 focus:ring-blue-500 transition"></div></div><div><label class="block text-gray-700 mb-2 font-medium" id="form-label-message">Your Message</label><textarea placeholder="Write your message here..." rows="4" class="w-full border border-gray-300 p-3 sm:p-4 rounded-xl focus:ring-2 focus:ring-blue-500 transition"></textarea></div><button class="gradient-bg w-full text-white py-3 sm:py-4 rounded-xl text-base sm:text-lg font-semibold hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"><i class="fas fa-paper-plane mr-2 sm:mr-3"></i><span id="form-button">Send Message</span></button></form></div>
                    <div class="space-y-6 sm:space-y-8"><div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl sm:rounded-3xl p-6 sm:p-8 md:p-10 text-white shadow-xl sm:shadow-2xl"><h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6" id="contact-info-title">Quick Contacts</h3><div class="space-y-4 sm:space-y-6"><div class="flex items-start"><div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center mr-3 sm:mr-4"><i class="fas fa-phone text-lg sm:text-xl"></i></div><div><h4 class="font-bold text-base sm:text-lg" id="contact-phone-label">Support Phone</h4><a href="tel:+255678669000" class="text-xl sm:text-2xl font-bold hover:text-yellow-300 transition block mt-1">+255 678 669 000</a><p class="text-blue-100 mt-1 text-sm sm:text-base" id="contact-phone-note">Monday - Sunday, 24 Hours</p></div></div><div class="flex items-start"><div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center mr-3 sm:mr-4"><i class="fas fa-envelope text-lg sm:text-xl"></i></div><div><h4 class="font-bold text-base sm:text-lg" id="contact-email-label">Email Address</h4><a href="mailto:pianop477@gmail.com" class="text-lg sm:text-xl hover:text-yellow-300 transition block mt-1">pianop477@gmail.com</a><p class="text-blue-100 mt-1 text-sm sm:text-base" id="contact-email-note">We reply within 24 hours</p></div></div><div class="flex items-start"><div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center mr-3 sm:mr-4"><i class="fas fa-map-marker-alt text-lg sm:text-xl"></i></div><div><h4 class="font-bold text-base sm:text-lg" id="contact-location-label">Location</h4><p class="text-lg sm:text-xl">Dodoma, Tanzania</p></div></div></div></div><div class="bg-white rounded-xl sm:rounded-3xl shadow-xl p-6 sm:p-8"><h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-gray-800" id="faq-title">Frequently Asked Questions</h3><div class="space-y-3 sm:space-y-4"><div class="border-l-4 border-blue-500 pl-3 sm:pl-4 py-1 sm:py-2"><h4 class="font-bold text-gray-800 text-sm sm:text-base" id="faq-1-q">Can I try before paying?</h4><p class="text-gray-600 mt-1 text-xs sm:text-sm" id="faq-1-a">Yes! We offer 30-day free trial with no charges.</p></div><div class="border-l-4 border-green-500 pl-3 sm:pl-4 py-1 sm:py-2"><h4 class="font-bold text-gray-800 text-sm sm:text-base" id="faq-2-q">Does system work online only?</h4><p class="text-gray-600 mt-1 text-xs sm:text-sm" id="faq-2-a">Yes We offer online service options based on our system needs.</p></div><div class="border-l-4 border-purple-500 pl-3 sm:pl-4 py-1 sm:py-2"><h4 class="font-bold text-gray-800 text-sm sm:text-base" id="faq-3-q">There is internal training once I join with ShuleApp</h4><p class="text-gray-600 mt-1 text-xs sm:text-sm" id="faq-3-a">Yes, The team will provide internal training as much as you will be satisfied</p></div></div></div></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer - ORIGINAL STYLING RESTORED -->
    <footer class="bg-gray-900 text-white py-8 sm:py-12">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-6 sm:gap-8 md:gap-10 mb-8 sm:mb-10">
                <div class="md:col-span-2 lg:col-span-1"><div class="flex items-center space-x-3 mb-4 sm:mb-6"><div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl overflow-hidden"><img src="{{ asset('storage/logo/new_logo.png') }}" alt="ShuleApp Logo" class="w-full h-full object-contain"></div><div><div class="text-xl sm:text-2xl font-bold">ShuleApp</div></div></div><p class="text-gray-400 text-sm sm:text-base" id="footer-description">We help schools have better management, accuracy and modern system at affordable cost.</p></div>
                <div><h4 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4" id="footer-links-title">Important Links</h4><ul class="space-y-2"><li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition text-sm sm:text-base block py-1" id="footer-link-login">Login</a></li><li><a href="{{ route('contract.gateway.init') }}" class="text-gray-400 hover:text-white transition text-sm sm:text-base block py-1" id="footer-link-contract">Contracts Gateway</a></li><li><a href="{{ route('tokens.verify') }}" class="text-gray-400 hover:text-white transition text-sm sm:text-base block py-1" id="footer-link-token">Gate Pass Verifier</a></li><li><a href="{{ route('parent.e-permit.student-form') }}" class="text-gray-400 hover:text-white transition text-sm sm:text-base block py-1" id="footer-link-pass">e-Permit System</a></li></ul></div>
                <div><h4 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4" id="footer-services-title">Our Services</h4><ul class="space-y-2"><li class="text-gray-400 text-sm sm:text-base" id="footer-service-1">Academic Management</li><li class="text-gray-400 text-sm sm:text-base" id="footer-service-2">Financial Management</li><li class="text-gray-400 text-sm sm:text-base" id="footer-service-3">SMS Messaging</li><li class="text-gray-400 text-sm sm:text-base" id="footer-service-4">Parent Portal</li></ul></div>
                <div><h4 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4" id="footer-follow-title">Follow Us</h4><div class="flex space-x-3 sm:space-x-4 mb-4 sm:mb-6"><a href="#" class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition"><i class="fab fa-facebook-f"></i></a><a href="#" class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-400 transition"><i class="fab fa-twitter"></i></a><a href="#" class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-pink-600 transition"><i class="fab fa-instagram"></i></a><a href="#" class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-700 transition"><i class="fab fa-linkedin-in"></i></a></div><p class="text-gray-400 text-sm sm:text-base mb-2" id="footer-whatsapp-text">Chat on WhatsApp</p><a href="https://wa.me/255678669000" class="inline-flex items-center text-green-400 hover:text-green-300 mt-1" target="_blank"><i class="fab fa-whatsapp text-xl sm:text-2xl mr-2 sm:mr-3"></i><span class="text-sm sm:text-base">+255 678 669 000</span></a></div>
            </div>
            <div class="border-t border-gray-800 pt-6 sm:pt-8 text-center"><p class="text-gray-400 text-xs sm:text-sm md:text-base">© 2025 ShuleApp. All Rights Reserved</p></div>
        </div>
    </footer>

    @include('sweetalert::alert')
    <script>
        // Language translations (full EN + SW)
        const translations = {
            en: {
                'nav-home':'Home','nav-features':'Features','nav-stats':'Stats','nav-contact':'Contact','nav-login':'Login Now','mobile-nav-home':'Home','mobile-nav-features':'Features','mobile-nav-stats':'Stats','mobile-nav-contact':'Contact','mobile-nav-login':'Login Now',
                'hero-title-1':'Transform ','hero-title-2':'Your School Management','hero-description':'Ultimate solution to run your school efficiently and with top-level security.',
                'time-label-hours':'Hours','time-label-minutes':'Minutes','time-label-seconds':'Seconds','current-time-label':'Current Time (EAT)',
                'hero-button-1':'Login now','hero-button-2':'View Features','hero-benefit-1':'No Setup Costs','hero-benefit-2':'Free Training',
                'stats-title-schools':'Schools Using ShuleApp','stats-subtitle-schools':'Currently using and growing daily','stats-label-students':'Students','stats-sub-students':'Registered','stats-label-teachers':'Teachers','stats-sub-teachers':'Using System','stats-label-parents':'Parents','stats-sub-parents':'Connected',
                'features-tagline':'Complete System Modules','features-title':'All ShuleApp Modules','features-description':'9 core modules with 20+ sub-modules to run your school perfectly',
                'feature-1-title':'Users Management','feature-1-desc':'Complete role-based access.','feature-1-point-1':'Teachers','feature-1-point-2':'Students','feature-1-point-3':'Parents','feature-1-point-4':'Accountant','feature-1-point-5':'Non-teaching Staff',
                'feature-2-title':'Academic Management','feature-2-desc':'Full control over classes and results.','feature-2-point-1':'Class Management','feature-2-point-2':'Course Management','feature-2-point-3':'Assessment Management','feature-2-point-4':'Results Management',
                'feature-3-title':'Reports & Analytics','feature-3-desc':'Comprehensive reports.','feature-3-point-1':'Attendance Report','feature-3-point-2':'Daily School Report','feature-3-point-3':'Holiday Package','feature-3-point-4':'Graduates',
                'feature-4-title':'Contract System','feature-4-desc':'Digital contract management.','feature-4-point-1':'Employee Contracts','feature-4-point-2':'Contract Renewals','feature-4-point-3':'Expiry Notifications',
                'feature-5-title':'Duty Roster System','feature-5-desc':'Manage staff duty schedules.','feature-5-point-1':'Shift Scheduling','feature-5-point-2':'Daily Assignments','feature-5-point-3':'Supervision Planning',
                'feature-6-title':'Bulk SMS Messaging','feature-6-desc':'Send instant announcements.','feature-6-point-1':'Parent Announcements','feature-6-point-2':'Emergency Alerts','feature-6-point-3':'Fee Reminders',
                'feature-7-title':'Financial System','feature-7-desc':'Complete financial tracking.','feature-7-point-1':'Expenditure Management','feature-7-point-2':'Bills Payment (School Fees)','feature-7-point-3':'Payroll Management',
                'feature-8-title':'Gate Pass Verification','feature-8-desc':'Secure Paid Students entry management.','feature-8-point-1':'Token Verification','feature-8-point-2':'Real-time Logs','feature-8-point-3':'Security Reports',
                'feature-9-title':'e-Permit System','feature-9-desc':'Digital permits for students.','feature-9-point-1':'Real-time Approval','feature-9-point-2':'Real-time Tracking','feature-9-point-3':'Permit History',
                'cta-title':'Ready to Transform Your School?','cta-description':'Be among the first schools to use ShuleApp','cta-button':'Start Free Trial','cta-note':'Registration takes few minutes',
                'stats-title-growth':'ShuleApp Growth Statistics','stats-label-1':'School','stats-desc-1':'Currently using','stats-label-2':'Students','stats-desc-2':'Registered','stats-label-3':'Teachers','stats-desc-3':'Using platform','stats-label-4':'Parents','stats-desc-4':'Connected',
                'why-title':'Why Choose ShuleApp?','why-1-title':'Fast & Easy','why-1-desc':'Start using within few hours','why-2-title':'100% Secure','why-2-desc':'Your data stored with highest standards','why-3-title':'24/7 Support','why-3-desc':'Our team ready to help anytime',
                'contact-tagline':'Contact Us','contact-title':'Get In Touch','contact-description':'We\'re happy to listen and answer any questions you have','form-title':'Send Your Message','form-label-name':'Full Name','form-label-phone':'Phone Number','form-label-message':'Your Message','form-button':'Send Message',
                'contact-info-title':'Quick Contacts','contact-phone-label':'Support Phone','contact-phone-note':'Monday - Sunday, 24 Hours','contact-email-label':'Email Address','contact-email-note':'We reply within 24 hours','contact-location-label':'Location',
                'faq-title':'Frequently Asked Questions','faq-1-q':'Can I try before paying?','faq-1-a':'Yes! We offer 30-day free trial with no charges.','faq-2-q':'Does system work online only?','faq-2-a':'Yes We offer online service options based on our system needs.','faq-3-q':'There is internal training once I join with ShuleApp?','faq-3-a':'Yes, The team will provide internal training as much as you will be satisfied',
                'footer-description':'We help schools have better management, accuracy and modern system at affordable cost.','footer-links-title':'Important Links','footer-link-login':'Login','footer-link-contract':'Contracts Gateway','footer-link-token':'Gate Pass Verifier','footer-link-pass':'e-Permit System','footer-services-title':'Our Services','footer-service-1':'Academic Management','footer-service-2':'Financial Management','footer-service-3':'SMS Messaging','footer-service-4':'Parent Portal','footer-follow-title':'Follow Us','footer-whatsapp-text':'Chat on WhatsApp'
            },
            sw: {
                'nav-home':'Nyumbani','nav-features':'Vipengele','nav-stats':'Takwimu','nav-contact':'Wasiliana','nav-login':'Ingia Sasa',
                'hero-title-1':'Badilisha ','hero-title-2':'Usimamizi wa Shule','hero-description':'Suluhisho kamili la kuendesha shule yako kwa ufanisi na usalama wa hali ya juu.',
                'time-label-hours':'Masaa','time-label-minutes':'Dakika','time-label-seconds':'Sekunde','current-time-label':'Sasa Hivi (EAT)',
                'hero-button-1':'Ingia Sasa','hero-button-2':'Ona Vipengele','hero-benefit-1':'Hakuna Gharama za Kuanzisha','hero-benefit-2':'Mafunzo Bure',
                'stats-title-schools':'Shule Zinazotumia ShuleApp','stats-subtitle-schools':'Zinatumia sasa na zinakua','stats-label-students':'Wanafunzi','stats-sub-students':'Waliosajiliwa','stats-label-teachers':'Walimu','stats-sub-teachers':'Wanatumia Mfumo','stats-label-parents':'Wazazi','stats-sub-parents':'Wameunganishwa',
                'features-tagline':'Moduli Kamili za Mfumo','features-title':'Moduli Zote za ShuleApp','features-description':'Moduli 9 na zaidi ya 20 ndogo kwa uendeshaji mzuri',
                'feature-1-title':'Usimamizi wa Watumiaji','feature-1-desc':'Udhibiti kamili kwa majukumu.','feature-1-point-1':'Walimu','feature-1-point-2':'Wanafunzi','feature-1-point-3':'Wazazi','feature-1-point-4':'Mhasibu','feature-1-point-5':'Wafanyakazi Wasio Walimu',
                'feature-2-title':'Usimamizi wa Masomo','feature-2-desc':'Udhibiti kamili wa masomo na matokeo.','feature-2-point-1':'Usimamizi wa Madarasa','feature-2-point-2':'Usimamizi wa Kozi','feature-2-point-3':'Usimamizi wa Tathmini','feature-2-point-4':'Usimamizi wa Matokeo',
                'feature-3-title':'Ripoti na Takwimu','feature-3-desc':'Ripoti za kina.','feature-3-point-1':'Ripoti ya Mahudhurio','feature-3-point-2':'Ripoti ya Kila Siku','feature-3-point-3':'Vifurushi vya Likizo','feature-3-point-4':'Wahitimu',
                'feature-4-title':'Mfumo wa Mikataba','feature-4-desc':'Usimamizi wa mikataba ya kidijitali.','feature-4-point-1':'Mikataba ya Wafanyakazi','feature-4-point-2':'Omba Mkataba Mpya','feature-4-point-3':'Taarifa za Kuisha kwa Mkataba',
                'feature-5-title':'Mfumo wa Ratiba za Kazi','feature-5-desc':'Dhibiti ratiba za wafanyakazi.','feature-5-point-1':'Ratiba za Zamu','feature-5-point-2':'Kazi za Kila Siku','feature-5-point-3':'Mipango ya Usimamizi',
                'feature-6-title':'Ujumbe Mfupi kwa Wingi','feature-6-desc':'Tuma matangazo ya papo kwa papo.','feature-6-point-1':'Matangazo kwa Wazazi','feature-6-point-2':'Taarifa za Dharula','feature-6-point-3':'Kumbusha Ada kwa Mara moja',
                'feature-7-title':'Mfumo wa Fedha','feature-7-desc':'Ufuatiliaji kamili wa fedha.','feature-7-point-1':'Usimamizi wa Matumizi','feature-7-point-2':'Malipo ya Ada za Shule','feature-7-point-3':'Usimamizi wa Mishahara',
                'feature-8-title':'Uhakiki wa Geti Pass','feature-8-desc':'Usimamizi salama wa kuingia kwa waliolipa Ada.','feature-8-point-1':'Uhakiki wa Token','feature-8-point-2':'Kumbukumbu za Wakati Halisi','feature-8-point-3':'Ripoti za Usalama',
                'feature-9-title':'Mfumo wa Kibali (e-Permit)','feature-9-desc':'Vibali vya kidijitali kwa wanafunzi.','feature-9-point-1':'Uidhinishwaji wa papo kwa papo','feature-9-point-2':'Ufuatiliaji wa Wakati Halisi','feature-9-point-3':'Historia ya Vibali',
                'cta-title':'Tayari Kubadilisha Shule Yako?','cta-description':'Jiunge na shule za kwanza kutumia ShuleApp','cta-button':'Anza Bila Malipo','cta-note':'Usajili huchukua dakika chache',
                'stats-title-growth':'Takwimu za Ukuaji wa ShuleApp','stats-label-1':'Shule','stats-desc-1':'Inatumia sasa','stats-label-2':'Wanafunzi','stats-desc-2':'Waliosajiliwa','stats-label-3':'Walimu','stats-desc-3':'Wanatumia mfumo','stats-label-4':'Wazazi','stats-desc-4':'Wameunganishwa',
                'why-title':'Kwa Nini Kuchagua ShuleApp?','why-1-title':'Haraka na Rahisi','why-1-desc':'Anza kuitumia ndani ya masaa machache','why-2-title':'Salama Kabisa','why-2-desc':'Data yako imehifadhiwa kwa viwango vya juu','why-3-title':'Msaada 24/7','why-3-desc':'Timu yetu iko tayari kukusaidia kila wakati',
                'contact-tagline':'Wasiliana Nasi','contact-title':'Tuongee','contact-description':'Tuna furaha kukusikiliza na kukujibu maswali yako yoyote','form-title':'Tuma Ujumbe Wako','form-label-name':'Jina Kamili','form-label-phone':'Namba ya Simu','form-label-message':'Ujumbe Wako','form-button':'Tuma Ujumbe',
                'contact-info-title':'Mawasiliano ya Haraka','contact-phone-label':'Simu ya Msaada','contact-phone-note':'Juma zima, Masaa 24','contact-email-label':'Barua Pepe','contact-email-note':'Tunajibu ndani ya masaa 24','contact-location-label':'Eneo',
                'faq-title':'Maswali Yanayoulizwa Mara Kwa Mara','faq-1-q':'Je, naweza kujaribu kabla ya kulipia?','faq-1-a':'Ndio! Tunatoa kipindi cha majaribio cha siku 30 bila malipo.','faq-2-q':'Mfumo unafanya kazi mtandaoni tu?','faq-2-a':'Tunatoa chaguo la mtandaoni pekee kulingana na mahitaji ya kimfumo.','faq-3-q':'Je, mnatoa mafunzo kwa watumiaji wapya wanapojiunga?','faq-3-a':'Ndio, timu ipo tayari kutoa mafunzo kwa watumiaji wapya.',
                'footer-description':'Tunasaidia shule kuwa na usimamizi bora, usahihi na wa kisasa kwa gharama nafuu.','footer-links-title':'Viungo Muhimu','footer-link-login':'Ingia','footer-link-contract':'Dirisha la Mikataba','footer-link-token':'Kithibitisho cha Geti Pass','footer-link-pass':'Mfumo wa Kibali (e-Permit)','footer-services-title':'Huduma Zetu','footer-service-1':'Usimamizi wa Masomo','footer-service-2':'Usimamizi wa Fedha','footer-service-3':'Ujumbe wa SMS','footer-service-4':'Portal ya Wazazi','footer-follow-title':'Tufuate','footer-whatsapp-text':'Jiunge Nasi WhatsApp'
            }
        };
        let currentLang = 'en';
        function changeLanguage(lang) { currentLang = lang; document.getElementById('language-selector').value = lang; Object.keys(translations[lang]).forEach(key => { let el = document.getElementById(key); if(el) el.textContent = translations[lang][key]; }); localStorage.setItem('shuleapp_lang', lang); }
        document.getElementById('language-selector').addEventListener('change', e => changeLanguage(e.target.value));
        let savedLang = localStorage.getItem('shuleapp_lang') || 'en'; changeLanguage(savedLang);
        document.getElementById('menu-toggle').addEventListener('click', function(){ document.getElementById('mobile-menu').classList.toggle('hidden'); });
        document.querySelectorAll('#mobile-menu a').forEach(link => { link.addEventListener('click', () => { document.getElementById('mobile-menu').classList.add('hidden'); }); });
        function updateLiveTime(){ let d=new Date(); let h=d.getHours().toString().padStart(2,'0'); let m=d.getMinutes().toString().padStart(2,'0'); let s=d.getSeconds().toString().padStart(2,'0'); document.getElementById('live-time').textContent=`${h}:${m}:${s}`; document.getElementById('current-hours').textContent=h; document.getElementById('current-minutes').textContent=m; document.getElementById('current-seconds').textContent=s; }
        setInterval(updateLiveTime,1000); updateLiveTime();
        function animateCounter(id,val){ let el=document.getElementById(id); if(!el) return; let start=0, inc=val/60, timer=setInterval(()=>{ start+=inc; if(start>=val){ el.textContent=val; clearInterval(timer); }else el.textContent=Math.floor(start); },20); }
        let statsObs = new IntersectionObserver((e)=>{ e.forEach(entry=>{ if(entry.isIntersecting){ animateCounter('stat1',3); statsObs.unobserve(entry.target); } }); },{threshold:0.3});
        let statsSec = document.getElementById('stats'); if(statsSec) statsObs.observe(statsSec);
        ScrollReveal().reveal('.feature-card',{delay:100,interval:100,origin:'bottom',distance:'30px'});
        ScrollReveal().reveal('#home .typing-container',{delay:300,duration:1500,distance:'0px',opacity:0,scale:0.9});
        setTimeout(() => { const typingEl = document.querySelector('.typing-text'); if(typingEl){ const phrases = currentLang === 'en' ? ['Your School Management','Academic Performance','Financial Tracking','Attendance Monitoring','Contracts Management'] : ['Usimamizi wa Shule','Utendaji wa Masomo','Ufuatiliaji wa Fedha','Mahudhurio','Usimamizi wa Mikataba']; let idx=0, charIdx=0, del=false; function type(){ let cur=phrases[idx]; if(del){ typingEl.textContent=cur.substring(0,charIdx-1); charIdx--; }else{ typingEl.textContent=cur.substring(0,charIdx+1); charIdx++; } if(!del && charIdx===cur.length){ del=true; setTimeout(type,1500); }else if(del && charIdx===0){ del=false; idx=(idx+1)%phrases.length; setTimeout(type,500); }else{ setTimeout(type,del?50:100); } } setTimeout(type,1000); } },500);
    </script>
</body>
</html>
