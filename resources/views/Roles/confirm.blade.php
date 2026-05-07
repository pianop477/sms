@extends('SRTDashboard.frame')

@section('content')
    <!-- Premium UI dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        .premium-wrapper {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .role-card {
            background: #ffffff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            max-width: 550px;
            width: 100%;
            overflow: hidden;
        }

        .warning-gradient {
            height: 6px;
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
        }

        .status-icon {
            width: 90px;
            height: 90px;
            background: #fff5f5;
            color: #e53e3e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            margin: 30px auto 20px;
            border: 2px solid #fed7d7;
        }

        .role-title {
            color: #2d3748;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .role-text {
            color: #718096;
            line-height: 1.6;
            padding: 0 20px;
        }

        .action-group {
            background: #f8fafc;
            padding: 30px;
            border-top: 1px solid #edf2f7;
        }

        .btn-action {
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .btn-confirm {
            background: #4a5568;
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(74, 85, 104, 0.2);
        }

        .btn-confirm:hover {
            background: #2d3748;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 85, 104, 0.3);
            color: white;
        }

        .btn-cancel-link {
            color: #a0aec0;
            text-decoration: none;
            transition: color 0.2s;
        }

        .btn-cancel-link:hover {
            color: #e53e3e;
        }
    </style>

    <div class="premium-wrapper">
        <div class="role-card animate__animated animate__zoomIn">
            <div class="warning-gradient"></div>

            <div class="card-body p-0 text-center">
                <div class="status-icon">
                    <i class="fas fa-user-shield"></i>
                </div>

                <h3 class="role-title px-4">Conflict Detected</h3>
                <p class="role-text mb-4">
                    The selected teacher is currently assigned to another role.
                    <strong>Proceeding will update their primary responsibilities.</strong>
                </p>

                <form id="roleConfirmForm" action="{{ route('roles.confirmProceed') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="teacher_id" value="{{ session('confirm_role_change.teacher_id') }}">
                    <input type="hidden" name="new_role" value="{{ session('confirm_role_change.new_role') }}">

                    <div class="action-group">
                        <div class="d-grid gap-3">
                            <button type="button" class="btn btn-action btn-confirm" id="confirmBtn">
                                Yes, Reassign Teacher
                            </button>
                            <a href="{{ route('roles.cancelConfirmation') }}" class="btn-cancel-link fw-bold mt-2">
                                No, Keep Existing Role
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("roleConfirmForm");
            const btn = document.getElementById("confirmBtn");

            if (!btn) return;

            btn.addEventListener("click", function() {
                // Premium Overlay
                Swal.fire({
                    title: 'Updating Role...',
                    html: 'Applying changes to teacher profile',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Smooth submission logic
                setTimeout(() => {
                    form.submit();
                }, 600);
            });
        });
    </script>
@endsection
