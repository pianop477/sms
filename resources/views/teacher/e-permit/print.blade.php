{{-- resources/views/teacher/e-permit/print.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permit_{{ $permit->permit_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-dark: #1e293b;
            --accent-blue: #2563eb;
            --border-color: #e2e8f0;
            --text-main: #334155;
            --text-muted: #64748b;
        }

        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: var(--text-main);
            line-height: 1.5;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Paper Simulation */
        .print-container {
            max-width: 21cm;
            /* A4 Width */
            margin: 30px auto;
            background: white;
            padding: 2cm;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        /* Watermark */
        .print-container::before {
            content: "OFFICIAL DOCUMENT";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            font-weight: 900;
            color: rgba(0, 0, 0, 0.02);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
        }

        /* Top Security Bar */
        .security-bar {
            height: 4px;
            background: linear-gradient(90deg, var(--primary-dark), var(--accent-blue));
            margin: -2cm -2cm 1.5cm -2cm;
        }

        /* Header Section */
        .header-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: end;
            border-bottom: 2px solid var(--primary-dark);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .school-info h4 {
            font-weight: 700;
            letter-spacing: -1px;
            color: var(--primary-dark);
            margin: 0;
            /* text-transform: uppercase; */
        }

        .permit-meta {
            text-align: right;
        }

        .permit-id-badge {
            background: var(--primary-dark);
            color: white;
            padding: 5px 15px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Profile Section */
        .student-hero {
            display: flex;
            gap: 25px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
        }

        .student-photo {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .info-table {
            width: 100%;
            font-size: 0.9rem;
        }

        .info-table td {
            padding: 5px 0;
        }

        .label {
            color: var(--text-muted);
            font-weight: 500;
            width: 150px;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .value {
            font-weight: 600;
            color: var(--primary-dark);
        }

        /* Sections */
        .section-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--accent-blue);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        /* Workflow Grid */
        .workflow-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .workflow-card {
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.8rem;
            position: relative;
        }

        .workflow-card.approved {
            border-left: 4px solid #22c55e;
        }

        .status-stamp {
            font-weight: 700;
            color: #22c55e;
            text-transform: uppercase;
            font-size: 0.7rem;
        }

        /* Footer/Signatures */
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 40px;
        }

        .sig-box {
            border-top: 1px solid var(--primary-dark);
            padding-top: 10px;
            text-align: center;
        }

        .sig-box p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin: 0;
        }

        /* Print Override */
        @media print {
            body {
                background: white;
            }

            .print-container {
                margin: 0;
                box-shadow: none;
                max-width: 100%;
                padding: 1cm;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 0;
            }
        }

        .digital-hash {
            font-family: monospace;
            font-size: 10px;
            color: var(--primary-dark);
            text-align: center;
            margin-top: 30px;
            opacity: 0.5;
        }
    </style>
</head>

<body>

    <div class="no-print container mt-4 mb-4 text-center">
        <button onclick="window.print()" class="btn btn-dark px-4 shadow-sm">
            <i class="fas fa-print me-2"></i> Print Official Copy
        </button>
        <a href="{{ route('teacher.e-permit.dashboard') }}" class="btn btn-outline-secondary px-4 ms-2">
            <i class="fas fa-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <div class="print-container">
        <div class="security-bar"></div>

        <div class="header-grid">
            <div class="d-flex align-items-center gap-4">
                @if ($school->logo)
                    <img src="{{ asset('storage/logo/' . $school->logo) }}" alt="Logo"
                        style="height: 80px; width: auto; object-fit: contain;">
                @endif
                <div class="school-info">
                    <h4 class="mb-1 text-uppercase">{{ $school->school_name }}</h4>
                    <div class="small text-muted" style="line-height: 1.3">
                        @if ($school->postal_name || $school->postal_address)
                            <div> {{ strtoupper($school->postal_address) }} - {{ ucfirst($school->postal_name) }}</div>
                        @endif
                        <div>
                            <i class="fas fa-phone me-1" style="font-size: 10px;"></i> {{ $school->school_phone }}
                            <span class="mx-2">|</span>
                            <i class="fas fa-envelope me-1" style="font-size: 10px;"></i> {{ $school->school_email }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="permit-meta">
                <div class="permit-id-badge mb-2"># {{ $permit->permit_number }}</div>
                <p class="small text-muted mb-0">Issued: {{ $permit->created_at->format('M d, Y | H:i') }}</p>
            </div>
        </div>

        <div class="section-title">Student Identification</div>
        <div class="student-hero">
            @php
                $studentImage =
                    $permit->student->image &&
                    file_exists(storage_path('app/public/students/' . $permit->student->image))
                        ? asset('storage/students/' . $permit->student->image)
                        : asset('storage/students/student.jpg');
            @endphp
            <img src="{{ $studentImage }}" class="student-photo" alt="Profile">
            <table class="info-table">
                <tr>
                    <td class="label">Full Name</td>
                    <td class="value">
                        {{ ucwords(strtolower($permit->student->first_name . ' ' . ($permit->student->middle_name ?? '') . ' ' . $permit->student->last_name)) }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Student ID</td>
                    <td class="value">{{ strtoupper($permit->student->admission_number) }}</td>
                </tr>
                <tr>
                    <td class="label">Class</td>
                    <td class="value">{{ strtoupper($permit->student->class->class_name ?? 'N/A') }}
                        ({{ strtoupper($permit->student->group ?? ($permit->student->stream ?? 'N/A')) }})</td>
                </tr>
            </table>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="section-title">Guardian Information</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Full Name</td>
                        <td class="value">{{ ucwords(strtolower($permit->guardian_name)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Phone</td>
                        <td class="value">{{ $permit->guardian_phone }}</td>
                    </tr>
                    <tr>
                        <td class="label">Relationship</td>
                        @php
                            $guardianType = match ($permit->guardian_type) {
                                'parent' =>  'Mzazi',
                                'guardian' => 'Mlezi',
                            }
                        @endphp
                        <td class="value">{{ ucfirst($guardianType)}} - {{ucfirst($permit->relationship)}}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <div class="section-title">Permission Details</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Departure</td>
                        <td class="value">{{ $permit->departure_date->format('d M, Y') }} -
                            {{ $permit->departure_time->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Return By</td>
                        <td class="value">{{ $permit->expected_return_date->format('d M, Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <div class="section-title">Reason for Exit</div>
            <p class="value"
                style="background: #fffbeb; padding: 10px; border-radius: 6px; border-left: 4px solid #f59e0b;">
                @php
                    $reasonText = match ($permit->reason) {
                        'medical' => 'Matibabu',
                        'family_matter' => 'Mambo ya Kifamilia',
                        default => ucfirst($permit->reason),
                    };
                @endphp
                {{ $reasonText }}
                @if ($permit->other_reason)
                    <span class="text-muted fw-normal"> — {{ $permit->other_reason }}</span>
                @endif
            </p>
        </div>

        <div class="section-title mt-4">Permission Authorization Approval</div>
        <div class="workflow-container">
            <div class="workflow-card {{ $permit->class_teacher_approved_at ? 'approved' : '' }}">
                <div class="text-muted small">Class Teacher</div>
                <div class="fw-bold mb-1">
                    {{ ucwords(strtolower($permit->classTeacher?->user?->first_name . ' ' . $permit->classTeacher?->user?->last_name)) }}
                </div>
                <div class="status-stamp">
                    <i class="fas {{ $permit->class_teacher_approved_at ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $permit->class_teacher_approved_at ? 'Verified' : 'Pending' }}
                </div>
            </div>
            <div class="workflow-card {{ $permit->academic_teacher_approved_at ? 'approved' : '' }}">
                <div class="text-muted small">Academic Office</div>
                <div class="fw-bold mb-1">
                    {{ ucwords(strtolower($permit->academicTeacher?->user?->first_name . ' ' . $permit->academicTeacher?->user?->last_name)) }}
                </div>
                <div class="status-stamp">
                    <i class="fas {{ $permit->academic_teacher_approved_at ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $permit->academic_teacher_approved_at ? 'Verified' : 'Pending' }}
                </div>
            </div>
            <div class="workflow-card {{ $permit->head_teacher_approved_at ? 'approved' : '' }}">
                <div class="text-muted small">Head Teacher Office</div>
                <div class="fw-bold mb-1">
                    {{ ucwords(strtolower($permit->headTeacher?->user?->first_name . ' ' . $permit->headTeacher?->user?->last_name)) }}
                </div>
                <div class="status-stamp">
                    <i class="fas {{ $permit->head_teacher_approved_at ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $permit->head_teacher_approved_at ? 'Authorized' : 'Pending' }}
                </div>
            </div>
        </div>

        <div class="row align-items-center mt-5">
            {{-- <div class="col-4 text-center">
                <div style="border: 1px solid var(--border-color); padding: 10px; display: inline-block; border-radius: 8px;">
                     <i class="fas fa-qrcode fa-5x"></i>
                </div>
                <p class="text-muted mt-2" style="font-size: 9px;">VERIFY VIA SCHOOL PORTAL</p>
            </div> --}}
            <div class="col-12">
                <div class="signature-grid">
                    <div class="sig-box">
                        <p>Parent/Guardian Signature</p>
                    </div>
                    <div class="sig-box">
                        <p>Authority / Stamp</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digital-hash">
            Generated via ShuleApp e-Permit System Module
        </div>
    </div>
</body>

</html>
