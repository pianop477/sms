@extends('SRTDashboard.frame')
@section('content')

{{-- **Hakikisha hii fomu ya tarehe inabaki bila kujali attendance** --}}
<div class="row">
    <div class="col-md-4 p-2">
        <form method="GET" action="{{ route('get.student.list', ['class' => Hashids::encode($myClass->first()->id)]) }}" class="needs-validation" novalidate>
            <label for="attendance_date">Select Date:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="date" id="attendance_date" name="attendance_date"
                    value="{{ request()->input('attendance_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                    max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                    min="{{ \Carbon\Carbon::now()->subWeek()->format('Y-m-d') }}"
                    class="form-control p-2" required>
            </div>
        </form>
    </div>
</div>

{{-- **Hapa ndipo ujumbe wa attendance utaonekana, lakini fomu ya tarehe inabaki juu** --}}
@if ($attendanceExists)
    <div class="alert alert-success text-center mt-3">
        <h6>Attendance for {{ \Carbon\Carbon::parse($selectedDate)->format('d-m-Y') }} has already been submitted.</h6>
        <hr>
        <p><a href="{{ route('home') }}" class="btn btn-primary btn-sm">Go Back</a></p>
    </div>
@else
    {{-- Ikiwa attendance haipo, onyesha fomu ya attendance --}}
    <form id="attendanceForm" action="{{ route('store.attendance', ['student_class' => Hashids::encode($student_class->id)]) }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">
        <div class="table-responsive-md">
            <table class="table">
                <thead class="text-capitalize bg-info">
                    <tr class="text-white">
                        <th>Name</th>
                        <th class="text-center">Sex</th>
                        <th class="text-center">Attendance Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($studentList as $student)
                        <tr>
                            <input type="hidden" name="student_id[]" value="{{ $student->id }}">
                            <td>
                                <a href="{{ route('Students.show', ['student' => Hashids::encode($student->id)]) }}">
                                    {{ ucwords(strtolower($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name)) }}
                                </a>
                            </td>
                            <td class="text-uppercase text-center">{{ $student->gender[0] }}</td>
                            <input type="hidden" name="group[{{ $student->id }}]" value="{{ $student->group }}">
                            <td>
                                <ul class="d-flex justify-content-center">
                                    <li class="mr-3">
                                        <input type="radio" name="attendance_status[{{ $student->id }}]" required value="present" {{ old('attendance_status.' . $student->id) == 'present' ? 'checked' : '' }}> Pres
                                    </li>
                                    <li class="mr-3">
                                        <input type="radio" name="attendance_status[{{ $student->id }}]" value="absent" {{ old('attendance_status.' . $student->id) == 'absent' ? 'checked' : '' }}> Abs
                                    </li>
                                    <li class="mr-3">
                                        <input type="radio" name="attendance_status[{{ $student->id }}]" value="permission" {{ old('attendance_status.' . $student->id) == 'permission' ? 'checked' : '' }}> Perm
                                    </li>
                                </ul>
                                @error('attendance_status.' . $student->id)
                                    <span class="text-sm text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Submit Button --}}
        <div class="card-footer text-center">
            <ul class="d-flex justify-content-center">
                <li class="mr-3">
                    <button type="submit" id="saveButton" class="btn btn-primary">
                        Submit
                    </button>
                </li>
                <li class="mr-3">
                    <a href="{{ route('today.attendance', ['student_class' => Hashids::encode($student_class->id)]) }}" class="btn btn-success">Today Report</a>
                </li>
            </ul>
        </div>
    </form>
@endif
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const attendanceDateInput = document.getElementById("attendance_date");

        // Unda preloader kwa JavaScript ikiwa haipo
        let preloader = document.createElement("div");
        preloader.id = "preloader";
        preloader.style.position = "fixed";
        preloader.style.top = "0";
        preloader.style.left = "0";
        preloader.style.width = "100%";
        preloader.style.height = "100%";
        preloader.style.backgroundColor = "rgba(255, 255, 255, 0.8)";
        preloader.style.display = "none"; // Default hidden
        preloader.style.justifyContent = "center";
        preloader.style.alignItems = "center";
        preloader.innerHTML = `<div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                            </div>`;
        document.body.appendChild(preloader);

        attendanceDateInput.addEventListener("change", function () {
            preloader.style.display = "flex"; // Onyesha preloader
            setTimeout(() => {
                window.location.href = "?attendance_date=" + this.value;
            }, 500); // Chelewesha kidogo kwa UX bora
        });

        // Zima preloader baada ya page kupakia
        window.addEventListener("load", function () {
            preloader.style.display = "none";
        });
    });

    // Disable button after form submission
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("attendanceForm"); // Tumia ID ya form
        const submitButton = document.getElementById("saveButton");

        if (!form || !submitButton) return;

        form.addEventListener("submit", function (event) {
            event.preventDefault(); // Zuia submission ya haraka

            // Thibitisha kuwa kila mwanafunzi ana radio button iliyochaguliwa
            let isValid = true;
            document.querySelectorAll("tbody tr").forEach((row) => {
                const studentId = row.querySelector("input[name^='student_id']").value;
                const radios = row.querySelectorAll(`input[name="attendance_status[${studentId}]"]`);
                const checked = [...radios].some(radio => radio.checked);

                if (!checked) {
                    isValid = false;
                    row.style.backgroundColor = "#f8d7da"; // Rangi nyekundu ikiwa haijachaguliwa
                } else {
                    row.style.backgroundColor = ""; // Rudisha rangi ya kawaida
                }
            });

            if (!isValid) {
                alert("Please select attendance status for all students.");
                return; // Acha submission ikiwa kuna errors
            }

            // Lemaza button na badilisha maandishi
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Submitting...`;

            // Endelea na submission baada ya kuchelewesha kidogo
            setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
</script>
@endsection
