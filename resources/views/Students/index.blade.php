@extends('SRTDashboard.frame')

@section('content')

<link
    href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
    rel="stylesheet"
/>


<style>
    /* =========================================================
       STUDENTS / CLASS MANAGEMENT
       Scoped to .students-page
       ========================================================= */

    .students-page {

        --sp-primary: #4361ee;
        --sp-primary-dark: #3a56d4;
        --sp-secondary: #3f37c9;
        --sp-accent: #4895ef;
        --sp-success: #1cc88a;
        --sp-warning: #f8961e;
        --sp-danger: #e74a3b;
        --sp-dark: #212529;
        --sp-muted: #64748b;
        --sp-border: #e2e8f0;
        --sp-light: #f8fafc;

        width: 100%;
        min-width: 0;

        position: relative;
        isolation: isolate;
    }


    /* =========================================================
       BACKGROUND
       ========================================================= */

    .students-page .animated-bg {

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
                rgba(67, 97, 238, .08),
                transparent 30%
            ),
            radial-gradient(
                circle at 20% 80%,
                rgba(63, 55, 201, .07),
                transparent 30%
            );
    }


    .students-page .animated-bg::before {

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

        animation:
            studentsRotate 60s linear infinite;
    }


    @keyframes studentsRotate {

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

    .students-page .particles {

        position: fixed;

        inset: 0;

        width: 100%;
        height: 100%;

        z-index: -1;

        pointer-events: none;

        overflow: hidden;
    }


    .students-page .particle {

        position: absolute;

        background: rgba(255,255,255,.45);

        border-radius: 50%;

        animation:
            studentsFloat 20s infinite;
    }


    @keyframes studentsFloat {

        0%,
        100% {
            transform:
                translate(0, 0)
                scale(1);
        }

        25% {
            transform:
                translate(100px, -100px)
                scale(1.2);
        }

        50% {
            transform:
                translate(200px, 0)
                scale(.8);
        }

        75% {
            transform:
                translate(100px, 100px)
                scale(1.1);
        }

    }


    /* =========================================================
       MAIN CONTAINER
       ========================================================= */

    .students-page .dashboard-container {

        width: min(100%, 1600px);

        margin: 24px auto;

        padding-inline: 20px;

        position: relative;

        z-index: 1;
    }


    /* =========================================================
       MAIN CARD
       ========================================================= */

    .students-page .modern-card {

        width: 100%;

        background: rgba(255,255,255,.96);

        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);

        border: 1px solid rgba(255,255,255,.65);

        border-radius: 24px;

        box-shadow:
            0 10px 30px rgba(15,23,42,.08),
            0 2px 8px rgba(15,23,42,.04);

        overflow: hidden;
    }


    /* =========================================================
       HEADER
       ========================================================= */

    .students-page .card-header-modern {

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


    .students-page .card-header-modern::before {

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


    .students-page .card-header-modern::after {

        content: "";

        position: absolute;

        left: 0;
        right: 0;
        bottom: 0;

        height: 2px;

        background:
            linear-gradient(
                90deg,
                var(--sp-warning),
                var(--sp-success),
                var(--sp-accent)
            );
    }


    .students-page .header-content {

        position: relative;

        z-index: 2;

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 20px;

        min-width: 0;
    }


    .students-page .header-left {

        display: flex;

        align-items: center;

        gap: 12px;

        min-width: 0;
    }


    .students-page .header-icon {

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


    .students-page .header-title {

        min-width: 0;

        color: #fff;
    }


    .students-page .header-title h3 {

        margin: 0;

        color: #fff;

        font-size:
            clamp(
                1rem,
                2vw,
                1.4rem
            );

        font-weight: 700;

        line-height: 1.25;

        overflow: hidden;

        text-overflow: ellipsis;

        white-space: nowrap;
    }


    /* =========================================================
       ACTION GROUP
       ========================================================= */

    .students-page .action-group {

        display: flex;

        align-items: center;

        justify-content: flex-end;

        gap: 8px;

        flex-wrap: wrap;

        flex-shrink: 0;

        position: relative;

        z-index: 100;
    }


    .students-page .btn-modern {

        min-height: 38px;

        padding: 8px 13px;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 6px;

        border: 1px solid transparent;

        border-radius: 8px;

        color: #fff;

        font-size: .82rem;

        font-weight: 600;

        line-height: 1;

        text-decoration: none;

        white-space: nowrap;

        cursor: pointer;

        transition:
            transform .2s ease,
            background-color .2s ease,
            box-shadow .2s ease;
    }


    .students-page .btn-modern:hover {

        color: #fff;

        transform:
            translateY(-1px);
    }


    .students-page .btn-promote {

        background: rgba(255,255,255,.14);

        border-color:
            rgba(255,255,255,.25);
    }


    .students-page .btn-export {

        background: rgba(255,255,255,.18);

        border-color:
            rgba(255,255,255,.28);
    }


    .students-page .btn-back {

        background: rgba(255,255,255,.12);

        border-color:
            rgba(255,255,255,.24);
    }


    .students-page .btn-add {

        background: rgba(255,255,255,.24);

        border-color:
            rgba(255,255,255,.35);
    }


    /* =========================================================
       DROPDOWN
       ========================================================= */

    .students-page .dropdown-modern {

        position: relative;
    }


    .students-page .dropdown-modern .dropdown-menu {

        min-width: 170px;

        margin-top: 7px !important;

        padding: 6px;

        border: 0;

        border-radius: 10px;

        background: #fff;

        box-shadow:
            0 12px 30px rgba(15,23,42,.15);

        z-index: 1080;
    }


    .students-page .dropdown-modern .dropdown-item {

        display: flex;

        align-items: center;

        gap: 8px;

        padding: 9px 11px;

        border-radius: 7px;

        color: var(--sp-dark);

        font-size: .82rem;
    }


    .students-page .dropdown-modern .dropdown-item:hover {

        background:
            linear-gradient(
                135deg,
                var(--sp-primary),
                var(--sp-secondary)
            );

        color: #fff;
    }


    /* =========================================================
       BODY
       ========================================================= */

    .students-page .card-body-modern {

        padding: 24px;
    }


    /* =========================================================
       STATS
       ========================================================= */

    .students-page .stats-card {

        width: 100%;

        margin-bottom: 20px;

        padding: 15px 18px;

        border-radius: 16px;

        color: #fff;

        background:
            linear-gradient(
                135deg,
                var(--sp-primary),
                var(--sp-secondary)
            );

        box-shadow:
            0 6px 18px rgba(67,97,238,.15);
    }


    .students-page .stat-item {

        display: flex;

        align-items: center;

        gap: 12px;

        min-height: 48px;
    }


    .students-page .stat-icon {

        width: 42px;
        height: 42px;

        min-width: 42px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 10px;

        background:
            rgba(255,255,255,.16);

        font-size: 18px;
    }


    .students-page .stat-label {

        margin-bottom: 2px;

        color:
            rgba(255,255,255,.85);

        font-size: .75rem;
    }


    .students-page .stat-value {

        color: #fff;

        font-size: 1.35rem;

        font-weight: 700;

        line-height: 1.2;
    }


    /* =========================================================
       BATCH FORM
       ========================================================= */

    .students-page .batch-form {

        margin-bottom: 20px;

        padding: 16px;

        background:
            linear-gradient(
                135deg,
                #f8fafc,
                #eef2f7
            );

        border: 1px solid #e5eaf0;

        border-radius: 14px;
    }


    .students-page .batch-grid {

        display: grid;

        grid-template-columns:
            minmax(180px, 1.2fr)
            auto
            minmax(200px, 1fr);

        align-items: end;

        gap: 12px;
    }


    .students-page .form-label-modern {

        display: block;

        margin-bottom: 5px;

        color: var(--sp-dark);

        font-size: .8rem;

        font-weight: 600;
    }


    .students-page .form-select-modern {

        width: 100%;

        min-height: 40px;

        padding: 8px 11px;

        border: 1px solid #dbe3ec;

        border-radius: 8px;

        background: #fff;

        color: #334155;

        font-size: .84rem;

        outline: none;

        transition:
            border-color .2s ease,
            box-shadow .2s ease;
    }


    .students-page .form-select-modern:focus {

        border-color:
            var(--sp-primary);

        box-shadow:
            0 0 0 3px
            rgba(67,97,238,.10);

        outline: none;
    }


    .students-page .btn-warning-modern {

        min-height: 40px;

        padding: 8px 15px;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 6px;

        border: 0;

        border-radius: 8px;

        background:
            linear-gradient(
                135deg,
                var(--sp-warning),
                #f4b619
            );

        color: #fff;

        font-size: .82rem;

        font-weight: 600;

        white-space: nowrap;

        transition:
            transform .2s ease,
            box-shadow .2s ease;
    }


    .students-page .btn-warning-modern:hover:not(:disabled) {

        transform:
            translateY(-1px);

        box-shadow:
            0 5px 15px
            rgba(248,150,30,.25);
    }


    .students-page .btn-warning-modern:disabled {

        opacity: .5;

        cursor: not-allowed;
    }


    .students-page .selected-counter {

        min-height: 40px;

        padding: 8px 12px;

        display: flex;

        align-items: center;

        gap: 8px;

        border-radius: 8px;

        background: #e9edf2;

        color: #475569;

        font-size: .8rem;
    }


    /* =========================================================
       TABLE CONTAINER
       ========================================================= */

    .students-page .table-container-modern {

        width: 100%;

        overflow-x: auto;

        overflow-y: hidden;

        background: #fff;

        border: 1px solid var(--sp-border);

        border-radius: 14px;

        box-shadow:
            0 4px 14px
            rgba(15,23,42,.05);

        -webkit-overflow-scrolling: touch;
    }


    /* =========================================================
       TABLE
       ========================================================= */

    .students-page .table-modern {

        width: 100%;

        min-width: 950px;

        margin: 0;

        border-collapse: separate;

        border-spacing: 0;

        font-size: .83rem;
    }


    .students-page .table-modern thead th {

        padding: 12px;

        background:
            linear-gradient(
                135deg,
                #2b3d5c,
                #1a2a44
            );

        color: #fff;

        border: 0;

        font-size: .72rem;

        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: .4px;

        white-space: nowrap;

        vertical-align: middle;
    }


    .students-page .table-modern tbody td {

        padding: 11px 12px;

        color: #475569;

        background: #fff;

        border-bottom:
            1px solid #edf2f7;

        vertical-align: middle;

        white-space: nowrap;
    }


    .students-page .table-modern tbody tr:last-child td {

        border-bottom: 0;
    }


    .students-page .table-modern tbody tr {

        transition:
            background-color .18s ease;
    }


    .students-page .table-modern tbody tr:hover td {

        background: #f8fafc;
    }


    /* =========================================================
       STUDENT INFO
       ========================================================= */

    .students-page .student-info {

        display: flex;

        align-items: center;

        gap: 9px;

        min-width: 170px;
    }


    .students-page .student-avatar-modern {

        width: 38px;
        height: 38px;

        min-width: 38px;

        object-fit: cover;

        border-radius: 9px;

        border: 2px solid #fff;

        box-shadow:
            0 2px 7px
            rgba(15,23,42,.12);
    }


    .students-page .student-name {

        max-width: 160px;

        color: var(--sp-dark);

        font-size: .82rem;

        font-weight: 600;

        overflow: hidden;

        text-overflow: ellipsis;

        white-space: nowrap;
    }


    /* =========================================================
       GENDER
       ========================================================= */

    .students-page .gender-badge {

        width: 28px;
        height: 28px;

        min-width: 28px;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        border-radius: 7px;

        color: #fff;

        font-size: .75rem;

        font-weight: 700;
    }


    .students-page .gender-male {

        background:
            linear-gradient(
                135deg,
                #4e73df,
                #224abe
            );
    }


    .students-page .gender-female {

        background:
            linear-gradient(
                135deg,
                #e83e8c,
                #c2185b
            );
    }


    /* =========================================================
       STREAM
       ========================================================= */

    .students-page .stream-badge {

        display: inline-block;

        padding: 4px 8px;

        border-radius: 6px;

        font-size: .72rem;

        font-weight: 600;

        white-space: nowrap;
    }


    .students-page .stream-A {

        background: #e8f0fe;

        color: #1a73e8;
    }


    .students-page .stream-B {

        background: #e6f4ea;

        color: #0f9d58;
    }


    .students-page .stream-C {

        background: #fce8e6;

        color: #d93025;
    }


    /* =========================================================
       CHECKBOX
       ========================================================= */

    .students-page .checkbox-modern {

        width: 18px;
        height: 18px;

        margin: 0;

        cursor: pointer;

        accent-color:
            var(--sp-primary);
    }


    /* =========================================================
       ACTIONS
       ========================================================= */

    .students-page .action-icons {

        display: flex;

        align-items: center;

        justify-content: flex-end;

        gap: 5px;

        flex-wrap: nowrap;
    }


    .students-page .action-icon {

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

        font-size: .78rem;

        cursor: pointer;

        transition:
            transform .18s ease,
            box-shadow .18s ease;
    }


    .students-page .action-icon:hover {

        color: #fff;

        transform:
            translateY(-1px);

        box-shadow:
            0 4px 9px
            rgba(15,23,42,.16);
    }


    .students-page .action-icon.edit {

        background:
            linear-gradient(
                135deg,
                var(--sp-primary),
                var(--sp-secondary)
            );
    }


    .students-page .action-icon.view {

        background:
            linear-gradient(
                135deg,
                #36b9cc,
                #1a8a9e
            );
    }


    .students-page .action-icon.delete {

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

    .students-page .empty-state-modern {

        padding: 45px 20px;

        text-align: center;

        background:
            linear-gradient(
                135deg,
                #fff8e1,
                #fff3cd
            );

        border:
            2px dashed #f6c23e;

        border-radius: 16px;
    }


    .students-page .empty-state-modern i {

        margin-bottom: 12px;

        color: #f6c23e;

        font-size: 44px;
    }


    .students-page .empty-state-modern h6 {

        margin-bottom: 5px;

        color: #334155;

        font-weight: 700;
    }


    /* =========================================================
       MODALS
       ========================================================= */

    .students-page .modal-modern .modal-content {

        border: 0;

        border-radius: 18px;

        overflow: hidden;

        box-shadow:
            0 20px 50px
            rgba(15,23,42,.20);
    }


    .students-page .modal-modern .modal-header {

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


    .students-page .modal-modern .modal-title {

        font-size: 1rem;

        font-weight: 700;
    }


    .students-page .modal-modern .modal-body {

        padding: 20px;

        max-height: 76vh;

        overflow-y: auto;
    }


    .students-page .modal-modern .modal-footer {

        padding: 14px 20px;

        border: 0;

        background: #f8fafc;
    }


    /* =========================================================
       FORM CONTROLS
       ========================================================= */

    .students-page .form-group-modern {

        margin-bottom: 13px;
    }


    .students-page .form-control-modern {

        width: 100%;

        min-height: 40px;

        padding: 8px 11px;

        border: 1px solid #dbe3ec;

        border-radius: 8px;

        background: #fff;

        color: #334155;

        font-size: .84rem;

        outline: none;

        transition:
            border-color .2s ease,
            box-shadow .2s ease;
    }


    .students-page .form-control-modern:focus {

        border-color:
            var(--sp-primary);

        box-shadow:
            0 0 0 3px
            rgba(67,97,238,.10);

        outline: none;
    }


    .students-page .form-label-modern .required {

        color:
            var(--sp-danger);
    }


    .students-page .note-text {

        margin-top: 3px;

        color: #64748b;

        font-size: .7rem;
    }


    .students-page .text-danger {

        display: block;

        margin-top: 4px;

        font-size: .72rem;
    }


    /* =========================================================
       SELECT2
       ========================================================= */

    .students-page .select2-container {

        width: 100% !important;
    }


    .students-page
    .select2-container--default
    .select2-selection--single {

        height: 40px !important;

        padding: 5px 11px !important;

        border:
            1px solid #dbe3ec !important;

        border-radius: 8px !important;

        background: #fff !important;
    }


    .students-page
    .select2-container--default
    .select2-selection--single
    .select2-selection__rendered {

        line-height: 28px !important;

        color: #334155 !important;

        font-size: .84rem;
    }


    .students-page
    .select2-container--default
    .select2-selection--single
    .select2-selection__arrow {

        height: 38px !important;
    }


    .students-page .select2-dropdown {

        border:
            1px solid #dbe3ec !important;

        border-radius: 8px !important;

        box-shadow:
            0 10px 25px
            rgba(15,23,42,.12);
    }


    .students-page
    .select2-container--default
    .select2-results__option {

        padding: 8px 10px;

        font-size: .82rem;
    }


    /* =========================================================
       TABLET
       ========================================================= */

    @media (max-width: 992px) {

        .students-page .dashboard-container {

            margin: 18px auto;

            padding-inline: 15px;
        }


        .students-page .card-body-modern {

            padding: 18px;
        }


        .students-page .header-content {

            align-items: flex-start;
        }


        .students-page .batch-grid {

            grid-template-columns:
                1fr 1fr;
        }


        .students-page .batch-grid
        > :last-child {

            grid-column:
                1 / -1;
        }

    }


    /* =========================================================
       MOBILE
       ========================================================= */

    @media (max-width: 767.98px) {

        .students-page .dashboard-container {

            margin: 10px auto;

            padding-inline: 10px;
        }


        .students-page .modern-card {

            border-radius: 16px;
        }


        .students-page .card-header-modern {

            padding: 15px;
        }


        .students-page .header-content {

            flex-direction: column;

            align-items: stretch;

            gap: 14px;
        }


        .students-page .header-left {

            width: 100%;
        }


        .students-page .header-icon {

            width: 40px;
            height: 40px;

            min-width: 40px;

            border-radius: 10px;

            font-size: 17px;
        }


        .students-page .header-title h3 {

            font-size: 1rem;

            white-space: normal;

            overflow: visible;
        }


        .students-page .action-group {

            width: 100%;

            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 8px;
        }


        .students-page .dropdown-modern {

            width: 100%;
        }


        .students-page .btn-modern {

            width: 100%;

            min-height: 40px;

            padding: 8px 9px;

            font-size: .76rem;
        }


        .students-page .dropdown-modern .btn-modern {

            width: 100%;
        }


        /* =====================================================
           BODY
           ===================================================== */

        .students-page .card-body-modern {

            padding: 12px;
        }


        /* =====================================================
           STATS
           ===================================================== */

        .students-page .stats-card {

            padding: 10px 12px;

            margin-bottom: 12px;

            border-radius: 11px;
        }


        .students-page .stats-card .row {

            flex-direction: column;

            gap: 4px;
        }


        .students-page .stats-card [class*="col-"] {

            width: 100%;

            flex: 1 1 100%;

            padding-inline: 4px;
        }


        .students-page .stat-item {

            min-height: 36px;

            gap: 8px;
        }


        .students-page .stat-icon {

            width: 32px;
            height: 32px;

            min-width: 32px;

            border-radius: 7px;

            font-size: 14px;
        }


        .students-page .stat-value {

            font-size: 1rem;
        }


        .students-page .stat-label {

            font-size: .66rem;
        }


        /* =====================================================
           BATCH FORM
           ===================================================== */

        .students-page .batch-form {

            margin-bottom: 12px;

            padding: 11px;

            border-radius: 11px;
        }


        .students-page .batch-grid {

            display: flex;

            flex-direction: column;

            align-items: stretch;

            gap: 9px;
        }


        .students-page .batch-grid > * {

            width: 100%;
        }


        .students-page .selected-counter {

            width: 100%;

            justify-content: center;
        }


        /* =====================================================
           TABLE → MOBILE CARDS
           ===================================================== */

        .students-page .table-container-modern {

            overflow: visible;

            border: 0;

            background: transparent;

            box-shadow: none;
        }


        .students-page .table-modern {

            display: block;

            width: 100%;

            min-width: 0;
        }


        .students-page .table-modern thead {

            display: none;
        }


        .students-page .table-modern tbody {

            display: flex;

            flex-direction: column;

            gap: 10px;
        }


        .students-page .table-modern tbody tr {

            display: grid;

            grid-template-columns:
                auto 1fr;

            gap: 0;

            padding: 12px;

            border:
                1px solid #e5e7eb;

            border-radius: 13px;

            background: #fff;

            box-shadow:
                0 3px 10px
                rgba(15,23,42,.05);
        }


        .students-page
        .table-modern tbody tr:hover td {

            background: transparent;
        }


        .students-page .table-modern tbody td {

            display: flex;

            align-items: center;

            min-width: 0;

            padding: 6px 0;

            border: 0;

            background: transparent;

            white-space: normal;
        }


        /* =====================================================
           CHECKBOX
           ===================================================== */

        .students-page
        .table-modern tbody td:first-child {

            grid-column: 1;

            grid-row: 1;

            align-self: start;

            padding-right: 9px;
        }


        /* =====================================================
           ADMISSION NUMBER
           ===================================================== */

        .students-page
        .table-modern tbody td:nth-child(2) {

            grid-column: 2;

            grid-row: 1;

            justify-content: flex-start;

            padding-bottom: 5px;

            color: #64748b;

            font-size: .68rem;

            font-weight: 700;
        }


        /* =====================================================
           STUDENT
           ===================================================== */

        .students-page
        .table-modern tbody td:nth-child(3) {

            grid-column:
                1 / -1;

            padding:
                5px 0 11px;

            margin-bottom: 5px;

            border-bottom:
                1px solid #edf2f7;
        }


        /* =====================================================
           MIDDLE / SURNAME
           ===================================================== */

        .students-page
        .table-modern tbody td:nth-child(4),
        .students-page
        .table-modern tbody td:nth-child(5),
        .students-page
        .table-modern tbody td:nth-child(6),
        .students-page
        .table-modern tbody td:nth-child(7),
        .students-page
        .table-modern tbody td:nth-child(8) {

            grid-column:
                1 / -1;

            justify-content:
                space-between;

            gap: 12px;
        }


        /* =====================================================
           LABELS
           ===================================================== */

        .students-page
        .table-modern tbody td:nth-child(4)::before {

            content: "Middle Name";
        }


        .students-page
        .table-modern tbody td:nth-child(5)::before {

            content: "Surname";
        }


        .students-page
        .table-modern tbody td:nth-child(6)::before {

            content: "Gender";
        }


        .students-page
        .table-modern tbody td:nth-child(7)::before {

            content: "Stream";
        }


        .students-page
        .table-modern tbody td:nth-child(8)::before {

            content: "Date of Birth";
        }


        .students-page
        .table-modern tbody td:nth-child(4)::before,
        .students-page
        .table-modern tbody td:nth-child(5)::before,
        .students-page
        .table-modern tbody td:nth-child(6)::before,
        .students-page
        .table-modern tbody td:nth-child(7)::before,
        .students-page
        .table-modern tbody td:nth-child(8)::before {

            flex:
                0 0 auto;

            color:
                #64748b;

            font-size: .7rem;

            font-weight: 600;
        }


        /* =====================================================
           STUDENT PROFILE
           ===================================================== */

        .students-page .student-info {

            width: 100%;

            min-width: 0;
        }


        .students-page .student-avatar-modern {

            width: 42px;
            height: 42px;

            min-width: 42px;
        }


        .students-page .student-name {

            max-width: none;

            font-size: .86rem;
        }


        /* =====================================================
           ACTIONS
           ===================================================== */

        .students-page
        .table-modern tbody td:last-child {

            grid-column:
                1 / -1;

            justify-content:
                flex-end;

            margin-top: 8px;

            padding-top: 11px;

            border-top:
                1px solid #edf2f7;
        }


        .students-page
        .table-modern tbody td:last-child::before {

            content: "Actions";

            margin-right: auto;

            color:
                #64748b;

            font-size: .7rem;

            font-weight: 600;
        }


        .students-page .action-icons {

            justify-content:
                flex-end;
        }


        .students-page .action-icon {

            width: 34px;
            height: 34px;

            min-width: 34px;
        }


        /* =====================================================
           MODALS
           ===================================================== */

        .students-page .modal-modern .modal-dialog {

            margin: 10px;
        }


        .students-page .modal-modern .modal-content {

            border-radius: 14px;
        }


        .students-page .modal-modern .modal-body {

            padding: 15px;

            max-height: 78vh;
        }


        .students-page .modal-modern .modal-footer {

            padding: 12px 15px;
        }


        /* =====================================================
           SELECT2 MOBILE
           ===================================================== */

        .students-page
        .select2-container--default
        .select2-selection--single {

            height: 44px !important;

            padding: 7px 10px !important;
        }


        .students-page
        .select2-container--default
        .select2-selection--single
        .select2-selection__rendered {

            line-height: 28px !important;

            font-size: 16px;
        }

    }


    /* =========================================================
       SMALL PHONES
       ========================================================= */

    @media (max-width: 480px) {

        .students-page .dashboard-container {

            margin: 7px auto;

            padding-inline: 7px;
        }


        .students-page .card-header-modern {

            padding: 13px;
        }


        .students-page .card-body-modern {

            padding: 9px;
        }


        .students-page .action-group {

            grid-template-columns: 1fr;
        }


        .students-page .table-modern tbody tr {

            padding: 10px;

            border-radius: 12px;
        }


        .students-page .student-avatar-modern {

            width: 38px;
            height: 38px;

            min-width: 38px;
        }


        .students-page .student-name {

            font-size: .8rem;
        }


        .students-page .action-icon {

            width: 32px;
            height: 32px;

            min-width: 32px;
        }


        .students-page .modal-modern .modal-dialog {

            margin: 6px;
        }


        .students-page .modal-modern .modal-header {

            padding: 12px;
        }


        .students-page .modal-modern .modal-body {

            padding: 12px;
        }


        .students-page .modal-modern .modal-footer {

            padding: 10px 12px;

            flex-direction: column;
        }


        .students-page
        .modal-modern
        .modal-footer
        .btn {

            width: 100%;
        }


        .students-page .form-control-modern {

            min-height: 44px;

            font-size: 16px;
        }

    }


    /* =========================================================
       VERY SMALL PHONES
       ========================================================= */

    @media (max-width: 360px) {

        .students-page .dashboard-container {

            margin: 5px auto;

            padding-inline: 5px;
        }


        .students-page .card-header-modern {

            padding: 10px;
        }


        .students-page .card-body-modern {

            padding: 7px;
        }


        .students-page .header-icon {

            width: 36px;
            height: 36px;

            min-width: 36px;

            font-size: 15px;
        }


        .students-page .header-title h3 {

            font-size: .9rem;
        }


        .students-page .stats-card {

            padding: 8px 9px;
        }


        .students-page .stat-icon {

            width: 28px;
            height: 28px;

            min-width: 28px;
        }


        .students-page .stat-value {

            font-size: .88rem;
        }


        .students-page .stat-label {

            font-size: .6rem;
        }


        .students-page .table-modern tbody tr {

            padding: 9px;
        }


        .students-page .student-avatar-modern {

            width: 34px;
            height: 34px;

            min-width: 34px;
        }


        .students-page .student-name {

            font-size: .75rem;
        }


        .students-page .action-icon {

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

        .students-page .animated-bg::before,
        .students-page .particle {

            animation: none;
        }

        .students-page *,
        .students-page *::before,
        .students-page *::after {

            transition-duration:
                .01ms !important;

            animation-duration:
                .01ms !important;

            animation-iteration-count:
                1 !important;
        }

    }
</style>


<div class="students-page">

    <div class="animated-bg"></div>

    <div class="particles"></div>


    <div class="dashboard-container">

        <div class="modern-card">


            {{-- =====================================================
                 HEADER
                 ===================================================== --}}

            <div class="card-header-modern">

                <div class="header-content">

                    <div class="header-left">

                        <div class="header-icon">

                            <i class="fas fa-users"></i>

                        </div>


                        <div class="header-title">

                            <h3>
                                {{ strtoupper($classId->class_name) }}
                                -
                                {{ strtoupper($classId->class_code) }}
                            </h3>

                        </div>

                    </div>


                    <div class="action-group">


                        {{-- Promote --}}
                        @if ($students->isNotEmpty())

                            @if (auth()->user()->usertype != 5)

                                <button
                                    type="button"
                                    class="btn-modern btn-promote"
                                    data-bs-toggle="modal"
                                    data-bs-target="#promoteModal"
                                >

                                    <i class="fas fa-exchange-alt"></i>

                                    <span>
                                        Promote
                                    </span>

                                </button>

                            @endif


                            {{-- Export --}}
                            <div class="dropdown dropdown-modern">

                                <button
                                    class="btn-modern btn-export dropdown-toggle"
                                    type="button"
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
                                >

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="{{ route(
                                                'students.export.excel',
                                                [
                                                    'class' => Hashids::encode($classId->id)
                                                ]
                                            ) }}"
                                        >

                                            <i class="fas fa-file-excel text-success"></i>

                                            Excel

                                        </a>

                                    </li>


                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="{{ route(
                                                'export.student.pdf',
                                                [
                                                    'class' => Hashids::encode($classId->id)
                                                ]
                                            ) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >

                                            <i class="fas fa-file-pdf text-danger"></i>

                                            PDF

                                        </a>

                                    </li>

                                </ul>

                            </div>

                        @endif


                        {{-- Back --}}
                        <a
                            href="{{ route(
                                'classes.list',
                                [
                                    'class' => Hashids::encode($classId->id)
                                ]
                            ) }}"
                            class="btn-modern btn-back"
                        >

                            <i class="fas fa-arrow-left"></i>

                            <span>
                                Back
                            </span>

                        </a>


                        {{-- Add Student --}}
                        @if (auth()->user()->usertype != 5)

                            <button
                                type="button"
                                class="btn-modern btn-add"
                                data-bs-toggle="modal"
                                data-bs-target="#addStudentModal"
                            >

                                <i class="fas fa-plus"></i>

                                <span>
                                    New Student
                                </span>

                            </button>

                        @endif

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
                                        Total Students
                                    </div>

                                    <div class="stat-value">
                                        {{ $students->count() }}
                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Boys --}}
                        <div class="col-md-4">

                            <div class="stat-item">

                                <div class="stat-icon">

                                    <i class="fas fa-male"></i>

                                </div>


                                <div>

                                    <div class="stat-label">
                                        Boys
                                    </div>

                                    <div class="stat-value">

                                        {{
                                            $students
                                                ->filter(
                                                    fn($s) =>
                                                        strtolower($s->gender) === 'male'
                                                )
                                                ->count()
                                        }}

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Girls --}}
                        <div class="col-md-4">

                            <div class="stat-item">

                                <div class="stat-icon">

                                    <i class="fas fa-female"></i>

                                </div>


                                <div>

                                    <div class="stat-label">
                                        Girls
                                    </div>

                                    <div class="stat-value">

                                        {{
                                            $students
                                                ->filter(
                                                    fn($s) =>
                                                        strtolower($s->gender) === 'female'
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
                     BATCH STREAM UPDATE
                     ================================================= --}}

                <form
                    id="batchForm"
                    action="{{ route('students.batchUpdateStream') }}"
                    method="POST"
                    class="batch-form"
                >

                    @csrf


                    <div class="batch-grid">


                        {{-- Stream --}}
                        <div>

                            <label class="form-label-modern">

                                Transfer Student Stream

                            </label>


                            <select
                                name="new_stream"
                                class="form-select-modern"
                                required
                            >

                                <option value="">
                                    -- Select Stream --
                                </option>

                                <option value="A">
                                    Stream A
                                </option>

                                <option value="B">
                                    Stream B
                                </option>

                                <option value="C">
                                    Stream C
                                </option>

                            </select>

                        </div>


                        {{-- Button --}}
                        <div>

                            <button
                                type="submit"
                                class="btn-warning-modern"
                                id="updateStreamBtn"
                                disabled
                            >

                                <i class="fas fa-random"></i>

                                Shift Stream

                            </button>

                        </div>


                        {{-- Counter --}}
                        <div>

                            <div
                                class="selected-counter"
                                id="selectedCount"
                            >

                                <i class="fas fa-users"></i>

                                <span>
                                    0 students selected
                                </span>

                            </div>

                        </div>

                    </div>

                </form>


                {{-- =================================================
                     STUDENTS
                     ================================================= --}}

                @if ($students->isEmpty())


                    <div class="empty-state-modern">

                        <i class="fas fa-users"></i>

                        <h6>
                            No Students Found
                        </h6>

                        <p class="text-muted small mb-0">
                            Click "New Student" to add your first student
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

                                    <th class="text-center">

                                        <input
                                            type="checkbox"
                                            id="selectAll"
                                            class="checkbox-modern"
                                            aria-label="Select all students"
                                        >

                                    </th>

                                    <th>
                                        Adm #
                                    </th>

                                    <th>
                                        Student
                                    </th>

                                    <th>
                                        Middle Name
                                    </th>

                                    <th>
                                        Surname
                                    </th>

                                    <th class="text-center">
                                        Gender
                                    </th>

                                    <th class="text-center">
                                        Stream
                                    </th>

                                    <th>
                                        Date of Birth
                                    </th>

                                    <th class="text-center">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach ($students as $student)

                                    <tr>


                                        {{-- Checkbox --}}
                                        <td class="text-center">

                                            <input
                                                type="checkbox"
                                                name="student[]"
                                                value="{{ $student->id }}"
                                                class="student-checkbox checkbox-modern"
                                                aria-label="Select student"
                                            >

                                        </td>


                                        {{-- Admission --}}
                                        <td>

                                            <span class="fw-bold text-uppercase">

                                                {{
                                                    $student->admission_number
                                                }}

                                            </span>

                                        </td>


                                        {{-- Student --}}
                                        <td>

                                            <div class="student-info">

                                                @php

                                                    $imageName =
                                                        $student->image;

                                                    $imagePath =
                                                        storage_path(
                                                            'app/public/students/' .
                                                            $imageName
                                                        );

                                                    $avatarImage =
                                                        (
                                                            !empty($imageName)
                                                            &&
                                                            file_exists($imagePath)
                                                        )

                                                        ? asset(
                                                            'storage/students/' .
                                                            $imageName
                                                        )

                                                        : asset(
                                                            'storage/students/student.jpg'
                                                        );

                                                @endphp


                                                <img
                                                    src="{{ $avatarImage }}"
                                                    class="student-avatar-modern"
                                                    alt="Student profile"
                                                    loading="lazy"
                                                >


                                                <span class="student-name">

                                                    {{
                                                        ucwords(
                                                            strtolower(
                                                                $student->first_name
                                                            )
                                                        )
                                                    }}

                                                </span>

                                            </div>

                                        </td>


                                        {{-- Middle --}}
                                        <td class="text-capitalize">

                                            {{
                                                ucwords(
                                                    strtolower(
                                                        $student->middle_name
                                                    )
                                                )
                                            }}

                                        </td>


                                        {{-- Surname --}}
                                        <td class="text-capitalize">

                                            {{
                                                ucwords(
                                                    strtolower(
                                                        $student->last_name
                                                    )
                                                )
                                            }}

                                        </td>


                                        {{-- Gender --}}
                                        <td class="text-center">

                                            <span
                                                class="gender-badge {{
                                                    strtolower($student->gender) === 'male'
                                                        ? 'gender-male'
                                                        : 'gender-female'
                                                }}"
                                                title="{{ ucfirst($student->gender) }}"
                                            >

                                                {{
                                                    strtoupper(
                                                        substr(
                                                            $student->gender,
                                                            0,
                                                            1
                                                        )
                                                    )
                                                }}

                                            </span>

                                        </td>


                                        {{-- Stream --}}
                                        <td class="text-center">

                                            <span
                                                class="stream-badge stream-{{
                                                    strtoupper($student->group)
                                                }}"
                                            >

                                                {{
                                                    strtoupper(
                                                        $student->group
                                                    )
                                                }}

                                            </span>

                                        </td>


                                        {{-- DOB --}}
                                        <td>

                                            {{
                                                \Carbon\Carbon::parse(
                                                    $student->dob
                                                )->format('d M Y')
                                            }}

                                        </td>


                                        {{-- Actions --}}
                                        <td class="text-center">

                                            <div class="action-icons">


                                                {{-- Edit --}}
                                                <a
                                                    href="{{ route(
                                                        'students.modify',
                                                        [
                                                            'students' =>
                                                                Hashids::encode(
                                                                    $student->id
                                                                )
                                                        ]
                                                    ) }}"
                                                    class="action-icon edit"
                                                    title="Edit Student"
                                                    aria-label="Edit Student"
                                                >

                                                    <i class="fas fa-pen"></i>

                                                </a>


                                                {{-- View --}}
                                                <a
                                                    href="{{ route(
                                                        'manage.student.profile',
                                                        [
                                                            'student' =>
                                                                Hashids::encode(
                                                                    $student->id
                                                                )
                                                        ]
                                                    ) }}"
                                                    class="action-icon view"
                                                    title="View Student"
                                                    aria-label="View Student"
                                                >

                                                    <i class="fas fa-eye"></i>

                                                </a>


                                                {{-- Delete --}}
                                                @if (auth()->user()->usertype != 5)

                                                    <form
                                                        method="POST"
                                                        action="{{ route(
                                                            'Students.destroy',
                                                            [
                                                                'student' =>
                                                                    Hashids::encode(
                                                                        $student->id
                                                                    )
                                                            ]
                                                        ) }}"
                                                        class="d-inline"
                                                        onsubmit="return confirm(
                                                            'Move {{ strtoupper($student->first_name) }} to trash?'
                                                        )"
                                                    >

                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="action-icon delete"
                                                            title="Delete Student"
                                                            aria-label="Delete Student"
                                                        >

                                                            <i class="fas fa-trash"></i>

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
         PROMOTE STUDENTS MODAL
         ========================================================= --}}

    <div
        class="modal fade modal-modern"
        id="promoteModal"
        tabindex="-1"
        aria-labelledby="promoteModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">


                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="promoteModalLabel"
                    >

                        <i class="fas fa-exchange-alt me-2"></i>

                        Promote Students

                    </h5>


                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>


                <div class="modal-body">

                    <p class="text-muted small mb-3">
                        Select class to promote students to
                    </p>


                    <form
                        class="needs-validation"
                        novalidate
                        action="{{ route(
                            'promote.student.class',
                            [
                                'class' =>
                                    Hashids::encode(
                                        $classId->id
                                    )
                            ]
                        ) }}"
                        method="POST"
                    >

                        @csrf

                        @method('PUT')


                        <div class="form-group-modern">

                            <label class="form-label-modern">

                                Class Name

                                <span class="required">
                                    *
                                </span>

                            </label>


                            <select
                                name="class_id"
                                id="classSelect"
                                class="form-control-modern"
                                required
                            >

                                <option value="">
                                    -- Select Class --
                                </option>


                                @if ($classes->isEmpty())

                                    <option
                                        value=""
                                        disabled
                                    >
                                        No more classes found
                                    </option>

                                @else

                                    @foreach ($classes as $class)

                                        <option
                                            value="{{ $class->id }}"
                                        >

                                            {{ $class->class_name }}

                                        </option>

                                    @endforeach

                                @endif


                                <option
                                    value="0"
                                    class="text-success fw-bold"
                                >

                                    🎓 Graduate Class 🎉

                                </option>

                            </select>

                        </div>


                        {{-- Graduation --}}
                        <div
                            id="graduationYearField"
                            class="form-group-modern"
                            style="display:none;"
                        >

                            <label class="form-label-modern">

                                Graduation Year

                                <span class="required">
                                    *
                                </span>

                            </label>


                            <input
                                type="number"
                                name="graduation_year"
                                id="graduation_year"
                                class="form-control-modern"
                                placeholder="e.g. 2025"
                                min="{{ date('Y') - 5 }}"
                                max="{{ date('Y') }}"
                            >


                            <div class="note-text">
                                Enter graduation year
                            </div>

                        </div>


                        <div class="modal-footer px-0 pb-0">

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
                            >
                                Upgrade
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ADD STUDENT MODAL
         ========================================================= --}}

    <div
        class="modal fade modal-modern"
        id="addStudentModal"
        tabindex="-1"
        aria-labelledby="addStudentModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">


                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="addStudentModalLabel"
                    >

                        <i class="fas fa-user-plus me-2"></i>

                        Add New Student -
                        {{ $classId->class_name }}

                    </h5>


                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>


                <div class="modal-body">

                    <form
                        class="needs-validation"
                        novalidate
                        action="{{ route(
                            'student.store',
                            [
                                'class' =>
                                    Hashids::encode(
                                        $classId->id
                                    )
                            ]
                        ) }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        @csrf


                        {{-- =========================================
                             NAMES
                             ========================================= --}}

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
                                        class="form-control-modern"
                                        name="fname"
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
                                        class="form-control-modern"
                                        name="middle"
                                        autocomplete="additional-name"
                                        required
                                    >


                                    @error('middle')

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
                                        class="form-control-modern"
                                        name="lname"
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

                        </div>


                        {{-- =========================================
                             PERSONAL
                             ========================================= --}}

                        <div class="row g-2">


                            {{-- Gender --}}
                            <div class="col-md-4">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">

                                        Gender

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        class="form-control-modern"
                                        name="gender"
                                        required
                                    >

                                        <option value="">
                                            -- Select --
                                        </option>

                                        <option value="male">
                                            Male
                                        </option>

                                        <option value="female">
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


                            {{-- DOB --}}
                            <div class="col-md-4">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">

                                        Date of Birth

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <input
                                        type="date"
                                        class="form-control-modern"
                                        name="dob"
                                        required
                                    >


                                    @error('dob')

                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>

                                    @enderror

                                </div>

                            </div>


                            {{-- Parent --}}
                            <div class="col-md-4">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">

                                        Parent/Guardian

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        name="parent"
                                        id="parentSelect"
                                        class="form-control-modern"
                                        required
                                    >

                                        <option value="">
                                            -- Select --
                                        </option>


                                        @foreach ($parents as $parent)

                                            <option
                                                value="{{ $parent->id }}"
                                            >

                                                {{
                                                    ucwords(
                                                        $parent->first_name .
                                                        ' ' .
                                                        $parent->last_name
                                                    )
                                                }}

                                                -
                                                {{ $parent->phone }}

                                            </option>

                                        @endforeach

                                    </select>


                                    @error('parent')

                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>

                                    @enderror

                                </div>

                            </div>

                        </div>


                        {{-- =========================================
                             SCHOOL DETAILS
                             ========================================= --}}

                        <div class="row g-2">


                            {{-- Stream --}}
                            <div class="col-md-4">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">

                                        Stream

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        class="form-control-modern"
                                        name="group"
                                        required
                                    >

                                        <option value="">
                                            -- Select --
                                        </option>

                                        <option value="a">
                                            Stream A
                                        </option>

                                        <option value="b">
                                            Stream B
                                        </option>

                                        <option value="c">
                                            Stream C
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
                            <div class="col-md-4">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">

                                        Bus Number

                                    </label>


                                    <select
                                        name="driver"
                                        class="form-control-modern"
                                    >

                                        <option value="">
                                            -- Select --
                                        </option>


                                        @foreach ($buses as $bus)

                                            <option
                                                value="{{ $bus->id }}"
                                            >

                                                Bus No.
                                                {{ $bus->bus_no }}

                                            </option>

                                        @endforeach

                                    </select>


                                    <div class="note-text">
                                        Optional - if using school bus
                                    </div>


                                    @error('driver')

                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>

                                    @enderror

                                </div>

                            </div>


                            {{-- Photo --}}
                            <div class="col-md-4">

                                <div class="form-group-modern">

                                    <label class="form-label-modern">

                                        Photo

                                    </label>


                                    <input
                                        type="file"
                                        class="form-control-modern"
                                        name="image"
                                        accept="image/*"
                                    >


                                    <div class="note-text">
                                        Optional - Max 2MB
                                    </div>


                                    @error('image')

                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>

                                    @enderror

                                </div>

                            </div>

                        </div>


                        {{-- Footer --}}
                        <div class="modal-footer px-0 pb-0">

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
                            >

                                Save Student

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<script
    src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
