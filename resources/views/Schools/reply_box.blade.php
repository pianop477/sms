@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5 col-12">
        <div class="card-body">
            <h4 class="header-title text-center">Send reply message</h4>
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
            <form class="needs-validation" novalidate="" action="{{route('send.reply.message')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <p class="text-capitalize">Customer Name: {{$sender->name}}</p>
                        <p class="">{{$sender->email}}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="hidden" name="text_id" value="{{$sender->id}}">
                        <input type="hidden" name="phone" value="{{$sender->email}}">
                        <label for="">Sender ID</label>
                        <select name="sender_id" id="" class="form-control" required>
                            <option value="">--Select Sender--</option>
                            @if ($schools->isEmpty())
                                <option value="" class="text-danger">No sender ID available</option>
                            @else
                                @foreach ($schools as $school)
                                    <option value="{{strtoupper($school->sender_id)}}">{{ucwords(strtolower($school->school_name))}}</option>
                                @endforeach
                            @endif
                        </select>
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
                <div class="col-md-8 mb-3">
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

@endsection
