@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title text-center">Send Invoice Bill</h4>
            @if (Session::has('errors'))
                <div class="alert alert-danger" role="alert">
                    {{Session::get('errors')}}
                </div>
            @endif
            <form class="needs-validation" novalidate="" action="{{route('send.sms.invoice', ['school' => Hashids::encode($schools->id), 'manager'=> Hashids::encode($managers->id)])}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <!-- Current Password -->
                    <div class="col-md-4 mb-3">
                        <p>From: <strong>SHULE APP</strong></p>
                        <p>To: <strong><span class="text-capitalize">{{$schools->school_name}}</span></strong></p>
                        <p>Phone: <strong>{{$managers->phone}}</strong></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="currentPassword">Student Number</label>
                        <div class="input-group">
                            <input type="text" readonly name="students" class="form-control" id="currentPassword" placeholder="" value="{{old('students', $students)}}">
                        </div>
                        @error('students')
                        <div class="invalid-feedback">
                            <span>{{$message}}</span>
                        </div>
                        @enderror
                    </div>
                    <!-- New Password -->
                    <div class="col-md-4 mb-3">
                        <label for="newPassword">Unit Cost</label>
                        <div class="input-group">
                            <input type="number" name="unit_cost" class="form-control" id="newPassword" placeholder="" required value="{{old('unit_cost')}}">
                        </div>
                        @error('unit_cost')
                        <div class="invalid-feedback">
                            <span>{{$message}}</span>
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <button class="btn btn-success float-right" id="saveButton" type="submit">Send Bill</button>
                </div>
            </form>

            <!-- FontAwesome Icons -->
            <!-- JavaScript for Show/Hide Password -->
            <script>
                //disable button
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
                    submitButton.innerHTML = "Send Bill";
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
