@extends('SRTDashboard.frame')
@section('content')
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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                @if (isset($message))
                                    <h4 class="header-title">{{ $message }}</h4>
                                @else
                                    <h4 class="header-title text-uppercase">Courses List: <span class="class-name-highlight">{{$class->class_name}}</span></h4>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <a href="{{route('courses.index')}}" class="btn btn-info btn-action mr-2">
                                        <i class="fas fa-arrow-circle-left me-1"></i> Back
                                    </a>
                                    <button type="button" class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#assignModal">
                                        <i class="fas fa-plus me-1"></i> Assign Course
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Content Section -->
                        @if (isset($message))
                            <div class="alert alert-warning text-center py-4" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <h6 class="mb-0">{{ $message }}</h6>
                            </div>
                        @elseif ($classCourse->isEmpty())
                            <div class="alert alert-warning text-center py-4" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <h6 class="mb-0">No courses assigned for this class</h6>
                            </div>
                        @else
                            <div class="single-table">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Course Name</th>
                                                <th scope="col">Course Code</th>
                                                <th scope="col">Subject Teacher</th>
                                                <th scope="col">Status</th>
                                                <th scope="col" class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($classCourse as $course)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="text-capitalize fw-medium">{{ $course->course_name }}</td>
                                                    <td class="text-uppercase fw-bold">{{ $course->course_code }}</td>
                                                    <td class="text-capitalize">{{ $course->first_name }} {{ $course->last_name }}</td>
                                                    <td>
                                                        @if ($course->status == 1)
                                                            <span class="badge-status bg-success text-white">Active</span>
                                                        @else
                                                            <span class="badge-status bg-danger">Blocked</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            @if ($course->status == 1)
                                                                <a href="{{route('courses.assign', ['id' => Hashids::encode($course->id)])}}" class="btn btn-sm btn-secondary" title="Edit">
                                                                    <i class="ti-pencil"></i>
                                                                </a>
                                                                <form action="{{route('block.assigned.course', ['id' => Hashids::encode($course->id)])}}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button class="btn btn-sm btn-warning" type="submit" title="Block" onclick="return confirm('Are you sure you want to block {{strtoupper($course->course_name)}} Course?')">
                                                                        <i class="ti-na"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <form action="{{route('unblock.assigned.course', ['id' => Hashids::encode($course->id)])}}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button class="btn btn-sm btn-success" type="submit" title="Unblock" onclick="return confirm('Are you sure you want to unblock {{strtoupper($course->course_name)}} Course?')">
                                                                        <i class="ti-reload"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <a href="{{route('courses.delete', ['id' => Hashids::encode($course->id)])}}" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to Delete {{strtoupper($course->course_name)}} Course permanently?')">
                                                                <i class="ti-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Course Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">Assign Teaching Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('course.assign')}}" method="POST">
                        @csrf
                        <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="courseSelect" class="form-label">Select Subject</label>
                                <select name="course_id" id="courseSelect" class="form-control select2" required>
                                    <option value="">--Select Course--</option>
                                    @if ($courses->isEmpty())
                                        <option value="" class="text-danger" disabled>No courses found</option>
                                    @else
                                        @foreach ($courses as $course)
                                            <option value="{{$course->id}}">{{ucwords(strtolower($course->course_name))}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('course_id')
                                <div class="text-danger small mt-2">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="classSelect" class="form-label">Select Class</label>
                                <select name="class_id" id="classSelect" class="form-control text-uppercase" required>
                                    <option value="{{$class->id}}" selected>{{$class->class_name}}</option>
                                </select>
                                @error('class_id')
                                <div class="text-danger small mt-2">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="teacherSelect" class="form-label">Select Teacher</label>
                                <select name="teacher_id" id="teacherSelect" class="form-control text-capitalize select2" required>
                                    <option value="">--Select Teacher--</option>
                                    @if ($teachers->isEmpty())
                                        <option value="" class="text-danger" disabled>No teachers found</option>
                                    @else
                                        @foreach ($teachers as $teacher)
                                            <option value="{{$teacher->id}}">{{$teacher->first_name}} {{$teacher->last_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('teacher_id')
                                <div class="text-danger small mt-2">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="saveButton" class="btn btn-success">Assign Course</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('#courseSelect').select2({
                    placeholder: "Search course...",
                    allowClear: true,
                    dropdownParent: $('#assignModal')
                });

                $('#teacherSelect').select2({
                    placeholder: "Search teacher...",
                    allowClear: true,
                    dropdownParent: $('#assignModal')
                });
            } else {
                console.error("Select2 is not loaded!");
            }

            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (!form || !submitButton) return;

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Assigning...`;

                // Check form validity
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = "Assign Course";
                    return;
                }

                // Delay submission to show loading state
                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
@endsection
