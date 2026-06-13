<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShuleApp | Gate Pass Verification</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon.ico') }}">
    <!-- Bootstrap 5 CSS (lightweight only for grid & utilities if needed) -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.5.2-web/css/all.css') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(145deg, #5B66E8 0%, #8B5CF6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }

        /* Main container - full width mobile friendly */
        .gateway-container {
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
        }

        /* Card design */
        .gateway-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(0px);
            border-radius: 32px;
            box-shadow: 0 20px 35px -8px rgba(0, 0, 0, 0.25);
            width: 100%;
            padding: 24px 20px 28px;
            transition: all 0.2s ease;
        }

        /* Header area */
        .gateway-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .gateway-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #5B66E8 0%, #8B5CF6 100%);
            border-radius: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 8px 14px rgba(91, 102, 232, 0.25);
        }

        .gateway-icon i {
            font-size: 32px;
            color: white;
        }

        .gateway-header h2 {
            font-size: 1.55rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b, #2d3a5e);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
        }

        .gateway-header p {
            color: #475569;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Token input area - BEST mobile optimization */
        .token-input-container {
            margin: 10px 0 16px;
        }

        .token-input-group {
            display: flex;
            justify-content: center;
        }

        .token-box {
            display: flex;
            gap: 14px;
            flex-wrap: nowrap;
            justify-content: center;
        }

        /* LARGE, TAPPABLE TOKEN INPUTS */
        .token-input {
            width: 85px;
            height: 85px;
            text-align: center;
            font-size: 48px;
            font-weight: 800;
            font-family: 'SF Mono', 'JetBrains Mono', monospace;
            border: 3px solid #e2e8f0;
            border-radius: 22px;
            background: #ffffff;
            color: #0f172a;
            transition: all 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            letter-spacing: 4px;
        }

        /* Remove spinners */
        .token-input::-webkit-outer-spin-button,
        .token-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .token-input[type=number] {
            -moz-appearance: textfield;
            appearance: textfield;
        }

        .token-input:focus {
            border-color: #5B66E8;
            outline: none;
            box-shadow: 0 0 0 4px rgba(91, 102, 232, 0.2);
            transform: scale(1.02);
        }

        .token-input.error {
            border-color: #dc2626;
            background: #fef2f2;
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

        /* Buttons - high tappable area */
        .btn-container {
            display: flex;
            justify-content: center;
            margin: 12px 0 8px;
        }

        .btn-submit {
            width: 100%;
            max-width: 320px;
            padding: 15px 20px;
            background: linear-gradient(105deg, #5B66E8 0%, #8B5CF6 100%);
            color: white;
            border: none;
            border-radius: 44px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 6px 14px rgba(91, 102, 232, 0.3);
        }

        .btn-submit:active {
            transform: scale(0.97);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }

        .btn-reset {
            background: transparent;
            border: 1px solid #cbd5e1;
            color: #334155;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 40px;
            width: auto;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
            transition: 0.2s;
        }

        .btn-reset:active {
            background: #f1f5f9;
        }

        /* Alert messages */
        .alert {
            padding: 14px 18px;
            border-radius: 24px;
            margin-bottom: 22px;
            font-size: 0.9rem;
            font-weight: 500;
            display: none;
            backdrop-filter: blur(4px);
            animation: fadeSlide 0.2s ease;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #dff9e6;
            color: #0a3b2a;
            border-left: 5px solid #22c55e;
        }

        .alert-error {
            background: #ffe5e5;
            color: #7f1a1a;
            border-left: 5px solid #ef4444;
        }

        .alert-info {
            background: #e0f2fe;
            color: #075985;
            border-left: 5px solid #0ea5e9;
        }

        .alert-warning {
            background: #fff1e6;
            color: #9a3412;
            border-left: 5px solid #f97316;
        }

        /* Loading state */
        .loading-state {
            text-align: center;
            padding: 36px 20px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e2e8f0;
            border-top-color: #5B66E8;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 12px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Student success card - Mobile first readability */
        .success-card {
            background: white;
            border-radius: 28px;
            overflow: hidden;
            margin-top: 6px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .success-header {
            background: linear-gradient(115deg, #22c55e, #15803d);
            padding: 18px 12px;
            text-align: center;
        }

        .success-header i {
            font-size: 28px;
            color: white;
            margin-right: 8px;
        }

        .success-header h2 {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0;
            color: white;
            letter-spacing: 1px;
        }

        .success-header p {
            font-size: 0.75rem;
            font-weight: 500;
            margin: 6px 0 0;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Identity & photo */
        .identity-verification-section {
            padding: 20px 16px 12px;
        }

        .student-photo-container {
            text-align: center;
        }

        .photo-frame {
            display: inline-block;
            cursor: pointer;
            border-radius: 24px;
            overflow: hidden;
            border: 3px solid #e2e8f0;
            transition: 0.2s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .photo-frame:active {
            transform: scale(0.98);
        }

        .student-large-photo {
            width: 160px;
            height: 160px;
            object-fit: cover;
            display: block;
        }

        .face-match-instruction {
            background: #fef9e3;
            border-radius: 40px;
            padding: 10px 14px;
            margin: 14px auto 8px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #b45309;
            max-width: 95%;
        }

        /* Student details grid */
        .student-details-panel {
            background: #f8fafc;
            border-radius: 24px;
            padding: 14px 16px;
            margin: 12px 0 8px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 12px 0;
            border-bottom: 1px solid #e2edf2;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.2px;
        }

        .detail-label i {
            width: 20px;
            color: #5B66E8;
            font-size: 0.9rem;
        }

        .detail-value {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.9rem;
            text-align: right;
            max-width: 60%;
            word-break: break-word;
        }

        /* Transport status */
        .transport-status-card {
            background: #f1f5f9;
            margin: 4px 16px 16px;
            padding: 10px 18px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Double action buttons */
        .confirm-access-section {
            padding: 8px 16px 22px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-confirm {
            background: #16a34a;
            box-shadow: 0 4px 10px rgba(34, 197, 94, 0.3);
            max-width: 100%;
            padding: 14px;
            font-size: 1rem;
        }

        .btn-new-verify {
            background: #475569;
            max-width: 100%;
            padding: 14px;
            font-size: 0.95rem;
            box-shadow: none;
        }

        /* Resend button area */
        .resend-section {
            margin-top: 22px;
            padding-top: 16px;
            border-top: 1px solid #eef2f6;
        }

        .resend-btn {
            background: white;
            border: 2px solid #cbd5e1;
            color: #2c3e66;
            width: 100%;
            padding: 14px 12px;
            border-radius: 60px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .resend-btn:active {
            background: #f8fafc;
            border-color: #5B66E8;
        }

        /* Modal for resend token */
        .resend-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1100;
            opacity: 0;
            visibility: hidden;
            transition: 0.25s;
        }

        .resend-modal.active {
            opacity: 1;
            visibility: visible;
        }

        .resend-modal-content {
            background: white;
            border-radius: 40px;
            max-width: 380px;
            width: 88%;
            padding: 28px 24px;
            box-shadow: 0 30px 40px rgba(0, 0, 0, 0.3);
        }

        .resend-modal-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .resend-modal-header i {
            font-size: 52px;
            background: linear-gradient(135deg, #5B66E8, #8B5CF6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .form-control {
            border-radius: 60px;
            border: 2px solid #e2e8f0;
            padding: 14px 18px;
            font-size: 1rem;
            width: 100%;
        }

        .form-control:focus {
            border-color: #5B66E8;
            outline: none;
            box-shadow: 0 0 0 3px rgba(91, 102, 232, 0.2);
        }

        /* Photo zoom modal */
        .photo-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.92);
            z-index: 1200;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: 0.2s;
        }

        .photo-modal.active {
            opacity: 1;
            visibility: visible;
        }

        .photo-modal img {
            max-width: 90%;
            max-height: 85%;
            border-radius: 24px;
            object-fit: contain;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.7rem;
        }

        .footer a {
            color: white;
            font-weight: 500;
            text-decoration: none;
        }

        /* === RESPONSIVE: super mobile tweaks === */
        @media (max-width: 520px) {
            .gateway-card {
                padding: 20px 16px;
            }

            .token-input {
                width: 70px;
                height: 70px;
                font-size: 42px;
                border-radius: 20px;
            }

            .token-box {
                gap: 10px;
            }

            .student-large-photo {
                width: 130px;
                height: 130px;
            }

            .detail-value {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 440px) {
            .token-input {
                width: 62px;
                height: 62px;
                font-size: 36px;
                border-radius: 18px;
            }

            .token-box {
                gap: 8px;
            }

            .gateway-header h2 {
                font-size: 1.3rem;
            }

            .btn-submit {
                padding: 12px 18px;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 380px) {
            .token-input {
                width: 55px;
                height: 55px;
                font-size: 32px;
            }

            .token-box {
                gap: 6px;
            }

            .student-large-photo {
                width: 110px;
                height: 110px;
            }

            .face-match-instruction {
                font-size: 0.7rem;
                padding: 6px 10px;
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
                <h2>VERIFY GATE PASS</h2>
                <p>Weka msimbo wa tarakimu 4 uliotumwa</p>
            </div>

            <div id="alertBox" class="alert"></div>

            <div id="tokenSection">
                <div class="token-input-container">
                    <div class="token-input-group">
                        <div class="token-box">
                            <input type="number" class="token-input" maxlength="1" data-idx="0" autocomplete="off"
                                inputmode="numeric" pattern="[0-9]">
                            <input type="number" class="token-input" maxlength="1" data-idx="1" autocomplete="off"
                                inputmode="numeric" pattern="[0-9]">
                            <input type="number" class="token-input" maxlength="1" data-idx="2" autocomplete="off"
                                inputmode="numeric" pattern="[0-9]">
                            <input type="number" class="token-input" maxlength="1" data-idx="3" autocomplete="off"
                                inputmode="numeric" pattern="[0-9]">
                        </div>
                    </div>
                </div>

                <div class="btn-container">
                    <button id="verifyBtn" class="btn-submit" disabled>
                        <i class="fas fa-check-circle"></i> HAKIKI TOKEN
                    </button>
                </div>

                <div class="text-center">
                    <button id="resetBtn" class="btn-reset">
                        <i class="fas fa-eraser"></i> Futa namba
                    </button>
                </div>

                <div class="resend-section">
                    <button id="showResendModalBtn" class="resend-btn">
                        <i class="fas fa-envelope-open-text"></i> Sijapata token? Tuma tena
                    </button>
                </div>
            </div>

            <div id="studentSection" style="display: none;"></div>
            <div id="loadingSection" style="display: none;" class="loading-state">
                <div class="spinner"></div>
                <p class="mt-2" style="color: #475569; font-weight: 500;">Inathibitisha ...</p>
            </div>
        </div>

        <div class="footer">
            <p><a href="{{ route('welcome') }}"><i class="fas fa-home"></i> Nyumbani</a> |
                <a href="{{ route('tokens.verify') }}"><i class="fas fa-sync-alt"></i> Onyesha upya</a>
            </p>
            <p>© {{ date('Y') }} ShuleApp</p>
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
                <h3 class="mt-2">Omba Token upya</h3>
                <p class="text-muted">Tumia namba ya usajili ya mwanafunzi</p>
            </div>

            <div id="modalAlert" class="alert" style="display: none;"></div>

            <form id="resendForm">
                @csrf
                <div class="mb-4">
                    <label class="fw-bold mb-1">Namba ya Usajili</label>
                    <input type="text" id="modalAdmissionInput" class="form-control text-uppercase"
                        placeholder="mfano: SSC-001" autocomplete="off" required>
                </div>

                <div class="btn-container">
                    <button type="submit" id="modalSubmitBtn" class="btn-submit" style="max-width: 100%;">
                        <i class="fas fa-paper-plane"></i> Tuma ombi
                    </button>
                </div>
                <div class="text-center mt-3">
                    <button type="button" id="closeModalBtn" class="resend-close btn btn-link text-muted">
                        <i class="fas fa-times"></i> Funga
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script>
        (function() {
        // ========== OFFLINE TOKEN SYNC ==========
        // Register background sync
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            navigator.serviceWorker.ready.then(reg => {
                reg.sync.register('sync-tokens').catch(err => console.log('Sync reg failed:', err));
            });
        }

        // Function to refresh tokens when online
        async function refreshOfflineTokens() {
            if (!navigator.onLine) return;
            try {
                const response = await fetch('/api/offline/tokens');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.tokens && navigator.serviceWorker.controller) {
                        navigator.serviceWorker.controller.postMessage({
                            type: 'SYNC_TOKENS',
                            tokens: data.tokens
                        });
                        console.log('Offline tokens refreshed:', data.tokens.length);
                    }
                }
            } catch (e) {
                console.error('Failed to refresh tokens:', e);
            }
        }

        // Refresh tokens on page load and every hour
        if ('serviceWorker' in navigator) {
            refreshOfflineTokens();
            setInterval(refreshOfflineTokens, 60 * 60 * 1000);
        }

        // ========== DOM ELEMENTS ==========
        const tokenInputs = document.querySelectorAll('.token-input');
        const verifyBtn = document.getElementById('verifyBtn');
        const resetBtn = document.getElementById('resetBtn');
        const alertBox = document.getElementById('alertBox');
        const tokenSection = document.getElementById('tokenSection');
        const studentSection = document.getElementById('studentSection');
        const loadingSection = document.getElementById('loadingSection');

        const resendModal = document.getElementById('resendModal');
        const showResendModalBtn = document.getElementById('showResendModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modalAlert = document.getElementById('modalAlert');
        const resendForm = document.getElementById('resendForm');
        const modalSubmitBtn = document.getElementById('modalSubmitBtn');
        const modalAdmissionInput = document.getElementById('modalAdmissionInput');
        const photoZoomModal = document.getElementById('photoZoomModal');
        const zoomedPhoto = document.getElementById('zoomedPhoto');

        let isLoading = false;
        let verificationTimeout = null;
        let currentStudentId = null;

        const ALERT_DURATION = 3500;
        const VERIFY_SESSION_DURATION = 60000;

        function init() {
            setupTokenEvents();
            setupModalEvents();
            setupPhotoZoom();
            focusFirstInput();
        }

        function setupTokenEvents() {
            tokenInputs.forEach((input, idx) => {
                input.addEventListener('input', (e) => handleInput(e, idx));
                input.addEventListener('keydown', handleKeydown);
                input.addEventListener('paste', handlePaste);
                input.addEventListener('keypress', onlyNumbers);
            });
            verifyBtn.addEventListener('click', verifyToken);
            resetBtn.addEventListener('click', resetAll);
        }

        function onlyNumbers(e) {
            if (!/[0-9]/.test(String.fromCharCode(e.which))) e.preventDefault();
        }

        function handleInput(e, idx) {
            let val = e.target.value.replace(/[^0-9]/g, '').slice(-1);
            e.target.value = val;
            if (val && idx < tokenInputs.length - 1) {
                tokenInputs[idx + 1].focus();
            }
            updateVerifyButton();
            hideAlert();
            removeError();
            if (idx === tokenInputs.length - 1 || Array.from(tokenInputs).every(inp => inp.value.length === 1)) {
                autoSubmitIfComplete();
            }
        }

        function handleKeydown(e) {
            const idx = parseInt(e.target.dataset.idx);
            if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                tokenInputs[idx - 1].focus();
                tokenInputs[idx - 1].value = '';
                updateVerifyButton();
            }
        }

        function handlePaste(e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').substring(0, 4);
            for (let i = 0; i < text.length && i < tokenInputs.length; i++) {
                tokenInputs[i].value = text[i];
            }
            if (text.length < tokenInputs.length) tokenInputs[text.length].focus();
            else tokenInputs[3].focus();
            updateVerifyButton();
            hideAlert();
            removeError();
            autoSubmitIfComplete();
        }

        function focusFirstInput() {
            setTimeout(() => tokenInputs[0]?.focus(), 100);
        }

        function removeError() {
            tokenInputs.forEach(inp => inp.classList.remove('error'));
        }

        function addError() {
            tokenInputs.forEach(inp => {
                if (inp.value) inp.classList.add('error');
            });
            setTimeout(() => tokenInputs.forEach(inp => inp.classList.remove('error')), 500);
        }

        function resetAll() {
            tokenInputs.forEach(inp => inp.value = '');
            focusFirstInput();
            updateVerifyButton();
            hideAlert();
            studentSection.style.display = 'none';
            tokenSection.style.display = 'block';
            verifyBtn.disabled = true;
            if (verificationTimeout) clearTimeout(verificationTimeout);
            currentStudentId = null;
            removeError();
        }

        function updateVerifyButton() {
            const allFilled = Array.from(tokenInputs).every(inp => inp.value.length === 1);
            verifyBtn.disabled = !allFilled || isLoading;
        }

        function getFullToken() {
            return Array.from(tokenInputs).map(inp => inp.value).join('');
        }

        function showAlert(message, type, autoHide = true, duration = ALERT_DURATION) {
            alertBox.textContent = message;
            alertBox.className = `alert alert-${type}`;
            alertBox.style.display = 'block';
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (autoHide) setTimeout(() => { if (alertBox.style.display === 'block') alertBox.style.display = 'none'; }, duration);
        }

        function hideAlert() {
            alertBox.style.display = 'none';
        }

        function setupPhotoZoom() {
            photoZoomModal.addEventListener('click', () => photoZoomModal.classList.remove('active'));
        }

        window.showPhotoZoom = (src) => {
            zoomedPhoto.src = src;
            photoZoomModal.classList.add('active');
        };

        function autoSubmitIfComplete() {
            const allFilled = Array.from(tokenInputs).every(inp => inp.value.length === 1);
            if (allFilled && !isLoading) {
                setTimeout(() => verifyToken(), 50);
            }
        }

        function showStudentInfo(data) {
            const student = data.student;
            const installment = data.installment;
            const token = data.token;
            const studentImage = student.image ? '/storage/students/' + student.image : '/storage/students/student.jpg';
            const hasTransport = student.has_transport;
            const transportIcon = hasTransport ? 'fa-bus' : 'fa-person-walking';
            const transportColor = hasTransport ? '#22c55e' : '#dc2626';
            const transportText = hasTransport ? 'ANATUMIA' : 'HATUMII';

            currentStudentId = student.id;

            const firstName = student.first_name ? student.first_name.toUpperCase() : '';
            const lastName = student.last_name ? student.last_name.toUpperCase() : '';
            const admissionNum = student.admission_number ? student.admission_number.toUpperCase() : 'N/A';
            const className = student.class_name ? student.class_name.toUpperCase() : 'N/A';

            studentSection.innerHTML = `
                <div class="success-card">
                    <div class="success-header">
                        <i class="fas fa-check-circle"></i>
                        <h2>HALALI</h2>
                        <p>GATE PASS INATAMBULIKA</p>
                    </div>
                    <div class="identity-verification-section">
                        <div class="student-photo-container">
                            <div class="photo-frame" onclick="showPhotoZoom('${studentImage}')">
                                <img src="${studentImage}" class="student-large-photo" onerror="this.src='/storage/students/student.jpg'" alt="Student">
                            </div>
                            <div class="face-match-instruction">
                                <i class="fas fa-id-card"></i> Linganisha uso na picha hii
                            </div>
                        </div>
                        <div class="student-details-panel">
                            <div class="detail-row">
                                <div class="detail-label"><i class="fas fa-user"></i> JINA KAMILI</div>
                                <div class="detail-value">${escapeHtml(firstName)} ${escapeHtml(lastName)}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label"><i class="fas fa-hashtag"></i> NA. USAJILI</div>
                                <div class="detail-value">${escapeHtml(admissionNum)}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label"><i class="fas fa-school"></i> DARASA</div>
                                <div class="detail-value">${escapeHtml(className)}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label"><i class="fas fa-coins"></i> AWAMU YA MALIPO</div>
                                <div class="detail-value">${escapeHtml(installment.name)}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label"><i class="fas fa-hourglass-end"></i> TOKEN INAISHA</div>
                                <div class="detail-value">${formatDate(token.expires_at)}</div>
                            </div>
                        </div>
                    </div>
                    <div class="transport-status-card">
                        <span class="detail-label"><i class="fas fa-truck-moving"></i> USAFIRI</span>
                        <span style="color: ${transportColor}; font-weight:800;"><i class="fas ${transportIcon}"></i> ${transportText}</span>
                    </div>
                    <div class="confirm-access-section">
                        <button id="confirmAccessBtn" class="btn-submit btn-confirm"><i class="fas fa-door-open"></i> RUHUSU KUINGIA</button>
                        <button id="newVerifyBtn" class="btn-submit btn-new-verify"><i class="fas fa-qrcode"></i> VERIFY MWINGINE</button>
                    </div>
                </div>
            `;
            studentSection.style.display = 'block';
            tokenSection.style.display = 'none';

            if (verificationTimeout) clearTimeout(verificationTimeout);
            verificationTimeout = setTimeout(() => {
                if (studentSection.style.display === 'block') {
                    showAlert('Muda wa verification umekwisha, tafadhali ingiza token upya.', 'warning');
                    resetAll();
                }
            }, VERIFY_SESSION_DURATION);

            document.getElementById('confirmAccessBtn')?.addEventListener('click', () => confirmAccess());
            document.getElementById('newVerifyBtn')?.addEventListener('click', () => resetAll());
        }

        function confirmAccess() {
            showAlert('✅ IMERUHUSIWA! Mwanafunzi anaweza kupita.', 'success', true, 2800);
            setTimeout(() => {
                resetAll();
                showAlert('Tayari kwa verification nyingine', 'info', true, 2000);
            }, 2800);
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
        }

        function formatDate(dateStr) {
            try {
                return new Date(dateStr).toLocaleString('sw-TZ', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
            } catch (e) { return dateStr; }
        }

        // Modal handlers
        function setupModalEvents() {
            showResendModalBtn?.addEventListener('click', () => {
                resendModal.classList.add('active');
                modalAdmissionInput.value = '';
                modalAlert.style.display = 'none';
                modalSubmitBtn.disabled = false;
                modalSubmitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Tuma ombi';
            });
            closeModalBtn?.addEventListener('click', () => resendModal.classList.remove('active'));
            resendModal?.addEventListener('click', (e) => { if (e.target === resendModal) resendModal.classList.remove('active'); });
            resendForm?.addEventListener('submit', handleResendToken);
        }

        async function handleResendToken(e) {
            e.preventDefault();
            const admission = modalAdmissionInput.value.trim();
            if (!admission) {
                showModalAlert('Ingiza namba ya usajili', 'error');
                return;
            }
            modalSubmitBtn.disabled = true;
            modalSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inachakata...';
            try {
                const resp = await fetch('{{ route("tokens.resend") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({ admission_number: admission })
                });
                const data = await resp.json();
                if (data.success) {
                    showModalAlert(data.message, 'success');
                    setTimeout(() => resendModal.classList.remove('active'), 2000);
                    // Refresh offline tokens after resend
                    refreshOfflineTokens();
                } else {
                    showModalAlert(data.message, 'error');
                }
            } catch (err) {
                showModalAlert('Hitilafu ya mtandao, jaribu tena', 'error');
            } finally {
                modalSubmitBtn.disabled = false;
                modalSubmitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Tuma ombi';
            }
        }

        function showModalAlert(msg, type) {
            modalAlert.textContent = msg;
            modalAlert.className = `alert alert-${type}`;
            modalAlert.style.display = 'block';
            setTimeout(() => modalAlert.style.display = 'none', 3000);
        }

        async function verifyToken() {
            const token = getFullToken();
            if (token.length !== 4) {
                showAlert('Tafadhali weka token kamili (namba 4)', 'error');
                addError();
                return;
            }
            if (isLoading) return;
            isLoading = true;
            verifyBtn.disabled = true;
            tokenSection.style.display = 'none';
            loadingSection.style.display = 'block';
            hideAlert();
            try {
                const response = await fetch('{{ route("tokens.verify.submit") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' },
                    body: JSON.stringify({ token: token })
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    if (data.offline) {
                        showAlert('Token halali (Hali ya offline)', 'info', true, 2000);
                    } else {
                        showAlert(data.message, 'success', true, 1800);
                    }
                    showStudentInfo(data.data);
                } else {
                    showAlert(data.message || 'Token si sahihi au imeisha muda.', 'error');
                    addError();
                    setTimeout(() => resetAll(), 3500);
                }
            } catch (error) {
                showAlert('Hitilafu ya mtandao, hakikisha umeunganishwa', 'error');
                addError();
                setTimeout(() => resetAll(), 4000);
            } finally {
                isLoading = false;
                loadingSection.style.display = 'none';
                verifyBtn.disabled = false;
            }
        }

        init();
    })();
    </script>
</body>

</html>
