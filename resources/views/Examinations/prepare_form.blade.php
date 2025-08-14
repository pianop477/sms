@extends('SRTDashboard.frame')

@section('content')
<div class="col-12">
    <div class="card mt-5 shadow-sm">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row align-items-center mb-4">
                <div class="col-md-8">
                    <h4 class="header-title text-primary text-capitalize mb-0">
                        <i class="fas fa-clipboard-list mr-2"></i>Results Submission Form
                    </h4>
                    <p class="text-muted mb-0">Pre-information section for examination results</p>
                </div>
                <div class="col-md-4 text-right">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-circle-left mr-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Form Section -->
            <form class="needs-validation" novalidate action="{{ route('score.captured.values') }}" method="POST">
                @csrf

                <!-- Hidden Fields -->
                <input type="hidden" name="course_id" value="{{ $class_course->course_id }}">
                <input type="hidden" name="class_id" value="{{ $class_course->class_id }}">
                <input type="hidden" name="teacher_id" value="{{ $class_course->teacher_id }}">
                <input type="hidden" name="school_id" value="{{ $class_course->school_id }}">

                <div class="form-row">
                    <!-- Examination Type -->
                    <div class="col-md-3 mb-3">
                        <label for="exam_type" class="font-weight-bold">Examination Type <span class="text-danger">*</span></label>
                        <select name="exam_type" id="exam_type" class="form-control select2" required>
                            <option value="" disabled selected>-- Select Exam type --</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}" {{ old('exam_type') == $exam->id ? 'selected' : '' }}>
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
                    <div class="col-md-3 mb-3">
                        <label for="exam_date" class="font-weight-bold">Upload Date <span class="text-danger">*</span></label>
                        <input type="date" name="exam_date" class="form-control flatpickr" id="exam_date" required
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
                    <div class="col-md-3 mb-3">
                        <label for="term" class="font-weight-bold">Academic Term <span class="text-danger">*</span></label>
                        <select name="term" id="term" class="form-control select2" required>
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
                    <div class="col-md-3 mb-3">
                        <label for="marking_style" class="font-weight-bold">Grading System <span class="text-danger">*</span></label>
                        <select name="marking_style" id="marking_style" class="form-control select2" required>
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

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    @if ($saved_results->isEmpty())
                        <button class="btn btn-primary" id="saveButton" type="submit">
                            <i class="fas fa-save mr-2"></i>Save & Proceed <i class="ti-arrow-right ml-1"></i>
                        </button>
                    @else
                        <div class="d-flex flex-column w-100">
                            <div class="d-flex justify-content-between mb-3">
                                <button class="btn btn-primary" id="saveButton" type="submit">
                                    <i class="fas fa-save mr-2"></i>Save & Proceed <i class="ti-arrow-right ml-1"></i>
                                </button>

                                <div class="text-right">
                                    <span class="badge badge-warning p-2">
                                        <i class="fas fa-clock mr-1"></i> Expires: {{\Carbon\Carbon::parse($saved_results->first()->expiry_date)->format('d M Y H:i')}}
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{route('form.saved.values', [
                                    'course' => Hashids::encode($class_course->course_id),
                                    'teacher' => Hashids::encode($class_course->teacher_id),
                                    'school' => Hashids::encode($class_course->school_id),
                                    'class' => Hashids::encode($class_course->class_id),
                                    'type' => $saved_results->first()->exam_type_id,
                                    'date' => $saved_results->first()->exam_date,
                                    'term' => $saved_results->first()->exam_term,
                                    'style' => $saved_results->first()->marking_style
                            ])}}" class="btn btn-warning">
                                    <i class="fas fa-edit mr-1"></i> Edit Pending Results
                                </a>

                                <a href="{{route('results.draft.delete', [
                                    'course' => Hashids::encode($class_course->course_id),
                                    'teacher' => Hashids::encode($class_course->teacher_id),
                                    'type' => $saved_results->first()->exam_type_id,
                                    'class' => Hashids::encode($class_course->class_id),
                                    'date' => $saved_results->first()->exam_date
                                ])}}"
                                onclick="return confirm('Are you sure you want to permanently delete these pending results? This action cannot be undone.')"
                                class="btn btn-danger">
                                    <i class="fas fa-trash-alt mr-1"></i> Delete Draft
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
