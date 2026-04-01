<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShuleApp | Resend Token</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.5.2-web/css/all.css') }}">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .card {
            max-width: 500px;
            width: 100%;
            border-radius: 28px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 28px 28px 0 0 !important;
            padding: 20px;
            text-align: center;
        }

        .card-header i {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .card-header h4 {
            margin: 0;
            font-weight: 700;
        }

        .card-body {
            padding: 24px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 14px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .alert {
            border-radius: 14px;
            margin-bottom: 20px;
        }

        .info-text {
            background: #f0f4f8;
            padding: 16px;
            border-radius: 16px;
            margin-top: 20px;
            font-size: 0.85rem;
            color: #4a5568;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <i class="fas fa-paper-plane"></i>
            <h4>Omba Token Tena</h4>
            <p class="mb-0 mt-2 opacity-75">Ulipoteza token? Tuma tena kwa simu yako</p>
        </div>
        <div class="card-body">
            <div id="alertBox" class="alert" style="display: none;"></div>

            <form id="resendForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Chagua njia ya kutafuta</label>
                    <div class="d-flex gap-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="search_type" id="typeAdmission" value="admission" checked>
                            <label class="form-check-label" for="typeAdmission">
                                <i class="fas fa-id-card"></i> Namba ya Admission
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="search_type" id="typePhone" value="phone">
                            <label class="form-check-label" for="typePhone">
                                <i class="fas fa-phone-alt"></i> Namba ya Simu
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-4" id="admissionField">
                    <label class="form-label fw-bold">Namba ya Admission</label>
                    <input type="text" id="admissionInput" class="form-control" placeholder="Mfano: SSC-001" autocomplete="off">
                    <small class="text-muted">Ingiza namba ya admission ya mwanafunzi</small>
                </div>

                <div class="mb-4" id="phoneField" style="display: none;">
                    <label class="form-label fw-bold">Namba ya Simu ya Mzazi</label>
                    <input type="tel" id="phoneInput" class="form-control" placeholder="Mfano: 0712345678" autocomplete="off">
                    <small class="text-muted">Ingiza namba ya simu iliyosajiliwa kwa mzazi</small>
                </div>

                <button type="submit" id="submitBtn" class="btn btn-primary w-100">
                    <i class="fas fa-paper-plane me-2"></i> Tuma Token Tena
                </button>
            </form>

            <div class="info-text">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Maelezo:</strong> Token itatumwa kwa namba ya simu ya mzazi iliyosajiliwa.
                Hakikisha namba ni sahihi kabla ya kuomba.
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('tokens.verify') }}" class="text-decoration-none">
                    <i class="fas fa-arrow-left"></i> Rudi kwenye Verification
                </a>
            </div>
        </div>
    </div>

    <script>
        const searchTypeRadios = document.querySelectorAll('input[name="search_type"]');
        const admissionField = document.getElementById('admissionField');
        const phoneField = document.getElementById('phoneField');
        const admissionInput = document.getElementById('admissionInput');
        const phoneInput = document.getElementById('phoneInput');
        const submitBtn = document.getElementById('submitBtn');
        const alertBox = document.getElementById('alertBox');
        const form = document.getElementById('resendForm');

        // Toggle fields based on search type
        searchTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'admission') {
                    admissionField.style.display = 'block';
                    phoneField.style.display = 'none';
                    admissionInput.required = true;
                    phoneInput.required = false;
                } else {
                    admissionField.style.display = 'none';
                    phoneField.style.display = 'block';
                    admissionInput.required = false;
                    phoneInput.required = true;
                }
            });
        });

        function showAlert(message, type) {
            alertBox.textContent = message;
            alertBox.className = `alert alert-${type}`;
            alertBox.style.display = 'block';
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 5000);
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const searchType = document.querySelector('input[name="search_type"]:checked').value;
            const identifier = searchType === 'admission' ? admissionInput.value.trim() : phoneInput.value.trim();

            if (!identifier) {
                showAlert('Tafadhali ingiza taarifa sahihi', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Inachakata...';

            try {
                const response = await fetch('{{ route("tokens.resend") }}', {
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
                    showAlert(data.message, 'success');
                    // Clear inputs
                    admissionInput.value = '';
                    phoneInput.value = '';
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert('Hitilafu ya mtandao. Tafadhali jaribu tena.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Tuma Token Tena';
            }
        });
    </script>
</body>

</html>
