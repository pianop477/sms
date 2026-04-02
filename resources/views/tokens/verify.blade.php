<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShuleApp | Gate Pass Verification</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon.ico') }}">
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.5.2-web/css/all.css') }}">

    <style>
        /* All existing styles remain the same */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .gateway-container {
            width: 100%;
            max-width: 550px;
            margin: 0 auto;
        }

        .gateway-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            padding: 24px 20px;
            animation: slideUp 0.4s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .gateway-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .gateway-header h2 {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 6px;
            font-size: 1.5rem;
        }

        .gateway-header p {
            color: #718096;
            font-size: 0.85rem;
        }

        .gateway-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .gateway-icon i {
            font-size: 32px;
            color: white;
        }

        .token-input-container {
            margin: 20px 0;
        }

        .token-input-group {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .token-box {
            display: flex;
            gap: 8px;
        }

        .token-input {
            width: 52px;
            height: 60px;
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            background: #ffffff;
            text-transform: uppercase;
            font-family: monospace;
            transition: all 0.2s;
        }

        .token-input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            transform: scale(1.02);
        }

        .token-input.error {
            border-color: #e53e3e;
            background: #fff5f5;
            animation: shake 0.3s ease;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .token-separator {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            padding: 0 4px;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin-top: 10px;
        }

        .btn-submit {
            width: 100%;
            max-width: 280px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-submit i {
            margin-right: 8px;
            font-size: 1rem;
        }

        .btn-reset {
            background: none;
            border: none;
            color: #667eea;
            font-size: 0.85rem;
            margin-top: 16px;
            cursor: pointer;
            font-weight: 600;
            padding: 8px 16px;
            width: auto;
        }

        .btn-reset:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            line-height: 1.4;
            display: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid #38a169;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border-left: 4px solid #e53e3e;
        }

        .alert-info {
            background: #bee3f8;
            color: #2c5282;
            border-left: 4px solid #3182ce;
        }

        .alert-warning {
            background: #feebc8;
            color: #7b341e;
            border-left: 4px solid #ed8936;
        }

        /* ========== NEW SUCCESS CARD STYLES ========== */
        .success-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 24px;
            padding: 0;
            margin-top: 20px;
            animation: fadeInUp 0.4s ease;
            border: 2px solid #22c55e;
            box-shadow: 0 10px 25px -5px rgba(34, 197, 94, 0.3);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-header {
            background: #22c55e;
            border-radius: 22px 22px 0 0;
            padding: 20px;
            text-align: center;
            color: white;
        }

        .success-header i {
            font-size: 48px;
            background: white;
            color: #22c55e;
            border-radius: 50%;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .success-header h2 {
            font-size: 28px;
            font-weight: 800;
            margin-top: 12px;
            margin-bottom: 0;
            letter-spacing: 1px;
        }

        .success-header p {
            font-size: 14px;
            margin-top: 5px;
            opacity: 0.9;
        }

        .token-display {
            background: white;
            margin: 20px;
            padding: 15px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .token-display-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 600;
        }

        .token-display-code {
            font-size: 36px;
            font-weight: 800;
            font-family: monospace;
            letter-spacing: 6px;
            color: #1e293b;
            background: #f8fafc;
            padding: 12px;
            border-radius: 16px;
            margin-top: 8px;
            border: 1px solid #e2e8f0;
        }

        .student-badge {
            display: flex;
            align-items: center;
            gap: 16px;
            background: white;
            margin: 0 20px 20px 20px;
            padding: 16px;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .student-avatar-sm {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #22c55e;
        }

        .student-info-sm {
            flex: 1;
        }

        .student-info-sm h3 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #0f172a;
            text-transform: uppercase;
        }

        .student-info-sm p {
            font-size: 13px;
            color: #475569;
            margin: 0;
        }

        .info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 0 20px 20px 20px;
        }

        .info-card {
            flex: 1;
            min-width: 100px;
            background: white;
            padding: 12px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .info-card i {
            font-size: 20px;
            color: #22c55e;
            margin-bottom: 6px;
            display: block;
        }

        .info-card .label {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .info-card .value {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .btn-new-success {
            background: #22c55e;
            max-width: 280px;
            margin: 0 auto;
            display: block;
        }

        .btn-new-success:hover {
            background: #16a34a;
            transform: translateY(-2px);
        }

        /* End of New Success Card Styles */

        .loading-state {
            text-align: center;
            padding: 30px 20px;
        }

        .spinner {
            display: inline-block;
            width: 32px;
            height: 32px;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.75rem;
        }

        .footer a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .help-text {
            text-align: center;
            margin-top: 16px;
            font-size: 0.75rem;
            color: #718096;
        }

        .help-text i {
            margin-right: 4px;
        }

        .resend-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .resend-btn {
            background: none;
            border: 2px solid #e2e8f0;
            color: #4a5568;
            width: 100%;
            padding: 12px;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .resend-btn:hover {
            border-color: #667eea;
            color: #667eea;
            background: #f7fafc;
        }

        .resend-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .resend-modal.active {
            opacity: 1;
            visibility: visible;
        }

        .resend-modal-content {
            background: white;
            border-radius: 28px;
            max-width: 400px;
            width: 90%;
            padding: 24px;
            animation: slideUp 0.3s ease;
        }

        .resend-modal-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .resend-modal-header i {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 12px;
        }

        .resend-modal-header h4 {
            font-weight: 700;
            color: #2d3748;
        }

        .radio-group {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
        }

        .radio-group .form-check {
            flex: 1;
        }

        .resend-close {
            background: none;
            border: none;
            color: #718096;
            font-size: 0.85rem;
            margin-top: 16px;
            cursor: pointer;
        }

        /* Transport Card Styles */
        .transport-card {
            transition: all 0.2s ease;
        }

        .transport-card i {
            font-size: 20px;
            margin-bottom: 6px;
            display: block;
        }

        .transport-card .label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .transport-card .value {
            font-size: 14px;
            font-weight: 800;
            margin-top: 4px;
            text-transform: uppercase;
        }

        /* Animation for transport card */
        .transport-card {
            animation: pulse 0.5s ease;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 12px;
            }

            .gateway-card {
                padding: 20px 16px;
                border-radius: 24px;
            }

            .gateway-header h2 {
                font-size: 1.3rem;
            }

            .gateway-icon {
                width: 55px;
                height: 55px;
            }

            .gateway-icon i {
                font-size: 26px;
            }

            .token-input {
                width: 45px;
                height: 52px;
                font-size: 22px;
                border-radius: 12px;
            }

            .token-separator {
                font-size: 20px;
                padding: 0 2px;
            }

            .token-display-code {
                font-size: 24px;
                letter-spacing: 4px;
            }

            .info-grid {
                gap: 8px;
            }

            .info-card {
                min-width: 70px;
                padding: 8px;
            }

            .info-card .value {
                font-size: 11px;
            }

            .student-avatar-sm {
                width: 50px;
                height: 50px;
            }

            .student-info-sm h3 {
                font-size: 14px;
            }
        }

        @media (max-width: 380px) {
            .token-input {
                width: 38px;
                height: 45px;
                font-size: 18px;
                border-radius: 10px;
            }

            .token-box {
                gap: 6px;
            }

            .token-input-group {
                gap: 4px;
            }

            .token-separator {
                font-size: 16px;
            }

            .token-display-code {
                font-size: 20px;
                letter-spacing: 3px;
            }
        }
    </style>
</head>

<body>
    <div class="gateway-container">
        <div class="gateway-card">
            <div class="gateway-header">
                <div class="gateway-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h2>Gate Pass Verification</h2>
                <p>Ingiza msimbo uliopokea kwenye simu yako</p>
            </div>

            <div id="alertBox" class="alert"></div>

            <div id="tokenSection">
                <div class="token-input-container">
                    <div class="token-input-group">
                        <div class="token-box">
                            <input type="text" class="token-input" maxlength="1" data-idx="0" autocomplete="off"
                                inputmode="text">
                            <input type="text" class="token-input" maxlength="1" data-idx="1" autocomplete="off"
                                inputmode="text">
                            <input type="text" class="token-input" maxlength="1" data-idx="2" autocomplete="off"
                                inputmode="text">
                        </div>
                        <div class="token-separator">—</div>
                        <div class="token-box">
                            <input type="text" class="token-input" maxlength="1" data-idx="3" autocomplete="off"
                                inputmode="text">
                            <input type="text" class="token-input" maxlength="1" data-idx="4" autocomplete="off"
                                inputmode="text">
                            <input type="text" class="token-input" maxlength="1" data-idx="5" autocomplete="off"
                                inputmode="text">
                        </div>
                    </div>
                </div>

                <div class="btn-container">
                    <button id="verifyBtn" class="btn-submit" disabled>
                        <i class="fas fa-check-circle"></i> Hakiki Token
                    </button>
                </div>

                <div class="text-center">
                    <button id="resetBtn" class="btn-reset">
                        <i class="fas fa-undo-alt"></i> Futa
                    </button>
                </div>

                <div class="resend-section">
                    <button id="showResendModalBtn" class="resend-btn">
                        <i class="fas fa-redo-alt me-2"></i> Umepoteza Token? Omba Token
                    </button>
                </div>
            </div>

            <div id="studentSection" style="display: none;"></div>

            <div id="loadingSection" style="display: none;" class="loading-state">
                <div class="spinner"></div>
                <p class="mt-2 mb-0" style="color: #718096;">Inachakata...</p>
            </div>
        </div>

        <div class="footer">
            <p><a href="{{ route('welcome') }}"><i class="fas fa-home"></i> Nyumbani</a> |
                <a href="{{ route('tokens.verify') }}"><i class="fas fa-sync-alt"></i> Refresh</a>
            </p>
            <p>© {{ date('Y') }} ShuleApp. Haki zote zimehifadhiwa</p>
        </div>
    </div>

    <div id="resendModal" class="resend-modal">
        <div class="resend-modal-content">
            <div class="resend-modal-header">
                <i class="fas fa-paper-plane"></i>
                <h4>Omba Token Tena</h4>
                <p class="text-muted small">Token itatumwa kwa simu ya mzazi</p>
            </div>

            <div id="modalAlert" class="alert" style="display: none;"></div>

            <form id="resendForm">
                @csrf
                <div class="radio-group">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="search_type" id="modalTypeAdmission"
                            value="admission" checked>
                        <label class="form-check-label" for="modalTypeAdmission">
                            <i class="fas fa-id-card"></i> Admission
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="search_type" id="modalTypePhone"
                            value="phone">
                        <label class="form-check-label" for="modalTypePhone">
                            <i class="fas fa-phone"></i> Simu ya Mzazi
                        </label>
                    </div>
                </div>

                <div id="modalAdmissionField" class="mb-3">
                    <label class="form-label fw-bold">Namba ya Admission</label>
                    <input type="text" id="modalAdmissionInput" class="form-control" placeholder="Mfano: SSC-001"
                        autocomplete="off">
                    <small class="text-muted">Ingiza namba ya admission ya mwanafunzi</small>
                </div>

                <div id="modalPhoneField" class="mb-3" style="display: none;">
                    <label class="form-label fw-bold">Namba ya Simu ya Mzazi</label>
                    <input type="tel" id="modalPhoneInput" class="form-control" placeholder="Mfano: 0712345678"
                        autocomplete="off">
                    <small class="text-muted">Ingiza namba ya simu iliyosajiliwa kwa mzazi</small>
                </div>

                <button type="submit" id="modalSubmitBtn" class="btn-submit" style="margin-top: 0;">
                    <i class="fas fa-paper-plane me-2"></i> Tuma Token Tena
                </button>

                <div class="text-center mt-3">
                    <button type="button" id="closeModalBtn" class="resend-close">
                        <i class="fas fa-times"></i> Funga
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            // DOM Elements
            const tokenInputs = document.querySelectorAll('.token-input');
            const verifyBtn = document.getElementById('verifyBtn');
            const resetBtn = document.getElementById('resetBtn');
            const alertBox = document.getElementById('alertBox');
            const tokenSection = document.getElementById('tokenSection');
            const studentSection = document.getElementById('studentSection');
            const loadingSection = document.getElementById('loadingSection');

            // Modal Elements
            const resendModal = document.getElementById('resendModal');
            const showResendModalBtn = document.getElementById('showResendModalBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const modalAlert = document.getElementById('modalAlert');
            const resendForm = document.getElementById('resendForm');
            const modalSubmitBtn = document.getElementById('modalSubmitBtn');
            const modalAdmissionField = document.getElementById('modalAdmissionField');
            const modalPhoneField = document.getElementById('modalPhoneField');
            const modalAdmissionInput = document.getElementById('modalAdmissionInput');
            const modalPhoneInput = document.getElementById('modalPhoneInput');
            const modalTypeAdmission = document.getElementById('modalTypeAdmission');
            const modalTypePhone = document.getElementById('modalTypePhone');

            let isLoading = false;

            // Initialize all
            function init() {
                setupTokenInputs();
                setupModal();
                setupOfflineDetection();
                setupPWAUpdates();
                focusFirstInput();
            }

            // Token Input Setup
            function setupTokenInputs() {
                tokenInputs.forEach(input => {
                    input.addEventListener('input', handleTokenInput);
                    input.addEventListener('keydown', handleTokenKeydown);
                    input.addEventListener('paste', handleTokenPaste);
                });
                verifyBtn.addEventListener('click', verifyToken);
                resetBtn.addEventListener('click', resetAll);
            }

            function handleTokenInput(e) {
                const input = e.target;
                const idx = parseInt(input.dataset.idx);
                let val = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                input.value = val;
                if (val && idx < tokenInputs.length - 1) tokenInputs[idx + 1].focus();
                updateVerifyButton();
                hideAlert();
                removeErrorStyling();
            }

            function handleTokenKeydown(e) {
                const input = e.target;
                const idx = parseInt(input.dataset.idx);
                if (e.key === 'Backspace' && !input.value && idx > 0) {
                    tokenInputs[idx - 1].focus();
                    tokenInputs[idx - 1].value = '';
                    updateVerifyButton();
                }
            }

            function handleTokenPaste(e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                const clean = text.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 6);
                for (let i = 0; i < clean.length && i < tokenInputs.length; i++) {
                    tokenInputs[i].value = clean[i];
                }
                if (clean.length < tokenInputs.length) tokenInputs[clean.length].focus();
                else tokenInputs[5].focus();
                updateVerifyButton();
                hideAlert();
                removeErrorStyling();
            }

            function focusFirstInput() {
                setTimeout(() => tokenInputs[0]?.focus(), 100);
            }

            function removeErrorStyling() {
                tokenInputs.forEach(input => input.classList.remove('error'));
            }

            function addErrorStyling() {
                tokenInputs.forEach(input => {
                    if (input.value) input.classList.add('error');
                });
                setTimeout(() => tokenInputs.forEach(input => input.classList.remove('error')), 500);
            }

            function resetAll() {
                tokenInputs.forEach(input => input.value = '');
                focusFirstInput();
                updateVerifyButton();
                hideAlert();
                studentSection.style.display = 'none';
                tokenSection.style.display = 'block';
                verifyBtn.disabled = true;
                removeErrorStyling();
            }

            function updateVerifyButton() {
                const allFilled = Array.from(tokenInputs).every(input => input.value.length === 1);
                verifyBtn.disabled = !allFilled || isLoading;
            }

            function getFullToken() {
                return Array.from(tokenInputs).map(input => input.value).join('');
            }

            function showAlert(message, type) {
                alertBox.textContent = message;
                alertBox.className = `alert alert-${type}`;
                alertBox.style.display = 'block';
                alertBox.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                setTimeout(() => {
                    if (alertBox.style.display === 'block') alertBox.style.display = 'none';
                }, 5000);
            }

            function hideAlert() {
                alertBox.style.display = 'none';
            }

            // Modal Setup
            function setupModal() {
                showResendModalBtn.addEventListener('click', () => {
                    resendModal.classList.add('active');
                    resetModalForm();
                });
                closeModalBtn.addEventListener('click', () => resendModal.classList.remove('active'));
                resendModal.addEventListener('click', (e) => {
                    if (e.target === resendModal) resendModal.classList.remove('active');
                });

                modalTypeAdmission.addEventListener('change', () => {
                    modalAdmissionField.style.display = 'block';
                    modalPhoneField.style.display = 'none';
                    modalAdmissionInput.required = true;
                    modalPhoneInput.required = false;
                });
                modalTypePhone.addEventListener('change', () => {
                    modalAdmissionField.style.display = 'none';
                    modalPhoneField.style.display = 'block';
                    modalAdmissionInput.required = false;
                    modalPhoneInput.required = true;
                });
                resendForm.addEventListener('submit', handleResendToken);
            }

            function resetModalForm() {
                modalAdmissionInput.value = '';
                modalPhoneInput.value = '';
                modalAlert.style.display = 'none';
                modalTypeAdmission.checked = true;
                modalAdmissionField.style.display = 'block';
                modalPhoneField.style.display = 'none';
                modalAdmissionInput.required = true;
                modalPhoneInput.required = false;
                modalSubmitBtn.disabled = false;
                modalSubmitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Tuma Token Tena';
            }

            function showModalAlert(message, type) {
                modalAlert.textContent = message;
                modalAlert.className = `alert alert-${type}`;
                modalAlert.style.display = 'block';
                setTimeout(() => {
                    if (modalAlert.style.display === 'block') modalAlert.style.display = 'none';
                }, 5000);
            }

            async function handleResendToken(e) {
                e.preventDefault();
                const searchType = document.querySelector('input[name="search_type"]:checked').value;
                const identifier = searchType === 'admission' ? modalAdmissionInput.value.trim() : modalPhoneInput
                    .value.trim();
                if (!identifier) {
                    showModalAlert('Tafadhali ingiza taarifa sahihi', 'error');
                    return;
                }

                modalSubmitBtn.disabled = true;
                modalSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Inachakata...';

                try {
                    const response = await fetch('{{ route('tokens.resend') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            identifier_type: searchType,
                            identifier: identifier
                        })
                    });
                    const data = await response.json();
                    if (data.success) {
                        showModalAlert(data.message, 'success');
                        setTimeout(() => {
                            resendModal.classList.remove('active');
                            resetModalForm();
                        }, 2000);
                    } else {
                        showModalAlert(data.message, 'error');
                    }
                } catch (error) {
                    showModalAlert('Hitilafu ya mtandao. Tafadhali jaribu tena.', 'error');
                } finally {
                    modalSubmitBtn.disabled = false;
                    modalSubmitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Tuma Token Tena';
                }
            }

            // Student Info Display - NEW DESIGN with colored transport status
            function showStudentInfo(data) {
                const student = data.student;
                const installment = data.installment;
                const token = data.token;
                const formattedToken = token.token.substring(0, 3) + '-' + token.token.substring(3, 6);
                const studentImage = student.image ? '/storage/students/' + student.image :
                    '/storage/students/student.jpg';

                // Determine transport status color and icon
                const hasTransport = student.has_transport;
                const transportIcon = hasTransport ? 'fa-bus' : 'fa-walking';
                const transportColor = hasTransport ? '#22c55e' : '#ef4444';
                const transportBg = hasTransport ? '#dcfce7' : '#fee2e2';
                const transportText = hasTransport ? 'ANATUMIA' : 'HATUMII';
                const transportStatusClass = hasTransport ? 'transport-yes' : 'transport-no';

                studentSection.innerHTML = `
                    <div class="success-card">
                        <div class="success-header">
                            <i class="fas fa-check-circle"></i>
                            <h2>ACTIVE</h2>
                            <p>Gate Pass Valid</p>
                        </div>

                        <div class="token-display">
                            <div class="token-display-label">GATE PASS TOKEN</div>
                            <div class="token-display-code">${escapeHtml(formattedToken)}</div>
                        </div>

                        <div class="student-badge" style="margin-top:12px;">
                            <img src="${studentImage}" class="student-avatar-sm" onerror="this.src='/storage/students/student.jpg'">
                            <div class="student-info-sm">
                                <h3>${escapeHtml(student.first_name)} ${escapeHtml(student.last_name)}</h3>
                                <p class="text-uppercase"><i class="fas fa-id-card"></i> ${escapeHtml(student.admission_number || 'N/A')}</p>
                            </div>
                        </div>

                        <div class="info-grid">
                            <div class="info-card">
                                <i class="fas fa-graduation-cap"></i>
                                <div class="label">DARASA</div>
                                <div class="value">${student.class ? escapeHtml(student.class.class_name) : 'N/A'}</div>
                            </div>
                            <div class="info-card">
                                <i class="fas fa-calendar-week"></i>
                                <div class="label">AWAMU YA KULIPA</div>
                                <div class="value">${escapeHtml(installment.name)}</div>
                            </div>
                            <div class="info-card">
                                <i class="fas fa-clock"></i>
                                <div class="label">MUDA WA KUISHA</div>
                                <div class="value">${formatDate(token.expires_at)}</div>
                            </div>
                            <div class="info-card transport-card" style="background: ${transportBg}; border: 1px solid ${transportColor};">
                                <i class="fas ${transportIcon}" style="color: ${transportColor};"></i>
                                <div class="label" style="color: ${transportColor};">USAFIRI</div>
                                <div class="value" style="color: ${transportColor}; font-weight: 800;">${transportText}</div>
                            </div>
                        </div>

                        <div class="btn-container">
                            <button id="newVerifyBtn" class="btn-submit btn-new-success">
                                <i class="fas fa-search"></i> HAKIKI NYINGINE
                            </button>
                        </div>
                    </div>
                `;

                studentSection.style.display = 'block';
                tokenSection.style.display = 'none';
                document.getElementById('newVerifyBtn').addEventListener('click', () => resetAll());
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;'
                } [m]));
            }

            function formatDate(dateStr) {
                try {
                    return new Date(dateStr).toLocaleDateString('sw-TZ', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                } catch (e) {
                    return dateStr;
                }
            }

            // Offline Detection & PWA Updates
            function setupOfflineDetection() {
                window.addEventListener('online', () => {
                    const alert = document.getElementById('alertBox');
                    if (alert && alert.classList.contains('alert-warning')) alert.style.display = 'none';
                    registerBackgroundSync();
                });
                window.addEventListener('offline', () => {
                    const alert = document.getElementById('alertBox');
                    if (alert) {
                        alert.textContent =
                            '⚠️ Hali ya Offline. Verification itafanya kazi kwa token zilizohifadhiwa.';
                        alert.className = 'alert alert-warning';
                        alert.style.display = 'block';
                    }
                });
                if (!navigator.onLine) {
                    const alert = document.getElementById('alertBox');
                    if (alert) {
                        alert.textContent = '⚠️ Hali ya Offline. Verification itafanya kazi kwa token zilizohifadhiwa.';
                        alert.className = 'alert alert-warning';
                        alert.style.display = 'block';
                    }
                }
            }

            async function registerBackgroundSync() {
                if ('serviceWorker' in navigator && 'SyncManager' in window) {
                    try {
                        const registration = await navigator.serviceWorker.ready;
                        await registration.sync.register('sync-tokens');
                        console.log('Background sync registered');
                    } catch (error) {
                        console.log('Background sync failed:', error);
                    }
                }
            }

            function setupPWAUpdates() {
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.ready.then(registration => {
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker
                                    .controller) {
                                    showUpdateNotification();
                                }
                            });
                        });
                    });
                }
            }

            function showUpdateNotification() {
                const alert = document.getElementById('alertBox');
                if (alert) {
                    alert.innerHTML =
                        `<i class="fas fa-sync-alt"></i> Update inapatikana! Tafadhali <strong>refresh</strong> ukurasa. <button onclick="location.reload()" class="btn btn-sm btn-light ms-2">Refresh</button>`;
                    alert.className = 'alert alert-info';
                    alert.style.display = 'block';
                }
            }

            // Token Verification
            async function verifyToken() {
                const tokenCode = getFullToken();
                if (tokenCode.length !== 6) {
                    showAlert('Tafadhali ingiza token kamili (herufi 6)', 'error');
                    addErrorStyling();
                    return;
                }

                isLoading = true;
                verifyBtn.disabled = true;
                tokenSection.style.display = 'none';
                loadingSection.style.display = 'block';
                hideAlert();

                try {
                    const response = await fetch('{{ route('tokens.verify.submit') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            token: tokenCode
                        })
                    });
                    const data = await response.json();
                    if (response.ok && data.success) {
                        showAlert(data.message, 'success');
                        showStudentInfo(data.data);
                    } else {
                        showAlert(data.message || 'Token si sahihi au imekwisha muda wake.', 'error');
                        addErrorStyling();
                        setTimeout(() => resetAll(), 5000);
                    }
                } catch (error) {
                    showAlert('Hitilafu ya mtandao. Tafadhali jaribu tena.', 'error');
                    addErrorStyling();
                    setTimeout(() => resetAll(), 5000);
                } finally {
                    isLoading = false;
                    loadingSection.style.display = 'none';
                    verifyBtn.disabled = false;
                }
            }

            // Start everything
            init();
        })();
    </script>
</body>

</html>
