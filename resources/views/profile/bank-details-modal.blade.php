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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 30px 60px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 20px 20px 0 0; padding: 1.5rem;">
                <h5 class="modal-title" id="bankDetailsModalLabel">
                    <i class="fas fa-university me-2"></i>
                    Bank Details Required
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="closeModalBtn"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="my-4">
                    <div class="icon-box mb-4" style="width: 80px; height: 80px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="mb-3" style="color: #333;">Warning!</h4>
                    <p class="text-muted mb-4" style="font-size: 1.1rem;">
                        Your bank account information is missing.<br>
                        Please update to ensure smooth salary processing.
                    </p>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-lg btn-outline-secondary px-4 py-2" id="remindLaterBtn" style="border-radius: 50px; font-weight: 600;">
                        <i class="fas fa-clock me-2"></i>Remind Later
                    </button>
                    <a href="{{ route('bank.details', ['id' => Hashids::encode($teacherId)]) }}" class="btn btn-lg px-4 py-2" id="updateNowBtn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 50px; font-weight: 600;">
                        <i class="fas fa-arrow-right me-2"></i>Update Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #updateNowBtn {
        animation: pulse 2s infinite;
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
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

    .modal.fade .modal-dialog {
        transform: scale(0.8);
        transition: transform 0.3s ease-in-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
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
        alert('Bootstrap JS not loaded. Please check your layout.');
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
        });
    } catch (error) {
        console.error('Error initializing modal:', error);
    }
});
</script>
@endif
@endif
