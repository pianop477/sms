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
            max-width: 800px;
            margin: 0 auto;
            padding: 10px 5px;
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
            padding: 8px;
            margin-bottom: 6px;
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

        .form-group {
            margin-bottom: 6px;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 4px;
            color: var(--primary);
        }

        .form-control {
            border: 2px solid rgba(67, 97, 238, 0.2);
            border-radius: 12px;
            padding: 6px 4px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.3rem rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }

        .form-control:read-only {
            background: rgba(67, 97, 238, 0.1);
            border-color: rgba(67, 97, 238, 0.3);
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--success), #0f9d58);
            border: none;
            border-radius: 16px;
            padding: 6px 10px;
            color: white;
            font-weight: 600;
            transition: all 0.4s ease;
            box-shadow: 0 15px 35px rgba(28, 200, 138, 0.3);
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 25px 45px rgba(28, 200, 138, 0.4);
            color: white;
        }

        .alert-modern {
            border-radius: 16px;
            border: none;
            padding: 6px;
            box-shadow: var(--card-shadow);
            backdrop-filter: blur(10px);
        }

        .recipient-info {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), rgba(58, 12, 163, 0.1));
            border-radius: 16px;
            padding: 6px;
            border: 2px solid rgba(67, 97, 238, 0.2);
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 4px;
            padding: 4px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: rgba(67, 97, 238, 0.05);
        }

        .info-item i {
            width: 20px;
            margin-right: 6px;
            color: var(--primary);
        }

        .total-calculation {
            background: linear-gradient(135deg, rgba(76, 201, 240, 0.1), rgba(72, 149, 239, 0.1));
            border-radius: 16px;
            padding: 8px;
            margin: 8px 0;
            border: 2px solid rgba(76, 201, 240, 0.2);
        }

        .total-amount {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            padding: 8px;
            background: rgba(67, 97, 238, 0.1);
            border-radius: 12px;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 6px;
            }

            .header-section {
                padding: 6px;
            }

            .form-section {
                padding: 6px;
            }

            .btn-modern {
                width: 100%;
                padding: 8px 10px;
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

        .invoice-preview {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 10px;
            border: 2px solid rgba(67, 97, 238, 0.1);
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .invoice-preview:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }
    </style>

    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="glass-card header-section fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">📨 Send Invoice Bill</h1>
                    <p class="lead mb-0 opacity-90 text-white">Send invoice details to school manager</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-white text-primary p-3 rounded-pill">
                        <i class="fas fa-receipt me-2"></i>
                        Invoice
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (Session::has('errors'))
            <div class="glass-card alert-modern alert-danger fade-in mb-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">Error</h5>
                        <p class="mb-0">{{ Session::get('errors') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Invoice Form -->
        <div class="glass-card form-section fade-in">
            <form class="needs-validation" novalidate action="{{ route('send.sms.invoice', ['school' => Hashids::encode($schools->id), 'manager'=> Hashids::encode($managers->id)]) }}" method="POST">
                @csrf

                <!-- Recipient Information -->
                <div class="recipient-info">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-user-circle me-2"></i>Recipient Information
                    </h5>
                    <div class="info-item">
                        <i class="fas fa-building"></i>
                        <strong>School:</strong> <span class="text-capitalize">{{ $schools->school_name }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <strong>Phone:</strong> {{ $managers->phone }}
                    </div>
                    <div class="info-item">
                        <i class="fas fa-paper-plane"></i>
                        <strong>From:</strong> SHULE APP
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="students" class="form-label">
                            <i class="fas fa-users"></i> Number of Students
                        </label>
                        <input type="text" readonly name="students" class="form-control" id="students" value="{{ old('students', $students) }}">
                        @error('students')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="unit_cost" class="form-label">
                            <i class="fas fa-money-bill-wave"></i> Unit Cost (TZS)
                        </label>
                        <input type="number" name="unit_cost" class="form-control" id="unit_cost" required value="{{ old('unit_cost') }}" placeholder="Enter amount per student">
                        @error('unit_cost')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Total Calculation -->
                <div class="total-calculation">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-calculator me-2"></i>Total Calculation
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Students:</strong> {{ $students }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Unit Cost:</strong> TZS <span id="unitCostDisplay">0</span></p>
                        </div>
                    </div>
                    <div class="total-amount">
                        Total Amount: TZS <span id="totalAmount">0</span>
                    </div>
                </div>

                <!-- Invoice Preview -->
                <div class="invoice-preview">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-eye me-2"></i> Message Preview
                    </h6>
                    <p class="mb-2"><strong>To:</strong> {{ $managers->phone }}</p>
                    <p class="mb-2"><strong>Message:</strong></p>
                    <div class="bg-light p-3 rounded" id="messagePreview">
                        [Invoice preview will appear here]
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button class="btn btn-modern" id="saveButton" type="submit">
                        <i class="fas fa-paper-plane me-2"></i>Send Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");
            const unitCostInput = document.getElementById("unit_cost");
            const unitCostDisplay = document.getElementById("unitCostDisplay");
            const totalAmount = document.getElementById("totalAmount");
            const messagePreview = document.getElementById("messagePreview");
            const students = {{ $students }};

            // Update calculations and preview
            function updateCalculations() {
                const unitCost = parseFloat(unitCostInput.value) || 0;
                const total = unitCost * students;

                // Update displays
                unitCostDisplay.textContent = unitCost.toLocaleString();
                totalAmount.textContent = total.toLocaleString();

                // Update message preview
                messagePreview.innerHTML = `
                    <strong>INVOICE FROM SHULE APP</strong><br>
                    Students: ${students}<br>
                    Unit Cost: TZS ${unitCost.toLocaleString()}<br>
                    <strong>Total Amount: TZS ${total.toLocaleString()}</strong><br>
                    Please make payment before due date.
                `;
            }

            // Initial update
            updateCalculations();

            // Update on unit cost change
            unitCostInput.addEventListener("input", updateCalculations);

            if (!form || !submitButton) return;

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    return;
                }

                // Show confirmation dialog
                if (!confirm('Are you sure you want to send this invoice to {{ $managers->phone }}?')) {
                    return;
                }

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Sending Invoice...
                `;

                setTimeout(() => {
                    form.submit();
                }, 500);
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
            }
        });
    </script>
@endsection
