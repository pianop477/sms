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
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 8px 6px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--card-shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-5px);
        }

        .header-section {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border-radius: 20px;
            padding: 8px;
            margin-bottom: 8px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 4px;
            margin-bottom: 8px;
        }

        .stat-card {
            padding: 6px;
            border-radius: 16px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            transform: rotate(45deg);
            transition: all 0.3s ease;
        }

        .stat-card:hover::before {
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .feedback-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 4px;
        }

        .feedback-card {
            padding: 6px;
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .feedback-card:hover {
            border-left-color: var(--warning);
            transform: translateX(5px);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--light);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .action-buttons {
            display: flex;
            gap: 3px;
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .empty-state {
            text-align: center;
            padding: 8px 6px;
        }

        .empty-icon {
            font-size: 16px;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 6px;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 5px;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border: none;
            border-radius: 12px;
        }

        .page-link {
            border: none;
            border-radius: 12px;
            margin: 0 4px;
            padding: 4px 6px;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }

        .badge {
            padding: 4px 8px;
            border-radius: 25px;
            font-weight: 600;
        }

        .message-preview {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.6;
            margin-bottom: 10px;
            position: relative;
        }

        .read-more-indicator {
            color: var(--primary);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-block;
            margin-top: 5px;
        }

        .read-more-indicator:hover {
            text-decoration: underline;
        }

        /* Modal Styles */
        .message-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 2rem;
            cursor: pointer;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .message-modal-content {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            max-width: 700px;
            width: 100%;
            max-height: 80vh;
            overflow-y: auto;
            cursor: default;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: modalSlideUp 0.3s ease;
        }

        @keyframes modalSlideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            transition: all 0.2s ease;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close-btn:hover {
            background: #f0f0f0;
            color: var(--danger);
            transform: rotate(90deg);
        }

        .message-content-box {
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 16px;
            margin: 1rem 0;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.8;
            font-size: 1rem;
            border-left: 4px solid var(--primary);
        }

        .modal-footer-custom {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 2px solid #f0f0f0;
        }

        .btn-modal {
            padding: 10px 24px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-modal-primary {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
        }

        .btn-modal-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }

        .btn-modal-secondary {
            background: #e9ecef;
            color: #495057;
        }

        .btn-modal-secondary:hover {
            background: #dee2e6;
            transform: translateY(-2px);
        }

        .message-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .message-badge {
            background: linear-gradient(135deg, #f72585, #b5179e);
            color: white;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 6px;
            }

            .header-section {
                padding: 6px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .feedback-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: row;
            }

            .btn-icon {
                width: 35px;
                height: 35px;
            }

            .message-modal-content {
                padding: 1.5rem;
            }

            .message-content-box {
                padding: 1rem;
            }
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }

        .slide-up {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>

    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="glass-card header-section slide-up">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">📨 Feedback Inbox</h1>
                    <p class="lead mb-0 text-white">Manage and respond to user feedback and inquiries</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-white text-primary p-3">
                        <i class="fas fa-envelope-open mr-2"></i>
                        {{ $message->total() }} Total Messages
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-grid fade-in">
            <div class="glass-card stat-card" style="background: linear-gradient(135deg, #4361ee, #3a0ca3);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-white-50 mb-1">Total Feedback</h6>
                        <h3 class="text-white mb-0">{{ $message->total() }}</h3>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-comments fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="glass-card stat-card" style="background: linear-gradient(135deg, #7209b7, #560bad);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-white-50 mb-1">This Month</h6>
                        <h3 class="text-white mb-0">{{ $message->filter(function($item) {
                            return $item->created_at->isCurrentMonth();
                        })->count() }}</h3>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="glass-card stat-card" style="background: linear-gradient(135deg, #f72585, #b5179e);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-white-50 mb-1">Unread</h6>
                        <h3 class="text-white mb-0">{{ $message->where('is_read', false)->count() }}</h3>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-envelope fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Messages -->
        @if ($message->isEmpty())
            <div class="glass-card empty-state fade-in">
                <div class="empty-icon">
                    <i class="fas fa-comment-slash"></i>
                </div>
                <h3 class="text-dark mb-3">No Feedback Yet</h3>
                <p class="text-muted mb-4">Your feedback inbox is empty. New messages will appear here.</p>
                <button class="btn btn-primary btn-lg" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt mr-2"></i> Refresh
                </button>
            </div>
        @else
            <div class="feedback-grid">
                @foreach ($message as $sms)
                    <div class="glass-card feedback-card fade-in"
                         data-message-id="{{ $sms->id }}"
                         data-full-message="{{ $sms->message }}"
                         data-user-name="{{ $sms->name }}"
                         data-user-email="{{ $sms->email }}"
                         data-timestamp="{{ \Carbon\Carbon::parse($sms->created_at)->format('M d, Y · h:i A') }}"
                         data-time-ago="{{ \Carbon\Carbon::parse($sms->created_at)->diffForHumans() }}"
                         data-reply-url="{{ route('reply.post', ['sms' => Hashids::encode($sms->id)]) }}"
                         style="animation-delay: {{ $loop->index * 0.1 }}s;">

                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-primary rounded-circle p-2 me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user text-white fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-primary text-capitalize fw-bold">{{ $sms->name }}</h6>
                                        <small class="text-muted">{{ $sms->email }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-clock mr-2"></i>
                                    {{ \Carbon\Carbon::parse($sms->created_at)->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        <div class="message-preview mb-2 text-dark">
                            {{ Str::limit($sms->message, 120) }}
                        </div>

                        <div class="read-more-indicator mb-3">
                            <i class="fas fa-chevron-circle-down mr-2"></i> Read more
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                {{ \Carbon\Carbon::parse($sms->created_at)->format('M d, Y · h:i A') }}
                            </small>

                            <div class="action-buttons" onclick="event.stopPropagation()">
                                <a href="{{ route('reply.post', ['sms' => Hashids::encode($sms->id)]) }}"
                                   class="btn-icon bg-success text-white"
                                   title="Reply to feedback"
                                   onclick="event.stopPropagation()">
                                    <i class="fas fa-reply"></i>
                                </a>
                                <a href="{{ route('delete.post', ['sms' => Hashids::encode($sms->id)]) }}"
                                   class="btn-icon bg-danger text-white"
                                   onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this feedback?')"
                                   title="Delete feedback">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Pagination -->
        @if (!$message->isEmpty())
            <div class="pagination-container slide-up mt-4">
                {{ $message->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Function to open modal with full message
            function openMessageModal(messageData) {
                // Remove any existing modal
                const existingModal = document.querySelector('.message-modal-overlay');
                if (existingModal) {
                    existingModal.remove();
                }

                // Create modal overlay
                const overlay = document.createElement('div');
                overlay.className = 'message-modal-overlay';

                // Create modal content
                const modalContent = document.createElement('div');
                modalContent.className = 'message-modal-content';

                // Build modal HTML
                modalContent.innerHTML = `
                    <div class="modal-header-custom">
                        <div>
                            <h3 class="text-primary mb-2" style="font-weight: 700;"> ${messageData.userName}</h3>
                            <div class="message-meta">
                                <small class="text-muted">
                                    <i class="fas fa-envelope mr-2"></i> ${messageData.userEmail}
                                </small>
                                <span class="message-badge">
                                    <i class="fas fa-clock mr-2"></i> ${messageData.timeAgo}
                                </span>
                            </div>
                        </div>
                        <button class="modal-close-btn" onclick="this.closest('.message-modal-overlay').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="message-content-box">
                        ${messageData.fullMessage.replace(/\n/g, '<br>')}
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt mr-2"></i> Received: ${messageData.timestamp}
                        </small>
                        <span class="badge bg-info text-white">
                            <i class="fas fa-comment mr-2"></i> ${messageData.messageLength} characters
                        </span>
                    </div>

                    <div class="modal-footer-custom">
                        <button class="btn-modal btn-modal-secondary" onclick="this.closest('.message-modal-overlay').remove()">
                            <i class="fas fa-times mr-2"></i> Close
                        </button>
                        <a href="${messageData.replyUrl}" class="btn-modal btn-modal-primary" onclick="event.stopPropagation()">
                            <i class="fas fa-reply mr-2"></i> Reply to this message
                        </a>
                    </div>
                `;

                overlay.appendChild(modalContent);

                // Close modal when clicking outside
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        overlay.remove();
                    }
                });

                // Prevent closing when clicking inside modal
                modalContent.addEventListener('click', (e) => {
                    e.stopPropagation();
                });

                document.body.appendChild(overlay);
            }

            // Add click event to all feedback cards
            const feedbackCards = document.querySelectorAll('.feedback-card');
            feedbackCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Don't open modal if clicking on action buttons or links
                    if (e.target.closest('.action-buttons') || e.target.closest('a')) {
                        return;
                    }

                    // Collect message data from data attributes
                    const messageData = {
                        fullMessage: this.dataset.fullMessage,
                        userName: this.dataset.userName,
                        userEmail: this.dataset.userEmail,
                        timestamp: this.dataset.timestamp,
                        timeAgo: this.dataset.timeAgo,
                        replyUrl: this.dataset.replyUrl,
                        messageLength: this.dataset.fullMessage.length
                    };

                    openMessageModal(messageData);
                });
            });

            // Add hover effects with animation
            feedbackCards.forEach((card, index) => {
                card.style.animation = `fadeIn 0.6s ease-in-out ${index * 0.1}s both`;
            });

            // Debug: Log full message for first card (useful for debugging)
            if (feedbackCards.length > 0) {
                console.log('Sample full message:', feedbackCards[0].dataset.fullMessage);
                console.log('Message length:', feedbackCards[0].dataset.fullMessage.length);
            }

            // Optional: Add keyboard support (Escape to close modal)
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.querySelector('.message-modal-overlay');
                    if (modal) {
                        modal.remove();
                    }
                }
            });
        });
    </script>

    <!-- Optional: Add Font Awesome if not already included -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> --}}
@endsection
