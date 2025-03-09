
@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title text-center text-capitalize">Edit school Information</h4>
            <form class="needs-validation" novalidate="" action="{{route('schools.update.school', ['school' => Hashids::encode($schools->id)])}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">School name</label>
                        <input type="text" name="name" class="form-control text-uppercase" id="validationCustom01" placeholder="School Name" value="{{old('name', $schools->school_name)}}" required="">
                        @error('name')
                        <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Registration No</label>
                        <input type="text" name="reg_no" class="form-control text-uppercase" id="validationCustom02" placeholder="Registration Number" required="" value="{{old('reg_no', $schools->school_reg_no)}}">
                        @error('reg_no')
                        <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Postal Address</label>
                        <input type="text" name="postal" class="form-control" id="userInput validationCustom01" onblur="addPrefix()" placeholder="P.O Box 123" value="{{old('postal', $schools->postal_address)}}" required="">
                        @error('postal')
                        <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Address Name</label>
                        <input type="text" name="postal_name" class="form-control text-capitalize" id="validationCustom01" placeholder="Dodoma" value="{{old('postal_name', $schools->postal_name)}}" required="">
                        @error('postal_name')
                        <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Abbreviation Code</label>
                        <input type="text" name="abbriv" class="form-control" id="userInput validationCustom01" onblur="" placeholder="" value="{{old('abbriv', $schools->abbriv_code)}}" required="">
                        @error('abbriv')
                        <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Country</label>
                        <select name="country" id="validationCustom01" class="form-control text-capitalize" required>
                            <option value="{{$schools->country}}" selected>{{$schools->country}}</option>
                        </select>
                        @error('country')
                        <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Sender Name</label>
                        <input type="text" name="sender_name" class="form-control" id="validationCustom02" placeholder="Last name" value="{{old('logo', $schools->sender_id)}}">
                        @error('sender_name')
                        <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">School Logo</label>
                        <input type="file" name="logo" class="form-control" id="validationCustom02" placeholder="Last name" value="{{old('logo')}}">
                        @error('logo')
                        <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <button class="btn btn-primary float-right" id="saveButton" type="submit">Update</button>
                </div>
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
            submitButton.innerHTML = `<span class="spinner-border text-white" role="status" aria-hidden="true"></span> Please Wait...`;

            // Hakikisha form haina errors kabla ya kutuma
            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                submitButton.disabled = false; // Warudishe button kama kuna errors
                submitButton.innerHTML = "Update";
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
