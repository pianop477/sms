@extends('SRTDashboard.frame')

@section('content')
<style>
    :root {
        --primary: #4e54c8;
        --secondary: #8f94fb;
        --info: #17a2b8;
        --warning: #ffc107;
        --danger: #dc3545;
        --success: #28a745;
        --light: #f8f9fa;
        --dark: #343a40;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        overflow-x: hidden;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        overflow: visible;
        margin-top: 30px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        position: relative;
    }

    .card-header-custom {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 20px 25px;
        position: relative;
        overflow: visible;
        z-index: 100;
        border-radius: 20px 20px 0 0;
    }

    .card-header-custom::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
        transform: rotate(30deg);
    }

    .header-title {
        font-weight: 700;
        margin: 0;
        position: relative;
        font-size: 28px;
    }

    .form-control-custom {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 16px;
        transition: all 0.3s;
        background-color: white;
    }

    .form-control-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
    }

    .alert-custom {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
    }

    .checkbox-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .checkbox-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .form-check-input {
        width: 20px;
        height: 20px;
        margin-top: 0.2rem;
    }

    .form-check-label {
        font-weight: 500;
        margin-left: 10px;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border: none;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 600;
        color: white;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 10;
        cursor: pointer;
    }

    .btn-primary-custom:hover {
        background: linear-gradient(135deg, #3f43b5 0%, #7a80f9 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
        color: white;
    }

    .floating-icons {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 50px;
        opacity: 0.1;
        color: white;
        z-index: 0;
    }

    .char-count {
        font-weight: 600;
    }

    .char-count.text-danger {
        color: var(--danger) !important;
    }
     /* Add responsive textarea styling */
    .message-textarea {
        width: 100%;
        min-height: 180px;
        resize: vertical;
        box-sizing: border-box;
    }

    @media (max-width: 768px) {
        .header-title {
            font-size: 22px;
        }

        .card-header-custom {
            padding: 15px 20px;
        }

        .btn-primary-custom {
            width: 100%;
            justify-content: center;
        }
        .checkbox-card {
            padding: 12px;
        }

        .form-control-custom {
            padding: 10px 12px;
        }
    }
</style>

<div class="">
    <div class="glass-card">
        <div class="card-header-custom">
            <div class="row align-items-center">
                <div class="col-md-12 text-center">
                    <h4 class="header-title text-white">
                        <i class="fas fa-bullhorn me-2"></i> Public Announcement SMS
                    </h4>
                    <p class="mb-0 text-white">Send messages to parents and teachers</p>
                </div>
            </div>
            <i class="fas fa-comment-sms floating-icons"></i>
        </div>

        <div class="card-body">
            <!-- Session Alerts (Dismissible) -->
            @if (session('error'))
                <div class="alert alert-custom alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ Session::get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-custom alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Form Start -->
            <form class="needs-validation" novalidate action="{{ route('Send.message.byNext') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <!-- Left Column (Recipient Selection) -->
                    <div class="col-md-6">
                        <!-- Individual Class Card -->
                        <div class="checkbox-card border-danger">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-users-class text-danger fs-4 me-3"></i>
                                <h6 class="mb-0 text-danger">Send to Individual Class</h6>
                            </div>
                            <select name="class" style="width: 100%" id="classSelect" class="form-control-custom text-uppercase @error('class') is-invalid @enderror">
                                <option value="">-- Select class --</option>
                                @forelse ($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class') == $class->id ? 'selected' : '' }}>
                                        {{ $class->class_name }}
                                    </option>
                                @empty
                                    <option disabled class="text-danger">No classes found</option>
                                @endforelse
                            </select>
                            @error('class')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Group Selection Card -->
                        <div class="checkbox-card border-primary">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-layer-group text-primary fs-4 me-3"></i>
                                <h6 class="mb-0 text-primary"> Send to Specific Group</h6>
                            </div>

                            <div class="form-check mb-3 p-2 rounded" style="background: rgba(78, 84, 200, 0.05);">
                                <input class="form-check-input" type="checkbox" name="send_to_all" id="sendToAll" value="1" {{ old('send_to_all') ? 'checked' : '' }}>
                                <label class="form-check-label" for="sendToAll">
                                    <i class="fas fa-users me-2 text-primary"></i> All Parents (All Classes)
                                </label>
                            </div>

                            <div class="form-check mb-3 p-2 rounded" style="background: rgba(78, 84, 200, 0.05);">
                                <input class="form-check-input" type="checkbox" name="send_with_transport" id="withTransport" value="1" {{ old('send_with_transport') ? 'checked' : '' }}>
                                <label class="form-check-label" for="withTransport">
                                    <i class="fas fa-bus me-2 text-success"></i> Parents with Transport Students
                                </label>
                            </div>

                            <div class="form-check mb-3 p-2 rounded" style="background: rgba(78, 84, 200, 0.05);">
                                <input class="form-check-input" type="checkbox" name="send_without_transport" id="withoutTransport" value="1" {{ old('send_without_transport') ? 'checked' : '' }}>
                                <label class="form-check-label" for="withoutTransport">
                                    <i class="fas fa-walking me-2 text-secondary"></i> Parents without Transport Students
                                </label>
                            </div>

                            <div class="form-check p-2 rounded" style="background: rgba(78, 84, 200, 0.05);">
                                <input class="form-check-input" type="checkbox" name="send_to_teachers" id="sendToTeachers" value="1" {{ old('send_to_teachers') ? 'checked' : '' }}>
                                <label class="form-check-label" for="sendToTeachers">
                                    <i class="fas fa-chalkboard-teacher me-2 text-info"></i> All Teachers
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column (Message Area) -->
                    <div class="col-md-6">
                        <div class="checkbox-card border-info h-100">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-comment-sms text-info fs-4 me-3"></i>
                                <h6 class="mb-0 text-info">Your Message</h6>
                            </div>

                            <textarea
                                name="message_content"
                                id="message_content"
                                class="form-control-custom flex-grow-1 message-textarea @error('message_content') is-invalid @enderror"
                                placeholder="Write your message here . . . . ."
                                required
                                rows="6"
                                maxlength="306">{{ old('message_content') }}</textarea>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">
                                    <span class="char-count" id="charCount">306</span> characters remaining
                                </small>
                                <small class="text-danger fw-bold">
                                    <i class="fas fa-info-circle me-1"></i> Max 2 SMS
                                </small>
                            </div>

                            @error('message_content')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-center mt-4">
                    <button class="btn btn-primary-custom" id="saveButton" type="submit">
                        <i class="fas fa-paper-plane me-2"></i> Send SMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript Section -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const textarea = document.getElementById("message_content");
        const charCount = document.getElementById("charCount");
        const maxChars = 306;

        // Initial display
        charCount.textContent = maxChars;

        textarea.addEventListener("input", function () {
            let currentLength = textarea.value.length;

            // If over limit, trim the value
            if (currentLength > maxChars) {
                textarea.value = textarea.value.substring(0, maxChars);
                currentLength = maxChars;
            }

            const remaining = maxChars - currentLength;
            charCount.textContent = remaining;

            // Add red text warning when remaining is less than 50
            charCount.classList.toggle("text-danger", remaining < 50);
        });

        // Form Submission Handler
        const form = document.querySelector(".needs-validation");
        const submitButton = document.getElementById("saveButton");

        form.addEventListener("submit", function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add("was-validated");

                // Scroll to first invalid field
                const invalidElements = form.querySelectorAll(':invalid');
                if (invalidElements.length > 0) {
                    invalidElements[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return;
            }

            // Check if at least one recipient is selected
            const classSelected = document.getElementById('classSelect').value !== '';
            const sendToAll = document.getElementById('sendToAll').checked;
            const withTransport = document.getElementById('withTransport').checked;
            const withoutTransport = document.getElementById('withoutTransport').checked;
            const sendToTeachers = document.getElementById('sendToTeachers').checked;

            if (!classSelected && !sendToAll && !withTransport && !withoutTransport && !sendToTeachers) {
                event.preventDefault();
                alert('Please select at least one recipient group.');
                return;
            }

            if (!confirm("Are you sure you want to send this SMS?")) {
                event.preventDefault();
                return;
            }

            submitButton.disabled = true;
            submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Sending...
            `;
        });

        // Prevent multiple checkbox selections that might conflict
        const sendToAll = document.getElementById('sendToAll');
        const classSelect = document.getElementById('classSelect');

        sendToAll.addEventListener('change', function() {
            if (this.checked) {
                classSelect.value = '';
            }
        });

        classSelect.addEventListener('change', function() {
            if (this.value !== '') {
                sendToAll.checked = false;
            }
        });
    });
</script>
@endsection
