@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5 col-12">
        <div class="card-body">
            <h4 class="header-title text-center">Send Announcement Via SMS</h4>
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{Session::get('error')}}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{Session::get('success')}}
                </div>
            @endif
            <form class="needs-validation" novalidate="" action="{{route('Send.message.byNext')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">Select Class</label>
                        <select name="class" id="validationCustom01" class="form-control text-capitalize">
                            <option value="">--Select class--</option>
                            @if ($classes->isEmpty())
                                <option value="" disabled class="text-danger">No classes found</option>
                            @else
                                @foreach ($classes as $class )
                                    <option value="{{$class->id}}">{{$class->class_name}}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('class')
                        <div class="text-danger">
                            <span>{{$message}}</span>
                        </div>
                        @enderror
                        <hr>
                        <label>
                            <input type="checkbox" name="send_to_all" value="1" class=""> Send to all Classes?
                        </label>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom02">Your Message</label>
                        <textarea name="message_content" id="message_content" cols="30" rows="5" class="form-control" required maxlength="160">{{ old('message_content') }}</textarea>
                        <small id="charCount" class="text-muted">160 characters remaining</small>
                        @error('message_content')
                        <div class="text-danger">
                            <span>{{ $message }}</span>
                        </div>
                        @enderror
                    </div>

                </div>
                <div class="col-md-12 mb-3">
                    <button class="btn btn-success float-right" id="saveButton" type="submit">Send SMS</button>
                </div>
            </form>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const textarea = document.getElementById("message_content");
                    const charCount = document.getElementById("charCount");
                    const maxChars = 160;

                    textarea.addEventListener("input", function () {
                        let remaining = maxChars - textarea.value.length;

                        if (remaining < 0) {
                            textarea.value = textarea.value.substring(0, maxChars); // Zuia zaidi ya 160 chars
                            remaining = 0;
                        }

                        charCount.textContent = remaining + " characters remaining";

                        if (remaining === 0) {
                            charCount.classList.add("text-danger");
                        } else {
                            charCount.classList.remove("text-danger");
                        }
                    });
                });

                //disable button
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
                    submitButton.innerHTML = "Send SMS";
                    return;
                }

                // Chelewesha submission kidogo ili button ibadilike kwanza
                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
            </script>

@endsection
