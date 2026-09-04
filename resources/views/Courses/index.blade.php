@extends('SRTDashboard.frame')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
/* =========================================================
   CLASS SUBJECT ASSIGNMENT
   Scoped styles — safe for SRTDashboard.frame
   ========================================================= */

.class-subject-page {
    --c-primary: #4361ee;
    --c-primary-dark: #3a56d4;
    --c-secondary: #3f37c9;
    --c-success: #1cc88a;
    --c-warning: #f6c23e;
    --c-danger: #e74a3b;
    --c-text: #212529;
    --c-muted: #64748b;
    --c-border: #e2e8f0;
    --c-surface: #ffffff;
    --c-soft: #f8fafc;

    width: 100%;
    min-width: 0;
    position: relative;
    /* isolation: isolate; */
}

.class-subject-page *,
.class-subject-page *::before,
.class-subject-page *::after {
    box-sizing: border-box;
}

.class-subject-page .animated-bg {
    position: fixed;
    inset: 0;
    /* z-index: -2; */
    pointer-events: none;
    overflow: hidden;
    background:
        radial-gradient(circle at 75% 20%, rgba(67,97,238,.08), transparent 30%),
        radial-gradient(circle at 20% 80%, rgba(63,55,201,.07), transparent 30%);
}

.class-subject-page .animated-bg::before {
    content: "";
    position: absolute;
    inset: -50%;
    background:
        radial-gradient(circle at 70% 30%, rgba(67,97,238,.08), transparent 30%),
        radial-gradient(circle at 30% 70%, rgba(63,55,201,.08), transparent 30%);
    animation: csaRotate 60s linear infinite;
}

@keyframes csaRotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.class-subject-page .particles {
    position: fixed;
    inset: 0;
    /* z-index: -1; */
    pointer-events: none;
    overflow: hidden;
}

.class-subject-page .particle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.45);
    animation: csaFloat 20s infinite;
}

@keyframes csaFloat {
    0%,100% { transform: translate(0,0) scale(1); }
    25% { transform: translate(100px,-100px) scale(1.2); }
    50% { transform: translate(200px,0) scale(.8); }
    75% { transform: translate(100px,100px) scale(1.1); }
}

.class-subject-page .dashboard-container {
    width: min(100%, 1450px);
    margin: 24px auto;
    padding-inline: 20px;
    position: relative;
    /* z-index: 1; */
}

.class-subject-page .modern-card {
    width: 100%;
    overflow: hidden;
    background: rgba(255,255,255,.96);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,.65);
    border-radius: 22px;
    box-shadow: 0 14px 35px rgba(15,23,42,.09);
    transition: transform .25s ease, box-shadow .25s ease;
}

.class-subject-page .modern-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 42px rgba(15,23,42,.13);
}

.class-subject-page .card-header-modern {
    position: relative;
    overflow: hidden;
    padding: 20px 24px;
    background: linear-gradient(135deg,var(--c-primary),var(--c-secondary));
    color: #fff;
}

.class-subject-page .card-header-modern::before {
    content: "";
    position: absolute;
    inset: -50%;
    background: radial-gradient(circle,rgba(255,255,255,.15),transparent 55%);
    pointer-events: none;
}

.class-subject-page .card-header-modern::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 3px;
    background: linear-gradient(90deg,var(--c-warning),#4cc9f0,#4895ef);
}

