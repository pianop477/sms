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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
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
            padding: 10px;
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

        .invalid-feedback {
            font-weight: 600;
            color: var(--danger);
            margin-top: 5px;
        }

        .text-danger small {
            font-weight: 600;
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

        .btn-primary-custom:disabled {
            opacity: 0.7;
            transform: none;
            box-shadow: none;
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

        .date-input-group {
            position: relative;
        }

        .date-input-group::after {
            content: '\f073';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 15px;
            top: 42px;
            color: var(--primary);
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 20px;
            }

            .form-section {
                padding: 20px;
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

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title text-white">
                            <i class="fas fa-calendar-check me-2"></i> Generate Attendance Report
                        </h4>
                        <p class="mb-0 text-white-50"> Generate detailed attendance reports for classes</p>
                    </div>
                </div>
                <i class="fas fa-chart-bar floating-icons"></i>
            </div>

            <div class="card-body">
                <form class="needs-validation" novalidate
                      action="{{route('class.attendance.report')}}"
                      method="POST"
                      enctype="multipart/form-data"
                      id="attendanceForm">
                    @csrf

                    <div class="form-section">
                        <div class="row">
                            <!-- Class Selection -->
                            <div class="col-md-6 mb-4">
                                <label for="class" class="form-label">
                                    <i class="fas fa-chalkboard text-primary"></i>
                                    Class <span class="required-star">*</span>
                                </label>
                                <select name="class" id="class" class="form-control text-uppercase" required>
                                    <option value="">-- Select Class --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{$class->id}}" {{old('class') == $class->id ? 'selected' : ''}}>
                                            {{$class->class_name}}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please select a class
                                </div>
                                @error('class')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Stream Selection -->
                            <div class="col-md-6 mb-4">
                                <label for="stream" class="form-label">
                                    <i class="fas fa-stream text-primary"></i>
                                    Stream
                                </label>
                                <select name="stream" id="stream" class="form-control">
                                    <option value="all" {{old('stream', 'all') == 'all' ? 'selected' : ''}}>All Streams</option>
                                    <option value="a" {{old('stream') == 'a' ? 'selected' : ''}}>Stream A</option>
                                    <option value="b" {{old('stream') == 'b' ? 'selected' : ''}}>Stream B</option>
                                    <option value="c" {{old('stream') == 'c' ? 'selected' : ''}}>Stream C</option>
                                </select>
                                @error('stream')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-6 mb-4 date-input-group">
                                <label for="start" class="form-label">
                                    {{-- <i class="fas fa-calendar-start text-primary"></i> --}}
                                    Start Date <span class="required-star">*</span>
                                </label>
                                <input type="date" name="start" class="form-control" id="start" required
                                       value="{{old('start')}}"
                                       max="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                <div class="invalid-feedback">
                                    Please provide a valid start date
                                </div>
                                @error('start')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div class="col-md-6 mb-4 date-input-group">
                                <label for="end" class="form-label">
                                    {{-- <i class="fas fa-calendar-day text-primary"></i> --}}
                                    End Date <span class="required-star">*</span>
                                </label>
                                <input type="date" name="end" class="form-control" id="end" required
                                       value="{{old('end')}}"
                                       max="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                <div class="invalid-feedback">
                                    Please provide a valid end date
                                </div>
                                @error('end')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary-custom pulse-animation" type="submit" id="generateButton">
                            <i class="fas fa-cog me-2"></i> Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('attendanceForm');
            const generateButton = document.getElementById('generateButton');

            // Form validation
            (function() {
                'use strict';
                window.addEventListener('load', function() {
                    // Fetch all the forms we want to apply custom Bootstrap validation styles to
                    var forms = document.getElementsByClassName('needs-validation');
                    // Loop over them and prevent submission
                    var validation = Array.prototype.filter.call(forms, function(form) {
                        form.addEventListener('submit', function(event) {
                            if (form.checkValidity() === false) {
                                event.preventDefault();
                                event.stopPropagation();

                                // Scroll to first invalid field
                                var invalidElements = form.querySelectorAll(':invalid');
                                if (invalidElements.length > 0) {
                                    invalidElements[0].scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                }
                            } else {
                                // Show loading state
                                event.preventDefault();
                                showPreloader();

                                // Submit the form after a brief delay to show the loading state
                                setTimeout(function() {
                                    form.submit();
                                }, 500);
                            }
                            form.classList.add('was-validated');
                        }, false);
                    });
                }, false);
            })();

            function showPreloader() {
                // Disable button and show loading spinner
                generateButton.disabled = true;
                generateButton.innerHTML = '<span class="spinner me-2"></span> Generating Report...';
            }

            // Reset button state when page is shown (for back button navigation)
            window.addEventListener("pageshow", function(event) {
                if (generateButton) {
                    generateButton.disabled = false;
                    generateButton.innerHTML = '<i class="fas fa-cog me-2"></i> Generate Report';
                }
            });

            // Date validation: End date should not be before start date
            const startDateInput = document.getElementById('start');
            const endDateInput = document.getElementById('end');

            startDateInput.addEventListener('change', function() {
                if (endDateInput.value && new Date(endDateInput.value) < new Date(this.value)) {
                    endDateInput.setCustomValidity('End date cannot be before start date');
                } else {
                    endDateInput.setCustomValidity('');
                }
            });

            endDateInput.addEventListener('change', function() {
                if (startDateInput.value && new Date(this.value) < new Date(startDateInput.value)) {
                    this.setCustomValidity('End date cannot be before start date');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
@endsection
