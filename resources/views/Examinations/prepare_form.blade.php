@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-8">
                    <h4 class="header-title">Fill the Result Form</h4>
                </div>
                <div class="col-4">
                    <a href="{{ route('home') }}" class="float-right">
                        <i class="fas fa-arrow-circle-left"></i> Back
                    </a>
                </div>
            </div>

            <form class="needs-validation" novalidate action="{{ route('score.captured.values') }}" method="POST">
                @csrf
                <!-- Hidden Fields -->
                <input type="hidden" name="course_id" value="{{ $class_course->course_id ?? '' }}">
                <input type="hidden" name="class_id" value="{{ $class_course->class_id ?? '' }}">
                <input type="hidden" name="teacher_id" value="{{ $class_course->teacher_id ?? '' }}">
                <input type="hidden" name="school_id" value="{{ $class_course->school_id ?? '' }}">

                <div class="form-row">
                    <!-- Examination -->
                    <div class="col-md-3 mb-3">
                        <label for="exam_type">Examination</label>
                        <select name="exam_type" id="exam_type" class="form-control text-uppercase" required>
                            <option value="">-- Select Exam --</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->exam_type }}</option>
                            @endforeach
                        </select>
                        @error('exam_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Exam Date -->
                    <div class="col-md-3 mb-3">
                        <label for="exam_date">Examination Date</label>
                        <input type="date" name="exam_date" class="form-control" id="exam_date" required
                               value="{{ old('exam_date') }}"
                               min="{{ \Carbon\Carbon::now()->subYears(1)->format('Y-m-d') }}"
                               max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        @error('exam_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Exam Term -->
                    <div class="col-md-3 mb-3">
                        <label for="term">Examination Term</label>
                        <select name="term" id="term" class="form-control text-uppercase" required>
                            <option value="">-- Select Term --</option>
                            <option value="i">Term I</option>
                            <option value="ii">Term II</option>
                        </select>
                        @error('term')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Marking System -->
                    <div class="col-md-3 mb-3">
                        <label for="marking_style">Marking System</label>
                        <select name="marking_style" id="marking_style" class="form-control" required>
                            <option value="">-- Select Marking System --</option>
                            <option value="2">Percentage</option>
                            <option value="1" selected>From 0 to 50</option>
                        </select>
                        @error('marking_style')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @if ($saved_results->isEmpty())
                    <button class="btn btn-primary float-right" id="saveButton" type="submit">
                        Next Step <i class="ti-arrow-right"></i>
                    </button>
                @else
                    <div class="row">
                        <div class="col-6">
                           <div class="row">
                            <div class="col-sm-12">
                                <span class="text-danger" style="font-size: 10px; font-style:italic">Expiry Date: {{\Carbon\Carbon::parse($saved_results->first()->expiry_date)->format('d-m-Y  H:i:s')}}</span>
                            </div>
                            <div class="row">
                                <div class="col-sm-8">
                                    <a href=""
                                        class="btn btn-warning" onclick="">Saved Scores</a>
                                </div>
                                {{-- <div class="col-4 mt-3">
                                    <a href="{{route('results.draft.delete', ['course' => Hashids::encode($class_course->course_id),
                                                'teacher' => Hashids::encode($class_course->teacher_id),
                                                'type' => $saved_results->first()->exam_type_id])}}" onclick="return confirm('Are you sure you want to delete this results? you will not able to recover it')">
                                        <i class="fas fa-trash text-danger" style="font-size: 1.2rem;"></i>
                                    </a>
                                </div> --}}
                            </div>
                           </div>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-primary float-right" id="saveButton" type="submit">
                                Next Step <i class="ti-arrow-right"></i>
                            </button>
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