></script>


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
     * =========================================================
     * PARTICLES
     * =========================================================
     */

    const particleContainer =
        $('.students-page .particles');


    if (particleContainer) {

        const fragment =
            document.createDocumentFragment();


        for (let i = 0; i < 20; i++) {

            const particle =
                document.createElement('div');


            particle.className =
                'particle';


            const size =
                Math.random() * 10 + 3;


            particle.style.width =
                `${size}px`;


            particle.style.height =
                `${size}px`;


            particle.style.left =
                `${Math.random() * 100}%`;


            particle.style.top =
                `${Math.random() * 100}%`;


            particle.style.animationDelay =
                `${Math.random() * 20}s`;


            particle.style.animationDuration =
                `${Math.random() * 10 + 15}s`;


            fragment.appendChild(
                particle
            );

        }


        particleContainer.appendChild(
            fragment
        );

    }


    /*
     * =========================================================
     * SELECT2
     * =========================================================
     */

    const parentSelect =
        $('#parentSelect');


    if (
        parentSelect &&
        typeof window.jQuery !== 'undefined' &&
        typeof window.jQuery.fn.select2 === 'function'
    ) {

        window.jQuery(parentSelect).select2({

            placeholder:
                'Search Parent...',

            allowClear: true,

            dropdownParent:
                window.jQuery('#addStudentModal'),

            width: '100%'

        });

    }


    /*
     * =========================================================
     * BATCH UPDATE
     * =========================================================
     */

    const batchForm =
        $('#batchForm');

    const updateStreamBtn =
        $('#updateStreamBtn');

    const selectAllCheckbox =
        $('#selectAll');

    const studentCheckboxes =
        $$('.student-checkbox');

    const selectedCountDiv =
        $('#selectedCount');


    function updateSelectionState() {

        const selectedStudents =
            $$('.student-checkbox:checked');


        const count =
            selectedStudents.length;


        if (selectedCountDiv) {

            selectedCountDiv.innerHTML = `
                <i class="fas fa-users"></i>

                <span>
                    ${count}
                    student${count !== 1 ? 's' : ''}
                    selected
                </span>
            `;

        }


        if (updateStreamBtn) {

            updateStreamBtn.disabled =
                count === 0;

        }


        if (selectAllCheckbox) {

            selectAllCheckbox.checked =
                count > 0 &&
                count === studentCheckboxes.length;


            selectAllCheckbox.indeterminate =
                count > 0 &&
                count < studentCheckboxes.length;

        }

    }


    /*
     * Select all
     */

    if (selectAllCheckbox) {

        selectAllCheckbox.addEventListener(
            'change',
            function () {

                studentCheckboxes.forEach(
                    checkbox => {

                        checkbox.checked =
                            this.checked;

                    }
                );


                updateSelectionState();

            }
        );

    }


    /*
     * Individual checkboxes
     */

    studentCheckboxes.forEach(
        checkbox => {

            checkbox.addEventListener(
                'change',
                updateSelectionState
            );

        }
    );


    /*
     * Batch submit
     */

    if (batchForm) {

        batchForm.addEventListener(
            'submit',
            function (event) {

                event.preventDefault();


                const selected =
                    $$('.student-checkbox:checked');


                const streamSelect =
                    $('select[name="new_stream"]');


                const newStream =
                    streamSelect?.value;


                if (selected.length === 0) {

                    alert(
                        'Select at least one student.'
                    );

                    return;

                }


                if (!newStream) {

                    alert(
                        'Select a stream.'
                    );

                    return;

                }


                const confirmed =
                    confirm(
                        `Move ${selected.length} student(s) to Stream ${newStream}?`
                    );


                if (!confirmed) {
                    return;
                }


                /*
                 * Remove previously generated hidden inputs.
                 *
                 * This prevents duplicate students if the user
                 * submits the batch form more than once.
                 */

                $$(
                    'input[data-batch-student]',
                    batchForm
                ).forEach(
                    input => input.remove()
                );


                selected.forEach(
                    checkbox => {

                        const input =
                            document.createElement('input');


                        input.type =
                            'hidden';


                        input.name =
                            'students[]';


                        input.value =
                            checkbox.value;


                        input.dataset.batchStudent =
                            'true';


                        batchForm.appendChild(
                            input
                        );

                    }
                );


                const submitButton =
                    $('#updateStreamBtn');


                if (submitButton) {

                    submitButton.disabled =
                        true;


                    submitButton.innerHTML = `
                        <span
                            class="spinner-border spinner-border-sm me-1"
                            role="status"
                            aria-hidden="true"
                        ></span>

                        Updating...
                    `;

                }


                batchForm.submit();

            }
        );

    }


    /*
     * =========================================================
     * PROMOTION / GRADUATION
     * =========================================================
     */

    const classSelect =
        $('#classSelect');

    const gradYearField =
        $('#graduationYearField');

    const gradYearInput =
        $('#graduation_year');


    function updateGraduationField() {

        if (
            !classSelect ||
            !gradYearField ||
            !gradYearInput
        ) {

            return;

        }


        const isGraduating =
            classSelect.value === '0';


        gradYearField.style.display =
            isGraduating
                ? 'block'
                : 'none';


        if (isGraduating) {

            gradYearInput.setAttribute(
                'required',
                'required'
            );

        } else {

            gradYearInput.removeAttribute(
                'required'
            );

            gradYearInput.value =
                '';

        }

    }


    if (classSelect) {

        classSelect.addEventListener(
            'change',
            updateGraduationField
        );

    }


    /*
     * =========================================================
     * FORM VALIDATION
     * =========================================================
     */

    $$('.needs-validation').forEach(
        function (form) {

            form.addEventListener(
                'submit',
                function (event) {


                    /*
                     * Keep graduation field state synchronized.
                     */

                    updateGraduationField();


                    if (!form.checkValidity()) {

                        event.preventDefault();

                        event.stopPropagation();

                        form.classList.add(
                            'was-validated'
                        );

                        return;

                    }


                    /*
                     * Prevent duplicate submissions.
                     */

                    const submitButtons =
                        $$(
                            'button[type="submit"]',
                            form
                        );


                    submitButtons.forEach(
                        button => {

                            button.disabled =
                                true;


                            button.innerHTML = `
                                <span
                                    class="spinner-border spinner-border-sm me-2"
                                    role="status"
                                    aria-hidden="true"
                                ></span>

                                Saving...
                            `;

                        }
                    );

                    form.classList.add(
                        'was-validated'
                    );

                }
            );

        }
    );


    /*
     * =========================================================
     * INITIAL STATE
     * =========================================================
     */

    updateSelectionState();

    updateGraduationField();

});
</script>

@endsection