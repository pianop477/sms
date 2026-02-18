@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f94144;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                radial-gradient(circle at 70% 30%, rgba(67, 97, 238, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 30% 70%, rgba(63, 55, 201, 0.1) 0%, transparent 30%);
            animation: rotate 60s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(100px, -100px) scale(1.2); }
            50% { transform: translate(200px, 0) scale(0.8); }
            75% { transform: translate(100px, 100px) scale(1.1); }
        }

        /* Main Container */
        .dashboard-container {
            max-width: 1600px;
            margin: 30px auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* Modern Card */
        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Card Header */
        .card-header-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 20px 25px;
            position: relative;
            overflow: visible;
        }

        .card-header-modern::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 60%);
            /* animation: rotate 20s linear infinite; */
        }

        .card-header-modern::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--warning), var(--success), var(--accent));
        }

        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header-title {
            color: white;
            margin: 0;
        }

        .header-title h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .header-title p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Add Button */
        .btn-add-modern {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }

        .btn-add-modern:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Card Body */
        .card-body-modern {
            padding: 25px;
        }

        /* Table Container */
        .table-container-modern {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Modern Table */
        .table-modern {
            width: 100%;
            border-collapse: collapse;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, #2b3d5c 0%, #1a2a44 100%);
            /* color: white; */
            font-weight: 600;
            padding: 12px 12px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            white-space: nowrap;
        }

        .table-modern tbody td {
            padding: 12px 12px;
            border-bottom: 1px solid #edf2f7;
            color: #4a5568;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table-modern tbody tr {
            transition: all 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background: #f7fafc;
        }

        /* Accountant Info */
        .accountant-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .accountant-avatar-modern {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
        }

        .accountant-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }

        /* Gender Badge */
        .gender-badge {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .gender-male {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .gender-female {
            background: linear-gradient(135deg, #e83e8c 0%, #c2185b 100%);
        }

        /* Phone Link */
        .phone-link {
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .phone-link i {
            font-size: 0.8rem;
        }

        /* Status Badge */
        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .status-active {
            background: #c6f6d5;
            color: #22543d;
        }

        .status-blocked {
            background: #fed7d7;
            color: #742a2a;
        }

        /* Action Icons */
        .action-icons {
            display: flex;
            gap: 4px;
            justify-content: center;
            flex-wrap: nowrap;
        }

        .action-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .action-icon.view {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .action-icon.warning {
            background: linear-gradient(135deg, #f6c23e 0%, #f4b619 100%);
        }

        .action-icon.success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }

        .action-icon.danger {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
        }

        .action-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Empty State */
        .empty-state-modern {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69b 100%);
            border-radius: 16px;
            border: 2px dashed #ffc107;
        }

        .empty-state-modern i {
            font-size: 50px;
            color: #ffc107;
            margin-bottom: 15px;
        }

        /* Modal Styles */
        .modal-modern .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .modal-modern .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 15px 20px;
        }

        .modal-modern .modal-body {
            padding: 20px;
        }

        .modal-modern .modal-footer {
            border: none;
            padding: 15px 20px;
            background: #f8f9fa;
        }

        /* Form Controls */
        .form-group-modern {
            margin-bottom: 15px;
        }

        .form-label-modern {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 4px;
            font-size: 0.85rem;
        }

        .form-label-modern .required {
            color: var(--danger);
        }

        .form-control-modern {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control-modern:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .table-modern {
                display: block;
                overflow-x: auto;
            }

            .action-icons {
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-add-modern {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .card-header-modern {
                padding: 15px;
            }

            .header-title h3 {
                font-size: 1.2rem;
            }

            .card-body-modern {
                padding: 15px;
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1c2c 0%, #2a2d4a 100%);
            }

            .modern-card {
                background: rgba(33, 37, 41, 0.95);
            }

            .table-modern tbody td {
                color: #e9ecef;
                border-bottom-color: #495057;
            }

            .table-modern tbody tr:hover {
                background: #343a40;
            }

            .accountant-name {
                color: #e9ecef;
            }

            .status-active {
                background: #22543d;
                color: #c6f6d5;
            }

            .status-blocked {
                background: #742a2a;
                color: #fed7d7;
            }

            .form-control-modern {
                background: #2b3035;
                border-color: #495057;
                color: #e9ecef;
            }
        }
    </style>

    <div class="animated-bg"></div>
    <div class="particles"></div>
    <div class="loading-spinner" id="loadingSpinner"></div>

    <div class="dashboard-container">
        <div class="modern-card">
            <!-- Header -->
            <div class="card-header-modern">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="header-title">
                            <h3>Accountants Management</h3>
                        </div>
                    </div>
                    <button type="button" class="btn-add-modern" data-bs-toggle="modal" data-bs-target="#addAccountantModal">
                        <i class="fas fa-user-plus"></i>
                        <span>Add Accountant</span>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body-modern">
                @if ($accountant->isEmpty())
                    <div class="empty-state-modern">
                        <i class="fas fa-calculator"></i>
                        <h6>No Accountants Found</h6>
                        <p class="text-muted small">Click "Add Accountant" to register</p>
                    </div>
                @else
                    <div class="table-container-modern">
                        <table class="table-modern" id="myTable">
                            <thead>
                                <tr>
                                    <th class="text-white">#</th>
                                    <th>Accountant information</th>
                                    <th>Gender</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($accountant as $row)
                                    <tr>
                                        <td><span class="fw-bold">{{ $loop->iteration }}</span></td>
                                        <td>
                                            <div class="accountant-info">
                                                @php
                                                    $imageName = $row->image;
                                                    $imagePath = storage_path('app/public/profile/' . $imageName);
                                                    $avatarImage = (!empty($imageName) && file_exists($imagePath))
                                                        ? asset('storage/profile/' . $imageName)
                                                        : asset('storage/profile/' . ($row->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                                                @endphp
                                                <img src="{{ $avatarImage }}" alt="Avatar" class="accountant-avatar-modern">
                                                <span class="accountant-name">{{ ucwords(strtolower($row->first_name . ' ' . $row->last_name)) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="gender-badge {{ $row->gender == 'male' ? 'gender-male' : 'gender-female' }}">
                                                {{ strtoupper(substr($row->gender, 0, 1)) }}
                                            </div>
                                        </td>
                                        <td>
                                            <a href="tel:{{ $row->phone }}" class="phone-link">
                                                <i class="fas fa-phone"></i>
                                                {{ $row->phone }}
                                            </a>
                                        </td>
                                        <td>
                                            @if ($row->status == 1)
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-circle"></i> Active
                                                </span>
                                            @else
                                                <span class="status-badge status-blocked">
                                                    <i class="fas fa-circle"></i> Blocked
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                                <a href="{{ route('Accountants.profile', ['id' => Hashids::encode($row->id)]) }}"
                                                   class="action-icon view"
                                                   title="View Profile">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if ($row->status == 1)
                                                    <form action="{{ route('Accountants.block', ['id' => Hashids::encode($row->id)]) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Block {{ $row->first_name }} {{ $row->last_name }}?')">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-icon warning" title="Block">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('Accountants.unblock', ['id' => Hashids::encode($row->id)]) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Unblock {{ $row->first_name }} {{ $row->last_name }}?')">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-icon success" title="Unblock">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('Accountants.delete', ['id' => Hashids::encode($row->id)]) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Delete {{ $row->first_name }} {{ $row->last_name }}? This cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-icon danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Accountant Modal -->
    <div class="modal fade modal-modern" id="addAccountantModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>
                        Accountant Registration Form
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form class="needs-validation" novalidate action="{{ route('Accountants.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        First Name <span class="required">*</span>
                                    </label>
                                    <input type="text" name="fname" class="form-control-modern" value="{{ old('fname') }}" required>
                                    @error('fname')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        Surname <span class="required">*</span>
                                    </label>
                                    <input type="text" name="lname" class="form-control-modern" value="{{ old('lname') }}" required>
                                    @error('lname')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Email</label>
                                    <input type="email" name="email" class="form-control-modern" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        Gender <span class="required">*</span>
                                    </label>
                                    <select name="gender" class="form-control-modern" required>
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    @error('gender')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        Phone <span class="required">*</span>
                                    </label>
                                    <input type="text" name="phone" class="form-control-modern" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Profile Picture</label>
                                    <input type="file" name="image" class="form-control-modern" accept="image/*">
                                    <div class="note-text small text-muted mt-1">Max: 2MB (JPG, PNG)</div>
                                    @error('image')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 p-3 bg-light rounded">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-2 text-info"></i>
                                Accountant login credentials will be sent via SMS after registration.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="saveButton">
                            <i class="fas fa-save me-2"></i>
                            Save Accountant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Form handling
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");
            const loadingSpinner = document.getElementById('loadingSpinner');

            if (form && submitButton) {
                form.addEventListener("submit", function(event) {
                    event.preventDefault();

                    // Show loading spinner
                    loadingSpinner.style.display = 'block';

                    // Disable button
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Saving...
                    `;

                    // Validate form
                    if (!form.checkValidity()) {
                        form.classList.add("was-validated");
                        loadingSpinner.style.display = 'none';
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Save Accountant';

                        // Show error toast
                        showToast('Please fill all required fields', 'error');

                        // Scroll to first invalid
                        const firstInvalid = form.querySelector(':invalid');
                        if (firstInvalid) {
                            firstInvalid.scrollIntoView({behavior: 'smooth', block: 'center'});
                        }
                        return;
                    }

                    // Submit after delay
                    setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            }

            // Create floating particles
            function createParticles() {
                const particlesContainer = document.querySelector('.particles');
                if (!particlesContainer) return;

                for (let i = 0; i < 30; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.width = Math.random() * 10 + 3 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 20 + 's';
                    particle.style.animationDuration = Math.random() * 10 + 15 + 's';
                    particlesContainer.appendChild(particle);
                }
            }
            createParticles();

            // Toast notification
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast-notification toast-${type}`;
                toast.innerHTML = `
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} fa-2x"></i>
                    <span>${message}</span>
                `;
                document.body.appendChild(toast);

                setTimeout(() => toast.classList.add('show'), 100);
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // Input animations
            document.querySelectorAll('.form-control-modern').forEach(input => {
                input.addEventListener('focus', () => {
                    input.style.transform = 'translateY(-2px)';
                });
                input.addEventListener('blur', () => {
                    input.style.transform = 'translateY(0)';
                });
            });

            // Phone number formatting
            const phoneInput = document.querySelector('input[name="phone"]');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 10) value = value.slice(0, 10);
                    e.target.value = value;
                });
            }

            // Reset button state on page show
            window.addEventListener("pageshow", function() {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Save Accountant';
                }
                if (loadingSpinner) {
                    loadingSpinner.style.display = 'none';
                }
            });
        });
    </script>

    <style>
        /* Toast notification styles */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 12px;
            padding: 12px 20px;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            z-index: 10000;
            border-left: 5px solid;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-success {
            border-left-color: #28a745;
        }

        .toast-error {
            border-left-color: var(--danger);
        }

        /* Loading spinner */
        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 9999;
            display: none;
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
    </style>
@endsection
