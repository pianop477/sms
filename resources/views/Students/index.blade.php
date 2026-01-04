@extends('SRTDashboard.frame')
@section('content')
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6f42c1;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .header-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-table {
            background-color: white;
        }

        .progress-table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .progress-table th {
            padding: 15px 10px;
            font-weight: 600;
        }

        .progress-table td {
            padding: 15px 10px;
            vertical-align: middle;
        }

        .btn-xs {
            padding: 0.35rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.2;
            border-radius: 0.35rem;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-buttons a, .action-buttons button {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid #e3e6f0;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ccc !important;
            border-radius: 4px !important;
            padding: 6px 12px !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .student-info-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .badge-stream {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-stream-A {
            background-color: #e8f0fe;
            color: #1a73e8;
        }

        .badge-stream-B {
            background-color: #e6f4ea;
            color: #0f9d58;
        }

        .badge-stream-C {
            background-color: #fce8e6;
            color: #d93025;
        }

        .form-control:focus, .select2-container--focus .select2-selection {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .table-responsive {
                overflow-x: auto;
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
                                    @if (auth()->user()->usertype != 5)
                                    <button type="button" class="btn btn-info btn-xs mr-1" data-bs-toggle="modal" data-bs-target="#promoteModal">
                                        <i class="fas fa-exchange-alt me-1"></i> Promote
                                    </button>
                                    @endif
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-xs btn-action dropdown-toggle mr-1" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cloud-arrow-down me-1"></i> Export
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                            <li>
                                                <a class="dropdown-item" href="{{route('students.export.excel', ['class' => Hashids::encode($classId->id)])}}">
                                                    <i class="fas fa-file-excel me-1 text-success"></i> Excel
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{route('export.student.pdf', ['class' => Hashids::encode($classId->id)])}}" target="_blank">
                                                    <i class="fas fa-file-pdf me-1 text-danger"></i> PDF
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                    <a href="{{route('classes.list', ['class' => Hashids::encode($classId->id)])}}" class="btn btn-secondary btn-xs mr-1">
                                        <i class="fas fa-arrow-circle-left me-1"></i> Back
                                    </a>
                                    @if (auth()->user()->usertype != 5)
                                    <button type="button" class="btn btn-success btn-xs mr-1" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                        <i class="fas fa-plus-circle me-1"></i> New
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Student Info Summary -->
                        <div class="student-info-card">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users fa-2x me-3 mr-2"></i>
                                        <div>
                                            <h6 class="mb-0"> Total Students</h6>
                                            <h3 class="mb-0"> {{ $students->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-male fa-2x me-3 mr-2"></i>
                                        <div>
                                            <h6 class="mb-0"> Boys</h6>
                                            <h3 class="mb-0"> {{ $students->filter(fn($s) => strtolower($s->gender) === 'male')->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-female fa-2x me-3 mr-2"></i>
                                        <div>
                                            <h6 class="mb-0"> Girls</h6>
                                            <h3 class="mb-0"> {{ $students->filter(fn($s)=> strtolower($s->gender) === 'female')->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Batch Update Form -->
                        <form id="batchForm" action="{{ route('students.batchUpdateStream') }}" method="POST" class="needs-validation mb-4">
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
                                    <button type="submit" class="btn btn-warning btn-xs text-capitalize"
                                        onclick="return confirm('Are you sure you want to move selected students to a new stream?')">
                                        <i class="fas fa-random me-1"></i> Shift Stream
                                    </button>
                                </div>
                            </div>

                            <!-- Checkboxes only — NO delete buttons here -->
                            <div class="mt-3">
                                @foreach ($students as $student)
                                    <input type="checkbox" name="student[]" value="{{ $student->id }}" style="display:none;">
                                @endforeach
                            </div>
                        </form>
                        <div class="table-responsive mt-4">
                                <table class="table table-hover progress-table table-responsive-md" id="myTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><input type="checkbox" id="selectAll"> All</th>
                                            <th class="text-center">Adm #</th>
                                            <th>Student</th>
                                            <th>Middle Name</th>
                                            <th>Surname</th>
                                            <th class="text-center">Gender</th>
                                            <th class="text-center">Stream</th>
                                            <th>Date of Birth</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($students as $student)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" form="batchForm" name="student[]" value="{{ $student->id }}">
                                            </td>
                                            <td class="text-center fw-bold text-uppercase">{{ $student->admission_number }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $imageName = $student->image;
                                                        $imagePath = storage_path('app/public/students/' . $imageName);

                                                        $avatarImage = (!empty($imageName) && file_exists($imagePath))
                                                                        ? asset('storage/students/' . $imageName)
                                                                        : asset('storage/students/student.jpg');
                                                    @endphp

                                                    <img src="{{ $avatarImage }}" class="student-avatar" alt="Student Avatar">
                                                    <span class="text-capitalize">{{ ucwords(strtolower($student->first_name)) }}</span>
                                                </div>
                                            </td>

                                            <td class="text-capitalize">{{ ucwords(strtolower($student->middle_name)) }}</td>
                                            <td class="text-capitalize">{{ ucwords(strtolower($student->last_name)) }}</td>

                                            <td class="text-center">
                                                <span class="badge bg-info text-white">{{ strtoupper($student->gender[0]) }}</span>
                                            </td>

                                            <td class="text-center">
                                                <span class="badge badge-stream badge-stream-{{ strtoupper($student->group) }}">
                                                    {{ strtoupper($student->group) }}
                                                </span>
                                            </td>

                                            <td>{{ \Carbon\Carbon::parse($student->dob)->format('M d, Y') }}</td>

                                            <td class="text-center">
                                                <!-- ========================= -->
                                                <!-- ACTION BUTTONS (DELETE OUTSIDE PARENT FORM) -->
                                                <!-- ========================= -->
                                                <div class="action-buttons">

                                                    <a href="{{ route('students.modify', ['students' => Hashids::encode($student->id)]) }}"
                                                        class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="ti-pencil"></i>
                                                    </a>

                                                    <a href="{{ route('manage.student.profile', ['student' => Hashids::encode($student->id)]) }}"
                                                        class="btn btn-sm btn-info" title="View">
                                                        <i class="ti-eye"></i>
                                                    </a>

                                                    <!-- DELETE FORM OUTSIDE BATCH FORM -->
                                                    @if (auth()->user()->usertype != 5)
                                                        <form method="POST"
                                                        action="{{ route('Students.destroy', ['student' => Hashids::encode($student->id)]) }}"
                                                        style="display:inline;">
                                                        @csrf
                                                        <button class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to delete {{ strtoupper($student->first_name) }} {{ strtoupper($student->middle_name) }} {{ strtoupper($student->last_name) }} permanently?')"
                                                            title="Delete">
                                                            <i class="ti-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                </div>
                                            </td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                        </div>
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
                    <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger mb-3">Select class you want to promote students to</p>
                    <form class="needs-validation" novalidate action="{{route('promote.student.class', ['class' => Hashids::encode($classId->id)])}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="classSelect" class="form-label">Class Name</label>
                            <select name="class_id" id="classSelect" class="form-control-custom text-uppercase" required>
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
                            <input type="number" name="graduation_year" id="graduation_year" placeholder="e.g 2025" class="form-control-custom" min="{{date('Y') - 5}}" max="{{date('Y')}}" value="{{old('graduation_year')}}">
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
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close"> Close</button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('student.store', ['class' => Hashids::encode($classId->id)])}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="text" class="form-control-custom" id="fname" name="fname" value="{{old('fname')}}" required placeholder="First Name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="middle" class="form-label">Middle Name</label>
                                <input type="text" class="form-control-custom" id="middle" name="middle" value="{{old('middle')}}" required placeholder="Middle Name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="lname" class="form-label">Last Name</label>
                                <input type="text" class="form-control-custom" id="lname" name="lname" value="{{old('lname')}}" required placeholder="Last Name">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select form-control-custom" id="gender" name="gender" required>
                                    <option value="">-- select gender --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control-custom" id="dob" name="dob" value="{{old('dob')}}" required min="{{\Carbon\Carbon::now()->subYears(17)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(3)->format('Y-m-d')}}" placeholder="Date of Birth">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="parentSelect" class="form-label">Parent/Guardian</label>
                                <select name="parent" id="parentSelect" class="form-select form-control-custom" required>
                                    <option value="">Select Parent</option>
                                    @if ($parents->isEmpty())
                                        <option value="" disabled class="text-danger">No parents records were found</option>
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
                                <select class="form-select form-control-custom" id="group" name="group" required>
                                    <option value="">--Select Stream--</option>
                                    <option value="a">Stream A</option>
                                    <option value="b">Stream B</option>
                                    <option value="c">Stream C</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="bus" class="form-label">Bus Number</label>
                                <select name="driver" id="bus" class="form-select form-control-custom">
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
                                <input type="file" class="form-control-custom" id="image" name="image">
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

            // Show/hide graduation year field and manage required attribute
            const classSelect = document.getElementById('classSelect');
            const graduationYearField = document.getElementById('graduationYearField');
            const graduationYearInput = document.getElementById('graduation_year');

            if (classSelect) {
                classSelect.addEventListener('change', function() {
                    if (this.value === '0') {
                        // If graduate class selected, show field and make it required
                        graduationYearField.style.display = 'block';
                        graduationYearInput.setAttribute('required', 'required');
                    } else {
                        // If other class selected, hide field and remove required
                        graduationYearField.style.display = 'none';
                        graduationYearInput.removeAttribute('required');
                        graduationYearInput.value = ''; // Optional: Clear value when hidden
                    }
                });
            }

            // Form validation and submission handling
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    // Dynamic required attribute handling before validation
                    const selectedClass = classSelect ? classSelect.value : '';
                    if (selectedClass === '0') {
                        graduationYearInput.setAttribute('required', 'required');
                    } else {
                        graduationYearInput.removeAttribute('required');
                    }

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
