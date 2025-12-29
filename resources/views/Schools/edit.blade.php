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
            max-width: 1200px;
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
            margin-bottom: 8px;
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
            /* background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent); */
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

        .btn-modern {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border: none;
            border-radius: 16px;
            padding: 5px 10px;
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

        .school-logo-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 20px;
            border: 3px solid rgba(67, 97, 238, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.4s ease;
        }

        .school-logo-preview:hover {
            transform: scale(1.1) rotate(5deg);
            border-color: var(--primary);
        }

        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-upload-btn {
            background: linear-gradient(135deg, #4cc9f0, #4895ef);
            border: none;
            border-radius: 12px;
            padding: 6px 8px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(76, 201, 240, 0.3);
        }

        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .current-logo {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 1rem;
        }

        .current-logo img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid rgba(67, 97, 238, 0.2);
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

            .school-logo-preview {
                width: 100px;
                height: 100px;
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
                    <h1 class="display-5 fw-bold mb-2">🏫 Edit School Information</h1>
                    <p class="lead mb-0 opacity-90 text-white">Update details for {{ $schools->school_name }}</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-white text-primary p-3 rounded-pill">
                        <i class="fas fa-certificate me-2"></i>
                        {{ $schools->school_reg_no }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="glass-card form-section fade-in">
            <form class="needs-validation" novalidate action="{{ route('schools.update.school', ['school' => Hashids::encode($schools->id)]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Current Logo Display -->
                <div class="current-logo mb-4">
                    <img src="{{ asset('storage/logo/' . $schools->logo) }}"
                         alt="{{ $schools->school_name }} Logo"
                         class="school-logo-preview">
                    <div>
                        <h6 class="text-primary mb-1">Current Logo</h6>
                        <small class="text-muted">Upload new logo to replace</small>
                    </div>
                </div>

                <div class="row">
                    <!-- School Name -->
                    <div class="col-md-6 form-group">
                        <label for="schoolName" class="form-label">
                            <i class="fas fa-school"></i>School Name
                        </label>
                        <input type="text" name="name" class="form-control text-uppercase" id="schoolName"
                               placeholder="Enter school name" value="{{ old('name', $schools->school_name) }}" required>
                        @error('name')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Registration Number -->
                    <div class="col-md-6 form-group">
                        <label for="regNumber" class="form-label">
                            <i class="fas fa-id-card"></i>Registration No
                        </label>
                        <input type="text" name="reg_no" class="form-control text-uppercase" id="regNumber" readonly
                               placeholder="REG12345" value="{{ old('reg_no', $schools->school_reg_no) }}">
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
                        <input type="text" name="postal" class="form-control" id="postalAddress"
                               placeholder="P.O Box 123" value="{{ old('postal', $schools->postal_address) }}" required>
                        @error('postal')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address Name -->
                    <div class="col-md-4 form-group">
                        <label for="addressName" class="form-label">
                            <i class="fas fa-map-marker-alt"></i>Address Name
                        </label>
                        <input type="text" name="postal_name" class="form-control text-capitalize" id="addressName"
                               placeholder="Dodoma" value="{{ old('postal_name', $schools->postal_name) }}" required>
                        @error('postal_name')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Abbreviation Code -->
                    <div class="col-md-4 form-group">
                        <label for="abbreviationCode" class="form-label">
                            <i class="fas fa-code"></i>School Code (Admission Prefix)
                        </label>
                        <input type="text" name="abbriv" class="form-control" id="abbreviationCode"
                               value="{{ old('abbriv', $schools->abbriv_code) }}" required>
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
                               placeholder="Enter Sender ID" value="{{ old('sender_name', $schools->sender_id) }}">
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
                            <option value="{{ $schools->country }}" selected>{{ $schools->country }}</option>
                        </select>
                        @error('country')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- School Logo Upload -->
                    <div class="col-md-3 form-group">
                        <label class="form-label">
                            <i class="fas fa-image"></i>Update Logo
                        </label>
                        <div class="file-upload">
                            <button type="button" class="file-upload-btn">
                                <i class="fas fa-upload me-2"></i>Choose File
                            </button>
                            <input type="file" name="logo" class="file-upload-input" id="logoUpload">
                        </div>
                        <small class="text-muted d-block mt-2" id="fileName">No file chosen</small>
                        @error('logo')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button class="btn btn-modern btn-lg" id="saveButton" type="submit">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");
            const fileInput = document.getElementById("logoUpload");
            const fileName = document.getElementById("fileName");

            // File upload display
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileName.textContent = this.files[0].name;
                } else {
                    fileName.textContent = 'No file chosen';
                }
            });

            // File upload button click
            document.querySelector('.file-upload-btn').addEventListener('click', function() {
                fileInput.click();
            });

            if (!form || !submitButton) return;

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Updating...
                `;

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
                    return;
                }

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

            // Preview logo on hover
            const logoPreview = document.querySelector('.school-logo-preview');
            if (logoPreview) {
                logoPreview.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.1) rotate(5deg)';
                });

                logoPreview.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1) rotate(0)';
                });
            }
        });
    </script>
@endsection
