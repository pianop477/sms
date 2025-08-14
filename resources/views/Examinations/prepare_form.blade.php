@extends('SRTDashboard.frame')

@section('content')
<div class="container-fluid py-5">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-gradient-primary text-white py-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="mb-1"><i class="fas fa-clipboard-list mr-2"></i> Results Submission Form</h4>
                    <small class="text-light">Pre-information section for examination results</small>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm shadow-sm">
                    <i class="fas fa-arrow-circle-left mr-1"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <div class="card-body p-4">
            <form class="needs-validation" novalidate action="{{ route('score.captured.values') }}" method="POST">
                @csrf

                <!-- Hidden Fields -->
                <input type="hidden" name="course_id" value="{{ $class_course->course_id }}">
                <input type="hidden" name="class_id" value="{{ $class_course->class_id }}">
                <input type="hidden" name="teacher_id" value="{{ $class_course->teacher_id }}">
                <input type="hidden" name="school_id" value="{{ $class_course->school_id }}">

                <div class="row g-3">
                    <!-- Examination Type -->
                    <div class="col-md-3">
                        <label for="exam_type" class="form-label fw-bold">Examination Type <span class="text-danger">*</span></label>
                        <select name="exam_type" id="exam_type" class="form-select select2" required>
                            <option value="" disabled selected>-- Select Exam type --</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}" {{ old('exam_type') == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->exam_type }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select an examination type</div>
                        @error('exam_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Exam Date -->
                    <div class="col-md-3">
                        <label for="exam_date" class="form-label fw-bold">Upload Date <span class="text-danger">*</span></label>
                        <input type="date" name="exam_date" class="form-control flatpickr" id="exam_date" required
                            value="{{ old('exam_date') }}"
                            min="{{ \Carbon\Carbon::now()->subYears(1)->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        <div class="invalid-feedback">Please provide a valid exam date</div>
                        @error('exam_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Academic Term -->
                    <div class="col-md-3">
                        <label for="term" class="form-label fw-bold">Academic Term <span class="text-danger">*</span></label>
                        <select name="term" id="term" class="form-select select2" required>
                            <option value="" disabled selected>-- Select Term --</option>
                            <option value="i" {{ old('term') == 'i' ? 'selected' : '' }}>Term 1</option>
                            <option value="ii" {{ old('term') == 'ii' ? 'selected' : '' }}>Term 2</option>
                        </select>
                        <div class="invalid-feedback">Please select an academic term</div>
                        @error('term') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Grading System -->
                    <div class="col-md-3">
                        <label for="marking_style" class="form-label fw-bold">Grading System <span class="text-danger">*</span></label>
                        <select name="marking_style" id="marking_style" class="form-select select2" required>
                            <option value="" disabled selected>-- Select Grading System --</option>
                            <option value="2" {{ old('marking_style') == '2' ? 'selected' : '' }}>Percentage (0-100%)</option>
                            <option value="1" {{ (old('marking_style') ?: '1') == '1' ? 'selected' : '' }}>Points (0-50)</option>
                        </select>
                        <div class="invalid-feedback">Please select a grading system</div>
                        @error('marking_style') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-5 border-top pt-4">
                    @if ($saved_results->isEmpty())
                        <button class="btn btn-primary btn-lg shadow-sm" id="saveButton" type="submit">
                            <i class="fas fa-save me-2"></i> Save & Proceed
                        </button>
                    @else
                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-3">
                            <div>
                                <button class="btn btn-primary shadow-sm" id="saveButton" type="submit">
                                    <i class="fas fa-save me-2"></i> Save & Proceed
                                </button>
                                <span class="badge bg-warning text-dark ms-2 p-2">
                                    <i class="fas fa-clock me-1"></i> Expires: {{\Carbon\Carbon::parse($saved_results->first()->expiry_date)->format('d M Y H:i')}}
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('form.saved.values', [
                                    'course' => Hashids::encode($class_course->course_id),
                                    'teacher' => Hashids::encode($class_course->teacher_id),
                                    'school' => Hashids::encode($class_course->school_id),
                                    'class' => Hashids::encode($class_course->class_id),
                                    'type' => $saved_results->first()->exam_type_id,
                                    'date' => $saved_results->first()->exam_date,
                                    'term' => $saved_results->first()->exam_term,
                                    'style' => $saved_results->first()->marking_style
                                ]) }}" class="btn btn-warning shadow-sm">
                                    <i class="fas fa-edit me-1"></i> Edit Pending Results
                                </a>
                                <a href="{{ route('results.draft.delete', [
                                    'course' => Hashids::encode($class_course->course_id),
                                    'teacher' => Hashids::encode($class_course->teacher_id),
                                    'type' => $saved_results->first()->exam_type_id,
                                    'class' => Hashids::encode($class_course->class_id),
                                    'date' => $saved_results->first()->exam_date
                                ]) }}"
                                onclick="return confirm('Are you sure you want to permanently delete these pending results? This action cannot be undone.')"
                                class="btn btn-danger shadow-sm">
                                    <i class="fas fa-trash-alt me-1"></i> Delete Draft
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(45deg, #007bff, #0056d2);
    }
    .form-label {
        font-weight: 600;
    }
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding-top: 5px;
    }
</style>

<script>
    $(function() {
        $('.select2').select2({ width: '100%' });
        $(".flatpickr").flatpickr({ dateFormat: "Y-m-d", maxDate: "today" });

        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    });
</script>
@endsection
