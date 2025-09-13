@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --success: #28a745;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            /* padding: 20px; */
            overflow-x: hidden;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: visible;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 25px 30px;
            position: relative;
            overflow: visible;
            /* z-index: 100; */
        }

        .card-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            transform: rotate(30deg);
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            font-size: 24px;
        }

        .card-body {
            padding: 10px;
            position: relative;
            z-index: 1;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 1;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
            text-decoration: none;
        }

        .form-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }

        .form-label {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .required-star {
            color: var(--danger);
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        textarea.form-control-custom {
            min-height: 120px;
            resize: vertical;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-success-custom:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .btn-success-custom:disabled {
            opacity: 0.7;
            transform: none;
            box-shadow: none;
        }

        .invalid-feedback {
            font-weight: 600;
            color: var(--danger);
            margin-top: 5px;
        }

        .text-danger small {
            font-weight: 600;
        }

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        .driver-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 24px;
            margin: 0 auto 20px;
        }

        .current-info {
            background: linear-gradient(135deg, rgba(78, 84, 200, 0.1) 0%, rgba(143, 148, 251, 0.1) 100%);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
        }

        .info-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .message-textarea {
            width: 100%;
            min-height: 180px;
            resize: vertical;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }
            .checkbox-card {
                padding: 12px;
            }

            .form-control-custom {
                padding: 10px 12px;
            }

            .header-title {
                font-size: 20px;
            }

            .form-section {
                padding: 20px;
            }

            .btn-success-custom {
                width: 100%;
                justify-content: center;
            }
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title text-white">
                            <i class="fas fa-user-edit me-2"></i> Edit Driver & Bus Information
                        </h4>
                        <p class="mb-0 text-white"> Update driver details and bus information</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('Transportation.index')}}" class="btn btn-back float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-bus-alt floating-icons"></i>
            </div>

            <div class="card-body">
                <!-- Current Information -->
                <div class="current-info">
                    <h6 class="info-title">
                        <i class="fas fa-info-circle"></i> Current Information
                    </h6>
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="driver-avatar text-capitalize">
                                {{ substr($transport->driver_name, 0, 1) }}
                            </div>
                            <div class="text-capitalize fw-bold">{{ $transport->driver_name }}</div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted">Bus Number:</small>
                                    <div class="fw-bold text-uppercase">{{ $transport->bus_no }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Gender:</small>
                                    <div class="fw-bold text-capitalize">{{ $transport->gender }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Phone:</small>
                                    <div class="fw-bold">{{ $transport->phone }}</div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <small class="text-muted">Current Route:</small>
                                    <div class="fw-bold text-capitalize">{{ $transport->routine }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <form class="needs-validation" novalidate action="{{route('transport.update.records', ['transport' => Hashids::encode($transport->id)])}}" method="POST" enctype="multipart/form-data" id="editForm">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <h5 class="mb-4 text-primary">
                            <i class="fas fa-user-tie me-2"></i> Driver Information
                        </h5>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="fullname" class="form-label">
                                    <i class="fas fa-user text-primary"></i>
                                    Driver's Full Name <span class="required-star">*</span>
                                </label>
                                <input type="text" name="fullname" class="form-control-custom" id="fullname"
                                       placeholder="Enter full name" value="{{ old('fullname', $transport->driver_name) }}" required>
                                <div class="invalid-feedback">
                                    Please provide driver's name
                                </div>
                                @error('fullname')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="gender" class="form-label">
                                    <i class="fas fa-venus-mars text-primary"></i>
                                    Gender <span class="required-star">*</span>
                                </label>
                                <select name="gender" id="gender" class="form-control-custom" required>
                                    <option value="{{ $transport->gender }}" selected>{{ ucfirst($transport->gender) }}</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select gender
                                </div>
                                @error('gender')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone text-primary"></i>
                                    Mobile Phone <span class="required-star">*</span>
                                </label>
                                <input type="text" name="phone" class="form-control-custom" id="phone"
                                       placeholder="Enter phone number" required value="{{ old('phone', $transport->phone) }}">
                                <div class="invalid-feedback">
                                    Please provide phone number
                                </div>
                                @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="mb-4 text-primary mt-4">
                            <i class="fas fa-bus me-2"></i> Bus Information
                        </h5>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="bus_no" class="form-label">
                                    <i class="fas fa-bus-alt text-primary"></i>
                                    School Bus Number <span class="required-star">*</span>
                                </label>
                                <input type="text" name="bus_no" class="form-control-custom" id="bus_no"
                                       placeholder="Enter bus number" required value="{{ old('bus_no', $transport->bus_no) }}">
                                <div class="invalid-feedback">
                                    Please provide bus number
                                </div>
                                @error('bus_no')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8 mb-4">
                                <label for="routine" class="form-label">
                                    <i class="fas fa-route text-primary"></i>
                                    School Bus Route Description
                                </label>
                                <textarea name="routine" class="form-control-custom message-textarea" id="routine"
                                          rows="4" placeholder="Describe the bus route">{{ old('routine', $transport->routine) }}</textarea>
                                @error('routine')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-success-custom" id="saveButton" type="submit">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("editForm");
            const submitButton = document.getElementById("saveButton");

            if (form && submitButton) {
                form.addEventListener("submit", function (event) {
                    event.preventDefault();

                    if (!form.checkValidity()) {
                        event.stopPropagation();
                        form.classList.add("was-validated");

                        // Scroll to first invalid field
                        const invalidElements = form.querySelectorAll(':invalid');
                        if (invalidElements.length > 0) {
                            invalidElements[0].scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                        return;
                    }

                    // Disable button and show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Saving Changes...`;

                    // Submit the form after a brief delay
                    setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            }

            // Reset button state when page is shown (for back button navigation)
            window.addEventListener("pageshow", function(event) {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-2"></i> Save Changes';
                }
            });
        });
    </script>
@endsection
