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
            font-size: 15px;
        }

        .card-body {
            padding: 30px;
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
            width: 100%;
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
            justify-content: space-between;
            align-items: center;
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

        .btn-danger-custom {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
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

        .btn-danger-custom:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
            color: white;
        }

        .expiry-badge {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            color: #856404;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .draft-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
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

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .draft-actions {
                flex-direction: column;
                gap: 10px;
            }

            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 24px;
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
    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title">
                            <i class="fas fa-clipboard-list me-2"></i> Results Submission Form
                        </h4>
                        <p class="mb-0 text-white-50"> Pre-information section for examination results</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('home') }}" class="btn btn-back float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
                <i class="fas fa-graduation-cap floating-icons"></i>
            </div>
            <div class="card-body">
                <form class="needs-validation" novalidate action="{{ route('score.captured.values') }}" method="POST">
                    @csrf

                    <!-- Hidden Fields -->
                    <input type="hidden" name="course_id" value="{{ $class_course->course_id }}">
                    <input type="hidden" name="class_id" value="{{ $class_course->class_id }}">
                    <input type="hidden" name="teacher_id" value="{{ $class_course->teacher_id }}">
                    <input type="hidden" name="school_id" value="{{ $class_course->school_id }}">

                    <div class="form-section">
                        <div class="row">
                            <!-- Examination Type -->
                            <div class="col-md-6 mb-4">
                                <label for="exam_type" class="form-label">
                                    <i class="fas fa-file-alt text-primary"></i>
                                    Examination Type <span class="required-star">*</span>
                                </label>
                                <select name="exam_type" id="exam_type" class="form-control-custom text-capitalize" required>
                                    <option value="" disabled selected>-- Select Exam type --</option>
                                    @foreach ($exams as $exam)
                                        <option value="{{ $exam->id }}" class="text-capitalize" {{ old('exam_type') == $exam->id ? 'selected' : '' }}>
                                            {{ $exam->exam_type }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please select an examination type
                                </div>
                                @error('exam_type')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Exam Date -->
                            <div class="col-md-6 mb-4">
                                <label for="exam_date" class="form-label">
                                    <i class="fas fa-calendar-day text-primary"></i>
                                    Upload Date <span class="required-star">*</span>
                                </label>
                                <input type="date" name="exam_date" class="form-control-custom" id="exam_date" required
                                       value="{{ old('exam_date') }}"
                                       min="{{ \Carbon\Carbon::now()->subYears(1)->format('Y-m-d') }}"
                                       max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                <div class="invalid-feedback">
                                    Please provide a valid exam date
                                </div>
                                @error('exam_date')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Exam Term -->
                            <div class="col-md-6 mb-4">
                                <label for="term" class="form-label">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                    Academic Term <span class="required-star">*</span>
                                </label>
                                <select name="term" id="term" class="form-control-custom" required>
                                    <option value="" disabled selected>-- Select Term --</option>
                                    <option value="i" {{ old('term') == 'i' ? 'selected' : '' }}>Term 1</option>
                                    <option value="ii" {{ old('term') == 'ii' ? 'selected' : '' }}>Term 2</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select an academic term
                                </div>
                                @error('term')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Marking System -->
                            <div class="col-md-6 mb-4">
                                <label for="marking_style" class="form-label">
                                    <i class="fas fa-chart-bar text-primary"></i>
                                    Grading System <span class="required-star">*</span>
                                </label>
                                <select name="marking_style" id="marking_style" class="form-control-custom" required>
                                    <option value="" disabled selected>-- Select Grading System --</option>
                                    <option value="2" {{ old('marking_style') == '2' ? 'selected' : '' }}>Percentage (0-100%)</option>
                                    <option value="1" {{ (old('marking_style') ?: '1') == '1' ? 'selected' : '' }}>Points (0-50)</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a grading system
                                </div>
                                @error('marking_style')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        @if ($saved_results->isEmpty())
                            <button class="btn btn-primary-custom pulse-animation" id="saveButton" type="submit">
                                <i class="fas fa-save"></i>Save & Proceed
                            </button>
                        @else
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <button class="btn btn-primary-custom pulse-animation" id="saveButton" type="submit">
                                        <i class="fas fa-save"></i>Save & Proceed
                                    </button>

                                    <div class="expiry-badge">
                                        <i class="fas fa-clock"></i>
                                        Expires: {{\Carbon\Carbon::parse($saved_results->first()->expiry_date)->format('d M Y H:i')}}
                                    </div>
                                </div>

                                <div class="draft-actions">
                                    <a href="{{route('form.saved.values', [
                                        'course' => Hashids::encode($class_course->course_id),
                                        'teacher' => Hashids::encode($class_course->teacher_id),
                                        'school' => Hashids::encode($class_course->school_id),
                                        'class' => Hashids::encode($class_course->class_id),
                                        'type' => $saved_results->first()->exam_type_id,
                                        'date' => $saved_results->first()->exam_date,
                                        'term' => $saved_results->first()->exam_term,
                                        'style' => $saved_results->first()->marking_style
                                    ])}}" class="btn btn-warning-custom">
                                        <i class="fas fa-edit"></i> Edit Pending Results
                                    </a>

                                    <a href="{{route('results.draft.delete', [
                                        'course' => Hashids::encode($class_course->course_id),
                                        'teacher' => Hashids::encode($class_course->teacher_id),
                                        'type' => $saved_results->first()->exam_type_id,
                                        'class' => Hashids::encode($class_course->class_id),
                                        'date' => $saved_results->first()->exam_date
                                    ])}}"
                                    onclick="return confirm('Are you sure you want to permanently delete these pending results? This action cannot be undone.')"
                                    class="btn btn-danger-custom">
                                        <i class="fas fa-trash-alt"></i> Delete Draft
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2({
                minimumResultsForSearch: Infinity,
                placeholder: $(this).data('placeholder'),
                width: '100%'
            });

            // Initialize flatpickr for date inputs
            $(".flatpickr").flatpickr({
                dateFormat: "Y-m-d",
                maxDate: "today"
            });

            // Form validation
            (function() {
                'use strict';
                window.addEventListener('load', function() {
                    var forms = document.getElementsByClassName('needs-validation');
                    var validation = Array.prototype.filter.call(forms, function(form) {
                        form.addEventListener('submit', function(event) {
                            if (form.checkValidity() === false) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                            form.classList.add('was-validated');
                        }, false);
                    });
                }, false);
            })();
        });
    </script>
@endsection
