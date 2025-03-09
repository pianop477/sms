
@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5">
        <div class="card-body">
           <div class="row">
            <div class="col-10">
                <h4 class="header-title">Edit Drivers & Bus Information</h4>
            </div>
            <div class="col-2">
                <a href="{{route('Transportation.index')}}"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
            </div>
           </div>
            <form class="needs-validation" novalidate="" action="{{route('transport.update.records', ['transport' => Hashids::encode($transport->id)])}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Drivers Full Name</label>
                        <input type="text" name="fullname" class="form-control text-uppercase" id="validationCustom01" placeholder="Full Name" value="{{$transport->driver_name}}" required="">
                        @error('fullname')
                        <div class="text-danger">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Gender</label>
                        <select name="gender" id="validationCustom01" class="form-control text-capitalize" required>
                            <option value="{{$transport->gender}}" selected>{{$transport->gender}}</option>
                            <option value="male">male</option>
                            <option value="female">female</option>
                        </select>
                        @error('gender')
                        <div class="text-danger">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Mobile Phone</label>
                        <input type="text" name="phone" class="form-control" id="validationCustom02" placeholder="Phone Number" required="" value="{{$transport->phone}}">
                        @error('phone')
                        <div class="text-danger">
                           {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">School Bus Number</label>
                        <input type="text" name="bus_no" class="form-control text-uppercase" id="validationCustom02" placeholder="Bus Number" required="" value="{{$transport->bus_no}}">
                        @error('bus_no')
                        <div class="text-danger">
                           {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">School Bus Routine</label>
                        <textarea name="routine" class="form-control text-uppercase" id="validationCustom02" cols="50" rows="4">{{$transport->routine}}</textarea>
                        @error('phone')
                        <div class="text-danger">
                           {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>

                <button class="btn btn-success" id="saveButton" type="submit">Save changes</button>
            </form>
        </div>
    </div>
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
                submitButton.innerHTML = "Save changes";
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
