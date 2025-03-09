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
                    <a href="{{ route('home') }}" class="float-right"><i class="fas fa-arrow-circle-left"></i> Back</a>
                </div>
            </div>
            <form class="needs-validation" novalidate="" action="{{ route('score.captured.values') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <!-- Hidden fields -->
                    <input type="hidden" name="course_id" value="{{ $class_course->course_id ?? '' }}">
                    <input type="hidden" name="class_id" value="{{ $class_course->class_id ?? '' }}">
                    <input type="hidden" name="teacher_id" value="{{ $class_course->teacher_id ?? '' }}">
                    <input type="hidden" name="school_id" value="{{ $class_course->school_id ?? '' }}">

                    <!-- Exam Type -->
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom01">Examination</label>
                        <select name="exam_type" id="validationCustom01" class="form-control text-uppercase" required>
                            <option value="">--Select Exam--</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->exam_type }}</option>
                            @endforeach
                        </select>
                        @error('exam_type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Exam Date -->
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom02">Examination Date</label>
                        <input type="date" name="exam_date" class="form-control" id="validationCustom02" placeholder="" required value="{{ old('exam_date') }}" min="{{\Carbon\Carbon::now()->subYears(1)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                        @error('exam_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Exam Term -->
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom02">Examination Term</label>
                        <select name="term" id="validationCustom02" class="form-control text-uppercase" required>
                            <option value="">-- Select Term --</option>
                            <option value="i">Term I</option>
                            <option value="ii">Term II</option>
                        </select>
                        @error('term')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom02">Marking System</label>
                        <select name="marking_style" id="validationCustom02" class="form-control" required>
                            <option value="">-- Select Marking System --</option>
                            <option value="2">Percentage</option>
                            <option value="1" selected>From 0 to 50</option>
                        </select>
                        @error('marking_style')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary float-right" id="saveButton" type="submit">Next Step<i class="ti-arrow-right"></i></button>
            </form>
            <button id="continueWithSavedData" class="btn btn-warning mt-3" style="display:none;">Saved Scores</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const continueWithSavedDataButton = document.getElementById('continueWithSavedData');
        const formKey = 'studentsFormData';

        // Check if there is saved data in local storage
        function checkSavedData() {
            const savedData = localStorage.getItem(formKey);
            if (savedData) {
                continueWithSavedDataButton.style.display = 'block';
            } else {
                continueWithSavedDataButton.style.display = 'none';
            }
        }

        // Call checkSavedData function on page load
        checkSavedData();

        // Event listener for the button to redirect to the form with saved data
        continueWithSavedDataButton.addEventListener('click', () => {
            if (confirm('You have unsubmitted Results. Do you want to continue with the saved data?')) {
                // Redirect to the page with saved data
                window.location.href = "{{ route('form.saved.values') }}";
            }
        });
    });

    //disable button after submission
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector(".needs-validation");
        const submitButton = document.getElementById("saveButton"); // Tafuta button kwa ID

        if (!form || !submitButton) return; // Kama form au button haipo, acha script isifanye kazi

        form.addEventListener("submit", function (event) {
            event.preventDefault(); // Zuia submission ya haraka

            // Disable button na badilisha maandishi
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border text-white" role="status" aria-hidden="true"></span> Please Wait...`;

            // Hakikisha form haina errors kabla ya kutuma
            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                submitButton.disabled = false; // Warudishe button kama kuna errors
                submitButton.innerHTML = "Next Step";
                return;
            }

            // Chelewesha submission kidogo ili button ibadilike kwanza
            setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
</script>
@endsection
