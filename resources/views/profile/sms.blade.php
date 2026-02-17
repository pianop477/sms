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

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 15px;
            margin: 0;
        }

        /* Main Container - SIMPLIFIED */
        .sms-dashboard {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
        }

        /* Header - CLEAN */
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 20px 30px;
            text-align: center;
        }

        .header-title {
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
            color: white;
        }

        .header-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-top: 8px;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Main Grid - FIXED HEIGHT ISSUE */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 25px;
            min-height: 600px;
            /* Minimum height */
        }

        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 20px;
            }
        }

        /* Card - REMOVED COMPLEX FLEX */
        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(78, 84, 200, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .dashboard-card:hover {
            box-shadow: 0 10px 25px rgba(78, 84, 200, 0.15);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(78, 84, 200, 0.1);
        }

        .card-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .card-icon i {
            font-size: 20px;
            color: white;
        }

        .card-title {
            font-weight: 600;
            font-size: 1.2rem;
            margin: 0;
            color: var(--dark);
        }

        .card-subtitle {
            color: #666;
            font-size: 0.9rem;
            margin-top: 3px;
        }

        /* Classes Section - VISIBLE */
        .classes-section {
            margin-bottom: 25px;
        }

        .section-title {
            font-weight: 600;
            font-size: 1rem;
            color: var(--dark);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 10px;
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
            padding: 12px 10px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .class-checkbox:checked+.class-label {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 10px rgba(78, 84, 200, 0.2);
        }

        .class-checkbox:checked+.class-label::after {
            content: '✓';
            position: absolute;
            top: -6px;
            right: -6px;
            width: 20px;
            height: 20px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: white;
        }

        /* Groups Section - VISIBLE */
        .group-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .group-option {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .group-option:hover {
            border-color: var(--primary);
            background: #f8f9fa;
        }

        .group-option input[type="checkbox"] {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        .group-icon {
            width: 36px;
            height: 36px;
            background: rgba(78, 84, 200, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .group-icon i {
            font-size: 16px;
            color: var(--primary);
        }

        .group-info {
            flex: 1;
        }

        .group-info h6 {
            margin: 0;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .group-info small {
            color: #666;
            font-size: 0.85rem;
            display: block;
            margin-top: 2px;
        }

        /* Message Area - SIMPLE */
        .message-container {
            margin-bottom: 20px;
        }

        .message-textarea {
            width: 100%;
            min-height: 180px;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            line-height: 1.5;
            resize: vertical;
            transition: all 0.2s ease;
            background: #f8f9fa;
            font-family: inherit;
            box-sizing: border-box;
        }

        .message-textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.1);
        }

        .char-counter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .char-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .char-count {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary);
        }

        /* SMS History - SIMPLE & VISIBLE */
        .sms-history {
            margin-top: 10px;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .history-title {
            font-weight: 600;
            color: var(--dark);
            font-size: 1rem;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .history-stats {
            font-size: 0.9rem;
            color: var(--primary);
            font-weight: 600;
            padding: 4px 10px;
            background: rgba(78, 84, 200, 0.1);
            border-radius: 15px;
        }

        /* SMS Table - SIMPLE & VISIBLE */
        .sms-table-container {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            overflow: auto;
            max-height: 300px;
            /* Fixed height with scroll */
        }

        .sms-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }

        .sms-table thead {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .sms-table th {
            padding: 12px 15px;
            color: white;
            font-weight: 600;
            text-align: left;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .sms-table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s ease;
        }

        .sms-table tbody tr:hover {
            background: rgba(78, 84, 200, 0.03);
        }

        .sms-table td {
            padding: 12px 15px;
            color: #495057;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .status-delivered {
            background: rgba(0, 184, 148, 0.1);
            color: #00b894;
            border: 1px solid rgba(0, 184, 148, 0.2);
        }

        .status-pending {
            background: rgba(253, 203, 110, 0.1);
            color: #e17055;
            border: 1px solid rgba(253, 203, 110, 0.2);
        }

        .status-failed {
            background: rgba(225, 112, 85, 0.1);
            color: #e17055;
            border: 1px solid rgba(225, 112, 85, 0.2);
        }

        /* SMS Preview */
        .sms-preview {
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 300px;
        }

        .sms-icon {
            width: 28px;
            height: 28px;
            background: rgba(78, 84, 200, 0.1);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sms-icon i {
            font-size: 14px;
            color: var(--primary);
        }

        .sms-text {
            flex: 1;
            min-width: 0;
        }

        .sms-text a {
            color: var(--dark);
            text-decoration: none;
            font-size: 0.9rem;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sms-text a:hover {
            color: var(--primary);
        }

        /* Submit Button */
        .submit-section {
            padding: 20px;
            background: rgba(248, 249, 250, 0.95);
            border-top: 1px solid rgba(78, 84, 200, 0.1);
            text-align: center;
        }

        .send-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 35px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(78, 84, 200, 0.25);
        }

        .send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.35);
        }

        /* Modal - COMPACT */
        .modal-content {
            border-radius: 12px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 15px 20px;
            border-radius: 12px 12px 0 0;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 15px 20px;
        }

        /* Scrollbar */
        .sms-table-container::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .sms-table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .sms-table-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 10px;
        }

        /* Mobile */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .dashboard-header {
                padding: 15px 20px;
            }

            .dashboard-grid {
                padding: 15px;
                gap: 15px;
            }

            .dashboard-card {
                padding: 15px;
            }

            .classes-grid {
                grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            }

            .send-btn {
                width: 100%;
                padding: 12px;
            }
        }
    </style>

    <!-- Session Alerts -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 15px; border-radius: 8px;">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="margin: 15px; border-radius: 8px;">
            <i class="fas fa-check-circle me-2"></i>
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Dashboard -->
    <div class="sms-dashboard">
        <!-- Header -->
        <div class="dashboard-header">
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
                        <h3 class="card-title"> Recipients</h3>
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
                                <p class="text-muted mb-0"> No classes available</p>
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
                        <!-- All Parents -->
                        <label class="group-option">
                            <input type="checkbox" name="send_to_all" value="1"
                                {{ old('send_to_all') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="group-info">
                                <h6> All Parents</h6>
                                <small>All students from all classes</small>
                            </div>
                        </label>

                        <!-- Transport Groups -->
                        <label class="group-option">
                            <input type="checkbox" name="send_with_transport" value="1"
                                {{ old('send_with_transport') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-bus"></i>
                            </div>
                            <div class="group-info">
                                <h6> With Transport</h6>
                                <small>Parents whose children use school transport</small>
                            </div>
                        </label>

                        <label class="group-option">
                            <input type="checkbox" name="send_without_transport" value="1"
                                {{ old('send_without_transport') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-walking"></i>
                            </div>
                            <div class="group-info">
                                <h6> Without Transport</h6>
                                <small>Parents whose children don't use transport</small>
                            </div>
                        </label>

                        <!-- Staff Groups -->
                        <label class="group-option">
                            <input type="checkbox" name="send_to_teachers" value="1"
                                {{ old('send_to_teachers') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="group-info">
                                <h6> Teaching Staff</h6>
                                <small>All teachers and academic staff</small>
                            </div>
                        </label>

                        <label class="group-option">
                            <input type="checkbox" name="send_to_other_staff" value="1"
                                {{ old('send_to_other_staff') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="group-info">
                                <h6> Non-Teaching Staff</h6>
                                <small>All support staff</small>
                            </div>
                        </label>

                        <label class="group-option">
                            <input type="checkbox" name="send_to_drivers" value="1"
                                {{ old('send_to_drivers') ? 'checked' : '' }}>
                            <div class="group-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="group-info">
                                <h6> Drivers</h6>
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
                        <h3 class="card-title"> Compose Message</h3>
                        <p class="card-subtitle">Write your announcement message</p>
                    </div>
                </div>

                <div class="message-container">
                    <textarea name="message_content" id="message_content"
                        class="message-textarea @error('message_content') is-invalid @enderror" placeholder="Type your message here..."
                        required maxlength="459">{{ old('message_content') }}</textarea>

                    @error('message_content')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror

                    <div class="char-counter">
                        <div class="char-info">
                            <span class="char-count" id="charCount">0</span>
                            <span class="text-muted">/ 459 characters</span>
                        </div>
                        <div>
                            <span class="text-muted">
                                <i class="fas fa-info-circle me-1"></i> Max 3 SMS
                            </span>
                        </div>
                    </div>
                </div>

                <!-- SMS History -->
                <div class="sms-history">
                    <div class="history-header">
                        <h6 class="history-title">
                            <i class="fas fa-history mr-2"></i> Recent Messages
                        </h6>
                        <span class="history-stats">{{ $smsCount }} total</span>
                    </div>

                    @if ($smsCount > 0)
                        <div class="sms-table-container">
                            <table class="sms-table table-responsive-md">
                                <thead>
                                    <tr>
                                        <th>Sent At</th>
                                        <th>To</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($smsContents as $sms)
                                        <tr>
                                            <td style="white-space: nowrap;">
                                                <div class="text-muted">
                                                    {{ \Carbon\Carbon::parse($sms['sentAt'])->format('H:i:s') }}
                                                </div>
                                                <div class="text-muted" style="font-size: 0.85rem;">
                                                    {{ \Carbon\Carbon::parse($sms['sentAt'])->format('d/m/Y') }}
                                                </div>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                {{ substr($sms['to'], -4) }}...
                                            </td>
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
                                                            {{ \Illuminate\Support\Str::limit($sms['text'], 20) }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($sms['delivery'] == 'DELIVERED')
                                                    <span class="status-badge status-delivered">
                                                        <i class="fas fa-check-circle"></i>
                                                        <span> Delivered</span>
                                                    </span>
                                                @elseif ($sms['delivery'] == 'PENDING')
                                                    <span class="status-badge status-pending">
                                                        <i class="fas fa-clock"></i>
                                                        <span> Pending</span>
                                                    </span>
                                                @else
                                                    <span class="status-badge status-failed">
                                                        <i class="fas fa-times-circle"></i>
                                                        <span> Failed</span>
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                            <h6 class="mb-1"> No messages yet</h6>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">Your sent messages will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </form>

        <!-- Submit Button -->
        <div class="submit-section">
            <button type="submit" form="smsForm" class="send-btn" id="sendButton"
                onclick="return confirm('Are you sure you want to send this message?')">
                <i class="fas fa-paper-plane me-2"></i> Broadcast SMS
            </button>
        </div>
    </div>

    <!-- SMS Modal -->
    <div class="modal fade" id="smsModal" tabindex="-1" aria-labelledby="smsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white mb-0">
                        <i class="fas fa-sms me-2"></i> SMS Details
                    </h5>
                    <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Recipient</small>
                            <div class="fw-semibold" id="modalTo"></div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Sender</small>
                            <div class="fw-semibold" id="modalFrom"></div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Sent</small>
                            <div class="fw-semibold" id="modalSentAt"></div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Delivered</small>
                            <div class="fw-semibold" id="modalDeliveredAt"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status</small>
                        <div id="modalStatus" class="text-white"></div>
                    </div>

                    <div>
                        <small class="text-muted d-block mb-2">Message</small>
                        <div class="p-3 bg-light rounded border"
                            style="white-space: pre-wrap; word-wrap: break-word; max-height: 200px; overflow-y: auto;"
                            id="modalFullText"></div>
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

            // Character counter
            textarea.addEventListener('input', function() {
                const currentLength = this.value.length;
                charCount.textContent = currentLength;
                charCount.style.color = currentLength > 256 ? '#e17055' : '#4e54c8';
            });

            // Initialize
            charCount.textContent = textarea.value.length;

            // Form submission - ENHANCED VALIDATION
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop form submission first

                // Reset previous error states
                document.querySelectorAll('.validation-error').forEach(el => el.remove());

                let isValid = true;
                let errorMessages = [];

                // 1. Check recipients
                const classChecked = document.querySelectorAll('.class-checkbox:checked').length;
                const groupChecked = document.querySelectorAll(
                    '.group-option input[type="checkbox"]:checked').length;

                if (classChecked === 0 && groupChecked === 0) {
                    isValid = false;
                    errorMessages.push('Please select at least one recipient group or class.');

                    // Highlight recipients section
                    const recipientsSection = document.querySelector('.dashboard-card:first-child');
                    recipientsSection.style.border = '2px solid #e17055';
                    recipientsSection.style.animation = 'shake 0.5s ease-in-out';

                    // Add error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'validation-error alert alert-danger mt-2';
                    errorDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Please select at least one recipient
                `;
                    recipientsSection.querySelector('.card-header').after(errorDiv);
                } else {
                    // Remove highlight if previously added
                    const recipientsSection = document.querySelector('.dashboard-card:first-child');
                    recipientsSection.style.border = '';
                    recipientsSection.style.animation = '';
                }

                // 2. Check message content
                const messageText = textarea.value.trim();
                if (messageText.length === 0) {
                    isValid = false;
                    errorMessages.push('Please enter a message to send.');

                    // Highlight message area
                    textarea.style.border = '2px solid #e17055';
                    textarea.style.boxShadow = '0 0 0 3px rgba(225, 112, 85, 0.1)';

                    // Add error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'validation-error alert alert-danger mt-2';
                    errorDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Please enter a message
                `;
                    textarea.parentNode.insertBefore(errorDiv, textarea.nextSibling);

                    // Scroll to message area
                    textarea.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    textarea.focus();
                } else if (messageText.length > 306) {
                    isValid = false;
                    errorMessages.push('Message is too long. Maximum 306 characters allowed.');

                    textarea.style.border = '2px solid #e17055';
                    textarea.style.boxShadow = '0 0 0 3px rgba(225, 112, 85, 0.1)';

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'validation-error alert alert-danger mt-2';
                    errorDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Message is too long (${messageText.length}/306 characters)
                `;
                    textarea.parentNode.insertBefore(errorDiv, textarea.nextSibling);

                    textarea.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    textarea.focus();
                } else {
                    // Remove highlight if previously added
                    textarea.style.border = '';
                    textarea.style.boxShadow = '';
                }

                // If valid, proceed with confirmation and submission
                if (isValid) {
                    // Show confirmation dialog
                    const confirmed = confirm(
                        'Are you sure you want to send this message to the selected recipients?');

                    if (confirmed) {
                        // Disable button
                        sendButton.disabled = true;
                        sendButton.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Sending...
                    `;

                        // Submit form
                        form.submit();
                    }
                } else {
                    // Show all error messages in a nice alert
                    const errorAlert = document.createElement('div');
                    errorAlert.className = 'alert alert-danger alert-dismissible fade show';
                    errorAlert.style.position = 'fixed';
                    errorAlert.style.top = '20px';
                    errorAlert.style.right = '20px';
                    errorAlert.style.zIndex = '9999';
                    errorAlert.style.maxWidth = '400px';
                    errorAlert.innerHTML = `
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle me-3 mt-1 fs-4"></i>
                        <div>
                            <h5 class="alert-heading mb-2">Please fix the following:</h5>
                            <ul class="mb-1">
                                ${errorMessages.map(msg => `<li>${msg}</li>`).join('')}
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                    document.body.appendChild(errorAlert);

                    // Auto-dismiss after 5 seconds
                    setTimeout(() => {
                        if (errorAlert.parentNode) {
                            errorAlert.style.opacity = '0';
                            errorAlert.style.transition = 'opacity 0.3s ease';
                            setTimeout(() => errorAlert.remove(), 300);
                        }
                    }, 5000);
                }
            });

            // Add shake animation for errors
            const style = document.createElement('style');
            style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }

            .validation-error {
                border-radius: 8px;
                border-left: 4px solid #e17055;
            }

            /* Real-time validation for textarea */
            .message-textarea.invalid {
                border: 2px solid #e17055 !important;
                background: rgba(225, 112, 85, 0.05) !important;
            }

            /* Real-time validation for checkboxes */
            .class-label.invalid, .group-option.invalid {
                border: 2px solid #e17055 !important;
                background: rgba(225, 112, 85, 0.05) !important;
            }
        `;
            document.head.appendChild(style);

            // Real-time validation for textarea
            textarea.addEventListener('input', function() {
                const text = this.value.trim();
                if (text.length === 0) {
                    this.classList.add('invalid');
                } else {
                    this.classList.remove('invalid');
                }
            });

            // Real-time validation for checkboxes
            const checkboxes = document.querySelectorAll('.class-checkbox, .group-option input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const classChecked = document.querySelectorAll('.class-checkbox:checked')
                    .length;
                    const groupChecked = document.querySelectorAll(
                        '.group-option input[type="checkbox"]:checked').length;

                    if (classChecked === 0 && groupChecked === 0) {
                        // Highlight all recipient options
                        document.querySelectorAll('.class-label, .group-option').forEach(el => {
                            el.classList.add('invalid');
                        });
                    } else {
                        // Remove highlight
                        document.querySelectorAll('.class-label, .group-option').forEach(el => {
                            el.classList.remove('invalid');
                        });
                    }
                });
            });

            // Modal functionality
            document.querySelectorAll('.sms-preview-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const data = {
                        fullText: this.getAttribute('data-full-text'),
                        to: this.getAttribute('data-to'),
                        from: this.getAttribute('data-from'),
                        status: this.getAttribute('data-status'),
                        sentAt: this.getAttribute('data-sent-at'),
                        deliveredAt: this.getAttribute('data-delivered-at')
                    };

                    // Populate modal
                    document.getElementById('modalFullText').textContent = data.fullText;
                    document.getElementById('modalTo').textContent = data.to;
                    document.getElementById('modalFrom').textContent = data.from;
                    document.getElementById('modalSentAt').textContent = data.sentAt;
                    document.getElementById('modalDeliveredAt').textContent = data.deliveredAt;

                    // Status
                    let statusHtml = '';
                    if (data.status === 'DELIVERED') {
                        statusHtml =
                            `<span class="badge bg-success py-1 px-2">${data.status}</span>`;
                    } else if (data.status === 'PENDING') {
                        statusHtml =
                            `<span class="badge bg-warning text-dark py-1 px-2">${data.status}</span>`;
                    } else {
                        statusHtml =
                        `<span class="badge bg-danger py-1 px-2">${data.status}</span>`;
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
                if (e.key === 'Enter' && e.ctrlKey) {
                    // Allow Ctrl+Enter for new lines
                    return;
                }
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection
