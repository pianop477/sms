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
            padding: 2rem 1rem;
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
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            padding: 1.5rem;
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
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
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
            gap: 1.5rem;
        }

        .feedback-card {
            padding: 1.5rem;
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
            cursor: pointer;
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
            gap: 0.5rem;
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
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border: none;
            border-radius: 12px;
        }

        .page-link {
            border: none;
            border-radius: 12px;
            margin: 0 0.25rem;
            padding: 0.75rem 1.25rem;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
        }

        .message-preview {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .header-section {
                padding: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .feedback-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-icon {
                width: 100%;
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
                        <i class="fas fa-envelope-open me-2"></i>
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
                        <h3 class="text-white mb-0">-</h3>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="glass-card stat-card" style="background: linear-gradient(135deg, #f72585, #b5179e);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-white-50 mb-1">Response Rate</h6>
                        <h3 class="text-white mb-0">-</h3>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-reply fa-2x text-white-50"></i>
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
                <button class="btn btn-primary btn-lg">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
            </div>
        @else
            <div class="feedback-grid">
                @foreach ($message as $sms)
                    <div class="glass-card feedback-card fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2 mr-1">
                                    <div class="bg-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-primary text-capitalize">{{ $sms->name }}</h6>
                                        <small class="text-muted">{{ $sms->email }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($sms->created_at)->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        <div class="message-preview mb-3 text-dark">
                            {{ Str::limit($sms->message, 150) }}
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($sms->created_at)->format('M d, Y · h:i A') }}
                            </small>

                            <div class="action-buttons">
                                <a href="{{ route('reply.post', ['sms' => Hashids::encode($sms->id)]) }}"
                                   class="btn-icon bg-success text-white"
                                   title="Reply to feedback">
                                    <i class="fas fa-reply"></i>
                                </a>
                                <a href="{{ route('delete.post', ['sms' => Hashids::encode($sms->id)]) }}"
                                   class="btn-icon bg-danger text-white"
                                   onclick="return confirm('Are you sure you want to delete this feedback?')"
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
            <div class="pagination-container slide-up">
                {{ $message->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Add click to expand message functionality
            const feedbackCards = document.querySelectorAll('.feedback-card');
            feedbackCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    if (!e.target.closest('.action-buttons')) {
                        const message = this.querySelector('.message-preview');
                        const fullMessage = message.getAttribute('data-full') || message.textContent;

                        // Create modal-like overlay
                        const overlay = document.createElement('div');
                        overlay.style.cssText = `
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
                        `;

                        const modalContent = document.createElement('div');
                        modalContent.style.cssText = `
                            background: white;
                            padding: 2rem;
                            border-radius: 20px;
                            max-width: 600px;
                            max-height: 80vh;
                            overflow-y: auto;
                            cursor: default;
                        `;

                        modalContent.innerHTML = `
                            <div class="mb-3">
                                <h5 class="text-primary">Message from ${this.querySelector('h6').textContent}</h5>
                                <small class="text-muted">${this.querySelector('small.text-muted').textContent}</small>
                            </div>
                            <p class="text-dark" style="line-height: 1.8;">${fullMessage}</p>
                            <div class="text-end mt-3">
                                <button class="btn btn-primary" onclick="this.closest('[style]').remove()">
                                    Close
                                </button>
                            </div>
                        `;

                        overlay.appendChild(modalContent);
                        overlay.addEventListener('click', (e) => {
                            if (e.target === overlay) overlay.remove();
                        });

                        document.body.appendChild(overlay);
                    }
                });
            });

            // Add hover effects with GSAP if available
            if (typeof gsap !== 'undefined') {
                gsap.from('.fade-in', {
                    duration: 0.8,
                    y: 50,
                    opacity: 0,
                    stagger: 0.1,
                    ease: "power3.out"
                });
            }
        });
    </script>
@endsection
