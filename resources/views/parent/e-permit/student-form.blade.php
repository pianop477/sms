{{-- resources/views/parent/e-permit/student-form.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShuleApp | e-Permit Request</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon.ico') }}">
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.5.2-web/css/all.css') }}">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">

    <style>
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

        .gateway-header h3 {
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
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .gateway-icon i {
            font-size: 36px;
            color: white;
        }

        .student-id-input-container {
            margin: 20px 0;
        }

        .student-id-input-group {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .student-id-input {
            width: 100%;
            padding: 16px 20px;
            font-size: 18px;
            font-weight: 600;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            background: #ffffff;
            text-transform: uppercase;
            transition: all 0.2s;
            text-align: center;
            letter-spacing: 2px;
        }

        .student-id-input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            transform: scale(1.01);
        }

        .student-id-input.error {
            border-color: #e53e3e;
            background: #fff5f5;
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
            align-items: center;
            width: 100%;
            margin-top: 10px;
            gap: 12px;
        }

        .btn-submit {
            width: 100%;
            max-width: 280px;
            padding: 12px 24px;
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
            gap: 8px;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        }

        .btn-secondary:hover:not(:disabled) {
            box-shadow: 0 8px 20px rgba(71, 85, 105, 0.3);
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

        .loading-state {
            text-align: center;
            padding: 30px 20px;
        }

        .spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .student-card {
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

        .student-header {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border-radius: 24px 24px 0 0;
            padding: 16px;
            text-align: center;
        }

        .student-header i {
            font-size: 28px;
            color: white;
            margin-right: 8px;
        }

        .student-header h3 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            color: white;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .student-header p {
            font-size: 12px;
            margin: 4px 0 0;
            color: rgba(255, 255, 255, 0.9);
        }

        .identity-verification-section {
            background: white;
            border-radius: 20px;
            padding: 16px;
            margin: 16px;
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
            border: 3px solid #e2e8f0;
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
            background: rgba(0, 0, 0, 0.6);
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

        .photo-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
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

        .student-details-panel {
            background: #f8fafc;
            border-radius: 16px;
            padding: 12px;
            margin-top: 12px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #475569;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-label i {
            width: 18px;
            font-size: 14px;
            color: #667eea;
        }

        .detail-value {
            font-weight: 600;
            color: #0f172a;
            font-size: 13px;
            text-align: right;
        }

        .action-buttons {
            padding: 0 16px 16px 16px;
            display: flex;
            gap: 12px;
        }

        .btn-confirm {
            background: #22c55e;
            max-width: 100%;
            flex: 1;
        }

        .btn-confirm:hover {
            background: #16a34a;
        }

        .btn-cancel {
            background: #ef4444;
            max-width: 100%;
            flex: 1;
        }

        .btn-cancel:hover {
            background: #dc2626;
        }

        .btn-new-verify {
            background: #64748b;
            width: 100%;
            margin-top: 8px;
        }

        .btn-new-verify:hover {
            background: #475569;
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

        @media (max-width: 480px) {
            body { padding: 12px; }
            .gateway-card { padding: 20px 16px; border-radius: 24px; }
            .gateway-header h3 { font-size: 1.1rem; }
            .gateway-icon { width: 65px; height: 65px; }
            .gateway-icon i { font-size: 32px; }
            .student-id-input { padding: 14px 16px; font-size: 16px; }
            .student-large-photo { width: 150px !important; height: 150px !important; }
            .detail-row { flex-direction: column; align-items: flex-start; gap: 4px; }
            .detail-value { text-align: left; }
            .action-buttons { flex-direction: column; }
        }

        @media (max-width: 380px) {
            .student-large-photo { width: 130px !important; height: 130px !important; }
        }
    </style>
</head>

<body>
    <div class="gateway-container">
        <div class="gateway-card">
            <div class="gateway-header">
                <div class="gateway-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3>e-PERMIT System Module</h3>
                <p>Omba ruhusa ya mwanafunzi hapa</p>
            </div>

            <div id="studentIdSection">
                <div class="student-id-input-container">
                    <div class="student-id-input-group">
                        <input type="text"
                               id="studentIdInput"
                               class="student-id-input"
                               placeholder="INGIZA ADMISSION NUMBER"
                               autocomplete="off"
                               autofocus>
                    </div>
                </div>

                <div class="btn-container">
                    <button id="verifyBtn" class="btn-submit">
                        <i class="fas fa-search"></i> HAKIKI
                    </button>
                </div>

                <div class="text-center">
                    <button id="resetBtn" class="btn-reset">
                        <i class="fas fa-undo-alt"></i> Futa
                    </button>
                </div>
            </div>

            <div id="studentSection" style="display: none;"></div>
            <div id="loadingSection" style="display: none;" class="loading-state">
                <div class="spinner"></div>
                <p class="mt-2 mb-0" style="color: #718096;">Inachakata taarifa za mwanafunzi...</p>
            </div>
        </div>

        <div class="footer">
            <p><a href="{{ route('welcome') }}"><i class="fas fa-home"></i> Nyumbani</a>
            </p>
            <p>© {{ date('Y') }} ShuleApp. Haki zote zimehifadhiwa</p>
        </div>
    </div>

    <!-- Photo Zoom Modal -->
    <div id="photoZoomModal" class="photo-modal">
        <img id="zoomedPhoto" src="" alt="Student Photo">
    </div>

    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script>
        (function() {
            // DOM Elements
            const studentIdInput = document.getElementById('studentIdInput');
            const verifyBtn = document.getElementById('verifyBtn');
            const resetBtn = document.getElementById('resetBtn');
            const studentIdSection = document.getElementById('studentIdSection');
            const studentSection = document.getElementById('studentSection');
            const loadingSection = document.getElementById('loadingSection');

            // Photo Zoom Elements
            const photoZoomModal = document.getElementById('photoZoomModal');
            const zoomedPhoto = document.getElementById('zoomedPhoto');

            let isLoading = false;
            let currentStudent = null;

            function init() {
                setupEventListeners();
                setupPhotoZoom();
                focusInput();
            }

            function setupEventListeners() {
                verifyBtn.addEventListener('click', verifyStudent);
                resetBtn.addEventListener('click', resetForm);
                studentIdInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') verifyStudent();
                });
            }

            function focusInput() {
                setTimeout(() => studentIdInput?.focus(), 100);
            }

            function resetForm() {
                studentIdInput.value = '';
                studentIdSection.style.display = 'block';
                studentSection.style.display = 'none';
                studentIdInput.classList.remove('error');
                focusInput();
                currentStudent = null;
            }

            function showLoading() {
                isLoading = true;
                verifyBtn.disabled = true;
                studentIdSection.style.display = 'none';
                loadingSection.style.display = 'block';
            }

            function hideLoading() {
                isLoading = false;
                verifyBtn.disabled = false;
                loadingSection.style.display = 'none';
            }

            function showError(message) {
                studentIdInput.classList.add('error');
                Swal.fire({
                    icon: 'error',
                    title: 'Hitilafu!',
                    text: message,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Jaribu Tena'
                });
                setTimeout(() => {
                    studentIdInput.classList.remove('error');
                    focusInput();
                }, 1000);
            }

            function showSuccess(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Imefanikiwa!',
                    text: message,
                    timer: 2000,
                    showConfirmButton: false
                });
            }

            function showWarning(message) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tahadhari!',
                    text: message,
                    confirmButtonColor: '#f59e0b',
                    confirmButtonText: 'Sawa'
                });
            }

            function showInfo(message, callback) {
                Swal.fire({
                    icon: 'info',
                    title: 'Taarifa',
                    text: message,
                    confirmButtonColor: '#667eea',
                    confirmButtonText: 'Endelea'
                }).then((result) => {
                    if (result.isConfirmed && callback) callback();
                });
            }

            function setupPhotoZoom() {
                photoZoomModal.addEventListener('click', () => {
                    photoZoomModal.classList.remove('active');
                });
            }

            function showPhotoZoom(imageSrc) {
                zoomedPhoto.src = imageSrc;
                photoZoomModal.classList.add('active');
            }

            function getStudentImagePath(student) {
                if (student.image && student.image !== 'student.png') {
                    return '/storage/students/' + student.image;
                }
                return '{{ asset("storage/app/public/students/student.jpg") }}';
            }

            function displayStudentInfo(student) {
                currentStudent = student;
                const studentImage = student.image ? '/storage/students/' + student.image : '/storage/students/student.jpg';
                const className = student.class ? student.class.class_name : 'N/A';
                const stream = student.group || student.stream || 'N/A';

                studentSection.innerHTML = `
                    <div class="student-card">
                        <div class="student-header">
                            <div>
                                <i class="fas fa-user-check"></i>
                                <h3>TAARIFA ZA MWANAFUNZI</h3>
                                <p>Tafadhali hakikisha taarifa kama ni sahihi</p>
                            </div>
                        </div>

                        <div class="identity-verification-section">
                            <div class="student-photo-container">
                                <div class="photo-frame" onclick="window.showPhotoZoom('${studentImage}')">
                                    <img src="${studentImage}" class="student-large-photo"
                                         onerror="this.src='{{ asset("storage/app/public/students/student.jpg") }}'"
                                         alt="Student Photo">
                                    <div class="zoom-icon">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="student-details-panel">
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-user-graduate"></i> JINA KAMILI</div>
                                    <div class="detail-value">${escapeHtml(student.first_name)} ${escapeHtml(student.middle_name || '')} ${escapeHtml(student.last_name)}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-id-card"></i> ADMISSION NUMBER</div>
                                    <div class="detail-value">${escapeHtml(student.admission_number.toUpperCase())}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-chalkboard-user"></i> DARASA</div>
                                    <div class="detail-value text-uppercase">${escapeHtml(className)}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-code-branch"></i> MKONDO</div>
                                    <div class="detail-value text-uppercase">${escapeHtml(stream)}</div>
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button id="confirmStudentBtn" class="btn-submit btn-confirm">
                                <i class="fas fa-check-circle"></i> THIBITISHA
                            </button>
                            <button id="cancelStudentBtn" class="btn-submit btn-cancel">
                                <i class="fas fa-times-circle"></i> GHAIRI
                            </button>
                        </div>
                    </div>
                `;

                studentSection.style.display = 'block';
                studentIdSection.style.display = 'none';

                document.getElementById('confirmStudentBtn').addEventListener('click', () => confirmStudent());
                document.getElementById('cancelStudentBtn').addEventListener('click', () => cancelStudent());
            }

            function confirmStudent() {
                // Navigate to multi-step form
                window.location.href = '{{ route("parent.e-permit.request-form", "") }}/' + currentStudent.id;
            }

            function cancelStudent() {
                showInfo('Umeghairi. Tafadhali ingiza Admission Number tena.', () => {
                    resetForm();
                });
            }

            function escapeHtml(str) {
                if (!str) return '';
                return String(str).replace(/[&<>]/g, function(m) {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    return m;
                });
            }

            async function verifyStudent() {
                const studentId = studentIdInput.value.trim().toUpperCase();

                if (!studentId) {
                    showError('Tafadhali ingiza Admission Number');
                    return;
                }

                showLoading();

                try {
                    const response = await fetch('{{ route("parent.e-permit.verify-student") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ student_id: studentId })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        if (data.has_active_permit) {
                            showWarning(data.message);
                            resetForm();
                        } else {
                            showSuccess('Mwanafunzi amepatikana!');
                            displayStudentInfo(data.student);
                        }
                    } else {
                        showError(data.message || 'Student ID haijapatikana. Tafadhali jaribu tena.');
                        resetForm();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showError('Hitilafu ya mtandao. Tafadhali jaribu tena.');
                    resetForm();
                } finally {
                    hideLoading();
                }
            }

            // Expose global functions
            window.showPhotoZoom = showPhotoZoom;

            init();
        })();
    </script>
</body>

</html>
