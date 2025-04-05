@extends('SRTDashboard.frame')

@section('content')
<div class="row">
   <!-- table primary start -->
   <div class="col-lg-12 mt-5">
    <div class="card">
        <div class="card-body">
            <div class="col-row">
                <div class="d-flex">
                    <div class="col-8">
                        <h4 class="header-title text-center text-capitalize">Timetable configuration settings</h4>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-info float-right btn-xs" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-plus"></i> New Config
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Timetable Settings</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{ route('timetable.settings.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Day Start Time</label>
                                                    <input type="time" required name="day_start_time" class="form-control" id="validationCustom01" placeholder="Class Name" value="{{old('name')}}" required="">
                                                    @error('day_start_time')
                                                    <div class="text-danger">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom02">Session Duration (in minutes)</label>
                                                    <input type="number" required name="period_duration" class="form-control" id="validationCustom02" placeholder="In minutes" required="" value="{{old('code')}}" required>
                                                    @error('period_duration')
                                                    <div class="text-danger">
                                                       {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom02">First Break Start</label>
                                                    <input type="time" required name="first_break_start" class="form-control" id="validationCustom02" placeholder="Class Code" required="" value="{{old('code')}}" required>
                                                    @error('first_break_start')
                                                    <div class="text-danger">
                                                       {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">First Break End</label>
                                                    <input type="time" required name="first_break_end" class="form-control" id="validationCustom01" placeholder="Class Name" value="{{old('name')}}" required="">
                                                    @error('first_break_end')
                                                    <div class="text-danger">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Second Break Start</label>
                                                    <input type="time" required name="second_break_start" class="form-control" id="validationCustom01" placeholder="Class Name" value="{{old('name')}}" required="">
                                                    @error('second_break_start')
                                                    <div class="text-danger">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Second Break End</label>
                                                    <input type="time" required name="second_break_end" class="form-control" id="validationCustom01" placeholder="Class Name" value="{{old('name')}}" required="">
                                                    @error('second_break_end')
                                                    <div class="text-danger">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Day End Time</label>
                                                    <input type="time" required name="day_end_time" class="form-control" id="validationCustom01" placeholder="Class Name" value="{{old('name')}}" required="">
                                                    @error('day_end_time')
                                                    <div class="text-danger">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Active Days</label><br>
                                                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                                        <label><input type="checkbox" name="active_days[]" class="" value="{{ $day }}"> {{ $day }}</label><br>
                                                    @endforeach
                                                    @error('active_days')
                                                    <div class="text-danger">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" id="saveButton" class="btn btn-success">Save Settings</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="single-table">
                <div class="table-responsive">
                    <table class="table text-capitalize">
                        <thead class="">
                            <tr class="">
                                <th scope="col">Session Duration</th>
                                <th scope="col">Timetable Start_at</th>
                                <th scope="col">First break_start</th>
                                <th scope="col">First break_ends</th>
                                <th scope="col">Second break_start</th>
                                <th scope="col">Second break_ends</th>
                                <th scope="col">Timetable End_at</th>
                                <th scope="col">action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($timetableSettings->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="alert alert-warning">No Timetable configuration settings found.</div>
                                    </td>
                                </tr>
                            @else
                            @foreach ($timetableSettings as $row )
                                <tr>
                                    <td>{{$row->period_duration}} Minutes</td>
                                    <td class="text-capitalize">Every day {{\Carbon\Carbon::parse($row->day_start_time)->format('H:i')}}</td>
                                    <td class="text-capitalize">{{\Carbon\Carbon::parse($row->first_break_start)->format('H:i')}}</td>
                                    <td class="text-capitalize">{{\Carbon\Carbon::parse($row->first_break_end)->format('H:i')}}</td>
                                    <td class="text-capitalize">{{\Carbon\Carbon::parse($row->second_break_start)->format('H:i')}}</td>
                                    <td class="text-capitalize">{{\Carbon\Carbon::parse($row->second_break_end)->format('H:i')}}</td>
                                    <td class="text-capitalize">{{\Carbon\Carbon::parse($row->day_end_time)->format('H:i')}}</td>
                                    <td>
                                        <ul class="d-flex justify-content-center">
                                            <li class="mr-3">
                                                <a href=""><i class="ti-pencil text-secondary"></i></a>
                                            </li>
                                            <li>
                                                <form action="{{route('timetable.delete.settings', ['timetable' => Hashids::encode($row->id)])}}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-link p-0" onclick="return confirm('Are you sure you want to delete this timetable configuration?')">
                                                        <i class="ti-trash text-danger"></i>
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- table primary end -->
</div>
<script>
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
                submitButton.innerHTML = "Save Settings";
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
