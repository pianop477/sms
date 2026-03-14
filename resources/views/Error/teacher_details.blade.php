@extends('SRTDashboard.frame')

@section('content')
<style>
    :root {
        --primary: #4361ee;
        --primary-light: #4895ef;
        --secondary: #3f37c9;
        --success: #4cc9f0;
        --danger: #f72585;
        --warning: #f8961e;
        --dark: #1e1e2f;
        --light: #f8f9fa;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(67, 97, 238, 0.15);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        pointer-events: none;
    }

    .page-header h4 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .page-header p {
        margin: 0.5rem 0 0;
        opacity: 0.9;
        font-size: 1rem;
    }

    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 1.8rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.25);
        position: relative;
        overflow: hidden;
    }

    .info-card::after {
        content: '📋';
        font-size: 8rem;
        position: absolute;
        bottom: -20px;
        right: -20px;
        opacity: 0.1;
        transform: rotate(15deg);
    }

    .info-card h5 {
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .info-list li {
        padding: 0.75rem 0;
        font-size: 1.1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-list li:last-child {
        border-bottom: none;
    }

    .info-list li i {
        font-size: 1.3rem;
        width: 30px;
        text-align: center;
    }

    .modern-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
    }

    .modern-card:hover {
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.05);
    }

    .card-body {
        padding: 2.5rem;
    }

    .form-group-modern {
        margin-bottom: 2rem;
    }

    .form-label-modern {
        display: block;
        margin-bottom: 0.8rem;
        font-weight: 600;
        color: var(--dark);
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-label-modern i {
        margin-right: 8px;
        color: var(--primary);
    }

    .form-control-modern {
        width: 100%;
        padding: 1rem 1.2rem;
        font-size: 1rem;
        border: 2px solid #eef2f6;
        border-radius: 15px;
        background-color: #fafbfc;
        transition: all 0.3s ease;
        color: var(--dark);
    }

    .form-control-modern:focus {
        outline: none;
        border-color: var(--primary);
        background-color: white;
        box-shadow: 0 5px 20px rgba(67, 97, 238, 0.1);
    }

    .form-control-modern::placeholder {
        color: #a0aec0;
        font-size: 0.95rem;
    }

    select.form-control-modern {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%234361ee' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1.2rem center;
        background-size: 1.2rem;
        padding-right: 3rem;
    }

    .input-with-icon {
        position: relative;
    }

    .input-with-icon .form-control-modern {
        padding-left: 3rem;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
        font-size: 1.2rem;
        z-index: 10;
        opacity: 0.7;
    }

    .error-feedback {
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: var(--danger);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .error-feedback i {
        font-size: 1rem;
    }

    .btn-modern {
        padding: 1rem 2.5rem;
        border-radius: 15px;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 8px 20px rgba(76, 201, 240, 0.3);
    }

    .btn-success-modern {
        background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
        color: white;
    }

    .btn-success-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(76, 201, 240, 0.4);
    }

    .btn-success-modern:active {
        transform: translateY(0);
    }

    .btn-success-modern:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0.8rem 1.5rem;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        color: white;
        text-decoration: none;
        font-weight: 500;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        transform: translateX(-5px);
    }

    .floating-effect {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .badge-modern {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 500;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .row-custom {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }

        .page-header h4 {
            font-size: 1.4rem;
        }

        .info-card {
            padding: 1.2rem;
        }
    }
</style>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4>
                <i class="fas fa-id-card me-2"></i>
                Update NIN & Form Four Index
            </h4>
            <p class="mb-0 text-white">Complete your profile with valid identification details</p>
        </div>
        <a href="{{url()->previous()}}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            <span>Back</span>
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="modern-card">
        <div class="card-body">
            <form class="needs-validation" novalidate
                  action="{{route('update.nida.form.four', ['id' => Hashids::encode($teacher->id)])}}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="updateForm">
                @csrf
                @method('PUT')

                <div class="row-custom">
                    <!-- Nationality -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-globe-africa"></i>
                            Nationality
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-flag input-icon"></i>
                            <select name="nationality" id="nationality" class="form-control-modern">
                                <option value="" disabled selected>-- Select Your Nationality --</option>
                                <option value="tanzania">🇹🇿 Tanzanian, United Republic</option>
                                <option value="foreigner">🌍 Foreigner, Other Countries</option>
                            </select>
                        </div>
                        @error('nationality')
                            <div class="error-feedback">
                                <i class="fas fa-exclamation-circle"></i>
                                {{$message}}
                            </div>
                        @enderror
                    </div>

                    <!-- NIN Number -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-fingerprint"></i>
                            NIN (NIDA Number)
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-id-card input-icon"></i>
                            <input type="text"
                                   name="nida"
                                   maxlength="23"
                                   class="form-control-modern"
                                   id="nin"
                                   placeholder="00000000-00000-00000-00"
                                   value="{{old('nida', $teacher->nida)}}"
                                   required>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Format: 8 digits - 5 digits - 5 digits - 2 digits
                        </small>
                        @error('nida')
                            <div class="error-feedback">
                                <i class="fas fa-exclamation-circle"></i>
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="row-custom">
                    <!-- Form Four Index Number -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-hashtag"></i>
                            Form Four Index Number
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-graduation-cap input-icon"></i>
                            <input type="text"
                                   name="index_number"
                                   maxlength="10"
                                   class="form-control-modern"
                                   id="index_number"
                                   placeholder="S0000-0001"
                                   required
                                   value="{{old('index_number', strtoupper($teacher->form_four_index_number))}}">
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Format: S followed by 4 digits, hyphen, and 4 digits
                        </small>
                        @error('index_number')
                            <div class="error-feedback">
                                <i class="fas fa-exclamation-circle"></i>
                                {{$message}}
                            </div>
                        @enderror
                    </div>

                    <!-- Completion Year -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-calendar-alt"></i>
                            Completion Year
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-calendar-check input-icon"></i>
                            <select name="completion" required id="completion" class="form-control-modern"
                                    style="overflow-y: auto;">
                                <option value="" disabled selected>-- Select Completion Year --</option>
                                @for ($year = date('Y'); $year >= 1985; $year--)
                                <option value="{{ $year }}"
                                    {{ old('completion', $teacher->form_four_completion_year) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                                @endfor
                            </select>
                        </div>
                        @error('completion')
                            <div class="error-feedback">
                                <i class="fas fa-exclamation-circle"></i>
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-end mt-5">
                    <button class="btn-modern btn-success-modern" id="saveButton" type="submit">
                        <i class="fas fa-save me-2"></i>
                        <span>Update Information</span>
                        <i class="fas fa-arrow-right ms-2 floating-effect"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById("updateForm");
    const submitButton = document.getElementById("saveButton");
    const nationality = document.getElementById('nationality');
    const ninInput = document.getElementById('nin');
    const indexInput = document.getElementById('index_number');

    if (!form || !submitButton || !nationality || !ninInput || !indexInput) return;

    // Form submission handling
    form.addEventListener("submit", function (e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();

            // Scroll to first error
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.classList.add('is-invalid');
            }
        } else {
            // Disable button to prevent double submission
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <i class="fas fa-spinner fa-spin mr-2"></i>
                <span>Updating...</span>
            `;
        }

        form.classList.add('was-validated');
    });

    // Real-time validation styling
    form.querySelectorAll('.form-control-modern').forEach(input => {
        input.addEventListener('invalid', function() {
            this.classList.add('is-invalid');
        });

        input.addEventListener('input', function() {
            if (this.validity.valid) {
                this.classList.remove('is-invalid');
            }
        });
    });

    // Format NIN
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

    // Format Index Number
    function formatIndex(e) {
        let value = e.target.value.toUpperCase();

        if (value.length > 0 && !['S', 'P'].includes(value[0])) {
            e.target.value = '';
            showToast('Invalid format. Must start with S or P', 'error');
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

    // Nationality change handler
    nationality.addEventListener('change', function () {
        ninInput.value = '';
        indexInput.value = '';

        if (this.value === 'tanzania') {
            ninInput.addEventListener('input', formatNIN);
            indexInput.addEventListener('input', formatIndex);
            showToast('Tanzanian selected - Formatting enabled', 'info');
        } else if (this.value === 'foreigner') {
            ninInput.removeEventListener('input', formatNIN);
            indexInput.removeEventListener('input', formatIndex);
            showToast('Foreigner selected - Free text enabled', 'info');
        }
    });

    // Trigger initial state
    if (nationality.value === 'tanzania') {
        ninInput.addEventListener('input', formatNIN);
        indexInput.addEventListener('input', formatIndex);
    }

    // Toast notification function
    function showToast(message, type = 'info') {
        // Check if toast container exists
        let toastContainer = document.querySelector('.toast-container');

        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white mr-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);

        // Initialize and show toast (if using Bootstrap)
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remove after hide
            toast.addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        } else {
            // Fallback
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    }

    // Auto-format on page load if values exist
    if (ninInput.value && nationality.value === 'tanzania') {
        const fakeEvent = { target: ninInput };
        formatNIN(fakeEvent);
    }

    if (indexInput.value && nationality.value === 'tanzania') {
        const fakeEvent = { target: indexInput };
        formatIndex(fakeEvent);
    }
});
</script>
@endsection
