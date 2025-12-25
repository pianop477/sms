@extends('SRTDashboard.frame')
<style>
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

</style>
@section('content')
<div class="col-12">
    <div class="card mt-1">
        <div class="card-body">
            <h4 class="header-title text-center p-3">Hello <strong>{{ucwords(strtolower($user->first_name))}}!</strong> Update your NIN and Four Four Details</h4>
            <!-- Password Requirements Box -->
            <div class="alert alert-info mb-4">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="alert-heading">Instructions:</h5>
                        <ul class="mb-0 pl-3">
                            <li>➡️ <i>Enter your valid NIN Number</i></li>
                            <li>➡️ <i>Enter your valid Form Four Index Number</i></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <a href="{{url()->previous()}}" class="btn btn-info float-right">
                            <i class="fas fa-arrow-circle-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
            <form class="needs-validation" novalidate="" action="{{route('update.nida.form.four', ['id' => Hashids::encode($teacher->id)])}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-6">
                        <label for="">Nationality</label>
                        <select name="nationality" id="nationality" class="form-control-custom" required>
                            <option value="">--Select--</option>
                            <option value="tanzania">Tanzanian, United Republic</option>
                            <option value="foreigner">Foreigner, Other Countries</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="currentPassword">NIN (NIDA)</label>
                        <div class="">
                            <input type="text" name="nida" maxlength="23" class="form-control-custom" id="nin" placeholder="19700130411110000123" value="{{old('nida', $teacher->nida)}}" required>
                        </div>
                        @error('nida')
                            <div class="text-danger text-sm">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="newPassword">Form Four Index Number</label>
                        <div class="">
                            <input type="text" name="index_number" maxlength="10" class="form-control-custom" id="index_number" placeholder="S1122-0001" required value="{{old('index_number', strtoupper($teacher->form_four_index_number))}}">
                        </div>
                        @error('index_number')
                            <div class="text-danger text-sm">{{$message}}</div>
                        @enderror
                    </div>
                   <div class="col-md-6 mb-3">
                        <label for="completion" class="form-label">Completion Year</label>
                        <select name="completion" required id="completion" class="form-control-custom"
                                style="overflow-y: auto;">
                            <option value="">-- Select Year --</option>
                            @for ($year = date('Y'); $year >= 1985; $year--)
                            <option value="{{ $year }}"
                                {{ old('completion', $teacher->form_four_completion_year) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                            @endfor
                        </select>
                        @error('completion')
                        <div class="text-danger small">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <button class="btn btn-success float-right" id="saveButton" type="submit">
                        <i class="ti-save mr-1"></i> Update Information
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Show/Hide Password and Form Validation -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const form = document.querySelector(".needs-validation");
        const submitButton = document.getElementById("saveButton");

        if (!form || !submitButton) return;

        form.addEventListener("submit", function () {
            // Ikiwa browser validation imefail → usifanye chochote
            if (!form.checkValidity()) {
                return;
            }

            // Disable button kuzuia double submit
            submitButton.disabled = true;

            submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm text-white"
                    role="status" aria-hidden="true"></span>
                Updating...
            `;
        });

        const nationality = document.getElementById('nationality');
        const ninInput = document.getElementById('nin');
        const indexInput = document.getElementById('index_number');

        function formatNIN(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            let formatted = '';

            if (value.length > 0) formatted += value.substring(0, 8);
            if (value.length >= 8) formatted += '-';
            if (value.length > 8) formatted += value.substring(8, 13);
            if (value.length >= 13) formatted += '-';
            if (value.length > 13) formatted += value.substring(13, 18);
            if (value.length >= 18) formatted += '-';
            if (value.length > 18) formatted += value.substring(18, 20);

            e.target.value = formatted;
        }

        function formatIndex(e) {
            let value = e.target.value.toUpperCase();

            if (value.length > 0 && !['S', 'P'].includes(value[0])) {
                e.target.value = '';
                return;
            }

            value = value.replace(/[^SP0-9]/g, '');
            let formatted = '';

            if (value.length >= 1) formatted += value.charAt(0);
            if (value.length > 1) formatted += value.substring(1, 5);
            if (value.length >= 5) formatted += '-';
            if (value.length > 5) formatted += value.substring(5, 9);

            e.target.value = formatted;
        }

        nationality.addEventListener('change', function () {
            ninInput.value = '';
            indexInput.value = '';

            if (this.value === 'tanzania') {
                ninInput.addEventListener('input', formatNIN);
                indexInput.addEventListener('input', formatIndex);
            } else {
                ninInput.removeEventListener('input', formatNIN);
                indexInput.removeEventListener('input', formatIndex);
            }
        });
    });

</script>
@endsection
