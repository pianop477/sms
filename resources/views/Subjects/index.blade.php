@extends('SRTDashboard.frame')

@section('content')
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

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .class-list {
            list-style: none;
            padding: 0;
        }

        .class-item {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .class-item:hover {
            border-color: var(--primary-color);
            box-shadow: 0 0.15rem 0.5rem 0 rgba(58, 59, 69, 0.1);
        }

        .class-link {
            display: block;
            padding: 12px 15px;
            color: var(--dark-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .class-link:hover {
            background-color: #f8f9fc;
            color: var(--primary-color);
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
            <!-- Classes List Section -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title text-center"> Subjects by Classes</h4>
                        <p class="text-danger mb-3"><i class="fas fa-info-circle me-2"></i>Select class to view subjects</p>

                        @if ($classes->isEmpty())
                            <div class="alert alert-warning text-center py-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <p class="mb-0">No Classes records found!</p>
                            </div>
                        @else
                            <ul class="class-list">
                                @foreach ($classes as $class)
                                <li class="class-item">
                                    <a href="{{route('courses.view.class', ['id' => Hashids::encode($class->id)])}}" class="class-link">
                                        <i class="fas fa-angle-double-right me-2 text-primary"></i>
                                        <span class="fw-medium text-uppercase">{{$class->class_name}}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Subjects List Section -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 class="header-title"> All Registered Subjects</h4>
                                <p class="text-success mb-0"><i class="fas fa-book me-2"></i> Registered subjects</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" class="btn btn-primary btn-action float-right" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                                    <i class="fas fa-plus me-1"></i> New Subject
                                </button>
                            </div>
                        </div>

                        @if ($subjects->isEmpty())
                            <div class="alert alert-warning text-center py-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <p class="mb-0 text-danger">No courses registered!</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover" id="myTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Course Name</th>
                                            <th>Course Code</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subjects as $course)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td class="text-uppercase fw-medium">{{$course->course_name}}</td>
                                                <td class="text-uppercase fw-bold">{{$course->course_code}}</td>
                                                <td class="text-center">
                                                    @if ($course->status == 1)
                                                        <span class="badge-status bg-success text-white">Active</span>
                                                    @else
                                                        <span class="badge-status bg-danger text-white">Blocked</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        @if ($course->status == 1)
                                                            <a href="{{route('course.edit', ['id' => Hashids::encode($course->id)])}}" class="btn btn-sm btn-secondary" title="Edit">
                                                                <i class="fas fa-pencil"></i>
                                                            </a>
                                                            <form action="{{route('courses.block', ['id' => Hashids::encode($course->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-warning" title="Block" onclick="return confirm('Are you sure you want to Block {{strtoupper($course->course_name)}} Course?')">
                                                                    <i class="ti-na"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{route('courses.unblock', ['id' => Hashids::encode($course->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-success" title="Unblock" onclick="return confirm('Are you sure you want to unblock {{strtoupper($course->course_name)}} Course?')">
                                                                    <i class="ti-reload"></i>
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Subject Modal -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubjectModalLabel">Register New Subject</h5>
                    <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('course.registration')}}" method="POST">
                        @csrf
                        <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sname" class="form-label">Subject Name</label>
                                <input type="text" required name="sname" class="form-control-custom" id="sname" placeholder="Course Name" value="{{old('name')}}">
                                @error('sname')
                                <div class="text-danger small">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="scode" class="form-label">Subject Code</label>
                                <input type="text" required name="scode" class="form-control-custom text-uppercase" id="scode" placeholder="Course Code" value="{{old('code')}}">
                                @error('scode')
                                <div class="text-danger small">
                                   {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="saveButton" class="btn btn-success"> <i class="fas fa-save"></i> Save</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (!form || !submitButton) return;

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...`;

                // Check form validity
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = "Save Subject";
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
