@extends('SRTDashboard.frame')

@section('content')

<style>
    /* =========================================================
       PARENTS MANAGEMENT
       Scoped styles — does not affect other dashboard views
       ========================================================= */

    .parents-page {
        --pm-primary: #4361ee;
        --pm-primary-dark: #3a56d4;
        --pm-secondary: #3f37c9;
        --pm-success: #1cc88a;
        --pm-warning: #f6c23e;
        --pm-danger: #e74a3b;
        --pm-dark: #212529;
        --pm-muted: #64748b;
        --pm-border: #e2e8f0;
        --pm-light: #f8fafc;

        width: 100%;
        min-width: 0;
        position: relative;
        isolation: isolate;
    }


    /* =========================================================
       BACKGROUND
       ========================================================= */

    .parents-page .animated-bg {
        position: fixed;
        inset: 0;

        width: 100%;
        height: 100%;

        z-index: -2;
        pointer-events: none;
        overflow: hidden;

        background:
            radial-gradient(
                circle at 75% 20%,
                rgba(67, 97, 238, .08),
                transparent 30%
            ),
            radial-gradient(
                circle at 20% 80%,
                rgba(63, 55, 201, .07),
                transparent 30%
            );
    }

    .parents-page .animated-bg::before {
        content: "";

        position: absolute;
        inset: -50%;

        background:
            radial-gradient(
                circle at 70% 30%,
                rgba(67, 97, 238, .08),
                transparent 30%
            ),
            radial-gradient(
                circle at 30% 70%,
                rgba(63, 55, 201, .08),
                transparent 30%
            );

        animation: pmRotate 60s linear infinite;
    }

    @keyframes pmRotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }


    /* =========================================================
       PARTICLES
       ========================================================= */

    .parents-page .particles {
        position: fixed;
        inset: 0;

        width: 100%;
        height: 100%;

        z-index: -1;

        pointer-events: none;
        overflow: hidden;
    }


    /* =========================================================
       CONTAINER
       ========================================================= */

    .parents-page .dashboard-container {
        width: min(100%, 1600px);

        margin: 24px auto;

        padding-inline: 20px;

        position: relative;
        z-index: 1;
    }


    /* =========================================================
       MAIN CARD
       ========================================================= */

    .parents-page .modern-card {
        width: 100%;

        background: rgba(255, 255, 255, .96);

        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);

        border: 1px solid rgba(255, 255, 255, .65);

        border-radius: 24px;

        box-shadow:
            0 10px 30px rgba(15, 23, 42, .08),
            0 2px 8px rgba(15, 23, 42, .04);

        overflow: hidden;
    }


    /* =========================================================
       HEADER
       ========================================================= */

    .parents-page .card-header-modern {
        position: relative;
        overflow: visible;

        padding: 20px 24px;

        color: #fff;

        background:
            linear-gradient(
                135deg,
                var(--pm-primary) 0%,
                var(--pm-secondary) 100%
            );
    }

    .parents-page .card-header-modern::before {
        content: "";

        position: absolute;
        inset: 0;

        pointer-events: none;

        background:
            radial-gradient(
                circle at 85% 20%,
                rgba(255, 255, 255, .18),
                transparent 25%
            );
    }

    .parents-page .header-content {
        position: relative;
        z-index: 2;

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        min-width: 0;
    }

    .parents-page .header-left {
        display: flex;
        align-items: center;

        gap: 12px;

        min-width: 0;
    }

    .parents-page .header-icon {
        width: 46px;
        height: 46px;
        min-width: 46px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 12px;

        background: rgba(255, 255, 255, .16);
        border: 1px solid rgba(255, 255, 255, .25);

        color: #fff;

        font-size: 20px;
    }

    .parents-page .header-title {
        min-width: 0;
    }

    .parents-page .header-title h3 {
        margin: 0;

        color: #fff;

        font-size: clamp(1.05rem, 2vw, 1.45rem);
        font-weight: 700;

        line-height: 1.2;
    }

    .parents-page .header-title p {
        margin: 4px 0 0;

        color: rgba(255, 255, 255, .85);

        font-size: .8rem;
    }


    /* =========================================================
       ACTION BUTTONS
       ========================================================= */

    .parents-page .action-group {
        display: flex;
        align-items: center;
        justify-content: flex-end;

        gap: 8px;

        flex-wrap: wrap;

        flex-shrink: 0;
    }

    .parents-page .btn-modern {
        min-height: 38px;

        padding: 8px 14px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        gap: 7px;

        border: 1px solid transparent;
        border-radius: 9px;

        color: #fff;

        font-size: .84rem;
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

    .parents-page .btn-modern:hover {
        color: #fff;

        transform: translateY(-1px);
    }

    .parents-page .btn-import {
        background: rgba(255, 255, 255, .14);
        border-color: rgba(255, 255, 255, .25);
    }

    .parents-page .btn-import:hover {
        background: rgba(255, 255, 255, .22);
    }

    .parents-page .btn-add {
        background: rgba(255, 255, 255, .22);
        border-color: rgba(255, 255, 255, .30);
    }

    .parents-page .btn-add:hover {
        background: rgba(255, 255, 255, .32);
    }


    /* =========================================================
       BODY
       ========================================================= */

    .parents-page .card-body-modern {
        padding: 24px;
    }


    /* =========================================================
       TABLE CONTAINER
       ========================================================= */

    .parents-page .table-container-modern {
        width: 100%;

        overflow-x: auto;
        overflow-y: hidden;

        background: #fff;

        border: 1px solid var(--pm-border);
        border-radius: 14px;

        box-shadow:
            0 4px 14px rgba(15, 23, 42, .05);

        -webkit-overflow-scrolling: touch;
    }


    /* =========================================================
       TABLE
       ========================================================= */

    .parents-page .table-modern {
        width: 100%;

        min-width: 850px;

        margin: 0;

        border-collapse: separate;
        border-spacing: 0;

        font-size: .875rem;
    }

    .parents-page .table-modern thead th {
        padding: 12px 13px;

        background:
            linear-gradient(
                135deg,
                #2b3d5c 0%,
                #1a2a44 100%
            );

        color: #fff;

        border: 0;

        font-size: .73rem;
        font-weight: 700;

        text-transform: uppercase;
        letter-spacing: .45px;

        white-space: nowrap;

        vertical-align: middle;
    }

    .parents-page .table-modern tbody td {
        padding: 12px 13px;

        color: #475569;

        background: #fff;

        border-bottom: 1px solid #edf2f7;

        vertical-align: middle;

        white-space: nowrap;
    }

    .parents-page .table-modern tbody tr:last-child td {
        border-bottom: 0;
    }

    .parents-page .table-modern tbody tr {
        transition: background-color .18s ease;
    }

    .parents-page .table-modern tbody tr:hover td {
        background: #f8fafc;
    }


    /* =========================================================
       PARENT INFO
       ========================================================= */

    .parents-page .parent-info {
        display: flex;
        align-items: center;

        gap: 9px;

        min-width: 190px;
    }

    .parents-page .parent-avatar {
        width: 38px;
        height: 38px;
        min-width: 38px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 9px;

        color: #fff;

        font-size: .8rem;
        font-weight: 700;

        background:
            linear-gradient(
                135deg,
                var(--pm-primary),
                var(--pm-secondary)
            );

        box-shadow:
            0 2px 7px rgba(15, 23, 42, .12);
    }

    .parents-page .parent-name {
        max-width: 200px;

        color: var(--pm-dark);

        font-size: .85rem;
        font-weight: 600;

        overflow: hidden;

        text-overflow: ellipsis;
        white-space: nowrap;
    }


    /* =========================================================
       GENDER
       ========================================================= */

    .parents-page .gender-badge {
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

    .parents-page .gender-male {
        background:
            linear-gradient(
                135deg,
                #4e73df,
                #224abe
            );
    }

    .parents-page .gender-female {
        background:
            linear-gradient(
                135deg,
                #e83e8c,
                #c2185b
            );
    }


    /* =========================================================
       PHONE
       ========================================================= */

    .parents-page .phone-link {
        display: inline-flex;
        align-items: center;

        gap: 5px;

        color: #334155;

        text-decoration: none;

        font-size: .82rem;

        white-space: nowrap;
    }

    .parents-page .phone-link:hover {
        color: var(--pm-primary);
    }


    /* =========================================================
       EMAIL
       ========================================================= */

    .parents-page .email-text {
        display: block;

        max-width: 170px;

        overflow: hidden;

        text-overflow: ellipsis;

        white-space: nowrap;

        color: #475569;

        font-size: .8rem;
    }


    /* =========================================================
       STATUS
       ========================================================= */

    .parents-page .status-badge {
        display: inline-flex;
        align-items: center;

        gap: 5px;

        padding: 4px 8px;

        border-radius: 6px;

        font-size: .72rem;
        font-weight: 600;

        white-space: nowrap;
    }

    .parents-page .status-badge i {
        font-size: .5rem;
    }

    .parents-page .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .parents-page .status-blocked {
        background: #fee2e2;
        color: #991b1b;
    }


    /* =========================================================
       ACTIONS
       ========================================================= */

    .parents-page .action-icons {
        display: flex;
        align-items: center;
        justify-content: flex-end;

        gap: 5px;

        flex-wrap: nowrap;
    }

    .parents-page .action-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        border: 0;
        border-radius: 7px;

        color: #fff;

        font-size: .78rem;

        text-decoration: none;

        cursor: pointer;

        transition:
            transform .18s ease,
            box-shadow .18s ease;
    }

    .parents-page .action-icon:hover {
        color: #fff;

        transform: translateY(-1px);

        box-shadow:
            0 4px 9px rgba(15, 23, 42, .16);
    }

    .parents-page .action-icon.view {
        background:
            linear-gradient(
                135deg,
                var(--pm-primary),
                var(--pm-secondary)
            );
    }

    .parents-page .action-icon.warning {
        background:
            linear-gradient(
                135deg,
                #f6c23e,
                #f4b619
            );
    }

    .parents-page .action-icon.success {
        background:
            linear-gradient(
                135deg,
                #1cc88a,
                #13855c
            );
    }

    .parents-page .action-icon.danger {
        background:
            linear-gradient(
                135deg,
                #e74a3b,
                #be2617
            );
    }


    /* =========================================================
       EMPTY STATE
       ========================================================= */

    .parents-page .empty-state-modern {
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

    .parents-page .empty-state-modern i {
        margin-bottom: 12px;

        color: #f6c23e;

        font-size: 44px;
    }

    .parents-page .empty-state-modern h6 {
        margin-bottom: 5px;

        color: #334155;

        font-weight: 700;
    }


    /* =========================================================
       MODALS
       ========================================================= */

    .parents-page .modal-modern .modal-content {
        border: 0;

        border-radius: 18px;

        overflow: hidden;

        box-shadow:
            0 20px 50px rgba(15, 23, 42, .20);
    }

    .parents-page .modal-modern .modal-header {
        padding: 15px 20px;

        color: #fff;

        border: 0;

        background:
            linear-gradient(
                135deg,
                var(--pm-primary),
                var(--pm-secondary)
            );
    }

    .parents-page .modal-modern .modal-title {
        font-size: 1rem;
        font-weight: 700;
    }

    .parents-page .modal-modern .modal-body {
        padding: 20px;

        max-height: 72vh;

        overflow-y: auto;
    }

    .parents-page .modal-modern .modal-footer {
        padding: 14px 20px;

        border: 0;

        background: #f8fafc;
    }


    /* =========================================================
       FORM SECTIONS
       ========================================================= */

    .parents-page .form-section-modern {
        margin-bottom: 18px;

        padding: 15px;

        background: #f8fafc;

        border: 1px solid #e9eef5;

        border-radius: 12px;
    }

    .parents-page .form-section-modern:last-child {
        margin-bottom: 0;
    }

    .parents-page .section-title-modern {
        display: flex;
        align-items: center;

        gap: 8px;

        margin-bottom: 15px;
        padding-bottom: 8px;

        color: var(--pm-primary);

        border-bottom: 1px solid #dee5ee;

        font-size: .95rem;
        font-weight: 700;
    }


    /* =========================================================
       FORM CONTROLS
       ========================================================= */

    .parents-page .form-group-modern {
        margin-bottom: 13px;
    }

    .parents-page .form-label-modern {
        display: block;

        margin-bottom: 5px;

        color: #334155;

        font-size: .8rem;
        font-weight: 600;
    }

    .parents-page .form-label-modern .required {
        color: var(--pm-danger);
    }

    .parents-page .form-control-modern {
        width: 100%;

        min-height: 40px;

        padding: 8px 11px;

        border: 1px solid #dbe3ec;

        border-radius: 8px;

        background: #fff;

        color: #334155;

        font-size: .85rem;

        outline: none;

        transition:
            border-color .2s ease,
            box-shadow .2s ease;
    }

    .parents-page .form-control-modern:focus {
        border-color: var(--pm-primary);

        box-shadow:
            0 0 0 3px rgba(67, 97, 238, .10);

        outline: none;
    }

    .parents-page .note-text {
        margin-top: 3px;

        color: #64748b;

        font-size: .7rem;
    }

    .parents-page .text-danger {
        display: block;

        margin-top: 4px;

        font-size: .73rem;
    }


    /* =========================================================
       IMPORT FILE INPUT
       ========================================================= */

    .parents-page .file-input-modern {
        padding: 22px;

        text-align: center;

        border: 2px dashed var(--pm-primary);

        border-radius: 12px;

        background: #fff;

        transition:
            background-color .2s ease,
            border-color .2s ease;
    }

    .parents-page .file-input-modern:hover,
    .parents-page .file-input-modern.drag-over {
        background: rgba(67, 97, 238, .05);

        border-color: var(--pm-primary-dark);
    }

    .parents-page .file-input-modern input[type="file"] {
        display: none;
    }

    .parents-page .file-input-label {
        display: flex;
        flex-direction: column;
        align-items: center;

        gap: 8px;

        cursor: pointer;
    }

    .parents-page .file-input-label i {
        font-size: 42px;
    }


    /* =========================================================
       IMPORT PREVIEW
       ========================================================= */

    .parents-page .import-preview-container {
        width: 100%;

        max-height: 400px;

        margin-top: 15px;

        overflow: auto;

        border: 1px solid #e9ecef;

        border-radius: 10px;

        -webkit-overflow-scrolling: touch;
    }

    .parents-page .import-preview-container table {
        width: max-content;

        min-width: 100%;

        margin: 0;
    }

    .parents-page .import-preview-container thead th {
        position: sticky;
        top: 0;

        z-index: 10;

        padding: 10px;

        background:
            linear-gradient(
                135deg,
                var(--pm-primary),
                var(--pm-secondary)
            );

        color: #fff;

        font-size: .72rem;

        white-space: nowrap;
    }

    .parents-page .import-preview-container td {
        padding: 8px 10px;

        font-size: .76rem;

        white-space: nowrap;
    }


    /* =========================================================
       STATS
       ========================================================= */

    .parents-page .stats-row {
        display: grid;

        grid-template-columns:
            repeat(4, minmax(0, 1fr));

        gap: 12px;

        margin-bottom: 15px;
    }

    .parents-page .stat-card-modern {
        min-width: 0;

        padding: 13px;

        background: #fff;

        border: 1px solid #e9ecef;

        border-radius: 11px;
    }

    .parents-page .stat-value {
        margin-bottom: 2px;

        color: var(--pm-primary);

        font-size: 1.55rem;
        font-weight: 700;

        line-height: 1.2;
    }

    .parents-page .stat-label {
        display: flex;
        align-items: center;

        gap: 5px;

        color: #64748b;

        font-size: .72rem;
    }


    /* =========================================================
       IMPORT OVERLAY
       ========================================================= */

    .parents-page .import-overlay {
        position: fixed;
        inset: 0;

        width: 100%;
        height: 100%;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 15px;

        background: rgba(255, 255, 255, .95);

        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);

        z-index: 9999;
    }

    .parents-page .overlay-content {
        width: min(100%, 500px);

        padding: 28px;

        text-align: center;

        background: #fff;

        border-radius: 18px;

        box-shadow:
            0 20px 50px rgba(15, 23, 42, .16);
    }

    .parents-page .overlay-progress {
        margin: 20px 0;
    }

    .parents-page .progress {
        height: 24px;

        overflow: hidden;

        border-radius: 12px;

        background: #e9ecef;
    }

    .parents-page .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;

        background:
            linear-gradient(
                135deg,
                var(--pm-primary),
                var(--pm-secondary)
            );

        border-radius: 12px;

        font-size: .78rem;
        font-weight: 700;
    }


    /* =========================================================
       DISABLED TABLE
       ========================================================= */

    .parents-page .table-disabled {
        opacity: .5;

        pointer-events: none;

        user-select: none;
    }


    /* =========================================================
       TABLET
       ========================================================= */

    @media (max-width: 992px) {

        .parents-page .dashboard-container {
            margin: 18px auto;

            padding-inline: 15px;
        }

        .parents-page .card-body-modern {
            padding: 18px;
        }

        .parents-page .header-content {
            align-items: flex-start;
        }

    }


    /* =========================================================
       MOBILE
       ========================================================= */

    @media (max-width: 767.98px) {

        .parents-page .dashboard-container {
            margin: 10px auto;

            padding-inline: 10px;
        }

        .parents-page .modern-card {
            border-radius: 16px;
        }

        .parents-page .card-header-modern {
            padding: 15px;
        }

        .parents-page .header-content {
            flex-direction: column;

            align-items: stretch;

            gap: 14px;
        }

        .parents-page .header-left {
            width: 100%;
        }

        .parents-page .header-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;

            border-radius: 10px;

            font-size: 17px;
        }

        .parents-page .header-title h3 {
            font-size: 1rem;
        }

        .parents-page .header-title p {
            font-size: .72rem;
        }


        /* Header buttons */

        .parents-page .action-group {
            width: 100%;

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 8px;
        }

        .parents-page .btn-modern {
            width: 100%;

            min-height: 40px;

            padding: 8px 10px;

            font-size: .78rem;
        }


        /* Body */

        .parents-page .card-body-modern {
            padding: 12px;
        }


        /* =====================================================
           TABLE → MOBILE CARDS
           ===================================================== */

        .parents-page .table-container-modern {
            overflow: visible;

            border: 0;

            background: transparent;

            box-shadow: none;
        }

        .parents-page .table-modern {
            display: block;

            width: 100%;

            min-width: 0;
        }

        .parents-page .table-modern thead {
            display: none;
        }

        .parents-page .table-modern tbody {
            display: flex;

            flex-direction: column;

            gap: 10px;
        }

        .parents-page .table-modern tbody tr {
            display: grid;

            grid-template-columns: 1fr auto;

            gap: 0;

            padding: 13px;

            border: 1px solid #e5e7eb;

            border-radius: 13px;

            background: #fff;

            box-shadow:
                0 3px 10px rgba(15, 23, 42, .05);
        }

        .parents-page .table-modern tbody tr:hover td {
            background: transparent;
        }

        .parents-page .table-modern tbody td {
            display: flex;
            align-items: center;

            min-width: 0;

            padding: 6px 0;

            border: 0;

            background: transparent;

            white-space: normal;
        }


        /* Hide # */

        .parents-page .table-modern tbody td:first-child {
            position: absolute;

            width: 1px;
            height: 1px;

            overflow: hidden;

            clip: rect(0, 0, 0, 0);
        }


        /* Parent */

        .parents-page .table-modern tbody td:nth-child(2) {
            grid-column: 1 / -1;

            padding: 0 0 11px;

            margin-bottom: 6px;

            border-bottom: 1px solid #edf2f7;
        }


        /* Data */

        .parents-page .table-modern tbody td:nth-child(3),
        .parents-page .table-modern tbody td:nth-child(4),
        .parents-page .table-modern tbody td:nth-child(5),
        .parents-page .table-modern tbody td:nth-child(6) {

            grid-column: 1 / -1;

            justify-content: space-between;

            gap: 12px;
        }


        /* Labels */

        .parents-page .table-modern tbody td:nth-child(3)::before {
            content: "Gender";
        }

        .parents-page .table-modern tbody td:nth-child(4)::before {
            content: "Phone";
        }

        .parents-page .table-modern tbody td:nth-child(5)::before {
            content: "Email";
        }

        .parents-page .table-modern tbody td:nth-child(6)::before {
            content: "Status";
        }

        .parents-page .table-modern tbody td:nth-child(3)::before,
        .parents-page .table-modern tbody td:nth-child(4)::before,
        .parents-page .table-modern tbody td:nth-child(5)::before,
        .parents-page .table-modern tbody td:nth-child(6)::before {

            flex: 0 0 auto;

            color: #64748b;

            font-size: .72rem;
            font-weight: 600;
        }


        /* Parent */

        .parents-page .parent-info {
            width: 100%;

            min-width: 0;
        }

        .parents-page .parent-avatar {
            width: 42px;
            height: 42px;
            min-width: 42px;

            border-radius: 10px;
        }

        .parents-page .parent-name {
            max-width: none;

            font-size: .86rem;
        }


        /* Phone */

        .parents-page .phone-link {
            max-width: 62%;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

            font-size: .78rem;
        }


        /* Email */

        .parents-page .email-text {
            max-width: 62%;

            text-align: right;
        }


        /* Actions */

        .parents-page .table-modern tbody td:last-child {
            grid-column: 1 / -1;

            justify-content: flex-end;

            margin-top: 8px;

            padding-top: 11px;

            border-top: 1px solid #edf2f7;
        }

        .parents-page .table-modern tbody td:last-child::before {
            content: "Actions";

            margin-right: auto;

            color: #64748b;

            font-size: .72rem;
            font-weight: 600;
        }

        .parents-page .action-icon {
            width: 34px;
            height: 34px;
            min-width: 34px;
        }


        /* =====================================================
           MODALS
           ===================================================== */

        .parents-page .modal-modern .modal-dialog {
            margin: 10px;
        }

        .parents-page .modal-modern .modal-content {
            border-radius: 14px;
        }

        .parents-page .modal-modern .modal-body {
            padding: 15px;

            max-height: 78vh;
        }

        .parents-page .modal-modern .modal-footer {
            padding: 12px 15px;
        }


        /* Form */

        .parents-page .form-section-modern {
            padding: 12px;

            margin-bottom: 12px;
        }

        .parents-page .section-title-modern {
            font-size: .88rem;
        }


        /* Import */

        .parents-page .stats-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));

            gap: 8px;
        }

        .parents-page .stat-card-modern {
            padding: 10px;
        }

        .parents-page .stat-value {
            font-size: 1.25rem;
        }

        .parents-page .stat-label {
            font-size: .65rem;
        }

        .parents-page .import-preview-container {
            max-height: 45vh;
        }

    }


    /* =========================================================
       SMALL PHONES
       ========================================================= */

    @media (max-width: 480px) {

        .parents-page .dashboard-container {
            margin: 7px auto;

            padding-inline: 7px;
        }

        .parents-page .card-header-modern {
            padding: 13px;
        }

        .parents-page .card-body-modern {
            padding: 9px;
        }


        /* Buttons */

        .parents-page .action-group {
            grid-template-columns: 1fr;
        }


        /* Table cards */

        .parents-page .table-modern tbody tr {
            padding: 11px;

            border-radius: 12px;
        }

        .parents-page .parent-avatar {
            width: 38px;
            height: 38px;

            min-width: 38px;
        }

        .parents-page .parent-name {
            font-size: .81rem;
        }

        .parents-page .phone-link {
            max-width: 55%;
        }

        .parents-page .email-text {
            max-width: 55%;
        }


        /* Modal */

        .parents-page .modal-modern .modal-dialog {
            margin: 6px;
        }

        .parents-page .modal-modern .modal-content {
            border-radius: 11px;
        }

        .parents-page .modal-modern .modal-header {
            padding: 12px;
        }

        .parents-page .modal-modern .modal-body {
            padding: 12px;
        }

        .parents-page .modal-modern .modal-footer {
            padding: 10px 12px;

            flex-direction: column;
        }

        .parents-page .modal-modern .modal-footer .btn {
            width: 100%;
        }


        /* Forms */

        .parents-page .form-control-modern {
            min-height: 44px;

            font-size: 16px;
        }


        /* Import stats */

        .parents-page .stats-row {
            grid-template-columns: 1fr 1fr;
        }

        .parents-page .stat-value {
            font-size: 1.05rem;
        }

    }


    /* =========================================================
       VERY SMALL PHONES
       ========================================================= */

    @media (max-width: 360px) {

        .parents-page .dashboard-container {
            margin: 5px auto;

            padding-inline: 5px;
        }

        .parents-page .card-header-modern {
            padding: 10px;
        }

        .parents-page .card-body-modern {
            padding: 7px;
        }

        .parents-page .header-icon {
            width: 36px;
            height: 36px;
            min-width: 36px;

            font-size: 15px;
        }

        .parents-page .header-title h3 {
            font-size: .92rem;
        }

        .parents-page .table-modern tbody tr {
            padding: 9px;
        }

        .parents-page .parent-avatar {
            width: 34px;
            height: 34px;
            min-width: 34px;
        }

        .parents-page .parent-name {
            font-size: .75rem;
        }

        .parents-page .action-icon {
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

        .parents-page .animated-bg::before {
            animation: none;
        }

        .parents-page *,
        .parents-page *::before,
        .parents-page *::after {

            transition-duration: .01ms !important;

            animation-duration: .01ms !important;

            animation-iteration-count: 1 !important;

            scroll-behavior: auto !important;
        }

    }
</style>


<div class="parents-page">

    <div class="animated-bg"></div>
    <div class="particles"></div>


    {{-- =========================================================
         MAIN CONTAINER
         ========================================================= --}}

    <div class="dashboard-container">

        <div class="modern-card">


            {{-- =================================================
                 HEADER
                 ================================================= --}}

            <div class="card-header-modern">

                <div class="header-content">

                    <div class="header-left">

                        <div class="header-icon">
                            <i class="fas fa-users"></i>
                        </div>

                        <div class="header-title">

                            <h3>
                                Parents Management
                            </h3>

                            <p>
                                Manage parents and guardians
                            </p>

                        </div>

                    </div>


                    <div class="action-group">


                        {{-- Import --}}
                        <button
                            type="button"
                            class="btn-modern btn-import"
                            data-bs-toggle="modal"
                            data-bs-target="#importModal"
                        >

                            <i class="fas fa-file-import"></i>

                            <span>
                                Import
                            </span>

                        </button>


                        {{-- Add Parent --}}
                        <button
                            type="button"
                            class="btn-modern btn-add"
                            data-bs-toggle="modal"
                            data-bs-target="#parentModal"
                        >

                            <i class="fas fa-user-plus"></i>

                            <span>
                                Add Parent
                            </span>

                        </button>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 BODY
                 ================================================= --}}

            <div class="card-body-modern">

                @if ($parents->isEmpty())


                    {{-- Empty State --}}

                    <div class="empty-state-modern">

                        <i class="fas fa-users"></i>

                        <h6>
                            No Parents Found
                        </h6>

                        <p class="text-muted small mb-0">
                            Click "Add Parent" to register
                        </p>

                    </div>


                @else


                    {{-- =================================================
                         PARENTS TABLE
                         ================================================= --}}

                    <div class="table-container-modern">

                        <table
                            class="table-modern"
                            id="myTable"
                        >

                            <thead>

                                <tr>

                                    <th>
                                        #
                                    </th>

                                    <th>
                                        Parent
                                    </th>

                                    <th>
                                        Gender
                                    </th>

                                    <th>
                                        Phone
                                    </th>

                                    <th>
                                        Email
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

                                @foreach ($parents as $parent)

                                    <tr>


                                        {{-- Number --}}
                                        <td>

                                            <span class="fw-bold">
                                                {{ $loop->iteration }}
                                            </span>

                                        </td>


                                        {{-- Parent --}}
                                        <td>

                                            <div class="parent-info">

                                                <div class="parent-avatar">

                                                    {{
                                                        strtoupper(
                                                            substr(
                                                                $parent->first_name,
                                                                0,
                                                                1
                                                            )
                                                        )
                                                    }}{{
                                                        strtoupper(
                                                            substr(
                                                                $parent->last_name,
                                                                0,
                                                                1
                                                            )
                                                        )
                                                    }}

                                                </div>


                                                <span class="parent-name">

                                                    {{
                                                        ucwords(
                                                            strtolower(
                                                                $parent->first_name .
                                                                ' ' .
                                                                $parent->last_name
                                                            )
                                                        )
                                                    }}

                                                </span>

                                            </div>

                                        </td>


                                        {{-- Gender --}}
                                        <td>

                                            <div
                                                class="gender-badge {{
                                                    strtolower($parent->gender) === 'male'
                                                        ? 'gender-male'
                                                        : 'gender-female'
                                                }}"
                                                title="{{ ucfirst($parent->gender) }}"
                                            >

                                                {{
                                                    strtoupper(
                                                        substr(
                                                            $parent->gender,
                                                            0,
                                                            1
                                                        )
                                                    )
                                                }}

                                            </div>

                                        </td>


                                        {{-- Phone --}}
                                        <td>

                                            <a
                                                href="tel:{{ $parent->phone }}"
                                                class="phone-link"
                                            >

                                                <i class="fas fa-phone-alt"></i>

                                                <span>
                                                    {{ $parent->phone }}
                                                </span>

                                            </a>

                                        </td>


                                        {{-- Email --}}
                                        <td>

                                            <span
                                                class="email-text"
                                                title="{{ $parent->email ?? 'N/A' }}"
                                            >

                                                {{ $parent->email ?? 'N/A' }}

                                            </span>

                                        </td>


                                        {{-- Status --}}
                                        <td>

                                            @if ($parent->status == 1)

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
                                                        'Parents.edit',
                                                        [
                                                            'parent' => Hashids::encode($parent->id)
                                                        ]
                                                    ) }}"
                                                    class="action-icon view"
                                                    title="View Profile"
                                                    aria-label="View Profile"
                                                >

                                                    <i class="fas fa-eye"></i>

                                                </a>


                                                {{-- Block / Unblock --}}
                                                @if ($parent->status == 1)

                                                    <form
                                                        action="{{ route(
                                                            'Update.parents.status',
                                                            [
                                                                'parent' => Hashids::encode($parent->id)
                                                            ]
                                                        ) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm(
                                                            'Block {{ $parent->first_name }} {{ $parent->last_name }}?'
                                                        )"
                                                    >

                                                        @csrf

                                                        @method('PUT')

                                                        <button
                                                            type="submit"
                                                            class="action-icon warning"
                                                            title="Block"
                                                            aria-label="Block Parent"
                                                        >

                                                            <i class="fas fa-ban"></i>

                                                        </button>

                                                    </form>


                                                @else

                                                    <form
                                                        action="{{ route(
                                                            'restore.parents.status',
                                                            [
                                                                'parent' => Hashids::encode($parent->id)
                                                            ]
                                                        ) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm(
                                                            'Unblock {{ $parent->first_name }} {{ $parent->last_name }}?'
                                                        )"
                                                    >

                                                        @csrf

                                                        @method('PUT')

                                                        <button
                                                            type="submit"
                                                            class="action-icon success"
                                                            title="Unblock"
                                                            aria-label="Unblock Parent"
                                                        >

                                                            <i class="fas fa-check"></i>

                                                        </button>

                                                    </form>

                                                @endif


                                                {{-- Delete --}}
                                                <form
                                                    action="{{ route(
                                                        'Parents.remove',
                                                        [
                                                            'parent' => Hashids::encode($parent->id)
                                                        ]
                                                    ) }}"
                                                    method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm(
                                                        'Delete {{ $parent->first_name }} {{ $parent->last_name }}? This cannot be undone.'
                                                    )"
                                                >

                                                    @csrf

                                                    @method('PUT')

                                                    <button
                                                        type="submit"
                                                        class="action-icon danger"
                                                        title="Delete"
                                                        aria-label="Delete Parent"
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
         IMPORT MODAL
         ========================================================= --}}

    <div
        class="modal fade modal-modern"
        id="importModal"
        tabindex="-1"
        aria-labelledby="importModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-xl modal-dialog-centered">

            <div class="modal-content">


                {{-- Header --}}
                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="importModalLabel"
                    >

                        <i class="fas fa-file-import me-2"></i>

                        Import Parents Data

                    </h5>


                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>


                <div class="modal-body">


                    {{-- =============================================
                         STEP 1 — UPLOAD
                         ============================================= --}}

                    <div id="uploadStep">

                        <div class="text-center mb-4">

                            <i
                                class="fas fa-cloud-upload-alt text-primary mb-3"
                                style="font-size: 3.5rem;"
                            ></i>

                            <h5>
                                Upload Excel File
                            </h5>

                            <p class="text-muted small mb-0">
                                Supported formats: .xlsx, .xls, .csv
                                (Max: 2MB)
                            </p>

                        </div>


                        <form
                            id="uploadForm"
                            enctype="multipart/form-data"
                        >

                            @csrf


                            <div
                                class="file-input-modern"
                                id="fileDropZone"
                            >

                                <input
                                    type="file"
                                    name="file"
                                    id="fileInput"
                                    accept=".xlsx,.xls,.csv"
                                    required
                                >


                                <label
                                    for="fileInput"
                                    class="file-input-label"
                                >

                                    <i class="fas fa-file-excel text-success"></i>

                                    <span class="fw-bold">
                                        Click to browse or drag & drop
                                    </span>

                                    <span class="small text-muted">
                                        Excel / CSV file only
                                    </span>

                                </label>

                            </div>


                            <div
                                id="fileError"
                                class="text-danger small mt-2 text-center d-none"
                            ></div>


                            <div class="mt-3 text-center">

                                <p class="mb-0">

                                    Need a template?

                                    <a
                                        href="{{ route('parent.template.export') }}"
                                        class="text-decoration-none"
                                    >

                                        <i class="fas fa-download me-1"></i>

                                        Download Sample

                                    </a>

                                </p>

                            </div>

                        </form>

                    </div>


                    {{-- =============================================
                         STEP 2 — PREVIEW
                         ============================================= --}}

                    <div
                        id="previewStep"
                        class="d-none"
                    >


                        {{-- Stats --}}
                        <div class="stats-row">


                            <div class="stat-card-modern">

                                <div
                                    class="stat-value"
                                    id="totalRows"
                                >
                                    0
                                </div>

                                <div class="stat-label">

                                    <i class="fas fa-file-archive text-primary"></i>

                                    Total Rows

                                </div>

                            </div>


                            <div class="stat-card-modern">

                                <div
                                    class="stat-value text-success"
                                    id="validRows"
                                >
                                    0
                                </div>

                                <div class="stat-label">

                                    <i class="fas fa-check-circle text-success"></i>

                                    Valid Data

                                </div>

                            </div>


                            <div class="stat-card-modern">

                                <div
                                    class="stat-value text-danger"
                                    id="invalidRows"
                                >
                                    0
                                </div>

                                <div class="stat-label">

                                    <i class="fas fa-exclamation-circle text-danger"></i>

                                    Errors

                                </div>

                            </div>


                            <div class="stat-card-modern">

                                <button
                                    type="button"
                                    id="startImportBtn"
                                    class="btn btn-success w-100"
                                    disabled
                                >

                                    <i class="fas fa-cloud-upload-alt me-1"></i>

                                    Import All

                                </button>

                            </div>

                        </div>


                        {{-- Errors --}}
                        <div
                            id="errorsContainer"
                            class="d-none"
                        >

                            <div class="alert alert-danger">

                                <h6 class="alert-heading">

                                    <i class="fas fa-exclamation-triangle me-2"></i>

                                    Validation Errors

                                </h6>

                                <ul
                                    id="errorsList"
                                    class="mb-0 small"
                                ></ul>

                            </div>

                        </div>


                        {{-- Preview Table --}}
                        <div class="import-preview-container">

                            <table
                                class="table table-hover table-bordered"
                                id="previewTable"
                            >

                                <thead>

                                    <tr>

                                        <th>#</th>
                                        <th>Parent Name</th>
                                        <th>Gender</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Student Name</th>
                                        <th>Student Gender</th>
                                        <th>Class</th>
                                        <th>Stream</th>
                                        <th>Status</th>

                                    </tr>

                                </thead>


                                <tbody id="previewTableBody"></tbody>

                            </table>

                        </div>


                        <div
                            id="tableInfo"
                            class="text-muted small mt-2 text-center d-none"
                        >

                            <i class="fas fa-info-circle me-1"></i>

                            <span id="rowCount">
                                0
                            </span>

                            records displayed

                        </div>


                        {{-- Progress --}}
                        <div
                            id="importProgress"
                            class="d-none mt-4"
                        >

                            <div class="progress">

                                <div
                                    id="importProgressBar"
                                    class="progress-bar progress-bar-striped progress-bar-animated"
                                    role="progressbar"
                                    style="width: 0%"
                                >
                                    0%
                                </div>

                            </div>


                            <div class="mt-2 text-center">

                                <span id="importStatus">
                                    Preparing import...
                                </span>

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
                        Close
                    </button>


                    <button
                        type="button"
                        id="uploadButton"
                        class="btn btn-primary"
                    >

                        <i class="fas fa-upload me-1"></i>

                        Upload & Preview

                    </button>


                    <button
                        type="button"
                        id="backButton"
                        class="btn btn-secondary d-none"
                    >

                        <i class="fas fa-arrow-left me-1"></i>

                        Back

                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         PARENT REGISTRATION MODAL
         ========================================================= --}}

    <div
        class="modal fade modal-modern"
        id="parentModal"
        tabindex="-1"
        aria-labelledby="parentModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">


                {{-- Header --}}
                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="parentModalLabel"
                    >

                        <i class="fas fa-user-plus me-2"></i>

                        Parent Registration Form

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
                    action="{{ route('Parents.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    @csrf


                    <div class="modal-body">


                        {{-- =========================================
                             PARENT INFORMATION
                             ========================================= --}}

                        <div class="form-section-modern">

                            <h6 class="section-title-modern">

                                <i class="fas fa-user"></i>

                                Parent / Guardian Information

                            </h6>


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

                                    </div>

                                </div>


                                {{-- Last Name --}}
                                <div class="col-md-6">

                                    <div class="form-group-modern">

                                        <label class="form-label-modern">

                                            Last Name

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

                            </div>

                        </div>


                        {{-- =========================================
                             STUDENT INFORMATION
                             ========================================= --}}

                        <div class="form-section-modern">

                            <h6 class="section-title-modern">

                                <i class="fas fa-user-graduate"></i>

                                Student Information

                            </h6>


                            <div class="row g-2">


                                {{-- First --}}
                                <div class="col-md-4">

                                    <div class="form-group-modern">

                                        <label class="form-label-modern">

                                            First Name

                                            <span class="required">
                                                *
                                            </span>

                                        </label>


                                        <input
                                            type="text"
                                            name="student_first_name"
                                            class="form-control-modern"
                                            value="{{ old('student_first_name') }}"
                                            required
                                        >


                                        @error('student_first_name')

                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>

                                        @enderror

                                    </div>

                                </div>


                                {{-- Middle --}}
                                <div class="col-md-4">

                                    <div class="form-group-modern">

                                        <label class="form-label-modern">

                                            Middle Name

                                            <span class="required">
                                                *
                                            </span>

                                        </label>


                                        <input
                                            type="text"
                                            name="student_middle_name"
                                            class="form-control-modern"
                                            value="{{ old('student_middle_name') }}"
                                            required
                                        >


                                        @error('student_middle_name')

                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>

                                        @enderror

                                    </div>

                                </div>


                                {{-- Last --}}
                                <div class="col-md-4">

                                    <div class="form-group-modern">

                                        <label class="form-label-modern">

                                            Last Name

                                            <span class="required">
                                                *
                                            </span>

                                        </label>


                                        <input
                                            type="text"
                                            name="student_last_name"
                                            class="form-control-modern"
                                            value="{{ old('student_last_name') }}"
                                            required
                                        >


                                        @error('student_last_name')

                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>

                                        @enderror

                                    </div>

                                </div>


                                {{-- Student Gender --}}
                                <div class="col-md-3">

                                    <div class="form-group-modern">

                                        <label class="form-label-modern">

                                            Gender

                                            <span class="required">
                                                *
                                            </span>

                                        </label>


                                        <select
                                            name="student_gender"
                                            class="form-control-modern"
                                            required
                                        >

                                            <option value="">
                                                Select
                                            </option>

                                            <option
                                                value="male"
                                                @selected(old('student_gender') === 'male')
                                            >
                                                Male
                                            </option>

                                            <option
                                                value="female"
                                                @selected(old('student_gender') === 'female')
                                            >
                                                Female
                                            </option>

                                        </select>


                                        @error('student_gender')

                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>

                                        @enderror

                                    </div>

                                </div>


                                {{-- DOB --}}
                                <div class="col-md-3">

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


                                {{-- Class --}}
                                <div class="col-md-3">

                                    <div class="form-group-modern">

                                        <label class="form-label-modern">

                                            Class

                                            <span class="required">
                                                *
                                            </span>

                                        </label>


                                        <select
                                            name="class"
                                            class="form-control-modern"
                                            required
                                        >

                                            <option value="">
                                                Select
                                            </option>

                                            @foreach ($classes as $class)

                                                <option
                                                    value="{{ $class->id }}"
                                                    @selected(old('class') == $class->id)
                                                >
                                                    {{ $class->class_name }}
                                                </option>

                                            @endforeach

                                        </select>


                                        @error('class')

                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>

                                        @enderror

                                    </div>

                                </div>


                                {{-- Stream --}}
                                <div class="col-md-3">

                                    <div class="form-group-modern">

                                        <label class="form-label-modern">

                                            Stream

                                            <span class="required">
                                                *
                                            </span>

                                        </label>


                                        <select
                                            name="group"
                                            class="form-control-modern"
                                            required
                                        >

                                            <option value="">
                                                Select
                                            </option>

                                            <option
                                                value="a"
                                                @selected(old('group') === 'a')
                                            >
                                                A
                                            </option>

                                            <option
                                                value="b"
                                                @selected(old('group') === 'b')
                                            >
                                                B
                                            </option>

                                            <option
                                                value="c"
                                                @selected(old('group') === 'c')
                                            >
                                                C
                                            </option>

                                        </select>


                                        @error('group')

                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>

                                        @enderror

                                    </div>

                                </div>


                                {{-- Bus --}}
                                <div class="col-md-6">

                                    <div class="form-group-modern">

                                        <label class="form-label-modern">
                                            Bus Number
                                        </label>


                                        <select
                                            name="bus_no"
                                            class="form-control-modern"
                                        >

                                            <option value="">
                                                Select
                                            </option>

                                            @foreach ($buses as $bus)

                                                <option
                                                    value="{{ $bus->id }}"
                                                    @selected(old('bus_no') == $bus->id)
                                                >
                                                    Bus No. {{ $bus->bus_no }}
                                                </option>

                                            @endforeach

                                        </select>


                                        @error('bus_no')

                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>

                                        @enderror

                                    </div>

                                </div>


                                {{-- Photo --}}
                                <div class="col-md-6">

                                    <div class="form-group-modern">

                                        <label class="form-label-modern">
                                            Student Photo
                                        </label>


                                        <input
                                            type="file"
                                            name="passport"
                                            class="form-control-modern"
                                            accept="image/*"
                                        >


                                        <div class="note-text">
                                            Max 1MB - Blue background
                                        </div>


                                        @error('passport')

                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>

                                        @enderror

                                    </div>

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
                            Save Parent
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- =========================================================
         IMPORT OVERLAY
         ========================================================= --}}

    <div
        id="importOverlay"
        class="import-overlay d-none"
    >

        <div class="overlay-content">

            <div class="text-center mb-3">

                <i
                    class="fas fa-cloud-upload-alt text-primary mb-3"
                    style="font-size: 3.5rem;"
                ></i>

                <h5>
                    Importing Records
                </h5>

            </div>


            <div class="overlay-progress">

                <div class="progress">

                    <div
                        class="progress-bar progress-bar-striped progress-bar-animated"
                        role="progressbar"
                        id="overlayProgress"
                        style="width: 0%"
                    >

                        <span class="progress-text">
                            0%
                        </span>

                    </div>

                </div>

            </div>


            <div class="status-text">

                <i class="fas fa-sync-alt fa-spin me-2"></i>

                <span id="overlayStatus">
                    Starting import process...
                </span>

            </div>


            <div class="mt-4">

                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    id="cancelImportBtn"
                >

                    <i class="fas fa-times me-1"></i>

                    Cancel

                </button>

            </div>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
     * =========================================================
     * HELPERS
     * =========================================================
     */

    const $ = (selector, parent = document) =>
        parent.querySelector(selector);

    const $$ = (selector, parent = document) =>
        Array.from(parent.querySelectorAll(selector));


    /*
     * Escape HTML before injecting Excel data into preview table.
     */

    function escapeHtml(value) {

        if (value === null || value === undefined) {
            return '';
        }

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    /*
     * =========================================================
     * PARENT FORM VALIDATION
     * =========================================================
     */

    $$('.needs-validation').forEach(function (form) {

        const submitButton = $('button[type="submit"]', form);

        if (!submitButton) {
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
             * Do not manually call form.submit().
             *
             * Let the browser continue normal Laravel form
             * submission after successful validation.
             */

            submitButton.disabled = true;

            submitButton.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm me-2"
                    role="status"
                    aria-hidden="true"
                ></span>
                Saving...
            `;

        });

    });


    /*
     * =========================================================
     * IMPORT ELEMENTS
     * =========================================================
     */

    const fileInput = $('#fileInput');
    const fileDropZone = $('#fileDropZone');

    const uploadButton = $('#uploadButton');
    const backButton = $('#backButton');
    const startImportBtn = $('#startImportBtn');

    const uploadStep = $('#uploadStep');
    const previewStep = $('#previewStep');

    const previewTableBody = $('#previewTableBody');

    const errorsContainer = $('#errorsContainer');
    const errorsList = $('#errorsList');

    const importOverlay = $('#importOverlay');
    const overlayProgress = $('#overlayProgress');
    const overlayStatus = $('#overlayStatus');

    const cancelImportBtn = $('#cancelImportBtn');


    /*
     * =========================================================
     * FILE VALIDATION
     * =========================================================
     */

    function validateFile(file) {

        if (!file) {

            return {
                valid: false,
                message: 'Please select a file.'
            };

        }


        const maxSize = 2 * 1024 * 1024;

        const allowedExtensions = [
            'xlsx',
            'xls',
            'csv'
        ];


        if (file.size > maxSize) {

            return {
                valid: false,
                message: 'File size exceeds the 2MB limit.'
            };

        }


        const extension = file.name
            .split('.')
            .pop()
            .toLowerCase();


        if (!allowedExtensions.includes(extension)) {

            return {
                valid: false,
                message:
                    'Please select an Excel or CSV file (.xlsx, .xls, .csv).'
            };

        }


        return {
            valid: true,
            message: ''
        };

    }


    /*
     * =========================================================
     * FILE ERROR
     * =========================================================
     */

    function showFileError(message) {

        const errorDiv = $('#fileError');

        if (!errorDiv) {
            return;
        }


        if (message) {

            errorDiv.textContent = message;

            errorDiv.classList.remove('d-none');

        } else {

            errorDiv.textContent = '';

            errorDiv.classList.add('d-none');

        }

    }


    /*
     * =========================================================
     * RESET UPLOAD BUTTON
     * =========================================================
     */

    function resetUploadButton() {

        if (!uploadButton) {
            return;
        }

        uploadButton.disabled = false;

        uploadButton.innerHTML = `
            <i class="fas fa-upload me-1"></i>
            Upload & Preview
        `;

    }


    /*
     * =========================================================
     * FILE INPUT
     * =========================================================
     */

    if (fileInput) {

        fileInput.addEventListener('change', function () {

            const file = this.files?.[0];

            if (!file) {

                showFileError('');

                return;

            }


            const validation = validateFile(file);


            if (!validation.valid) {

                showFileError(validation.message);

                this.value = '';

                return;

            }


            showFileError('');

            uploadAndPreviewFile(file);

        });

    }


    /*
     * =========================================================
     * DRAG & DROP
     * =========================================================
     */

    if (fileDropZone && fileInput) {

        ['dragenter', 'dragover'].forEach(function (eventName) {

            fileDropZone.addEventListener(
                eventName,
                function (event) {

                    event.preventDefault();

                    event.stopPropagation();

                    fileDropZone.classList.add('drag-over');

                }
            );

        });


        ['dragleave', 'drop'].forEach(function (eventName) {

            fileDropZone.addEventListener(
                eventName,
                function (event) {

                    event.preventDefault();

                    event.stopPropagation();

                    fileDropZone.classList.remove('drag-over');

                }
            );

        });


        fileDropZone.addEventListener(
            'drop',
            function (event) {

                const file = event.dataTransfer?.files?.[0];

                if (!file) {
                    return;
                }

                fileInput.files = event.dataTransfer.files;

                fileInput.dispatchEvent(
                    new Event('change', {
                        bubbles: true
                    })
                );

            }
        );

    }


    /*
     * =========================================================
     * UPLOAD BUTTON
     * =========================================================
     */

    if (uploadButton) {

        uploadButton.addEventListener('click', function () {

            if (!fileInput?.files?.length) {

                showFileError(
                    'Please select a file first.'
                );

                return;

            }


            const file = fileInput.files[0];

            const validation = validateFile(file);


            if (!validation.valid) {

                showFileError(validation.message);

                return;

            }


            uploadAndPreviewFile(file);

        });

    }


    /*
     * =========================================================
     * UPLOAD + PREVIEW
     * =========================================================
     */

    function uploadAndPreviewFile(file) {

        if (!uploadButton) {
            return;
        }


        uploadButton.disabled = true;

        uploadButton.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-2"
                role="status"
                aria-hidden="true"
            ></span>
            Processing...
        `;


        const formData = new FormData();

        formData.append('file', file);

        formData.append(
            '_token',
            '{{ csrf_token() }}'
        );


        fetch(
            '{{ route('import.parents.students') }}',
            {
                method: 'POST',

                body: formData,

                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        )
        .then(function (response) {

            if (!response.ok) {
                throw new Error(
                    `Request failed with status ${response.status}`
                );
            }

            return response.json();

        })
        .then(function (data) {

            if (data.success) {

                showPreview(data);

            } else {

                showFileError(
                    data.message ||
                    'Failed to process the file.'
                );

            }

        })
        .catch(function (error) {

            console.error(
                'Parent import error:',
                error
            );

            showFileError(
                'Network error. Please try again.'
            );

        })
        .finally(function () {

            resetUploadButton();

        });

    }


    /*
     * =========================================================
     * SHOW PREVIEW
     * =========================================================
     */

    function showPreview(data) {

        const totalRows = $('#totalRows');
        const validRows = $('#validRows');
        const invalidRows = $('#invalidRows');

        if (totalRows) {
            totalRows.textContent =
                data.total_rows ?? 0;
        }

        if (validRows) {
            validRows.textContent =
                data.valid_rows ?? 0;
        }

        if (invalidRows) {
            invalidRows.textContent =
                data.invalid_rows ?? 0;
        }


        /*
         * Validation errors
         */

        if (
            Array.isArray(data.errors) &&
            data.errors.length > 0
        ) {

            errorsContainer?.classList.remove('d-none');

            if (errorsList) {

                errorsList.innerHTML = '';

                data.errors.forEach(function (error) {

                    const li =
                        document.createElement('li');

                    li.textContent = error;

                    errorsList.appendChild(li);

                });

            }

        } else {

            errorsContainer?.classList.add('d-none');

            if (errorsList) {
                errorsList.innerHTML = '';
            }

        }


        /*
         * Preview rows
         */

        if (previewTableBody) {

            previewTableBody.innerHTML = '';

            if (
                Array.isArray(data.preview_data) &&
                data.preview_data.length > 0
            ) {

                data.preview_data.forEach(
                    function (row, index) {

                        const tr =
                            document.createElement('tr');


                        const parentName =
                            escapeHtml(
                                row.parent_name
                            );

                        const parentGender =
                            escapeHtml(
                                row.parent_gender
                            );

                        const parentPhone =
                            escapeHtml(
                                row.parent_phone
                            );

                        const parentEmail =
                            escapeHtml(
                                row.parent_email
                            );

                        const studentName =
                            escapeHtml(
                                row.student_name
                            );

                        const studentGender =
                            escapeHtml(
                                row.student_gender
                            );

                        const className =
                            escapeHtml(
                                row.class_name
                            );

                        const studentGroup =
                            escapeHtml(
                                row.student_group
                            );


                        tr.innerHTML = `
                            <td>
                                ${index + 1}
                            </td>

                            <td class="fw-bold">
                                ${parentName}
                            </td>

                            <td>
                                <span class="badge bg-info text-white">
                                    ${escapeHtml(
                                        parentGender.charAt(0)
                                    )}
                                </span>
                            </td>

                            <td>
                                ${parentPhone}
                            </td>

                            <td>
                                ${parentEmail}
                            </td>

                            <td>
                                ${studentName}
                            </td>

                            <td>
                                <span class="badge bg-secondary text-white">
                                    ${escapeHtml(
                                        studentGender.charAt(0)
                                    )}
                                </span>
                            </td>

                            <td>
                                ${className}
                            </td>

                            <td>
                                <span class="badge bg-primary">
                                    ${studentGroup}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-warning">
                                    Pending
                                </span>
                            </td>
                        `;


                        previewTableBody.appendChild(tr);

                    }
                );


                const rowCount = $('#rowCount');
                const tableInfo = $('#tableInfo');


                if (rowCount) {

                    rowCount.textContent =
                        data.preview_data.length;

                }


                tableInfo?.classList.remove('d-none');

            }

        }


        /*
         * Switch to preview step
         */

        uploadStep?.classList.add('d-none');

        previewStep?.classList.remove('d-none');

        uploadButton?.classList.add('d-none');

        backButton?.classList.remove('d-none');


        /*
         * Enable import only when there are valid rows.
         */

        const validCount =
            Number(data.valid_rows ?? 0);


        if (startImportBtn) {

            startImportBtn.disabled =
                validCount <= 0;

            startImportBtn.innerHTML = `
                <i class="fas fa-cloud-upload-alt me-1"></i>
                Import ${validCount} Records
            `;

        }

    }


    /*
     * =========================================================
     * BACK BUTTON
     * =========================================================
     */

    if (backButton) {

        backButton.addEventListener(
            'click',
            function () {

                previewStep?.classList.add('d-none');

                uploadStep?.classList.remove('d-none');

                uploadButton?.classList.remove('d-none');

                backButton.classList.add('d-none');


                fileInput.value = '';

                showFileError('');


                if (previewTableBody) {
                    previewTableBody.innerHTML = '';
                }


                $('#tableInfo')?.classList.add('d-none');

                $('#errorsContainer')?.classList.add('d-none');

            }
        );

    }


    /*
     * =========================================================
     * START IMPORT
     * =========================================================
     */

    if (startImportBtn) {

        startImportBtn.addEventListener(
            'click',
            function () {

                const validRows =
                    Number(
                        $('#validRows')?.textContent || 0
                    );


                if (validRows <= 0) {
                    return;
                }


                if (
                    !confirm(
                        `Import ${validRows} records?`
                    )
                ) {

                    return;

                }


                showImportOverlay();

                processImport();

            }
        );

    }


    /*
     * =========================================================
     * IMPORT OVERLAY
     * =========================================================
     */

    function showImportOverlay() {

        importOverlay?.classList.remove('d-none');

        document
            .querySelector(
                '.parents-page .table-container-modern'
            )
            ?.classList.add('table-disabled');


        updateOverlayProgress(
            0,
            'Starting import...'
        );

    }


    function hideImportOverlay() {

        importOverlay?.classList.add('d-none');

        document
            .querySelector(
                '.parents-page .table-container-modern'
            )
            ?.classList.remove('table-disabled');

    }


    function updateOverlayProgress(
        percent,
        status
    ) {

        const safePercent =
            Math.min(
                100,
                Math.max(0, percent)
            );


        if (overlayProgress) {

            overlayProgress.style.width =
                `${safePercent}%`;


            const text =
                overlayProgress.querySelector(
                    '.progress-text'
                );


            if (text) {
                text.textContent =
                    `${safePercent}%`;
            }

        }


        if (overlayStatus) {

            overlayStatus.textContent =
                status;

        }

    }


    /*
     * =========================================================
     * PROCESS IMPORT
     * =========================================================
     */

    function processImport() {

        if (startImportBtn) {

            startImportBtn.disabled = true;

            startImportBtn.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm me-2"
                    role="status"
                    aria-hidden="true"
                ></span>
                Importing...
            `;

        }


        /*
         * This progress is visual feedback only.
         * Actual import status comes from the server response.
         */

        let progress = 0;


        const interval =
            setInterval(function () {

                progress += 5;

                if (progress > 90) {
                    progress = 90;
                }

                updateOverlayProgress(
                    progress,
                    `Processing... ${progress}%`
                );

            }, 300);


        fetch(
            '{{ route('process.import') }}',
            {
                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/json',

                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}',

                    'X-Requested-With':
                        'XMLHttpRequest',

                    'Accept':
                        'application/json'
                }
            }
        )
        .then(function (response) {

            if (!response.ok) {

                throw new Error(
                    `Import request failed (${response.status})`
                );

            }

            return response.json();

        })
        .then(function (data) {

            clearInterval(interval);


            if (data.success) {

                updateOverlayProgress(
                    100,
                    'Import completed!'
                );


                setTimeout(function () {

                    hideImportOverlay();


                    if (
                        typeof Swal !== 'undefined'
                    ) {

                        Swal.fire({
                            icon: 'success',

                            title: 'Success!',

                            text:
                                `${data.count ?? 0} records imported successfully`,

                            timer: 3000,

                            showConfirmButton: false
                        }).then(function () {

                            window.location.reload();

                        });

                    } else {

                        window.location.reload();

                    }

                }, 800);


            } else {

                hideImportOverlay();


                if (
                    typeof Swal !== 'undefined'
                ) {

                    Swal.fire({
                        icon: 'error',

                        title: 'Import Failed',

                        text:
                            data.message ||
                            'Failed to import records.'
                    });

                } else {

                    alert(
                        data.message ||
                        'Failed to import records.'
                    );

                }


                if (startImportBtn) {

                    startImportBtn.disabled =
                        false;

                }

            }

        })
        .catch(function (error) {

            clearInterval(interval);

            hideImportOverlay();


            console.error(
                'Import processing error:',
                error
            );


            if (
                typeof Swal !== 'undefined'
            ) {

                Swal.fire({
                    icon: 'error',

                    title: 'Network Error',

                    text:
                        error.message ||
                        'Unable to complete import.'
                });

            } else {

                alert(
                    error.message ||
                    'Unable to complete import.'
                );

            }


            if (startImportBtn) {

                startImportBtn.disabled =
                    false;

            }

        });

    }


    /*
     * =========================================================
     * CANCEL IMPORT
     * =========================================================
     */

    if (cancelImportBtn) {

        cancelImportBtn.addEventListener(
            'click',
            function () {

                if (
                    confirm(
                        'Cancel import?'
                    )
                ) {

                    hideImportOverlay();

                }

            }
        );

    }


    /*
     * =========================================================
     * RESET IMPORT MODAL WHEN CLOSED
     * =========================================================
     */

    const importModal =
        $('#importModal');


    if (importModal) {

        importModal.addEventListener(
            'hidden.bs.modal',
            function () {

                uploadStep?.classList.remove(
                    'd-none'
                );

                previewStep?.classList.add(
                    'd-none'
                );

                uploadButton?.classList.remove(
                    'd-none'
                );

                backButton?.classList.add(
                    'd-none'
                );


                if (fileInput) {
                    fileInput.value = '';
                }


                showFileError('');


                if (previewTableBody) {
                    previewTableBody.innerHTML = '';
                }


                $('#tableInfo')
                    ?.classList.add('d-none');

                $('#errorsContainer')
                    ?.classList.add('d-none');


                if (startImportBtn) {

                    startImportBtn.disabled =
                        true;

                    startImportBtn.innerHTML = `
                        <i class="fas fa-cloud-upload-alt me-1"></i>
                        Import All
                    `;

                }

            }
        );

    }

});
</script>

@endsection