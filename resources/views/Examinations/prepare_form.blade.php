@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-8">
                    <h4 class="header-title text-capitalize">results form | pre-information</h4>
                </div>
                <div class="col-4">
                    <a href="{{ route('home') }}" class="float-right btn btn-info">
                        <i class="fas fa-arrow-circle-left"></i> Back
                    </a>
                </div>
            </div>

            <form class="needs-validation" novalidate action="{{ route('score.captured.values') }}" method="POST">
                @csrf
                <!-- Hidden Fields -->
                <input type="hidden" name="course_id" value="{{ $class_course->course_id }}">
                <input type="hidden" name="class_id" value="{{ $class_course->class_id }}">
                <input type="hidden" name="teacher_id" value="{{ $class_course->teacher_id }}">
                <input type="hidden" name="school_id" value="{{ $class_course->school_id }}">
                <div class="form-row">
                    <!-- Examination -->
                    <div class="col-md-3 mb-3">
                        <label for="exam_type">Examination Type</label>
                        <select name="exam_type" id="exam_type" class="form-control text-capitalize" required>
                            <option value="">-- Select Exam type --</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->exam_type }}</option>
                            @endforeach
                        </select>
                        @error('exam_type')
                        <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Exam Date -->
                    <div class="col-md-3 mb-3">
                        <label for="exam_date">Upload Date</label>
                        <input type="date" name="exam_date" class="form-control" id="exam_date" required
                               value="{{ old('exam_date') }}"
                               min="{{ \Carbon\Carbon::now()->subYears(1)->format('Y-m-d') }}"
                               max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        @error('exam_date')
                        <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Exam Term -->
                    <div class="col-md-3 mb-3">
                        <label for="term">Term</label>
                        <select name="term" id="term" class="form-control text-capitalize" required>
                            <option value="">-- Select Term --</option>
                            <option value="i">Term 1</option>
                            <option value="ii">Term 2</option>
                        </select>
                        @error('term')
                        <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Marking System -->
                    <div class="col-md-3 mb-3">
                        <label for="marking_style">Grading System</label>
                        <select name="marking_style" id="marking_style" class="form-control" required>
                            <option value="">-- Select Grading System --</option>
                            <option value="2">Percentage (0-100%)</option>
                            <option value="1" selected>Points (0-50)</option>
                        </select>
                        @error('marking_style')
                        <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @if ($saved_results->isEmpty())
                    <button class="btn btn-success" id="saveButton" type="submit">
                        Save & Proceed <i class="ti-arrow-right"></i>
                    </button>
                @else
                    <div class="col-12">
                        <button class="btn btn-success" id="saveButton" type="submit">
                            Save & Proceed <i class="ti-arrow-right"></i>
                        </button>
                    </div>
                    <hr>
                    <div class="col-12">
                        <p class="text-danger text-center" style="font-style:italic; font-size:11px;">Results will expire on: {{\Carbon\Carbon::parse($saved_results->first()->expiry_date)->format('d-m-Y  H:i')}}</p>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <a href="{{route('form.saved.values', ['course' => Hashids::encode($class_course->course_id),
                                'teacher' => Hashids::encode($class_course->teacher_id),
                                'school' => Hashids::encode($class_course->school_id),
                                'class' => Hashids::encode($class_course->class_id),
                                'type' => $saved_results->first()->exam_type_id,
                                'date' => $saved_results->first()->exam_date,
                                'term' => $saved_results->first()->exam_term,
                                'style' => $saved_results->first()->marking_style])}}"
                                class="btn btn-warning" onclick="" title="Pending score">Pending Results</a>
                            </div>
                            <div class="col-6">
                                <a href="{{route('results.draft.delete', ['course' => Hashids::encode($class_course->course_id),
                                    'teacher' => Hashids::encode($class_course->teacher_id),
                                    'type' => $saved_results->first()->exam_type_id,
                                    'class' => Hashids::encode($class_course->class_id),
                                    'date' => $saved_results->first()->exam_date])}}" onclick="return confirm('Are you sure you want to delete this pending results? you cannot recover after delete')"
                                    title="Delete score"
                                    class="btn btn-danger float-right">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                @endif
            </form>
        </div>
    </div>
</div>

<script>

</script>
@endsection
