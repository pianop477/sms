<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShuleApp | Gate Pass Verification</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon.ico') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.5.2-web/css/all.css') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">

    <style>
        /* ========== ALL YOUR EXISTING CSS REMAINS UNCHANGED ========== */
        /* [Mistari yako ya CSS hapa - nimeihifadhi kama ilivyo] */
        .offline-status-bar {
            background: #f8fafc;
            border-radius: 60px;
            padding: 8px 16px;
            margin: 10px 0 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            border: 1px solid #e2e8f0;
            min-height: 48px;
        }
        .offline-status-bar .info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #334155;
        }
        .offline-status-bar .info i {
            font-size: 1.1rem;
        }
        .offline-status-bar .info .status-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 4px;
        }
        .status-dot.online {
            background: #22c55e;
        }
        .status-dot.offline {
            background: #ef4444;
        }
        .status-dot.syncing {
            background: #f59e0b;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
        .btn-sync-offline {
            background: transparent;
            border: 2px solid #5B66E8;
            color: #5B66E8;
            border-radius: 40px;
            padding: 4px 10px;
            font-weight: 600;
            font-size: 0.5rem;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            cursor: pointer;
        }
        .btn-sync-offline:hover {
            background: #5B66E8;
            color: white;
        }
        .btn-sync-offline:active {
            transform: scale(0.95);
        }
        .btn-sync-offline:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        .btn-sync-offline.hidden {
            display: none !important;
        }
        .btn-sync-offline .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        .sync-progress-container {
            display: none;
            margin: 8px 0 12px;
            background: #f1f5f9;
            border-radius: 60px;
            height: 8px;
            overflow: hidden;
            position: relative;
        }
        .sync-progress-container.active {
            display: block;
        }
        .sync-progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #5B66E8, #8B5CF6);
            border-radius: 60px;
            transition: width 0.4s ease;
        }
        .sync-progress-text {
            font-size: 0.7rem;
            color: #64748b;
            text-align: center;
            margin-top: 4px;
            display: none;
        }
        .sync-progress-text.active {
            display: block;
        }
        /* Rest of your CSS... (keep all existing styles) */
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
        .gateway-container {
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
        }
        .gateway-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(0px);
            border-radius: 32px;
            box-shadow: 0 20px 35px -8px rgba(0, 0, 0, 0.25);
            width: 100%;
            padding: 24px 20px 28px;
            transition: all 0.2s ease;
        }
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
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
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
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
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
            to { transform: rotate(360deg); }
        }
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
        .transport-status-card {
            background: #f1f5f9;
            margin: 4px 16px 16px;
            padding: 10px 18px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
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
        @media (max-width: 520px) {
            .gateway-card { padding: 20px 16px; }
            .token-input { width: 70px; height: 70px; font-size: 42px; border-radius: 20px; }
            .token-box { gap: 10px; }
            .student-large-photo { width: 130px; height: 130px; }
            .detail-value { font-size: 0.8rem; }
        }
        @media (max-width: 440px) {
            .token-input { width: 62px; height: 62px; font-size: 36px; border-radius: 18px; }
            .token-box { gap: 8px; }
            .gateway-header h2 { font-size: 1.3rem; }
            .btn-submit { padding: 12px 18px; font-size: 0.95rem; }
        }
        @media (max-width: 380px) {
            .token-input { width: 55px; height: 55px; font-size: 32px; }
            .token-box { gap: 6px; }
            .student-large-photo { width: 110px; height: 110px; }
            .face-match-instruction { font-size: 0.7rem; padding: 6px 10px; }
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

            <!-- ========================================= -->
            <!-- OFFLINE STATUS BAR                        -->
            <!-- ========================================= -->
            <div id="offlineStatusBar" class="offline-status-bar">
                <div class="info">
                    <span class="status-dot" id="statusDot"></span>
                    <span id="statusText">Inaangalia...</span>
                </div>
                <button id="syncOfflineBtn" class="btn-sync-offline hidden">
                    <i class="fas fa-cloud-download-alt"></i>
                    <span>Sync Token</span>
                </button>
            </div>

            <!-- Progress Bar for Sync -->
            <div id="syncProgressContainer" class="sync-progress-container">
                <div id="syncProgressBar" class="sync-progress-bar" style="width: 0%;"></div>
                <div id="syncProgressText" class="sync-progress-text">Inapakia...</div>
            </div>

            <div id="tokenSection">
                <div class="token-input-container">
                    <div class="token-input-group">
                        <div class="token-box">
                            <input type="number" class="token-input" maxlength="1" data-idx="0" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="number" class="token-input" maxlength="1" data-idx="1" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="number" class="token-input" maxlength="1" data-idx="2" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="number" class="token-input" maxlength="1" data-idx="3" autocomplete="off" inputmode="numeric" pattern="[0-9]">
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
                        <i class="fas fa-envelope-open-text"></i> Umepoteza token? Omba tena
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

    <script>
        (function() {
            'use strict';

            // ========== DOM REFS ==========
            const syncBtn = document.getElementById('syncOfflineBtn');
            const statusDot = document.getElementById('statusDot');
            const statusText = document.getElementById('statusText');
            const progressContainer = document.getElementById('syncProgressContainer');
            const progressBar = document.getElementById('syncProgressBar');
            const progressText = document.getElementById('syncProgressText');
            const alertBox = document.getElementById('alertBox');
            const tokenInputs = document.querySelectorAll('.token-input');
            const verifyBtn = document.getElementById('verifyBtn');
            const resetBtn = document.getElementById('resetBtn');
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
            let hasTokens = false;
            let isSyncing = false;
            let offlineTokenCount = 0;
            let onlineTokenTotal = 0;

            const ALERT_DURATION = 3500;
            const VERIFY_SESSION_DURATION = 60000;

            // ========== OFFLINE STATUS ==========

            function setStatus(mode, message) {
                statusDot.className = 'status-dot';
                if (mode === 'online') {
                    statusDot.classList.add('online');
                    statusText.textContent = message || 'Online';
                } else if (mode === 'offline') {
                    statusDot.classList.add('offline');
                    statusText.textContent = message || 'Offline';
                } else if (mode === 'syncing') {
                    statusDot.classList.add('syncing');
                    statusText.textContent = message || 'Inapakia...';
                } else {
                    statusText.textContent = message || 'Inaangalia...';
                }
            }

            // Get offline token count from IndexedDB
            async function getOfflineTokenCount() {
                if (!('serviceWorker' in navigator)) return 0;
                try {
                    const registration = await navigator.serviceWorker.ready;
                    if (!registration.active) return 0;
                    return new Promise((resolve) => {
                        const channel = new MessageChannel();
                        channel.port1.onmessage = (event) => {
                            resolve(event.data?.count || 0);
                        };
                        registration.active.postMessage({
                            type: 'GET_OFFLINE_TOKEN_COUNT'
                        }, [channel.port2]);
                        setTimeout(() => resolve(0), 3000);
                    });
                } catch (e) {
                    return 0;
                }
            }

            // Check if there are tokens available for sync (with comparison)
            async function checkTokensAvailability() {
                try {
                    // Get online token count from server
                    const response = await fetch('/offline/tokens', {
                        headers: { 'Accept': 'application/json' }
                    });

                    let onlineTotal = 0;
                    if (response.ok) {
                        const data = await response.json();
                        onlineTotal = data.total || 0;
                        onlineTokenTotal = onlineTotal;
                    }

                    // Get offline token count from IndexedDB
                    const offlineCount = await getOfflineTokenCount();
                    offlineTokenCount = offlineCount;

                    // Show sync button only if there are tokens online AND offline count is less than online total
                    // OR if offline count is 0 but online has tokens (first sync)
                    if (onlineTotal > 0 && offlineCount < onlineTotal) {
                        hasTokens = true;
                        syncBtn.classList.remove('hidden');
                        syncBtn.disabled = false;
                        syncBtn.title = 'Sync Token';
                        return true;
                    } else {
                        hasTokens = false;
                        syncBtn.classList.add('hidden');
                        return false;
                    }
                } catch (e) {
                    // If offline, try to check IndexedDB only
                    try {
                        const offlineCount = await getOfflineTokenCount();
                        offlineTokenCount = offlineCount;
                        if (offlineCount > 0) {
                            // Show button but disabled (can't download without internet)
                            hasTokens = true;
                            syncBtn.classList.remove('hidden');
                            syncBtn.disabled = true;
                            syncBtn.title = 'Unahitaji mtandao kupakua token mpya';
                            return true;
                        }
                    } catch (err) {}
                    hasTokens = false;
                    syncBtn.classList.add('hidden');
                    return false;
                }
            }

            // ========== PROGRESS BAR ==========

            function showProgress(percent, text) {
                progressContainer.classList.add('active');
                progressBar.style.width = Math.min(100, Math.max(0, percent)) + '%';
                if (text) {
                    progressText.textContent = text;
                    progressText.classList.add('active');
                } else {
                    progressText.classList.remove('active');
                }
            }

            function hideProgress() {
                progressContainer.classList.remove('active');
                progressBar.style.width = '0%';
                progressText.classList.remove('active');
            }

            // ========== SYNC TRIGGER ==========

            async function triggerOfflineSync() {
                if (isSyncing) return;
                if (!('serviceWorker' in navigator)) {
                    showAlert('Huduma ya offline haipatikani.', 'error');
                    return;
                }

                isSyncing = true;
                syncBtn.disabled = true;
                const originalHtml = syncBtn.innerHTML;
                syncBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Inapakia...';
                setStatus('syncing', 'Inapakia...');
                showProgress(10, 'Kuanza upakiaji...');

                try {
                    const registration = await navigator.serviceWorker.ready;
                    if (!registration.active) {
                        throw new Error('Service Worker haijaanza');
                    }

                    showProgress(30, 'Inasambaza ombi...');

                    const result = await new Promise((resolve) => {
                        const channel = new MessageChannel();
                        channel.port1.onmessage = (event) => {
                            resolve(event.data || { success: false });
                        };
                        registration.active.postMessage({
                            type: 'SYNC_TOKENS'
                        }, [channel.port2]);
                        setTimeout(() => resolve({ success: false, error: 'Timeout' }), 20000);
                    });

                    showProgress(80, 'Inahifadhi token...');

                    if (result && result.success) {
                        showProgress(100, 'Imekamilika!');
                        setTimeout(hideProgress, 800);
                        // ✅ DON'T show "Token zimepakuliwa kikamilifu!" - just update status
                        setStatus('online', 'Online Mode');
                        // Re-check if tokens are available (hide button if all synced)
                        await checkTokensAvailability();
                    } else {
                        hideProgress();
                        showAlert('Imeshindwa kupakua token. Jaribu tena.', 'error');
                        setStatus('offline', 'Hitilafu');
                    }
                } catch (e) {
                    console.error('Sync error:', e);
                    hideProgress();
                    showAlert('Hitilafu wakati wa upakiaji. Hakikisha umeunganishwa mtandao.', 'error');
                    setStatus('offline', 'Hitilafu');
                } finally {
                    isSyncing = false;
                    syncBtn.disabled = false;
                    syncBtn.innerHTML = originalHtml;
                }
            }

            // ========== SERVICE WORKER MESSAGES ==========

            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('message', function(event) {
                    const data = event.data;
                    if (data && data.type === 'SYNC_STATUS') {
                        if (data.success) {
                            // ✅ Don't show "Token zimepakuliwa kikamilifu!" - just update
                            setStatus('online', 'Online Mode');
                            checkTokensAvailability();
                        } else {
                            showAlert('Imeshindwa kupakua token. Jaribu tena.', 'error');
                            setStatus('offline', 'Hitilafu');
                        }
                        syncBtn.disabled = false;
                        syncBtn.innerHTML = '<i class="fas fa-cloud-download-alt"></i> Sync Token';
                        isSyncing = false;
                        hideProgress();
                    }
                });

                navigator.serviceWorker.getRegistration().then(registration => {
                    if (!registration) {
                        navigator.serviceWorker.register('/service-worker.js', { scope: '/' })
                            .then(() => console.log('SW registered from page'))
                            .catch(err => console.warn('SW registration failed:', err));
                    }
                });
            }

            // ========== CHECK ONLINE/OFFLINE STATUS ==========

            function updateConnectionStatus() {
                if (navigator.onLine) {
                    setStatus('online', 'Online Mode');
                    checkTokensAvailability();
                } else {
                    setStatus('offline', 'Offline Mode');
                    syncBtn.classList.add('hidden');
                }
            }

            window.addEventListener('online', () => {
                updateConnectionStatus();
                setTimeout(checkTokensAvailability, 1000);
            });

            window.addEventListener('offline', () => {
                setStatus('offline', 'Offline Mode');
                syncBtn.classList.add('hidden');
            });

            syncBtn.addEventListener('click', triggerOfflineSync);

            // ========== INIT OFFLINE STATUS ==========

            async function initOfflineStatus() {
                updateConnectionStatus();
                if (navigator.onLine) {
                    await checkTokensAvailability();
                } else {
                    try {
                        const count = await getOfflineTokenCount();
                        offlineTokenCount = count;
                        if (count > 0) {
                            hasTokens = true;
                            syncBtn.classList.remove('hidden');
                            syncBtn.disabled = true;
                            syncBtn.title = 'Unahitaji mtandao kupakua token mpya';
                        }
                    } catch (e) {}
                }
            }

            // ==========================================
            // VERIFICATION LOGIC (FULLY OFFLINE CAPABLE)
            // ==========================================

            function init() {
                setupTokenEvents();
                setupModalEvents();
                setupPhotoZoom();
                focusFirstInput();
                initOfflineStatus();

                // ✅ Check if page is being served from cache (offline)
                if (!navigator.onLine) {
                    setTimeout(async () => {
                        const count = await getOfflineTokenCount();
                        if (count === 0) {
                            showAlert('Huna token zilizohifadhiwa offline. Unganisha mtandao kupakua token.', 'warning', true, 5000);
                        } else {
                            // ✅ Show simple info - no count displayed
                            showAlert('Hali ya offline. Token zilizohifadhiwa zinaweza kuthibitishwa.', 'info', true, 3000);
                        }
                    }, 1000);
                }
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
                if (navigator.onLine) checkTokensAvailability();
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

            // ==========================================
            // VERIFY TOKEN (OFFLINE CAPABLE)
            // ==========================================
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
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 15000);

                    const response = await fetch('{{ route("tokens.verify.submit") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ token: token }),
                        signal: controller.signal
                    });

                    clearTimeout(timeoutId);

                    const data = await response.json();
                    if (response.ok && data.success) {
                        if (data.offline) {
                            showAlert('Token halali (Hali ya offline)', 'info', true, 2000);
                        } else {
                            showAlert(data.message, 'success', true, 1800);
                        }
                        showStudentInfo(data.data);
                        if (!data.offline) checkTokensAvailability();
                    } else {
                        showAlert(data.message || 'Token si sahihi au imeisha muda.', 'error');
                        addError();
                        setTimeout(() => resetAll(), 3500);
                    }
                } catch (error) {
                    // Network failed - try offline verification via SW message
                    if (error.name === 'AbortError') {
                        showAlert('Muda wa ombi umeisha. Jaribu tena.', 'error');
                    } else {
                        showAlert('Hakuna mtandao. Inajaribu kuthibitisha token offline...', 'info', true, 2000);

                        try {
                            if ('serviceWorker' in navigator) {
                                const registration = await navigator.serviceWorker.ready;
                                if (registration.active) {
                                    const result = await new Promise((resolve) => {
                                        const channel = new MessageChannel();
                                        channel.port1.onmessage = (event) => {
                                            resolve(event.data);
                                        };
                                        registration.active.postMessage({
                                            type: 'VERIFY_TOKEN_OFFLINE',
                                            token: token
                                        }, [channel.port2]);
                                        setTimeout(() => resolve({ success: false, error: 'Timeout' }), 5000);
                                    });

                                    if (result && result.success) {
                                        showAlert('Token halali (Hali ya offline)', 'info', true, 2000);
                                        showStudentInfo(result.data);
                                        isLoading = false;
                                        loadingSection.style.display = 'none';
                                        verifyBtn.disabled = false;
                                        return;
                                    } else {
                                        showAlert('Token haipatikani offline. Unganisha mtandao.', 'error');
                                        addError();
                                        setTimeout(() => resetAll(), 4000);
                                    }
                                } else {
                                    showAlert('Service Worker haijaanza. Unganisha mtandao.', 'error');
                                    addError();
                                    setTimeout(() => resetAll(), 4000);
                                }
                            } else {
                                showAlert('Huduma ya offline haipatikani. Unganisha mtandao.', 'error');
                                addError();
                                setTimeout(() => resetAll(), 4000);
                            }
                        } catch (swError) {
                            console.error('SW offline verification error:', swError);
                            showAlert('Hitilafu ya verification offline. Unganisha mtandao.', 'error');
                            addError();
                            setTimeout(() => resetAll(), 4000);
                        }
                    }
                } finally {
                    isLoading = false;
                    loadingSection.style.display = 'none';
                    verifyBtn.disabled = false;
                }
            }

            // ==========================================
            // SHOW STUDENT INFO (FIXED FOR OFFLINE)
            // ==========================================
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
                // ✅ FIX: Use class_name directly from student data (works for both online and offline)
                const className = student.class_name ? student.class_name.toUpperCase() :
                                 (student.class && student.class.class_name ? student.class.class_name.toUpperCase() : 'N/A');

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
                                    <div class="detail-label"><i class="fas fa-user"></i> JINA:</div>
                                    <div class="detail-value">${escapeHtml(firstName)} ${escapeHtml(lastName)}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-hashtag"></i> NA. USAJILI:</div>
                                    <div class="detail-value">${escapeHtml(admissionNum)}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-school"></i> DARASA:</div>
                                    <div class="detail-value">${escapeHtml(className)}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-coins"></i> AWAMU: </div>
                                    <div class="detail-value">${escapeHtml(installment.name)}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-hourglass-end"></i> MWISHO:</div>
                                    <div class="detail-value">${formatDate(token.expires_at)}</div>
                                </div>
                            </div>
                        </div>
                        <div class="transport-status-card">
                            <span class="detail-label"><i class="fas fa-truck-moving"></i> USAFIRI:</span>
                            <span style="color: ${transportColor}; font-weight:800;"><i class="fas ${transportIcon}"></i> ${transportText}</span>
                        </div>
                        <div class="confirm-access-section">
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

                document.getElementById('newVerifyBtn')?.addEventListener('click', () => resetAll());
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

            // ========== MODAL HANDLERS ==========

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
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ admission_number: admission })
                    });
                    const data = await resp.json();
                    if (data.success) {
                        showModalAlert(data.message, 'success');
                        setTimeout(() => resendModal.classList.remove('active'), 2000);
                        triggerOfflineSync();
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

            function refreshCsrfToken() {
                return fetch('{{ route('refresh-csrf') }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Failed to refresh CSRF token');
                    return response.json();
                })
                .then(data => {
                    if (data.token) {
                        // Update all forms
                        document.querySelectorAll('input[name="_token"]').forEach(input => {
                            input.value = data.token;
                        });
                        // Update meta tag for AJAX requests
                        const metaTag = document.querySelector('meta[name="csrf-token"]');
                        if (metaTag) metaTag.content = data.token;
                        return data.token;
                    } else {
                        throw new Error('No token in response');
                    }
                })
                .catch(error => {
                    console.warn('CSRF refresh failed:', error);
                    // Return null so we can fall back to existing token
                    return null;
                });
            }

            // ========== INIT ==========

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
</body>

</html>
