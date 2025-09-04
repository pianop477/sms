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
            margin: 20px auto;
            max-width: 800px;
        }

        .card-body {
            padding: 30px;
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

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
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
                <div class="row mb-4">
                    <div class="col-md-10">
                        <h4 class="header-title">Edit Course Details</h4>
                    </div>
                    <div class="col-md-2 text-end">
                        <a href="{{route('courses.index')}}" class="btn btn-info btn-xs float-right">
                            <i class="fas fa-arrow-circle-left me-2"></i> Back
                        </a>
                    </div>
                </div>

                <!-- Edit Form -->
                <form class="needs-validation" novalidate action="{{route('course.update', ['id' => Hashids::encode($course->id)])}}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <h5 class="mb-4"><i class="fas fa-book me-2 text-primary"></i> Course Information</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sname" class="form-label">Course Name</label>
                                <input type="text" required name="sname" class="form-control text-capitalize" id="sname" value="{{$course->course_name}}">
                                @error('sname')
                                <div class="text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{$message}}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="scode" class="form-label">Course Code</label>
                                <input type="text" required name="scode" class="form-control text-uppercase" id="scode" value="{{$course->course_code}}">
                                @error('scode')
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
