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
            width: 180px;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
        }

        .label-sw {
            color: var(--text-muted);
            font-weight: 400;
            font-size: 0.65rem;
            font-style: italic;
            display: block;
            margin-top: 1px;
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

        .section-title .sw-label {
            font-weight: 400;
            color: var(--text-muted);
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            text-transform: lowercase;
            margin-left: 8px;
        }

        .section-title .sw-label::before {
            content: "(";
        }

        .section-title .sw-label::after {
            content: ")";
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

        .sig-box .sw-label {
            font-size: 0.65rem;
            color: var(--text-muted);
            font-style: italic;
            display: block;
            margin-top: 2px;
        }

        /* Print Override - FIXED with balanced spacing */
        @media print {
            /* Reset body and html for print */
            html, body {
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                overflow: visible !important;
            }

            .print-container {
                margin: 0 !important;
                padding: 1.5cm !important;
                max-width: 100% !important;
                box-shadow: none !important;
                min-height: 100% !important;
                height: 100% !important;
                overflow: visible !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
            }

            .notice-box {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            .no-print {
                display: none !important;
            }

            /* Critical fix: Prevent content from spilling to next page */
            .header-grid,
            .student-hero,
            .workflow-container,
            .signature-grid,
            .notice-box {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            /* Maintain original spacing but balanced */
            .security-bar {
                margin: -1.5cm -1.5cm 1.2cm -1.5cm !important;
            }

            .header-grid {
                padding-bottom: 15px !important;
                margin-bottom: 20px !important;
            }

            .student-hero {
                padding: 15px !important;
                margin-bottom: 20px !important;
                gap: 20px !important;
            }

            .student-photo {
                width: 120px !important;
                height: 120px !important;
            }

            .section-title {
                margin-bottom: 10px !important;
                padding-bottom: 5px !important;
                font-size: 0.8rem !important;
            }

            .workflow-container {
                margin-top: 15px !important;
                gap: 12px !important;
            }

            .workflow-card {
                padding: 12px !important;
                font-size: 0.75rem !important;
            }

            .signature-grid {
                margin-top: 25px !important;
                gap: 40px !important;
            }

            .info-table td {
                padding: 4px 0 !important;
                font-size: 0.85rem !important;
            }

            .label {
                font-size: 0.65rem !important;
                width: 160px !important;
            }

            .label-sw {
                font-size: 0.6rem !important;
            }

            .digital-hash {
                margin-top: 20px !important;
                font-size: 8px !important;
            }

            .notice-box {
                padding: 12px 18px !important;
                margin-top: 20px !important;
            }

            .notice-box p {
                font-size: 0.8rem !important;
                margin: 0 !important;
            }

            .notice-box .sw-label {
                font-size: 0.7rem !important;
            }

            /* Ensure everything stays on one page */
            @page {
                size: A4 portrait;
                margin: 0.8cm !important;
            }

            /* Force single page */
            .print-container {
                page-break-after: avoid !important;
            }

            /* Adjust logo size */
            .school-info h4 {
                font-size: 1.2rem !important;
            }

            .permit-id-badge {
                font-size: 0.95rem !important;
                padding: 4px 12px !important;
            }

            /* Reduce spacing in hero section */
            .student-hero .info-table .label {
                width: 140px !important;
            }

            /* Ensure content is vertically centered */
            .print-container > *:last-child {
                margin-bottom: 0 !important;
            }

            .row {
                margin-bottom: 5px !important;
            }

            .mt-4 {
                margin-top: 15px !important;
            }

            .mt-5 {
                margin-top: 20px !important;
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

        <div class="section-title">
            Student Identification
            <span class="sw-label">Utambulisho wa Mwanafunzi</span>
        </div>
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
                    <td class="label">
                        Full Name
                        <span class="label-sw">Jina Kamili</span>
                    </td>
                    <td class="value">
                        {{ ucwords(strtolower($permit->student->first_name . ' ' . ($permit->student->middle_name ?? '') . ' ' . $permit->student->last_name)) }}
                    </td>
                </tr>
                <tr>
                    <td class="label">
                        Student ID
                        <span class="label-sw">Namba ya Mwanafunzi</span>
                    </td>
                    <td class="value">{{ strtoupper($permit->student->admission_number) }}</td>
                </tr>
                <tr>
                    <td class="label">
                        Class
                        <span class="label-sw">Darasa</span>
                    </td>
                    <td class="value">{{ strtoupper($permit->student->class->class_name ?? 'N/A') }}
                        ({{ strtoupper($permit->student->group ?? ($permit->student->stream ?? 'N/A')) }})</td>
                </tr>
            </table>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="section-title">
                    Guardian Information
                    <span class="sw-label">Maelezo ya Mlezi/Mzazi</span>
                </div>
                <table class="info-table">
                    <tr>
                        <td class="label">
                            Full Name
                            <span class="label-sw">Jina Kamili</span>
                        </td>
                        <td class="value">{{ ucwords(strtolower($permit->guardian_name)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">
                            Phone
                            <span class="label-sw">Namba ya Simu</span>
                        </td>
                        <td class="value">{{ $permit->guardian_phone }}</td>
                    </tr>
                    <tr>
                        <td class="label">
                            Relationship
                            <span class="label-sw">Uhusiano</span>
                        </td>
                        @php
                        $guardianType = match ($permit->guardian_type) {
                        'parent' => 'Mzazi',
                        'guardian' => 'Mlezi',
                        }
                        @endphp
                        <td class="value">{{ ucfirst($guardianType)}} - {{ucfirst($permit->relationship)}}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <div class="section-title">
                    Permission Details
                    <span class="sw-label">Maelezo ya Ruhusa</span>
                </div>
                <table class="info-table">
                    <tr>
                        <td class="label">
                            Departure
                            <span class="label-sw">Kuondoka</span>
                        </td>
                        <td class="value">{{ $permit->departure_date->format('d M, Y') }} -
                            {{ $permit->departure_time->format('H:i') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            Return By
                            <span class="label-sw">Kurudi</span>
                        </td>
                        <td class="value">{{ $permit->expected_return_date->format('d M, Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <div class="section-title">
                Reason for Exit
                <span class="sw-label">Sababu ya Kuondoka</span>
            </div>
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

        <div class="section-title mt-4">
            Permission Authorization Approval
            <span class="sw-label">Idhini ya Ruhusa</span>
        </div>
        <div class="workflow-container">
            <div class="workflow-card {{ $permit->class_teacher_approved_at ? 'approved' : '' }}">
                <div class="text-muted small">
                    Class Teacher
                    <span class="label-sw" style="display: block; font-size: 0.6rem;">Mwalimu wa Darasa</span>
                </div>
                <div class="fw-bold mb-1">
                    {{ ucwords(strtolower($permit->classTeacher?->user?->first_name . ' ' . $permit->classTeacher?->user?->last_name)) }}
                </div>
                <div class="status-stamp">
                    <i class="fas {{ $permit->class_teacher_approved_at ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $permit->class_teacher_approved_at ? 'Verified' : 'Pending' }}
                    <span class="label-sw" style="display: block; font-size: 0.55rem; color: #22c55e;">
                        {{ $permit->class_teacher_approved_at ? 'Imethibitishwa' : 'Inasubiri' }}
                    </span>
                </div>
            </div>
            <div class="workflow-card {{ $permit->academic_teacher_approved_at ? 'approved' : '' }}">
                <div class="text-muted small">
                    Academic Office
                    <span class="label-sw" style="display: block; font-size: 0.6rem;">Ofisi ya Masomo</span>
                </div>
                <div class="fw-bold mb-1">
                    {{ ucwords(strtolower($permit->academicTeacher?->user?->first_name . ' ' . $permit->academicTeacher?->user?->last_name)) }}
                </div>
                <div class="status-stamp">
                    <i class="fas {{ $permit->academic_teacher_approved_at ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $permit->academic_teacher_approved_at ? 'Verified' : 'Pending' }}
                    <span class="label-sw" style="display: block; font-size: 0.55rem; color: #22c55e;">
                        {{ $permit->academic_teacher_approved_at ? 'Imethibitishwa' : 'Inasubiri' }}
                    </span>
                </div>
            </div>
            <div class="workflow-card {{ $permit->head_teacher_approved_at ? 'approved' : '' }}">
                <div class="text-muted small">
                    Head Teacher Office
                    <span class="label-sw" style="display: block; font-size: 0.6rem;">Ofisi ya Mkuu wa Shule</span>
                </div>
                <div class="fw-bold mb-1">
                    {{ ucwords(strtolower($permit->headTeacher?->user?->first_name . ' ' . $permit->headTeacher?->user?->last_name)) }}
                </div>
                <div class="status-stamp">
                    <i class="fas {{ $permit->head_teacher_approved_at ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $permit->head_teacher_approved_at ? 'Authorized' : 'Pending' }}
                    <span class="label-sw" style="display: block; font-size: 0.55rem; color: #22c55e;">
                        {{ $permit->head_teacher_approved_at ? 'Imeidhinishwa' : 'Inasubiri' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="row align-items-center mt-5">
            <div class="col-12">
                <div class="signature-grid">
                    <div class="sig-box">
                        <p>
                            Parent/Guardian Signature
                        </p>
                    </div>
                    <div class="sig-box">
                        <p>
                            Authority / Stamp
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- NOTICE / REMINDER -->
        <div class="notice-box mt-4" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 15px 20px;">
            <div class="d-flex align-items-start gap-3">
                <div>
                    <div style="font-weight: 700; color: #991b1b; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        KUMBUKA:
                        <span class="sw-label" style="font-weight: 400; color: #991b1b; font-size: 0.75rem; letter-spacing: 0.5px;">
                            Fomu hii <strong>INAPASWA KURUDISHWA</strong> shuleni siku ya kurudi (<strong>{{ $permit->expected_return_date->format('d M, Y') }}</strong>)
                            kwa ajili ya kuthibitisha kurudi kwa mwanafunzi.
                        </span>
                    </div>
                    <p style="margin: 5px 0 0 0; color: #991b1b; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <br>
                        <b>REMINDER:</b>
                        <span class="sw-label" style="font-weight: 400; color: #991b1b; font-size: 0.75rem; letter-spacing: 0.5px;">
                            This form <strong>MUST BE RETURNED</strong> to school on the day of return (<strong>{{ $permit->expected_return_date->format('d M, Y') }}</strong>) to confirm the student's return.
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="digital-hash">
            Generated via ShuleApp e-Permit System
        </div>
    </div>
</body>

</html>