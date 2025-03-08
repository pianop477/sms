@extends('SRTDashboard.frame')
    @section('content')
    @if ($studentList->isEmpty())
        <div class="alert alert-warning text-center">
            <h6>No Students Records Available for this Class</h6>
            <hr>
            <p><a href="{{route('home')}}" class="btn btn-primary btn-sm">Go Back</a></p>
        </div>
    @else
    {{-- <p class="text-center text-danger">Attendance Date: {{\Carbon\Carbon::now()->format('d-m-Y')}}</p> --}}
    <form action="{{ route('store.attendance', ['student_class' => Hashids::encode($student_class->id)]) }}" method="POST" enctype="multipart/form-data" onsubmit="showPreloader()" class="needs-validation" novalidate>
        @csrf
        <div class="col-md-3 float-right">
           Date: <input type="date" name="attendance_date" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"
                max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                min="{{ \Carbon\Carbon::now()->subWeek()->format('Y-m-d') }}" class="form-control"
                required>
           @error('attendance_date')
               <div class="text-danger">{{$message}}</div>
           @enderror
        </div>
        <div class="single-table">
            <div class="table-responsive-lg">
                <table class="table">
                    <thead class="text-capitalize bg-info">
                        <tr class="text-white">
                            <th scope="col" style="width: auto;">AdmNo.</th>
                            <th scope="col">Name</th>
                            <th scope="col" class="text-center">Sex</th>
                            <th scope="col" colspan="3" class="text-center">Attendance Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentList as $student)
                            <tr>
                                <td class="text-uppercase">
                                    <input type="hidden" name="student_id[]" value="{{ $student->id }}">
                                    {{ $student->admission_number }}
                                </td>
                                <td class="text-uppercase">
                                    <a href="{{ route('Students.show', ['student' => Hashids::encode($student->id)]) }}">{{ $student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name }}</a>
                                </td>
                                <td class="text-uppercase text-center">{{ $student->gender[0] }}</td>
                                    <input type="hidden" name="group[{{$student->id}}]" value="{{$student->group}}">
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
        </div>

        <div class="card-footer text-center">
            <ul class="d-flex justify-content-center">
                <li class="mr-3"><button type="submit" id="saveButton" class="btn btn-primary" onclick="return confirm('Are you sure you want to submit attendance? You will not able to make any changes')">Submit</button></li>
                <li><a href="{{route('today.attendance', ['student_class' => Hashids::encode($student_class->id)])}}" target="_blank" class="btn btn-success">Check Today Report</a></li>
            </ul>
        </div>
    </form>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton"); // Tafuta button kwa ID

            if (!form || !submitButton) return; // Kama form au button haipo, acha script isifanye kazi

            form.addEventListener("submit", function (event) {
                event.preventDefault(); // Zuia submission ya haraka

                // Disable button na badilisha maandishi
                submitButton.disabled = true;
                submitButton.innerHTML = "Sending.....";

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
    </script>
    @endsection
