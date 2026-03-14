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

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
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
                    <i class="fas fa-university me-2"></i>
                    Update Bank Details
                </h4>
                <p class="mb-0 text-white">Complete your profile with valid bank details for salary payments</p>
            </div>
            <a href="{{ route('show.profile') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>

        <!-- Main Form Card -->
        <div class="modern-card">
            <div class="card-body">
                <form class="needs-validation" novalidate
                    action="{{route('update.bank.details', ['id' => Hashids::encode($teacher->id)])}}" method="POST"
                    enctype="multipart/form-data" id="updateForm">
                    @csrf
                    @method('PUT')

                    <div class="row-custom">
                        <!-- Bank Name -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-building"></i>
                                Bank Name <span class="text-danger">*</span>
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-university input-icon"></i>
                                <select name="bank_name" id="bank_name" class="form-control-modern" required>
                                    <option value="" disabled selected>-- Select Your Bank --</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank['code'] }}"
                                            {{ old('bank_name', $teacher->bank_name ?? '') == $bank['code'] ? 'selected' : '' }}>
                                            {{ $bank['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('bank_name')
                                <div class="error-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Account Number -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-hashtag"></i>
                                Account Number <span class="text-danger">*</span>
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-credit-card input-icon"></i>
                                <input type="text" name="account_number" maxlength="16" minlength="10"
                                    class="form-control-modern" id="account_number" placeholder="Enter account number"
                                    value="{{ old('account_number', $teacher->bank_account_number ?? '') }}"
                                    pattern="[0-9]{10,16}" title="Account number must be between 10-16 digits" required>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Account number must be between 10-16 digits (numbers only)
                            </small>
                            @error('account_number')
                                <div class="error-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row-custom">
                        <!-- Account Name -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-user"></i>
                                Account Name <span class="text-danger">*</span>
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-signature input-icon"></i>
                                <input type="text" name="account_name" class="form-control-modern" id="account_name"
                                    placeholder="Enter account holder's name"
                                    value="{{ old('account_name', $teacher->bank_account_name ?? '') }}" required>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Enter the name exactly as it appears on your bank account
                            </small>
                            @error('account_name')
                                <div class="error-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Confirm Account Number (Optional but recommended) -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-check-circle"></i>
                                Confirm Account Number
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-credit-card input-icon"></i>
                                <input type="text" class="form-control-modern" value="{{$teacher->bank_account_number}}" id="confirm_account_number"
                                    placeholder="Re-enter account number">
                            </div>
                            <div class="password-feedback" id="accountMatchFeedback"></div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end mt-5">
                        <button class="btn-modern btn-success-modern" id="saveButton" type="submit">
                            <i class="fas fa-save me-2"></i>
                            <span>Update Bank Details</span>
                            <i class="fas fa-arrow-right ms-2 floating-effect"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById("updateForm");
            const submitButton = document.getElementById("saveButton");
            const accountNumber = document.getElementById('account_number');
            const confirmAccount = document.getElementById('confirm_account_number');
            const accountMatchFeedback = document.getElementById('accountMatchFeedback');
            const accountName = document.getElementById('account_name');

            if (!form || !submitButton) return;

            // Format account number - allow only digits
            function formatAccountNumber(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                // Limit to 16 digits
                if (value.length > 16) {
                    value = value.slice(0, 16);
                }
                e.target.value = value;
            }

            // Format account name - proper case
            function formatAccountName(e) {
                let value = e.target.value;
                // Capitalize first letter of each word
                value = value.toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
                e.target.value = value;
            }

            // Add input listeners
            if (accountNumber) {
                accountNumber.addEventListener('input', formatAccountNumber);
                accountNumber.addEventListener('input', validateAccountMatch);
            }

            if (accountName) {
                accountName.addEventListener('input', formatAccountName);
            }

            if (confirmAccount) {
                confirmAccount.addEventListener('input', validateAccountMatch);
                confirmAccount.addEventListener('input', formatAccountNumber);
            }

            // Validate account numbers match
            function validateAccountMatch() {
                if (!confirmAccount || !accountNumber) return;

                const accNum = accountNumber.value;
                const confAcc = confirmAccount.value;

                if (confAcc.length === 0) {
                    accountMatchFeedback.innerHTML = '';
                    return;
                }

                if (accNum === confAcc) {
                    accountMatchFeedback.innerHTML =
                        '<i class="fas fa-check-circle text-success"></i> <span class="text-success">Account numbers match</span>';
                    accountMatchFeedback.className = 'password-feedback valid-feedback-custom';
                    confirmAccount.setCustomValidity('');
                } else {
                    accountMatchFeedback.innerHTML =
                        '<i class="fas fa-exclamation-circle text-danger"></i> <span class="text-danger">Account numbers do not match</span>';
                    accountMatchFeedback.className = 'password-feedback invalid-feedback-custom';
                    confirmAccount.setCustomValidity('Account numbers must match');
                }
            }

            // Form submission handling
            form.addEventListener("submit", function(e) {
                // Check if confirm account is present and matches
                if (confirmAccount && confirmAccount.value.length > 0) {
                    if (accountNumber.value !== confirmAccount.value) {
                        e.preventDefault();
                        e.stopPropagation();
                        showToast('Account numbers do not match', 'danger');
                        confirmAccount.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        return;
                    }
                }

                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Scroll to first error
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstInvalid.classList.add('is-invalid');
                    }

                    showToast('Please fill all required fields correctly', 'warning');
                } else {
                    // Disable button to prevent double submission
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                <i class="fas fa-spinner fa-spin me-2"></i>
                <span>Updating Bank Details...</span>
            `;

                    // Optional: Show success message before submit
                    showToast('Updating your bank details...', 'info');
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

            // Toast notification function
            function showToast(message, type = 'info') {
                // Define colors based on type
                const bgColors = {
                    'info': 'bg-primary',
                    'success': 'bg-success',
                    'warning': 'bg-warning',
                    'danger': 'bg-danger'
                };

                const bgColor = bgColors[type] || 'bg-primary';

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
                toast.className = `toast align-items-center text-white ${bgColor} border-0`;
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.setAttribute('aria-atomic', 'true');

                toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${type === 'danger' ? 'fa-exclamation-circle' :
                                     type === 'success' ? 'fa-check-circle' :
                                     type === 'warning' ? 'fa-exclamation-triangle' :
                                     'fa-info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
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

            // Optional: Auto-uppercase bank selection display
            const bankSelect = document.getElementById('bank_name');
            if (bankSelect) {
                bankSelect.addEventListener('change', function() {
                    // You can add any custom logic here
                    console.log('Bank selected:', this.value);
                });
            }
        });
    </script>
@endsection
