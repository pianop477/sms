@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --success: #00b894;
            --info: #00cec9;
            --warning: #fdcb6e;
            --danger: #e17055;
            --dark: #2d3436;
            --light: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 8px;
            margin: 0;
        }

        /* Main Container */
        .sms-dashboard {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Header */
        .dashboard-header-sms {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 15px;
            text-align: center;
        }

        .header-title {
            font-weight: 700;
            font-size: 1.3rem;
            margin: 0;
            color: white;
        }

        .header-subtitle {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-top: 5px;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Main Grid - Mobile First */
        .dashboard-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 15px;
        }

        @media (min-width: 992px) {
            .dashboard-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                padding: 25px;
            }

            .dashboard-header-sms {
                padding: 20px;
            }

            .header-title {
                font-size: 1.8rem;
            }

            .header-subtitle {
                font-size: 1rem;
            }
        }

        /* Card */
        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(78, 84, 200, 0.1);
        }

        @media (min-width: 768px) {
            .dashboard-card {
                padding: 20px;
            }
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(78, 84, 200, 0.1);
        }

        .card-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .card-icon i {
            font-size: 18px;
            color: white;
        }

        .card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            color: var(--dark);
        }

        .card-subtitle {
            color: #666;
            font-size: 0.75rem;
            margin-top: 2px;
        }

        @media (min-width: 768px) {
            .card-icon {
                width: 45px;
                height: 45px;
                margin-right: 15px;
            }

            .card-icon i {
                font-size: 20px;
            }

            .card-title {
                font-size: 1.2rem;
            }

            .card-subtitle {
                font-size: 0.85rem;
            }
        }

        /* Classes Section - Improved for mobile */
        .classes-section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        /* Classes Grid - Responsive */
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        @media (min-width: 480px) {
            .classes-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }
        }

        @media (min-width: 768px) {
            .classes-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
                gap: 10px;
            }
        }

        .class-option {
            position: relative;
        }

        .class-checkbox {
            position: absolute;
            opacity: 0;
        }

        .class-label {
            display: block;
            padding: 10px 8px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            font-weight: 500;
            font-size: 0.85rem;
            word-break: break-word;
        }

        .class-checkbox:checked+.class-label {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-color: var(--primary);
            position: relative;
        }

        .class-checkbox:checked+.class-label::after {
            content: '✓';
            position: absolute;
            top: -6px;
            right: -6px;
            width: 18px;
            height: 18px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: white;
        }

        /* Groups Section */
        .group-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .group-option {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .group-option:active {
            background: #f0f0f0;
        }

        .group-option input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            flex-shrink: 0;
        }

        .group-icon {
            width: 32px;
            height: 32px;
            background: rgba(78, 84, 200, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .group-icon i {
            font-size: 14px;
            color: var(--primary);
        }

        .group-info {
            flex: 1;
            min-width: 0;
        }

        .group-info h6 {
            margin: 0;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.85rem;
        }

        .group-info small {
            color: #666;
            font-size: 0.7rem;
            display: block;
            margin-top: 2px;
        }

        /* Message Area */
        .message-container {
            margin-bottom: 20px;
        }

        .message-textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
            resize: vertical;
            background: #f8f9fa;
            font-family: inherit;
        }

        .message-textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.1);
        }

        .char-counter {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 8px;
            padding: 8px 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        @media (min-width: 480px) {
            .char-counter {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        .char-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
        }

        .char-count {
            font-weight: 600;
            color: var(--primary);
        }

        /* SMS History - Card View for Mobile */
        .sms-history {
            margin-top: 15px;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .history-title {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .history-stats {
            font-size: 0.8rem;
            color: var(--primary);
            font-weight: 600;
            padding: 4px 10px;
            background: rgba(78, 84, 200, 0.1);
            border-radius: 15px;
        }

        /* Mobile Cards View for SMS History */
        .sms-cards-view {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sms-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.2s ease;
        }

        .sms-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f0f0;
        }

        .sms-recipient-info {
            flex: 1;
        }

        .sms-phone {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--dark);
        }

        .sms-time {
            font-size: 0.7rem;
            color: #999;
            margin-top: 2px;
        }

        .sms-status {
            flex-shrink: 0;
        }

        .sms-card-body {
            margin-bottom: 8px;
        }

        .sms-message-preview {
            font-size: 0.85rem;
            color: #555;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .sms-message-full {
            display: none;
        }

        .sms-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 8px;
            border-top: 1px solid #f0f0f0;
        }

        .sms-count {
            font-size: 0.75rem;
            color: var(--primary);
            font-weight: 600;
        }

        .view-details-btn {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 0.75rem;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .view-details-btn:active {
            background: rgba(78, 84, 200, 0.1);
        }

        /* Desktop Table View */
        .sms-table-container {
            display: none;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            overflow-x: auto;
        }

        @media (min-width: 768px) {
            .sms-cards-view {
                display: none;
            }

            .sms-table-container {
                display: block;
            }
        }

        .sms-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .sms-table thead {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .sms-table th {
            padding: 12px 15px;
            color: white;
            font-weight: 600;
            text-align: left;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .sms-table td {
            padding: 12px 15px;
            color: #495057;
            vertical-align: middle;
            font-size: 0.85rem;
            border-bottom: 1px solid #f0f0f0;
        }

        /* Status Badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .status-delivered {
            background: rgba(0, 184, 148, 0.1);
            color: #00b894;
            border: 1px solid rgba(0, 184, 148, 0.2);
        }

        .status-pending {
            background: rgba(253, 203, 110, 0.1);
            color: #f39c12;
            border: 1px solid rgba(253, 203, 110, 0.2);
        }

        .status-failed {
            background: rgba(225, 112, 85, 0.1);
            color: #e17055;
            border: 1px solid rgba(225, 112, 85, 0.2);
        }

        /* Submit Button */
        .submit-section {
            padding: 15px;
            background: rgba(248, 249, 250, 0.95);
            border-top: 1px solid rgba(78, 84, 200, 0.1);
            text-align: center;
        }

        .send-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 0.95rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .send-btn:active {
            transform: scale(0.98);
        }

        @media (min-width: 768px) {
            .submit-section {
                padding: 20px;
            }

            .send-btn {
                width: auto;
                padding: 12px 35px;
                font-size: 1rem;
            }

            .send-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(78, 84, 200, 0.35);
            }
        }

        /* Modal */
        .modal-content {
            border-radius: 12px;
            border: none;
            margin: 10px;
        }

        @media (min-width: 768px) {
            .modal-content {
                margin: 0;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 12px 15px;
            border-radius: 12px 12px 0 0;
        }

        .modal-title {
            font-size: 1rem;
            font-weight: 600;
        }

        .modal-body {
            padding: 15px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 12px 15px;
        }

        /* Alerts */
        .alert {
            margin: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            padding: 12px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 30px 20px;
        }

        .empty-state i {
            font-size: 2rem;
            color: #ccc;
            margin-bottom: 10px;
        }

        .empty-state h6 {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .empty-state p {
            font-size: 0.8rem;
        }

        /* Loading Spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }
    </style>

    <!-- Session Alerts -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Dashboard -->
    <div class="sms-dashboard">
        <!-- Header -->
        <div class="dashboard-header-sms">
            <h1 class="header-title">
                <i class="fas fa-bullhorn me-2"></i> SMS Broadcast
            </h1>
            <p class="header-subtitle">
                Communicate with parents, teachers, and staff
            </p>
        </div>

        <!-- Main Grid -->
        <form class="dashboard-grid" id="smsForm" novalidate action="{{ route('Send.message.byNext') }}" method="POST">
            @csrf

            <!-- Left Column - Recipients -->
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Recipients</h3>
                        <p class="card-subtitle">Select message recipients</p>
                    </div>
                </div>

                <!-- Classes Selection -->
                <div class="classes-section">
                    <div class="section-title">
                        <i class="fas fa-graduation-cap me-2"></i> Classes
                    </div>
                    <div class="classes-grid">
                        @forelse ($classes as $class)
                            <div class="class-option">
                                <input type="checkbox" class="class-checkbox" name="classes[]" value="{{ $class->id }}"
                                    id="class{{ $class->id }}"
                                    {{ in_array($class->id, old('classes', [])) ? 'checked' : '' }}>
                                <label class="class-label" for="class{{ $class->id }}">
                                    {{ strtoupper($class->class_code) }}
                                </label>
                            </div>
                        @empty
                            <div class="text-center py-4" style="grid-column: 1 / -1;">
                                <i class="fas fa-inbox fa-lg text-muted mb-2"></i>
                                <p class="text-muted mb-0">No classes available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Groups Selection -->
                <div>
                    <div class="section-title">
                        <i class="fas fa-layer-group me-2"></i> Groups
                    </div>
                    <div class="group-grid">
                        <label class="group-option">
                            <input type="checkbox" name="send_to_all" value="1" {{ old('send_to_all') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="group-info">
                                <h6>All Parents</h6>
                                <small>All students from all classes</small>
                            </div>
                        </label>

                        <label class="group-option">
                            <input type="checkbox" name="send_with_transport" value="1" {{ old('send_with_transport') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-bus"></i>
                            </div>
                            <div class="group-info">
                                <h6>With Transport</h6>
                                <small>Parents whose children use school transport</small>
                            </div>
                        </label>

                        <label class="group-option">
                            <input type="checkbox" name="send_without_transport" value="1" {{ old('send_without_transport') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-walking"></i>
                            </div>
                            <div class="group-info">
                                <h6>Without Transport</h6>
                                <small>Parents whose children don't use transport</small>
                            </div>
                        </label>

                        <label class="group-option">
                            <input type="checkbox" name="send_to_teachers" value="1" {{ old('send_to_teachers') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="group-info">
                                <h6>Teaching Staff</h6>
                                <small>All teachers and academic staff</small>
                            </div>
                        </label>

                        <label class="group-option">
                            <input type="checkbox" name="send_to_other_staff" value="1" {{ old('send_to_other_staff') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="group-info">
                                <h6>Non-Teaching Staff</h6>
                                <small>All support staff</small>
                            </div>
                        </label>

                        <label class="group-option">
                            <input type="checkbox" name="send_to_drivers" value="1" {{ old('send_to_drivers') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="group-info">
                                <h6>Drivers</h6>
                                <small>School transport drivers only</small>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Column - Message & History -->
            <div class="dashboard-card">
                <!-- Message Section -->
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-comment-alt"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Compose Message</h3>
                        <p class="card-subtitle">Write your announcement message</p>
                    </div>
                </div>

                <!-- Message Container -->
                <div class="message-container">
                    @php
                        $school = Auth::user()->school;
                        $isBasicPackage = $school && $school->package === 'basic';
                        $maxChars = $isBasicPackage ? 306 : 459;
                        $smsCount = $isBasicPackage ? 2 : 3;
                    @endphp

                    <textarea name="message_content" id="message_content"
                        class="message-textarea @error('message_content') is-invalid @enderror"
                        placeholder="Type your message here..."
                        required maxlength="{{ $maxChars }}">{{ old('message_content') }}</textarea>

                    @error('message_content')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror

                    <div class="char-counter">
                        <div class="char-info">
                            <span class="char-count" id="charCount">0</span>
                            <span class="text-muted">/ {{ $maxChars }} characters</span>
                        </div>
                        <div>
                            <span class="badge" style="background: {{ $isBasicPackage ? '#e17055' : '#4e54c8' }}; color: white; padding: 4px 8px; border-radius: 20px; font-size: 0.7rem;">
                                <i class="fas fa-{{ $isBasicPackage ? 'star' : 'crown' }} me-1"></i>
                                {{ $isBasicPackage ? 'Basic' : 'Premium' }} - Max {{ $smsCount }} SMS
                            </span>
                        </div>
                    </div>
                </div>

                <!-- SMS History -->
                <div class="sms-history">
                    <div class="history-header">
                        <h6 class="history-title">
                            <i class="fas fa-history me-2"></i> Recent Messages
                        </h6>
                        <span class="history-stats">{{ count($smsContents) }} total</span>
                    </div>

                    @if (count($smsContents) > 0)
                        <!-- Mobile Cards View -->
                        <div class="sms-cards-view">
                            @foreach ($smsContents as $sms)
                                <div class="sms-card">
                                    <div class="sms-card-header">
                                        <div class="sms-recipient-info">
                                            <div class="sms-phone">
                                                <i class="fas fa-phone-alt me-1" style="font-size: 0.7rem;"></i>
                                                {{ substr($sms['to'], -4) }}...
                                            </div>
                                            <div class="sms-time">
                                                <i class="far fa-clock me-1"></i>
                                                {{ \Carbon\Carbon::parse($sms['sentAt'])->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                        <div class="sms-status">
                                            @if ($sms['delivery'] == 'DELIVERED')
                                                <span class="status-badge status-delivered">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                            @elseif ($sms['delivery'] == 'PENDING')
                                                <span class="status-badge status-pending">
                                                    <i class="fas fa-clock"></i>
                                                </span>
                                            @else
                                                <span class="status-badge status-failed">
                                                    <i class="fas fa-times-circle"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="sms-card-body">
                                        <div class="sms-message-preview">
                                            {{ \Illuminate\Support\Str::limit($sms['text'], 60) }}
                                        </div>
                                    </div>
                                    <div class="sms-card-footer">
                                        <div class="sms-count">
                                            <i class="fas fa-envelope"></i> {{ $sms['smsCount'] }} SMS
                                        </div>
                                        <button type="button" class="view-details-btn" data-bs-toggle="modal"
                                            data-bs-target="#smsModal"
                                            data-full-text="{{ htmlspecialchars($sms['text']) }}"
                                            data-to="{{ $sms['to'] }}"
                                            data-from="{{ $sms['from'] }}"
                                            data-status="{{ $sms['delivery'] }}"
                                            data-sent-at="{{ \Carbon\Carbon::parse($sms['sentAt'])->format('d/m/Y H:i') }}"
                                            data-delivered-at="{{ \Carbon\Carbon::parse($sms['doneAt'])->format('d/m/Y H:i') }}">
                                            <i class="fas fa-eye me-1"></i> View Details
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table View -->
                        <div class="sms-table-container">
                            <table class="sms-table">
                                <thead>
                                    <tr>
                                        <th>Sent At</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Message</th>
                                        <th>Count</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($smsContents as $sms)
                                        <tr>
                                            <td style="white-space: nowrap;">
                                                {{ \Carbon\Carbon::parse($sms['sentAt'])->format('d/m/Y H:i') }}
                                            </td>
                                            <td>{{ $sms['from'] }}</td>
                                            <td>{{ substr($sms['to'], -4) }}...</td>
                                            <td>
                                                <div class="sms-preview">
                                                    <div class="sms-icon">
                                                        <i class="fas fa-sms"></i>
                                                    </div>
                                                    <div class="sms-text">
                                                        <a href="#" class="sms-preview-link" data-bs-toggle="modal"
                                                            data-bs-target="#smsModal"
                                                            data-full-text="{{ htmlspecialchars($sms['text']) }}"
                                                            data-to="{{ $sms['to'] }}"
                                                            data-from="{{ $sms['from'] }}"
                                                            data-status="{{ $sms['delivery'] }}"
                                                            data-sent-at="{{ \Carbon\Carbon::parse($sms['sentAt'])->format('d/m/Y H:i') }}"
                                                            data-delivered-at="{{ \Carbon\Carbon::parse($sms['doneAt'])->format('d/m/Y H:i') }}">
                                                            {{ \Illuminate\Support\Str::limit($sms['text'], 30) }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">{{ $sms['smsCount'] }}</td>
                                            <td>
                                                @if ($sms['delivery'] == 'DELIVERED')
                                                    <span class="status-badge status-delivered">
                                                        <i class="fas fa-check-circle"></i> Delivered
                                                    </span>
                                                @elseif ($sms['delivery'] == 'PENDING')
                                                    <span class="status-badge status-pending">
                                                        <i class="fas fa-clock"></i> Pending
                                                    </span>
                                                @else
                                                    <span class="status-badge status-failed">
                                                        <i class="fas fa-times-circle"></i> Failed
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                             </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h6>No messages yet</h6>
                            <p class="text-muted">Your sent messages will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </form>

        <!-- Submit Button -->
        <div class="submit-section">
            <button type="submit" form="smsForm" class="send-btn" id="sendButton">
                <i class="fas fa-paper-plane me-2"></i> Broadcast SMS
            </button>
        </div>
    </div>

    <!-- SMS Modal -->
    <div class="modal fade" id="smsModal" tabindex="-1" aria-labelledby="smsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white mb-0">
                        <i class="fas fa-sms me-2"></i> SMS Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Recipient</small>
                        <div class="fw-semibold" id="modalTo" style="word-break: break-all;"></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Sender</small>
                        <div class="fw-semibold" id="modalFrom"></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Sent At</small>
                        <div class="fw-semibold" id="modalSentAt"></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Delivered At</small>
                        <div class="fw-semibold" id="modalDeliveredAt"></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status</small>
                        <div id="modalStatus"></div>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-2">Message</small>
                        <div class="p-3 bg-light rounded border" style="white-space: pre-wrap; word-wrap: break-word; max-height: 200px; overflow-y: auto; font-size: 0.9rem;" id="modalFullText"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="copySmsBtn">
                        <i class="fas fa-copy me-1"></i> Copy
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('message_content');
            const charCount = document.getElementById('charCount');
            const sendButton = document.getElementById('sendButton');
            const form = document.querySelector('form');

            const maxLength = parseInt(textarea.getAttribute('maxlength'));
            const warningThreshold = maxLength - 50;

            // Character counter
            function updateCharCount() {
                const currentLength = textarea.value.length;
                charCount.textContent = currentLength;

                if (currentLength > maxLength - 10) {
                    charCount.style.color = '#e17055';
                } else if (currentLength > warningThreshold) {
                    charCount.style.color = '#f39c12';
                } else {
                    charCount.style.color = '#4e54c8';
                }

                const remaining = maxLength - currentLength;
                if (remaining <= 20) {
                    textarea.style.borderColor = remaining <= 10 ? '#e17055' : '#f39c12';
                } else {
                    textarea.style.borderColor = '';
                }
            }

            textarea.addEventListener('input', updateCharCount);
            updateCharCount();

            // Form submission validation
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Remove existing error messages
                document.querySelectorAll('.validation-error').forEach(el => el.remove());
                document.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));

                let isValid = true;
                let errorMessages = [];

                // Check recipients
                const classChecked = document.querySelectorAll('.class-checkbox:checked').length;
                const groupChecked = document.querySelectorAll('.group-option input[type="checkbox"]:checked').length;

                if (classChecked === 0 && groupChecked === 0) {
                    isValid = false;
                    errorMessages.push('Please select at least one recipient group or class.');

                    const recipientsSection = document.querySelector('.dashboard-card:first-child');
                    recipientsSection.style.border = '2px solid #e17055';

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'validation-error alert alert-danger mt-2';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please select at least one recipient';
                    recipientsSection.querySelector('.card-header').after(errorDiv);

                    setTimeout(() => {
                        recipientsSection.style.border = '';
                    }, 3000);
                }

                // Check message
                const messageText = textarea.value.trim();
                const maxChars = parseInt(textarea.getAttribute('maxlength'));

                if (messageText.length === 0) {
                    isValid = false;
                    errorMessages.push('Please enter a message to send.');
                    highlightMessageArea('Message cannot be empty');
                } else if (messageText.length > maxChars) {
                    isValid = false;
                    errorMessages.push(`Message is too long. Maximum ${maxChars} characters allowed.`);
                    highlightMessageArea(`Message is too long (${messageText.length}/${maxChars} characters)`);
                }

                function highlightMessageArea(errorMessage) {
                    textarea.style.border = '2px solid #e17055';
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'validation-error alert alert-danger mt-2';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i> ${errorMessage}`;
                    textarea.parentNode.insertBefore(errorDiv, textarea.nextSibling);
                    textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    textarea.focus();

                    setTimeout(() => {
                        textarea.style.border = '';
                    }, 3000);
                }

                if (isValid) {
                    const isBasic = {{ Auth::user()->school->package === 'basic' ? 'true' : 'false' }};
                    const smsLimit = isBasic ? 2 : 3;
                    const charLimit = isBasic ? 306 : 459;

                    const confirmed = confirm(
                        `Are you sure you want to send this message?\n\n` +
                        `📊 Summary:\n` +
                        `• Characters: ${messageText.length}/${charLimit}\n` +
                        `• SMS Count: ${Math.ceil(messageText.length / 153)}/${smsLimit}\n` +
                        `• Package: ${isBasic ? 'Basic' : 'Premium'}\n\n` +
                        `Click OK to send.`
                    );

                    if (confirmed) {
                        sendButton.disabled = true;
                        sendButton.innerHTML = `
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Sending...
                        `;
                        form.submit();
                    }
                } else {
                    showErrorAlert(errorMessages);
                }
            });

            function showErrorAlert(messages) {
                const errorAlert = document.createElement('div');
                errorAlert.className = 'alert alert-danger alert-dismissible fade show';
                errorAlert.style.position = 'fixed';
                errorAlert.style.top = '10px';
                errorAlert.style.left = '10px';
                errorAlert.style.right = '10px';
                errorAlert.style.zIndex = '9999';
                errorAlert.style.maxWidth = '400px';
                errorAlert.style.margin = '10px auto';
                errorAlert.innerHTML = `
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle me-3 mt-1"></i>
                        <div>
                            <strong>Please fix the following:</strong>
                            <ul class="mb-0 mt-1">
                                ${messages.map(msg => `<li>${msg}</li>`).join('')}
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                document.body.appendChild(errorAlert);

                setTimeout(() => {
                    if (errorAlert.parentNode) {
                        errorAlert.style.opacity = '0';
                        errorAlert.style.transition = 'opacity 0.3s ease';
                        setTimeout(() => errorAlert.remove(), 300);
                    }
                }, 5000);
            }

            // Modal functionality
            document.querySelectorAll('.sms-preview-link, .view-details-btn').forEach(link => {
                link.addEventListener('click', function(e) {
                    const data = {
                        fullText: this.getAttribute('data-full-text'),
                        to: this.getAttribute('data-to'),
                        from: this.getAttribute('data-from'),
                        status: this.getAttribute('data-status'),
                        sentAt: this.getAttribute('data-sent-at'),
                        deliveredAt: this.getAttribute('data-delivered-at')
                    };

                    document.getElementById('modalFullText').textContent = data.fullText;
                    document.getElementById('modalTo').textContent = data.to;
                    document.getElementById('modalFrom').textContent = data.from;
                    document.getElementById('modalSentAt').textContent = data.sentAt;
                    document.getElementById('modalDeliveredAt').textContent = data.deliveredAt;

                    let statusHtml = '';
                    let statusClass = '';
                    if (data.status === 'DELIVERED') {
                        statusClass = 'bg-success';
                        statusHtml = `<span class="badge ${statusClass} py-1 px-2">${data.status}</span>`;
                    } else if (data.status === 'PENDING') {
                        statusClass = 'bg-warning text-dark';
                        statusHtml = `<span class="badge ${statusClass} py-1 px-2">${data.status}</span>`;
                    } else {
                        statusClass = 'bg-danger';
                        statusHtml = `<span class="badge ${statusClass} py-1 px-2">${data.status}</span>`;
                    }
                    document.getElementById('modalStatus').innerHTML = statusHtml;
                });
            });

            // Copy SMS
            document.getElementById('copySmsBtn')?.addEventListener('click', function() {
                const text = document.getElementById('modalFullText').textContent;
                navigator.clipboard.writeText(text).then(() => {
                    const original = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check me-1"></i> Copied!';
                    this.classList.replace('btn-primary', 'btn-success');

                    setTimeout(() => {
                        this.innerHTML = original;
                        this.classList.replace('btn-success', 'btn-primary');
                    }, 2000);
                });
            });

            // Prevent form submission on Enter key in textarea
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.ctrlKey) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection
