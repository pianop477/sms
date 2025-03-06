@extends('SRTDashboard.frame')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card col-md-6 mt-5">
            <div class="card-body text-center">
                <h4 class="card-title">Change Confirmation Request</h4>
                <p class="card-text text-danger">
                    The selected teacher already has another assigned role. Do you want to proceed with changing the role?
                </p>

                <form action="{{ route('roles.confirmProceed') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="teacher_id" value="{{ session('confirm_role_change.teacher_id') }}">
                    <input type="hidden" name="new_role" value="{{ session('confirm_role_change.new_role') }}">

                    <button type="submit" class="btn btn-success" id="saveButton">Yes, Proceed</button>
                    <a href="{{ route('roles.cancelConfirmation') }}" class="btn btn-danger">No, Cancel</a>
                </form>
            </div>
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
            submitButton.innerHTML = "Processing...";

            // Hakikisha form haina errors kabla ya kutuma
            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                submitButton.disabled = false; // Warudishe button kama kuna errors
                submitButton.innerHTML = "Yes, proceed";
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
