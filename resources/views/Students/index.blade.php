@extends('SRTDashboard.frame')
@section('content')
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 class="header-title text-uppercase">{{$classId->class_name}} Students list - ({{$classId->class_code}})</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    @if ($students->isNotEmpty())
                                    <button type="button" class="btn btn-info btn-xs mr-1" data-bs-toggle="modal" data-bs-target="#promoteModal">
                                        <i class="fas fa-exchange-alt me-1"></i> Promote
                                    </button>
                                    <a href="{{route('export.student.pdf', ['class' => Hashids::encode($classId->id)])}}" target="_blank" class="btn btn-primary btn-xs mr-1">
                                        <i class="fas fa-cloud-arrow-down me-1"></i> Export
                                    </a>
                                    @endif
                                    <a href="{{route('classes.list', ['class' => Hashids::encode($classId->id)])}}" class="btn btn-secondary btn-xs mr-1">
                                        <i class="fas fa-arrow-circle-left me-1"></i> Back
                                    </a>
                                    <button type="button" class="btn btn-success btn-xs mr-1" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                        <i class="fas fa-plus-circle me-1"></i> New
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Student Info Summary -->
                        <div class="student-info-card">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users fa-2x me-3"></i>
                                        <div>
                                            <h6 class="mb-0"> Total Students</h6>
                                            <h3 class="mb-0"> {{ $students->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-male fa-2x me-3"></i>
                                        <div>
                                            <h6 class="mb-0"> Boys</h6>
                                            <h3 class="mb-0"> {{ $students->filter(fn($s) => strtolower($s->gender) === 'male')->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-female fa-2x me-3"></i>
                                        <div>
                                            <h6 class="mb-0"> Girls</h6>
                                            <h3 class="mb-0"> {{ $students->filter(fn($s)=> strtolower($s->gender) === 'female')->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Batch Update Form -->
                        <form action="{{ route('students.batchUpdateStream') }}" novalidate class="needs-validation mb-4" method="POST">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Transfer Student Stream</label>
                                    <select name="new_stream" class="form-select text-capitalize" required>
                                        <option value="">-- Select Stream --</option>
                                        <option value="A">Stream A</option>
                                        <option value="B">Stream B</option>
                                        <option value="C">Stream C</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-warning btn-xs text-capitalize" onclick="return confirm('Are you sure you want to move selected students to a new stream?')">
                                        <i class="fas fa-random me-1"></i> Shift Stream
                                    </button>
                                </div>
                            </div>

                            <!-- Students Table -->
                            <div class="table-responsive mt-4">
                                <table class="table table-hover progress-table table-responsive-md" id="myTable">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center"><input type="checkbox" id="selectAll"> All</th>
                                            <th scope="col" class="text-center">Adm #</th>
                                            <th scope="col">Student</th>
                                            <th scope="col">Middle Name</th>
                                            <th scope="col">Surname</th>
                                            <th scope="col" class="text-center">Gender</th>
                                            <th scope="col" class="text-center">Stream</th>
                                            <th scope="col">Date of Birth</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $student)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="student[]" value="{{$student->id}}">
                                            </td>
                                            <td class="text-uppercase text-center fw-bold">{{$student->admission_number}}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $imageName = $student->image;
                                                        $imagePath = public_path('assets/img/students/' . $imageName);

                                                        if (!empty($imageName) && file_exists($imagePath)) {
                                                            $avatarImage = asset('assets/img/students/' . $imageName);
                                                        } else {
                                                            $avatarImage = asset('assets/img/students/student.jpg');
                                                        }
                                                    @endphp
                                                    <img src="{{ $avatarImage }}" alt="Student Avatar" class="student-avatar">
                                                    <span class="text-capitalize">{{ucwords(strtolower($student->first_name))}}</span>
                                                </div>
                                            </td>
                                            <td class="text-capitalize">{{ucwords(strtolower($student->middle_name))}}</td>
                                            <td class="text-capitalize">{{ucwords(strtolower($student->last_name))}}</td>
                                            <td class="text-center text-capitalize">
                                                <span class="badge bg-info text-white">{{$student->gender[0]}}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-stream badge-stream-{{$student->group}}">{{$student->group}}</span>
                                            </td>
                                            <td>{{\Carbon\Carbon::parse($student->dob)->format('M d, Y')}}</td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{route('students.modify', ['students' => Hashids::encode($student->id)])}}" class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="ti-pencil"></i>
                                                    </a>
                                                    <a href="{{route('manage.student.profile', ['student' => Hashids::encode($student->id)])}}" class="btn btn-sm btn-info" title="View">
                                                        <i class="ti-eye"></i>
                                                    </a>
                                                    <form action="{{route('Students.destroy', ['student' => Hashids::encode($student->id)])}}" method="POST">
                                                        @csrf
                                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to block {{strtoupper($student->first_name)}} {{strtoupper($student->middle_name)}} {{strtoupper($student->last_name)}}?')" title="Delete">
                                                            <i class="ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Promote Students Modal -->
    <div class="modal fade" id="promoteModal" tabindex="-1" aria-labelledby="promoteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" id="promoteModalLabel">Promote Students to the Next Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger mb-3">Select class you want to promote students to</p>
                    <form class="needs-validation" novalidate action="{{route('promote.student.class', ['class' => Hashids::encode($classId->id)])}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="classSelect" class="form-label">Class Name</label>
                            <select name="class_id" id="classSelect" class="form-select text-uppercase" required>
                                <option value="">--Select Class--</option>
                                @if ($classes->isEmpty())
                                    <option value="" class="text-danger">No more classes found</option>
                                    <option value="0" class="text-success fw-bold">🎓 Graduate Class 🎉</option>
                                @else
                                    @foreach ($classes as $class)
                                        <option value="{{$class->id}}">{{$class->class_name}}</option>
                                    @endforeach
                                    <option value="0" class="text-success fw-bold">🎓 Graduate Class 🎉</option>
                                @endif
                            </select>
                        </div>
                        <div class="mb-3" id="graduationYearField" style="display: none;">
                            <label for="graduation_year" class="form-label">Graduation Year</label>
                            <input type="number" name="graduation_year" id="graduation_year" class="form-control" min="{{date('Y') - 5}}" max="{{date('Y')}}" value="{{old('graduation_year')}}">
                            <div class="form-text">Please enter the graduation year</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to promote this class?')">Upgrade</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" id="addStudentModalLabel">{{$classId->class_name}} Student Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('student.store', ['class' => Hashids::encode($classId->id)])}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="fname" name="fname" value="{{old('fname')}}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="middle" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middle" name="middle" value="{{old('middle')}}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="lname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lname" name="lname" value="{{old('lname')}}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">-- select gender --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" value="{{old('dob')}}" required min="{{\Carbon\Carbon::now()->subYears(17)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(3)->format('Y-m-d')}}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="parentSelect" class="form-label">Parent/Guardian</label>
                                <select name="parent" id="parentSelect" class="form-select" required>
                                    <option value="">Select Parent</option>
                                    @if ($parents->isEmpty())
                                        <option value="" disabled class="text-danger">No parents records found</option>
                                    @else
                                        @foreach ($parents as $parent)
                                            <option value="{{$parent->id}}">
                                                {{ucwords(strtoupper($parent->first_name . ' ' . $parent->last_name))}} - {{$parent->phone}}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="group" class="form-label">Class Group</label>
                                <select class="form-select" id="group" name="group" required>
                                    <option value="">--Select Stream--</option>
                                    <option value="a">Stream A</option>
                                    <option value="b">Stream B</option>
                                    <option value="c">Stream C</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="bus" class="form-label">Bus Number</label>
                                <select name="driver" id="bus" class="form-select">
                                    <option value="">-- select bus number --</option>
                                    @if ($buses->isEmpty())
                                        <option value="" disabled class="text-danger">No school bus records found</option>
                                    @else
                                        @foreach ($buses as $bus)
                                            <option value="{{$bus->id}}">Bus No. {{$bus->bus_no}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-text">Select if using School bus</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="image" class="form-label">Photo</label>
                                <input type="file" class="form-control" id="image" name="image">
                                <div class="form-text">Optional</div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Save Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2
            $('#parentSelect').select2({
                placeholder: "Search Parent...",
                allowClear: true,
                dropdownParent: $('#addStudentModal')
            });

            // Select all checkboxes
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="student[]"]');
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            });

            // Show/hide graduation year field
            const classSelect = document.getElementById('classSelect');
            const graduationYearField = document.getElementById('graduationYearField');

            if (classSelect) {
                classSelect.addEventListener('change', function() {
                    if (this.value === '0') {
                        graduationYearField.style.display = 'block';
                    } else {
                        graduationYearField.style.display = 'none';
                    }
                });
            }

            // Form validation and submission handling
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
        });
    </script>
@endsection
