@extends('SRTDashboard.frame')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10 mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h4 class="mb-3 mb-md-0 text-center text-md-left">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Results - {{ strtoupper($courseName) }} ({{ strtoupper($className) }})
                        </h4>
                        <a href="{{route('home')}}" class="btn btn-light text-primary">
                            <i class="fas fa-arrow-circle-left mr-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Exam Info Header -->
                    <div class="alert alert-info mb-4">
                        <div class="row text-center">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <strong>Examination:</strong> {{ $examName }}
                            </div>
                            <div class="col-md-6">
                                <strong>Exam Date:</strong> {{ \Carbon\Carbon::parse($examDate)->format('d F Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Main Form -->
                    <form action="{{ route('results.update.draft') }}" method="POST" id="resultsForm" class="needs-validation" novalidate>
                        @csrf

                        <!-- Exam Details Section -->
                        <div class="row mb-4">
                            <div class="col-md-5 mb-3 mb-md-0">
                                <label class="font-weight-bold">Exam Type <span class="text-danger">*</span></label>
                                <select name="exam_type_id" class="form-control select2" required>
                                    <option value="">-- Select Exam type --</option>
                                    @foreach ($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ $exam->id == $examTypeId ? 'selected' : '' }}>
                                            {{ $exam->exam_type }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select an exam type</div>
                            </div>

                            <div class="col-md-5 offset-md-2">
                                <label class="font-weight-bold">Exam Date <span class="text-danger">*</span></label>
                                <input type="date" name="exam_date" class="form-control flatpickr" required
                                       value="{{ \Carbon\Carbon::parse($examDate)->format('Y-m-d') }}"
                                       min="{{ \Carbon\Carbon::now()->subYears(1)->format('Y-m-d') }}"
                                       max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                <div class="invalid-feedback">Please select a valid date</div>
                            </div>
                        </div>

                        <!-- Hidden Fields -->
                        <input type="hidden" name="course_id" value="{{ $courseId }}">
                        <input type="hidden" name="class_id" value="{{ $classId }}">
                        <input type="hidden" name="teacher_id" value="{{ $teacherId }}">
                        <input type="hidden" name="school_id" value="{{ $schoolId }}">
                        <input type="hidden" name="term" value="{{ $draftResults->first()->exam_term }}">
                        <input type="hidden" name="marking_style" value="{{ $marking_style }}">

                        <!-- Results Table -->
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="45%">Student Name</th>
                                        <th width="25%">Score (0-{{ $marking_style == 1 ? '50' : '100' }})</th>
                                        <th width="25%">Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                        @php
                                            $draftResult = $draftResults->where('student_id', $student->id)->first();
                                            $score = $draftResult ? $draftResult->score : '';
                                            $grade = '';
                                            $bgColor = '';

                                            // Grade calculation logic
                                            if (is_numeric($score)) {
                                                if ($marking_style == 1) {
                                                    if ($score >= 41 && $score <= 50) {
                                                        $grade = 'A'; $bgColor = '#d4edda';
                                                    } else if ($score >= 31 && $score <= 40) {
                                                        $grade = 'B'; $bgColor = '#cce5ff';
                                                    } else if ($score >= 21 && $score <= 30) {
                                                        $grade = 'C'; $bgColor = '#fff3cd';
                                                    } else if ($score >= 11 && $score <= 20) {
                                                        $grade = 'D'; $bgColor = '#f8d7da';
                                                    } else if($score >= 0 && $score <= 10) {
                                                        $grade = 'E'; $bgColor = '#e2e3e5';
                                                    } else {
                                                        $grade = 'Error'; $bgColor = '#dc3545';
                                                    }
                                                } else if ($marking_style == 2) {
                                                    if ($score >= 81 && $score <= 100) {
                                                        $grade = 'A'; $bgColor = '#d4edda';
                                                    } else if ($score >= 61 && $score <= 80) {
                                                        $grade = 'B'; $bgColor = '#cce5ff';
                                                    } else if ($score >= 41 && $score <= 60) {
                                                        $grade = 'C'; $bgColor = '#fff3cd';
                                                    } else if ($score >= 21 && $score <= 40) {
                                                        $grade = 'D'; $bgColor = '#f8d7da';
                                                    } else if($score >= 0 && $score <= 20) {
                                                        $grade = 'E'; $bgColor = '#e2e3e5';
                                                    } else {
                                                        $grade = 'Error'; $bgColor = '#dc3545';
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="text-capitalize">
                                                {{ ucwords(strtolower($student->first_name)) }}
                                                {{ ucwords(strtolower($student->middle_name)) }}
                                                {{ ucwords(strtolower($student->last_name)) }}
                                            </td>
                                            <td>
                                                <input type="number" name="scores[{{ $student->id }}]"
                                                       class="form-control score-input"
                                                       value="{{ $score }}"
                                                       min="0"
                                                       max="{{ $marking_style == 1 ? '50' : '100' }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control grade-input text-center font-weight-bold"
                                                       value="{{ $grade }}"
                                                       disabled
                                                       style="background-color: {{ $bgColor }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-warning btn-lg px-4 mr-3" name="action" value="save"
                                    onclick="return confirm('Save these results as draft for later editing?')">
                                <i class="fas fa-save mr-2"></i> Save Draft
                            </button>

                            <button type="submit" class="btn btn-success btn-lg px-4 mr-3" name="action" value="submit"
                                    onclick="return confirm('WARNING: Final submission cannot be edited. Are you sure?')">
                                <i class="fas fa-check-circle mr-2"></i> Submit Final Results
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });

        // Initialize flatpickr for date inputs
        $(".flatpickr").flatpickr({
            dateFormat: "Y-m-d",
            maxDate: "today"
        });

        // Grade calculation on score input
        $('.score-input').on('input', function() {
            const row = $(this).closest('tr');
            const score = parseFloat($(this).val());
            const gradeInput = row.find('.grade-input');
            let grade = '';
            let bgColor = '';

            if (isNaN(score)) {
                grade = 'Abs';
                bgColor = '#ffc107';
            } else if ({{ $marking_style }} == 1) { // Points system (0-50)
                if (score >= 41 && score <= 50) {
                    grade = 'A'; bgColor = '#d4edda';
                } else if (score >= 31 && score <= 40) {
                    grade = 'B'; bgColor = '#cce5ff';
                } else if (score >= 21 && score <= 30) {
                    grade = 'C'; bgColor = '#fff3cd';
                } else if (score >= 11 && score <= 20) {
                    grade = 'D'; bgColor = '#f8d7da';
                } else if(score >= 0 && score <= 10) {
                    grade = 'E'; bgColor = '#e2e3e5';
                } else {
                    grade = 'Error'; bgColor = '#dc3545';
                }
            } else if ({{ $marking_style }} == 2) { // Percentage system (0-100)
                if (score >= 81 && score <= 100) {
                    grade = 'A'; bgColor = '#d4edda';
                } else if (score >= 61 && score <= 80) {
                    grade = 'B'; bgColor = '#cce5ff';
                } else if (score >= 41 && score <= 60) {
                    grade = 'C'; bgColor = '#fff3cd';
                } else if (score >= 21 && score <= 40) {
                    grade = 'D'; bgColor = '#f8d7da';
                } else if(score >= 0 && score <= 20) {
                    grade = 'E'; bgColor = '#e2e3e5';
                } else {
                    grade = 'Error'; bgColor = '#dc3545';
                }
            }

            gradeInput.val(grade).css('background-color', bgColor);
        });

        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var form = document.getElementById('resultsForm');
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            }, false);
        })();
    });
</script>
@endsection
