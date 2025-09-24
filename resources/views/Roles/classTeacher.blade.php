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
            vertical-align: middle;
        }

        .progress-table td {
            padding: 15px 10px;
            vertical-align: middle;
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

            .btn-action {
                margin-bottom: 10px;
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
                                <h4 class="header-title text-uppercase">Assigned Class Teachers: <span class="class-name-highlight">{{ $classes->class_name}}</span></h4>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <a href="{{route('Classes.index', ['class' => Hashids::encode($classes->id)])}}" class="btn btn-info btn-action mr-2">
                                        <i class="fas fa-arrow-circle-left me-1"></i> Back
                                    </a>
                                    <button type="button" class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#assignModal">
                                        <i class="fas fa-user-plus me-1"></i> Assign Teacher
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Teachers Table -->
                        <div class="single-table">
                            <div class="table-responsive">
                                <table class="table table-hover progress-table" id="teachersTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Class Name</th>
                                            <th scope="col">Stream</th>
                                            <th scope="col">Teacher Name</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($assignedTeachers->isEmpty())
                                            <tr>
                                                <td colspan="5" class="text-center text-danger">No class teachers assigned yet.</td>
                                            </tr>
                                        @else
                                            @foreach ($classTeacher as $teacher)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td class="text-uppercase fw-bold">{{$teacher->class_name}}</td>
                                                    <td class="">
                                                        <span class="badge bg-info text-white">Stream {{$teacher->group}}</span>
                                                    </td>
                                                    <td class="fw-medium text-capitalize">{{$teacher->teacher_first_name}} {{$teacher->teacher_last_name}}</td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <a href="{{route('roles.edit', ['teacher' => Hashids::encode($teacher->id)])}}" class="btn btn-sm btn-secondary" title="Edit">
                                                                <i class="ti-pencil"></i>
                                                            </a>
                                                            <a href="{{route('roles.destroy', ['teacher' => Hashids::encode($teacher->id)])}}" class="btn btn-sm btn-danger" title="Remove" onclick="return confirm('Are you sure you want to remove {{ strtoupper($teacher->teacher_first_name) }} {{ strtoupper($teacher->teacher_last_name) }} from this class?')">
                                                                <i class="ti-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Teacher Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">Assign Class Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('Class.teacher.assign', ['classes' => Hashids::encode($classes->id)])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="teacherSelect" class="form-label">Teacher's Name</label>
                                <select name="teacher" id="teacherSelect" class="form-control-custom select2" style="width: 100%;" required>
                                    <option value="">-- Select Class Teacher --</option>
                                    @if ($teachers->isEmpty())
                                        <option value="" class="text-danger" disabled>No teachers found</option>
                                    @else
                                        @foreach ($teachers as $teacher)
                                            <option value="{{$teacher->id}}">{{ucwords(strtolower($teacher->teacher_first_name))}} {{ucwords(strtolower($teacher->teacher_last_name))}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('teacher')
                                <div class="text-danger small mt-2">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="groupSelect" class="form-label">Stream</label>
                                <select name="group" id="groupSelect" class="form-control-custom " required>
                                    <option value="">-- Select Stream --</option>
                                    <option value="A">Stream A</option>
                                    <option value="B">Stream B</option>
                                    <option value="C">Stream C</option>
                                </select>
                                @error('group')
                                <div class="text-danger small mt-2">
                                   {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="saveButton" class="btn btn-success">Assign Teacher</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('#teacherSelect').select2({
                    placeholder: "Search teacher...",
                    allowClear: true,
                    dropdownParent: $('#assignModal')
                }).on('select2:open', function () {
                    $('.select2-results__option').css('text-transform', 'capitalize');
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
                    submitButton.innerHTML = "Assign Teacher";
                    return;
                }

                // Delay submission to show loading state
                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
</body>
</html>
@endsection
