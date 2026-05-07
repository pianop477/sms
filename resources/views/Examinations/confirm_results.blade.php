@extends('SRTDashboard.frame')

@section('content')
    <!-- Include SweetAlert2 for the premium feel -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .premium-modal-container {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .confirmation-card {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }

        .card-accent {
            height: 5px;
            background: linear-gradient(90deg, #ffc107, #f44336);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: #fff3cd;
            color: #ffc107;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 20px auto;
        }

        .btn-premium {
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-proceed {
            background: #28a745;
            color: white;
            border: none;
        }

        .btn-proceed:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
    </style>

    <div class="premium-modal-container">
        <div class="confirmation-card animate__animated animate__fadeInUp">
            <div class="card-accent"></div>
            <div class="card-body p-5 text-center">
                <div class="icon-box">
                    <i class="fas fa-exclamation-circle"></i>
                </div>

                <h3 class="fw-bold text-dark mb-3">Pending Draft Found</h3>
                <p class="text-muted mb-4 fs-5">
                    You have unsaved results from a previous session. Would you like to pick up where you left off?
                </p>

                @if (isset($saved_results))
                    <form id="premiumConfirmForm" action="{{ route('results.edit.draft', ['id' => $id]) }}" method="POST"
                        novalidate>
                        @csrf
                        <!-- Keeping all your hidden inputs for logic integrity -->
                        <input type="hidden" name="course_id" value="{{ $saved_results->first()->course_id }}">
                        <input type="hidden" name="class_id" value="{{ $saved_results->first()->class_id }}">
                        <input type="hidden" name="teacher_id" value="{{ $saved_results->first()->teacher_id }}">
                        <input type="hidden" name="school_id" value="{{ $saved_results->first()->school_id }}">
                        <input type="hidden" name="exam_type_id" value="{{ $saved_results->first()->exam_type_id }}">
                        <input type="hidden" name="exam_date" value="{{ $saved_results->first()->exam_date }}">
                        <input type="hidden" name="term" value="{{ $saved_results->first()->exam_term }}">
                        <input type="hidden" name="marking_style" value="{{ $saved_results->first()->marking_style }}">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button type="button" class="btn btn-premium btn-proceed shadow-sm w-100" id="smartSubmitBtn">
                                Yes, Resume Work
                            </button>
                            <a href="{{ route('score.prepare.form', ['id' => Hashids::encode($saved_results->first()->course_id)]) }}"
                                class="btn btn-premium btn-light border w-100">
                                No, Start New
                            </a>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning border-0 bg-light">
                        <p class="mb-0">No saved results were found in the database.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("premiumConfirmForm");
            const btn = document.getElementById("smartSubmitBtn");

            if (!btn) return;

            btn.addEventListener("click", function() {
                // SweetAlert2 Confirmation for that 'Premium' feel
                Swal.fire({
                    title: 'Loading Results...',
                    text: 'Please wait while we prepare your draft.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();

                        // Small delay to ensure the smooth feel
                        setTimeout(() => {
                            form.submit();
                        }, 800);
                    }
                });
            });
        });
    </script>
@endsection
