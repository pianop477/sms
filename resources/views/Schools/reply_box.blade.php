@extends('SRTDashboard.frame')
@section('content')
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient-start: #4361ee;
            --gradient-end: #3a0ca3;
            --card-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--card-shadow);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .glass-card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-5px);
        }

        .header-section {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border-radius: 24px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .form-section {
            padding: 2.5rem;
        }

        .sender-card {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), rgba(58, 12, 163, 0.1));
            border-radius: 20px;
            padding: 2rem;
            border: 2px solid rgba(67, 97, 238, 0.2);
            transition: all 0.3s ease;
        }

        .sender-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 0.5rem;
            color: var(--primary);
        }

        .form-control {
            border: 2px solid rgba(67, 97, 238, 0.2);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.3rem rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }

        .form-select {
            border: 2px solid rgba(67, 97, 238, 0.2);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%234361ee' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }

        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.3rem rgba(67, 97, 238, 0.1);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .char-count {
            font-size: 0.9rem;
            margin-top: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .char-count.warning {
            color: var(--warning);
        }

        .char-count.danger {
            color: var(--danger);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--success), #0f9d58);
            border: none;
            border-radius: 20px;
            padding: 1.25rem 2.5rem;
            color: white;
            font-weight: 600;
            transition: all 0.4s ease;
            box-shadow: 0 15px 35px rgba(28, 200, 138, 0.3);
            font-size: 1.1rem;
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 25px 45px rgba(28, 200, 138, 0.4);
            color: white;
        }

        .alert-modern {
            border-radius: 16px;
            border: none;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            backdrop-filter: blur(10px);
        }

        .sender-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(67, 97, 238, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .sender-avatar:hover {
            transform: scale(1.1);
            border-color: var(--primary);
        }

        .message-preview {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 1.5rem;
            border: 2px solid rgba(67, 97, 238, 0.1);
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .message-preview:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .header-section {
                padding: 1.5rem;
            }

            .form-section {
                padding: 1.5rem;
            }

            .sender-card {
                padding: 1.5rem;
            }

            .btn-modern {
                width: 100%;
                padding: 1rem 2rem;
            }
        }

        /* Animation classes */
        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .slide-in {
            animation: slideInRight 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>

    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="glass-card header-section fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">💬 Send Reply Message</h1>
                    <p class="lead mb-0 opacity-90 text-white"> Communicate with your users directly</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-white text-primary p-3 rounded-pill">
                        <i class="fas fa-paper-plane me-2"></i>
                        Quick Reply
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('error'))
            <div class="glass-card alert-modern alert-danger fade-in mb-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">Error</h5>
                        <p class="mb-0">{{ Session::get('error') }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="glass-card alert-modern alert-success fade-in mb-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">Success!</h5>
                        <p class="mb-0">{{ Session::get('success') }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <!-- Message Form -->
        <div class="glass-card form-section fade-in">
            <form class="needs-validation" novalidate action="{{ route('send.reply.message') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Sender Information -->
                    <div class="col-md-6 mb-4">
                        <div class="sender-card">
                            <div class="text-center mb-3">
                                <div class="sender-avatar-placeholder">
                                    <i class="fas fa-user-circle sender-avatar" style="font-size: 80px; color: #4361ee;"></i>
                                </div>
                            </div>
                            <h5 class="text-primary mb-3 text-center">
                                <i class="fas fa-user me-2"></i>Sender Information
                            </h5>
                            <div class="sender-details">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-signature text-white"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Name</small>
                                        <strong class="text-primary">{{ ucwords(strtolower($sender->name)) }}</strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-info rounded-circle p-2 me-3">
                                        <i class="fas fa-envelope text-white"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Email</small>
                                        <strong>{{ $sender->email }}</strong>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="text_id" value="{{ $sender->id }}">
                            <input type="hidden" name="phone" value="{{ $sender->email }}">
                        </div>
                    </div>

                    <!-- Message Form -->
                    <div class="col-md-6 mb-4">
                        <div class="form-group">
                            <label for="sender_id" class="form-label">
                                <i class="fas fa-id-card"></i> Sender ID
                            </label>
                            <select name="sender_id" id="sender_id" class="form-select" required>
                                <option value="">-- Select Sender --</option>
                                @if ($schools->isEmpty())
                                    <option value="" class="text-danger" disabled> No sender ID available</option>
                                @else
                                    @foreach ($schools as $school)
                                        <option value="{{ strtoupper($school->sender_id) }}" {{ old('sender_id') == strtoupper($school->sender_id) ? 'selected' : '' }}>
                                            🏫 {{ ucwords(strtolower($school->school_name)) }}
                                            <small class="text-muted">({{ strtoupper($school->sender_id) }})</small>
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('sender_id')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="message_content" class="form-label">
                                <i class="fas fa-comment-dots"></i> Your Message
                            </label>
                            <textarea name="message_content" id="message_content" class="form-control" rows="6" required maxlength="160" placeholder="Type your reply message here...">{{ old('message_content') }}</textarea>
                            <div id="charCount" class="char-count text-muted">160 characters remaining</div>
                            @error('message_content')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message Preview -->
                        <div class="message-preview">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-eye me-2"></i>Message Preview
                            </h6>
                            <p id="messagePreview" class="text-muted mb-0">
                                Your message will appear here...
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <button class="btn btn-modern" id="saveButton" type="submit">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Character counter functionality
            const textarea = document.getElementById("message_content");
            const charCount = document.getElementById("charCount");
            const messagePreview = document.getElementById("messagePreview");
            const maxChars = 160;

            // Update character count and preview
            function updateCharacterCount() {
                let remaining = maxChars - textarea.value.length;
                let message = textarea.value;

                if (remaining < 0) {
                    textarea.value = textarea.value.substring(0, maxChars);
                    remaining = 0;
                    message = textarea.value;
                }

                // Update character count
                charCount.textContent = `${remaining} characters remaining`;

                if (remaining < 20) {
                    charCount.className = "char-count warning";
                } else if (remaining < 10) {
                    charCount.className = "char-count danger";
                } else {
                    charCount.className = "char-count text-muted";
                }

                // Update message preview
                messagePreview.textContent = message || "Your message will appear here...";
                messagePreview.className = message ? "text-dark mb-0" : "text-muted mb-0";
            }

            textarea.addEventListener("input", updateCharacterCount);
            updateCharacterCount(); // Initial update

            // Form submission handling
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (!form || !submitButton) return;

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Sending...
                `;

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Message';
                    return;
                }

                // Add sending animation
                document.querySelector('.btn-modern').style.background = 'linear-gradient(135deg, #4895ef, #4361ee)';

                setTimeout(() => {
                    form.submit();
                }, 1000);
            });

            // Add GSAP animations if available
            if (typeof gsap !== 'undefined') {
                gsap.from('.fade-in', {
                    duration: 1,
                    y: 30,
                    opacity: 0,
                    stagger: 0.2,
                    ease: "power3.out"
                });

                gsap.from('.slide-in', {
                    duration: 0.8,
                    x: 50,
                    opacity: 0,
                    stagger: 0.1,
                    ease: "power2.out"
                });
            }

            // Add real-time preview updates
            textarea.addEventListener('focus', function() {
                document.querySelector('.message-preview').style.borderColor = '#4361ee';
            });

            textarea.addEventListener('blur', function() {
                document.querySelector('.message-preview').style.borderColor = 'rgba(67, 97, 238, 0.1)';
            });
        });
    </script>
@endsection
