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
            max-width: 1400px;
            margin: 0 auto;
            padding: 8px 4px;
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
            padding: 4px;
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
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        .modern-table thead {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        }

        .modern-table th {
            padding: 4ppx 4px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            border: none;
        }

        .modern-table td {
            padding: 6px 4px
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
            vertical-align: middle;
            transition: all 0.3s ease;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .modern-table tbody tr:hover {
            background: rgba(67, 97, 238, 0.05);
            transform: translateX(5px);
        }

        .ip-address {
            font-family: 'Fira Code', 'Courier New', monospace;
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), rgba(58, 12, 163, 0.1));
            padding: 3px 6px;
            border-radius: 12px;
            border: 1px solid rgba(67, 97, 238, 0.2);
            font-size: 14px;
            color: var(--primary);
            font-weight: 600;
        }

        .user-agent {
            max-width: 50px;
            /* white-space: nowrap; */
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-agent:hover {
            color: var(--primary);
            transform: translateX(3px);
        }

        .timestamp {
            background: linear-gradient(135deg, rgba(76, 201, 240, 0.1), rgba(72, 149, 239, 0.1));
            padding: 3px 6px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            color: var(--info);
        }

        .username {
            font-weight: 600;
            color: var(--dark);
            padding: 3px 6px;
            background: linear-gradient(135deg, rgba(247, 37, 133, 0.1), rgba(181, 23, 158, 0.1));
            border-radius: 20px;
            display: inline-block;
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--danger), #b5179e);
            border: none;
            border-radius: 16px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 600;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(230, 57, 70, 0.3);
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(230, 57, 70, 0.4);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 8px 4px;
        }

        .empty-icon {
            font-size: 16px;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1px;
        }

        .stats-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 2px ;
            border-radius: 20px;
            color: white;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 5px;
            }

            .header-section {
                padding: 4px;
            }

            .modern-table {
                font-size: 14px;
            }

            .modern-table th,
            .modern-table td {
                padding: 4px 2px;
            }

            .ip-address,
            .timestamp {
                font-size: 14px;
                padding: 2px 0.4px;
            }

            .user-agent {
                max-width: 150px;
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

        .security-shield {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.3);
        }
    </style>

    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="glass-card header-section fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">🛡️ Security Monitor</h1>
                    <p class="lead mb-0 opacity-90 text-white"> Failed Login Attempts Tracking</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="stats-badge">
                        <i class="fas fa-shield-alt me-2"></i>
                        {{ $attempts->count() }} Failed Attempts
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="glass-card text-center p-3 fade-in">
                    <div class="security-shield">
                        <i class="fas fa-lock text-white fa-2x"></i>
                    </div>
                    <h4 class="text-primary mb-1">{{ $attempts->count() }}</h4>
                    <small class="text-muted">Total Attempts</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card text-center p-3 fade-in" style="animation-delay: 0.1s;">
                    <div class="security-shield" style="background: linear-gradient(135deg, #f72585, #b5179e);">
                        <i class="fas fa-user-slash text-white fa-2x"></i>
                    </div>
                    <h4 class="text-primary mb-1">{{ $attempts->unique('ip')->count() }}</h4>
                    <small class="text-muted">Unique IPs</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card text-center p-3 fade-in" style="animation-delay: 0.2s;">
                    <div class="security-shield" style="background: linear-gradient(135deg, #4cc9f0, #4895ef);">
                        <i class="fas fa-clock text-white fa-2x"></i>
                    </div>
                    <h4 class="text-primary mb-1">24h</h4>
                    <small class="text-muted">Monitoring Period</small>
                </div>
            </div>
        </div>

        <!-- Failed Attempts Table -->
        <div class="glass-card fade-in">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <h4 class="text-primary mb-0">
                        <i class="fas fa-list me-2"></i>Failed Login Attempts
                    </h4>
                    <button class="btn-modern" onclick="clearFailedAttempts()">
                        <i class="fas fa-trash-alt me-2"></i>Clear All
                    </button>
                </div>

                <div class="table-responsive p-3">
                    <table class="modern-table table-responsive-md" id="myTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>IP Address</th>
                                <th>Username</th>
                                <th>User Agent</th>
                                <th>Attempted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($attempts->isEmpty())
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-shield-check"></i>
                                            </div>
                                            <h4 class="text-dark mb-3">All Systems Secure</h4>
                                            <p class="text-muted mb-4">No failed login attempts detected. Your system is protected.</p>
                                            <div class="security-shield" style="background: linear-gradient(135deg, #1cc88a, #0f9d58);">
                                                <i class="fas fa-check text-white fa-2x"></i>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach ($attempts as $row)
                                    <tr class="slide-in" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                                        <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="ip-address">
                                                <i class="fas fa-network-wired me-2"></i>{{ $row->ip }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="username">
                                                <i class="fas fa-user me-2"></i>{{ $row->username }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="user-agent" title="{{ $row->user_agent }}">
                                                <i class="fas fa-desktop me-2"></i>{{ $row->user_agent }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="timestamp">
                                                <i class="fas fa-clock me-2"></i>
                                                {{ \Carbon\Carbon::parse($row->attempted_at)->format('M d, Y H:i:s') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                @if (!$attempts->isEmpty())
                <div class="p-4 border-top">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Showing {{ $attempts->count() }} failed login attempts
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-2"></i>Export
                                </button>
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Add click to expand user agent
            const userAgents = document.querySelectorAll('.user-agent');
            userAgents.forEach(agent => {
                agent.addEventListener('click', function() {
                    const fullText = this.getAttribute('title');
                    alert('User Agent Details:\n\n' + fullText);
                });
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

                gsap.from('.slide-in', {
                    duration: 0.8,
                    x: 50,
                    opacity: 0,
                    stagger: 0.1,
                    ease: "power2.out"
                });
            }

            // Add real-time updates (simulated)
            setInterval(() => {
                const timestampElements = document.querySelectorAll('.timestamp');
                timestampElements.forEach(el => {
                    const originalTime = el.textContent;
                    el.innerHTML = '<i class="fas fa-sync fa-spin me-2"></i>Updating...';

                    setTimeout(() => {
                        el.innerHTML = originalTime;
                    }, 1000);
                });
            }, 30000);
        });
    </script>
@endsection
