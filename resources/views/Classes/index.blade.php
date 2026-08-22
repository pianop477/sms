@extends('SRTDashboard.frame')

@section('content')

<style>
    /* =========================================================
       CLASS MANAGEMENT PAGE
       Scoped to .class-management-page
       ========================================================= */

    .class-management-page {

        --cm-primary: #4361ee;
        --cm-primary-dark: #3a56d4;
        --cm-secondary: #3f37c9;
        --cm-accent: #4895ef;

        --cm-success: #1cc88a;
        --cm-warning: #f8961e;
        --cm-danger: #e74a3b;

        --cm-dark: #212529;
        --cm-muted: #64748b;
        --cm-border: #e2e8f0;
        --cm-light: #f8fafc;

        width: 100%;
        min-width: 0;

        position: relative;
        isolation: isolate;
    }


    /* =========================================================
       BACKGROUND
       ========================================================= */

    .class-management-page .animated-bg {

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


    .class-management-page .animated-bg::before {

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
            classManagementRotate 60s linear infinite;
    }


    @keyframes classManagementRotate {

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

    .class-management-page .particles {

        position: fixed;

        inset: 0;

        width: 100%;
        height: 100%;

        pointer-events: none;

        z-index: -1;

        overflow: hidden;
    }


    .class-management-page .particle {

        position: absolute;

        background:
            rgba(255,255,255,.45);

        border-radius: 50%;

        animation:
            classManagementFloat 20s infinite;
    }


    @keyframes classManagementFloat {

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

    .class-management-page .dashboard-container {

        width: min(100%, 1500px);

        margin: 24px auto;

        padding-inline: 20px;

        position: relative;

        z-index: 1;
    }


    /* =========================================================
       MODERN CARD
       ========================================================= */

    .class-management-page .modern-card {

        width: 100%;

        height: 100%;

        position: relative;

        overflow: hidden;

        background:
            rgba(255,255,255,.96);

        backdrop-filter:
            blur(16px);

        -webkit-backdrop-filter:
            blur(16px);

        border:
            1px solid
            rgba(255,255,255,.65);

        border-radius: 22px;

        box-shadow:
            0 12px 32px
            rgba(15,23,42,.08),
            0 2px 8px
            rgba(15,23,42,.04);

        transition:
            box-shadow .3s ease,
            transform .3s ease;
    }


    .class-management-page .modern-card:hover {

        transform:
            translateY(-3px);

        box-shadow:
            0 18px 40px
            rgba(15,23,42,.12);
    }


    /* =========================================================
       HEADER
       ========================================================= */

    .class-management-page .card-header-modern {

        min-height: 82px;

        padding: 18px 22px;

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 16px;

        position: relative;

        overflow: hidden;

        color: #fff;
    }


    .class-management-page
    .card-header-modern::before {

        content: "";

        position: absolute;

        inset: 0;

        pointer-events: none;

        background:
            radial-gradient(
                circle at 85% 20%,
                rgba(255,255,255,.15),
                transparent 25%
            );
    }


    .class-management-page
    .card-header-modern::after {

        content: "";

        position: absolute;

        left: 0;
        right: 0;
        bottom: 0;

        height: 2px;

        background:
            linear-gradient(
                90deg,
                rgba(255,255,255,.15),
                rgba(255,255,255,.8),
                rgba(255,255,255,.15)
            );
    }


    .class-management-page .gradient-success {

        background:
            linear-gradient(
                135deg,
                #1cc88a,
                #13855c
            );
    }


    .class-management-page .gradient-primary {

        background:
            linear-gradient(
                135deg,
                #4e73df,
                #224abe
            );
    }


    .class-management-page .header-left {

        min-width: 0;

        display: flex;

        align-items: center;

        gap: 12px;

        position: relative;

        z-index: 2;
    }


    .class-management-page .header-icon {

        width: 46px;
        height: 46px;

        min-width: 46px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 12px;

        background:
            rgba(255,255,255,.16);

        border:
            1px solid
            rgba(255,255,255,.22);

        color: #fff;

        font-size: 20px;
    }


    .class-management-page .header-title {

        margin: 0;

        color: #fff;

        font-size:
            clamp(
                1rem,
                2vw,
                1.25rem
            );

        font-weight: 700;

        line-height: 1.3;
    }


    .class-management-page .header-title span {

        color: inherit;
    }


    /* =========================================================
       ADD BUTTON
       ========================================================= */

    .class-management-page .btn-add-modern {

        min-height: 40px;

        padding: 9px 15px;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 7px;

        position: relative;

        z-index: 3;

        border:
            1px solid
            rgba(255,255,255,.35);

        border-radius: 9px;

        background:
            rgba(255,255,255,.16);

        color: #fff;

        font-size: .82rem;

        font-weight: 600;

        white-space: nowrap;

        cursor: pointer;

        transition:
            transform .2s ease,
            background-color .2s ease,
            box-shadow .2s ease;
    }


    .class-management-page .btn-add-modern:hover {

        color: #fff;

        background:
            rgba(255,255,255,.25);

        transform:
            translateY(-1px);

        box-shadow:
            0 6px 15px
            rgba(0,0,0,.12);
    }


    /* =========================================================
       BODY
       ========================================================= */

    .class-management-page .card-body-modern {

        padding: 20px;
    }


    /* =========================================================
       TABLE CONTAINER
       ========================================================= */

    .class-management-page
    .table-container-modern {

        width: 100%;

        overflow-x: auto;

        overflow-y: hidden;

        background: #fff;

        border:
            1px solid
            var(--cm-border);

        border-radius: 14px;

        box-shadow:
            0 4px 14px
            rgba(15,23,42,.05);

        -webkit-overflow-scrolling:
            touch;
    }


    /* =========================================================
       TABLE
       ========================================================= */

    .class-management-page .table-modern {

        width: 100%;

        margin: 0;

        border-collapse: separate;

        border-spacing: 0;

        font-size: .83rem;
    }


    .class-management-page
    .table-modern thead th {

        padding: 12px 13px;

        background:
            linear-gradient(
                135deg,
                var(--cm-primary),
                var(--cm-secondary)
            );

        color: #fff;

        border: 0;

        font-size: .7rem;

        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: .45px;

        white-space: nowrap;

        vertical-align: middle;
    }


    .class-management-page
    .table-modern tbody td {

        padding: 11px 13px;

        background: #fff;

        color: #475569;

        border-bottom:
            1px solid #edf2f7;

        vertical-align: middle;
    }


    .class-management-page
    .table-modern tbody tr:last-child td {

        border-bottom: 0;
    }


    .class-management-page
    .table-modern tbody tr {

        transition:
            background-color .18s ease;
    }


    .class-management-page
    .table-modern tbody tr:hover td {

        background: #f8fafc;
    }


    /* =========================================================
       CLASS NAME
       ========================================================= */

    .class-management-page .class-name-modern {

        display: flex;

        align-items: center;

        gap: 9px;

        min-width: 150px;
    }


    .class-management-page .class-icon {

        width: 38px;
        height: 38px;

        min-width: 38px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 9px;

        background:
            linear-gradient(
                135deg,
                #4facfe,
                #00c6fb
            );

        color: #fff;

        font-size: 16px;
    }


    .class-management-page .class-details {

        min-width: 0;
    }


    .class-management-page
    .class-details h6 {

        margin: 0;

        color:
            var(--cm-dark);

        font-size: .82rem;

        font-weight: 700;

        overflow: hidden;

        text-overflow: ellipsis;

        white-space: nowrap;
    }


    /* =========================================================
       CODE BADGE
       ========================================================= */

    .class-management-page .code-badge {

        display: inline-block;

        padding: 5px 9px;

        border-radius: 7px;

        background:
            linear-gradient(
                135deg,
                #667eea,
                #764ba2
            );

        color: #fff;

        font-size: .7rem;

        font-weight: 700;

        letter-spacing: .6px;

        white-space: nowrap;
    }


    /* =========================================================
       ACTION BUTTONS
       ========================================================= */

    .class-management-page .action-group {

        display: flex;

        align-items: center;

        justify-content: center;

        gap: 6px;

        flex-wrap: nowrap;
    }


    .class-management-page
    .btn-action-modern {

        width: 32px;
        height: 32px;

        min-width: 32px;

        padding: 0;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        border: 0;

        border-radius: 8px;

        color: #fff;

        font-size: .75rem;

        cursor: pointer;

        transition:
            transform .18s ease,
            box-shadow .18s ease;
    }


    .class-management-page
    .btn-action-modern:hover {

        color: #fff;

        transform:
            translateY(-1px);

        box-shadow:
            0 4px 10px
            rgba(15,23,42,.15);
    }


    .class-management-page
    .btn-warning-modern {

        background:
            linear-gradient(
                135deg,
                #f6c23e,
                #f4b619
            );
    }


    .class-management-page
    .btn-success-modern {

        background:
            linear-gradient(
                135deg,
                #1cc88a,
                #17a673
            );
    }


    .class-management-page
    .btn-danger-modern {

        background:
            linear-gradient(
                135deg,
                #e74a3b,
                #be2617
            );
    }


    /* =========================================================
       TEACHER LINK
       ========================================================= */

    .class-management-page .class-link-modern {

        min-height: 50px;

        padding: 7px 10px;

        display: flex;

        align-items: center;

        gap: 10px;

        border:
            1px solid
            #e9eef5;

        border-radius: 10px;

        background: #fff;

        text-decoration: none;

        transition:
            background-color .2s ease,
            transform .2s ease,
            box-shadow .2s ease;
    }


    .class-management-page
    .class-link-modern:hover {

        transform:
            translateX(3px);

        background:
            linear-gradient(
                135deg,
                var(--cm-primary),
                var(--cm-secondary)
            );

        box-shadow:
            0 6px 15px
            rgba(67,97,238,.16);
    }


    .class-management-page
    .link-icon {

        width: 34px;
        height: 34px;

        min-width: 34px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 8px;

        background:
            linear-gradient(
                135deg,
                var(--cm-primary),
                var(--cm-secondary)
            );

        color: #fff;

        font-size: 14px;
    }


    .class-management-page
    .class-link-modern:hover .link-icon {

        background:
            rgba(255,255,255,.18);
    }


    .class-management-page
    .class-link-text {

        min-width: 0;

        flex: 1;

        color:
            var(--cm-dark);

        font-size: .8rem;

        font-weight: 600;

        overflow: hidden;

        text-overflow: ellipsis;

        white-space: nowrap;

        transition:
            color .2s ease;
    }


    .class-management-page
    .class-link-modern:hover
    .class-link-text {

        color: #fff;
    }


    .class-management-page
    .class-link-arrow {

        flex-shrink: 0;

        color:
            var(--cm-primary);

        font-size: .75rem;

        transition:
            color .2s ease,
            transform .2s ease;
    }


    .class-management-page
    .class-link-modern:hover
    .class-link-arrow {

        color: #fff;

        transform:
            translateX(3px);
    }


    /* =========================================================
       EMPTY STATE
       ========================================================= */

    .class-management-page .empty-state-modern {

        padding: 35px 15px;

        text-align: center;

        background:
            linear-gradient(
                135deg,
                #fff8e1,
                #fff3cd
            );

        border:
            2px dashed
            #f6c23e;

        border-radius: 13px;
    }


    .class-management-page
    .empty-state-modern i {

        margin-bottom: 8px;

        color: #f6c23e;

        font-size: 38px;

        animation:
            classManagementBounce 2s infinite;
    }


    @keyframes classManagementBounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-8px);
        }

    }


    .class-management-page
    .empty-state-modern h6 {

        margin-bottom: 5px;

        color:
            var(--cm-dark);

        font-weight: 700;
    }


    .class-management-page
    .empty-state-modern p {

        margin-bottom: 0;

        font-size: .78rem;
    }


    /* =========================================================
       MODAL
       ========================================================= */

    .class-management-page .modal-modern
    .modal-content {

        border: 0;

        border-radius: 18px;

        overflow: hidden;

        box-shadow:
            0 20px 50px
            rgba(15,23,42,.20);
    }


    .class-management-page .modal-modern
    .modal-header {

        padding: 15px 20px;

        border: 0;

        color: #fff;

        background:
            linear-gradient(
                135deg,
                var(--cm-primary),
                var(--cm-secondary)
            );
    }


    .class-management-page
    .modal-modern
    .modal-title {

        font-size: 1rem;

        font-weight: 700;
    }


    .class-management-page
    .btn-modal-close {

        width: 34px;
        height: 34px;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        padding: 0;

        border:
            1px solid
            rgba(255,255,255,.25);

        border-radius: 8px;

        background:
            rgba(255,255,255,.12);

        color: #fff;

        cursor: pointer;

        transition:
            transform .2s ease,
            background-color .2s ease;
    }


    .class-management-page
    .btn-modal-close:hover {

        background:
            rgba(255,255,255,.22);

        transform:
            rotate(90deg);
    }


    .class-management-page
    .modal-modern
    .modal-body {

        padding: 20px;
    }


    .class-management-page
    .modal-modern
    .modal-footer {

        padding: 13px 20px;

        border: 0;

        background:
            #f8fafc;
    }


    /* =========================================================
       FORM
       ========================================================= */

    .class-management-page
    .form-group-modern {

        margin-bottom: 13px;
    }


    .class-management-page
    .form-label-modern {

        display: block;

        margin-bottom: 5px;

        color:
            var(--cm-dark);

        font-size: .8rem;

        font-weight: 600;
    }


    .class-management-page
    .form-control-modern {

        width: 100%;

        min-height: 42px;

        padding: 9px 11px;

        border:
            1px solid
            #dbe3ec;

        border-radius: 8px;

        background: #fff;

        color:
            #334155;

        font-size: .84rem;

        outline: none;

        transition:
            border-color .2s ease,
            box-shadow .2s ease;
    }


    .class-management-page
    .form-control-modern:focus {

        border-color:
            var(--cm-primary);

        box-shadow:
            0 0 0 3px
            rgba(67,97,238,.10);

        outline: none;
    }


    .class-management-page
    .form-control-modern.error {

        border-color:
            var(--cm-danger);
    }


    .class-management-page
    .error-message {

        display: flex;

        align-items: flex-start;

        gap: 5px;

        margin-top: 4px;

        color:
            var(--cm-danger);

        font-size: .72rem;
    }


    .class-management-page
    .class-info-note {

        padding: 10px 12px;

        border-radius: 8px;

        background:
            #f8fafc;

        color:
            #64748b;

        font-size: .72rem;

        line-height: 1.5;
    }


    /* =========================================================
       MODAL BUTTONS
       ========================================================= */

    .class-management-page
    .btn-modal-save {

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
                #1cc88a,
                #17a673
            );

        color: #fff;

        font-size: .8rem;

        font-weight: 600;

        cursor: pointer;

        transition:
            transform .2s ease,
            box-shadow .2s ease;
    }


    .class-management-page
    .btn-modal-save:hover:not(:disabled) {

        transform:
            translateY(-1px);

        box-shadow:
            0 5px 15px
            rgba(28,200,138,.22);
    }


    .class-management-page
    .btn-modal-cancel {

        min-height: 40px;

        padding: 8px 15px;

        border: 0;

        border-radius: 8px;

        background:
            #64748b;

        color: #fff;

        font-size: .8rem;

        font-weight: 600;

        cursor: pointer;
    }


    .class-management-page
    .btn-modal-cancel:hover {

        background:
            #475569;

        color: #fff;
    }


    /* =========================================================
       LOADING
       ========================================================= */

    .class-management-page .loading-spinner {

        position: fixed;

        top: 50%;
        left: 50%;

        width: 56px;
        height: 56px;

        display: none;

        transform:
            translate(-50%, -50%);

        border:
            4px solid
            rgba(67,97,238,.15);

        border-top-color:
            var(--cm-primary);

        border-radius: 50%;

        animation:
            classManagementSpin 1s linear infinite;

        z-index: 9999;
    }


    @keyframes classManagementSpin {

        to {
            transform:
                translate(-50%, -50%)
                rotate(360deg);
        }

    }


    /* =========================================================
       TOAST
       ========================================================= */

    .class-management-page .toast-notification {

        position: fixed;

        top: 18px;
        right: 18px;

        max-width: min(360px, calc(100vw - 36px));

        padding: 12px 15px;

        display: flex;

        align-items: center;

        gap: 10px;

        border-left:
            4px solid;

        border-radius: 10px;

        background: #fff;

        box-shadow:
            0 12px 30px
            rgba(15,23,42,.15);

        transform:
            translateX(120%);

        transition:
            transform .25s ease;

        z-index: 10000;

        font-size: .8rem;
    }


    .class-management-page
    .toast-notification.show {

        transform:
            translateX(0);
    }


    .class-management-page
    .toast-success {

        border-left-color:
            #28a745;
    }


    .class-management-page
    .toast-error {

        border-left-color:
            var(--cm-danger);
    }


    /* =========================================================
       TABLET
       ========================================================= */

    @media (max-width: 991.98px) {

        .class-management-page
        .dashboard-container {

            margin: 18px auto;

            padding-inline: 15px;
        }


        .class-management-page
        .modern-card {

            border-radius: 18px;
        }


        .class-management-page
        .card-header-modern {

            min-height: auto;

            padding: 15px 17px;
        }


        .class-management-page
        .card-body-modern {

            padding: 16px;
        }

    }


    /* =========================================================
       MOBILE
       ========================================================= */

    @media (max-width: 767.98px) {

        .class-management-page
        .dashboard-container {

            margin: 10px auto;

            padding-inline: 9px;
        }


        .class-management-page
        .modern-card {

            border-radius: 15px;

            box-shadow:
                0 5px 18px
                rgba(15,23,42,.07);
        }


        .class-management-page
        .modern-card:hover {

            transform: none;
        }


        .class-management-page
        .card-header-modern {

            padding: 13px;

            flex-direction: column;

            align-items: stretch;

            gap: 12px;
        }


        .class-management-page
        .header-left {

            width: 100%;
        }


        .class-management-page
        .header-icon {

            width: 39px;
            height: 39px;

            min-width: 39px;

            border-radius: 9px;

            font-size: 16px;
        }


        .class-management-page
        .header-title {

            font-size: .98rem;
        }


        .class-management-page
        .btn-add-modern {

            width: 100%;

            min-height: 40px;
        }


        .class-management-page
        .card-body-modern {

            padding: 10px;
        }


        /* =====================================================
           TABLE → CARD LIST
           ===================================================== */

        .class-management-page
        .table-container-modern {

            overflow: visible;

            border: 0;

            background: transparent;

            box-shadow: none;
        }


        .class-management-page
        .table-modern {

            display: block;

            width: 100%;
        }


        .class-management-page
        .table-modern thead {

            display: none;
        }


        .class-management-page
        .table-modern tbody {

            display: flex;

            flex-direction: column;

            gap: 9px;
        }


        .class-management-page
        .table-modern tbody tr {

            display: grid;

            grid-template-columns:
                1fr auto;

            gap: 0;

            padding: 11px;

            border:
                1px solid
                #e5eaf0;

            border-radius: 11px;

            background: #fff;

            box-shadow:
                0 3px 10px
                rgba(15,23,42,.045);
        }


        .class-management-page
        .table-modern tbody tr:hover {

            transform: none;
        }


        .class-management-page
        .table-modern tbody td {

            display: flex;

            align-items: center;

            min-width: 0;

            padding: 5px 0;

            border: 0;

            background: transparent;

            white-space: normal;
        }


        /* Class name */

        .class-management-page
        .table-modern tbody td:first-child {

            grid-column: 1;

            grid-row: 1;

            padding-right: 8px;
        }


        .class-management-page
        .table-modern tbody td:first-child
        .class-name-modern {

            width: 100%;

            min-width: 0;
        }


        /* Code */

        .class-management-page
        .table-modern tbody td:nth-child(2) {

            grid-column: 2;

            grid-row: 1;

            justify-content: flex-end;

            align-self: center;
        }


        /* Actions */

        .class-management-page
        .table-modern tbody td:last-child {

            grid-column:
                1 / -1;

            grid-row: 2;

            justify-content:
                flex-end;

            margin-top: 7px;

            padding-top: 9px;

            border-top:
                1px solid
                #edf2f7;
        }


        .class-management-page
        .table-modern tbody td:last-child::before {

            content: "Actions";

            margin-right: auto;

            color:
                #64748b;

            font-size: .68rem;

            font-weight: 600;
        }


        .class-management-page
        .action-group {

            justify-content: flex-end;
        }


        .class-management-page
        .btn-action-modern {

            width: 34px;
            height: 34px;

            min-width: 34px;

            border-radius: 8px;
        }


        /* =====================================================
           TEACHER LINKS
           ===================================================== */

        .class-management-page
        .class-link-modern {

            min-height: 48px;

            padding: 7px 9px;

            border-radius: 9px;
        }


        .class-management-page
        .link-icon {

            width: 32px;
            height: 32px;

            min-width: 32px;

            font-size: 13px;
        }


        .class-management-page
        .class-link-text {

            font-size: .76rem;
        }


        /* =====================================================
           EMPTY STATE
           ===================================================== */

        .class-management-page
        .empty-state-modern {

            padding: 30px 12px;
        }


        .class-management-page
        .empty-state-modern i {

            font-size: 34px;
        }


        /* =====================================================
           MODAL
           ===================================================== */

        .class-management-page
        .modal-modern
        .modal-dialog {

            margin: 10px;
        }


        .class-management-page
        .modal-modern
        .modal-content {

            border-radius: 14px;
        }


        .class-management-page
        .modal-modern
        .modal-header {

            padding: 13px 15px;
        }


        .class-management-page
        .modal-modern
        .modal-body {

            padding: 15px;
        }


        .class-management-page
        .modal-modern
        .modal-footer {

            padding: 11px 15px;
        }


        .class-management-page
        .form-control-modern {

            min-height: 44px;

            font-size: 16px;
        }

    }


    /* =========================================================
       SMALL PHONES
       ========================================================= */

    @media (max-width: 480px) {

        .class-management-page
        .dashboard-container {

            margin: 7px auto;

            padding-inline: 6px;
        }


        .class-management-page
        .card-header-modern {

            padding: 11px;
        }


        .class-management-page
        .card-body-modern {

            padding: 8px;
        }


        .class-management-page
        .header-icon {

            width: 36px;
            height: 36px;

            min-width: 36px;
        }


        .class-management-page
        .header-title {

            font-size: .88rem;
        }


        .class-management-page
        .table-modern tbody tr {

            padding: 9px;
        }


        .class-management-page
        .class-icon {

            width: 34px;
            height: 34px;

            min-width: 34px;

            border-radius: 8px;

            font-size: 14px;
        }


        .class-management-page
        .class-details h6 {

            max-width: 130px;

            font-size: .76rem;
        }


        .class-management-page
        .code-badge {

            padding: 4px 7px;

            font-size: .64rem;
        }


        .class-management-page
        .btn-action-modern {

            width: 32px;
            height: 32px;

            min-width: 32px;

            font-size: .7rem;
        }


        .class-management-page
        .modal-modern
        .modal-dialog {

            margin: 6px;
        }


        .class-management-page
        .modal-modern
        .modal-footer {

            flex-direction: column;

            gap: 7px;
        }


        .class-management-page
        .btn-modal-save,
        .class-management-page
        .btn-modal-cancel {

            width: 100%;
        }

    }


    /* =========================================================
       VERY SMALL PHONES
       ========================================================= */

    @media (max-width: 360px) {

        .class-management-page
        .dashboard-container {

            padding-inline: 4px;
        }


        .class-management-page
        .card-header-modern {

            padding: 9px;
        }


        .class-management-page
        .card-body-modern {

            padding: 6px;
        }


        .class-management-page
        .class-details h6 {

            max-width: 105px;

            font-size: .7rem;
        }


        .class-management-page
        .class-link-text {

            font-size: .7rem;
        }


        .class-management-page
        .class-link-arrow {

            font-size: .65rem;
        }

    }


    /* =========================================================
       REDUCED MOTION
       ========================================================= */

    @media (prefers-reduced-motion: reduce) {

        .class-management-page
        .animated-bg::before,
        .class-management-page
        .particle,
        .class-management-page
        .empty-state-modern i {

            animation: none;
        }

        .class-management-page *,
        .class-management-page *::before,
        .class-management-page *::after {

            transition-duration:
                .01ms !important;

            animation-duration:
                .01ms !important;
        }

    }