.class-subject-page .header-content {
    position: relative;
    /* z-index: 2; */
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.class-subject-page .header-left {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.class-subject-page .header-icon {
    width: 48px;
    height: 48px;
    min-width: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
    background: rgba(255,255,255,.16);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    font-size: 21px;
}

.class-subject-page .header-title {
    min-width: 0;
    color: #fff;
}

.class-subject-page .header-title h3 {
    margin: 0;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-size: clamp(1.05rem,2vw,1.35rem);
    font-weight: 700;
    line-height: 1.35;
}

.class-subject-page .class-highlight {
    display: inline-flex;
    align-items: center;
    max-width: 100%;
    padding: 5px 11px;
    border-radius: 999px;
    background: var(--c-warning);
    color: #212529;
    font-size: .72rem;
    font-weight: 800;
    line-height: 1.2;
}

.class-subject-page .action-group {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    flex-wrap: wrap;
}

.class-subject-page .btn-modern {
    min-height: 40px;
    padding: 9px 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid transparent;
    border-radius: 10px;
    color: #fff;
    font-size: .8rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
}

.class-subject-page .btn-modern:hover {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 7px 17px rgba(0,0,0,.16);
}

.class-subject-page .btn-info-modern {
    background: rgba(255,255,255,.14);
    border-color: rgba(255,255,255,.25);
}

.class-subject-page .btn-primary-modern {
    background: linear-gradient(135deg,#1cc88a,#17a673);
}

.class-subject-page .card-body-modern {
    padding: 20px;
}

.class-subject-page .table-container-modern {
    width: 100%;
    overflow-x: auto;
    background: #fff;
    border: 1px solid var(--c-border);
    border-radius: 14px;
    box-shadow: 0 4px 15px rgba(15,23,42,.05);
    -webkit-overflow-scrolling: touch;
}

.class-subject-page .table-modern {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: .82rem;
}

.class-subject-page .table-modern thead th {
    padding: 12px 13px;
    background: linear-gradient(135deg,var(--c-primary),var(--c-secondary));
    color: #fff;
    border: 0;
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
}

.class-subject-page .table-modern tbody td {
    padding: 12px 13px;
    background: #fff;
    color: #475569;
    border-bottom: 1px solid #edf2f7;
    vertical-align: middle;
}

.class-subject-page .table-modern tbody tr:last-child td {
    border-bottom: 0;
}

.class-subject-page .table-modern tbody tr {
    transition: background-color .18s ease;
}

.class-subject-page .table-modern tbody tr:hover td {
    background: #f8fafc;
}

.class-subject-page .subject-info,
.class-subject-page .teacher-info {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.class-subject-page .subject-icon {
    width: 38px;
    height: 38px;
    min-width: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: linear-gradient(135deg,var(--c-primary),var(--c-secondary));
    color: #fff;
}

.class-subject-page .subject-details {
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.class-subject-page .subject-name {
    max-width: 230px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: var(--c-text);
    font-weight: 700;
}

.class-subject-page .subject-code {
    color: #64748b;
    font-size: .7rem;
    font-weight: 600;
}

.class-subject-page .teacher-avatar {
    width: 35px;
    height: 35px;
    min-width: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: linear-gradient(135deg,#36b9cc,#1a8a9e);
    color: #fff;
    font-size: .75rem;
    font-weight: 700;
}

.class-subject-page .teacher-name {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #475569;
}

.class-subject-page .badge-modern {
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
    padding: 6px 9px;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 700;
}

.class-subject-page .badge-success {
    background: linear-gradient(135deg,#1cc88a,#13855c);
    color: #fff;
}

.class-subject-page .badge-danger {
    background: linear-gradient(135deg,#e74a3b,#be2617);
    color: #fff;
}

.class-subject-page .action-icons {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    flex-wrap: nowrap;
}

.class-subject-page .action-icon {
    width: 34px;
    height: 34px;
    min-width: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: 0;
    border-radius: 8px;
    color: #fff;
    text-decoration: none;
    font-size: .75rem;
    cursor: pointer;
    transition: transform .18s ease, box-shadow .18s ease;
}

.class-subject-page .action-icon:hover {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 5px 12px rgba(15,23,42,.16);
}

.class-subject-page .action-icon.edit {
    background: linear-gradient(135deg,#6c757d,#5a6268);
}

.class-subject-page .action-icon.warning {
    background: linear-gradient(135deg,#f6c23e,#f4b619);
}

.class-subject-page .action-icon.success {
    background: linear-gradient(135deg,#1cc88a,#13855c);
}

.class-subject-page .action-icon.danger {
    background: linear-gradient(135deg,#e74a3b,#be2617);
}

.class-subject-page .empty-state-modern {
    padding: 40px 16px;
    text-align: center;
    background: linear-gradient(135deg,#fff8e1,#fff3cd);
    border: 2px dashed #ffc107;
    border-radius: 14px;
}

.class-subject-page .empty-state-modern i {
    color: #ffc107;
    font-size: 45px;
    animation: csaBounce 2s infinite;
}

@keyframes csaBounce {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

/* Modal */
.class-subject-page .modal-modern .modal-content {
    border: 0;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 22px 55px rgba(15,23,42,.2);
}

.class-subject-page .modal-modern .modal-header {
    padding: 15px 20px;
    border: 0;
    background: linear-gradient(135deg,var(--c-primary),var(--c-secondary));
    color: #fff;
}

.class-subject-page .modal-modern .modal-title {
    font-size: 1rem;
    font-weight: 700;
}

.class-subject-page .btn-modal-close {
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 8px;
    background: rgba(255,255,255,.12);
    color: #fff;
    cursor: pointer;
}

.class-subject-page .modal-modern .modal-body {
    padding: 20px;
}

.class-subject-page .modal-modern .modal-footer {
    padding: 13px 20px;
    border: 0;
    background: #f8fafc;
}

.class-subject-page .form-group-modern {
    margin-bottom: 14px;
}

.class-subject-page .form-label-modern {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 6px;
    color: var(--c-text);
    font-size: .8rem;
    font-weight: 700;
}

.class-subject-page .form-control-modern {
    width: 100%;
    min-height: 44px;
    padding: 9px 11px;
    border: 1px solid #dbe3ec;
    border-radius: 9px;
    background: #fff;
    color: #334155;
    font-size: .84rem;
    outline: none;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.class-subject-page .form-control-modern:focus {
    border-color: var(--c-primary);
    box-shadow: 0 0 0 3px rgba(67,97,238,.1);
}

.class-subject-page .error-message {
    display: flex;
    align-items: flex-start;
    gap: 5px;
    margin-top: 4px;
    color: var(--c-danger);
    font-size: .72rem;
}

/* Select2 */
.class-subject-page .select2-container {
    width: 100% !important;
}

.class-subject-page .select2-container--default .select2-selection--single {
    height: 44px !important;
    padding: 7px 10px !important;
    border: 1px solid #dbe3ec !important;
    border-radius: 9px !important;
    background: #fff !important;
}

.class-subject-page .select2-container--default .select2-selection--single .select2-selection__rendered {
    padding-left: 0 !important;
    line-height: 27px !important;
    color: #334155 !important;
    font-size: .84rem;
}

.class-subject-page .select2-container--default .select2-selection--single .select2-selection__arrow {
    top: 6px !important;
    right: 8px !important;
    height: 30px !important;
}

.class-subject-page .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: var(--c-primary) !important;
    box-shadow: 0 0 0 3px rgba(67,97,238,.1);
}

.class-subject-page .select2-dropdown {
    border: 1px solid #dbe3ec !important;
    border-radius: 9px !important;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(15,23,42,.12);
}

.class-subject-page .select2-search__field {
    min-height: 38px;
    border: 1px solid #dbe3ec !important;
    border-radius: 7px;
    padding: 7px 9px !important;
}

.class-subject-page .select2-results__option {
    padding: 9px 11px !important;
    font-size: .8rem;
}

.class-subject-page .select2-results__option--highlighted {
    background: linear-gradient(135deg,var(--c-primary),var(--c-secondary)) !important;
}

.class-subject-page .assignment-note {
    padding: 10px 12px;
    border-radius: 9px;
    background: #f8fafc;
    color: #64748b;
    font-size: .72rem;
    line-height: 1.5;
}

.class-subject-page .btn-modal-secondary,
.class-subject-page .btn-modal-success {
    min-height: 40px;
    padding: 8px 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: 0;
    border-radius: 9px;
    color: #fff;
    font-size: .8rem;
    font-weight: 700;
    cursor: pointer;
}

.class-subject-page .btn-modal-secondary {
    background: #64748b;
}

.class-subject-page .btn-modal-success {
    background: linear-gradient(135deg,#1cc88a,#13855c);
}

.class-subject-page .btn-modal-secondary:hover,
.class-subject-page .btn-modal-success:hover {
    color: #fff;
    transform: translateY(-1px);
}

.class-subject-page .loading-spinner {
    position: fixed;
    top: 50%;
    left: 50%;
    width: 55px;
    height: 55px;
    display: none;
    transform: translate(-50%,-50%);
    border: 4px solid rgba(67,97,238,.15);
    border-top-color: var(--c-primary);
    border-radius: 50%;
    animation: csaSpin 1s linear infinite;
    /* z-index: 9999; */
}

@keyframes csaSpin {
    to { transform: translate(-50%,-50%) rotate(360deg); }
}

.class-subject-page .toast-notification {
    position: fixed;
    top: 18px;
    right: 18px;
    max-width: min(360px,calc(100vw - 36px));
    padding: 11px 14px;
    display: flex;
    align-items: center;
    gap: 9px;
    border-left: 4px solid;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 12px 30px rgba(15,23,42,.15);
    transform: translateX(120%);
    transition: transform .25s ease;
    /* z-index: 10000; */
    font-size: .78rem;
}

.class-subject-page .toast-notification.show {
    transform: translateX(0);
}

.class-subject-page .toast-success {
    border-left-color: #28a745;
}

.class-subject-page .toast-error {
    border-left-color: var(--c-danger);
}

/* Tablet */
@media (max-width: 991.98px) {
    .class-subject-page .dashboard-container {
        margin: 16px auto;
        padding-inline: 14px;
    }

    .class-subject-page .modern-card {
        border-radius: 18px;
    }

    .class-subject-page .card-header-modern {
        padding: 16px 18px;
    }

    .class-subject-page .card-body-modern {
        padding: 16px;
    }
}

/* Mobile: table becomes cards */
@media (max-width: 767.98px) {
    .class-subject-page .dashboard-container {
        margin: 9px auto;
        padding-inline: 8px;
    }

    .class-subject-page .modern-card:hover {
        transform: none;
    }

    .class-subject-page .card-header-modern {
        padding: 13px;
    }

    .class-subject-page .header-content {
        align-items: stretch;
        flex-direction: column;
    }

    .class-subject-page .header-left {
        align-items: center;
    }

    .class-subject-page .header-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 10px;
        font-size: 17px;
    }

    .class-subject-page .header-title h3 {
        font-size: .98rem;
    }

    .class-subject-page .action-group {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 7px;
    }

    .class-subject-page .btn-modern {
        width: 100%;
        min-height: 40px;
        padding: 8px 10px;
    }

    .class-subject-page .card-body-modern {
        padding: 9px;
    }

    .class-subject-page .table-container-modern {
        overflow: visible;
        border: 0;
        background: transparent;
        box-shadow: none;
    }

    .class-subject-page .table-modern {
        display: block;
    }

    .class-subject-page .table-modern thead {
        display: none;
    }

    .class-subject-page .table-modern tbody {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .class-subject-page .table-modern tbody tr {
        display: grid;
        grid-template-columns: 1fr auto;
        padding: 11px;
        border: 1px solid #e5eaf0;
        border-radius: 11px;
        background: #fff;
        box-shadow: 0 3px 10px rgba(15,23,42,.045);
    }

    .class-subject-page .table-modern tbody tr:hover td {
        background: transparent;
    }

    .class-subject-page .table-modern tbody td {
        display: flex;
        align-items: center;
        min-width: 0;
        padding: 5px 0;
        border: 0;
        background: transparent;
    }

    /* # */
    .class-subject-page .table-modern tbody td:nth-child(1) {
        display: none;
    }

    /* Subject */
    .class-subject-page .table-modern tbody td:nth-child(2) {
        grid-column: 1 / -1;
        grid-row: 1;
        padding-bottom: 9px;
    }

    .class-subject-page .subject-name {
        max-width: calc(100vw - 135px);
        font-size: .8rem;
    }

    /* Teacher */
    .class-subject-page .table-modern tbody td:nth-child(3) {
        grid-column: 1 / -1;
        grid-row: 2;
        padding-top: 8px;
        border-top: 1px solid #edf2f7;
    }

    .class-subject-page .table-modern tbody td:nth-child(3)::before {
        content: "Teacher";
        margin-right: auto;
        color: #64748b;
        font-size: .67rem;
        font-weight: 700;
    }

    .class-subject-page .teacher-info {
        max-width: 65%;
        margin-left: auto;
    }

    .class-subject-page .teacher-name {
        font-size: .75rem;
    }

    /* Status */
    .class-subject-page .table-modern tbody td:nth-child(4) {
        grid-column: 1;
        grid-row: 3;
        padding-top: 8px;
    }

    .class-subject-page .table-modern tbody td:nth-child(4)::before {
        content: "Status";
        margin-right: 8px;
        color: #64748b;
        font-size: .67rem;
        font-weight: 700;
    }

    /* Actions */
    .class-subject-page .table-modern tbody td:nth-child(5) {
        grid-column: 2;
        grid-row: 3;
        justify-content: flex-end;
        padding-top: 8px;
    }

    .class-subject-page .table-modern tbody td:nth-child(5)::before {
        content: "Actions";
        margin-right: 8px;
        color: #64748b;
        font-size: .67rem;
        font-weight: 700;
    }

    .class-subject-page .action-icons {
        justify-content: flex-end;
    }

    .class-subject-page .action-icon {
        width: 33px;
        height: 33px;
        min-width: 33px;
        border-radius: 8px;
    }

    .class-subject-page .modal-modern .modal-dialog {
        margin: 10px;
    }

    .class-subject-page .modal-modern .modal-body {
        padding: 15px;
    }

    .class-subject-page .modal-modern .modal-footer {
        padding: 11px 15px;
    }

    .class-subject-page .form-control-modern,
    .class-subject-page .select2-container--default .select2-selection--single {
        min-height: 44px !important;
        font-size: 16px !important;
    }

    .class-subject-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-size: 16px !important;
    }
}

/* Small phones */
@media (max-width: 480px) {
    .class-subject-page .dashboard-container {
        padding-inline: 5px;
    }

    .class-subject-page .card-header-modern {
        padding: 11px;
    }

    .class-subject-page .card-body-modern {
        padding: 7px;
    }

    .class-subject-page .header-icon {
        width: 36px;
        height: 36px;
        min-width: 36px;
        font-size: 15px;
    }

    .class-subject-page .header-title h3 {
        font-size: .88rem;
    }

    .class-subject-page .class-highlight {
        font-size: .64rem;
    }

    .class-subject-page .action-group {
        grid-template-columns: 1fr;
    }

    .class-subject-page .table-modern tbody tr {
        padding: 9px;
    }

    .class-subject-page .teacher-info {
        max-width: 62%;
    }

    .class-subject-page .modal-modern .modal-dialog {
        margin: 6px;
    }

    .class-subject-page .modal-modern .modal-footer {
        flex-direction: column;
        gap: 7px;
    }

    .class-subject-page .btn-modal-secondary,
    .class-subject-page .btn-modal-success {
        width: 100%;
    }
}

/* Very small phones */
@media (max-width: 360px) {
    .class-subject-page .dashboard-container {
        padding-inline: 3px;
    }

    .class-subject-page .card-body-modern {
        padding: 5px;
    }

    .class-subject-page .subject-name {
        max-width: 150px;
    }

    .class-subject-page .teacher-info {
        max-width: 58%;
    }

    .class-subject-page .action-icon {
        width: 31px;
        height: 31px;
        min-width: 31px;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .class-subject-page .animated-bg::before,
    .class-subject-page .particle,
    .class-subject-page .empty-state-modern i {
        animation: none;
    }

    .class-subject-page *,
    .class-subject-page *::before,
    .class-subject-page *::after {
        transition-duration: .01ms !important;
        animation-duration: .01ms !important;
    }
}
</style>

    <div class="class-subject-page">
    <div class="animated-bg"></div>
    <div class="particles"></div>
    <div class="loading-spinner" id="loadingSpinner"></div>

    <div class="dashboard-container">
        <div class="modern-card">
            <!-- Header -->
            <div class="card-header-modern">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="header-title">
                            <h3>
                                <span>Subject List</span>
                                @if(!isset($message) && isset($class))
                                    <span class="class-highlight">{{strtoupper($class->class_name)}}</span>
                                @endif
                            </h3>
                        </div>
                    </div>
                    <div class="action-group">
                        <a href="{{route('courses.index')}}" class="btn-modern btn-info-modern">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back</span>
                        </a>
                        @if(!isset($message))
                            <button type="button" class="btn-modern btn-primary-modern" data-bs-toggle="modal" data-bs-target="#assignModal">
                                <i class="fas fa-plus"></i>
                                <span>Assign Subject</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body-modern">
                @if(isset($message))
                    <div class="empty-state-modern">
                        <i class="fas fa-info-circle"></i>
                        <h6 class="mt-4">{{ $message }}</h6>
                        <p class="text-muted">Please check back later or contact administrator</p>
                    </div>
                @elseif ($classCourse->isEmpty())
                    <div class="empty-state-modern">
                        <i class="fas fa-book-open"></i>
                        <h6 class="mt-4">No Subjects Assigned</h6>
                        <p class="text-muted">Click "Assign Subject" to add subjects to this class</p>
                    </div>
                @else
                    <div class="table-container-modern">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Subject Information</th>
                                    <th>Subject Teacher</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classCourse as $course)
                                    <tr>
                                        <td><span class="fw-bold">{{ $loop->iteration }}</span></td>
                                        <td>
                                            <div class="subject-info">
                                                <div class="subject-icon">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                                <div class="subject-details">
                                                    <span class="subject-name text-uppercase">{{ strtoupper($course->course_name) }}</span>
                                                    <span class="subject-code">{{ strtoupper($course->course_code) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="teacher-info">
                                                <div class="teacher-avatar">
                                                    {{ strtoupper(substr($course->first_name, 0, 1)) }}{{ strtoupper(substr($course->last_name, 0, 1)) }}
                                                </div>
                                                <span class="text-capitalize">{{ ucwords(strtolower($course->first_name)) }} {{ ucwords(strtolower($course->last_name)) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($course->status == 1)
                                                <span class="badge-modern badge-success">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            @else
                                                <span class="badge-modern badge-danger">
                                                    <i class="fas fa-ban me-1"></i> Blocked
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-icons">
                                                @if ($course->status == 1)
                                                    <a href="{{route('courses.assign', ['id' => Hashids::encode($course->id)])}}"
                                                       class="action-icon edit"
                                                       title="Edit Assignment">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <form action="{{route('block.assigned.course', ['id' => Hashids::encode($course->id)])}}"
                                                          method="POST"
                                                          class="d-inline"
                                                          data-confirm-message="⚠️ Are you sure you want to block {{ strtoupper($course->course_name) }}?">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-icon warning" title="Block Subject">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{route('unblock.assigned.course', ['id' => Hashids::encode($course->id)])}}"
                                                          method="POST"
                                                          class="d-inline"
                                                          data-confirm-message="✅ Unblock {{ strtoupper($course->course_name) }}?">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-icon success" title="Unblock Subject">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{route('courses.delete', ['id' => Hashids::encode($course->id)])}}"
                                                   class="action-icon danger"
                                                   title="Delete Permanently"
                                                   onclick="return confirm('⚠️ Delete {{ strtoupper($course->course_name) }} permanently? This action cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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

    <!-- Modern Modal -->
    <div class="modal fade modal-modern" id="assignModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>
                        Assign Teaching Subject
                    </h5>
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form class="needs-validation" novalidate action="{{route('course.assign')}}" method="POST">
                    @csrf
                    <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-book me-2 text-primary"></i>
                                        Select Subject
                                    </label>
                                    <select name="course_id" id="courseSelect" class="form-control-modern select2" required>
                                        <option value="" disabled selected>-- Search or select subject --</option>
                                        @if ($courses->isEmpty())
                                            <option value="" disabled class="text-danger">No subjects available</option>
                                        @else
                                            @foreach ($courses as $course)
                                                <option value="{{$course->id}}">{{ ucwords(strtolower($course->course_name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('course_id')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{$message}}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-layer-group me-2 text-primary"></i>
                                        Class
                                    </label>
                                    <select name="class_id" id="classSelect" class="form-control-modern" required>
                                        <option value="{{$class->id}}" selected>{{$class->class_name}}</option>
                                    </select>
                                    @error('class_id')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{$message}}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-user-tie me-2 text-primary"></i>
                                        Select Teacher
                                    </label>
                                    <select name="teacher_id" id="teacherSelect" class="form-control-modern select2" required>
                                        <option value="" disabled selected>-- Search or select teacher --</option>
                                        @if ($teachers->isEmpty())
                                            <option value="" disabled class="text-danger">No teachers available</option>
                                        @else
                                            @foreach ($teachers as $teacher)
                                                <option value="{{$teacher->id}}">{{ ucwords(strtolower($teacher->first_name)) }} {{ ucwords(strtolower($teacher->last_name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('teacher_id')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{$message}}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-light rounded">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-2 text-info"></i>
                                Assign a subject to this class with a specific teacher. Each subject can have one teacher per class.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </button>
                        <button type="submit" class="btn-modal-success" id="saveButton">
                            <i class="fas fa-save me-2"></i>
                            Assign Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const page = document.querySelector('.class-subject-page');

    if (!page) return;

    const loadingSpinner = document.getElementById('loadingSpinner');
    const form = page.querySelector('form.needs-validation');
    const submitButton = document.getElementById('saveButton');
    const modal = document.getElementById('assignModal');

    /* ---------------------------------------------------------
       Particles
       --------------------------------------------------------- */
    const particlesContainer = page.querySelector('.particles');

    if (particlesContainer) {
        const fragment = document.createDocumentFragment();

        for (let i = 0; i < 18; i++) {
            const particle = document.createElement('div');
            const size = Math.random() * 8 + 3;

            particle.className = 'particle';
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.top = `${Math.random() * 100}%`;
            particle.style.animationDelay = `${Math.random() * 20}s`;
            particle.style.animationDuration = `${Math.random() * 10 + 15}s`;

            fragment.appendChild(particle);
        }

        particlesContainer.appendChild(fragment);
    }

    /* ---------------------------------------------------------
       Select2
       --------------------------------------------------------- */
    if (
        typeof window.jQuery !== 'undefined' &&
        typeof jQuery.fn.select2 === 'function' &&
        modal
    ) {
        const $modal = jQuery(modal);

        jQuery('#courseSelect').select2({
            placeholder: 'Search subject...',
            allowClear: true,
            dropdownParent: $modal,
            width: '100%'
        });

        jQuery('#teacherSelect').select2({
            placeholder: 'Search teacher...',
            allowClear: true,
            dropdownParent: $modal,
            width: '100%'
        });
    }

    /* ---------------------------------------------------------
       Form validation + submit protection
       --------------------------------------------------------- */
    if (form && submitButton) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                form.classList.add('was-validated');

                if (loadingSpinner) {
                    loadingSpinner.style.display = 'none';
                }

                submitButton.disabled = false;
                submitButton.innerHTML = `
                    <i class="fas fa-save"></i>
                    <span>Assign Subject</span>
                `;

                showToast('Please fill all required fields.', 'error');

                const firstInvalid = form.querySelector(':invalid');

                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                return;
            }

            form.classList.add('was-validated');

            submitButton.disabled = true;
            submitButton.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true"
                ></span>
                <span>Assigning...</span>
            `;

            if (loadingSpinner) {
                loadingSpinner.style.display = 'block';
            }

            /* Allow the browser to perform the normal POST. */
        });
    }

    /* ---------------------------------------------------------
       Confirmation for assignment actions
       --------------------------------------------------------- */
    page.querySelectorAll('form[data-confirm-message]').forEach(function (actionForm) {
        actionForm.addEventListener('submit', function (event) {
            const message = this.dataset.confirmMessage || 'Are you sure?';

            if (!window.confirm(message)) {
                event.preventDefault();
                return;
            }

            const button = this.querySelector('button[type="submit"]');

            if (button) {
                button.disabled = true;
                button.innerHTML = `
                    <span
                        class="spinner-border spinner-border-sm"
                        role="status"
                        aria-hidden="true"
                    ></span>
                `;
            }
        });
    });

    /* ---------------------------------------------------------
       Toast
       --------------------------------------------------------- */
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');

        toast.className = `toast-notification toast-${type}`;

        toast.innerHTML = `
            <i class="fas ${
                type === 'success'
                    ? 'fa-check-circle'
                    : 'fa-exclamation-circle'
            }"></i>
            <span>${message}</span>
        `;

        page.appendChild(toast);

        requestAnimationFrame(function () {
            toast.classList.add('show');
        });

        setTimeout(function () {
            toast.classList.remove('show');

            setTimeout(function () {
                toast.remove();
            }, 250);
        }, 3000);
    }

    /* ---------------------------------------------------------
       Restore state after browser back/forward cache
       --------------------------------------------------------- */
    window.addEventListener('pageshow', function () {
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = `
                <i class="fas fa-save"></i>
                <span>Assign Subject</span>
            `;
        }

        if (loadingSpinner) {
            loadingSpinner.style.display = 'none';
        }
    });
});
</script>
@endsection