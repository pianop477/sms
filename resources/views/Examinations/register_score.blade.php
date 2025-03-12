@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <!-- Header and form code remains the same -->
                <div class="row">
                    <div class="col-10">
                        <h3 class="header-title text-uppercase text-center">Students Result Form</h3>
                    </div>
                    <div class="col-2">
                        <a href="{{ route('score.prepare.form', ['id' => Hashids::encode($courseId)]) }}" class="float-right">
                            <i class="fas fa-arrow-circle-left text-secondary" style="font-size: 1.7rem;"></i>
                        </a>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <ul>
                            <li>
                                <p>
                                    Class Code: <span class="text-uppercase fw-bold"><strong>{{ $className }}</strong></span>
                                </p>
                            </li>
                            <li>
                                <p>
                                    Course Code: <span class="text-uppercase fw-bold"><strong>{{ $courseName }}</strong></span>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <ul>
                            <li>
                                <p>
                                    Exam Type: <span class="text-uppercase fw-bold"><strong>{{ $examName }}</strong></span>
                                </p>
                            </li>
                            <li>
                                <p>
                                    Exam Date: <span class="text-uppercase"><strong>{{ \Carbon\Carbon::parse($examDate)->format('d-M-Y') }}</strong></span>
                                </p>
                            </li>
                            <li>
                                <p>
                                    Term: <span class="text-uppercase"><strong>{{ $term }}</strong></span>
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
                <hr>
                <h5 class="text-center">Examination Scores</h5>
                @if ($marking_style == 1)
                    <p class="text-center text-danger"><i>(Enter score from 0 to 50 correctly)</i></p>
                @else
                    <p class="text-center text-danger"><i>(Enter score from 0 to 100 correctly)</i></p>
                @endif
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
                            <table class="table table-responsive-md table-hover table-bordered" style="width: 100%;">
                                <thead class="table-primary">
                                    <th style="width: 5px">S/N</th>
                                    <th class="text-center">Admission No.</th>
                                    <th>Students Name</th>
                                    <th style="width: ">Score</th>
                                    <th style="width: ">Grade</th>
                                </thead>
                                <tbody id="studentsTableBody">
                                    @if ($students->isEmpty())
                                        <tr>
                                            <td colspan="5" class="alert alert-warning text-center">No students records found!</td>
                                        </tr>
                                    @else
                                        @foreach ($students as $student)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-center text-uppercase">
                                                    {{ $student->admission_number }}
                                                    <input type="hidden" name="students[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                                </td>
                                                <td class="text-capitalize">
                                                    {{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}
                                                </td>
                                                <td>
                                                    <input type="number" required class="form-control score-input" name="students[{{ $loop->index }}][score]" placeholder="Score", value="{{old('score')}}">
                                                </td>
                                                <td>
                                                    <input type="text" disabled name="students[{{ $loop->index }}][grade]" class="form-control grade-input">
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <hr>
                            <ul class="d-flex justify-content-center my-3">
                                <li class="mr-3">
                                    <button type="button" class="btn btn-danger" id="saveToLocalStorage">Save</button>
                                </li>
                                <li>
                                    <button type="submit" class="btn btn-primary" id="saveButton" onclick="return confirm('Are you sure you want to submit the results? You will not be able to make any changes after submission')">Submit</button>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .score-input {
        width: 80px;
    }

    @media (max-width: 576px) {
        .score-input {
            width: 70px;
        }
    }
    .grade-input {
        width: 70px;
    }

    @media(max-width:576px) {
        .grade-input {
            width: 80px;
        }
    }
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const scoreForm = document.getElementById('scoreForm');
    const saveButton = document.getElementById('saveToLocalStorage');
    const studentsTableBody = document.getElementById('studentsTableBody');
    const formKey = 'studentsFormData';

    // Load data from local storage or session
    loadDataFromLocalStorageOrSession();

    // Save data to local storage when clicking the "Save" button
    saveButton.addEventListener('click', () => {
        if (confirm('Are you sure you want to save these results? Remember to submit the results after you complete the process')) {
            saveDataToLocalStorage();
        }
    });

    function saveDataToLocalStorage() {
        const scores = {};
        const scoreInputs = studentsTableBody.querySelectorAll('.score-input');
        const gradeInputs = studentsTableBody.querySelectorAll('.grade-input');

        scoreInputs.forEach((input, index) => {
            const studentId = input.closest('tr').querySelector('input[name^="students["]').value;
            const score = input.value;
            const grade = gradeInputs[index].value;

            if (studentId) {
                scores[studentId] = { score, grade };
            }
        });

        localStorage.setItem(formKey, JSON.stringify(scores));
        alert('Data has been saved to your browser.');
    }

    function loadDataFromLocalStorageOrSession() {
        const savedData = localStorage.getItem(formKey) || ({{ isset($savedData) ? json_encode($savedData) : 'null' }});

        if (savedData) {
            const formData = JSON.parse(savedData);

            Object.keys(formData).forEach(studentId => {
                const { score, grade } = formData[studentId];
                const row = studentsTableBody.querySelector(`input[name^="students"][value="${studentId}"]`).closest('tr');
                const scoreInput = row.querySelector('.score-input');
                const gradeInput = row.querySelector('.grade-input');

                if (scoreInput) {
                    if (score) {
                        scoreInput.value = score;
                        scoreInput.disabled = true; // Disable the input if a value is present
                    } else {
                        scoreInput.value = ''; // Ensure the input is empty if no value is present
                        scoreInput.disabled = false; // Allow editing if no value is present
                    }
                }

                if (gradeInput) {
                    gradeInput.value = grade;
                    gradeInput.disabled = true;
                }
            });
        }
    }

        // Clear local storage when the form is submitted
        scoreForm.addEventListener('submit', () => {
            // Temporarily enable all score inputs
            const scoreInputs = studentsTableBody.querySelectorAll('.score-input');
            scoreInputs.forEach(input => input.disabled = false);

            localStorage.removeItem(formKey);
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
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Please Wait...`;

            // Hakikisha form haina errors kabla ya kutuma
            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                submitButton.disabled = false; // Warudishe button kama kuna errors
                submitButton.innerHTML = "Submit";
                return;
            }

            // Chelewesha submission kidogo ili button ibadilike kwanza
            setTimeout(() => {
                form.submit();
            }, 500);
        });
    });

    //save score
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector(".needs-validation");
        const submitButton = document.getElementById("saveToLocal"); // Tafuta button kwa ID

        if (!form || !submitButton) return; // Kama form au button haipo, acha script isifanye kazi

        form.addEventListener("submit", function (event) {
            event.preventDefault(); // Zuia submission ya haraka

            // Disable button na badilisha maandishi
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Please Wait...`;

            // Hakikisha form haina errors kabla ya kutuma
            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                submitButton.disabled = false; // Warudishe button kama kuna errors
                submitButton.innerHTML = "Save";
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
