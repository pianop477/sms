@extends('SRTDashboard.frame')

@section('content')
<div class="col-12">
    <div class="card mt-5 col-12">
        <div class="card-body">
            <!-- Header Title with Icon -->
            <h3 class="header-title text-center text-uppercase mb-4">
                <i class="fas fa-bullhorn me-2 text-primary"></i> Public Announcement SMS
            </h3>
            <hr class="border-primary opacity-50">

            <!-- Session Alerts (Dismissible) -->
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ Session::get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Form Start -->
            <form class="needs-validation" novalidate action="{{ route('Send.message.byNext') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <!-- Left Column (Recipient Selection) -->
                    <div class="col-md-6">
                        <!-- Individual Class Card -->
                        <div class="card border-danger mb-4">
                            <div class="card-header bg-danger text-white py-2">
                                <h6 class="mb-0"><i class="fas fa-users-class me-2"></i> Send to Individual Class</h6>
                            </div>
                            <div class="card-body">
                                {{-- <label for="classSelect" class="form-label">Select Class</label> --}}
                                <select name="class" id="classSelect" class="text-uppercase form-control @error('class') is-invalid @enderror">
                                    <option value="">-- Select class --</option>
                                    @forelse ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @empty
                                        <option disabled class="text-danger">No classes found</option>
                                    @endforelse
                                </select>
                                @error('class')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Group Selection Card -->
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i> Send to Specific Group</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="send_to_all" id="sendToAll" value="1">
                                    <label class="form-check-label" for="sendToAll">
                                        All Parents (All Classes)
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="send_with_transport" id="withTransport" value="1">
                                    <label class="form-check-label" for="withTransport">
                                        Parents with Transport Students
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="send_without_transport" id="withoutTransport" value="1">
                                    <label class="form-check-label" for="withoutTransport">
                                        Parents without Transport Students
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="send_to_teachers" id="sendToTeachers" value="1">
                                    <label class="form-check-label" for="sendToTeachers">
                                        All Teachers
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column (Message Area) -->
                    <div class="col-md-6">
                        <div class="card border-info h-100">
                            <div class="card-header bg-info text-white py-2">
                                <h6 class="mb-0"><i class="fas fa-comment-sms me-2"></i> Your Message</h6>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <textarea
                                    name="message_content"
                                    id="message_content"
                                    class="form-control flex-grow-1"
                                    placeholder="Write your message here . . . . ."
                                    required
                                    maxlength="306">{{ old('message_content') }}</textarea>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted">
                                        <span id="charCount">306</span> characters remaining
                                    </small>
                                    <small class="text-danger">
                                        Max 2 SMS
                                    </small>
                                </div>
                                @error('message_content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button class="btn btn-primary px-4" id="saveButton" type="submit">
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
    });
</script>

@endsection
