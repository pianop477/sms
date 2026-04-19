{{-- resources/views/teacher/e-permit/show.blade.php --}}
@extends('SRTDashboard.frame')

@section('content')
    <style>
        .header-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
        }

        .permit-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .permit-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 10px;
        }

        .status-pending {
            background: #f59e0b;
            color: #fff;
        }

        .status-pending-class-teacher {
            background: #f59e0b;
            color: #fff;
        }

        .status-pending-duty-teacher {
            background: #f59e0b;
            color: #fff;
        }

        .status-pending-academic {
            background: #f59e0b;
            color: #fff;
        }

        .status-pending-head {
            background: #f59e0b;
            color: #fff;
        }

        .status-approved {
            background: #22c55e;
            color: #fff;
        }

        .status-rejected {
            background: #ef4444;
            color: #fff;
        }

        .status-completed {
            background: #3b82f6;
            color: #fff;
        }

        .info-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        .card-title i {
            color: #667eea;
            margin-right: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .info-icon {
            width: 35px;
            height: 35px;
            background: #f1f5f9;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
        }

        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 2px;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
        }

        .student-photo {
            width: 100px;
            height: 100px;
            border-radius: 16px;
            object-fit: cover;
            border: 3px solid #667eea;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 25px;
        }

        .timeline-icon {
            position: absolute;
            left: -30px;
            top: 0;
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #e2e8f0;
            z-index: 1;
        }

        .timeline-icon.blue {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .timeline-icon.green {
            background: #22c55e;
            color: white;
            border-color: #22c55e;
        }

        .timeline-icon.red {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }

        .timeline-icon.orange {
            background: #f59e0b;
            color: white;
            border-color: #f59e0b;
        }

        .timeline-icon.purple {
            background: #8b5cf6;
            color: white;
            border-color: #8b5cf6;
        }

        .timeline-content {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px 15px;
            margin-left: 15px;
        }

        .timeline-title {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .timeline-meta {
            font-size: 0.75rem;
            color: #64748b;
        }

        .timeline-comment {
            margin-top: 8px;
            padding: 8px;
            background: white;
            border-radius: 8px;
            font-size: 0.85rem;
            border-left: 3px solid #667eea;
        }

        .approval-section {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
        }

        .btn-approve {
            background: #22c55e;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-approve:hover {
            background: #16a34a;
            transform: translateY(-2px);
        }

        .btn-reject {
            background: #ef4444;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-reject:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .btn-back {
            background: #64748b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background: #475569;
            color: white;
            transform: translateY(-2px);
        }

        .alert-info-custom {
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .info-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .info-badge-class-teacher {
            background: #dbeafe;
            color: #1e40af;
        }

        .info-badge-academic {
            background: #dcfce7;
            color: #166534;
        }

        .info-badge-head {
            background: #e0e7ff;
            color: #3730a3;
        }

        .return-section {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
        }

        .rejection-section {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 12px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .timeline {
                padding-left: 20px;
            }

            .timeline-icon {
                width: 30px;
                height: 30px;
                left: -25px;
            }

            .timeline-icon i {
                font-size: 12px;
            }

            .btn-approve,
            .btn-reject,
            .btn-back {
                padding: 8px 16px;
                font-size: 0.85rem;
            }
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt me-2"></i> e-Permit Details
                            </h5>
                            <div>
                                @php
                                    $roleClass = match ($teacher->role_id) {
                                        4 => 'class-teacher',
                                        3 => 'academic',
                                        2 => 'head',
                                        default => 'class-teacher',
                                    };
                                    $roleName = match ($teacher->role_id) {
                                        4 => 'Mwalimu wa Darasa',
                                        3 => 'Mwalimu wa Taaluma',
                                        2 => 'Mwalimu Mkuu',
                                        default => 'Mwalimu',
                                    };
                                @endphp
                                <span class="info-badge info-badge-{{ $roleClass }}">
                                    <i class="fas fa-user-shield me-1"></i> {{ $roleName }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Header Info -->
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-4">
                            <div>
                                <h3 class="mb-0">{{ $permit->permit_number }}</h3>
                                <small class="text-muted">Created: {{ $permit->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <div>
                                @php
                                    $statusDisplay = match ($permit->status) {
                                        'pending_class_teacher' => 'Pending Class Teacher',
                                        'pending_duty_teacher' => 'Pending Duty Teacher',
                                        'pending_academic' => 'Pending Academic Teacher',
                                        'pending_head' => 'Pending Head Teacher',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'completed' => 'Completed',
                                        default => ucfirst(str_replace('_', ' ', $permit->status)),
                                    };
                                    $statusClass = match ($permit->status) {
                                        'pending_class_teacher',
                                        'pending_duty_teacher',
                                        'pending_academic',
                                        'pending_head'
                                            => 'pending',
                                        'approved' => 'approved',
                                        'rejected' => 'rejected',
                                        'completed' => 'completed',
                                        default => 'pending',
                                    };
                                @endphp
                                <span class="permit-status status-{{ $statusClass }}">
                                    <i
                                        class="fas {{ $permit->status === 'approved' ? 'fa-check-circle' : ($permit->status === 'rejected' ? 'fa-times-circle' : 'fa-clock') }} me-1"></i>
                                    {{ $statusDisplay }}
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-lg-8">
                                <!-- Student Information -->
                                <div class="info-card">
                                    <div class="card-title">
                                        <i class="fas fa-user-graduate"></i> Taarifa za Mwanafunzi
                                    </div>
                                    <div class="d-flex gap-3 flex-wrap">
                                        @php
                                            $studentImage =
                                                $permit->student->image &&
                                                file_exists(
                                                    storage_path('app/public/students/' . $permit->student->image),
                                                )
                                                    ? asset('storage/students/' . $permit->student->image)
                                                    : asset('storage/students/student.jpg');
                                        @endphp
                                        <img src="{{ $studentImage }}" class="student-photo"
                                            onerror="this.src='{{ asset('storage/students/student.jpg') }}'"
                                            alt="Student Photo">
                                        <div class="flex-grow-1">
                                            <div class="info-grid">
                                                <div class="info-item">
                                                    <div class="info-icon"><i class="fas fa-user"></i></div>
                                                    <div>
                                                        <div class="info-label">Jina Kamili</div>
                                                        <div class="info-value">{{ ucfirst($permit->student->first_name) }}
                                                            {{ ucfirst($permit->student->middle_name) }}
                                                            {{ ucfirst($permit->student->last_name) }}</div>
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-icon"><i class="fas fa-id-card"></i></div>
                                                    <div>
                                                        <div class="info-label">Student ID</div>
                                                        <div class="info-value">
                                                            {{ strtoupper($permit->student->admission_number) }}</div>
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-icon"><i class="fas fa-chalkboard-user"></i></div>
                                                    <div>
                                                        <div class="info-label">Darasa</div>
                                                        <div class="info-value">
                                                            {{ strtoupper($permit->student->class->class_name ?? 'N/A') }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-icon"><i class="fas fa-code-branch"></i></div>
                                                    <div>
                                                        <div class="info-label">Mkondo / Stream</div>
                                                        <div class="info-value">
                                                            {{ strtoupper($permit->student->group ?? 'N/A') }}</div>
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-icon"><i class="fas fa-venus-mars"></i></div>
                                                    <div>
                                                        <div class="info-label">Jinsia</div>
                                                        <div class="info-value">
                                                            {{ ucfirst($permit->student->gender ?? 'N/A') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Parent/Guardian Information -->
                                <div class="info-card">
                                    <div class="card-title">
                                        <i class="fas fa-users"></i> Taarifa za Mzazi/Mlezi
                                    </div>
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <div class="info-icon"><i class="fas fa-user-circle"></i></div>
                                            <div>
                                                <div class="info-label">Jina Kamili</div>
                                                <div class="info-value">{{ ucwords(strtolower($permit->guardian_name)) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                                            <div>
                                                <div class="info-label">Namba ya Simu</div>
                                                <div class="info-value">{{ $permit->guardian_phone }}</div>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-icon"><i class="fas fa-handshake"></i></div>
                                            <div>
                                                <div class="info-label">Undugu</div>
                                                @php
                                                    $guardianType = $permit->guardian_type;
                                                    if (strtolower($guardianType) == 'parent') {
                                                        $guardianType = 'mzazi';
                                                    } else {
                                                        $guardianType = 'mlezi';
                                                    }
                                                @endphp
                                                <div class="info-value">{{ ucfirst($guardianType) }}</div>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-icon"><i class="fas fa-heart"></i></div>
                                            <div>
                                                <div class="info-label">Uhusiano</div>
                                                <div class="info-value">{{ ucfirst($permit->relationship) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permission Details -->
                                <div class="info-card">
                                    <div class="card-title">
                                        <i class="fas fa-clipboard-list"></i> Taarifa za Ruhusa
                                    </div>
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <div class="info-icon"><i class="fas fa-question-circle"></i></div>
                                            <div>
                                                <div class="info-label">Sababu ya Kuomba Ruhusa</div>
                                                <div class="info-value">
                                                    @php
                                                        $reasonText = match ($permit->reason) {
                                                            'medical' => 'Matibabu',
                                                            'family_matter' => 'Jambo la Kifamilia',
                                                            'other' => 'Sababu Nyingine',
                                                            default => ucfirst($permit->reason),
                                                        };
                                                    @endphp
                                                    {{ $reasonText }}
                                                    @if ($permit->other_reason)
                                                        <br><small class="text-muted">{{ $permit->other_reason }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-icon"><i class="fas fa-calendar-day"></i></div>
                                            <div>
                                                <div class="info-label">Tarehe ya Ombi</div>
                                                <div class="info-value">
                                                    {{ \Carbon\Carbon::parse($permit->departure_date)->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-icon"><i class="fas fa-clock"></i></div>
                                            <div>
                                                <div class="info-label">Muda wa Ombi</div>
                                                <div class="info-value">
                                                    {{ \Carbon\Carbon::parse($permit->departure_time)->format('H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-icon"><i class="fas fa-calendar-week"></i></div>
                                            <div>
                                                <div class="info-label">Tarehe ya Kurudi Shuleni</div>
                                                <div class="info-value">
                                                    {{ \Carbon\Carbon::parse($permit->expected_return_date)->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ============ REJECTION INFORMATION SECTION ============ -->
                                @if ($permit->status == 'rejected')
                                    <div class="info-card rejection-section">
                                        <div class="card-title" style="color: #dc2626;">
                                            <i class="fas fa-ban"></i> Taarifa za Kukataliwa kwa Ombi
                                        </div>
                                        <div class="info-grid">
                                            <div class="info-item">
                                                <div class="info-icon" style="background: #fee2e2; color: #dc2626;">
                                                    <i class="fas fa-user-times"></i>
                                                </div>
                                                <div>
                                                    <div class="info-label">Aliyekataa Ombi</div>
                                                    <div class="info-value">
                                                        @php
                                                            $rejectorName = 'Unknown';
                                                            $rejectorRole = '';

                                                            if ($permit->class_teacher_action === 'rejected') {
                                                                $rejectorName = ucwords(
                                                                    strtolower(
                                                                        $permit->classTeacher?->user?->first_name .
                                                                            ' ' .
                                                                            $permit->classTeacher?->user?->last_name,
                                                                    ),
                                                                );
                                                                $rejectorRole = 'Mwalimu wa Darasa';
                                                            } elseif ($permit->duty_teacher_action === 'rejected') {
                                                                $rejectorName = ucwords(
                                                                    strtolower(
                                                                        $permit->dutyTeacher?->user?->first_name .
                                                                            ' ' .
                                                                            $permit->dutyTeacher?->user?->last_name,
                                                                    ),
                                                                );
                                                                $rejectorRole = 'Mwalimu wa Zamu';
                                                            } elseif ($permit->academic_teacher_action === 'rejected') {
                                                                $rejectorName = ucwords(
                                                                    strtolower(
                                                                        $permit->academicTeacher?->user?->first_name .
                                                                            ' ' .
                                                                            $permit->academicTeacher?->user?->last_name,
                                                                    ),
                                                                );
                                                                $rejectorRole = 'Mwalimu wa Taaluma';
                                                            } elseif ($permit->head_teacher_action === 'rejected') {
                                                                $rejectorName = ucwords(
                                                                    strtolower(
                                                                        $permit->headTeacher?->user?->first_name .
                                                                            ' ' .
                                                                            $permit->headTeacher?->user?->last_name,
                                                                    ),
                                                                );
                                                                $rejectorRole = 'Mwalimu Mkuu';
                                                            }
                                                        @endphp
                                                        <strong>{{ $rejectorName }}</strong><br>
                                                        <small class="text-muted">{{ $rejectorRole }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-icon" style="background: #fee2e2; color: #dc2626;">
                                                    <i class="fas fa-calendar-times"></i>
                                                </div>
                                                <div>
                                                    <div class="info-label">Tarehe ya Kukataliwa</div>
                                                    <div class="info-value">
                                                        @php
                                                            $rejectionTime = null;
                                                            if ($permit->class_teacher_action === 'rejected') {
                                                                $rejectionTime = $permit->class_teacher_approved_at;
                                                            } elseif ($permit->duty_teacher_action === 'rejected') {
                                                                $rejectionTime = $permit->duty_teacher_approved_at;
                                                            } elseif ($permit->academic_teacher_action === 'rejected') {
                                                                $rejectionTime = $permit->academic_teacher_approved_at;
                                                            } elseif ($permit->head_teacher_action === 'rejected') {
                                                                $rejectionTime = $permit->head_teacher_approved_at;
                                                            }
                                                        @endphp
                                                        {{ $rejectionTime ? \Carbon\Carbon::parse($rejectionTime)->format('d/m/Y H:i') : 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-icon" style="background: #fee2e2; color: #dc2626;">
                                                    <i class="fas fa-comment-dots"></i>
                                                </div>
                                                <div>
                                                    <div class="info-label">Sababu ya Kukataliwa</div>
                                                    <div class="info-value">
                                                        <div class="alert alert-danger mb-0"
                                                            style="background: #fee2e2; border-color: #fecaca; border-radius: 8px; padding: 10px;">
                                                            <i class="fas fa-exclamation-circle me-2"></i>
                                                            {{ ucfirst($permit->rejection_reason ?? ($permit->class_teacher_comment ?? ($permit->duty_teacher_comment ?? ($permit->academic_teacher_comment ?? ($permit->head_teacher_comment ?? 'Hakuna sababu iliyotolewa'))))) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- ============ RETURN INFORMATION SECTION ============ -->
                                @if ($permit->status == 'completed' && $permit->verified_at)
                                    <div class="info-card return-section">
                                        <div class="card-title">
                                            <i class="fas fa-undo-alt"></i> Taarifa za Kurejea Shuleni
                                        </div>
                                        <div class="info-grid">
                                            <div class="info-item">
                                                <div class="info-icon"><i class="fas fa-calendar-check"></i></div>
                                                <div>
                                                    <div class="info-label">Tarehe ya Kurejea</div>
                                                    <div class="info-value">
                                                        {{ \Carbon\Carbon::parse($permit->verified_at)->format('d/m/Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-icon"><i class="fas fa-user-check"></i></div>
                                                <div>
                                                    <div class="info-label">Aliyethibitisha</div>
                                                    <div class="info-value">
                                                        {{ ucwords(strtolower($permit->verifier?->user?->first_name . ' ' . $permit->verifier?->user?->last_name)) }}
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($permit->is_late_return)
                                                <div class="info-item">
                                                    <div class="info-icon"><i class="fas fa-exclamation-triangle"
                                                            style="color: #f59e0b;"></i></div>
                                                    <div>
                                                        <div class="info-label">Hali ya Kurejea</div>
                                                        <div class="info-value"><span
                                                                class="badge bg-warning text-dark">Imechelewa</span></div>
                                                    </div>
                                                </div>
                                                @if ($permit->late_return_reason)
                                                    <div class="info-item">
                                                        <div class="info-icon"><i class="fas fa-comment"></i></div>
                                                        <div>
                                                            <div class="info-label">Sababu ya Kuchelewa</div>
                                                            <div class="info-value">
                                                                {{ ucfirst($permit->late_return_reason) }}</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="info-item">
                                                    <div class="info-icon"><i class="fas fa-check-circle"
                                                            style="color: #22c55e;"></i></div>
                                                    <div>
                                                        <div class="info-label">Hali ya Kurejea</div>
                                                        <div class="info-value"><span class="badge bg-success">Kwa
                                                                Wakati</span></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Accompanied Person Information -->
                                        @if ($permit->returned_alone == 0 || $permit->returned_alone === false)
                                            <div
                                                style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e2e8f0;">
                                                <h6 class="mb-3" style="color: #475569;"><i
                                                        class="fas fa-users me-2"></i>Taarifa za Aliyemrudisha</h6>
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <div class="info-icon"><i class="fas fa-user"></i></div>
                                                        <div>
                                                            <div class="info-label">Jina Kamili</div>
                                                            <div class="info-value">
                                                                {{ ucwords(strtolower($permit->return_accompanied_by)) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="info-item">
                                                        <div class="info-icon"><i class="fas fa-handshake"></i></div>
                                                        <div>
                                                            <div class="info-label">Undugu</div>
                                                            @php
                                                                $returnGuardianType = match (
                                                                    $permit->return_guardian_type
                                                                ) {
                                                                    'parent' => 'Mzazi',
                                                                    'guardian' => 'Mlezi',
                                                                    default => ucfirst($permit->return_guardian_type),
                                                                };
                                                            @endphp
                                                            <div class="info-value">{{ $returnGuardianType }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="info-item">
                                                        <div class="info-icon"><i class="fas fa-heart"></i></div>
                                                        <div>
                                                            <div class="info-label">Uhusiano</div>
                                                            <div class="info-value">
                                                                {{ ucfirst($permit->return_relationship) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div
                                                style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e2e8f0;">
                                                <div class="info-item">
                                                    <div class="info-icon"><i class="fas fa-user-check"></i></div>
                                                    <div>
                                                        <div class="info-label">Hali ya Kurejea</div>
                                                        <div class="info-value">Mwanafunzi amerudi peke yake</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column - Timeline -->
                            <div class="col-lg-4">
                                <div class="info-card">
                                    <div class="card-title">
                                        <i class="fas fa-chart-line"></i> Workflow Timeline
                                    </div>
                                    <div class="timeline">
                                        @foreach ($timeline as $item)
                                            <div class="timeline-item">
                                                <div class="timeline-icon {{ $item['color'] }}">
                                                    <i class="fas {{ $item['icon'] }}"></i>
                                                </div>
                                                <div class="timeline-content">
                                                    <div class="timeline-title">{{ $item['stage'] }}</div>
                                                    <div class="timeline-meta">
                                                        <i class="fas fa-user"></i> {{ $item['person'] }}<br>
                                                        <i class="fas fa-clock"></i>
                                                        {{ \Carbon\Carbon::parse($item['time'])->format('d/m/Y H:i') }}
                                                        @if (isset($item['status']) &&
                                                                $item['status'] !== 'submitted' &&
                                                                $item['status'] !== 'completed' &&
                                                                $item['status'] !== 'skipped')
                                                            <br>
                                                            <span
                                                                class="badge {{ $item['status'] === 'approved' ? 'bg-success' : 'bg-danger' }}">
                                                                {{ ucfirst($item['status']) }}
                                                            </span>
                                                        @endif
                                                        @if (isset($item['status']) && $item['status'] === 'skipped')
                                                            <br>
                                                            <span class="badge bg-warning text-dark">Imerukwa</span>
                                                        @endif
                                                    </div>
                                                    @if (isset($item['comment']) && $item['comment'])
                                                        <div class="timeline-comment">
                                                            <i class="fas fa-comment"></i> {{ $item['comment'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Section -->
                        @if (in_array($permit->status, ['pending_class_teacher', 'pending_duty_teacher', 'pending_academic', 'pending_head']) &&
                                $canApprove)
                            <div class="info-card">
                                <div class="card-title">
                                    <i class="fas fa-gavel"></i> Thibitisha au Kataa Ombi
                                </div>

                                <!-- Info for Class Teacher -->
                                @if ($permit->status === 'pending_class_teacher' && $teacher->role_id == 4)
                                    @if (!$hasDutyTeacher)
                                        <div class="alert-info-custom">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Taarifa:</strong> Hakuna mwalimu wa zamu kwenye duty roster kwa tarehe
                                            hii ({{ \Carbon\Carbon::parse($permit->departure_date)->format('d/m/Y') }}).
                                            Baada ya kuthibitisha kwako, ombi litaenda moja kwa moja kwa Mwalimu wa Taaluma.
                                        </div>
                                    @else
                                        <div class="alert-info-custom">
                                            <i class="fas fa-check-circle me-2 text-success"></i>
                                            <strong>Taarifa:</strong> Kuna mwalimu wa zamu kwa tarehe hii. Baada ya
                                            kuthibitisha kwako, ombi litaenda kwa Mwalimu wa Zamu.
                                        </div>
                                    @endif
                                @endif

                                <!-- Info for Academic Teacher acting as Duty Teacher -->
                                @if ($permit->status === 'pending_duty_teacher' && $teacher->role_id == 3)
                                    <div class="alert-info-custom">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Taarifa:</strong> Unathibitisha ombi kwa niaba ya Mwalimu wa Zamu. Ombi
                                        litaenda moja kwa moja kwa Mwalimu Mkuu baada ya uthibitisho wako.
                                    </div>
                                @endif

                                <!-- Info for Academic Teacher -->
                                @if ($permit->status === 'pending_academic' && $teacher->role_id == 3)
                                    <div class="alert-info-custom">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Taarifa:</strong> Baada ya kuthibitisha kwako, ombi litaenda kwa Mwalimu
                                        Mkuu kwa uthibitisho wa mwisho.
                                    </div>
                                @endif

                                <!-- Info for Head Teacher -->
                                @if ($permit->status === 'pending_head' && $teacher->role_id == 2)
                                    <div class="alert-info-custom">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Taarifa:</strong> Wewe ndiye ngazi ya mwisho ya uthibitisho. Baada ya
                                        kuthibitisha, ruhusa itatolewa na mzazi atapata kibali.
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Maoni (Si lazima)</label>
                                    <textarea id="approveComment" class="form-control" rows="3"
                                        placeholder="Andika maoni yako kuhusu ombi hili..."></textarea>
                                </div>

                                <div class="d-flex gap-2 flex-wrap">
                                    <button onclick="approvePermit({{ $permit->id }})" class="btn-approve">
                                        <i class="fas fa-check"></i> Kubali Ombi
                                    </button>
                                    <button onclick="showRejectModal({{ $permit->id }})" class="btn-reject">
                                        <i class="fas fa-times"></i> Kataa Ombi
                                    </button>
                                    <a href="{{ route('teacher.e-permit.dashboard') }}" class="btn-back">
                                        <i class="fas fa-arrow-left"></i> Rudi Nyuma
                                    </a>
                                </div>
                            </div>
                        @elseif(in_array($permit->status, ['pending_class_teacher', 'pending_duty_teacher', 'pending_academic', 'pending_head']) &&
                                !$canApprove)
                            <div class="info-card">
                                <div class="alert-info-custom">
                                    <i class="fas fa-lock me-2"></i>
                                    <strong>Kumbuka:</strong> Huna uwezo wa kuthibitisha ombi hili kwa sababu wewe si
                                    mwalimu aliyepewa jukumu la hatua hii.
                                </div>
                                <a href="{{ route('teacher.e-permit.dashboard') }}" class="btn-back">
                                    <i class="fas fa-arrow-left"></i> Rudi Nyuma
                                </a>
                            </div>
                        @else
                            <div class="info-card">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        Ombi hili limekwisha kushughulikiwa
                                    </div>
                                    <a href="{{ route('teacher.e-permit.dashboard') }}" class="btn-back">
                                        <i class="fas fa-arrow-left"></i> Rudi Nyuma
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function approvePermit(permitId) {
            const comment = document.getElementById('approveComment')?.value || '';

            Swal.fire({
                title: 'Thibitisha Ombi',
                text: 'Una hakika unataka kukubali ombi hili la ruhusa?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ndiyo, Kubali',
                cancelButtonText: 'Hapana, Ghairi'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Inachakata...',
                        text: 'Tafadhali subiri',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`{{ url('teacher/e-permit') }}/${permitId}/approve`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                comment: comment
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Imefanikiwa!', data.message, 'success')
                                    .then(() => window.location.reload());
                            } else {
                                Swal.fire('Hitilafu!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Hitilafu!', 'Tafadhali jaribu tena', 'error');
                        });
                }
            });
        }

        function showRejectModal(permitId) {
            Swal.fire({
                title: 'Kataa Ombi',
                text: 'Je, una hakika unataka kukataa ombi hili la ruhusa?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ndiyo, Kataa',
                cancelButtonText: 'Hapana, Ghairi',
                input: 'textarea',
                inputPlaceholder: 'Tafadhali andika sababu ya kukataa...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Sababu ya kukataa inahitajika!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Inachakata...',
                        text: 'Tafadhali subiri',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`{{ url('teacher/e-permit') }}/${permitId}/reject`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                reason: result.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Imefanikiwa!', data.message, 'success')
                                    .then(() => window.location.href =
                                        '{{ route('teacher.e-permit.dashboard') }}');
                            } else {
                                Swal.fire('Hitilafu!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Hitilafu!', 'Tafadhali jaribu tena', 'error');
                        });
                }
            });
        }
    </script>
@endsection
