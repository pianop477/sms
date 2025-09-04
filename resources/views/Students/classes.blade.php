@extends('SRTDashboard.frame')
@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --success: #28a745;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px; */
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 5px 10px;
            position: relative;
            overflow: hidden;
        }

        .card-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            transform: rotate(30deg);
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            font-size: 24px;
        }

        .card-body {
            padding: 5px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 1;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .form-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }

        .form-label {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .required-star {
            color: var(--danger);
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .select2-container--default .select2-selection--single {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px;
            height: auto;
            background-color: white;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .flatpickr-input {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            background-color: white;
        }

        .flatpickr-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .invalid-feedback {
            font-weight: 600;
            color: var(--danger);
            margin-top: 5px;
        }

        .text-danger small {
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #3f43b5 0%, #7a80f9 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            color: #856404;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-warning-custom:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-success-custom:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .info-alert {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.15) 0%, rgba(23, 162, 184, 0.25) 100%);
            border: 1px solid rgba(23, 162, 184, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            backdrop-filter: blur(5px);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            /* padding: 10px; */
            font-weight: 600;
            text-align: center;
        }

        .table-custom tbody td {
            /* padding: 10px; */
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: rgba(78, 84, 200, 0.05);
        }

        .table-custom tbody tr:hover {
            background-color: rgba(78, 84, 200, 0.1);
        }

        .score-input {
            width: auto;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            transition: all 0.3s;
        }

        .score-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .grade-input {
            width: 60px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            font-weight: bold;
        }

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        .instruction-text {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.25) 100%);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 193, 7, 0.3);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .card-body {
                padding: 5px;
            }

            .header-title {
                font-size: 20px;
            }

            .table-responsive {
                font-size: 14px;
            }

            .score-input, .grade-input {
                width: 100%;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(78, 84, 200, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0);
            }
        }
    </style>
    <div class="py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body p-4">
                        <h4 class="header-title text-uppercase text-center">Students by Classes</h4>

                        <!-- Search Form -->
                        <div class="search-form mb-4">
                            <h5 class="mb-3"><i class="fas fa-search me-2"></i> Search Student</h5>
                            <form id="searchForm" method="GET">
                                @csrf
                                <div class="input-group" style="position: relative;">
                                    <input type="text" id="searchInput" name="search_query" class="form-control text-uppercase" placeholder="Enter student name or Admission ID#" aria-label="Search student" required>
                                    <button class="btn search-btn text-white" type="submit" disabled>
                                        <i class="fas fa-search me-1"></i> Search
                                        <span class="loading-spinner" id="searchSpinner"></span>
                                    </button>
                                </div>
                                <div class="search-results text-uppercase" id="searchResults"></div>
                            </form>
                        </div>

                        <!-- Student Information Display (Initially Hidden) -->
                        <div id="studentInfo" class="student-info-card p-4 mb-4 text-center">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <img src="https://ui-avatars.com/api/?name=Student+Name&background=4e73df&color=fff&size=120" alt="Student Image" class="student-img mb-3" id="studentImage">
                                </div>
                                <div class="col-md-8 text-md-start">
                                    <h4 class="mb-3">Student Details</h4>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <p class="mb-2 text-capitalize">
                                                <span class="info-label">Name:</span>
                                                <a id="studentProfileLink" href="#">
                                                    <span class="text-dark fw-bold" style="text-decoration: underline" id="infoName">-</span>
                                                </a>
                                            </p>
                                            <p class="mb-2 text-uppercase"><span class="info-label">Class:</span> <span id="infoClass">-</span></p>
                                            <p class="mb-2 text-capitalize"><span class="info-label">Stream:</span> <span id="infoStream">-</span></p>
                                            <p class="mb-2 text-uppercase"><span class="info-label">Admission #:</span> <span id="infoId">-</span></p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="mb-2 text-capitalize"><span class="info-label">Gender:</span> <span id="infoGender">-</span></p>
                                            <p class="mb-2"><span class="info-label">Date of Birth:</span> <span id="infoDob">-</span></p>
                                            <p class="mb-2"><span class="info-label">Parent Phone:</span> <span id="infoPhone">-</span></p>
                                            <p class="mb-2 text-capitalize"><span class="info-label">Driver Name:</span> <span id="inforTrans">-</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($classes->isEmpty())
                        <div class="alert alert-warning text-center">
                            <p>No classes records found. Please register classes first!</p>
                        </div>
                        @else
                        <p class="text-danger p-2"><i class="fas fa-mouse-pointer me-2"></i> Choose class to view students</p>
                        <ul class="list-group">
                            @foreach ($classes as $class)
                            <a href="{{ route('create.selected.class', ['class' => Hashids::encode($class->id)]) }}" class="text-decoration-none">
                                <li class="list-group-item d-flex justify-content-between align-items-center text-uppercase">
                                    <span class="text-primary">>> {{ $class->class_name }}</span>
                                    <span class="badge badge-primary badge-pill">{{ $class->students_count }}</span>
                                </li>
                            </a>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const searchInput = document.getElementById('searchInput');
        const resultsContainer = document.getElementById('searchResults');
        const studentInfo = document.getElementById('studentInfo');
        const $studentId = document.getElementById('studentId');

        searchInput.addEventListener('keyup', function() {
            const query = this.value.trim();
            if (query.length < 2) {
                resultsContainer.style.display = 'none';
                return;
            }

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
                        div.textContent = `${student.name} (${student.admission_number || 'N/A'})`;
                        div.onclick = () => showStudentInfo(student);
                        resultsContainer.appendChild(div);
                    });

                    resultsContainer.style.display = 'block';
                })
                .catch(() => {
                    resultsContainer.innerHTML = `<div class="no-results text-danger">Error fetching data</div>`;
                    resultsContainer.style.display = 'block';
                });
        });

        function showStudentInfo(student) {
            document.getElementById('infoName').textContent = student.name;
            document.getElementById('infoClass').textContent = student.class_name || 'N/A';
            document.getElementById('infoId').textContent = student.admission_number || 'N/A';
            document.getElementById('infoGender').textContent = student.gender || 'N/A';
            document.getElementById('infoDob').textContent = student.dob || 'N/A';
            document.getElementById('infoPhone').textContent = student.phone || 'N/A';
            document.getElementById('studentImage').src = student.image_url;
            document.getElementById('infoStream').textContent = student.group || 'N/A';
            document.getElementById('inforTrans').textContent = student.driver_name || 'N/A';

            // update profile link dynamically
            const profileLink = document.getElementById('studentProfileLink');
            profileLink.href = `{{ url('Manage/Student-profile/id') }}/${student.id}`;

            studentInfo.style.display = 'block';
            resultsContainer.style.display = 'none';
        }

</script>

@endsection
