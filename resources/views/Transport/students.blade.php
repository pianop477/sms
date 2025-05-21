@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase text-center">Student Lists - Driver Name: {{$transport->driver_name}}</h4>
                    </div>
                    <div class="col-2">
                        @if ($students->isNotEmpty())
                            <h6 class="text-left">
                                <a href="{{route('transport.export', ['trans' => Hashids::encode($transport->id)])}}" target="_blank" class="btn btn-primary btn-xs float-right"><i class="fas fa-cloud-arrow-down"></i> Export</a>
                            </h6>
                        @endif
                    </div>
                    <div class="col-2">
                       <a href="{{route('Transportation.index')}}" class="float-right btn btn-info btn-xs"><i class="fas fa-arrow-circle-left" style=";"></i> Back</a>
                    </div>
                </div>
                <hr>
                <div class="single-table">
                    <form action="{{route('update.transport.batch')}}" novalidate class="needs-validation" method="POST" id="form">
                        @csrf
                            <div class="col-md-12 mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Transfer Student Bus</label>
                                        <select name="new_bus" class="form-control text-capitalize" required>
                                            <option value="">-- Select school Bus # --</option>
                                            @if ($AllBuses->isEmpty())
                                                <option value="" class="text-danger">No any or Other school buses available</option>
                                            @else
                                                @foreach ($AllBuses as $bus)
                                                    <option value="{{$bus->id}}" class="text-capitalize">Bus # {{$bus->bus_no}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mt-4"></div>
                                        <button type="submit" class="btn btn-warning btn-xs text-capitalize" onclick="return confirm('Are you sure you want to move selected students to New School bus routine?')"><i class="fas fa-random"></i> Shift Bus</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                            <table class="table table-hover progress-table" id="myTable">
                                <thead class="text-capitalize">
                                    <tr>
                                        <th scope="col" class="text-center"> <input type="checkbox" id="selectAll"> All</th>
                                        <th scope="col">Student Full name</th>
                                        <th scope="col">Gender</th>
                                        <th scope="col">Class</th>
                                        <th scope="col">Stream</th>
                                        <th scope="col">Parent Phone</th>
                                        <th scope="col">Street</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student )
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="student[]" value="{{$student->id}}">
                                                {{$loop->iteration}}
                                            </td>
                                            <td class="text-capitalize">{{ucwords(strtolower($student->first_name . ' ' .$student->middle_name . ' '. $student->last_name))}}</td>
                                            <td class="text-uppercase">{{$student->gender[0]}}</td>
                                            <td class="text-uppercase">{{$student->class_code}}</td>
                                            <td class="text-uppercase">{{$student->group}}</td>
                                            <td>{{$student->parent_phone}}</td>
                                            <td class="text-capitalize">{{ucwords(strtolower($student->address))}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="student[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

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
                    submitButton.innerHTML = "Shift Bus"; // Rudisha maandiko ya button
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
