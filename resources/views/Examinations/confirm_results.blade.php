@extends('SRTDashboard.frame')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card col-md-6 mt-5">
            <div class="card-body text-center">
                <h4 class="card-title">Pending Saved Results</h4>
                <p class="card-text text-danger">
                    You have pending results which are not submitted. Do you want to proceed with pending results?
                </p>
                @if(isset($saved_results))
                <form action="{{ route('results.edit.draft') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $saved_results['courseId'] ?? $courseId }}">
                    <input type="hidden" name="class_id" value="{{ $saved_results['classId'] ?? $classId }}">
                    <input type="hidden" name="teacher_id" value="{{ $saved_results['teacherId'] ?? $teacherId }}">
                    <input type="hidden" name="school_id" value="{{ $saved_results['schoolId'] ?? $schoolId }}">
                    <input type="hidden" name="exam_type_id" value="{{ $saved_results['examTypeId'] ?? $examTypeId }}">
                    <input type="hidden" name="exam_date" value="{{ $saved_results['examDate'] ?? $examDate }}">
                    <input type="hidden" name="term" value="{{ $saved_results['term'] ?? $term }}">
                    <input type="hidden" name="marking_style" value="{{ $saved_results['marking_style'] ?? $marking_style }}">

                    <button type="submit" class="btn btn-success" id="saveButton">Yes, Proceed</button>
                    <a href="{{route('score.prepare.form', ['id' => Hashids::encode($saved_results['courseId'] ?? $courseId)])}}" class="btn btn-danger">No, Cancel</a>
                </form>
            @else
                <p class="text-danger">No saved results found.</p>
            @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector(".needs-validation");
        const submitButton = document.getElementById("saveButton");

        if (!form || !submitButton) return;

        form.addEventListener("submit", function (event) {
            event.preventDefault();

            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Please Wait...`;

            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                submitButton.disabled = false;
                submitButton.innerHTML = "Yes, proceed";
                return;
            }

            setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
</script>
@endsection
