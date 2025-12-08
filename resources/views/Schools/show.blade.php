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
            padding: 8px 4rem;
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
            transform: translateY(-8px);
        }

        .header-section {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border-radius: 24px;
            padding: 10px;
            margin-bottom: 10px;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 8px;
            margin-bottom: 10px;
        }

        .stat-card {
            padding: 10px;
            border-radius: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px) scale(1.02);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            border-radius: 20px;
        }

        .profile-section {
            padding: 10px;
        }

        .school-logo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 24px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: all 0.4s ease;
        }

        .school-logo:hover {
            transform: scale(1.1) rotate(5deg);
            border-color: rgba(255, 255, 255, 0.6);
        }

        .profile-img {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            transition: all 0.4s ease;
        }

        .profile-img:hover {
            transform: scale(1.1);
            border-color: rgba(255, 255, 255, 0.6);
        }

        .info-item {
            padding: 6px;
            background: rgba(67, 97, 238, 0.05);
            border-radius: 16px;
            margin-bottom: 6px;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .info-item:hover {
            background: rgba(67, 97, 238, 0.1);
            border-left-color: var(--primary);
            transform: translateX(5px);
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border: none;
            border-radius: 16px;
            padding: 6px 10px;
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

        .badge-modern {
            padding: 4px 8px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(67, 97, 238, 0.3), transparent);
            margin: 10px 0;
            border: none;
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
                gap: 4px;
            }

            .profile-section {
                padding: 6px;
            }

            .school-logo {
                width: 100px;
                height: 100px;
            }

            .profile-img {
                width: 120px;
                height: 120px;
            }
        }

        /* Animation classes */
        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .slide-in {
            animation: slideInRight 0.8s ease-out;
        }

        .bounce-in {
            animation: bounceIn 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="glass-card header-section fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-3">🏫 {{ucwords(strtolower($schools->school_name))}}</h3>
                    {{-- <p class="lead mb-0 opacity-90">Comprehensive School Management Dashboard</p> --}}
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-white text-primary p-3 rounded-pill">
                        <i class="fas fa-certificate me-2"></i>
                        Registered: {{$schools->school_reg_no}}
                    </div>
                </div>
            </div>
        </div>

        <!-- School Information Section -->
        <div class="glass-card profile-section fade-in">
            <div class="row align-items-center">
                <!-- School Logo -->
                <div class="col-12 col-md-3 text-center mb-4 mb-md-0">
                    <img src="{{asset('storage/logo/'. $schools->logo)}}"
                         alt="School Logo"
                         class="school-logo bounce-in">
                </div>

                <!-- School Information -->
                <div class="col-12 col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item slide-in">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-id-card text-white"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Registration Number</small>
                                        <strong class="text-primary text-uppercase">{{$schools->school_reg_no}}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="info-item slide-in" style="animation-delay: 0.1s;">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info rounded-circle p-2 me-3">
                                        <i class="fas fa-map-marker-alt text-white"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block text-capitalize">Address</small>
                                        <strong>{{strtoupper($schools->postal_address)}} - {{ucwords(strtolower($schools->postal_name))}}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item slide-in" style="animation-delay: 0.2s;">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle p-2 me-3">
                                        <i class="fas fa-globe text-white"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Country</small>
                                        <strong>{{ucwords(strtolower($schools->country))}}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="info-item slide-in" style="animation-delay: 0.3s;">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded-circle p-2 me-3">
                                        <i class="fas fa-user-tie text-white"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block text-capitalize">School Admin</small>
                                        <strong>{{ucwords(strtolower($managers->first()->first_name))}} {{ucwords(strtolower($managers->first()->last_name))}}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Manager Profile -->
            <div class="row align-items-center">
                <div class="col-12 col-md-8">
                    <h4 class="text-primary mb-4">👨‍💼 School Administrator</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item text-capitalize">
                                <i class="fas fa-user me-2 text-primary"></i>
                                <strong>Name:</strong> {{$managers->first()->first_name}} {{$managers->first()->last_name}}
                            </div>
                            <div class="info-item text-capitalize">
                                <i class="fas fa-venus-mars me-2 text-primary"></i>
                                <strong>Gender:</strong> {{ucwords(strtolower($managers->first()->gender))}}
                            </div>
                            <div class="info-item">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                <strong>Phone:</strong> {{$managers->first()->phone}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                <strong>Email:</strong> {{strtolower($managers->first()->email)}}
                            </div>
                            <div class="info-item">
                                <i class="fas fa-circle me-2 text-primary"></i>
                                <strong>Status:</strong>
                                @if ($managers->first()->status == 1)
                                    <span class="badge-modern bg-success text-white">Active</span>
                                @else
                                    <span class="badge-modern bg-secondary">Blocked</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 text-center">
                    @if ($managers->first()->image == NULL)
                        @if ($managers->first()->gender == 'male')
                            <img src="{{asset('storage/profile/avatar.jpg')}}"
                                 alt="Manager Avatar"
                                 class="profile-img bounce-in">
                        @else
                            <img src="{{asset('storage/profile/avatar-female.jpg')}}"
                                 alt="Manager Avatar"
                                 class="profile-img bounce-in">
                        @endif
                    @else
                        <img src="{{asset('storage/profile/' .$managers->first()->image)}}"
                             alt="Manager Photo"
                             class="profile-img bounce-in">
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <!-- Teachers -->
            <div class="glass-card stat-card" style="background: linear-gradient(135deg, #4361ee, #3a0ca3);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-white-50 mb-2">👨‍🏫 Teachers</h6>
                        <h2 class="text-white mb-0 display-5 fw-bold">{{count($teachers)}}</h2>
                        <small class="text-white-50">Teaching staff members</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-chalkboard-teacher fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>

            <!-- Parents -->
            <div class="glass-card stat-card" style="background: linear-gradient(135deg, #7209b7, #560bad);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-white-50 mb-2">👪 Parents</h6>
                        <h2 class="text-white mb-0 display-5 fw-bold">{{count($parents)}}</h2>
                        <small class="text-white-50">Registered guardians</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-friends fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>

            <!-- Students -->
            <div class="glass-card stat-card" style="background: linear-gradient(135deg, #f72585, #b5179e);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-white-50 mb-2">🎓 Students</h6>
                        <h2 class="text-white mb-0 display-5 fw-bold">{{count($students)}}</h2>
                        <small class="text-white-50">Total enrolled students</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-graduation-cap fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>

            <!-- Classes -->
            <div class="glass-card stat-card" style="background: linear-gradient(135deg, #4cc9f0, #4895ef);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-white-50 mb-2">🏫 Classes</h6>
                        <h2 class="text-white mb-0 display-5 fw-bold">{{count($classes)}}</h2>
                        <small class="text-white-50">Active classes</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-school fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>

            <!-- Courses -->
            <div class="glass-card stat-card" style="background: linear-gradient(135deg, #f77f00, #d62828);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-white-50 mb-2">📚 Courses</h6>
                        <h2 class="text-white mb-0 display-5 fw-bold">{{count($courses)}}</h2>
                        <small class="text-white-50">Available courses</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-book fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>

            <!-- Performance -->
            <div class="glass-card stat-card" style="background: linear-gradient(135deg, #ff9e00, #ff6b00);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-white-50 mb-2">📊 Performance</h6>
                        <h2 class="text-white mb-0 display-5 fw-bold">-</h2>
                        <small class="text-white-50">Overall rating</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="text-center mt-4 fade-in">
            <a href="{{route('admin.generate.invoice', ['school' => Hashids::encode($schools->id)])}}"
               class="btn btn-modern btn-lg">
               <i class="fas fa-file-invoice-dollar me-2"></i> Generate Invoice
            </a>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Add interactive animations
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });

            // Add hover effects with GSAP if available
            if (typeof gsap !== 'undefined') {
                gsap.from('.fade-in', {
                    duration: 1,
                    y: 50,
                    opacity: 0,
                    stagger: 0.2,
                    ease: "power3.out"
                });

                gsap.from('.slide-in', {
                    duration: 0.8,
                    x: 100,
                    opacity: 0,
                    stagger: 0.1,
                    ease: "power2.out"
                });

                gsap.from('.bounce-in', {
                    duration: 1,
                    scale: 0.8,
                    opacity: 0,
                    ease: "elastic.out(1, 0.3)"
                });
            }
        });
    </script>
@endsection
