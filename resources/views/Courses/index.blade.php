@extends('SRTDashboard.frame')
@section('content')
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
            color: #333;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
        }

        .header-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .btn-action {
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background-color: var(--info-color);
            color: white;
        }

        .table th {
            padding: 15px 10px;
            font-weight: 600;
            vertical-align: middle;
        }

        .table td {
            padding: 15px 10px;
            vertical-align: middle;
        }

        .badge-status {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
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

        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

        .form-control:focus, .form-select:focus, .select2-container--focus .select2-selection {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            color: #495057 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        .class-name-highlight {
            color: var(--secondary-color);
            font-weight: 700;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .btn-action {
                margin-bottom: 10px;
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
                                                    <td class="text-capitalize fw-medium">{{ ucwords(strtolower($course->course_name)) }}</td>
                                                    <td class="text-uppercase fw-bold">{{ strtoupper($course->course_code) }}</td>
                                                    <td class="text-capitalize">{{ ucwords(strtolower($course->first_name ))}} {{ ucwords(strtolower($course->last_name)) }}</td>
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
