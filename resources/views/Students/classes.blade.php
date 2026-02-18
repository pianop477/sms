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
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                radial-gradient(circle at 70% 30%, rgba(67, 97, 238, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 30% 70%, rgba(63, 55, 201, 0.1) 0%, transparent 30%);
            animation: rotate 60s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(100px, -100px) scale(1.2); }
            50% { transform: translate(200px, 0) scale(0.8); }
            75% { transform: translate(100px, 100px) scale(1.1); }
        }

        /* Main Container */
        .dashboard-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* Modern Card */
        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        /* Card Header */
        .card-header-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 25px 30px;
            position: relative;
            overflow: hidden;
        }

        .card-header-modern::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 60%);
            /* animation: rotate 20s linear infinite; */
        }

        .card-header-modern::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--warning), var(--success), var(--accent));
        }

        .header-title {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            text-align: center;
        }

        /* Card Body */
        .card-body-modern {
            padding: 30px;
        }

        /* Search Section */
        .search-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.7);
        }

        .search-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        /* Search Input Group */
        .search-group {
            position: relative;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-input-wrapper {
            flex: 1;
            position: relative;
            min-width: 250px;
        }

        .search-input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        .search-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 15px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            white-space: nowrap;
        }

        .search-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
        }

        .search-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Search Results Dropdown */
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 15px;
            margin-top: 5px;
            box-shadow: var(--shadow-lg);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            border: 1px solid #e9ecef;
        }

        .search-results::-webkit-scrollbar {
            width: 6px;
        }

        .search-results::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .search-results::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .search-result-item {
            padding: 12px 18px;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-result-item:hover {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1) 0%, rgba(63, 55, 201, 0.1) 100%);
            transform: translateX(5px);
        }

        .result-avatar {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .result-info {
            flex: 1;
        }

        .result-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .result-meta {
            font-size: 0.75rem;
            color: #6c757d;
            display: flex;
            gap: 10px;
        }

        .no-results {
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Student Info Card */
        .student-info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.7);
            display: none;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .student-avatar {
            width: 140px;
            height: 140px;
            border-radius: 20px;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: var(--shadow-md);
        }

        .info-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: white;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.75rem;
            color: #6c757d;
            margin-bottom: 2px;
        }

        .info-value {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .info-value a {
            color: var(--primary);
            text-decoration: none;
        }

        .info-value a:hover {
            text-decoration: underline;
        }

        /* Class List */
        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .class-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .class-item {
            margin-bottom: 10px;
            animation: fadeInUp 0.5s ease-out;
            animation-fill-mode: both;
        }

        /* Manual animation delays for each class item */
        .class-item:nth-child(1) { animation-delay: 0.1s; }
        .class-item:nth-child(2) { animation-delay: 0.2s; }
        .class-item:nth-child(3) { animation-delay: 0.3s; }
        .class-item:nth-child(4) { animation-delay: 0.4s; }
        .class-item:nth-child(5) { animation-delay: 0.5s; }
        .class-item:nth-child(6) { animation-delay: 0.6s; }
        .class-item:nth-child(7) { animation-delay: 0.7s; }
        .class-item:nth-child(8) { animation-delay: 0.8s; }
        .class-item:nth-child(9) { animation-delay: 0.9s; }
        .class-item:nth-child(10) { animation-delay: 1.0s; }

        .class-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .class-link:hover {
            transform: translateX(10px);
            border-color: var(--primary);
            box-shadow: var(--shadow-md);
        }

        .class-name {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary);
            font-weight: 600;
            font-size: 1rem;
        }

        .class-icon {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        .class-badge {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69b 100%);
            border-radius: 20px;
            border: 2px dashed #ffc107;
        }

        .empty-state i {
            font-size: 50px;
            color: #ffc107;
            margin-bottom: 15px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                margin: 20px auto;
            }

            .card-header-modern {
                padding: 20px;
            }

            .header-title {
                font-size: 1.5rem;
            }

            .card-body-modern {
                padding: 20px;
            }

            .search-group {
                flex-direction: column;
            }

            .search-btn {
                width: 100%;
                justify-content: center;
            }

            .student-avatar {
                width: 100px;
                height: 100px;
                margin-bottom: 15px;
            }

            .info-section {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .search-section {
                padding: 15px;
            }

            .student-info-card {
                padding: 20px;
            }

            .info-item {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1c2c 0%, #2a2d4a 100%);
            }

            .modern-card {
                background: rgba(33, 37, 41, 0.95);
            }

            .search-section {
                background: linear-gradient(135deg, #2b3035 0%, #343a40 100%);
            }

            .search-input {
                background: #2b3035;
                border-color: #495057;
                color: #e9ecef;
            }

            .search-results {
                background: #2b3035;
                border-color: #495057;
            }

            .search-result-item {
                border-bottom-color: #495057;
                color: #e9ecef;
            }

            .search-result-item:hover {
                background: #343a40;
            }

            .result-name {
                color: #e9ecef;
            }

            .student-info-card {
                background: linear-gradient(135deg, #2b3035 0%, #343a40 100%);
            }

            .info-item {
                background: #2b3035;
                border-color: #495057;
            }

            .info-value {
                color: #e9ecef;
            }

            .class-link {
                background: #2b3035;
                border-color: #495057;
            }

            .class-link:hover {
                background: #343a40;
            }
        }
    </style>

    <div class="animated-bg"></div>
    <div class="particles"></div>

    <div class="dashboard-container">
        <div class="modern-card">
            <!-- Header -->
            <div class="card-header-modern">
                <h4 class="header-title">
                    <i class="fas fa-users mr-2"></i>
                    Students by Classes
                </h4>
            </div>

            <!-- Body -->
            <div class="card-body-modern">
                <!-- Search Section -->
                <div class="search-section">
                    <div class="search-title">
                        <i class="fas fa-search"></i>
                        <span>Search Student</span>
                    </div>

                    <form id="searchForm" method="GET">
                        @csrf
                        <div class="search-group">
                            <div class="search-input-wrapper">
                                <input type="text"
                                       id="searchInput"
                                       name="search_query"
                                       class="search-input text-uppercase"
                                       placeholder="Enter name or admission number..."
                                       autocomplete="off"
                                       required>
                                <div class="search-results" id="searchResults"></div>
                            </div>
                            <button class="search-btn" type="submit" disabled>
                                <i class="fas fa-search"></i>
                                <span>Search</span>
                                <span class="loading-spinner" id="searchSpinner"></span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Student Information Card -->
                <div id="studentInfo" class="student-info-card">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <img src="https://ui-avatars.com/api/?name=Student+Name&background=4361ee&color=fff&size=140"
                                 alt="Student"
                                 class="student-avatar mb-3"
                                 id="studentImage">
                        </div>
                        <div class="col-md-8">
                            <h5 class="mb-3">Student Details</h5>
                            <div class="info-section">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Full Name</div>
                                        <div class="info-value">
                                            <a id="studentProfileLink" href="#">
                                                <span id="infoName">-</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Class & Stream</div>
                                        <div class="info-value">
                                            <span id="infoClass">-</span> / <span id="infoStream">-</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Admission Number</div>
                                        <div class="info-value" id="infoId">-</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-venus-mars"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Gender</div>
                                        <div class="info-value" id="infoGender">-</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Date of Birth</div>
                                        <div class="info-value" id="infoDob">-</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-phone-alt"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Parent Phone</div>
                                        <div class="info-value" id="infoPhone">-</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-bus"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Driver Name</div>
                                        <div class="info-value" id="inforTrans">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Classes List -->
                @if ($classes->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-layer-group"></i>
                        <h6>No Classes Found</h6>
                        <p class="text-muted small">Please register classes first</p>
                    </div>
                @else
                    <div class="section-title">
                        <i class="fas fa-mouse-pointer"></i>
                        <span>Choose a class to view students</span>
                    </div>

                    <ul class="class-list">
                        @foreach ($classes as $class)
                            <li class="class-item">
                                <a href="{{ route('create.selected.class', ['class' => Hashids::encode($class->id)]) }}"
                                   class="class-link">
                                    <span class="class-name">
                                        <span class="class-icon">
                                            <i class="fas fa-graduation-cap"></i>
                                        </span>
                                        {{ strtoupper($class->class_name) }}
                                    </span>
                                    <span class="class-badge">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $class->students_count }} Students
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Create floating particles
            createParticles();

            // DOM Elements
            const searchInput = document.getElementById('searchInput');
            const resultsContainer = document.getElementById('searchResults');
            const studentInfo = document.getElementById('studentInfo');
            const searchBtn = document.querySelector('.search-btn');
            const searchSpinner = document.getElementById('searchSpinner');

            // Debounce timer
            let searchTimer;

            // Search input handler
            searchInput.addEventListener('keyup', function() {
                clearTimeout(searchTimer);
                const query = this.value.trim();

                if (query.length < 2) {
                    resultsContainer.style.display = 'none';
                    searchBtn.disabled = true;
                    return;
                }

                searchBtn.disabled = false;

                // Debounce search
                searchTimer = setTimeout(() => performSearch(query), 300);
            });

            // Perform search
            function performSearch(query) {
                // Show loading
                searchSpinner.style.display = 'inline-block';
                searchBtn.disabled = true;

                fetch(`{{ route('api.search.students') }}?search_query=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        resultsContainer.innerHTML = '';

                        if (!data.students || data.students.length === 0) {
                            resultsContainer.innerHTML = `<div class="no-results">No students found</div>`;
                            resultsContainer.style.display = 'block';
                            return;
                        }

                        data.students.forEach(student => {
                            const div = document.createElement('div');
                            div.classList.add('search-result-item');

                            // Create avatar with initials
                            const initials = student.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);

                            div.innerHTML = `
                                <div class="result-avatar">${initials}</div>
                                <div class="result-info">
                                    <div class="result-name">${student.name}</div>
                                    <div class="result-meta">
                                        <span>📋 ${student.admission_number || 'N/A'}</span>
                                        <span>🏫 ${student.class_name || 'N/A'}</span>
                                    </div>
                                </div>
                            `;

                            div.onclick = () => showStudentInfo(student);
                            resultsContainer.appendChild(div);
                        });

                        resultsContainer.style.display = 'block';
                    })
                    .catch(() => {
                        resultsContainer.innerHTML = `<div class="no-results text-danger">Error fetching data</div>`;
                        resultsContainer.style.display = 'block';
                    })
                    .finally(() => {
                        searchSpinner.style.display = 'none';
                        searchBtn.disabled = false;
                    });
            }

            // Show student info
            function showStudentInfo(student) {
                document.getElementById('infoName').textContent = student.name || '-';
                document.getElementById('infoClass').textContent = student.class_name || 'N/A';
                document.getElementById('infoId').textContent = student.admission_number || 'N/A';
                document.getElementById('infoGender').textContent = student.gender || 'N/A';
                document.getElementById('infoDob').textContent = student.dob || 'N/A';
                document.getElementById('infoPhone').textContent = student.phone || 'N/A';
                document.getElementById('infoStream').textContent = student.group || 'N/A';
                document.getElementById('inforTrans').textContent = student.driver_name || 'N/A';

                // Update profile image
                if (student.image_url) {
                    document.getElementById('studentImage').src = student.image_url;
                } else {
                    const initials = student.name ? student.name.split(' ').map(n => n[0]).join('').toUpperCase() : 'ST';
                    document.getElementById('studentImage').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(initials)}&background=4361ee&color=fff&size=140`;
                }

                // Update profile link
                const profileLink = document.getElementById('studentProfileLink');
                profileLink.href = `{{ url('Manage/Student-profile/id') }}/${student.id}`;

                studentInfo.style.display = 'block';
                resultsContainer.style.display = 'none';
                searchInput.value = '';
                searchBtn.disabled = true;
            }

            // Create floating particles
            function createParticles() {
                const particlesContainer = document.querySelector('.particles');
                if (!particlesContainer) return;

                for (let i = 0; i < 20; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.width = Math.random() * 10 + 3 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 20 + 's';
                    particle.style.animationDuration = Math.random() * 10 + 15 + 's';
                    particlesContainer.appendChild(particle);
                }
            }

            // Click outside to close search results
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                    resultsContainer.style.display = 'none';
                }
            });

            // Clear search on escape key
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    resultsContainer.style.display = 'none';
                    this.value = '';
                    searchBtn.disabled = true;
                }
            });
        });
    </script>
@endsection
