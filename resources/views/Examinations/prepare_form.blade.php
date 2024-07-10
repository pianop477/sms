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
                    <a href="{{route('results_byCourse', $courses->id)}}" class="float-right btn-xs btn btn-success"><i class="ti-eye"></i> Results</a>
                </div>
            </div>
            <form class="needs-validation" novalidate="" action="{{route('score.captured.values')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <!-- Hidden fields -->
                    <input type="hidden" name="course_id" value="{{$courses->id}}">
                    <input type="hidden" name="class_id" value="{{$courses->class_id}}">
                    <input type="hidden" name="teacher_id" value="{{$courses->teacher_id}}">
                    <input type="hidden" name="school_id" value="{{$courses->school_id}}">

                    <!-- Exam Type -->
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">Examination</label>
                        <select name="exam_type" id="validationCustom01" class="form-control text-uppercase" required>
                            <option value="">--Select Exam--</option>
                            @foreach ($exams as $exam)
                                <option value="{{$exam->id}}">{{$exam->exam_type}}</option>
                            @endforeach
                        </select>
                        @error('exam_type')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>

                    <!-- Exam Date -->
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom02">Examination Date</label>
                        <input type="date" name="exam_date" class="form-control" id="validationCustom02" placeholder="" required="" value="{{old('exam_date')}}">
                        @error('exam_date')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>

                    <!-- Exam Term -->
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom02">Examination Term</label>
                        <select name="term" id="validationCustom02" class="form-control text-uppercase" required>
                            <option value="">-- select term --</option>
                            <option value="i">term i</option>
                            <option value="ii">term ii</option>
                        </select>
                        @error('term')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom02">Marking System</label>
                        <select name="marking_style" id="validationCustom02" class="form-control" required>
                            <option value="">-- Select Marking System --</option>
                            <option value="2">Percentage</option>
                            <option value="1">From 0 to 50</option>
                        </select>
                        @error('marking_style')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary float-right" type="submit">Next <i class="ti-arrow-right"></i></button>
            </form>
        </div>
    </div>
</div>
@endsection
