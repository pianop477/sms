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
        --gold: #ffd700;
        --teal: #33a4c6;
        --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --shadow-sm: 0 5px 15px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 10px 25px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        min-height: 100vh;
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
        position: relative;
        overflow-x: hidden;
    }

    /* Animated Background */
    .animated-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
    }

    .animated-bg::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background:
            radial-gradient(circle at 70% 30%, rgba(67, 97, 238, 0.1) 0%, transparent 30%),
            radial-gradient(circle at 30% 70%, rgba(63, 55, 201, 0.1) 0%, transparent 30%);
        animation: rotate 60s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Floating Particles */
    .particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
    }

    .particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        animation: float 20s infinite;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(100px, -100px) scale(1.2); }
        50% { transform: translate(200px, 0) scale(0.8); }
        75% { transform: translate(100px, 100px) scale(1.1); }
    }

    /* Main Container */
    .dashboard-container {
        max-width: 1600px;
        margin: 30px auto;
        padding: 0 20px;
        position: relative;
        z-index: 1;
    }

    /* Glass Card Effect */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 30px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.5);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
        position: relative;
    }

    .glass-card::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, var(--primary), var(--secondary), var(--accent));
        border-radius: 32px;
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: -1;
    }

    .glass-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 30px 50px rgba(0, 0, 0, 0.3);
    }

    .glass-card:hover::before {
        opacity: 0.2;
    }

    /* Header Styles */
    .header-modern {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        padding: 40px;
        position: relative;
        overflow: hidden;
        border-radius: 30px 30px 30px 30px;
        margin-bottom: 30px;
    }

    .header-modern::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 60%);
        /* animation: spin 30s linear infinite; */
    }

    .header-modern::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--gold), var(--teal), var(--success));
    }

    .header-title {
        color: white;
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        position: relative;
        z-index: 1;
        animation: slideInDown 0.8s ease-out;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .header-badge {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        padding: 12px 25px;
        border-radius: 50px;
        color: white;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        animation: slideInUp 0.8s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Card Headers */
    .card-header-modern {
        padding: 20px 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .gold-header {
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    }

    .teal-header {
        background: linear-gradient(135deg, var(--teal) 0%, #5bc0de 100%);
    }

    .card-header-modern h5 {
        margin: 0;
        font-weight: 700;
        color: rgb(29, 27, 27);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.3rem;
    }

    /* Back Button */
    .btn-back-modern {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(94, 67, 67, 0.3);
        color: rgb(33, 31, 31);
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-back-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .btn-back-modern:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-3px);
        color: white;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-back-modern:hover::before {
        left: 100%;
    }

    /* Compile Button */
    .btn-compile-modern {
        background: linear-gradient(135deg, var(--success) 0%, #4cc9f0 100%);
        color: rgb(43, 40, 40);
        border: none;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .btn-compile-modern::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-compile-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(76, 201, 240, 0.4);
    }

    .btn-compile-modern:hover::before {
        width: 300px;
        height: 300px;
    }

    /* Card Body */
    .card-body-modern {
        padding: 25px;
    }

    /* Instruction Text */
    .instruction-modern {
        color: var(--danger);
        font-weight: 600;
        margin-bottom: 20px;
        padding: 12px 20px;
        background: rgba(249, 65, 68, 0.1);
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border-left: 4px solid var(--danger);
        /* animation: pulse 10s infinite; */
    }

    /* Exam List */
    .exam-list-modern {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .exam-item-modern {
        background: white;
        border-radius: 15px;
        padding: 5px;
        transition: all 0.3s ease;
        border: 1px solid rgba(52, 48, 48, 0.363);
        position: relative;
        overflow: hidden;
    }

    .exam-item-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(74, 93, 179, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .exam-item-modern:hover {
        transform: translateX(10px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .exam-item-modern:hover::before {
        left: 100%;
    }

    .exam-link-modern {
        text-decoration: none;
        color: var(--dark);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 20px;
        width: 100%;
    }

    .exam-link-modern i {
        color: var(--primary);
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .exam-item-modern:hover .exam-link-modern i {
        transform: translateX(5px);
        color: var(--secondary);
    }

    /* Table Styles */
    .table-wrapper {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        margin-top: 20px;
    }

    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }

    .table-modern thead th {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        font-weight: 600;
        padding: 18px 15px;
        font-size: 0.95rem;
        text-transform: capitalize;
        letter-spacing: 1px;
    }

    .table-modern tbody td {
        padding: 18px 15px;
        border-bottom: 1px solid #e9ecef;
        color: #495057;
        vertical-align: middle;
    }

    .table-modern tbody tr {
        transition: all 0.3s ease;
    }

    .table-modern tbody tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
        box-shadow: var(--shadow-sm);
    }

    /* Action Icons */
    .action-list-modern {
        display: flex;
        gap: 15px;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .action-icon {
        width: 30px;
        height: 30px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .action-icon.view {
        /* background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); */
        background: var(--primary);
    }

    .action-icon.download {
        /* background: linear-gradient(135deg, var(--success) 0%, #4cc9f0 100%); */
        background: var(--teal);
    }

    .action-icon.publish {
        /* background: linear-gradient(135deg, #28a745 0%, #20c997 100%); */
        background: var(--warning);
    }

    .action-icon.unpublish {
        /* background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); */
        background: var(--success);
    }

    .action-icon.delete {
        /* background: linear-gradient(135deg, var(--danger) 0%, #dc3545 100%); */
        background: var(--danger);
    }

    .action-icon:hover {
        transform: translateY(-5px) rotate(360deg);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    /* Modal Styles */
    .modal-modern .modal-content {
        border-radius: 30px;
        border: none;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
    }

    .modal-modern .modal-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        padding: 25px 30px;
    }

    .modal-modern .modal-body {
        padding: 30px;
    }

    .modal-modern .modal-footer {
        border: none;
        padding: 20px 30px;
        background: #f8f9fa;
    }

    /* Form Controls */
    .form-control-modern {
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 12px 18px;
        width: 100%;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control-modern:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
        outline: none;
    }

    .checkbox-container-modern {
        max-height: 250px;
        overflow-y: auto;
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 15px;
        background: white;
    }

    .checkbox-item-modern {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .checkbox-item-modern:hover {
        background: white;
        border-color: var(--primary);
        transform: translateX(5px);
        box-shadow: var(--shadow-sm);
    }

    /* Badges */
    .badge-modern {
        padding: 8px 15px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }

    .badge-info {
        background: linear-gradient(135deg, var(--accent) 0%, var(--success) 100%);
        color: white;
    }

    /* Empty State */
    .empty-state-modern {
        text-align: center;
        padding: 30px 10px;
        background: linear-gradient(135deg, #fff3cd 0%, #ffe69b 100%);
        border-radius: 20px;
        border: 2px dashed #ffc107;
    }

    .empty-state-modern i {
        font-size: 60px;
        color: #ffc107;
        margin-bottom: 10px;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    /* Loading Spinner */
    /* .loading-spinner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 70px;
        height: 70px;
        border: 5px solid #f3f3f3;
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 9999;
        display: none;
    } */

    /* Toast Notifications */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: 15px;
        padding: 15px 25px;
        box-shadow: var(--shadow-lg);
        display: flex;
        align-items: center;
        gap: 15px;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        z-index: 10000;
    }

    .toast-notification.show {
        transform: translateX(0);
    }

    .toast-success {
        border-left: 5px solid #28a745;
    }

    .toast-error {
        border-left: 5px solid var(--danger);
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .header-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 992px) {
        .action-list-modern {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            margin: 15px auto;
        }

        .header-modern {
            padding: 30px 20px;
        }

        .header-title {
            font-size: 1.6rem;
        }

        .card-header-modern {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .table-modern {
            display: block;
            overflow-x: auto;
        }

        .action-list-modern {
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .header-title {
            font-size: 1.3rem;
        }

        .btn-back-modern {
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .card-body-modern {
            padding: 15px;
        }
    }

    /* Dark Mode */
    @media (prefers-color-scheme: dark) {
        body {
            background: linear-gradient(135deg, #1a1c2c 0%, #2a2d4a 100%);
        }

        .glass-card {
            background: rgba(33, 37, 41, 0.95);
        }

        .exam-item-modern {
            background: #2b3035;
            border-color: #495057;
        }

        .exam-link-modern {
            color: #e9ecef;
        }

        .table-modern tbody td {
            color: #e9ecef;
            border-bottom-color: #495057;
        }

        .table-modern tbody tr:hover {
            background: #343a40;
        }

        .form-control-modern {
            background: #2b3035;
            border-color: #495057;
            color: #e9ecef;
        }

        .checkbox-container-modern {
            background: #2b3035;
            border-color: #495057;
        }

        .checkbox-item-modern {
            background: #343a40;
            color: #e9ecef;
        }
    }
</style>

<div class="animated-bg"></div>
<div class="particles"></div>
{{-- <div class="loading-spinner" id="loadingSpinner"></div> --}}

<div class="dashboard-container">
    <!-- Modern Header -->
    <div class="header-modern">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="header-title">
                    <i class="fas fa-chart-line me-3"></i>
                    <span>{{strtoupper($classes->class_code)}}</span>
                    <span style="color: var(--gold);">Academic Year {{$year}}</span>
                </h3>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Single Results Section -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="glass-card">
                <div class="card-header-modern gold-header">
                    <h5>
                        <i class="fas fa-file-alt"></i>
                        Examination Assessment
                    </h5>
                    <a href="{{route('results.classesByYear', ['school' => Hashids::encode($schools->id), 'year'=>$year])}}"
                       class="btn-back-modern">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </a>
                </div>
                <div class="card-body-modern">
                    <div class="instruction-modern">
                        <i class="fas fa-mouse-pointer"></i>
                        Select Examination Type
                    </div>

                    @if ($groupedByExamType->isEmpty())
                        <div class="empty-state-modern">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h6 class="mt-3">No Result Records Found</h6>
                            <p class="text-muted">Please add results first</p>
                        </div>
                    @else
                        <div class="exam-list-modern">
                            @foreach ($groupedByExamType as $exam_type_id => $results )
                                <div class="exam-item-modern">
                                    <a href="{{ route('results.monthsByExamType', [
                                        'school' => Hashids::encode($schools->id),
                                        'year' => $year,
                                        'class' => Hashids::encode($classes->id),
                                        'examType' => Hashids::encode($exam_type_id)
                                    ]) }}" class="exam-link-modern text-uppercase">
                                        <i class="fas fa-chevron-right"></i>
                                        {{ $results->first()->exam_type }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Compiled Results Section -->
        <div class="col-lg-8 col-md-6 mb-4">
            <div class="glass-card">
                <div class="card-header-modern teal-header">
                    <h5>
                        <i class="fas fa-file-pdf"></i>
                        Continuous Assessment
                    </h5>
                    <button type="button" class="btn-compile-modern" data-bs-toggle="modal" data-bs-target="#compileModal">
                        <i class="fas fa-cog"></i>
                        <span>Generate New</span>
                    </button>
                </div>
                <div class="card-body-modern">
                    <!-- Alerts -->
                    @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ Session::get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ Session::get('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Table -->
                    <div class="table-wrapper">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Report Title</th>
                                    <th>Issued Date</th>
                                    <th>Issued By</th>
                                    <th>Aggt Mode</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($reports->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state-modern">
                                                <i class="fas fa-inbox"></i>
                                                <h6 class="mt-3">No Compiled Reports Found</h6>
                                                <p>Click "Generate New" to create your first report</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($reports as $report)
                                        <tr>
                                            <td class="fw-bold">{{ ucwords(strtolower($report->title)) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y') }}</td>
                                            <td>{{ ucwords(strtolower($report->first_name. ' ' . $report->last_name[0])) }}.</td>
                                            <td>
                                                <span class="badge-modern badge-info">
                                                    {{ ucfirst($report->combine_option) }}
                                                </span>
                                            </td>
                                            <td>
                                                <ul class="action-list-modern">
                                                    <li>
                                                        <a href="{{route('students.combined.report', [
                                                            'school' => Hashids::encode($report->school_id),
                                                            'year' => $year,
                                                            'class' => Hashids::encode($report->class_id),
                                                            'report' => Hashids::encode($report->id)
                                                        ])}}"
                                                           class="action-icon view"
                                                           title="View Report">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('download.general.combined', [
                                                            'school' => Hashids::encode($report->school_id),
                                                            'year' => $year,
                                                            'class' => Hashids::encode($report->class_id),
                                                            'report' => Hashids::encode($report->id)
                                                        ])}}"
                                                           onclick="return confirm('Download this report?')"
                                                           class="action-icon download"
                                                           title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        @if ($report->status === 0)
                                                            <form action="{{route('publish.combined.report', [
                                                                'school' => Hashids::encode($report->school_id),
                                                                'year' => $year,
                                                                'class' => Hashids::encode($report->class_id),
                                                                'report' => Hashids::encode($report->id)
                                                            ])}}"
                                                                  method="POST"
                                                                  class="d-inline"
                                                                  onsubmit="return confirm('Publish this report?')">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="action-icon publish" title="Publish">
                                                                    <i class="fas fa-toggle-off"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{route('Unpublish.combined.report', [
                                                                'school' => Hashids::encode($report->school_id),
                                                                'year' => $year,
                                                                'class' => Hashids::encode($report->class_id),
                                                                'report' => Hashids::encode($report->id)
                                                            ])}}"
                                                                  method="POST"
                                                                  class="d-inline"
                                                                  onsubmit="return confirm('Unpublish this report?')">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="action-icon unpublish" title="Unpublish">
                                                                    <i class="fas fa-toggle-on"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </li>
                                                    <li>
                                                        <a href="{{route('generated.report.delete', [
                                                            'school' => Hashids::encode($report->school_id),
                                                            'year' => $year,
                                                            'class' => Hashids::encode($report->class_id),
                                                            'report' => Hashids::encode($report->id)
                                                        ])}}"
                                                           onclick="return confirm('Delete this report permanently?')"
                                                           class="action-icon delete"
                                                           title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($reports->hasPages())
                        <div class="pagination-container mt-4">
                            {{ $reports->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Modal -->
<div class="modal fade modal-modern" id="compileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf me-2"></i>
                    Generate New Report
                </h5>
                <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal"><i class="fas fa-close"></i></button>
            </div>
            <form class="needs-validation" novalidate
                  action="{{route('submit.compiled.results', [
                      'school' => Hashids::encode($schools->id),
                      'year' => $year,
                      'class' => Hashids::encode($classes->id)
                  ])}}"
                  method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Report Type</label>
                            <select name="exam_type" id="reportType" class="form-control-modern" required>
                                <option value="">Select Type</option>
                                <option value="mid-term">Mid-Term Assessment</option>
                                <option value="terminal">Terminal Assessment</option>
                                <option value="annual">Annual Assessment</option>
                                <option value="custom">Custom Report</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3" id="customInputContainer" style="display: none;">
                            <label class="form-label">Custom Title</label>
                            <input type="text" name="custom_exam_type" id="customReportType"
                                   class="form-control-modern" placeholder="Enter report title">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class</label>
                            <select name="class_id" class="form-control-modern" required>
                                <option value="{{$classes->id}}" selected>{{$classes->class_name}}</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Term</label>
                            <select name="term" class="form-control-modern" required>
                                <option value="">Select Term</option>
                                <option value="i">Term 1</option>
                                <option value="ii">Term 2</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="mb-3">
                                <i class="fas fa-database text-danger me-2"></i>
                                Results Data Set
                            </h6>
                            @if ($groupedByMonth->isEmpty())
                                <div class="empty-state-modern">
                                    <i class="fas fa-exclamation"></i>
                                    <p class="mt-2">No results found</p>
                                </div>
                            @else
                                <div class="checkbox-container-modern">
                                    @foreach ($groupedByMonth as $date => $resultDate)
                                        <div class="checkbox-item-modern">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="exam_dates[]"
                                                       value="{{$date}}"
                                                       id="date-{{Str::slug($date)}}">
                                                <label class="form-check-label" for="date-{{Str::slug($date)}}">
                                                    <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ ucwords(strtolower($resultDate->first()->exam_type)) }}
                                                        (Term {{ strtoupper($resultDate->first()->Exam_term) }})
                                                    </small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6 mb-3">
                            <h6 class="mb-3">
                                <i class="fas fa-chart-pie text-danger me-2"></i>
                                Display Mode
                            </h6>
                            <div class="checkbox-container-modern">
                                <div class="radio-option">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               name="combine_option" id="optionIndividual"
                                               value="individual" required>
                                        <label class="form-check-label" for="optionIndividual">
                                            <strong>Individual Scores</strong>
                                            <br>
                                            <small>Show each subject score separately</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="radio-option">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               name="combine_option" id="optionSum"
                                               value="sum" required>
                                        <label class="form-check-label" for="optionSum">
                                            <strong>Total Marks</strong>
                                            <br>
                                            <small>Show sum of all subjects</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="radio-option">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               name="combine_option" id="optionAverage"
                                               value="average" required>
                                        <label class="form-check-label" for="optionAverage">
                                            <strong>Average Score</strong>
                                            <br>
                                            <small>Show calculated average</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-compile-modern"
                            onclick="return confirm('Generate compiled results?')">
                        <i class="fas fa-cog me-2"></i>
                        Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Dynamic custom report input
document.getElementById('reportType').addEventListener('change', function() {
    const customContainer = document.getElementById('customInputContainer');
    const customInput = document.getElementById('customReportType');

    if (this.value === 'custom') {
        customContainer.style.display = 'block';
        customInput.setAttribute('required', 'required');
    } else {
        customContainer.style.display = 'none';
        customInput.removeAttribute('required');
    }
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.needs-validation');

    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});

// Show loading spinner on form submit
// document.querySelectorAll('form').forEach(form => {
//     form.addEventListener('submit', function() {
//         document.getElementById('loadingSpinner').style.display = 'block';
//     });
// });

// Animate particles
function createParticles() {
    const particlesContainer = document.querySelector('.particles');
    for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.width = Math.random() * 10 + 5 + 'px';
        particle.style.height = particle.style.width;
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 20 + 's';
        particle.style.animationDuration = Math.random() * 10 + 20 + 's';
        particlesContainer.appendChild(particle);
    }
}
createParticles();
</script>
@endsection
