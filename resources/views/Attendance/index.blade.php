@extends('SRTDashboard.frame')
@section('content')

{{-- Custom Styling --}}
<style>
    .page-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        padding: 1.2rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .page-header h4 {
        margin: 0;
        font-weight: 600;
    }
    .date-form {
        background: #ffffff;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .table thead {
        background: #2575fc;
    }
    .table thead th {
        color: #fff;
        font-weight: 600;
    }
    .table-hover tbody tr:hover {
        background: #f1f7ff;
        transition: 0.3s;
    }
    .status-radio label {
        margin-right: 1rem;
        cursor: pointer;
    }
    .status-radio input {
        margin-right: 5px;
    }
    .btn-success {
        border-radius: 25px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
    }
    .alert-success {
        border-radius: 12px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
</style>

{{-- Page Header --}}
<div class="page-header mb-3">
    <h4><i class="fas fa-users"></i> Attendance Management</h4>
</div>

{{-- Date Form --}}
<div class="row">
    <div class="col-md-4">
        <div class="date-form">
            <form method="GET" action="{{ route('get.student.list', ['class' => Hashids::encode($myClass->first()->id)]) }}" class="needs-validation" novalidate>
                <label for="attendance_date" class="font-weight-bold">Select Date:</label>
                <div class="input-group mt-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-primary text-white">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="date" id="attendance_date" name="attendance_date"
                        value="{{ request()->input('attendance_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                        max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                        min="{{ \Carbon\Carbon::now()->subWeek()->format('Y-m-d') }}"
                        class="form-control" required>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Attendance Feedback / Form --}}
@if ($attendanceExists)
    <div class="alert alert-success text-center mt-4">
        <h5 class="mb-2"><i class="fas fa-check-circle"></i> Attendance Submitted</h5>
        <p>Attendance for <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('d-m-Y') }}</strong> has already been submitted.</p>
        <a href="{{ route('home') }}" class="btn btn-primary btn-sm mt-2"><i class="fas fa-home"></i> Go Home</a>
    </div>
@else
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <strong><i class="fas fa-edit"></i> Complete Attendance for {{ \Carbon\Carbon::parse($selectedDate)->format('d-m-Y') }}</strong>
        </div>
        <form id="attendanceForm" action="{{ route('store.attendance', ['student_class' => Hashids::encode($student_class->id)]) }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0 table-responsive-md">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th class="text-center">Gender</th>
                            <th class="text-center">Stream</th>
                            <th class="text-center">Attendance Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($studentList->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center text-danger">No students enrolled to this class!</td>
                            </tr>
                        @else
                            @foreach ($studentList as $student)
                                <tr>
                                    <input type="hidden" name="student_id[]" value="{{ $student->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('class.teacher.student.profile', ['student' => Hashids::encode($student->id)]) }}" class="text-dark font-weight-bold">
                                            {{ ucwords(strtolower($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name)) }}
                                        </a>
                                    </td>
                                    <td class="text-uppercase text-center">{{ $student->gender[0] }}</td>
                                    <td class="text-center">{{strtoupper($student->group)}}</td>
                                    <input type="hidden" name="group[{{ $student->id }}]" value="{{ $student->group }}">
                                    <td class="text-center status-radio">
                                        <label><input type="radio" name="attendance_status[{{ $student->id }}]" required value="present"> ✅ Present</label>
                                        <label><input type="radio" name="attendance_status[{{ $student->id }}]" value="absent"> ❌ Absent</label>
                                        <label><input type="radio" name="attendance_status[{{ $student->id }}]" value="permission"> 📝 Permission</label>
                                        @error('attendance_status.' . $student->id)
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-center">
                <button type="submit" id="saveButton" class="btn btn-success" onclick="return confirm('Are you sure you want to submit? You won’t be able to make changes afterward.')">
                    <i class="fas fa-save"></i> Submit Attendance
                </button>
            </div>
        </form>
    </div>
@endif

{{-- Scripts (unchanged except polish done on Swal) --}}
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

    document.addEventListener("DOMContentLoaded", function () {
        const attendanceDateInput = document.getElementById("attendance_date");

        attendanceDateInput.addEventListener("change", function () {
            const selectedDate = new Date(this.value);
            const day = selectedDate.getDay();

            // Check if it's Saturday (6) or Sunday (0)
            if (day === 0 || day === 6) {
                // Use SweetAlert toast notification
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Weekends are not allowed',
                    showConfirmButton: false,
                    timer: 5000,
                    toast: true
                });

                // Reset the input value to today
                this.value = new Date().toISOString().split("T")[0];
            }
        });
    });
</script>
@endsection
