@extends('SRTDashboard.frame')

@section('content')
<style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #6f42c1;
        --success-color: #1cc88a;
        --info-color: #36b9cc;
        --warning-color: #f6c23e;
        --danger-color: #e74a3b;
        --light-color: #f8f9fc;
        --dark-color: #5a5c69;
    }

    body {
        background-color: #f8f9fc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0.5rem 1.5rem 0 rgba(58, 59, 69, 0.1);
        margin-bottom: 20px;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 2rem 0 rgba(58, 59, 69, 0.15);
    }

    .header-title {
        color: var(--primary-color);
        font-weight: 800;
        border-bottom: 3px solid var(--primary-color);
        padding-bottom: 15px;
        margin-bottom: 25px;
        font-size: 1.75rem;
    }

    /* Statistics Cards Styling */
    .stat-card {
        border-radius: 15px;
        border: none;
        position: relative;
        overflow: hidden;
        transition: all 0.4s ease;
        min-height: 140px;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.175);
    }

    .stat-card .card-body {
        position: relative;
        z-index: 2;
        padding: 1.5rem;
    }

    .stat-card .card-icon {
        position: absolute;
        right: 20px;
        top: 20px;
        opacity: 0.2;
        font-size: 4rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover .card-icon {
        opacity: 0.3;
        transform: scale(1.1);
    }

    .stat-card .card-title {
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        opacity: 0.9;
    }

    .stat-card .card-value {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0;
    }

    .bg-children {
        background: linear-gradient(135deg, #c84fe0 0%, #9c27b0 100%);
    }

    /* Table Styles */
    .table-responsive {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }

    .progress-table {
        background-color: white;
        border: none;
    }

    .progress-table thead {
        background: linear-gradient(135deg, var(--primary-color) 0%, #2e59d9 100%);
        color: white;
    }

    .progress-table th {
        padding: 18px 12px;
        font-weight: 700;
        vertical-align: middle;
        border: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .progress-table td {
        padding: 15px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #e3e6f0;
        transition: all 0.3s ease;
    }

    .progress-table tbody tr:hover td {
        background-color: #f8f9fc;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .action-buttons a, .action-buttons button {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .action-buttons a:hover, .action-buttons button:hover {
        transform: translateY(-2px);
    }

    /* Modal Styles */
    .modal-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #2e59d9 100%);
        color: white;
        border-radius: 0;
    }
     .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
            align-items: center;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .stat-card .card-value {
            font-size: 1.5rem;
        }

        .stat-card .card-icon {
            font-size: 2.5rem;
        }
    }
</style>

<div class="py-4">
    <div class="row">
        <!-- My Children Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card bg-children text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-title">My Children</div>
                            <div class="card-value">{{ count($students) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate card-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children List Section -->
        <div class="col-xl-8 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="header-title mb-0">
                            <i class="fas fa-child me-2"></i> Children List
                        </h4>
                        <!-- Student Registration Modal Trigger -->
                        {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentRegistrationModal">
                            <i class="fas fa-plus me-1"></i> New Child
                        </button> --}}
                    </div>
                    <style>
                        .btn-action {
                            background-color: #cccccc;
                            border: 1px solid #ced4da;
                            color: #212529;
                            font-weight: bold
                        }
                    </style>
                    <!-- Children Table -->
                    <div class="table-responsive">
                        <table class="table table-hover progress-table mb-0">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="text-white fw-bold small">
                                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">
                                                    {{ ucwords(strtolower($student->first_name.' '.$student->last_name)) }}
                                                </h6>
                                                <small class="text-muted">{{ ucwords(strtolower($student->middle_name)) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-info">{{ strtoupper($student->class_code) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('students.profile', ['student' => Hashids::encode($student->id)]) }}"
                                           class="btn btn-action btn-xs"
                                           data-bs-toggle="tooltip"
                                           title="Manage Child Profile">
                                           <i class="fas fa-cog me-1"></i> Manage
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-child fa-3x mb-3 d-block opacity-50"></i>
                                            <h5>No Children Registered</h5>
                                            <p class="mb-0">Contact Admin to get started</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Registration Modal -->
<div class="modal fade" id="studentRegistrationModal" tabindex="-1" aria-labelledby="studentRegistrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="studentRegistrationModalLabel">
                    <i class="fas fa-user-graduate me-2"></i> Student Registration Form
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close"></i></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Please fill the form with valid information
                </div>

                <form class="needs-validation" novalidate action="{{ route('register.student') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Name Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="firstName" class="form-label fw-semibold">First Name</label>
                            <input type="text" name="fname" class="form-control-custom" id="firstName" value="{{ old('fname') }}" required>
                            @error('fname')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="middleName" class="form-label fw-semibold">Middle Name</label>
                            <input type="text" name="middle" class="form-control-custom" id="middleName" value="{{ old('middle') }}" required>
                            @error('middle')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="lastName" class="form-label fw-semibold">Last Name</label>
                            <input type="text" name="lname" class="form-control-custom" id="lastName" value="{{ old('lname') }}" required>
                            @error('lname')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Personal Details Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="gender" class="form-label fw-semibold">Gender</label>
                            <select name="gender" id="gender" class="form-control-custom" required>
                                <option value="">-- Select Gender --</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="dob" class="form-control-custom" id="dob"
                                value="{{ old('dob') }}"
                                min="{{ \Carbon\Carbon::now()->subYears(17)->format('Y-m-d') }}"
                                max="{{ \Carbon\Carbon::now()->subYears(2)->format('Y-m-d') }}"
                                required>
                            @error('dob')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="grade" class="form-label fw-semibold">Class</label>
                            <select name="grade" id="grade" class="form-control-custom text-uppercase" required>
                                <option value="">-- Select Class --</option>
                                @forelse ($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('grade') == $class->id ? 'selected' : '' }}>
                                        {{ $class->class_name }}
                                    </option>
                                @empty
                                    <option value="" disabled class="text-danger">No classes available</option>
                                @endforelse
                            </select>
                            @error('grade')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Details Section -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="stream" class="form-label fw-semibold">Stream</label>
                            <select name="group" id="stream" class="form-control-custom" required>
                                <option value="">-- Select Stream --</option>
                                <option value="a" {{ old('group') == 'a' ? 'selected' : '' }}>Stream A</option>
                                <option value="b" {{ old('group') == 'b' ? 'selected' : '' }}>Stream B</option>
                                <option value="c" {{ old('group') == 'c' ? 'selected' : '' }}>Stream C</option>
                            </select>
                            @error('group')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="busNumber" class="form-label fw-semibold">
                                School Bus <small class="text-muted">(optional)</small>
                            </label>
                            <select name="driver" id="busNumber" class="form-control-custom">
                                <option value="">-- Select School Bus --</option>
                                @forelse ($buses as $bus)
                                    <option value="{{ $bus->id }}" {{ old('driver') == $bus->id ? 'selected' : '' }}>
                                        Bus No. {{ $bus->bus_no }}
                                    </option>
                                @empty
                                    <option value="" disabled class="text-danger">No buses available</option>
                                @endforelse
                            </select>
                            @error('driver')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="studentPhoto" class="form-label fw-semibold">
                                Photo <small class="text-muted">(optional)</small>
                            </label>
                            <input type="file" name="image" class="form-control-custom" id="studentPhoto" accept="image/*">
                            @error('image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveButton" class="btn btn-success">
                            <span id="submitText">Submit</span>
                            <span id="submitSpinner" class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Form submission handling
        const form = document.querySelector(".needs-validation");
        const saveButton = document.getElementById("saveButton");
        const submitText = document.getElementById("submitText");
        const submitSpinner = document.getElementById("submitSpinner");

        if (form && saveButton) {
            form.addEventListener("submit", function(event) {
                event.preventDefault();

                // Validate form
                if (!form.checkValidity()) {
                    event.stopPropagation();
                    form.classList.add("was-validated");
                    return;
                }

                // Show loading state
                saveButton.disabled = true;
                submitText.textContent = "Processing...";
                submitSpinner.classList.remove("d-none");

                // Submit form after a brief delay to show feedback
                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        }

        // Reset form state when modal is closed
        const modal = document.getElementById('studentRegistrationModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function () {
                if (form) {
                    form.classList.remove("was-validated");
                    saveButton.disabled = false;
                    submitText.textContent = "Register Child";
                    submitSpinner.classList.add("d-none");
                }
            });
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Authorization check
    @if (Auth::user()->usertype != 4)
        window.location.href = '/error-page';
    @endif
</script>

<style>
    @media (max-width: 768px) {
        .table-responsive {
            border: 0;
        }
        .table-responsive thead {
            display: none;
        }
        .table-responsive tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .table-responsive td {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 10px 15px;
            position: relative;
            border-bottom: 1px solid #f1f1f1;
            width: 100%;
        }
        .table-responsive td::before {
            display: none;
        }
        .btn-group {
            display: flex;
            gap: 5px;
            justify-content: center;
            width: 100%;
        }
        .table-responsive td.text-center {
            justify-content: center;
        }

        .form-control-lg {
            font-size: 0.875rem;
        }
    }
</style>
@endsection
