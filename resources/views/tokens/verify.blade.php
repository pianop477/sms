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
        /* All original styles remain the same */
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
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
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
            to { transform: rotate(360deg); }
        }

        /* Success Card - Clean & Simple */
        .success-card {
            background: white;
            border-radius: 24px;
            padding: 0;
            margin-top: 20px;
            animation: fadeInUp 0.4s ease;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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

        /* Simple header - not overpowering */
        .success-header {
            background: #f0fdf4;
            border-radius: 24px 24px 0 0;
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #dcfce7;
        }

        .success-header i {
            font-size: 24px;
            color: #22c55e;
            margin-right: 8px;
        }

        .success-header h2 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: #166534;
            display: inline-block;
        }

        .success-header p {
            font-size: 12px;
            margin: 4px 0 0;
            color: #64748b;
        }

        /* Token display - minimal */
        .token-display {
            background: #f8fafc;
            margin: 16px;
            padding: 8px;
            border-radius: 12px;
            text-align: center;
        }

        .token-display-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 600;
        }

        .token-display-code {
            font-size: 16px;
            font-weight: 700;
            font-family: monospace;
            letter-spacing: 2px;
            color: #1e293b;
            margin-top: 2px;
        }

        /* Photo Section - Clean with zoom capability */
        .identity-verification-section {
            background: white;
            border-radius: 20px;
            padding: 16px;
            margin: 0 16px 16px 16px;
            border: 1px solid #e2e8f0;
        }

        .student-photo-container {
            text-align: center;
            position: relative;
        }

        .photo-frame {
            position: relative;
            display: inline-block;
            cursor: pointer;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            transition: all 0.2s;
        }

        .photo-frame:hover {
            border-color: #667eea;
            transform: scale(1.01);
        }

        .student-large-photo {
            width: 180px;
            height: 180px;
            object-fit: cover;
            display: block;
        }

        @media (min-width: 640px) {
            .student-large-photo {
                width: 200px;
                height: 200px;
            }
        }

        .zoom-icon {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(0,0,0,0.6);
            color: white;
            padding: 6px;
            border-radius: 50%;
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .photo-frame:hover .zoom-icon {
            opacity: 1;
        }

        /* Modal for zoomed photo */
        .photo-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .photo-modal.active {
            opacity: 1;
            visibility: visible;
        }

        .photo-modal img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            border-radius: 8px;
        }

        .face-match-instruction {
            background: #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 10px;
            border-radius: 10px;
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #92400e;
        }

        .face-match-instruction i {
            font-size: 16px;
            color: #f59e0b;
        }

        /* Student Details - Clean grid */
        .student-details-panel {
            background: #f8fafc;
            border-radius: 16px;
            padding: 12px;
            margin-top: 12px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #475569;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .detail-label i {
            width: 16px;
            font-size: 12px;
            color: #667eea;
        }

        .detail-value {
            font-weight: 600;
            color: #0f172a;
            font-size: 12px;
            text-align: right;
        }

        /* Transport status - simple */
        .transport-status-card {
            background: #f8fafc;
            margin: 0 16px 16px 16px;
            padding: 10px 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .transport-status-card .label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
        }

        .transport-status-card .value {
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Buttons in success card */
        .confirm-access-section {
            padding: 0 16px 16px 16px;
        }

        .btn-confirm {
            background: #22c55e;
            max-width: 100%;
            padding: 10px;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-confirm:hover {
            background: #16a34a;
        }

        .btn-new-verify {
            background: #64748b;
            max-width: 100%;
            margin-top: 8px;
            padding: 10px;
            font-size: 13px;
        }

        .btn-new-verify:hover {
            background: #475569;
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

        /* Modal Styles */
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

        /* Responsive - maintaining original breakpoints */
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

            .student-large-photo {
                width: 150px !important;
                height: 150px !important;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .detail-value {
                text-align: left;
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

            .student-large-photo {
                width: 130px !important;
                height: 130px !important;
            }
        }
    </style>
</head>

<body>
    <div class="gateway-container">
        <div class="gateway-card">
            <div class="gateway-header">
                <div class="gateway-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2>Gate Pass Verification</h2>
                <p>Ingiza msimbo uliopokea kwenye simu yako</p>
            </div>

            <div id="alertBox" class="alert"></div>

            <div id="tokenSection">
                <div class="token-input-container">
                    <div class="token-input-group">
                        <div class="token-box">
                            <input type="text" class="token-input" maxlength="1" data-idx="0" autocomplete="off" inputmode="text">
                            <input type="text" class="token-input" maxlength="1" data-idx="1" autocomplete="off" inputmode="text">
                            <input type="text" class="token-input" maxlength="1" data-idx="2" autocomplete="off" inputmode="text">
                        </div>
                        <div class="token-separator">—</div>
                        <div class="token-box">
                            <input type="text" class="token-input" maxlength="1" data-idx="3" autocomplete="off" inputmode="text">
                            <input type="text" class="token-input" maxlength="1" data-idx="4" autocomplete="off" inputmode="text">
                            <input type="text" class="token-input" maxlength="1" data-idx="5" autocomplete="off" inputmode="text">
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

    <!-- Photo Zoom Modal -->
    <div id="photoZoomModal" class="photo-modal">
        <img id="zoomedPhoto" src="" alt="Student Photo">
    </div>

    <!-- Resend Token Modal -->
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
                        <input class="form-check-input" type="radio" name="search_type" id="modalTypeAdmission" value="admission" checked>
                        <label class="form-check-label" for="modalTypeAdmission">
                            <i class="fas fa-id-card"></i> Admission
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="search_type" id="modalTypePhone" value="phone">
                        <label class="form-check-label" for="modalTypePhone">
                            <i class="fas fa-phone"></i> Simu
                        </label>
                    </div>
                </div>

                <div id="modalAdmissionField" class="mb-3">
                    <label class="form-label fw-bold">Namba ya Usajili</label>
                    <input type="text" id="modalAdmissionInput" class="form-control" placeholder="Mfano: SSC-001" autocomplete="off">
                    <small class="text-muted">Ingiza namba ya usajili ya mwanafunzi</small>
                </div>

                <div id="modalPhoneField" class="mb-3" style="display: none;">
                    <label class="form-label fw-bold">Simu ya Mzazi</label>
                    <input type="tel" id="modalPhoneInput" class="form-control" placeholder="Mfano: 0712345678" autocomplete="off">
                    <small class="text-muted">Ingiza namba ya simu iliyosajiliwa kwa mzazi</small>
                </div>

                <button type="submit" id="modalSubmitBtn" class="btn-submit" style="margin-top: 0;">
                    <i class="fas fa-paper-plane me-2"></i> Tuma Ombi
                </button>

                <div class="text-center mt-3">
                    <button type="button" id="closeModalBtn" class="resend-close btn btn-link">
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

            // Photo Zoom Elements
            const photoZoomModal = document.getElementById('photoZoomModal');
            const zoomedPhoto = document.getElementById('zoomedPhoto');

            let isLoading = false;
            let verificationTimeout = null;
            let currentStudentId = null;
            let currentTokenData = null;

            function init() {
                setupTokenInputs();
                setupModal();
                setupPhotoZoom();
                setupOfflineDetection();
                focusFirstInput();
            }

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
                if (verificationTimeout) clearTimeout(verificationTimeout);
                currentStudentId = null;
                currentTokenData = null;
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
                alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => {
                    if (alertBox.style.display === 'block') alertBox.style.display = 'none';
                }, 5000);
            }

            function hideAlert() {
                alertBox.style.display = 'none';
            }

            // Photo Zoom Setup
            function setupPhotoZoom() {
                photoZoomModal.addEventListener('click', () => {
                    photoZoomModal.classList.remove('active');
                });
            }

            function showPhotoZoom(imageSrc) {
                zoomedPhoto.src = imageSrc;
                photoZoomModal.classList.add('active');
            }

            // Student Info Display - Clean Version
            function showStudentInfo(data) {
                const student = data.student;
                const installment = data.installment;
                const token = data.token;
                const formattedToken = token.token.substring(0, 3) + '-' + token.token.substring(3, 6);
                const studentImage = student.image ? '/storage/students/' + student.image : '/storage/students/student.jpg';

                const hasTransport = student.has_transport;
                const transportIcon = hasTransport ? 'fa-bus' : 'fa-walking';
                const transportColor = hasTransport ? '#22c55e' : '#ef4444';
                const transportText = hasTransport ? 'ANATUMIA' : 'HATUMII';

                currentStudentId = student.id;
                currentTokenData = token;

                studentSection.innerHTML = `
                    <div class="success-card">
                        <div class="success-header">
                            <div>
                                <i class="fas fa-check-circle"></i>
                                <h2>HALALI</h2>
                                <p>Gate Pass ni Sahihi</p>
                            </div>
                        </div>

                        <div class="identity-verification-section">
                            <div class="student-photo-container">
                                <div class="photo-frame" onclick="showPhotoZoom('${studentImage}')">
                                    <img src="${studentImage}" class="student-large-photo"
                                         onerror="this.src='/storage/students/student.jpg'"
                                         alt="Student Photo">
                                    <div class="zoom-icon">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                </div>

                                <div class="face-match-instruction">
                                    <i class="fas fa-user-check"></i>
                                    <span><strong>Kumbuka:</strong> Linganisha sura ya mwanafunzi aliye mbele yako na picha hii</span>
                                </div>
                            </div>

                            <div class="student-details-panel">
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-user-graduate"></i> Jina Kamili</div>
                                    <div class="detail-value">${escapeHtml(student.first_name)} ${escapeHtml(student.last_name)}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-id-card"></i> Namba ya Usajili</div>
                                    <div class="detail-value">${escapeHtml(student.admission_number || 'N/A')}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-chalkboard-user"></i> Darasa</div>
                                    <div class="detail-value text-uppercase">${student.class ? escapeHtml(student.class.class_name) : 'N/A'}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-calendar-week"></i> Awamu ya Malipo</div>
                                    <div class="detail-value text-capitalize">${escapeHtml(installment.name)}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-clock"></i> Muda wa Kuisha</div>
                                    <div class="detail-value">${formatDate(token.expires_at)}</div>
                                </div>
                            </div>
                        </div>

                        <div class="transport-status-card">
                            <div class="label">USAFIRI</div>
                            <div class="value" style="color: ${transportColor};">
                                <i class="fas ${transportIcon}"></i>
                                ${transportText}
                            </div>
                        </div>

                        <div class="confirm-access-section">
                            <button id="confirmAccessBtn" class="btn-submit btn-confirm">
                                <i class="fas fa-check-double"></i> THIBITISHA NA RUHUSU
                            </button>
                            <button id="newVerifyBtn" class="btn-submit btn-new-verify">
                                <i class="fas fa-search"></i> HAKIKI MWANAFUNZI MWINGINE
                            </button>
                        </div>
                    </div>
                `;

                studentSection.style.display = 'block';
                tokenSection.style.display = 'none';

                if (verificationTimeout) clearTimeout(verificationTimeout);
                verificationTimeout = setTimeout(() => {
                    if (studentSection.style.display === 'block') {
                        showAlert('Kipindi cha verification kimeisha. Tafadhali verify tena.', 'warning');
                        resetAll();
                    }
                }, 120000);

                document.getElementById('confirmAccessBtn').addEventListener('click', () => confirmAccess());
                document.getElementById('newVerifyBtn').addEventListener('click', () => resetAll());
            }

            function confirmAccess() {
                showAlert('✅ Ruhusa imetolewa! Mwanafunzi anaweza kuingia.', 'success');
                console.log('Access granted for student ID:', currentStudentId, 'Token:', currentTokenData?.token, 'Time:', new Date().toISOString());
                setTimeout(() => {
                    resetAll();
                    showAlert('Tayari kwa verification nyingine', 'info');
                }, 3000);
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;'
                }[m]));
            }

            function formatDate(dateStr) {
                try {
                    return new Date(dateStr).toLocaleDateString('sw-TZ', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch (e) {
                    return dateStr;
                }
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
                modalSubmitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Tuma Ombi';
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
                const identifier = searchType === 'admission' ? modalAdmissionInput.value.trim() : modalPhoneInput.value.trim();

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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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

            function setupOfflineDetection() {
                window.addEventListener('online', () => {
                    const alert = document.getElementById('alertBox');
                    if (alert && alert.classList.contains('alert-warning')) alert.style.display = 'none';
                });
                window.addEventListener('offline', () => {
                    showAlert('⚠️ Hali ya Offline. Verification itafanya kazi kwa token zilizohifadhiwa.', 'warning');
                });
            }

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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ token: tokenCode })
                    });
                    const data = await response.json();

                    if (response.ok && data.success) {
                        showAlert(data.message, 'success');
                        showStudentInfo(data.data);
                    } else {
                        showAlert(data.message || 'Token si sahihi au imekwisha muda wake.', 'error');
                        addErrorStyling();
                        setTimeout(() => resetAll(), 3000);
                    }
                } catch (error) {
                    showAlert('Hitilafu ya mtandao. Tafadhali jaribu tena.', 'error');
                    addErrorStyling();
                    setTimeout(() => resetAll(), 3000);
                } finally {
                    isLoading = false;
                    loadingSection.style.display = 'none';
                    verifyBtn.disabled = false;
                }
            }

            // Expose showPhotoZoom to global scope for onclick
            window.showPhotoZoom = showPhotoZoom;

            init();
        })();
    </script>
</body>

</html>