</style>


<div class="class-management-page">

    {{-- Background --}}
    <div class="animated-bg"></div>

    <div class="particles"></div>

    <div class="loading-spinner" id="loadingSpinner"></div>


    <div class="dashboard-container">

        <div class="row g-3">


            {{-- =====================================================
                 CLASS MANAGEMENT
                 ===================================================== --}}

            <div class="col-lg-6">

                <div class="modern-card">


                    {{-- Header --}}
                    <div class="card-header-modern gradient-success">

                        <div class="header-left">

                            <div class="header-icon">

                                <i class="fas fa-layer-group"></i>

                            </div>


                            <h3 class="header-title">

                                <span>
                                    Class
                                </span>

                                Management

                            </h3>

                        </div>


                        <button
                            type="button"
                            class="btn-add-modern"
                            data-bs-toggle="modal"
                            data-bs-target="#addClassModal"
                        >

                            <i class="fas fa-plus"></i>

                            <span>
                                New Class
                            </span>

                        </button>

                    </div>


                    {{-- Body --}}
                    <div class="card-body-modern">

                        <div class="table-container-modern">

                            <table class="table-modern">

                                <thead>

                                    <tr>

                                        <th>
                                            Class Name
                                        </th>

                                        <th>
                                            Class Code
                                        </th>

                                        <th class="text-center">
                                            Actions
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>


                                    @if ($classes->isEmpty())

                                        <tr>

                                            <td
                                                colspan="3"
                                                class="text-center py-5"
                                            >

                                                <div class="empty-state-modern">

                                                    <i class="fas fa-layer-group"></i>

                                                    <h6 class="mt-2">
                                                        No Classes Found
                                                    </h6>

                                                    <p class="text-muted">
                                                        Click "New Class" to create your first class
                                                    </p>

                                                </div>

                                            </td>

                                        </tr>


                                    @else


                                        @foreach ($classes as $class)

                                            <tr>


                                                {{-- Class --}}
                                                <td>

                                                    <div class="class-name-modern">

                                                        <div class="class-icon">

                                                            <i class="fas fa-graduation-cap"></i>

                                                        </div>


                                                        <div class="class-details">

                                                            <h6>

                                                                {{
                                                                    strtoupper(
                                                                        $class->class_name
                                                                    )
                                                                }}

                                                            </h6>

                                                        </div>

                                                    </div>

                                                </td>


                                                {{-- Code --}}
                                                <td>

                                                    <span class="code-badge">

                                                        {{
                                                            strtoupper(
                                                                $class->class_code
                                                            )
                                                        }}

                                                    </span>

                                                </td>


                                                {{-- Actions --}}
                                                <td>

                                                    <div class="action-group">


                                                        @if ($class->status == 1)


                                                            {{-- Disable --}}
                                                            <form
                                                                action="{{ route(
                                                                    'Classes.block',
                                                                    [
                                                                        'id' =>
                                                                            Hashids::encode(
                                                                                $class->id
                                                                            )
                                                                    ]
                                                                ) }}"
                                                                method="POST"
                                                                class="d-inline"
                                                                data-confirm-action="disable"
                                                                data-class-name="{{ strtoupper($class->class_name) }}"
                                                            >

                                                                @csrf

                                                                @method('PUT')


                                                                <button
                                                                    type="submit"
                                                                    class="btn-action-modern btn-warning-modern"
                                                                    title="Disable Class"
                                                                    aria-label="Disable Class"
                                                                >

                                                                    <i class="fas fa-ban"></i>

                                                                </button>

                                                            </form>


                                                        @else


                                                            {{-- Enable --}}
                                                            <form
                                                                action="{{ route(
                                                                    'Classes.unblock',
                                                                    [
                                                                        'id' =>
                                                                            Hashids::encode(
                                                                                $class->id
                                                                            )
                                                                    ]
                                                                ) }}"
                                                                method="POST"
                                                                class="d-inline"
                                                                data-confirm-action="enable"
                                                                data-class-name="{{ strtoupper($class->class_name) }}"
                                                            >

                                                                @csrf

                                                                @method('PUT')


                                                                <button
                                                                    type="submit"
                                                                    class="btn-action-modern btn-success-modern"
                                                                    title="Enable Class"
                                                                    aria-label="Enable Class"
                                                                >

                                                                    <i class="fas fa-check"></i>

                                                                </button>

                                                            </form>


                                                            {{-- Delete --}}
                                                            <form
                                                                action="{{ route(
                                                                    'Classes.destroy',
                                                                    [
                                                                        'id' =>
                                                                            Hashids::encode(
                                                                                $class->id
                                                                            )
                                                                    ]
                                                                ) }}"
                                                                method="POST"
                                                                class="d-inline"
                                                                data-confirm-action="delete"
                                                                data-class-name="{{ strtoupper($class->class_name) }}"
                                                            >

                                                                @csrf

                                                                @method('DELETE')


                                                                <button
                                                                    type="submit"
                                                                    class="btn-action-modern btn-danger-modern"
                                                                    title="Delete Class"
                                                                    aria-label="Delete Class"
                                                                >

                                                                    <i class="fas fa-trash"></i>

                                                                </button>

                                                            </form>

                                                        @endif

                                                    </div>

                                                </td>

                                            </tr>

                                        @endforeach

                                    @endif

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 CLASS TEACHERS
                 ===================================================== --}}

            <div class="col-lg-6">

                <div class="modern-card">


                    {{-- Header --}}
                    <div class="card-header-modern gradient-primary">

                        <div class="header-left">

                            <div class="header-icon">

                                <i class="fas fa-chalkboard-teacher"></i>

                            </div>


                            <h3 class="header-title">

                                <span>
                                    Class
                                </span>

                                Teachers Management

                            </h3>

                        </div>

                    </div>


                    {{-- Body --}}
                    <div class="card-body-modern">


                        @if ($classes->isEmpty())


                            <div class="empty-state-modern">

                                <i class="fas fa-chalkboard"></i>

                                <h6 class="mt-2">
                                    No Classes Available
                                </h6>

                                <p>
                                    Create a class first to assign teachers
                                </p>

                            </div>


                        @else


                            <div class="table-container-modern">

                                <table class="table-modern">

                                    <thead>

                                        <tr>

                                            <th>
                                                Class Name & Code
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>


                                        @foreach ($classes as $class)

                                            <tr>

                                                <td>

                                                    <a
                                                        href="{{ route(
                                                            'Class.Teachers',
                                                            [
                                                                'class' =>
                                                                    Hashids::encode(
                                                                        $class->id
                                                                    )
                                                            ]
                                                        ) }}"
                                                        class="class-link-modern"
                                                    >


                                                        <div class="link-icon">

                                                            <i class="fas fa-users"></i>

                                                        </div>


                                                        <span class="class-link-text">

                                                            {{
                                                                strtoupper(
                                                                    $class->class_name
                                                                )
                                                            }}

                                                            -

                                                            {{
                                                                strtoupper(
                                                                    $class->class_code
                                                                )
                                                            }}

                                                        </span>


                                                        <i
                                                            class="fas fa-arrow-right class-link-arrow"
                                                        ></i>

                                                    </a>

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

        </div>

    </div>


    {{-- =========================================================
         ADD CLASS MODAL
         ========================================================= --}}

    <div
        class="modal fade modal-modern"
        id="addClassModal"
        tabindex="-1"
        aria-labelledby="addClassModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">


                {{-- Header --}}
                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="addClassModalLabel"
                    >

                        <i class="fas fa-plus-circle me-2"></i>

                        Create New Class

                    </h5>


                    <button
                        type="button"
                        class="btn-modal-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >

                        <i class="fas fa-times"></i>

                    </button>

                </div>


                {{-- Form --}}
                <form
                    class="needs-validation"
                    novalidate
                    action="{{ route('Classes.store') }}"
                    method="POST"
                    id="addClassForm"
                >

                    @csrf


                    <div class="modal-body">

                        <div class="row g-2">


                            {{-- Class Name --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label
                                        for="className"
                                        class="form-label-modern"
                                    >

                                        <i
                                            class="fas fa-school me-1 text-primary"
                                        ></i>

                                        Class Name

                                    </label>


                                    <input
                                        type="text"
                                        id="className"
                                        name="name"
                                        class="form-control-modern @error('name') error @enderror"
                                        placeholder="e.g. Form One, Standard Seven"
                                        value="{{ old('name') }}"
                                        autocomplete="off"
                                        required
                                    >


                                    @error('name')

                                        <div class="error-message">

                                            <i class="fas fa-exclamation-circle"></i>

                                            <span>
                                                {{ $message }}
                                            </span>

                                        </div>

                                    @enderror

                                </div>

                            </div>


                            {{-- Class Code --}}
                            <div class="col-md-6">

                                <div class="form-group-modern">

                                    <label
                                        for="classCode"
                                        class="form-label-modern"
                                    >

                                        <i
                                            class="fas fa-barcode me-1 text-primary"
                                        ></i>

                                        Class Code

                                    </label>


                                    <input
                                        type="text"
                                        id="classCode"
                                        name="code"
                                        class="form-control-modern @error('code') error @enderror"
                                        placeholder="e.g. F1, STD7"
                                        value="{{ old('code') }}"
                                        autocomplete="off"
                                        maxlength="20"
                                        required
                                    >


                                    @error('code')

                                        <div class="error-message">

                                            <i class="fas fa-exclamation-circle"></i>

                                            <span>
                                                {{ $message }}
                                            </span>

                                        </div>

                                    @enderror

                                </div>

                            </div>

                        </div>


                        {{-- Information --}}
                        <div class="class-info-note">

                            <i
                                class="fas fa-info-circle me-1 text-info"
                            ></i>

                            Class code should be unique and easy to identify.
                            Example:
                            <strong>F1</strong>
                            for Form One or
                            <strong>STD7</strong>
                            for Standard Seven.

                        </div>

                    </div>


                    {{-- Footer --}}
                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn-modal-cancel"
                            data-bs-dismiss="modal"
                        >

                            <i class="fas fa-times me-1"></i>

                            Cancel

                        </button>


                        <button
                            type="submit"
                            class="btn-modal-save"
                            id="saveButton"
                        >

                            <i class="fas fa-save"></i>

                            <span>
                                Save Class
                            </span>

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
     * =========================================================
     * HELPERS
     * =========================================================
     */

    const page =
        document.querySelector(
            '.class-management-page'
        );


    if (!page) {
        return;
    }


    const $ = (
        selector,
        parent = page
    ) =>
        parent.querySelector(selector);


    const $$ = (
        selector,
        parent = page
    ) =>
        Array.from(
            parent.querySelectorAll(selector)
        );


    /*
     * =========================================================
     * PARTICLES
     * =========================================================
     */

    const particlesContainer =
        $('.particles');


    if (particlesContainer) {

        const fragment =
            document.createDocumentFragment();


        for (let i = 0; i < 18; i++) {

            const particle =
                document.createElement('div');


            particle.className =
                'particle';


            const size =
                Math.random() * 8 + 3;


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


        particlesContainer.appendChild(
            fragment
        );

    }


    /*
     * =========================================================
     * FORM
     * =========================================================
     */

    const form =
        $('#addClassForm');


    const submitButton =
        $('#saveButton');


    const loadingSpinner =
        $('#loadingSpinner');


    /*
     * =========================================================
     * TOAST
     * =========================================================
     */

    function showToast(
        message,
        type = 'success'
    ) {

        const toast =
            document.createElement('div');


        toast.className =
            `toast-notification toast-${type}`;


        const icon =
            type === 'success'
                ? 'fa-check-circle'
                : 'fa-exclamation-circle';


        toast.innerHTML = `

            <i class="fas ${icon}"></i>

            <span>
                ${message}
            </span>

        `;


        page.appendChild(
            toast
        );


        requestAnimationFrame(() => {

            toast.classList.add(
                'show'
            );

        });


        setTimeout(() => {

            toast.classList.remove(
                'show'
            );


            setTimeout(() => {

                toast.remove();

            }, 250);

        }, 3000);

    }


    /*
     * =========================================================
     * FORM VALIDATION
     * ========================================================= */

    if (
        form &&
        submitButton
    ) {

        form.addEventListener(
            'submit',
            function (event) {

                if (!form.checkValidity()) {

                    event.preventDefault();

                    event.stopPropagation();


                    form.classList.add(
                        'was-validated'
                    );


                    showToast(
                        'Please fill all required fields.',
                        'error'
                    );


                    return;

                }


                /*
                 * Prevent double submission.
                 */

                submitButton.disabled =
                    true;


                submitButton.innerHTML = `

                    <span
                        class="spinner-border spinner-border-sm me-1"
                        role="status"
                        aria-hidden="true"
                    ></span>

                    <span>
                        Saving...
                    </span>

                `;


                if (loadingSpinner) {

                    loadingSpinner.style.display =
                        'block';

                }


                form.classList.add(
                    'was-validated'
                );

            }
        );

    }


    /*
     * =========================================================
     * CLASS / CODE UPPERCASE
     * =========================================================
     *
     * IMPORTANT:
     * We scope this only to the class form.
     * It will NOT affect unrelated inputs in the layout.
     */

    const classNameInput =
        $('#className');


    const classCodeInput =
        $('#classCode');


    if (classNameInput) {

        classNameInput.addEventListener(
            'input',
            function () {

                this.value =
                    this.value.toUpperCase();

            }
        );

    }


    if (classCodeInput) {

        classCodeInput.addEventListener(
            'input',
            function () {

                this.value =
                    this.value.toUpperCase();

            }
        );

    }


    /*
     * =========================================================
     * CLASS ACTION CONFIRMATION
     * ========================================================= */

    $$(
        'form[data-confirm-action]'
    ).forEach(
        function (actionForm) {

            actionForm.addEventListener(
                'submit',
                function (event) {

                    const action =
                        this.dataset.confirmAction;


                    const className =
                        this.dataset.className ||
                        'this class';


                    let message =
                        'Are you sure?';


                    if (
                        action ===
                        'disable'
                    ) {

                        message =
                            `⚠️ Are you sure you want to disable ${className}?`;

                    }


                    if (
                        action ===
                        'enable'
                    ) {

                        message =
                            `✅ Enable ${className}?`;

                    }


                    if (
                        action ===
                        'delete'
                    ) {

                        message =
                            `⚠️ This action cannot be undone. Delete ${className} permanently?`;

                    }


                    if (
                        !confirm(message)
                    ) {

                        event.preventDefault();

                        return;

                    }


                    /*
                     * Show small loading state on action button.
                     */

                    const button =
                        this.querySelector(
                            'button[type="submit"]'
                        );


                    if (button) {

                        button.disabled =
                            true;


                        button.innerHTML = `

                            <span
                                class="spinner-border spinner-border-sm"
                                role="status"
                                aria-hidden="true"
                            ></span>

                        `;

                    }

                }
            );

        }
    );


    /*
     * =========================================================
     * RESET MODAL STATE
     * ========================================================= */

    const modal =
        document.getElementById(
            'addClassModal'
        );


    if (modal) {

        modal.addEventListener(
            'hidden.bs.modal',
            function () {

                if (!form) {
                    return;
                }


                /*
                 * Only reset if there are no
                 * server validation errors.
                 */

                const hasServerErrors =
                    form.querySelector(
                        '.error-message'
                    );


                if (!hasServerErrors) {

                    form.reset();

                    form.classList.remove(
                        'was-validated'
                    );

                }


                if (submitButton) {

                    submitButton.disabled =
                        false;


                    submitButton.innerHTML = `

                        <i class="fas fa-save"></i>

                        <span>
                            Save Class
                        </span>

                    `;

                }


                if (loadingSpinner) {

                    loadingSpinner.style.display =
                        'none';

                }

            }
        );

    }


    /*
     * =========================================================
     * AUTO OPEN MODAL WHEN VALIDATION ERROR EXISTS
     * =========================================================
     */

    @if ($errors->has('name') || $errors->has('code'))

        const modalElement =
            document.getElementById(
                'addClassModal'
            );


        if (
            modalElement &&
            typeof bootstrap !== 'undefined'
        ) {

            const addClassModal =
                bootstrap.Modal.getOrCreateInstance(
                    modalElement
                );


            addClassModal.show();

        }

    @endif

});
</script>

@endsection