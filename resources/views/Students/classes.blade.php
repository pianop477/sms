@extends('SRTDashboard.frame')
@section('content')
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .header-title {
            font-weight: 700;
            color: #3b3b79;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        .list-group-item {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .list-group-item:hover {
            background-color: #f1f5fd;
            border-left: 4px solid #4e73df;
            transform: translateX(5px);
        }
        .badge {
            font-size: 0.9rem;
            padding: 0.5em 0.8em;
        }
        .search-form {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .student-info-card {
            display: none;
            background: linear-gradient(120deg, #e3f2fd, #f3e5f5);
            border-radius: 10px;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .student-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .info-label {
            font-weight: 600;
            color: #4e73df;
        }
        .search-btn {
            background: #4e73df;
            border: none;
            transition: all 0.3s;
        }
        .search-btn:hover {
            background: #2e59d9;
            transform: translateY(-2px);
        }
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4e73df;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .search-results {
            max-height: 300px;
            overflow-y: auto;
            position: absolute;
            width: 80%;
            z-index: 1000;
            background: white;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: none;
        }
        .search-result-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .search-result-item:hover {
            background-color: #f8f9fa;
        }
        .no-results {
            padding: 10px 15px;
            color: #6c757d;
            font-style: italic;
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
