{{-- resources/views/parent/e-permit/request-form.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShuleApp | e-Permit Request Form</title>
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
            padding: 20px;
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
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

        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            text-align: center;
            color: white;
        }

        .form-header h2 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .form-header p {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        /* Progress Bar */
        .progress-container {
            padding: 20px 20px 0 20px;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 30px;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            width: 100%;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            background: #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: bold;
            color: #64748b;
            transition: all 0.3s;
        }

        .step.active .step-circle {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .step.completed .step-circle {
            background: #22c55e;
            color: white;
        }

        .step.completed .step-circle i {
            font-size: 20px;
        }

        .step-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
        }

        .step.active .step-label {
            color: #667eea;
            font-weight: 700;
        }

        /* Form Sections */
        .form-section {
            padding: 20px;
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .form-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-title i {
            color: #667eea;
            margin-right: 8px;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
            display: block;
            font-size: 0.9rem;
        }

        .form-label i {
            margin-right: 6px;
            color: #667eea;
        }

        .form-label .required {
            color: #ef4444;
            margin-left: 4px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control[readonly] {
            background-color: #f8fafc;
            cursor: not-allowed;
        }

        select.form-control {
            cursor: pointer;
        }

        /* Student Info Card */
        .student-info-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            gap: 16px;
            align-items: center;
            border: 1px solid #e2e8f0;
        }

        .student-info-card .student-photo {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #667eea;
        }

        .student-info-card .student-details {
            flex: 1;
        }

        .student-info-card .student-name {
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 4px;
        }

        .student-info-card .student-meta {
            font-size: 12px;
            color: #64748b;
        }

        /* Buttons */
        .form-buttons {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-prev {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-prev:hover {
            background: #cbd5e1;
        }

        .btn-next {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-submit {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Dynamic fields */
        .dynamic-field {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .dynamic-field.show {
            display: block;
        }

        /* Responsive */
        @media (max-width: 640px) {
            body { padding: 12px; }
            .form-card { border-radius: 20px; }
            .form-header { padding: 16px; }
            .form-header h2 { font-size: 1.2rem; }
            .student-info-card { flex-direction: column; text-align: center; }
            .student-info-card .student-photo { width: 80px; height: 80px; margin: 0 auto; }
            .form-buttons { flex-direction: column; }
            .btn { justify-content: center; }
            .step-label { font-size: 10px; }
            .step-circle { width: 32px; height: 32px; font-size: 12px; }
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h2><i class="fas fa-file-signature"></i> Fomu ya Maombi ya Ruhusa</h2>
                <p>Tafadhali jaza taarifa zote kwa usahihi</p>
            </div>

            <div class="progress-container">
                <div class="progress-steps">
                    <div class="step active" id="step1Indicator">
                        <div class="step-circle"><span>1</span></div>
                        <div class="step-label">Taarifa za Mwanafunzi</div>
                    </div>
                    <div class="step" id="step2Indicator">
                        <div class="step-circle"><span>2</span></div>
                        <div class="step-label">Taarifa za Mzazi/Mlezi</div>
                    </div>
                    <div class="step" id="step3Indicator">
                        <div class="step-circle"><span>3</span></div>
                        <div class="step-label">Taarifa za Ruhusa</div>
                    </div>
                </div>
            </div>

            <form id="ePermitForm" action="{{ route('parent.e-permit.submit-request', ['student' => Hashids::encode($student->id)]) }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" value="{{Hashids::encode($student->id )}}" class="form-control">

                <!-- STEP 1: Student Details (Read Only) -->
                <div class="form-section active" id="step1">
                    <div class="section-title">
                        <i class="fas fa-user-graduate"></i> Taarifa za Mwanafunzi
                    </div>

                    <div class="student-info-card">
                        @php
                            $studentImage = $student->image ? '/storage/students/' . $student->image : asset('storage/students/student.jpg');
                        @endphp
                        <img src="{{ $studentImage }}" class="student-photo"
                             onerror="this.src='{{ asset('storage/students/student.jpg') }}'"
                             alt="Student Photo">
                        <div class="student-details">
                            <div class="student-name">{{ ucfirst($student->first_name) }} {{ ucfirst($student->middle_name) }} {{ ucfirst($student->last_name) }}</div>
                            <div class="student-meta">
                                <i class="fas fa-id-card"></i> ID: {{ strtoupper($student->admission_number)}}<br>
                                <i class="fas fa-chalkboard-user"></i> Darasa: {{ strtoupper($student->class->class_name) }}
                                @if($student->group)
                                    | <i class="fas fa-code-branch"></i> Mkondo: {{ strtoupper($student->group) }}
                                @endif
                                | <i class="fas fa-venus-mars"></i> Jinsi: {{ ucfirst($student->gender)}}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Jina Kamili la Mwanafunzi
                        </label>
                        <input type="text" class="form-control" readonly
                               value="{{ ucwords(strtolower($student->first_name)) }} {{ ucwords(strtolower($student->middle_name)) }} {{ ucwords(strtolower($student->last_name)) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card"></i> Admission ID
                        </label>
                        <input type="text" class="form-control" readonly
                               value="{{ strtoupper($student->admission_number)}}">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-chalkboard-user"></i> Darasa
                                </label>
                                <input type="text" class="form-control" readonly
                                       value="{{ strtoupper($student->class->class_name) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-code-branch"></i> Mkondo
                                </label>
                                <input type="text" class="form-control" readonly
                                       value="{{ strtoupper($student->group) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Parent/Guardian Details -->
                <div class="form-section" id="step2">
                    <div class="section-title">
                        <i class="fas fa-users"></i> Taarifa za Mzazi/Mlezi (Taarifa za Anayemchukua Mwanafunzi)
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user-circle"></i> Jina Kamili <span class="required">*</span>
                        </label>
                        <input type="text" name="guardian_name" class="form-control" required
                               placeholder="Ingiza jina kamili" value="{{ old('guardian_name') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i> Namba ya Simu <span class="required">*</span>
                        </label>
                        <input type="tel" name="guardian_phone" class="form-control" required
                               placeholder="e.g., 0712345678" value="{{ old('guardian_phone') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-handshake"></i> Undugu <span class="required">*</span>
                        </label>
                        <select name="guardian_type" id="guardianType" class="form-control" required>
                            <option value="">-- Chagua Undugu --</option>
                            <option value="parent" {{ old('guardian_type') == 'parent' ? 'selected' : '' }}>Mzazi</option>
                            <option value="guardian" {{ old('guardian_type') == 'guardian' ? 'selected' : '' }}>Mlezi</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-heart"></i> Uhusiano <span class="required">*</span>
                        </label>
                        <select name="relationship" id="relationship" class="form-control" required>
                            <option value="">-- Chagua Uhusiano --</option>
                        </select>
                    </div>
                </div>

                <!-- STEP 3: Permission Details -->
                <div class="form-section" id="step3">
                    <div class="section-title">
                        <i class="fas fa-clipboard-list"></i> Taarifa za Ruhusa
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-question-circle"></i> Sababu ya Kuomba Ruhusa <span class="required">*</span>
                        </label>
                        <select name="reason" id="reason" class="form-control" required>
                            <option value="">-- Chagua Sababu --</option>
                            <option value="medical" {{ old('reason') == 'medical' ? 'selected' : '' }}>Matibabu</option>
                            <option value="family_matter" {{ old('reason') == 'family_matter' ? 'selected' : '' }}>Jambo la Kifamilia</option>
                            <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Sababu Nyingine</option>
                        </select>
                    </div>

                    <div class="dynamic-field" id="otherReasonField">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-pen"></i> Taja Sababu Nyingine <span class="required">*</span>
                            </label>
                            <textarea name="other_reason" class="form-control" rows="3"
                                      placeholder="Tafadhali eleza sababu yako...">{{ old('other_reason') }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-day"></i> Tarehe ya Kuomba Ruhusa <span class="required">*</span>
                                </label>
                                <input type="date" name="departure_date" class="form-control" required
                                       value="{{ old('departure_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Muda wa Maombi
                                </label>
                                <input type="text" class="form-control" readonly
                                       value="">
                                <small class="text-muted">Muda wa sasa utatumika</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-week"></i> Tarehe ya Kurudi Shuleni <span class="required">*</span>
                        </label>
                        <input type="date" name="expected_return_date" class="form-control" required
                               value="{{ old('expected_return_date', date('Y-m-d', strtotime('+1 day'))) }}"
                               min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="alert alert-info mt-3" style="background: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 12px;">
                        <i class="fas fa-info-circle"></i>
                        <strong>Kumbuka:</strong> Ombi lako litapitishwa kwa walimu wanaohusika. Utahabarishwa mara ombi litakapothibitishwa.
                    </div>
                </div>

                <div class="form-buttons p-3">
                    <button type="button" id="prevBtn" class="btn btn-prev" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Nyuma
                    </button>
                    <button type="button" id="nextBtn" class="btn btn-next">
                        Endelea <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" id="submitBtn" class="btn btn-submit" style="display: none;">
                        <i class="fas fa-paper-plane"></i> Wasilisha Ombi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            // Current step
            let currentStep = 1;
            const totalSteps = 3;

            // DOM Elements
            const steps = document.querySelectorAll('.step');
            const sections = document.querySelectorAll('.form-section');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('ePermitForm');

            // Form elements for dynamic behavior
            const guardianType = document.getElementById('guardianType');
            const relationship = document.getElementById('relationship');
            const reason = document.getElementById('reason');
            const otherReasonField = document.getElementById('otherReasonField');
            const departureDate = document.querySelector('input[name="departure_date"]');
            const returnDate = document.querySelector('input[name="expected_return_date"]');

            // Relationship options based on guardian type
            const parentRelationships = ['Baba', 'Mama'];
            const guardianRelationships = ['Dada', 'Kaka', 'Shangazi', 'Mjomba', 'Babu', 'Bibi'];

            function init() {
                setupEventListeners();
                setupDateValidation();
                updateStepDisplay();
                populateRelationshipOptions();
            }

            function setupEventListeners() {
                prevBtn.addEventListener('click', goToPrevStep);
                nextBtn.addEventListener('click', goToNextStep);
                guardianType.addEventListener('change', populateRelationshipOptions);
                reason.addEventListener('change', toggleOtherReasonField);

                // Form submission
                form.addEventListener('submit', handleSubmit);
            }

            function setupDateValidation() {
                if (departureDate && returnDate) {
                    departureDate.addEventListener('change', function() {
                        returnDate.min = this.value;
                        if (returnDate.value < this.value) {
                            returnDate.value = this.value;
                        }
                    });
                }
            }

            function populateRelationshipOptions() {
                const selectedType = guardianType.value;
                let options = '<option value="">-- Chagua Uhusiano --</option>';

                if (selectedType === 'parent') {
                    parentRelationships.forEach(rel => {
                        options += `<option value="${rel.toLowerCase()}">${rel}</option>`;
                    });
                } else if (selectedType === 'guardian') {
                    guardianRelationships.forEach(rel => {
                        options += `<option value="${rel.toLowerCase()}">${rel}</option>`;
                    });
                }

                relationship.innerHTML = options;
            }

            function toggleOtherReasonField() {
                const selectedReason = reason.value;
                if (selectedReason === 'other') {
                    otherReasonField.classList.add('show');
                    document.querySelector('textarea[name="other_reason"]').required = true;
                } else {
                    otherReasonField.classList.remove('show');
                    document.querySelector('textarea[name="other_reason"]').required = false;
                }
            }

            function validateStep(step) {
                const currentSection = document.getElementById(`step${step}`);
                const requiredFields = currentSection.querySelectorAll('[required]');

                for (let field of requiredFields) {
                    if (!field.value.trim()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Taarifa Hazijakamilika',
                            text: `Tafadhali jaza: ${getFieldLabel(field)}`,
                            confirmButtonColor: '#ef4444'
                        });
                        field.focus();
                        return false;
                    }
                }

                // Additional validation for step 3 dates
                if (step === 3) {
                    const departure = new Date(departureDate.value);
                    const returnDt = new Date(returnDate.value);

                    if (returnDt < departure) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tarehe si Sahihi',
                            text: 'Tarehe ya kurudi haiwezi kuwa kabla ya tarehe ya kuondoka',
                            confirmButtonColor: '#ef4444'
                        });
                        return false;
                    }
                }

                return true;
            }

            function getFieldLabel(field) {
                const label = field.closest('.form-group')?.querySelector('.form-label');
                if (label) {
                    return label.innerText.replace(/\*/g, '').trim();
                }
                return field.name || 'Field';
            }

            function goToNextStep() {
                if (validateStep(currentStep)) {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        updateStepDisplay();
                    }
                }
            }

            function goToPrevStep() {
                if (currentStep > 1) {
                    currentStep--;
                    updateStepDisplay();
                }
            }

            function updateStepDisplay() {
                // Update sections
                sections.forEach((section, index) => {
                    if (index + 1 === currentStep) {
                        section.classList.add('active');
                    } else {
                        section.classList.remove('active');
                    }
                });

                // Update step indicators
                steps.forEach((step, index) => {
                    const stepNum = index + 1;
                    step.classList.remove('active', 'completed');

                    if (stepNum < currentStep) {
                        step.classList.add('completed');
                        const circle = step.querySelector('.step-circle');
                        circle.innerHTML = '<i class="fas fa-check"></i>';
                    } else if (stepNum === currentStep) {
                        step.classList.add('active');
                        const circle = step.querySelector('.step-circle');
                        circle.innerHTML = `<span>${stepNum}</span>`;
                    } else {
                        const circle = step.querySelector('.step-circle');
                        circle.innerHTML = `<span>${stepNum}</span>`;
                    }
                });

                // Update buttons
                if (currentStep === 1) {
                    prevBtn.style.display = 'none';
                } else {
                    prevBtn.style.display = 'inline-flex';
                }

                if (currentStep === totalSteps) {
                    nextBtn.style.display = 'none';
                    submitBtn.style.display = 'inline-flex';
                } else {
                    nextBtn.style.display = 'inline-flex';
                    submitBtn.style.display = 'none';
                }

                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            async function handleSubmit(e) {
                e.preventDefault();

                // Final validation
                if (!validateStep(3)) {
                    return;
                }

                // Show loading
                Swal.fire({
                    title: 'Inachakata...',
                    text: 'Tafadhali subiri ombi lako linawasilishwa',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Ombi Limewasilishwa!',
                            html: `
                                <p>${data.message}</p>
                                <p><strong>Namba ya Ombi:</strong> ${data.permit_number}</p>
                                <p>Utapata taarifa mara ombi litakapothibitishwa.</p>
                            `,
                            confirmButtonColor: '#22c55e',
                            confirmButtonText: 'Sawa'
                        }).then(() => {
                            window.location.href = '{{ route("parent.e-permit.student-form") }}';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hitilafu!',
                            text: data.message || 'Ombi halikuweza kufanikiwa. Tafadhali jaribu tena.',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Hitilafu ya Mtandao!',
                        text: 'Tafadhali angalia muunganiko wako wa intaneti na jaribu tena.',
                        confirmButtonColor: '#ef4444'
                    });
                }
            }

            // Initialize
            init();
        })();
    </script>
</body>

</html>
