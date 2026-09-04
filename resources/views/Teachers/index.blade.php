@extends('SRTDashboard.frame')

@section('content')

<style>
    /* =========================================================
       TEACHERS PAGE
       Scoped styles - will not interfere with other Blade views
       ========================================================= */

    .teachers-page {
        --tp-primary: #4361ee;
        --tp-primary-dark: #3a56d4;
        --tp-secondary: #3f37c9;
        --tp-success: #1cc88a;
        --tp-warning: #f6c23e;
        --tp-danger: #e74a3b;
        --tp-dark: #212529;
        --tp-muted: #64748b;
        --tp-border: #e2e8f0;
        --tp-light: #f8fafc;

        width: 100%;
        min-width: 0;
        position: relative;
        /* isolation: isolate; */
    }

    /* =========================================================
       BACKGROUND
       ========================================================= */

    .teachers-page .animated-bg {
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
        overflow: hidden;
        pointer-events: none;
        background:
            radial-gradient(
                circle at 75% 20%,
                rgba(67, 97, 238, 0.08),
                transparent 30%
            ),
            radial-gradient(
                circle at 20% 80%,
                rgba(63, 55, 201, 0.07),
                transparent 30%
            );
    }

    .teachers-page .animated-bg::before {
        content: "";
        position: absolute;
        inset: -50%;
        background:
            radial-gradient(
                circle at 70% 30%,
                rgba(67, 97, 238, 0.08),
                transparent 30%
            ),
            radial-gradient(
                circle at 30% 70%,
                rgba(63, 55, 201, 0.08),
                transparent 30%
            );
        animation: teachersRotate 60s linear infinite;
    }

    @keyframes teachersRotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* =========================================================
       MAIN CONTAINER
       ========================================================= */

    .teachers-page .dashboard-container {
        width: min(100%, 1600px);
        margin: 24px auto;
        padding-inline: 20px;
        position: relative;
        z-index: 1;
    }

    /* =========================================================
       MAIN CARD
       ========================================================= */

    .teachers-page .modern-card {
        width: 100%;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.65);
        border-radius: 24px;
        box-shadow:
            0 10px 30px rgba(15, 23, 42, 0.08),
            0 2px 8px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    /* =========================================================
       HEADER
       ========================================================= */

    .teachers-page .card-header-modern {
        position: relative;
        overflow: visible;
        padding: 20px 24px;
        background:
            linear-gradient(
                135deg,
                var(--tp-primary) 0%,
                var(--tp-secondary) 100%
            );
        color: #fff;
    }

    .teachers-page .card-header-modern::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(
                circle at 85% 20%,
                rgba(255,255,255,.18),
                transparent 25%
            );
    }

    .teachers-page .header-content {
        position: relative;
        z-index: 2;

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;
        min-width: 0;
    }

    .teachers-page .header-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .teachers-page .header-icon {
        width: 46px;
        height: 46px;
        min-width: 46px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 12px;
        background: rgba(255,255,255,.16);
        border: 1px solid rgba(255,255,255,.25);

        font-size: 20px;
    }

    .teachers-page .header-title {
        min-width: 0;
    }

    .teachers-page .header-title h3 {
        margin: 0;
        color: #fff;
        font-size: clamp(1.05rem, 2vw, 1.45rem);
        font-weight: 700;
        line-height: 1.2;
    }

    .teachers-page .header-title p {
        margin: 4px 0 0;
        font-size: .8rem;
        opacity: .85;
    }

    /* =========================================================
       ACTION BUTTONS
       ========================================================= */

    .teachers-page .action-group {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .teachers-page .btn-modern {
        min-height: 38px;
        padding: 8px 14px;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        border: 1px solid transparent;
        border-radius: 9px;

        color: #fff;
        font-size: .85rem;
        font-weight: 600;
        line-height: 1;

        text-decoration: none;
        white-space: nowrap;

        cursor: pointer;
        transition:
            background-color .2s ease,
            transform .2s ease,
            box-shadow .2s ease;
    }

    .teachers-page .btn-modern:hover {
        color: #fff;
        transform: translateY(-1px);
    }

    .teachers-page .btn-export {
        background: rgba(255,255,255,.14);
        border-color: rgba(255,255,255,.25);
    }

    .teachers-page .btn-export:hover {
        background: rgba(255,255,255,.22);
    }

    .teachers-page .btn-add {
        background: rgba(255,255,255,.22);
        border-color: rgba(255,255,255,.3);
    }

    .teachers-page .btn-add:hover {
        background: rgba(255,255,255,.32);
    }

    /* =========================================================
       DROPDOWN
       ========================================================= */

    .teachers-page .dropdown-modern {
        position: relative;
    }

    .teachers-page .dropdown-modern .dropdown-menu {
        min-width: 180px;
        margin-top: 7px !important;

        padding: 6px;
        border: 0;
        border-radius: 10px;

        background: #fff;
        box-shadow: 0 12px 30px rgba(15,23,42,.15);
        z-index: 1080;
    }

    .teachers-page .dropdown-modern .dropdown-item {
        display: flex;
        align-items: center;
        gap: 8px;

        padding: 9px 11px;
        border-radius: 7px;

        color: var(--tp-dark);
        font-size: .85rem;
    }

    .teachers-page .dropdown-modern .dropdown-item:hover {
        background: #f1f5f9;
    }

    /* =========================================================
       CARD BODY
       ========================================================= */

    .teachers-page .card-body-modern {
        padding: 24px;
    }

    /* =========================================================
       TABLE
       ========================================================= */

    .teachers-page .table-container-modern {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;

        border: 1px solid var(--tp-border);
        border-radius: 14px;
        background: #fff;

        box-shadow: 0 4px 14px rgba(15,23,42,.05);

        -webkit-overflow-scrolling: touch;
    }

    .teachers-page .table-modern {
        width: 100%;
        min-width: 900px;

        margin: 0;
        border-collapse: separate;
        border-spacing: 0;

        font-size: .875rem;
    }

    .teachers-page .table-modern thead th {
        padding: 12px 13px;

        background: linear-gradient(
            135deg,
            #2b3d5c 0%,
            #1a2a44 100%
        );

        color: #fff;
        border: 0;

        font-size: .74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .45px;

        white-space: nowrap;
        vertical-align: middle;
    }

    .teachers-page .table-modern tbody td {
        padding: 12px 13px;

        color: #475569;
        background: #fff;

        border-bottom: 1px solid #edf2f7;

        vertical-align: middle;
        white-space: nowrap;
    }

    .teachers-page .table-modern tbody tr:last-child td {
        border-bottom: 0;
    }

    .teachers-page .table-modern tbody tr {
        transition: background-color .18s ease;
    }

    .teachers-page .table-modern tbody tr:hover td {
        background: #f8fafc;
    }

    /* =========================================================
       TEACHER INFO
       ========================================================= */

    .teachers-page .teacher-info {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 180px;
    }

    .teachers-page .teacher-avatar-modern {
        width: 38px;
        height: 38px;
        min-width: 38px;

        object-fit: cover;
        border-radius: 9px;

        border: 2px solid #fff;
        box-shadow: 0 2px 7px rgba(15,23,42,.12);
    }

    .teachers-page .teacher-name {
        max-width: 190px;

        color: var(--tp-dark);
        font-size: .86rem;
        font-weight: 600;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* =========================================================
       BADGES
       ========================================================= */

    .teachers-page .member-id-badge,
    .teachers-page .role-badge,
    .teachers-page .year-badge,
    .teachers-page .status-badge {
        white-space: nowrap;
    }

    .teachers-page .member-id-badge {
        display: inline-block;

        padding: 4px 8px;
        border-radius: 6px;

        background: #edf2f7;
        color: #475569;

        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: .72rem;
        font-weight: 700;
    }

    .teachers-page .gender-badge {
        width: 28px;
        height: 28px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 7px;

        color: #fff;
        font-size: .76rem;
        font-weight: 700;
    }

    .teachers-page .gender-male {
        background: linear-gradient(135deg, #4e73df, #224abe);
    }

    .teachers-page .gender-female {
        background: linear-gradient(135deg, #e83e8c, #c2185b);
    }

    .teachers-page .role-badge {
        display: inline-block;

        padding: 4px 9px;
        border-radius: 999px;

        color: #fff;
        font-size: .71rem;
        font-weight: 600;
    }

    .teachers-page .role-admin {
        background: linear-gradient(135deg, #e74a3b, #be2617);
    }

    .teachers-page .role-teacher {
        background: linear-gradient(135deg, #1cc88a, #13855c);
    }

    .teachers-page .role-staff {
        background: linear-gradient(135deg, #36b9cc, #1a8a9e);
    }

    .teachers-page .role-other {
        background: linear-gradient(135deg, #6c757d, #5a6268);
    }

    .teachers-page .year-badge {
        display: inline-block;

        padding: 4px 8px;
        border-radius: 6px;

        background: linear-gradient(135deg, #36b9cc, #1a8a9e);
        color: #fff;

        font-size: .72rem;
        font-weight: 600;
    }

    .teachers-page .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        padding: 4px 8px;
        border-radius: 6px;

        font-size: .72rem;
        font-weight: 600;
    }

    .teachers-page .status-badge i {
        font-size: .5rem;
    }

    .teachers-page .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .teachers-page .status-blocked {
        background: #fee2e2;
        color: #991b1b;
    }

    /* =========================================================
       PHONE
       ========================================================= */

    .teachers-page .phone-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        color: #334155;
        text-decoration: none;

        font-size: .82rem;
    }

    .teachers-page .phone-link:hover {
        color: var(--tp-primary);
    }

    /* =========================================================
       ACTIONS
       ========================================================= */

    .teachers-page .action-icons {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 5px;

        flex-wrap: nowrap;
    }

    .teachers-page .action-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        border: 0;
        border-radius: 7px;

        color: #fff;
        text-decoration: none;

        font-size: .8rem;
        cursor: pointer;

        transition:
            transform .18s ease,
            box-shadow .18s ease;
    }

    .teachers-page .action-icon:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 9px rgba(15,23,42,.16);
    }

    .teachers-page .action-icon.view {
        background: linear-gradient(
            135deg,
            var(--tp-primary),
            var(--tp-secondary)
        );
    }

    .teachers-page .action-icon.warning {
        background: linear-gradient(135deg, #f6c23e, #f4b619);
    }

    .teachers-page .action-icon.success {
        background: linear-gradient(135deg, #1cc88a, #13855c);
    }

    .teachers-page .action-icon.danger {
        background: linear-gradient(135deg, #e74a3b, #be2617);
    }

    /* =========================================================
       EMPTY STATE
       ========================================================= */

    .teachers-page .empty-state-modern {
        padding: 45px 20px;

        text-align: center;

        background: linear-gradient(
            135deg,
            #fff8e1,
            #fff3cd
        );

        border: 2px dashed #f6c23e;
        border-radius: 16px;
    }

    .teachers-page .empty-state-modern i {
        margin-bottom: 12px;

        color: #f6c23e;
        font-size: 44px;
    }

    .teachers-page .empty-state-modern h6 {
        margin-bottom: 5px;
        font-weight: 700;
    }

    /* =========================================================
       MODAL
       ========================================================= */

    .teachers-page .modal-modern .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15,23,42,.2);
    }

    .teachers-page .modal-modern .modal-header {
        padding: 15px 20px;

        color: #fff;
        border: 0;

        background: linear-gradient(
            135deg,
            var(--tp-primary),
            var(--tp-secondary)
        );
    }

    .teachers-page .modal-modern .modal-title {
        font-size: 1rem;
        font-weight: 700;
    }

    .teachers-page .modal-modern .modal-body {
        padding: 20px;
    }

    .teachers-page .modal-modern .modal-footer {
        padding: 14px 20px;

        border: 0;
        background: #f8fafc;
    }

    /* =========================================================
       FORM
       ========================================================= */

    .teachers-page .form-group-modern {
        margin-bottom: 14px;
    }

    .teachers-page .form-label-modern {
        display: block;

        margin-bottom: 5px;

        color: #334155;
        font-size: .82rem;
        font-weight: 600;
    }

    .teachers-page .form-control-modern {
        width: 100%;
        min-height: 39px;

        padding: 8px 11px;

        border: 1px solid #dbe3ec;
        border-radius: 8px;

        background: #fff;
        color: #334155;

        font-size: .86rem;

        outline: none;

        transition:
            border-color .2s ease,
            box-shadow .2s ease;
    }

    .teachers-page .form-control-modern:focus {
        border-color: var(--tp-primary);
        box-shadow: 0 0 0 3px rgba(67,97,238,.10);
    }

    .teachers-page .text-danger {
        display: block;
        margin-top: 4px;
        font-size: .75rem;
    }

    /* =========================================================
       TABLET
       ========================================================= */

    @media (max-width: 992px) {

        .teachers-page .dashboard-container {
            margin: 18px auto;
            padding-inline: 15px;
        }

        .teachers-page .card-body-modern {
            padding: 18px;
        }

        .teachers-page .header-content {
            align-items: flex-start;
        }

        .teachers-page .action-group {
            justify-content: flex-end;
        }
    }

    /* =========================================================
       MOBILE
       ========================================================= */

    @media (max-width: 767.98px) {

        .teachers-page .dashboard-container {
            margin: 10px auto;
            padding-inline: 10px;
        }

        .teachers-page .modern-card {
            border-radius: 16px;
        }

        .teachers-page .card-header-modern {
            padding: 15px;
        }

        .teachers-page .header-content {
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
        }

        .teachers-page .header-left {
            width: 100%;
        }

        .teachers-page .header-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 10px;
            font-size: 17px;
        }

        .teachers-page .header-title h3 {
            font-size: 1rem;
        }

        .teachers-page .header-title p {
            font-size: .72rem;
        }

        .teachers-page .action-group {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .teachers-page .dropdown-modern {
            width: 100%;
        }

        .teachers-page .dropdown-modern .btn-modern,
        .teachers-page .btn-add {
            width: 100%;
        }

        .teachers-page .btn-modern {
            min-height: 40px;
            padding: 8px 10px;
            font-size: .78rem;
        }

        .teachers-page .card-body-modern {
            padding: 12px;
        }

        /*
         * MOBILE TABLE → CARD
         *
         * Instead of forcing users to horizontally scroll
         * through 9 columns, each row becomes a readable card.
         */

        .teachers-page .table-container-modern {
            overflow: visible;
            border: 0;
            box-shadow: none;
            background: transparent;
        }

        .teachers-page .table-modern {
            display: block;
            width: 100%;
            min-width: 0;
        }

        .teachers-page .table-modern thead {
            display: none;
        }

        .teachers-page .table-modern tbody {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .teachers-page .table-modern tbody tr {
            display: grid;

            grid-template-columns: 1fr auto;
            gap: 0;

            padding: 13px;

            border: 1px solid #e5e7eb;
            border-radius: 13px;

            background: #fff;

            box-shadow: 0 3px 10px rgba(15,23,42,.05);
        }

        .teachers-page .table-modern tbody tr:hover {
            background: #fff;
        }

        .teachers-page .table-modern tbody td {
            display: flex;
            align-items: center;

            min-width: 0;

            padding: 6px 0;

            border: 0;
            background: transparent;

            white-space: normal;
        }

        /*
         * First cell = row number
         */
        .teachers-page .table-modern tbody td:first-child {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
        }

        /*
         * Teacher row becomes card header
         */
        .teachers-page .table-modern tbody td:nth-child(3) {
            grid-column: 1 / -1;

            padding: 0 0 11px;
            margin-bottom: 6px;

            border-bottom: 1px solid #edf2f7;
        }

        .teachers-page .table-modern tbody td:nth-child(3)::before {
            display: none;
        }

        /*
         * All remaining cells get their labels
         */
        .teachers-page .table-modern tbody td:nth-child(2),
        .teachers-page .table-modern tbody td:nth-child(4),
        .teachers-page .table-modern tbody td:nth-child(5),
        .teachers-page .table-modern tbody td:nth-child(6),
        .teachers-page .table-modern tbody td:nth-child(7),
        .teachers-page .table-modern tbody td:nth-child(8) {

            grid-column: 1 / -1;

            justify-content: space-between;

            gap: 12px;
        }

        .teachers-page .table-modern tbody td:nth-child(2)::before {
            content: "Staff ID";
        }

        .teachers-page .table-modern tbody td:nth-child(4)::before {
            content: "Gender";
        }

        .teachers-page .table-modern tbody td:nth-child(5)::before {
            content: "Role";
        }

        .teachers-page .table-modern tbody td:nth-child(6)::before {
            content: "Phone";
        }

        .teachers-page .table-modern tbody td:nth-child(7)::before {
            content: "Joined";
        }

        .teachers-page .table-modern tbody td:nth-child(8)::before {
            content: "Status";
        }

        .teachers-page .table-modern tbody td:nth-child(2)::before,
        .teachers-page .table-modern tbody td:nth-child(4)::before,
        .teachers-page .table-modern tbody td:nth-child(5)::before,
        .teachers-page .table-modern tbody td:nth-child(6)::before,
        .teachers-page .table-modern tbody td:nth-child(7)::before,
        .teachers-page .table-modern tbody td:nth-child(8)::before {
            flex: 0 0 auto;

            color: #64748b;
            font-size: .72rem;
            font-weight: 600;
        }

        .teachers-page .teacher-info {
            width: 100%;
            min-width: 0;
        }

        .teachers-page .teacher-avatar-modern {
            width: 42px;
            height: 42px;
            min-width: 42px;
        }

        .teachers-page .teacher-name {
            max-width: none;
            font-size: .88rem;
        }

        .teachers-page .phone-link {
            font-size: .78rem;
            max-width: 60%;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /*
         * Actions occupy full card width at bottom
         */
        .teachers-page .table-modern tbody td:last-child {
            grid-column: 1 / -1;

            justify-content: flex-end;

            margin-top: 8px;
            padding-top: 11px;

            border-top: 1px solid #edf2f7;
        }

        .teachers-page .table-modern tbody td:last-child::before {
            content: "Actions";
            margin-right: auto;

            color: #64748b;
            font-size: .72rem;
            font-weight: 600;
        }

        .teachers-page .action-icons {
            justify-content: flex-end;
        }

        .teachers-page .action-icon {
            width: 32px;
            height: 32px;
            min-width: 32px;
        }

        /*
         * Modal
         */
        .teachers-page .modal-modern .modal-dialog {
            margin: 10px;
        }

        .teachers-page .modal-modern .modal-content {
            border-radius: 14px;
        }

        .teachers-page .modal-modern .modal-body {
            padding: 15px;
        }

        .teachers-page .modal-modern .modal-footer {
            padding: 12px 15px;
        }
    }

    /* =========================================================
       VERY SMALL DEVICES
       ========================================================= */

    @media (max-width: 380px) {

        .teachers-page .dashboard-container {
            padding-inline: 7px;
        }

        .teachers-page .card-header-modern {
            padding: 13px;
        }

        .teachers-page .card-body-modern {
            padding: 9px;
        }

        .teachers-page .action-group {
            grid-template-columns: 1fr;
        }

        .teachers-page .btn-modern {
            width: 100%;
        }

        .teachers-page .table-modern tbody tr {
            padding: 11px;
        }

        .teachers-page .teacher-avatar-modern {
            width: 38px;
            height: 38px;
            min-width: 38px;
        }

        .teachers-page .teacher-name {
            font-size: .82rem;
        }

        .teachers-page .phone-link {
            max-width: 55%;
        }
    }

    /* =========================================================
       REDUCED MOTION
       ========================================================= */

    @media (prefers-reduced-motion: reduce) {

        .teachers-page .animated-bg::before {
            animation: none;
        }

        .teachers-page *,
        .teachers-page *::before,
        .teachers-page *::after {
            scroll-behavior: auto !important;
            transition-duration: .01ms !important;
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
        }
    }
</style>


<div class="teachers-page">

    <div class="animated-bg"></div>

    <div class="dashboard-container">

        <div class="modern-card">

            {{-- =====================================================
                 HEADER
                 ===================================================== --}}
            <div class="card-header-modern">

                <div class="header-content">

                    <div class="header-left">

                        <div class="header-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>

                        <div class="header-title">
                            <h3>Teachers Management</h3>
                            <p>Manage teaching staff and their accounts</p>
                        </div>

                    </div>


                    <div class="action-group">

                        {{-- Export --}}
                        <div class="dropdown dropdown-modern">

                            <button
                                class="btn-modern btn-export dropdown-toggle"
                                type="button"
                                id="exportDropdown"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                <i class="fas fa-download"></i>
                                <span>Export</span>
                            </button>

                            <ul
                                class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="exportDropdown"
                            >
                                <li>
                                    <a
                                        class="dropdown-item"
                                        href="{{ route('teachers.excel.export') }}"
                                    >
                                        <i class="fas fa-file-excel text-success"></i>
                                        <span>Excel</span>
                                    </a>
                                </li>

                                <li>
                                    <a
                                        class="dropdown-item"
                                        href="{{ route('teachers.pdf.export') }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <i class="fas fa-file-pdf text-danger"></i>
                                        <span>PDF</span>
                                    </a>
                                </li>
                            </ul>

                        </div>


                        {{-- Add Teacher --}}
                        <button
                            type="button"
                            class="btn-modern btn-add"
                            data-bs-toggle="modal"
                            data-bs-target="#addTeacherModal"
                        >
                            <i class="fas fa-plus"></i>
                            <span>Add Teacher</span>
                        </button>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 BODY
                 ===================================================== --}}
            <div class="card-body-modern">

                @if ($teachers->isEmpty())

                    <div class="empty-state-modern">

                        <i class="fas fa-chalkboard-teacher"></i>

                        <h6>No Teachers Found</h6>

                        <p class="text-muted small mb-0">
                            Click "Add Teacher" to register your first teacher.
                        </p>

                    </div>

                @else

                    <div class="table-container-modern">

                        <table
                            class="table-modern"
                            id="myTable"
                        >

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Staff ID</th>
                                    <th>Teacher</th>
                                    <th>Gender</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>


                            <tbody>

                                @foreach ($teachers as $teacher)

                                    <tr>

                                        {{-- Number --}}
                                        <td>
                                            <span class="fw-bold">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>


                                        {{-- Staff ID --}}
                                        <td>
                                            <span class="member-id-badge">
                                                {{ strtoupper($teacher->member_id) }}
                                            </span>
                                        </td>


                                        {{-- Teacher --}}
                                        <td>

                                            <div class="teacher-info">

                                                @php
                                                    $imageName = $teacher->image;

                                                    $imagePath = storage_path(
                                                        'app/public/profile/' . $imageName
                                                    );

                                                    $avatarImage =
                                                        !empty($imageName) && file_exists($imagePath)
                                                            ? asset('storage/profile/' . $imageName)
                                                            : asset(
                                                                'storage/profile/' .
                                                                (
                                                                    $teacher->gender === 'male'
                                                                        ? 'avatar.jpg'
                                                                        : 'avatar-female.jpg'
                                                                )
                                                            );
                                                @endphp

                                                <img
                                                    src="{{ $avatarImage }}"
                                                    alt="{{ $teacher->first_name }} {{ $teacher->last_name }}"
                                                    class="teacher-avatar-modern"
                                                    loading="lazy"
                                                >

                                                <span class="teacher-name">
                                                    {{ ucwords(strtolower(
                                                        $teacher->first_name . ' ' . $teacher->last_name
                                                    )) }}
                                                </span>

                                            </div>

                                        </td>


                                        {{-- Gender --}}
                                        <td>

                                            <div
                                                class="gender-badge {{ $teacher->gender === 'male' ? 'gender-male' : 'gender-female' }}"
                                                title="{{ ucfirst($teacher->gender) }}"
                                            >
                                                {{ strtoupper(substr($teacher->gender, 0, 1)) }}
                                            </div>

                                        </td>


                                        {{-- Role --}}
                                        <td>

                                            @php
                                                $roleClass = match ($teacher->role_id) {
                                                    1 => 'role-admin',
                                                    2 => 'role-teacher',
                                                    3 => 'role-staff',
                                                    default => 'role-other',
                                                };
                                            @endphp

                                            <span class="role-badge {{ $roleClass }}">
                                                {{ ucwords(strtolower($teacher->role_name)) }}
                                            </span>

                                        </td>


                                        {{-- Phone --}}
                                        <td>

                                            <a
                                                href="tel:{{ $teacher->phone }}"
                                                class="phone-link"
                                            >
                                                <i class="fas fa-phone-alt"></i>
                                                <span>{{ $teacher->phone }}</span>
                                            </a>

                                        </td>


                                        {{-- Joined --}}
                                        <td>

                                            <span class="year-badge">
                                                {{ $teacher->joined }}
                                            </span>

                                        </td>


                                        {{-- Status --}}
                                        <td>

                                            @if ($teacher->status == 1)

                                                <span class="status-badge status-active">
                                                    <i class="fas fa-circle"></i>
                                                    Active
                                                </span>

                                            @else

                                                <span class="status-badge status-blocked">
                                                    <i class="fas fa-circle"></i>
                                                    Blocked
                                                </span>

                                            @endif

                                        </td>


                                        {{-- Actions --}}
                                        <td>

                                            <div class="action-icons">

                                                {{-- View --}}
                                                <a
                                                    href="{{ route(
                                                        'teacher.profile',
                                                        ['teacher' => Hashids::encode($teacher->id)]
                                                    ) }}"
                                                    class="action-icon view"
                                                    title="View Profile"
                                                    aria-label="View Profile"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </a>


                                                {{-- Block / Unblock --}}
                                                @if ($teacher->status == 1)

                                                    <form
                                                        action="{{ route(
                                                            'update.teacher.status',
                                                            ['teacher' => Hashids::encode($teacher->id)]
                                                        ) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Block {{ $teacher->first_name }}?')"
                                                    >
                                                        @csrf
                                                        @method('PUT')

                                                        <button
                                                            type="submit"
                                                            class="action-icon warning"
                                                            title="Block Teacher"
                                                            aria-label="Block Teacher"
                                                        >
                                                            <i class="fas fa-ban"></i>
                                                        </button>

                                                    </form>

                                                @else

                                                    <form
                                                        action="{{ route(
                                                            'teachers.restore',
                                                            ['teacher' => Hashids::encode($teacher->id)]
                                                        ) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Unblock {{ $teacher->first_name }}?')"
                                                    >
                                                        @csrf
                                                        @method('PUT')

                                                        <button
                                                            type="submit"
                                                            class="action-icon success"
                                                            title="Unblock Teacher"
                                                            aria-label="Unblock Teacher"
                                                        >
                                                            <i class="fas fa-check"></i>
                                                        </button>

                                                    </form>

                                                @endif


                                                {{-- Delete --}}
                                                <form
                                                    action="{{ route(
                                                        'Teachers.remove',
                                                        ['teacher' => Hashids::encode($teacher->id)]
                                                    ) }}"
                                                    method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Delete {{ $teacher->first_name }}? This cannot be undone.')"
                                                >
                                                    @csrf
                                                    @method('PUT')

                                                    <button
                                                        type="submit"
                                                        class="action-icon danger"
                                                        title="Delete Teacher"
                                                        aria-label="Delete Teacher"
                                                    >
                                                        <i class="fas fa-trash"></i>
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- =========================================================
         ADD TEACHER MODAL
         ========================================================= --}}
    <div
        class="modal fade modal-modern"
        id="addTeacherModal"
        tabindex="-1"
        aria-labelledby="addTeacherModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="addTeacherModalLabel"
                    >
                        Register New Teacher
                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>


                <form
                    class="needs-validation"
                    novalidate
                    action="{{ route('Teachers.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    @csrf

                    <div class="modal-body">

                        <div class="row g-2">

                            {{-- First Name --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        First Name
                                    </label>

                                    <input
                                        type="text"
                                        name="fname"
                                        class="form-control-modern"
                                        value="{{ old('fname') }}"
                                        required
                                    >

                                    @error('fname')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror

                                </div>

                            </div>


                            {{-- Other Names --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        Other Names
                                    </label>

                                    <input
                                        type="text"
                                        name="lname"
                                        class="form-control-modern"
                                        value="{{ old('lname') }}"
                                        required
                                    >

                                    @error('lname')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror

                                </div>

                            </div>


                            {{-- Email --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        Email
                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control-modern"
                                        value="{{ old('email') }}"
                                    >

                                    @error('email')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror

                                </div>

                            </div>


                            {{-- Gender --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        Gender
                                    </label>

                                    <select
                                        name="gender"
                                        class="form-control-modern"
                                        required
                                    >
                                        <option value="">Select</option>
                                        <option value="male" @selected(old('gender') === 'male')>
                                            Male
                                        </option>
                                        <option value="female" @selected(old('gender') === 'female')>
                                            Female
                                        </option>
                                    </select>

                                    @error('gender')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror

                                </div>

                            </div>


                            {{-- Phone --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        Phone
                                    </label>

                                    <input
                                        type="tel"
                                        name="phone"
                                        class="form-control-modern"
                                        value="{{ old('phone') }}"
                                        autocomplete="tel"
                                        required
                                    >

                                    @error('phone')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror

                                </div>

                            </div>


                            {{-- Qualification --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        Qualification
                                    </label>

                                    <select
                                        name="qualification"
                                        class="form-control-modern"
                                        required
                                    >
                                        <option value="">Select</option>
                                        <option value="1" @selected(old('qualification') == 1)>
                                            Masters
                                        </option>
                                        <option value="2" @selected(old('qualification') == 2)>
                                            Degree
                                        </option>
                                        <option value="3" @selected(old('qualification') == 3)>
                                            Diploma
                                        </option>
                                        <option value="4" @selected(old('qualification') == 4)>
                                            Certificate
                                        </option>
                                    </select>

                                    @error('qualification')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror

                                </div>

                            </div>


                            {{-- Date of Birth --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        Date of Birth
                                    </label>

                                    <input
                                        type="date"
                                        name="dob"
                                        class="form-control-modern"
                                        value="{{ old('dob') }}"
                                        required
                                    >

                                    @error('dob')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror

                                </div>

                            </div>


                            {{-- Year Joined --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        Year Joined
                                    </label>

                                    <select
                                        name="joined"
                                        class="form-control-modern"
                                        required
                                    >
                                        <option value="">Select</option>

                                        @for ($year = date('Y'); $year >= 2010; $year--)

                                            <option
                                                value="{{ $year }}"
                                                @selected(old('joined') == $year)
                                            >
                                                {{ $year }}
                                            </option>

                                        @endfor

                                    </select>

                                    @error('joined')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror

                                </div>

                            </div>


                            {{-- Street --}}
                            <div class="col-12">

                                <div class="form-group-modern mb-0">

                                    <label class="form-label-modern">
                                        Street / Village
                                    </label>

                                    <input
                                        type="text"
                                        name="street"
                                        class="form-control-modern"
                                        value="{{ old('street') }}"
                                        required
                                    >

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn btn-success"
                            id="saveButton"
                        >
                            Save Teacher
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const form = document.querySelector(
            '#addTeacherModal .needs-validation'
        );

        const submitButton = document.getElementById('saveButton');

        if (!form || !submitButton) {
            return;
        }

        form.addEventListener('submit', function (event) {

            if (!form.checkValidity()) {

                event.preventDefault();
                event.stopPropagation();

                form.classList.add('was-validated');

                return;
            }

            /*
             * Allow native form submission.
             * Prevent duplicate submissions while the request is processing.
             */
            submitButton.disabled = true;

            submitButton.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm me-1"
                    role="status"
                    aria-hidden="true"
                ></span>
                Saving...
            `;

        });

    });
</script>

@endsection