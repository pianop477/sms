@extends('SRTDashboard.frame')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100 bg-light">
        <div class="col-xl-4 col-lg-5 col-md-6">
            <!-- Glass morphism card -->
            <div class="card glass-card border-0">
                <div class="card-header bg-transparent border-0 pt-4">
                    <div class="warning-icon mx-auto">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>

                <div class="card-body px-4 pb-4 text-center">
                    <h4 class="card-title text-dark mb-3 fw-bold">Role Change Required</h4>

                    <p class="card-text text-muted mb-4">
                        <span class="teacher-name">{{ session('confirm_role_change.teacher_name') ?? 'Selected teacher' }}</span>
                        is currently assigned to another role</span>.
                        Changing to new role will replace their current assignment.
                    </p>

                    <div class="action-buttons">
                        <form action="{{ route('roles.confirmProceed') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" name="teacher_id" value="{{ session('confirm_role_change.teacher_id') }}">
                            <input type="hidden" name="new_role" value="{{ session('confirm_role_change.new_role') }}">

                            <button type="submit" class="btn btn-confirm btn-lg w-100 mb-3" id="saveButton">
                                <i class="fas fa-check-circle me-2"></i>
                                <span class="btn-text">Confirm Change</span>
                            </button>
                        </form>

                        <a href="{{ route('roles.cancelConfirmation') }}" class="btn btn-cancel btn-lg w-100">
                            <i class="fas fa-times-circle me-2"></i>
                            Cancel Action
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.min-vh-100 {
    min-height: 100vh;
}

.bg-light {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow:
        0 15px 35px rgba(0, 0, 0, 0.1),
        0 5px 15px rgba(0, 0, 0, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transform: translateY(0);
    transition: all 0.3s ease;
}

.glass-card:hover {
    transform: translateY(-5px);
    box-shadow:
        0 25px 50px rgba(0, 0, 0, 0.15),
        0 10px 20px rgba(0, 0, 0, 0.1);
}

.warning-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #ffd166 0%, #ff9e00 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.warning-icon i {
    font-size: 2rem;
    color: white;
}

.teacher-name {
    font-weight: 600;
    color: #4361ee;
}

.btn-confirm {
    background: linear-gradient(135deg, #06d6a0 0%, #04a777 100%);
    border: none;
    border-radius: 12px;
    color: white;
    font-weight: 600;
    padding: 12px 24px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(6, 214, 160, 0.3);
}

.btn-confirm::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-confirm:hover::before {
    left: 100%;
}

.btn-cancel {
    background: rgba(108, 117, 125, 0.1);
    border: 2px solid #6c757d;
    border-radius: 12px;
    color: #6c757d;
    font-weight: 600;
    padding: 12px 24px;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
}

.badge {
    font-size: 0.8em;
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: 500;
}

.fw-bold {
    font-weight: 700 !important;
}

/* Loading animation */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.loading {
    animation: pulse 1s infinite;
}

/* Page entrance animation */
.card {
    opacity: 0;
    animation: slideUp 0.6s ease forwards;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".needs-validation");
    const submitButton = document.getElementById("saveButton");
    const btnText = submitButton.querySelector('.btn-text');

    // Update role names dynamically
    const currentRole = document.querySelector('.current-role');
    const newRole = document.querySelector('.new-role');

    // You can set these dynamically based on your data
    if (currentRole) {
        currentRole.textContent = '{{ session('confirm_role_change.current_role') ?? 'Current Role' }}';
    }
    if (newRole) {
        newRole.textContent = '{{ session('confirm_role_change.new_role_name') ?? 'New Role' }}';
    }

    if (!form || !submitButton) return;

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const originalContent = btnText.textContent;

        // Enhanced loading state
        submitButton.disabled = true;
        submitButton.classList.add('loading');
        btnText.textContent = 'Updating Role...';

        // Smooth transition
        form.style.opacity = "0.8";
        form.style.transition = "opacity 0.3s ease";

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

    // Add hover effects
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endsection
