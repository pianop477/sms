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
            max-width: 1400px;
            margin: 0 auto;
            padding: 8px 4px;
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
            margin-bottom: 4px;
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
            padding: 8px;
        }

        .form-group {
            margin-bottom: 6px;
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
            border-radius: 12px;
            padding: 6px 8px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.3rem rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }

        .form-select {
            border: 2px solid rgba(67, 97, 238, 0.2);
            border-radius: 12px;
            padding: 6px 8px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.3rem rgba(67, 97, 238, 0.1);
        }

        .input-group-text {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border: none;
            border-radius: 12px 0 0 12px;
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border: none;
            border-radius: 16px;
            padding: 8px 12px;
            color: white;
            font-weight: 600;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.3);
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(67, 97, 238, 0.4);
            color: white;
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        .modern-table thead {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        }

        .modern-table th {
            padding: 1.2rem 1rem;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            border: none;
        }

        .modern-table td {
            padding: 8px 6px;
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
            vertical-align: middle;
            transition: all 0.3s ease;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .modern-table tbody tr:hover {
            background: rgba(67, 97, 238, 0.05);
            transform: translateX(5px);
        }

        .school-logo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .school-logo:hover {
            transform: scale(1.2);
            border-color: var(--primary);
        }

        .badge-modern {
            padding: 6px 10px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .action-buttons {
            display: flex;
            gap: 4px;
            justify-content: center;
        }

        .btn-icon {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: none;
            font-size: 14px;
        }

        .btn-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        /* Modal fixes */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        .modal-glass {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border-radius: 24px 24px 0 0;
            padding: 8px;
            border: none;
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-body {
            padding: 8px;
        }

        .modal-footer {
            border-radius: 0 0 24px 24px;
            padding: 6px;
            border: none;
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 6px;
            }

            .header-section {
                padding: 4px;
            }

            .form-section {
                padding: 4px;
            }

            .modern-table {
                font-size: 14px;
            }

            .modern-table th,
            .modern-table td {
                padding: 6px 4px;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-icon {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }

            .modal-body {
                padding: 6px;
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
                    <h1 class="display-5 fw-bold mb-2">🏫 School Registration</h1>
                    <p class="lead mb-0 opacity-90 text-white">Register new schools and manage existing institutions</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-white text-primary p-3 rounded-pill">
                        <i class="fas fa-school me-2"></i>
                        {{ count($schools) }} Registered Schools
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Form -->
        <div class="glass-card form-section fade-in">
            <h4 class="text-primary mb-4">
                <i class="fas fa-info-circle me-2"></i>School Information
            </h4>

            <form class="needs-validation" novalidate action="{{ route('Schools.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <!-- School Name -->
                    <div class="col-md-6 form-group">
                        <label for="validationCustom01" class="form-label">
                            <i class="fas fa-school"></i>School Name
                        </label>
                        <input type="text" required name="name" class="form-control text-uppercase" id="validationCustom01"
                               placeholder="Enter school name" value="{{ old('name') }}">
                        @error('name')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Registration Number -->
                    <div class="col-md-6 form-group">
                        <label for="validationCustom02" class="form-label">
                            <i class="fas fa-id-card"></i>Registration No
                        </label>
                        <input type="text" required name="reg_no" class="form-control text-uppercase"
                               id="validationCustom02" placeholder="REG12345" value="{{ old('reg_no') }}">
                        @error('reg_no')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Postal Address -->
                    <div class="col-md-4 form-group">
                        <label for="postalAddress" class="form-label">
                            <i class="fas fa-envelope"></i>Postal Address
                        </label>
                        <input type="text" required name="postal" class="form-control" id="postalAddress"
                               placeholder="P.O Box 123" value="{{ old('postal') }}">
                        @error('postal')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address Name -->
                    <div class="col-md-4 form-group">
                        <label for="addressName" class="form-label">
                            <i class="fas fa-map-marker-alt"></i>Address Name
                        </label>
                        <input type="text" required name="postal_name" class="form-control text-capitalize"
                               id="addressName" placeholder="Dodoma" value="{{ old('postal_name') }}">
                        @error('postal_name')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Abbreviation Code -->
                    <div class="col-md-4 form-group">
                        <label for="abbreviationCode" class="form-label">
                            <i class="fas fa-code"></i>Abbreviation Code
                        </label>
                        <input type="text" name="abbriv" class="form-control" id="abbreviationCode"
                               value="{{ old('abbriv') }}" required>
                        @error('abbriv')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Sender ID -->
                    <div class="col-md-6 form-group">
                        <label for="senderId" class="form-label">
                            <i class="fas fa-bullhorn"></i>Sender ID
                        </label>
                        <input type="text" name="sender_name" class="form-control" id="senderId"
                               placeholder="Enter Sender ID" value="{{ old('sender_name') }}">
                        <small class="text-muted">Enter sender ID name as it appears to your service provider</small>
                        @error('sender_name')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div class="col-md-3 form-group">
                        <label for="countrySelect" class="form-label">
                            <i class="fas fa-globe"></i>Country
                        </label>
                        <select name="country" id="countrySelect" class="form-select" required>
                            <option value="Tanzania" selected>Tanzania</option>
                        </select>
                        @error('country')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- School Logo -->
                    <div class="col-md-3 form-group">
                        <label for="schoolLogo" class="form-label">
                            <i class="fas fa-image"></i>School Logo
                        </label>
                        <input type="file" required name="logo" class="form-control" id="schoolLogo">
                        @error('logo')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="divider" style="height: 2px; background: linear-gradient(90deg, transparent, rgba(67, 97, 238, 0.3), transparent); margin: 2rem 0;"></div>

                <h4 class="text-primary mb-4">
                    <i class="fas fa-user-tie me-2"></i>School Admin Information
                </h4>

                <div class="row">
                    <!-- First Name -->
                    <div class="col-md-4 form-group">
                        <label for="firstName" class="form-label">
                            <i class="fas fa-user"></i>First Name
                        </label>
                        <input type="text" name="fname" class="form-control text-capitalize" id="firstName"
                               placeholder="First name" value="{{ old('fname') }}" required>
                        @error('fname')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div class="col-md-4 form-group">
                        <label for="lastName" class="form-label">
                            <i class="fas fa-user"></i>Last Name
                        </label>
                        <input type="text" name="lname" class="form-control text-capitalize" id="lastName"
                               placeholder="Last name" value="{{ old('lname') }}" required>
                        @error('lname')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-4 form-group">
                        <label for="emailAddress" class="form-label">
                            <i class="fas fa-envelope"></i>Email
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input type="email" name="email" class="form-control" id="emailAddress"
                                   placeholder="email@example.com" value="{{ old('email') }}" required>
                        </div>
                        @error('email')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Gender -->
                    <div class="col-md-4 form-group">
                        <label for="genderSelect" class="form-label">
                            <i class="fas fa-venus-mars"></i>Gender
                        </label>
                        <select name="gender" id="genderSelect" class="form-select" required>
                            <option value="">-- select gender --</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="col-md-4 form-group">
                        <label for="phoneNumber" class="form-label">
                            <i class="fas fa-phone"></i>Mobile Phone
                        </label>
                        <input type="tel" name="phone" class="form-control" id="phoneNumber"
                               placeholder="+255 123 456 789" value="{{ old('phone') }}" required>
                        @error('phone')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-modern btn-lg" id="saveButton" type="submit">
                        <i class="fas fa-plus me-2"></i>Register School
                    </button>
                </div>
            </form>
        </div>

        <!-- Schools List -->
        <div class="glass-card form-section fade-in">
            <h4 class="text-primary mb-4">
                <i class="fas fa-list me-2"></i>Registered Institutions
            </h4>

            <div class="table-responsive">
                <table class="modern-table table-responsive-md">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>School Name</th>
                            <th>Sender ID</th>
                            <th>Abbreviation</th>
                            <th>Registration No</th>
                            <th>Logo</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schools as $school)
                            <tr class="slide-in" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                                <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                                <td class="text-uppercase fw-bold">{{ $school->school_name }}</td>
                                <td class="text-uppercase">
                                    {{ $school->sender_id ?? 'Not set' }}
                                </td>
                                <td class="text-uppercase">{{ $school->abbriv_code }}</td>
                                <td class="text-uppercase">{{ $school->school_reg_no }}</td>
                                <td class="text-center">
                                    <img src="{{ asset('assets/img/logo/' . $school->logo) }}"
                                         alt="{{ $school->school_name }}"
                                         class="school-logo">
                                </td>
                                <td>
                                    @if ($school->status == 1)
                                        <span class="badge-modern bg-success">Open</span>
                                    @elseif($school->status == 0)
                                        <span class="badge-modern bg-danger">Closed</span>
                                    @else
                                        <span class="badge-modern bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if ($school->status == 2)
                                            <button class="btn-icon bg-success text-white"
                                                    data-toggle="modal"
                                                    data-target="#approveModal{{ $school->id }}"
                                                    title="Approve School">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                        @else
                                            <button class="btn-icon bg-info text-white"
                                                    data-toggle="modal"
                                                    data-target="#viewModal{{ $school->id }}"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @foreach ($schools as $school)
        @if ($school->status == 2)
            <!-- Approval Modal -->
            <div class="modal fade" id="approveModal{{ $school->id }}" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel{{ $school->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content modal-glass">
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveModalLabel{{ $school->id }}">Add Service Time Duration</h5>
                            <button type="button" class="close btn-close-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-center text-primary mb-3">School Details</p>
                                    <ul class="list-group">
                                        <li class="list-group-item text-uppercase">School Name: <strong>{{ $school->school_name }}</strong></li>
                                        <li class="list-group-item text-uppercase">Registration ID: <strong>{{ $school->school_reg_no }}</strong></li>
                                    </ul>
                                </div>
                            </div>
                            <hr class="my-4">
                            <p class="text-center text-primary mb-3">Complete Approval Actions</p>
                            <form action="{{ route('approve.school.request', ['school' => Hashids::encode($school->id)]) }}" method="POST" novalidate class="needs-validation">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="service_duration{{ $school->id }}" class="control-label">Set Months</label>
                                            <input type="number" class="form-control" name="service_duration" id="service_duration{{ $school->id }}" required placeholder="Number of Months for Service" value="{{ old('service_duration') }}">
                                            @error('service_duration')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to send this request?')">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- View Details Modal -->
            <div class="modal fade" id="viewModal{{ $school->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $school->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content modal-glass">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewModalLabel{{ $school->id }}">Payment Status</h5>
                            <button type="button" class="close btn-close-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-center text-primary mb-3">School Details</p>
                                    <ul class="list-group">
                                        <li class="list-group-item text-capitalize">School Name: <strong>{{ $school->school_name }}</strong></li>
                                        <li class="list-group-item text-capitalize">Registration ID: <strong>{{ $school->school_reg_no }}</strong></li>
                                        <li class="list-group-item">Sender ID: <strong>{{ $school->sender_id ?? 'Not set' }}</strong></li>
                                        <li class="list-group-item text-capitalize">Service Start Date: <strong>{{ $school->service_start_date }}</strong></li>
                                        <li class="list-group-item text-capitalize">Service Expiry Date: <strong>{{ $school->service_end_date }}</strong></li>
                                        <li class="list-group-item text-capitalize">Active Time Duration: <strong>{{ $school->service_duration }} Months</strong></li>
                                    </ul>
                                </div>
                            </div>
                            <hr class="my-4">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (!form || !submitButton) return;

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Registering...
                `;

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-plus me-2"></i>Register School';
                    return;
                }

                setTimeout(() => {
                    form.submit();
                }, 500);
            });

            // Fix modal backdrop issues
            $('.modal').on('shown.bs.modal', function () {
                $('.modal-backdrop').css('z-index', '1040');
                $(this).css('z-index', '1050');
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
        });
    </script>
@endsection
