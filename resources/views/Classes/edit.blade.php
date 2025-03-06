@extends('SRTDashboard.frame')
    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title">Edit Class</h4>
                </div>
                <div class="col-2">
                    <a href="{{route('Classes.index')}}"><i class="fas fa-arrow-circle-left"></i> Back</a>
                </div>
            </div>
            <form class="needs-validation" novalidate="" action="{{route('Classes.update', ['id' => Hashids::encode($class->id)])}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">Class Name</label>
                        <input type="text" name="cname" class="form-control text-uppercase" id="validationCustom01" value="{{$class->class_name}}" required="">
                        @error('name')
                        <div class="text-danger">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">Class Code</label>
                        <input type="text" name="ccode" class="form-control text-uppercase" id="validationCustom01" placeholder="" value="{{$class->class_code}}" required="">
                        @error('name')
                        <div class="text-danger">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-success" id="saveButton" type="submit">Save Changes</button>
            </form>
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
                submitButton.innerHTML = "Updating...";

                // Hakikisha form haina errors kabla ya kutuma
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false; // Warudishe button kama kuna errors
                    submitButton.innerHTML = "Save Changes";
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
