@extends('SRTDashboard.frame')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100 bg-gradient-primary">
        <div class="col-xl-4 col-lg-5 col-md-6">
            <!-- Glass morphism card -->
            <div class="card glass-card border-0">
                <div class="card-header bg-transparent border-0 pt-2">
                    <div class="pending-icon mx-auto">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>

                <div class="card-body px-4 pb-4 text-center">
                    <h4 class="card-title text-dark mb-3 fw-bold">Pending Results Detected</h4>

                    <p class="card-text text-muted mb-4">
                        You have <span class="badge bg-warning text-dark fs-6">{{ $saved_results->count() ?? 0 }} Students with pending results</span>
                        that are not submitted. Do you want to continue with your draft or start fresh?
                    </p>

                    @if(isset($saved_results) && $saved_results->count() > 0)
                    <div class="action-buttons">
                        <form action="{{ route('results.edit.draft') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $saved_results->first()->course_id}}">
                            <input type="hidden" name="class_id" value="{{ $saved_results->first()->class_id}}">
                            <input type="hidden" name="teacher_id" value="{{ $saved_results->first()->teacher_id}}">
                            <input type="hidden" name="school_id" value="{{ $saved_results->first()->school_id}}">
                            <input type="hidden" name="exam_type_id" value="{{ $saved_results->first()->exam_type_id}}">
                            <input type="hidden" name="exam_date" value="{{ $saved_results->first()->exam_date}}">
                            <input type="hidden" name="term" value="{{ $saved_results->first()->exam_term}}">
                            <input type="hidden" name="marking_style" value="{{ $saved_results->first()->marking_style}}">

                            <button type="submit" class="btn btn-continue btn-lg w-100 mb-3" id="saveButton">
                                <i class="fas fa-play-circle me-2"></i>
                                <span class="btn-text">Continue with Draft</span>
                            </button>
                        </form>

                        <a href="{{route('score.prepare.form', ['id' => Hashids::encode($saved_results->first()->course_id)])}}"
                           class="btn btn-new btn-lg w-100">
                            <i class="fas fa-plus-circle me-2"></i>
                            Start Fresh
                        </a>
                    </div>
                    @else
                    <div class="alert alert-danger border-0 rounded-12">
                        <i class="fas fa-times-circle me-2"></i>
                        No saved results found. Please start a new entry.
                    </div>

                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg w-100 mt-3">
                        <i class="fas fa-home me-2"></i>
                        Return to Dashboard
                    </a>
                    @endif
                </div>
                <!-- Footer with additional info -->
                <div class="card-footer bg-light border-0 text-center py-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Drafts can be saved and resumed anytime within 3 days!
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.min-vh-100 {
    min-height: 100vh;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow:
        0 20px 40px rgba(0, 0, 0, 0.15),
        0 10px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transform: translateY(0);
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.glass-card:hover {
    transform: translateY(-8px);
    box-shadow:
        0 30px 60px rgba(0, 0, 0, 0.2),
        0 15px 30px rgba(0, 0, 0, 0.15);
}

.pending-icon {
    width: 90px;
    height: 90px;
    background: linear-gradient(135deg, #ff9a00 0%, #ff6b00 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 20px rgba(255, 154, 0, 0.3);
}

.pending-icon i {
    font-size: 2.5rem;
    color: white;
    animation: pulse 2s infinite;
}

.rounded-12 {
    border-radius: 12px !important;
}

.btn-continue {
    background: linear-gradient(135deg, #06d6a0 0%, #04a777 100%);
    border: none;
    border-radius: 12px;
    color: white;
    font-weight: 600;
    padding: 15px 30px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-continue:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(6, 214, 160, 0.4);
}

.btn-continue::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.6s;
}

.btn-continue:hover::before {
    left: 100%;
}

.btn-new {
    background: rgba(108, 117, 125, 0.1);
    border: 2px solid #6c757d;
    border-radius: 12px;
    color: #6c757d;
    font-weight: 600;
    padding: 15px 30px;
    transition: all 0.3s ease;
}

.btn-new:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
}

.badge {
    font-size: 0.9em;
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: 600;
}

.fs-6 {
    font-size: 1rem !important;
}

.fw-bold {
    font-weight: 700 !important;
}

/* Animations */
@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    opacity: 0;
    animation: slideUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 1px solid #ffeaa7;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border: 1px solid #f5c6cb;
}

.loading {
    animation: pulse 1s infinite;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .glass-card {
        margin: 20px;
        border-radius: 16px;
    }

    .pending-icon {
        width: 70px;
        height: 70px;
    }

    .pending-icon i {
        font-size: 2rem;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".needs-validation");
    const submitButton = document.getElementById("saveButton");

    if (submitButton) {
        const btnText = submitButton.querySelector('.btn-text');

        if (!form || !submitButton) return;

        form.addEventListener("submit", function (event) {
            event.preventDefault();

            const originalContent = btnText.textContent;

            // Enhanced loading state
            submitButton.disabled = true;
            submitButton.classList.add('loading');
            btnText.textContent = 'Loading Draft...';

            // Smooth transition
            form.style.opacity = "0.7";
            form.style.transition = "all 0.3s ease";

            if (!form.checkValidity()) {
                form.classList.add("was-validated");

                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.classList.remove('loading');
                    btnText.textContent = originalContent;
                    form.style.opacity = "1";
                }, 1000);
                return;
            }

            setTimeout(() => {
                form.submit();
            }, 800);
        });

        // Add hover effects to all buttons
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });

            btn.addEventListener('mouseleave', function() {
                if (!this.disabled) {
                    this.style.transform = 'translateY(0)';
                }
            });
        });

        // Add ripple effect to buttons
        buttons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size/2;
                const y = e.clientY - rect.top - size/2;

                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255,255,255,0.6);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                `;

                this.appendChild(ripple);

                setTimeout(() => ripple.remove(), 600);
            });
        });

        // Add ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
});
</script>
@endsection
