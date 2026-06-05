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
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-600: #6c757d;
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            position: relative;
        }

        /* Mobile Container */
        .dashboard-container {
            max-width: 100%;
            padding: 12px;
            position: relative;
            z-index: 1;
        }

        /* Mobile Card */
        .mobile-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        /* Card Header */
        .mobile-card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 16px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .header-title-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .header-title-section h3 {
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }

        /* Add Button */
        .btn-add-mobile {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-add-mobile:active {
            transform: scale(0.97);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .stat-card-mobile {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .stat-card-mobile i {
            font-size: 20px;
            color: white;
            margin-bottom: 6px;
            display: block;
        }

        .stat-card-mobile .stat-label {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 4px;
        }

        .stat-card-mobile .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: white;
        }

        /* Accountants Grid */
        .accountants-grid {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Accountant Card */
        .accountant-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .accountant-card:active {
            transform: scale(0.98);
        }

        /* Card Header */
        .accountant-card-header {
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--gray-200);
            background: linear-gradient(135deg, var(--gray-100) 0%, white 100%);
        }

        .accountant-avatar-mobile {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
        }

        .accountant-info-header {
            flex: 1;
        }

        .accountant-name-mobile {
            font-size: 15px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        /* Status Badge */
        .status-badge-card {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-active-card {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactive-card {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Card Body */
        .accountant-card-body {
            padding: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 12px;
        }

        .info-item-mobile {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }

        .info-item-mobile i {
            width: 28px;
            height: 28px;
            background: var(--gray-100);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 11px;
        }

        .info-item-mobile .info-label {
            color: var(--gray-600);
            font-size: 9px;
        }

        .info-item-mobile .info-value {
            color: var(--dark);
            font-size: 12px;
            font-weight: 500;
        }

        /* Gender Badge */
        .gender-badge-mobile {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .gender-male-badge {
            background: #e3f2fd;
            color: #1565c0;
        }

        .gender-female-badge {
            background: #fce4ec;
            color: #c2185b;
        }

        /* Phone Link */
        .phone-link-mobile {
            color: var(--dark);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
        }

        /* Card Actions */
        .card-actions {
            display: flex;
            gap: 8px;
            padding-top: 12px;
            border-top: 1px solid var(--gray-200);
            margin-top: 8px;
        }

        .card-action-btn {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .card-action-btn.view {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .card-action-btn.warning {
            background: linear-gradient(135deg, #f6c23e 0%, #f4b619 100%);
            color: white;
        }

        .card-action-btn.success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
        }

        .card-action-btn.danger {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
            color: white;
        }

        .card-action-btn:active {
            transform: scale(0.97);
        }

        /* Empty State */
        .empty-state-mobile {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69b 100%);
            border-radius: 16px;
            margin: 16px;
        }

        .empty-state-mobile i {
            font-size: 50px;
            color: #ffc107;
            margin-bottom: 15px;
        }

        /* Modal Mobile Optimized */
        .modal-mobile .modal-content {
            border-radius: 20px;
            border: none;
            margin: 16px;
        }

        .modal-mobile .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 16px;
        }

        .modal-mobile .modal-body {
            padding: 16px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-mobile .modal-footer {
            border: none;
            padding: 12px 16px;
            background: var(--gray-100);
        }

        /* Form Controls */
        .form-group-mobile {
            margin-bottom: 14px;
        }

        .form-label-mobile {
            font-weight: 600;
            font-size: 12px;
            color: var(--dark);
            margin-bottom: 6px;
            display: block;
        }

        .form-label-mobile .required {
            color: var(--danger);
        }

        .form-control-mobile {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .form-control-mobile:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .form-select-mobile {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            font-size: 13px;
            background: white;
        }

        .note-text {
            font-size: 10px;
            color: var(--gray-600);
            margin-top: 4px;
        }

        /* Info Box */
        .info-box-mobile {
            background: var(--gray-100);
            border-radius: 10px;
            padding: 10px;
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-box-mobile i {
            font-size: 18px;
            color: var(--primary);
        }

        .info-box-mobile .small {
            font-size: 11px;
            color: var(--gray-600);
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: white;
            border-radius: 12px;
            padding: 12px 20px;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 10000;
            transition: transform 0.3s ease;
            min-width: 200px;
            justify-content: center;
        }

        .toast-notification.show {
            transform: translateX(-50%) translateY(0);
        }

        .toast-success {
            border-left: 4px solid #28a745;
        }

        .toast-error {
            border-left: 4px solid var(--danger);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .dashboard-container {
                padding: 8px;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .header-row {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-add-mobile {
                justify-content: center;
            }

            .stats-grid {
                gap: 6px;
            }

            .stat-card-mobile {
                padding: 8px;
            }

            .stat-value {
                font-size: 16px;
            }

            .card-actions {
                flex-wrap: wrap;
            }

            .card-action-btn {
                min-width: calc(50% - 4px);
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1c2c 0%, #2a2d4a 100%);
            }

            .mobile-card {
                background: #2d3748;
            }

            .accountant-card {
                background: #2d3748;
                border-color: #4a5568;
            }

            .accountant-card-header {
                background: #374151;
                border-bottom-color: #4a5568;
            }

            .accountant-name-mobile {
                color: #f8f9fa;
            }

            .info-item-mobile i {
                background: #374151;
                color: var(--primary);
            }

            .info-item-mobile .info-value {
                color: #f8f9fa;
            }

            .phone-link-mobile {
                color: #f8f9fa;
            }

            .form-control-mobile,
            .form-select-mobile {
                background: #374151;
                border-color: #4a5568;
                color: #f8f9fa;
            }

            .form-label-mobile {
                color: #f8f9fa;
            }

            .modal-mobile .modal-content {
                background: #2d3748;
            }

            .modal-mobile .modal-footer {
                background: #374151;
            }

            .info-box-mobile {
                background: #374151;
            }

            .info-box-mobile .small {
                color: #adb5bd;
            }

            .toast-notification {
                background: #2d3748;
                color: #f8f9fa;
            }
        }
    </style>

    <div class="dashboard-container">
        <div class="mobile-card">
            <!-- Header -->
            <div class="mobile-card-header">
                <div class="header-row">
                    <div class="header-title-section">
                        <div class="header-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div>
                            <h3>Accountants</h3>
                        </div>
                    </div>
                    <button type="button" class="btn-add-mobile" data-bs-toggle="modal" data-bs-target="#addAccountantModal">
                        <i class="fas fa-user-plus"></i> Add Accountant
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            @php
                $activeCount = $accountant->filter(fn($a) => $a->status == 1)->count();
                $maleCount = $accountant->filter(fn($a) => strtolower($a->gender) == 'male')->count();
                $femaleCount = $accountant->filter(fn($a) => strtolower($a->gender) == 'female')->count();
            @endphp
            <div class="stats-grid">
                <div class="stat-card-mobile">
                    <i class="fas fa-user-check"></i>
                    <div class="stat-label">Active</div>
                    <div class="stat-value">{{ $activeCount }}</div>
                </div>
                <div class="stat-card-mobile">
                    <i class="fas fa-male"></i>
                    <div class="stat-label">Male</div>
                    <div class="stat-value">{{ $maleCount }}</div>
                </div>
                <div class="stat-card-mobile">
                    <i class="fas fa-female"></i>
                    <div class="stat-label">Female</div>
                    <div class="stat-value">{{ $femaleCount }}</div>
                </div>
            </div>

            <!-- Accountants Grid -->
            @if ($accountant->isEmpty())
                <div class="empty-state-mobile">
                    <i class="fas fa-calculator"></i>
                    <h6 class="mt-2">No Accountants Found</h6>
                    <p class="text-muted small">Tap "Add Accountant" to register</p>
                </div>
            @else
                <div class="accountants-grid">
                    @foreach ($accountant as $row)
                        @php
                            $imageName = $row->image;
                            $imagePath = storage_path('app/public/profile/' . $imageName);
                            $avatarImage = (!empty($imageName) && file_exists($imagePath))
                                ? asset('storage/profile/' . $imageName)
                                : asset('storage/profile/' . ($row->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                        @endphp

                        <div class="accountant-card">
                            <!-- Card Header -->
                            <div class="accountant-card-header">
                                <img src="{{ $avatarImage }}" alt="Avatar" class="accountant-avatar-mobile">
                                <div class="accountant-info-header">
                                    <div class="accountant-name-mobile">{{ ucwords(strtolower($row->first_name . ' ' . $row->last_name)) }}</div>
                                </div>
                                @if ($row->status == 1)
                                    <span class="status-badge-card status-active-card">
                                        <i class="fas fa-circle" style="font-size: 6px;"></i> Active
                                    </span>
                                @else
                                    <span class="status-badge-card status-inactive-card">
                                        <i class="fas fa-circle" style="font-size: 6px;"></i> Blocked
                                    </span>
                                @endif
                            </div>

                            <!-- Card Body -->
                            <div class="accountant-card-body">
                                <div class="info-grid">
                                    <div class="info-item-mobile">
                                        <i class="fas fa-venus-mars"></i>
                                        <div>
                                            <div class="info-label">Gender</div>
                                            <div class="info-value">
                                                <span class="gender-badge-mobile {{ $row->gender == 'male' ? 'gender-male-badge' : 'gender-female-badge' }}">
                                                    <i class="fas {{ $row->gender == 'male' ? 'fa-mars' : 'fa-venus' }}"></i>
                                                    {{ ucfirst($row->gender) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-phone"></i>
                                        <div>
                                            <div class="info-label">Phone</div>
                                            <div class="info-value">
                                                <a href="tel:{{ $row->phone }}" class="phone-link-mobile">
                                                    <i class="fas fa-phone-alt"></i> {{ $row->phone }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-envelope"></i>
                                        <div>
                                            <div class="info-label">Email</div>
                                            <div class="info-value">
                                                @if($row->email)
                                                    <a href="mailto:{{ $row->email }}" class="phone-link-mobile">
                                                        <i class="fas fa-envelope"></i> {{ substr($row->email, 0, 20) }}{{ strlen($row->email) > 20 ? '...' : '' }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-item-mobile">
                                        <i class="fas fa-id-card"></i>
                                        <div>
                                            <div class="info-label">Staff ID</div>
                                            <div class="info-value">{{ strtoupper($row->member_id ?? 'N/A') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="card-actions">
                                    <a href="{{ route('Accountants.profile', ['id' => Hashids::encode($row->id)]) }}"
                                       class="card-action-btn view">
                                        <i class="fas fa-eye"></i> View
                                    </a>

                                    @if ($row->status == 1)
                                        <form action="{{ route('Accountants.block', ['id' => Hashids::encode($row->id)]) }}"
                                              method="POST"
                                              class="d-inline"
                                              style="flex: 1"
                                              onsubmit="return confirm('Block {{ $row->first_name }} {{ $row->last_name }}?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="card-action-btn warning">
                                                <i class="fas fa-ban"></i> Block
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('Accountants.unblock', ['id' => Hashids::encode($row->id)]) }}"
                                              method="POST"
                                              class="d-inline"
                                              style="flex: 1"
                                              onsubmit="return confirm('Unblock {{ $row->first_name }} {{ $row->last_name }}?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="card-action-btn success">
                                                <i class="fas fa-check"></i> Unblock
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('Accountants.delete', ['id' => Hashids::encode($row->id)]) }}"
                                          method="POST"
                                          class="d-inline"
                                          style="flex: 1"
                                          onsubmit="return confirm('Delete {{ $row->first_name }} {{ $row->last_name }}? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="card-action-btn danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Add Accountant Modal -->
    <div class="modal fade modal-mobile" id="addAccountantModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Register Accountant</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form class="needs-validation" novalidate action="{{ route('Accountants.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group-mobile">
                            <label class="form-label-mobile">First Name <span class="required">*</span></label>
                            <input type="text" name="fname" class="form-control-mobile" value="{{ old('fname') }}" required>
                            @error('fname')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Surname <span class="required">*</span></label>
                            <input type="text" name="lname" class="form-control-mobile" value="{{ old('lname') }}" required>
                            @error('lname')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Email</label>
                            <input type="email" name="email" class="form-control-mobile" value="{{ old('email') }}">
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Gender <span class="required">*</span></label>
                            <select name="gender" class="form-select-mobile" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            @error('gender')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Phone <span class="required">*</span></label>
                            <input type="tel" name="phone" class="form-control-mobile" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-mobile">
                            <label class="form-label-mobile">Profile Picture</label>
                            <input type="file" name="image" class="form-control-mobile" accept="image/*">
                            <div class="note-text">Max: 2MB (JPG, PNG)</div>
                            @error('image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="info-box-mobile">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <div class="small fw-bold">Accountant Login Credentials</div>
                                <div class="small">Login credentials will be sent via SMS after registration.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="saveButton">
                            <i class="fas fa-save me-2"></i>Save Accountant
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

            if (form && submitButton) {
                form.addEventListener("submit", function(event) {
                    event.preventDefault();

                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                    if (!form.checkValidity()) {
                        form.classList.add("was-validated");
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Save Accountant';

                        showToast('Please fill all required fields', 'error');

                        const firstInvalid = form.querySelector(':invalid');
                        if (firstInvalid) {
                            firstInvalid.scrollIntoView({behavior: 'smooth', block: 'center'});
                        }
                        return;
                    }

                    setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            }

            // Toast notification
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast-notification toast-${type}`;
                toast.innerHTML = `
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                    <span>${message}</span>
                `;
                document.body.appendChild(toast);

                setTimeout(() => toast.classList.add('show'), 100);
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // Phone number formatting
            const phoneInput = document.querySelector('input[name="phone"]');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 10) value = value.slice(0, 10);
                    e.target.value = value;
                });
            }

            // Touch feedback for cards
            const cards = document.querySelectorAll('.accountant-card');
            cards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                card.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
        });
    </script>
@endsection
