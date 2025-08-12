@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title text-center p-3">Change Password</h4>

            <!-- Password Requirements Box -->
            <div class="alert alert-info mb-4">
                <h5 class="alert-heading">Password Requirements:</h5>
                <ul class="mb-0 pl-3">
                    <li>Minimum 8 characters long</li>
                    <li>Must contain both letters and numbers</li>
                </ul>
            </div>

            <form class="needs-validation" novalidate="" action="{{route('change.new.password')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <!-- Current Password -->
                    <div class="col-md-4 mb-3">
                        <label for="currentPassword">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" class="form-control" id="currentPassword" placeholder="" value="{{old('current_password')}}" required>
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password" data-target="currentPassword">
                                    <i class="ti-eye"></i>
                                </span>
                            </div>
                        </div>
                        @error('current_password')
                            <div class="text-danger text-sm">{{$message}}</div>
                        @enderror
                        @if (Session::has('error'))
                            <div class="text-danger text-sm">{{Session::get('error')}}</div>
                        @endif
                    </div>

                    <!-- New Password -->
                    <div class="col-md-4 mb-3">
                        <label for="newPassword">New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password" class="form-control" id="newPassword" placeholder="" required value="{{old('new_password')}}"
                                   pattern="^(?=.*[A-Za-z])(?=.*\d).{8,}$"
                                   title="Must be at least 8 characters with letters and numbers">
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password" data-target="newPassword">
                                    <i class="ti-eye"></i>
                                </span>
                            </div>
                        </div>
                        @error('new_password')
                            <div class="text-danger text-sm">{{$message}}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-4 mb-3">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" class="form-control" id="confirmPassword" placeholder="" required value="{{old('confirm_password')}}">
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password" data-target="confirmPassword">
                                    <i class="ti-eye"></i>
                                </span>
                            </div>
                        </div>
                        @error('confirm_password')
                            <div class="text-danger text-sm">{{$message}}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <button class="btn btn-success float-right" id="saveButton" type="submit">
                        <i class="ti-save mr-1"></i> Save Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Show/Hide Password and Form Validation -->
<script>
    document.querySelectorAll(".toggle-password").forEach(item => {
        item.addEventListener("click", function() {
            let input = document.getElementById(this.getAttribute("data-target"));
            if (input.type === "password") {
                input.type = "text";
                this.innerHTML = '<i class="ti-eye-slash"></i>';
            } else {
                input.type = "password";
                this.innerHTML = '<i class="ti-eye"></i>';
            }
        });
    });

    // Form submission handling
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector(".needs-validation");
        const submitButton = document.getElementById("saveButton");

        if (!form || !submitButton) return;

        form.addEventListener("submit", function (event) {
            event.preventDefault();

            // Validate password pattern
            const newPassword = document.getElementById("newPassword");
            const passwordPattern = new RegExp(newPassword.getAttribute("pattern"));

            if (!passwordPattern.test(newPassword.value)) {
                newPassword.setCustomValidity("Password must be at least 8 characters with letters and numbers");
                newPassword.reportValidity();
                return;
            } else {
                newPassword.setCustomValidity("");
            }

            // Disable button during submission
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Processing...`;

            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                submitButton.disabled = false;
                submitButton.innerHTML = `<i class="ti-save mr-1"></i> Save Password`;
                return;
            }

            setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
</script>
@endsection
