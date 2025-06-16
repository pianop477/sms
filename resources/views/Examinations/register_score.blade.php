@extends('SRTDashboard.frame')
@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h3 class="header-title text-uppercase text-center">Students Result Form</h3>
                    </div>
                    <div class="col-2 text-right">
                        <a href="{{ route('home') }}" class="btn btn-info float-right">
                            <i class="fas fa-arrow-circle-left" style=""></i> Back
                        </a>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <ul>
                            <li><p>Class Code: <strong class="text-uppercase">{{ $className }}</strong></p></li>
                            <li><p>Course Code: <strong class="text-uppercase">{{ $courseName }}</strong></p></li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <ul>
                            <li><p>Exam Type: <strong class="text-uppercase">{{ $examName }}</strong></p></li>
                            <li><p>Exam Date: <strong class="text-uppercase">{{ \Carbon\Carbon::parse($examDate)->format('d-M-Y') }}</strong></p></li>
                            <li><p>Term: <strong class="text-uppercase">{{ $term }}</strong></p></li>
                        </ul>
                    </div>
                </div>
                <hr>
                <h5 class="text-center">Examination Scores</h5>
                <p class="text-center text-danger"><i>(Enter score from 0 to {{ $marking_style == 1 ? '50' : '100' }} correctly)</i></p>
                <hr>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row">
                    <div class="col-12">
                        <form id="scoreForm" action="{{ route('exams.store.score') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" name="course_id" value="{{$courseId}}">
                            <input type="hidden" name="class_id" value="{{$classId}}">
                            <input type="hidden" name="teacher_id" value="{{$teacherId}}">
                            <input type="hidden" name="school_id" value="{{$schoolId}}">
                            {{-- <input type="hidden" name="exam_id" value="{{$examTypeId}}"> --}}
                            {{-- <input type="hidden" name="exam_date" value="{{$examDate}}"> --}}
                            <input type="hidden" name="term" value="{{$term}}">
                            <input type="hidden" name="marking_style" value="{{$marking_style}}">
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Exam Type</label>
                                    <select name="exam_id" id="exam_type" class="form-control text-capitalize" required>
                                        <option value="">-- Select Exam type --</option>
                                        @foreach ($exams as $exam)
                                            <option value="{{ $exam->id }}" {{ $exam->id == $examTypeId ? 'selected' : '' }}>{{ $exam->exam_type }}</option>
                                        @endforeach
                                    </select>
                                    @error('exam_type')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-2"></div>
                                <div class="col-md-4">
                                    <label for="">Uploading Date</label>
                                    <input type="date" name="exam_date" class="form-control" id="exam_date" required
                                        value="{{ \Carbon\Carbon::parse($examDate)->format('Y-m-d') }}"
                                        min="{{ \Carbon\Carbon::now()->subYears(1)->format('Y-m-d') }}"
                                        max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    @error('exam_date')
                                    <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <table class="table table-responsive-md table-hover table-bordered w-100">
                                <thead class="table-primary">
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Score</th>
                                    <th>Grade</th>
                                </thead>
                                <tbody id="studentsTableBody">
                                    @forelse ($students as $student)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <input type="hidden" name="students[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                            <td class="text-capitalize">{{ ucwords(strtolower($student->first_name)) }} {{ucwords(strtolower($student->middle_name))}} {{ucwords(strtolower($student->last_name))}}</td>
                                            <td><input type="number" class="form-control score-input" name="students[{{ $loop->index }}][score]" placeholder="Score" value="{{ old('score') }}"></td>
                                            <td><input type="text" disabled name="students[{{ $loop->index }}][grade]" class="form-control grade-input"></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="alert alert-warning text-center">No student records found!</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <hr>
                            <div class="d-flex justify-content-center my-3">
                                <!-- Save Button -->
                                <button type="submit" class="btn btn-warning mr-3" name="action" value="save" id="saveButton" onclick="return confirm('Are you sure you want to save results temporarily?')">
                                    <i class="fas fa-save"></i> Save as Draft
                                </button>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-success" name="action" value="submit" id="submitButton" onclick="return confirm('Are you sure you want to submit the final results? No editing will be allowed')">
                                    <i class="fas fa-check"></i> Submit Final Results
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .score-input, .grade-input { width: 80px; }
    @media (max-width: 576px) { .score-input, .grade-input { width: 70px; } }
</style>

    @if ($marking_style == 1)
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
                    } else if (score >= 41 && score <= 50) {
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

                    gradeInputs[index].value = grade;
                    gradeInputs[index].style.backgroundColor = bgColor;
                });
            });
        });
    </script>
@else
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
                    } else if (score >= 81 && score <= 100) {
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

                    gradeInputs[index].value = grade;
                    gradeInputs[index].style.backgroundColor = bgColor;
                });
            });
        });
    </script>
@endif
@endsection
