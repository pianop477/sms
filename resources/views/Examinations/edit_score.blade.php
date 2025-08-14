@extends('SRTDashboard.frame')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10 mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h4 class="mb-3 mb-md-0 text-center text-md-left">Edit Saved Results - {{ ucwords(strtoupper($courseName)) }} ({{ ucwords(strtoupper($className)) }})</h4>
                        <a href="{{route('home')}}" class="btn btn-light text-primary">
                            <i class="fas fa-arrow-circle-left mr-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body">
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
                    <form action="{{ route('results.update.draft') }}" method="POST" id="resultsForm">
                        @csrf
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
                        <input type="hidden" name="course_id" value="{{ $courseId }}">
                        <input type="hidden" name="class_id" value="{{ $classId }}">
                        <input type="hidden" name="teacher_id" value="{{ $teacherId }}">
                        <input type="hidden" name="school_id" value="{{ $schoolId }}">
                        {{-- <input type="hidden" name="exam_type_id" value="{{ $examTypeId }}"> --}}
                        {{-- <input type="hidden" name="exam_date" value="{{ \Carbon\Carbon::parse($examDate)->format('Y-m-d') }}"> --}}
                        <input type="hidden" name="term" value="{{ $draftResults->first()->exam_term }}">
                        <input type="hidden" name="marking_style" value="{{ $marking_style }}">
                        <p class="text-center text-danger"><i>(Enter score from 0 to {{ $marking_style == 1 ? '50' : '100' }} correctly)</i></p>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="45%">Student Name</th>
                                    <th width="25%">Score (0-{{ $marking_style == 1 ? '50' : '100' }})</th>
                                    <th width="25%">Grade</th> <!-- Added Grade Column -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
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
                                                } else if ($score >= 31 && $score <= 40) {
                                                    $grade = 'B';
                                                    $bgColor = '#4edcdc';
                                                } else if ($score >= 21 && $score <= 30) {
                                                    $grade = 'C';
                                                    $bgColor = '#e9f0aa';
                                                } else if ($score >= 11 && $score <= 20) {
                                                    $grade = 'D';
                                                    $bgColor = '#ef8f8f';
                                                } else if($score >= 0 && $score <= 10) {
                                                    $grade = 'E';
                                                    $bgColor = '#ebc4f3';
                                                } else {
                                                    $grade = 'Error';
                                                    $bgColor = 'red';
                                                }
                                            } else if ($marking_style == 2) {
                                                if ($score >= 81 && $score <= 100) {
                                                    $grade = 'A';
                                                    $bgColor = '#97e897';
                                                } else if ($score >= 61 && $score <= 80) {
                                                    $grade = 'B';
                                                    $bgColor = '#4edcdc';
                                                } else if ($score >= 41 && $score <= 60) {
                                                    $grade = 'C';
                                                    $bgColor = '#e9f0aa';
                                                } else if ($score >= 21 && $score <= 40) {
                                                    $grade = 'D';
                                                    $bgColor = '#ef8f8f';
                                                } else if($score >= 0 && $score <= 20) {
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
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-capitalize">{{ ucwords(strtolower($student->first_name))}} {{ucwords(strtolower($student->middle_name))}} {{ucwords(strtolower($student->last_name)) }}</td>
                                        <td>
                                            <input type="number" style="width: auto;" name="scores[{{ $student->id }}]" class="form-control score-input" value="{{ $score }}" min="0" max="100">
                                        </td>
                                        <td>
                                            <input type="text" style="width: 50px;" class="form-control grade-input" value="{{ $grade }}" disabled style="background-color: {{ $bgColor }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="d-flex justify-content-center my-3">
                            <!-- Save to Draft -->
                            <button type="submit" class="btn btn-warning mr-3" name="action" value="save" onclick="return confirm('Are you sure you want to save results to draft?')">
                                <i class="fas fa-save"></i> Save as Draft
                            </button>

                            <!-- Submit Final Results -->
                            <button type="submit" class="btn btn-success" name="action" value="submit" id="submitButton" onclick="return confirm('Are you sure you want to submit final results? No editing will be allowed')">
                                <i class="fas fa-check"></i> Submit Final Results
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const scoreInputs = document.querySelectorAll('.score-input');
        const gradeInputs = document.querySelectorAll('.grade-input');

        scoreInputs.forEach((input, index) => {
            input.addEventListener('blur', () => {
                const score = parseFloat(input.value);
                let grade = '';
                let bgColor = '';

                if (isNaN(score)) {
                    grade = 'Abs';
                    bgColor = 'orange';
                } else if ({{ $marking_style }} == 1) {
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
                    } else if(score >= 0 && score <= 10) {
                        grade = 'E';
                        bgColor = '#ebc4f3';
                    } else {
                        grade = 'Error';
                        bgColor = 'red';
                    }
                } else if ({{ $marking_style }} == 2) {
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
                    } else if(score >= 0 && score <= 20) {
                        grade = 'E';
                        bgColor = '#ebc4f3';
                    } else {
                        grade = 'Error';
                        bgColor = 'red';
                    }
                }

                gradeInputs[index].value = grade;
                gradeInputs[index].style.backgroundColor = bgColor;
            });
        });
    });
</script>

@endsection
