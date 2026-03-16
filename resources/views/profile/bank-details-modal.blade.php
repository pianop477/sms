@if(session('show_bank_modal') && !request()->routeIs('bank.details'))
@php
    // Pata teacher ID kwa usalama
    $teacherId = session('teacher_id_for_modal');
    if (!$teacherId && Auth::check() && Auth::user()->usertype == 3) {
        $teacher = \App\Models\Teacher::where('user_id', Auth::id())->first();
        $teacherId = $teacher ? $teacher->id : null;
    }
@endphp

@if($teacherId)
<div class="modal fade" id="bankDetailsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="bankDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm modal-md modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 30px 60px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 20px 20px 0 0; padding: 1rem 1.5rem;">
                <h5 class="modal-title" id="bankDetailsModalLabel">
                    <i class="fas fa-university me-2"></i>
                    Bank Details Required
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="closeModalBtn"></button>
            </div>
            <div class="modal-body text-center p-3 p-sm-4 p-md-5">
                <div class="my-2 my-sm-3 my-md-4">
                    <div class="icon-box mb-3 mb-sm-4" style="width: 60px; height: 60px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle text-white" style="font-size: 1.8rem;"></i>
                    </div>
                    <h4 class="mb-2 mb-sm-3" style="color: #333; font-size: clamp(1.2rem, 4vw, 1.5rem);">Warning!</h4>
                    <p class="text-muted mb-3 mb-sm-4" style="font-size: clamp(0.9rem, 3vw, 1.1rem); line-height: 1.5;">
                        Your Bank Account Details is missing.<br>
                        Please update now to ensure smooth Salary Processing.
                    </p>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 gap-sm-3 mt-3 mt-sm-4">
                    <button type="button" class="btn btn-outline-secondary px-3 px-sm-4 py-2" id="remindLaterBtn" style="border-radius: 50px; font-weight: 600; font-size: clamp(0.85rem, 3vw, 1rem);">
                        <i class="fas fa-clock mr-1"></i> Later
                    </button>
                    <a href="{{ route('bank.details', ['id' => Hashids::encode($teacherId)]) }}" class="btn px-3 px-sm-4 py-2" id="updateNowBtn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 50px; font-weight: 600; font-size: clamp(0.85rem, 3vw, 1rem);">
                        <i class="fas fa-arrow-right mr-1"></i> Update Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Responsive Modal Dialog */
    .modal-dialog {
        margin: 0.5rem;
        width: auto;
    }

    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 400px;
            margin: 1.75rem auto;
        }
    }

    @media (min-width: 768px) {
        .modal-dialog {
            max-width: 450px;
        }
    }

    /* Modal Content - Full width on small screens */
    .modal-content {
        width: 100%;
        margin: 0 auto;
    }

    /* Animation for Update Now button */
    #updateNowBtn {
        animation: pulse 2s infinite;
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        white-space: nowrap;
    }

    @media (max-width: 375px) {
        #updateNowBtn {
            white-space: normal;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.5);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
    }

    /* Responsive Icon Box */
    .icon-box {
        width: 60px;
        height: 60px;
    }

    @media (min-width: 576px) {
        .icon-box {
            width: 70px;
            height: 70px;
        }

        .icon-box i {
            font-size: 2rem !important;
        }
    }

    @media (min-width: 768px) {
        .icon-box {
            width: 80px;
            height: 80px;
        }

        .icon-box i {
            font-size: 2.5rem !important;
        }
    }

    /* Modal Animation */
    .modal.fade .modal-dialog {
        transform: scale(0.8);
        transition: transform 0.3s ease-in-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }

    /* Button container responsive */
    .d-flex.flex-column.flex-sm-row {
        width: 100%;
    }

    .d-flex.flex-column.flex-sm-row .btn {
        width: 100%;
    }

    @media (min-width: 576px) {
        .d-flex.flex-column.flex-sm-row .btn {
            width: auto;
            min-width: 140px;
        }
    }

    /* Modal Header responsive */
    .modal-header {
        padding: 1rem;
    }

    @media (min-width: 576px) {
        .modal-header {
            padding: 1.25rem 1.5rem;
        }
    }

    @media (min-width: 768px) {
        .modal-header {
            padding: 1.5rem;
        }
    }

    .modal-title {
        font-size: clamp(1rem, 4vw, 1.25rem);
    }

    /* Modal Body responsive */
    .modal-body {
        padding: 1.5rem 1rem;
    }

    @media (min-width: 576px) {
        .modal-body {
            padding: 2rem;
        }
    }

    @media (min-width: 768px) {
        .modal-body {
            padding: 2.5rem;
        }
    }

    /* Close button */
    .btn-close-white {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .btn-close-white:hover {
        opacity: 1;
    }

    /* Small devices landscape */
    @media (max-height: 500px) and (orientation: landscape) {
        .modal-dialog {
            margin: 0.5rem auto;
        }

        .modal-body {
            padding: 1rem;
        }

        .icon-box {
            width: 40px;
            height: 40px;
        }

        .icon-box i {
            font-size: 1.2rem !important;
        }

        .modal-body h4 {
            margin-bottom: 0.25rem !important;
        }

        .modal-body p {
            margin-bottom: 0.5rem !important;
        }
    }

    /* Extra small devices */
    @media (max-width: 360px) {
        .modal-body {
            padding: 1rem;
        }

        .btn {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }

        .icon-box {
            width: 50px;
            height: 50px;
        }

        .icon-box i {
            font-size: 1.5rem !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking for modal...');

    const modalElement = document.getElementById('bankDetailsModal');
    console.log('Modal element found:', modalElement);

    if (!modalElement) {
        console.log('Modal element not found!');
        return;
    }

    // Check if bootstrap is loaded
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap JavaScript is not loaded!');

        // Create fallback alert
        const fallbackAlert = document.createElement('div');
        fallbackAlert.className = 'alert alert-danger position-fixed top-50 start-50 translate-middle';
        fallbackAlert.style.zIndex = '9999';
        fallbackAlert.innerHTML = `
            <strong>Error!</strong> Bootstrap JS not loaded.
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        document.body.appendChild(fallbackAlert);

        // Try to initialize modal manually
        if (modalElement) {
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            document.body.classList.add('modal-open');

            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        }

        return;
    }

    console.log('Bootstrap is loaded, initializing modal...');

    try {
        // Initialize Bootstrap modal
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });

        // Show the modal automatically
        modal.show();
        console.log('Modal show() called successfully');

        // Handle "Remind Later" button
        document.getElementById('remindLaterBtn')?.addEventListener('click', function() {
            console.log('Remind Later clicked');
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Please wait...';

            fetch('{{ route("bank.remind.later") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            }).then(() => {
                modal.hide();
            }).catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-clock me-1"></i> Remind Later';
                modal.hide();
            });
        });

        // Handle modal close button
        document.getElementById('closeModalBtn')?.addEventListener('click', function() {
            console.log('Close button clicked');
            fetch('{{ route("bank.remind.later") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            }).catch(error => console.error('Error:', error));
        });

        // When modal is hidden
        modalElement.addEventListener('hidden.bs.modal', function () {
            console.log('Modal hidden');
            if (!window.location.href.includes('Bank-information')) {
                fetch('{{ route("bank.modal.closed") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                }).catch(error => console.error('Error:', error));
            }

            // Reset buttons
            const remindBtn = document.getElementById('remindLaterBtn');
            if (remindBtn) {
                remindBtn.disabled = false;
                remindBtn.innerHTML = '<i class="fas fa-clock me-1"></i> Remind Later';
            }
        });

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Adjust modal position if needed
                if (modalElement.classList.contains('show')) {
                    modal.handleUpdate();
                }
            }, 250);
        });

    } catch (error) {
        console.error('Error initializing modal:', error);

        // Show error message to user
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-warning position-fixed top-0 start-50 translate-middle-x mt-3';
        errorDiv.style.zIndex = '9999';
        errorDiv.innerHTML = `
            <strong>Warning!</strong> Could not initialize modal automatically.
            <a href="{{ route('bank.details', ['id' => Hashids::encode($teacherId)]) }}" class="alert-link">Click here</a> to update your bank details.
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        document.body.appendChild(errorDiv);

        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }
});

// Additional fallback for manual modal show
window.onload = function() {
    const modalElement = document.getElementById('bankDetailsModal');
    if (modalElement && !modalElement.classList.contains('show') && typeof bootstrap === 'undefined') {
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
        document.body.classList.add('modal-open');

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
    }
};
</script>
@endif
@endif
