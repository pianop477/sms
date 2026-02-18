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
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
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

            .score-input,
            .grade-input {
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

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header- text-white">
                            <i class="fas fa-edit me-2"></i> Edit Results - {{ strtoupper($courseName) }}
                            ({{ strtoupper($className) }})
                        </h4>
                        <p class="mb-0 text-white-50 text-white"> Update examination scores for students</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('home') }}" class="btn btn-back float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-graduation-cap floating-icons"></i>
            </div>

            <div class="card-body">
                <div class="info-alert">
                    <div class="row text-center">
                        <div class="col-md-6 mb-3 mb-md-0 text-capitalize">
                            <strong><i class="fas fa-file-alt me-2"></i> Examination:</strong> {{ $examName }}
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar-day me-2"></i> Exam Date:</strong>
                            {{ \Carbon\Carbon::parse($examDate)->format('d F Y') }}
                        </div>
                    </div>
                </div>

                <form action="{{ route('results.update.draft', ['id' => $id]) }}" method="POST" id="resultsForm"
                    class="needs-validation" novalidate>
                    @csrf

                    <div class="form-section">
                        <div class="row">
                            <!-- Examination Type -->
                            <div class="col-md-6 mb-4">
                                <label for="exam_type_id" class="form-label">
                                    <i class="fas fa-file-alt text-primary"></i>
                                    Examination Type <span class="required-star">*</span>
                                </label>
                                <select name="exam_type_id" id="exam_type_id" class="form-control-custom text-capitalize"
                                    required>
                                    <option value="">-- Select Exam type --</option>
                                    @foreach ($exams as $exam)
                                        <option value="{{ $exam->id }}"
                                            {{ $exam->id == $examTypeId ? 'selected' : '' }}>
                                            {{ $exam->exam_type }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please select an examination type
                                </div>
                            </div>

                            <!-- Exam Date -->
                            <div class="col-md-6 mb-4">
                                <label for="exam_date" class="form-label">
                                    <i class="fas fa-calendar-day text-primary"></i>
                                    Exam Date <span class="required-star">*</span>
                                </label>
                                <input type="date" name="exam_date" class="form-control-custom" id="exam_date" required
                                    value="{{ \Carbon\Carbon::parse($examDate)->format('Y-m-d') }}"
                                    min="{{ \Carbon\Carbon::now()->subYears(1)->format('Y-m-d') }}"
                                    max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                <div class="invalid-feedback">
                                    Please provide a valid exam date
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="course_id" value="{{ $courseId }}">
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                    <input type="hidden" name="teacher_id" value="{{ $teacherId }}">
                    <input type="hidden" name="school_id" value="{{ $schoolId }}">
                    <input type="hidden" name="term" value="{{ $draftResults->first()->exam_term }}">
                    <input type="hidden" name="marking_style" value="{{ $marking_style }}">

                    <div class="instruction-text">
                        <i class="fas fa-info-circle me-2"></i>Enter score from 0 to
                        {{ $marking_style == 1 ? '50' : '100' }} correctly
                    </div>

                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-custom table-responsive-md">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="45%">Student Name</th>
                                        <th width="30%">Score</th>
                                        <th width="20%">Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $index => $student)
                                        @php
                                            $draftResult = $draftResults->where('student_id', $student->id)->first();
                                            $score = $draftResult ? $draftResult->score : '';
                                            $grade = '';
                                            $bgColor = '';

                                            // Auto-generate grade on page load
                                            if (is_numeric($score)) {
                                                if ($marking_style == 1) {
                                                    if ($score >= 41 && $score <= 50) {
                                                        $grade = 'A';
                                                        $bgColor = '#97e897';
                                                    } elseif ($score >= 31 && $score <= 40) {
                                                        $grade = 'B';
                                                        $bgColor = '#4edcdc';
                                                    } elseif ($score >= 21 && $score <= 30) {
                                                        $grade = 'C';
                                                        $bgColor = '#e9f0aa';
                                                    } elseif ($score >= 11 && $score <= 20) {
                                                        $grade = 'D';
                                                        $bgColor = '#ef8f8f';
                                                    } elseif ($score >= 0 && $score <= 10) {
                                                        $grade = 'E';
                                                        $bgColor = '#ebc4f3';
                                                    } else {
                                                        $grade = 'Error';
                                                        $bgColor = 'red';
                                                    }
                                                } elseif ($marking_style == 2) {
                                                    if ($score >= 81 && $score <= 100) {
                                                        $grade = 'A';
                                                        $bgColor = '#97e897';
                                                    } elseif ($score >= 61 && $score <= 80) {
                                                        $grade = 'B';
                                                        $bgColor = '#4edcdc';
                                                    } elseif ($score >= 41 && $score <= 60) {
                                                        $grade = 'C';
                                                        $bgColor = '#e9f0aa';
                                                    } elseif ($score >= 21 && $score <= 40) {
                                                        $grade = 'D';
                                                        $bgColor = '#ef8f8f';
                                                    } elseif ($score >= 0 && $score <= 20) {
                                                        $grade = 'E';
                                                        $bgColor = '#ebc4f3';
                                                    } else {
                                                        $grade = 'Error';
                                                        $bgColor = 'red';
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-capitalize">{{ ucwords(strtolower($student->first_name)) }}
                                                {{ ucwords(strtolower($student->middle_name)) }}
                                                {{ ucwords(strtolower($student->last_name)) }}</td>
                                            <td class="text-center">
                                                <input type="number" name="scores[{{ $student->id }}]"
                                                    class="form-control score-input" value="{{ $score }}"
                                                    min="0" max="{{ $marking_style == 1 ? '50' : '100' }}">
                                            </td>
                                            <td class="text-center">
                                                <input type="text" class="form-control grade-input"
                                                    value="{{ $grade }}" disabled
                                                    style="background-color: {{ $bgColor }}; font-weight: bold;">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <!-- Save to Draft -->
                        <button type="submit" class="btn btn-warning-custom" name="action" value="save">
                            <i class="fas fa-save"></i> Save as Draft
                        </button>

                        <!-- Submit Final Results -->
                        <button type="submit" class="btn btn-success-custom pulse-animation" name="action" value="submit"
                            id="submitButton">
                            <i class="fas fa-check"></i> Submit Final Results
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
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

                                // Scroll to first invalid field
                                var invalidElements = form.querySelectorAll(':invalid');
                                if (invalidElements.length > 0) {
                                    invalidElements[0].scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                }
                            }
                            form.classList.add('was-validated');
                        }, false);
                    });
                }, false);
            })();

            // Score input validation and grade calculation
            const scoreInputs = document.querySelectorAll('.score-input');
            const gradeInputs = document.querySelectorAll('.grade-input');
            const markingStyle = {{ $marking_style }}; // Store marking style in variable

            // Function to calculate grade based on score
            function calculateGrade(score) {
                let grade = '';
                let bgColor = '';

                if (isNaN(score)) {
                    grade = 'X';
                    bgColor = 'orange';
                } else if (markingStyle == 1) {
                    if (score >= 41 && score <= 50) {
                        grade = 'A';
                        bgColor = '#97e897';
                    } else if (score >= 31 && score <= 40) {
                        grade = 'B';
                        bgColor = '#4edcdc';
                    } else if (score >= 21 && score <= 30) {
                        grade = 'C';
                        bgColor = '#e9f0aa';
                    } else if (score >= 11 && score <= 20) {
                        grade = 'D';
                        bgColor = '#ef8f8f';
                    } else if (score >= 0 && score <= 10) {
                        grade = 'E';
                        bgColor = '#ebc4f3';
                    } else {
                        grade = 'Error';
                        bgColor = 'red';
                    }
                } else if (markingStyle == 2) {
                    if (score >= 81 && score <= 100) {
                        grade = 'A';
                        bgColor = '#97e897';
                    } else if (score >= 61 && score <= 80) {
                        grade = 'B';
                        bgColor = '#4edcdc';
                    } else if (score >= 41 && score <= 60) {
                        grade = 'C';
                        bgColor = '#e9f0aa';
                    } else if (score >= 21 && score <= 40) {
                        grade = 'D';
                        bgColor = '#ef8f8f';
                    } else if (score >= 0 && score <= 20) {
                        grade = 'E';
                        bgColor = '#ebc4f3';
                    } else {
                        grade = 'Error';
                        bgColor = 'red';
                    }
                } else if (markingStyle == 3) {
                    if (score >= 75 && score <= 100) {
                        grade = 'A';
                        bgColor = '#97e897';
                    } else if (score >= 65 && score <= 74) {
                        grade = 'B';
                        bgColor = '#4edcdc';
                    } else if (score >= 45 && score <= 64) {
                        grade = 'C';
                        bgColor = '#e9f0aa';
                    } else if (score >= 30 && score <= 44) {
                        grade = 'D';
                        bgColor = '#ef8f8f';
                    } else if (score >= 0 && score <= 29) {
                        grade = 'F';
                        bgColor = '#ebc4f3';
                    } else {
                        grade = 'Error';
                        bgColor = 'red';
                    }
                }

                return {
                    grade,
                    bgColor
                };
            }

            // Function to update grade for specific input
            function updateGrade(input, index) {
                const score = parseFloat(input.value);
                const {
                    grade,
                    bgColor
                } = calculateGrade(score);

                gradeInputs[index].value = grade;
                gradeInputs[index].style.backgroundColor = bgColor;
            }

            // Process all score inputs
            scoreInputs.forEach((input, index) => {
                // Initialize grade on page load if score exists
                if (input.value.trim() !== '') {
                    updateGrade(input, index);
                }

                // Add realtime input event (updates as you type)
                input.addEventListener('input', () => {
                    updateGrade(input, index);

                    // Optional: Enforce max value based on marking style
                    const maxValue = markingStyle == 1 ? 50 : 100;
                    if (input.value > maxValue) {
                        input.value = maxValue;
                        updateGrade(input, index);
                    }
                });

                // Also update on blur (for when user leaves field)
                input.addEventListener('blur', () => {
                    updateGrade(input, index);
                });
            });

            // Add confirmation dialogs with null checks
            const saveButton = document.querySelector('button[name="action"][value="save"]');
            const submitButton = document.querySelector('button[name="action"][value="submit"]');

            if (saveButton) {
                saveButton.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to save results to draft?')) {
                        e.preventDefault();
                    }
                });
            }

            if (submitButton) {
                submitButton.addEventListener('click', function(e) {
                    if (!confirm(
                            'Are you sure you want to submit final results? No editing will be allowed after submission.'
                            )) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
@endsection
