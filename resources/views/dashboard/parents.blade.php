@extends('SRTDashboard.frame')

@section('content')
<div class="col-lg-12">
    <div class="row">
        <!-- My Children Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-lg rounded-lg" style="background: linear-gradient(135deg, #c84fe0 0%, #9c27b0 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase small opacity-75">
                                <i class=""></i> My Children
                            </h6>
                            <h2 class="text-white mb-0">{{ count($students) }}</h2>
                        </div>
                        <div class="bg-white rounded-circle p-3">
                            <i class="fas fa-user-graduate fa-2x text-purple"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-white small d-flex align-items-center">
                            View All <i class="fas fa-arrow-right ms-2"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children List Section -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="header-title text-uppercase mb-0">My Children List</h4>
                        <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target=".bd-example-modal-lg">
                            <i class="fas fa-plus"></i> New Student
                        </button>
                        <!-- Student Registration Modal -->
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title text-capitalize" id="studentRegistrationModalLabel">Student Registration Form</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-danger text-center text-capitalize mb-3">
                                            <i class="fas fa-exclamation-circle me-2"></i> Please fill the form with valid information
                                        </p>
                                        <hr>

                                        <form class="needs-validation" novalidate action="{{ route('register.student') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <!-- Name Section -->
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="firstName" class="form-label">First Name</label>
                                                    <input type="text" name="fname" class="form-control" id="firstName" value="{{ old('fname') }}" required>
                                                    @error('fname')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="middleName" class="form-label">Middle Name</label>
                                                    <input type="text" name="middle" class="form-control" id="middleName" value="{{ old('middle') }}" required>
                                                    @error('middle')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="lastName" class="form-label">Last Name</label>
                                                    <input type="text" name="lname" class="form-control" id="lastName" value="{{ old('lname') }}" required>
                                                    @error('lname')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Personal Details Section -->
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="gender" class="form-label">Gender</label>
                                                    <select name="gender" id="gender" class="form-select form-control" required>
                                                        <option value="">-- Select Gender --</option>
                                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                                    </select>
                                                    @error('gender')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="dob" class="form-label">Date of Birth</label>
                                                    <input type="date" name="dob" class="form-control" id="dob"
                                                        value="{{ old('dob') }}"
                                                        min="{{ \Carbon\Carbon::now()->subYears(17)->format('Y-m-d') }}"
                                                        max="{{ \Carbon\Carbon::now()->subYears(2)->format('Y-m-d') }}"
                                                        required>
                                                    @error('dob')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="grade" class="form-label">Class</label>
                                                    <select name="grade" id="grade" class="form-select form-control text-uppercase" required>
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
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Additional Details Section -->
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="stream" class="form-label">Stream</label>
                                                    <select name="group" id="stream" class="form-select form-control" required>
                                                        <option value="">-- Select Stream --</option>
                                                        <option value="a" {{ old('group') == 'a' ? 'selected' : '' }}>Stream A</option>
                                                        <option value="b" {{ old('group') == 'b' ? 'selected' : '' }}>Stream B</option>
                                                        <option value="c" {{ old('group') == 'c' ? 'selected' : '' }}>Stream C</option>
                                                    </select>
                                                    @error('group')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="busNumber" class="form-label">
                                                        School Bus <small class="text-muted">(optional)</small>
                                                    </label>
                                                    <select name="driver" id="busNumber" class="form-select form-control">
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
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="studentPhoto" class="form-label">
                                                        Photo <small class="text-muted">(optional, with blue background)</small>
                                                    </label>
                                                    <input type="file" name="image" class="form-control" id="studentPhoto" accept="image/*">
                                                    @error('image')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                <button type="submit" id="saveButton" class="btn btn-success">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Children Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light text-uppercase">
                                <tr>
                                    <th>Student Name</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $student)
                                <tr>
                                    <td class="text-uppercase">
                                        {{ strtoupper($student->first_name.' '.$student->middle_name.' '.$student->last_name) }} - {{strtoupper($student->class_code)}}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('students.profile', ['student' => Hashids::encode($student->id)]) }}"
                                           class="btn btn-sm btn-success">
                                           <i class="fas fa-cog me-1"></i> Manage
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        <div class="alert alert-info mb-0">
                                            No children registered yet
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

<style>
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .modal-header {
        padding: 1rem 1.5rem;
    }
    .modal-title {
        font-weight: 600;
    }
    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .form-label {
        font-weight: 500;
    }

    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .bg-success-lighten {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .bg-warning-lighten {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-secondary-lighten {
        background-color: rgba(108, 117, 125, 0.1);
    }
    .table-centered td, .table-centered th {
        vertical-align: middle;
    }
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
            justify-content: center; /* Changed from space-between to center */
            align-items: center; /* Uncommented and added to center vertically */
            text-align: center;
            padding-left: 15px; /* Changed from 50% to 15px */
            position: relative;
            border-bottom: 1px solid #f1f1f1;
            width: 100%; /* Added to ensure full width */
        }
        .table-responsive td::before {
            display: none; /* Removed the data-label pseudo-element */
        }
        .btn-group {
            display: flex;
            gap: 5px;
            justify-content: center; /* Center the buttons */
            width: 100%;
        }

        /* Additional rule for the action button cell */
        .table-responsive td.text-center {
            justify-content: center;
        }
    }
</style>

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
                    submitText.textContent = "Save";
                    submitSpinner.classList.add("d-none");
                }
            });
        }
    });

     // Authorization check
    @if (Auth::user()->usertype != 4)
        window.location.href = '/error-page';
    @endif
</script>
@endsection
