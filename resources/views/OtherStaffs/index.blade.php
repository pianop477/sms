@extends('SRTDashboard.frame')

@section('content')

<style>
    /* =========================================================
       STAFF MANAGEMENT PAGE
       All styles are scoped to .staff-page
       ========================================================= */

    .staff-page {
        --sp-primary: #4361ee;
        --sp-primary-dark: #3a56d4;
        --sp-secondary: #3f37c9;
        --sp-success: #1cc88a;
        --sp-warning: #f6c23e;
        --sp-danger: #e74a3b;
        --sp-dark: #212529;
        --sp-muted: #64748b;
        --sp-border: #e2e8f0;
        --sp-light: #f8fafc;

        width: 100%;
        min-width: 0;
        position: relative;
        /* isolation: isolate; */
    }


    /* =========================================================
       BACKGROUND
       ========================================================= */

    .staff-page .animated-bg {
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100%;

        /* z-index: -2; */
        pointer-events: none;
        overflow: hidden;

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

    .staff-page .animated-bg::before {
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

        animation: staffRotate 60s linear infinite;
    }

    @keyframes staffRotate {

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

    .staff-page .dashboard-container {
        width: min(100%, 1600px);

        margin: 24px auto;
        padding-inline: 20px;

        position: relative;
        /* z-index: 1; */
    }


    /* =========================================================
       MAIN CARD
       ========================================================= */

    .staff-page .modern-card {
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

    .staff-page .card-header-modern {
        position: relative;
        overflow: visible;

        padding: 20px 24px;

        background:
            linear-gradient(
                135deg,
                var(--sp-primary) 0%,
                var(--sp-secondary) 100%
            );

        color: #fff;
    }

    .staff-page .card-header-modern::before {
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

    .staff-page .header-content {
        position: relative;
        /* z-index: 2; */

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        min-width: 0;
    }

    .staff-page .header-left {
        display: flex;
        align-items: center;

        gap: 12px;

        min-width: 0;
    }

    .staff-page .header-icon {
        width: 46px;
        height: 46px;
        min-width: 46px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 12px;

        background: rgba(255,255,255,.16);
        border: 1px solid rgba(255,255,255,.25);

        color: #fff;

        font-size: 20px;
    }

    .staff-page .header-title {
        min-width: 0;
    }

    .staff-page .header-title h3 {
        margin: 0;

        color: #fff;

        font-size: clamp(1.05rem, 2vw, 1.45rem);
        font-weight: 700;

        line-height: 1.2;
    }

    .staff-page .header-title p {
        margin: 4px 0 0;

        font-size: .8rem;

        opacity: .85;
    }


    /* =========================================================
       ACTION BUTTONS
       ========================================================= */

    .staff-page .action-group {
        display: flex;
        align-items: center;
        justify-content: flex-end;

        gap: 8px;

        flex-wrap: wrap;

        flex-shrink: 0;
    }

    .staff-page .btn-modern {
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

    .staff-page .btn-modern:hover {
        color: #fff;
        transform: translateY(-1px);
    }

    .staff-page .btn-export {
        background: rgba(255,255,255,.14);
        border-color: rgba(255,255,255,.25);
    }

    .staff-page .btn-export:hover {
        background: rgba(255,255,255,.22);
    }

    .staff-page .btn-add {
        background: rgba(255,255,255,.22);
        border-color: rgba(255,255,255,.30);
    }

    .staff-page .btn-add:hover {
        background: rgba(255,255,255,.32);
    }


    /* =========================================================
       DROPDOWN
       ========================================================= */

    .staff-page .dropdown-modern {
        position: relative;
    }

    .staff-page .dropdown-modern .dropdown-menu {
        min-width: 180px;

        margin-top: 7px !important;

        padding: 6px;

        border: 0;
        border-radius: 10px;

        background: #fff;

        box-shadow:
            0 12px 30px rgba(15,23,42,.15);

        /* z-index: 1080; */
    }

    .staff-page .dropdown-modern .dropdown-item {
        display: flex;
        align-items: center;

        gap: 8px;

        padding: 9px 11px;

        border-radius: 7px;

        color: var(--sp-dark);

        font-size: .85rem;
    }

    .staff-page .dropdown-modern .dropdown-item:hover {
        background: #f1f5f9;
    }


    /* =========================================================
       STATS CARD
       ========================================================= */

    .staff-page .stats-card {
        width: 100%;

        margin-bottom: 20px;
        padding: 15px 18px;

        border-radius: 16px;

        color: #fff;

        background:
            linear-gradient(
                135deg,
                var(--sp-primary) 0%,
                var(--sp-secondary) 100%
            );

        box-shadow:
            0 6px 18px rgba(67,97,238,.15);
    }

    .staff-page .stats-card .row {
        align-items: center;
    }

    .staff-page .stat-item {
        display: flex;
        align-items: center;

        gap: 12px;

        min-height: 48px;
    }

    .staff-page .stat-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 10px;

        background: rgba(255,255,255,.16);

        font-size: 18px;
    }

    .staff-page .stat-label {
        margin-bottom: 2px;

        color: rgba(255,255,255,.85);

        font-size: .76rem;
        font-weight: 500;
    }

    .staff-page .stat-value {
        color: #fff;

        font-size: 1.35rem;
        font-weight: 700;

        line-height: 1.2;
    }


    /* =========================================================
       CARD BODY
       ========================================================= */

    .staff-page .card-body-modern {
        padding: 24px;
    }


    /* =========================================================
       TABLE CONTAINER
       ========================================================= */

    .staff-page .table-container-modern {
        width: 100%;

        overflow-x: auto;
        overflow-y: hidden;

        background: #fff;

        border: 1px solid var(--sp-border);
        border-radius: 14px;

        box-shadow:
            0 4px 14px rgba(15,23,42,.05);

        -webkit-overflow-scrolling: touch;
    }


    /* =========================================================
       TABLE
       ========================================================= */

    .staff-page .table-modern {
        width: 100%;
        min-width: 900px;

        margin: 0;

        border-collapse: separate;
        border-spacing: 0;

        font-size: .875rem;
    }

    .staff-page .table-modern thead th {
        padding: 12px 13px;

        background:
            linear-gradient(
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

    .staff-page .table-modern tbody td {
        padding: 12px 13px;

        color: #475569;

        background: #fff;

        border-bottom: 1px solid #edf2f7;

        vertical-align: middle;

        white-space: nowrap;
    }

    .staff-page .table-modern tbody tr:last-child td {
        border-bottom: 0;
    }

    .staff-page .table-modern tbody tr {
        transition: background-color .18s ease;
    }

    .staff-page .table-modern tbody tr:hover td {
        background: #f8fafc;
    }


    /* =========================================================
       STAFF INFO
       ========================================================= */

    .staff-page .staff-info {
        display: flex;
        align-items: center;

        gap: 9px;

        min-width: 190px;
    }

    .staff-page .staff-avatar-modern {
        width: 38px;
        height: 38px;
        min-width: 38px;

        object-fit: cover;

        border-radius: 9px;

        border: 2px solid #fff;

        box-shadow:
            0 2px 7px rgba(15,23,42,.12);
    }

    .staff-page .staff-name {
        max-width: 190px;

        color: var(--sp-dark);

        font-size: .86rem;
        font-weight: 600;

        overflow: hidden;

        text-overflow: ellipsis;
        white-space: nowrap;
    }


    /* =========================================================
       BADGES
       ========================================================= */

    .staff-page .staff-id-badge {
        display: inline-block;

        padding: 4px 8px;

        border-radius: 6px;

        background: #edf2f7;
        color: #475569;

        font-family:
            ui-monospace,
            SFMono-Regular,
            Menlo,
            Monaco,
            Consolas,
            monospace;

        font-size: .72rem;
        font-weight: 700;

        white-space: nowrap;
    }

    .staff-page .gender-badge {
        width: 28px;
        height: 28px;
        min-width: 28px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 7px;

        color: #fff;

        font-size: .76rem;
        font-weight: 700;
    }

    .staff-page .gender-male {
        background:
            linear-gradient(
                135deg,
                #4e73df,
                #224abe
            );
    }

    .staff-page .gender-female {
        background:
            linear-gradient(
                135deg,
                #e83e8c,
                #c2185b
            );
    }

    .staff-page .job-badge {
        display: inline-block;

        padding: 4px 9px;

        border-radius: 999px;

        background: #f1f5f9;
        color: #475569;

        font-size: .72rem;
        font-weight: 600;

        white-space: nowrap;
    }

    .staff-page .year-badge {
        display: inline-block;

        padding: 4px 8px;

        border-radius: 6px;

        background: #f1f5f9;
        color: #475569;

        font-size: .72rem;
        font-weight: 600;

        white-space: nowrap;
    }


    /* =========================================================
       PHONE
       ========================================================= */

    .staff-page .phone-link {
        display: inline-flex;
        align-items: center;

        gap: 5px;

        color: #334155;

        text-decoration: none;

        font-size: .82rem;

        white-space: nowrap;
    }

    .staff-page .phone-link:hover {
        color: var(--sp-primary);
    }


    /* =========================================================
       STATUS
       ========================================================= */

    .staff-page .status-badge {
        display: inline-flex;
        align-items: center;

        gap: 5px;

        padding: 4px 8px;

        border-radius: 6px;

        font-size: .72rem;
        font-weight: 600;

        white-space: nowrap;
    }

    .staff-page .status-badge i {
        font-size: .5rem;
    }

    .staff-page .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .staff-page .status-blocked {
        background: #fee2e2;
        color: #991b1b;
    }


    /* =========================================================
       ACTIONS
       ========================================================= */

    .staff-page .action-icons {
        display: flex;
        align-items: center;
        justify-content: flex-end;

        gap: 5px;

        flex-wrap: nowrap;
    }

    .staff-page .action-icon {
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

    .staff-page .action-icon:hover {
        color: #fff;

        transform: translateY(-1px);

        box-shadow:
            0 4px 9px rgba(15,23,42,.16);
    }

    .staff-page .action-icon.view {
        background:
            linear-gradient(
                135deg,
                var(--sp-primary),
                var(--sp-secondary)
            );
    }

    .staff-page .action-icon.warning {
        background:
            linear-gradient(
                135deg,
                #f6c23e,
                #f4b619
            );
    }

    .staff-page .action-icon.success {
        background:
            linear-gradient(
                135deg,
                #1cc88a,
                #13855c
            );
    }


    /* =========================================================
       EMPTY STATE
       ========================================================= */

    .staff-page .empty-state-modern {
        padding: 45px 20px;

        text-align: center;

        background:
            linear-gradient(
                135deg,
                #fff8e1,
                #fff3cd
            );

        border: 2px dashed #f6c23e;

        border-radius: 16px;
    }

    .staff-page .empty-state-modern i {
        margin-bottom: 12px;

        color: #f6c23e;

        font-size: 44px;
    }

    .staff-page .empty-state-modern h6 {
        margin-bottom: 5px;

        font-weight: 700;
    }


    /* =========================================================
       MODAL
       ========================================================= */

    .staff-page .modal-modern .modal-content {
        border: 0;

        border-radius: 18px;

        overflow: hidden;

        box-shadow:
            0 20px 50px rgba(15,23,42,.2);
    }

    .staff-page .modal-modern .modal-header {
        padding: 15px 20px;

        color: #fff;

        border: 0;

        background:
            linear-gradient(
                135deg,
                var(--sp-primary),
                var(--sp-secondary)
            );
    }

    .staff-page .modal-modern .modal-title {
        font-size: 1rem;
        font-weight: 700;
    }

    .staff-page .modal-modern .modal-body {
        padding: 20px;
    }

    .staff-page .modal-modern .modal-footer {
        padding: 14px 20px;

        border: 0;

        background: #f8fafc;
    }


    /* =========================================================
       FORM
       ========================================================= */

    .staff-page .form-group-modern {
        margin-bottom: 14px;
    }

    .staff-page .form-label-modern {
        display: block;

        margin-bottom: 5px;

        color: #334155;

        font-size: .82rem;
        font-weight: 600;
    }

    .staff-page .form-label-modern .required {
        color: var(--sp-danger);
    }

    .staff-page .form-control-modern {
        width: 100%;

        min-height: 40px;

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

    .staff-page .form-control-modern:focus {
        border-color: var(--sp-primary);

        box-shadow:
            0 0 0 3px rgba(67,97,238,.10);

        outline: none;
    }

    .staff-page .text-danger {
        display: block;

        margin-top: 4px;

        font-size: .75rem;
    }


    /* =========================================================
       TABLET
       ========================================================= */

    @media (max-width: 992px) {

        .staff-page .dashboard-container {
            margin: 18px auto;
            padding-inline: 15px;
        }

        .staff-page .card-body-modern {
            padding: 18px;
        }

        .staff-page .header-content {
            align-items: flex-start;
        }

        .staff-page .action-group {
            justify-content: flex-end;
        }

    }


    /* =========================================================
       MOBILE
       ========================================================= */

    @media (max-width: 767.98px) {

        .staff-page .dashboard-container {
            margin: 10px auto;
            padding-inline: 10px;
        }

        .staff-page .modern-card {
            border-radius: 16px;
        }

        .staff-page .card-header-modern {
            padding: 15px;
        }

        .staff-page .header-content {
            flex-direction: column;
            align-items: stretch;

            gap: 14px;
        }

        .staff-page .header-left {
            width: 100%;
        }

        .staff-page .header-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;

            border-radius: 10px;

            font-size: 17px;
        }

        .staff-page .header-title h3 {
            font-size: 1rem;
        }

        .staff-page .header-title p {
            font-size: .72rem;
        }


        /* -------------------------
           HEADER BUTTONS
           ------------------------- */

        .staff-page .action-group {
            width: 100%;

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 8px;
        }

        .staff-page .dropdown-modern {
            width: 100%;
        }

        .staff-page .dropdown-modern .btn-modern,
        .staff-page .btn-add {
            width: 100%;
        }

        .staff-page .btn-modern {
            min-height: 40px;

            padding: 8px 10px;

            font-size: .78rem;
        }


        /* -------------------------
           BODY
           ------------------------- */

        .staff-page .card-body-modern {
            padding: 12px;
        }


        /* -------------------------
           STATS
           ------------------------- */

        .staff-page .stats-card {
            margin-bottom: 12px;

            padding: 10px 12px;

            border-radius: 11px;
        }

        .staff-page .stats-card .row {
            flex-direction: column;

            gap: 4px;
        }

        .staff-page .stats-card [class*="col-"] {
            width: 100%;

            flex: 1 1 100%;

            padding-inline: 4px;
        }

        .staff-page .stat-item {
            min-height: 36px;

            gap: 8px;

            padding: 2px 0;
        }

        .staff-page .stat-icon {
            width: 32px;
            height: 32px;
            min-width: 32px;

            border-radius: 7px;

            font-size: 14px;
        }

        .staff-page .stat-value {
            font-size: 1rem;
        }

        .staff-page .stat-label {
            font-size: .68rem;
        }


        /* =====================================================
           IMPORTANT:
           TABLE → MOBILE CARDS
           ===================================================== */

        .staff-page .table-container-modern {
            overflow: visible;

            border: 0;

            background: transparent;

            box-shadow: none;
        }

        .staff-page .table-modern {
            display: block;

            width: 100%;

            min-width: 0;
        }

        .staff-page .table-modern thead {
            display: none;
        }

        .staff-page .table-modern tbody {
            display: flex;

            flex-direction: column;

            gap: 10px;
        }

        .staff-page .table-modern tbody tr {
            display: grid;

            grid-template-columns: 1fr auto;

            gap: 0;

            padding: 13px;

            border: 1px solid #e5e7eb;

            border-radius: 13px;

            background: #fff;

            box-shadow:
                0 3px 10px rgba(15,23,42,.05);
        }

        .staff-page .table-modern tbody tr:hover td {
            background: transparent;
        }

        .staff-page .table-modern tbody td {
            display: flex;
            align-items: center;

            min-width: 0;

            padding: 6px 0;

            border: 0;

            background: transparent;

            white-space: normal;
        }


        /* -------------------------
           Hide row number
           ------------------------- */

        .staff-page .table-modern tbody td:first-child {
            position: absolute;

            width: 1px;
            height: 1px;

            overflow: hidden;

            clip: rect(0, 0, 0, 0);
        }


        /* -------------------------
           Staff header
           ------------------------- */

        .staff-page .table-modern tbody td:nth-child(3) {
            grid-column: 1 / -1;

            padding: 0 0 11px;

            margin-bottom: 6px;

            border-bottom: 1px solid #edf2f7;
        }

        .staff-page .table-modern tbody td:nth-child(3)::before {
            display: none;
        }


        /* -------------------------
           Data rows
           ------------------------- */

        .staff-page .table-modern tbody td:nth-child(2),
        .staff-page .table-modern tbody td:nth-child(4),
        .staff-page .table-modern tbody td:nth-child(5),
        .staff-page .table-modern tbody td:nth-child(6),
        .staff-page .table-modern tbody td:nth-child(7),
        .staff-page .table-modern tbody td:nth-child(8) {

            grid-column: 1 / -1;

            justify-content: space-between;

            gap: 12px;
        }


        /* -------------------------
           Mobile labels
           ------------------------- */

        .staff-page .table-modern tbody td:nth-child(2)::before {
            content: "Staff ID";
        }

        .staff-page .table-modern tbody td:nth-child(4)::before {
            content: "Gender";
        }

        .staff-page .table-modern tbody td:nth-child(5)::before {
            content: "Job Title";
        }

        .staff-page .table-modern tbody td:nth-child(6)::before {
            content: "Phone";
        }

        .staff-page .table-modern tbody td:nth-child(7)::before {
            content: "Joined";
        }

        .staff-page .table-modern tbody td:nth-child(8)::before {
            content: "Status";
        }

        .staff-page .table-modern tbody td:nth-child(2)::before,
        .staff-page .table-modern tbody td:nth-child(4)::before,
        .staff-page .table-modern tbody td:nth-child(5)::before,
        .staff-page .table-modern tbody td:nth-child(6)::before,
        .staff-page .table-modern tbody td:nth-child(7)::before,
        .staff-page .table-modern tbody td:nth-child(8)::before {

            flex: 0 0 auto;

            color: #64748b;

            font-size: .72rem;
            font-weight: 600;
        }


        /* -------------------------
           Staff profile
           ------------------------- */

        .staff-page .staff-info {
            width: 100%;

            min-width: 0;
        }

        .staff-page .staff-avatar-modern {
            width: 42px;
            height: 42px;
            min-width: 42px;
        }

        .staff-page .staff-name {
            max-width: none;

            font-size: .88rem;
        }


        /* -------------------------
           Phone
           ------------------------- */

        .staff-page .phone-link {
            max-width: 60%;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

            font-size: .78rem;
        }


        /* -------------------------
           Actions
           ------------------------- */

        .staff-page .table-modern tbody td:last-child {
            grid-column: 1 / -1;

            justify-content: flex-end;

            margin-top: 8px;

            padding-top: 11px;

            border-top: 1px solid #edf2f7;
        }

        .staff-page .table-modern tbody td:last-child::before {
            content: "Actions";

            margin-right: auto;

            color: #64748b;

            font-size: .72rem;
            font-weight: 600;
        }

        .staff-page .action-icons {
            justify-content: flex-end;
        }

        .staff-page .action-icon {
            width: 34px;
            height: 34px;

            min-width: 34px;

            font-size: .8rem;
        }


        /* -------------------------
           Modal
           ------------------------- */

        .staff-page .modal-modern .modal-dialog {
            margin: 10px;
        }

        .staff-page .modal-modern .modal-content {
            border-radius: 14px;
        }

        .staff-page .modal-modern .modal-body {
            padding: 15px;
        }

        .staff-page .modal-modern .modal-footer {
            padding: 12px 15px;
        }

    }


    /* =========================================================
       SMALL PHONES
       ========================================================= */

    @media (max-width: 480px) {

        .staff-page .dashboard-container {
            margin: 7px auto;

            padding-inline: 7px;
        }

        .staff-page .card-header-modern {
            padding: 13px;
        }

        .staff-page .card-body-modern {
            padding: 9px;
        }


        /* Header */

        .staff-page .action-group {
            grid-template-columns: 1fr;
        }


        /* Stats */

        .staff-page .stats-card {
            padding: 9px 10px;

            border-radius: 9px;

            margin-bottom: 9px;
        }

        .staff-page .stat-item {
            gap: 7px;
        }

        .staff-page .stat-icon {
            width: 29px;
            height: 29px;
            min-width: 29px;

            font-size: 12px;
        }

        .staff-page .stat-value {
            font-size: .92rem;
        }

        .staff-page .stat-label {
            font-size: .62rem;
        }


        /* Cards */

        .staff-page .table-modern tbody tr {
            padding: 11px;

            border-radius: 12px;
        }

        .staff-page .staff-avatar-modern {
            width: 38px;
            height: 38px;

            min-width: 38px;
        }

        .staff-page .staff-name {
            font-size: .82rem;
        }

        .staff-page .phone-link {
            max-width: 55%;
        }


        /* Modal */

        .staff-page .modal-modern .modal-dialog {
            margin: 6px;
        }

        .staff-page .modal-modern .modal-content {
            border-radius: 11px;
        }

        .staff-page .modal-modern .modal-header {
            padding: 12px;
        }

        .staff-page .modal-modern .modal-body {
            padding: 12px;
        }

        .staff-page .modal-modern .modal-footer {
            padding: 10px 12px;

            flex-direction: column;
        }

        .staff-page .modal-modern .modal-footer .btn {
            width: 100%;
        }

        .staff-page .form-group-modern {
            margin-bottom: 10px;
        }

        .staff-page .form-control-modern {
            min-height: 44px;

            font-size: 16px;
        }

        .staff-page .empty-state-modern {
            padding: 25px 14px;
        }

    }


    /* =========================================================
       VERY SMALL PHONES
       ========================================================= */

    @media (max-width: 360px) {

        .staff-page .dashboard-container {
            margin: 5px auto;

            padding-inline: 5px;
        }

        .staff-page .card-header-modern {
            padding: 10px;
        }

        .staff-page .card-body-modern {
            padding: 7px;
        }

        .staff-page .header-icon {
            width: 36px;
            height: 36px;
            min-width: 36px;

            font-size: 15px;
        }

        .staff-page .header-title h3 {
            font-size: .92rem;
        }

        .staff-page .header-title p {
            font-size: .65rem;
        }

        .staff-page .stats-card {
            padding: 7px 8px;
        }

        .staff-page .stat-icon {
            width: 26px;
            height: 26px;
            min-width: 26px;
        }

        .staff-page .stat-value {
            font-size: .82rem;
        }

        .staff-page .stat-label {
            font-size: .55rem;
        }

        .staff-page .table-modern tbody tr {
            padding: 9px;
        }

        .staff-page .staff-avatar-modern {
            width: 34px;
            height: 34px;
            min-width: 34px;
        }

        .staff-page .staff-name {
            font-size: .76rem;
        }

        .staff-page .action-icon {
            width: 30px;
            height: 30px;
            min-width: 30px;

            font-size: .7rem;
        }

    }


    /* =========================================================
       REDUCED MOTION
       ========================================================= */

    @media (prefers-reduced-motion: reduce) {

        .staff-page .animated-bg::before {
            animation: none;
        }

        .staff-page *,
        .staff-page *::before,
        .staff-page *::after {

            transition-duration: .01ms !important;

            animation-duration: .01ms !important;

            animation-iteration-count: 1 !important;

            scroll-behavior: auto !important;
        }

    }
</style>


<div class="staff-page">

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
                            <i class="fas fa-users-cog"></i>
                        </div>

                        <div class="header-title">

                            <h3>
                                Staff Management
                            </h3>

                            <p>
                                Manage non-teaching staff and their accounts
                            </p>

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

                                <span>
                                    Export
                                </span>

                            </button>


                            <ul
                                class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="exportDropdown"
                            >

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="{{ route(
                                            'export.other.staffs',
                                            ['format' => 'excel']
                                        ) }}"
                                    >

                                        <i class="fas fa-file-excel text-success"></i>

                                        <span>
                                            Excel
                                        </span>

                                    </a>

                                </li>


                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="{{ route(
                                            'export.other.staffs',
                                            ['format' => 'pdf']
                                        ) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >

                                        <i class="fas fa-file-pdf text-danger"></i>

                                        <span>
                                            PDF
                                        </span>

                                    </a>

                                </li>

                            </ul>

                        </div>


                        {{-- Register --}}
                        <button
                            type="button"
                            class="btn-modern btn-add"
                            data-bs-toggle="modal"
                            data-bs-target="#addStaffModal"
                        >

                            <i class="fas fa-user-plus"></i>

                            <span>
                                Register Staff
                            </span>

                        </button>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 BODY
                 ===================================================== --}}

            <div class="card-body-modern">


                {{-- =================================================
                     STATISTICS
                     ================================================= --}}

                <div class="stats-card">

                    <div class="row">


                        {{-- Total --}}
                        <div class="col-md-4">

                            <div class="stat-item">

                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>

                                <div>

                                    <div class="stat-label">
                                        Total Staff
                                    </div>

                                    <div class="stat-value">
                                        {{
                                            $combinedStaffs
                                                ->filter(fn($s) => $s->status === 1)
                                                ->count()
                                        }}
                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Male --}}
                        <div class="col-md-4">

                            <div class="stat-item">

                                <div class="stat-icon">
                                    <i class="fas fa-male"></i>
                                </div>

                                <div>

                                    <div class="stat-label">
                                        Male
                                    </div>

                                    <div class="stat-value">
                                        {{
                                            $combinedStaffs
                                                ->filter(
                                                    fn($s) =>
                                                        strtolower($s->gender) === 'male'
                                                        && $s->status == 1
                                                )
                                                ->count()
                                        }}
                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Female --}}
                        <div class="col-md-4">

                            <div class="stat-item">

                                <div class="stat-icon">
                                    <i class="fas fa-female"></i>
                                </div>

                                <div>

                                    <div class="stat-label">
                                        Female
                                    </div>

                                    <div class="stat-value">
                                        {{
                                            $combinedStaffs
                                                ->filter(
                                                    fn($s) =>
                                                        strtolower($s->gender) === 'female'
                                                        && $s->status == 1
                                                )
                                                ->count()
                                        }}
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     EMPTY STATE
                     ================================================= --}}

                @if ($combinedStaffs->isEmpty())

                    <div class="empty-state-modern">

                        <i class="fas fa-users-cog"></i>

                        <h6>
                            No Staff Found
                        </h6>

                        <p class="text-muted small mb-0">
                            Click "Register Staff" to add your first staff member.
                        </p>

                    </div>


                @else


                    {{-- =================================================
                         STAFF TABLE
                         ================================================= --}}

                    <div class="table-container-modern">

                        <table
                            class="table-modern"
                            id="myTable"
                        >

                            <thead>

                                <tr>

                                    <th>#</th>

                                    <th>
                                        Staff ID
                                    </th>

                                    <th>
                                        Staff
                                    </th>

                                    <th>
                                        Gender
                                    </th>

                                    <th>
                                        Job Title
                                    </th>

                                    <th>
                                        Phone
                                    </th>

                                    <th>
                                        Joined
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th class="text-end">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach ($combinedStaffs as $row)

                                    <tr>


                                        {{-- Number --}}
                                        <td>

                                            <span class="fw-bold">
                                                {{ $loop->iteration }}
                                            </span>

                                        </td>


                                        {{-- Staff ID --}}
                                        <td>

                                            <span class="staff-id-badge">
                                                {{
                                                    strtoupper(
                                                        $row->staff_id ?? 'N/A'
                                                    )
                                                }}
                                            </span>

                                        </td>


                                        {{-- Staff --}}
                                        <td>

                                            <div class="staff-info">


                                                @php

                                                    $imageName =
                                                        $row->profile_image;

                                                    $imagePath =
                                                        storage_path(
                                                            'app/public/profile/' .
                                                            $imageName
                                                        );

                                                    $avatarImage =
                                                        !empty($imageName)
                                                        && file_exists($imagePath)

                                                            ? asset(
                                                                'storage/profile/' .
                                                                $imageName
                                                            )

                                                            : asset(
                                                                'storage/profile/' .
                                                                (
                                                                    $row->gender === 'male'
                                                                        ? 'avatar.jpg'
                                                                        : 'avatar-female.jpg'
                                                                )
                                                            );

                                                @endphp


                                                <img
                                                    src="{{ $avatarImage }}"
                                                    alt="Staff profile"
                                                    class="staff-avatar-modern"
                                                    loading="lazy"
                                                >


                                                @if (isset($row->driver_name))

                                                    <span class="staff-name">
                                                        {{
                                                            ucwords(
                                                                strtolower(
                                                                    $row->driver_name
                                                                )
                                                            )
                                                        }}
                                                    </span>

                                                @else

                                                    <span class="staff-name">
                                                        {{
                                                            ucwords(
                                                                strtolower(
                                                                    $row->first_name .
                                                                    ' ' .
                                                                    $row->last_name
                                                                )
                                                            )
                                                        }}
                                                    </span>

                                                @endif

                                            </div>

                                        </td>


                                        {{-- Gender --}}
                                        <td>

                                            <div
                                                class="gender-badge {{
                                                    $row->gender === 'male'
                                                        ? 'gender-male'
                                                        : 'gender-female'
                                                }}"
                                                title="{{ ucfirst($row->gender) }}"
                                            >

                                                {{
                                                    strtoupper(
                                                        substr(
                                                            $row->gender,
                                                            0,
                                                            1
                                                        )
                                                    )
                                                }}

                                            </div>

                                        </td>


                                        {{-- Job --}}
                                        <td>

                                            <span class="job-badge">
                                                {{ $row->job_title ?? 'N/A' }}
                                            </span>

                                        </td>


                                        {{-- Phone --}}
                                        <td>

                                            <a
                                                href="tel:{{ $row->phone }}"
                                                class="phone-link"
                                            >

                                                <i class="fas fa-phone-alt"></i>

                                                <span>
                                                    {{ $row->phone }}
                                                </span>

                                            </a>

                                        </td>


                                        {{-- Joined --}}
                                        <td>

                                            <span class="year-badge">
                                                {{
                                                    $row->joining_year ?? 'N/A'
                                                }}
                                            </span>

                                        </td>


                                        {{-- Status --}}
                                        <td>

                                            @if ($row->status == 1)

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
                                                        'OtherStaffs.profile',
                                                        [
                                                            'type' => $row->job_title,
                                                            'id' => Hashids::encode($row->id)
                                                        ]
                                                    ) }}"
                                                    class="action-icon view"
                                                    title="View Profile"
                                                    aria-label="View Profile"
                                                >

                                                    <i class="fas fa-eye"></i>

                                                </a>


                                                {{-- Block / Unblock --}}
                                                @if ($row->status == 1)

                                                    <form
                                                        action="{{ route(
                                                            'block.other.staffs',
                                                            [
                                                                'type' => $row->job_title,
                                                                'id' => Hashids::encode($row->id)
                                                            ]
                                                        ) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Block this staff member?')"
                                                    >

                                                        @csrf

                                                        @method('PUT')

                                                        <button
                                                            type="submit"
                                                            class="action-icon warning"
                                                            title="Block Staff"
                                                            aria-label="Block Staff"
                                                        >

                                                            <i class="fas fa-ban"></i>

                                                        </button>

                                                    </form>


                                                @else

                                                    <form
                                                        action="{{ route(
                                                            'unblock.other.staffs',
                                                            [
                                                                'type' => $row->job_title,
                                                                'id' => Hashids::encode($row->id)
                                                            ]
                                                        ) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Unblock this staff member?')"
                                                    >

                                                        @csrf

                                                        @method('PUT')

                                                        <button
                                                            type="submit"
                                                            class="action-icon success"
                                                            title="Unblock Staff"
                                                            aria-label="Unblock Staff"
                                                        >

                                                            <i class="fas fa-check"></i>

                                                        </button>

                                                    </form>

                                                @endif

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
         REGISTER STAFF MODAL
         ========================================================= --}}

    <div
        class="modal fade modal-modern"
        id="addStaffModal"
        tabindex="-1"
        aria-labelledby="addStaffModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">


                {{-- Header --}}
                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="addStaffModalLabel"
                    >
                        Staff Registration Form
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
                    action="{{ route('OtherStaffs.store') }}"
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

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <input
                                        type="hidden"
                                        name="school"
                                        value="{{ Hashids::encode($user->school_id) }}"
                                    >


                                    <input
                                        type="text"
                                        name="fname"
                                        class="form-control-modern"
                                        value="{{ old('fname') }}"
                                        autocomplete="given-name"
                                        required
                                    >


                                    @error('fname')

                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>

                                    @enderror


                                    @error('school')

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

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <input
                                        type="text"
                                        name="lname"
                                        class="form-control-modern"
                                        value="{{ old('lname') }}"
                                        autocomplete="family-name"
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
                                        autocomplete="email"
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

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        name="gender"
                                        class="form-control-modern"
                                        required
                                    >

                                        <option value="">
                                            Select
                                        </option>

                                        <option
                                            value="male"
                                            @selected(old('gender') === 'male')
                                        >
                                            Male
                                        </option>

                                        <option
                                            value="female"
                                            @selected(old('gender') === 'female')
                                        >
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

                                        <span class="required">
                                            *
                                        </span>

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


                            {{-- Education --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">

                                        Education Level

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        name="education"
                                        class="form-control-modern"
                                        required
                                    >

                                        <option value="">
                                            Select
                                        </option>

                                        <option value="university">
                                            University
                                        </option>

                                        <option value="college">
                                            College
                                        </option>

                                        <option value="high_school">
                                            High school
                                        </option>

                                        <option value="secondary">
                                            Secondary school
                                        </option>

                                        <option value="primary">
                                            Primary school
                                        </option>

                                        <option value="other">
                                            Other
                                        </option>

                                    </select>


                                    @error('education')

                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>

                                    @enderror

                                </div>

                            </div>


                            {{-- DOB --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">

                                        Date of Birth

                                        <span class="required">
                                            *
                                        </span>

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


                            {{-- Joined --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">

                                        Year Joined

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        name="joined"
                                        class="form-control-modern"
                                        required
                                    >

                                        <option value="">
                                            Select
                                        </option>

                                        @for (
                                            $year = date('Y');
                                            $year >= 2010;
                                            $year--
                                        )

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
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">

                                        Street / Village

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <input
                                        type="text"
                                        name="street"
                                        class="form-control-modern"
                                        value="{{ old('street') }}"
                                        autocomplete="street-address"
                                        required
                                    >


                                    @error('street')

                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>

                                    @enderror

                                </div>

                            </div>


                            {{-- Job Title --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">

                                        Job Title

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        name="job_title"
                                        class="form-control-modern"
                                        required
                                    >

                                        <option value="">
                                            Select
                                        </option>

                                        <option value="cooks">
                                            Cooks
                                        </option>

                                        <option value="matron">
                                            Matron
                                        </option>

                                        <option value="patron">
                                            Patron
                                        </option>

                                        <option value="cleaner">
                                            Cleaner
                                        </option>

                                        <option value="security guard">
                                            Security Guard
                                        </option>

                                        <option value="other">
                                            Other
                                        </option>

                                    </select>


                                    @error('job_title')

                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>

                                    @enderror

                                </div>

                            </div>


                            {{-- NIN --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        NIN (NIDA)
                                    </label>


                                    <input
                                        type="text"
                                        name="nida"
                                        class="form-control-modern"
                                        id="nin"
                                        value="{{ old('nida') }}"
                                        maxlength="23"
                                        inputmode="numeric"
                                        autocomplete="off"
                                        placeholder="Enter NIDA"
                                    >


                                    @error('nida')

                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>

                                    @enderror

                                </div>

                            </div>


                            {{-- Profile --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">
                                        Profile Picture
                                    </label>


                                    <input
                                        type="file"
                                        name="image"
                                        class="form-control-modern"
                                        accept="image/*"
                                    >


                                    @error('image')

                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>

                                    @enderror

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Footer --}}
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
                            Save Staff
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
         * =====================================================
         * STAFF REGISTRATION FORM
         * =====================================================
         */

        const form = document.querySelector(
            '#addStaffModal .needs-validation'
        );

        const submitButton = document.getElementById(
            'saveButton'
        );


        if (form && submitButton) {

            form.addEventListener('submit', function (event) {

                if (!form.checkValidity()) {

                    event.preventDefault();
                    event.stopPropagation();

                    form.classList.add('was-validated');

                    return;
                }


                /*
                 * Prevent duplicate submissions.
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

        }


        /*
         * =====================================================
         * NIN FORMATTER
         *
         * Format:
         * 12345678-12345-12345-12
         * =====================================================
         */

        const ninInput = document.getElementById('nin');


        if (ninInput) {

            ninInput.addEventListener('input', function (event) {

                let value = event.target.value
                    .replace(/\D/g, '')
                    .substring(0, 20);


                let formatted = '';


                if (value.length > 0) {
                    formatted += value.substring(0, 8);
                }


                if (value.length >= 8) {
                    formatted += '-';
                }


                if (value.length > 8) {
                    formatted += value.substring(8, 13);
                }


                if (value.length >= 13) {
                    formatted += '-';
                }


                if (value.length > 13) {
                    formatted += value.substring(13, 18);
                }


                if (value.length >= 18) {
                    formatted += '-';
                }


                if (value.length > 18) {
                    formatted += value.substring(18, 20);
                }


                event.target.value = formatted;

            });

        }

    });
</script>

@endsection