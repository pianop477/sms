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
            margin: 20px auto;
            max-width: 900px;
        }

        .card-body {
            padding: 5px;
        }

        .header-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .btn-action {
            border-radius: 5px;
            padding: 10px 25px;
            font-weight: 600;
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

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .form-control:focus, .select2-container--focus .select2-selection {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
        }

        .back-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .form-section {
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
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

        .info-field {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            min-height: 38px;
            display: flex;
            align-items: center;
        }

        @media (max-width: 768px) {
            .card {
                margin: 10px;
            }

            .card-body {
                padding: 20px;
            }

            .btn-action {
                width: 100%;
            }
        }
    </style>
    <div class="py-4">
        <div class="card">
            <div class="card-body">
                <!-- Header Section -->
                <div class="row mb-4 mt-3 p-3">
                    <div class="col-md-8">
                        <h4 class="header-title">Change Subject Teacher</h4>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('courses.view.class', ['id' => Hashids::encode($classCourse->class_id)])}}" class="back btn btn-info btn-xs float-right">
                            <i class="fas fa-arrow-circle-left me-2"></i> Back
                        </a>
                    </div>
                </div>

                <!-- Edit Form -->
                <form class="needs-validation" novalidate action="{{route('courses.assigned.teacher', ['id' => Hashids::encode($classCourse->id)])}}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <h5 class="mb-4"><i class="fas fa-book me-2 text-primary"></i> Course Information</h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Course Name</label>
                                <div class="info-field">
                                    {{$classCourse->course_name}}
                                </div>
                                <input type="hidden" name="course_id" class="text-capitalize" value="{{$classCourse->course_id}}">
                                @error('course_id')
                                <div class="text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{$message}}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Class</label>
                                <div class="info-field">
                                    {{$classCourse->class_name}}
                                </div>
                                <input type="hidden" name="class_id" class="text-uppercase" value="{{$classCourse->class_id}}">
                                @error('class_id')
                                <div class="text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{$message}}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="teacherSelect" class="form-label">Subject Teacher</label>
                                <select name="teacher_id" id="teacherSelect" class="form-control-custom select2" required>
                                    <option value="{{$classCourse->teacherId}}" selected>{{ucwords(strtolower($classCourse->first_name))}} {{ucwords(strtolower($classCourse->last_name))}}</option>
                                    @if ($teachers->isEmpty())
                                        <option value="" class="text-danger" disabled>No Teachers found</option>
                                    @else
                                        @foreach ($teachers as $teacher)
                                            <option value="{{$teacher->id}}">{{ucwords(strtolower($teacher->first_name))}} {{ucwords(strtolower($teacher->last_name))}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('teacher_id')
                                <div class="text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn btn-success btn-action" id="saveButton" type="submit">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
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
                $('#teacherSelect').select2({
                    placeholder: "Search teacher...",
                    allowClear: true
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
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...`;

                // Check form validity
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = `<i class="fas fa-save me-2"></i> Save Changes`;
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
