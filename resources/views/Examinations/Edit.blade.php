@extends('SRTDashboard.frame')
    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title">Edit Examination Test</h4>
                </div>
                <div class="col-2">
                    <a href="{{route('exams.index')}}"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                </div>
            </div>
            <form class="needs-validation" novalidate="" action="{{route('exams.update', $exam->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">Examination Name</label>
                        <input type="text" name="name" class="form-control text-uppercase" id="validationCustom01" placeholder="Enter Examination Eest" value="{{$exam->exam_type}}" required="">
                        @error('name')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">Symbolic Abbreviation</label>
                        <input type="text" name="abbreviation" class="form-control text-uppercase" id="validationCustom01" placeholder="Examination Abbreviation" value="{{ $exam->symbolic_abbr}}" required="">
                        @error('abbreviation')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-success float-right" id="saveButton" type="submit">Save Changes</button>
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
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Please Wait...`;

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
