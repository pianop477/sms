<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShuleApp | Contracts Gateway</title>

    <!-- Manifest and Icons -->
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-16x16.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-32x32.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-192x192.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/new_favicon-512x512.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.5.2-web/css/all.css') }}">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }

        .gateway-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .gateway-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            width: 100%;
            padding: 32px 24px;
            animation: slideUp 0.4s ease;
        }

        /* Small screen optimizations */
        @media (max-width: 480px) {
            .gateway-card {
                padding: 24px 16px;
                border-radius: 24px;
            }
        }

        @media (max-width: 360px) {
            .gateway-card {
                padding: 20px 12px;
            }
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

        .reapply-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
        }

        @media (max-width: 480px) {
            .reapply-header {
                padding: 1.2rem 1rem;
            }

            .reapply-header h4 {
                font-size: 1.3rem;
            }

            .reapply-header p {
                font-size: 0.9rem;
            }
        }

        .original-contract-card {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 480px) {
            .original-contract-card {
                padding: 1rem;
            }
        }

        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 6px;
            display: block;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 16px;
            /* Prevents zoom on mobile */
            transition: all 0.3s;
            background: #fafbfc;
            -webkit-appearance: none;
        }

        .form-control:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
            background: white;
        }

        /* iOS fix for input zoom */
        @supports (-webkit-touch-callout: none) {
            .form-control {
                font-size: 16px;
            }
        }

        select.form-control {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            appearance: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            width: 100%;
            margin-top: 10px;
        }

        @media (min-width: 768px) {
            .btn-primary {
                width: auto;
                margin-top: 0;
            }
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-bottom: 10px;
        }

        @media (min-width: 768px) {
            .btn-secondary {
                width: auto;
                margin-bottom: 0;
                margin-right: 10px;
            }
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .text-right {
            text-align: center;
        }

        @media (min-width: 768px) {
            .text-right {
                text-align: right;
            }
        }

        .alert {
            padding: 14px 16px;
            border-radius: 14px;
            font-size: 0.95rem;
            line-height: 1.5;
            word-break: break-word;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .footer {
            text-align: center;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.85rem;
            padding: 0 10px;
        }

        .footer a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            opacity: 0.9;
            transition: opacity 0.3s;
            padding: 4px 8px;
            display: inline-block;
        }

        .footer a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        /* Touch-friendly improvements */
        button,
        a {
            -webkit-tap-highlight-color: transparent;
        }

        /* Row spacing for mobile */
        .row {
            margin-left: -8px;
            margin-right: -8px;
        }

        .col-md-6 {
            padding-left: 8px;
            padding-right: 8px;
        }

        small.text-muted {
            font-size: 0.8rem;
            display: block;
            margin-top: 5px;
        }

        strong {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="gateway-container">
        <div class="gateway-card">
            <div class="reapply-header">
                <h4>
                    <i class="fas fa-redo-alt mr-2"></i>
                    Tuma Ombi Tena
                </h4>
                <p class="mb-0">Tafadhali sahihisha mapungufu na wasilisha ombi tena</p>
            </div>

            <!-- Original Contract Details -->
            <div class="original-contract-card">
                <h5 class="text-danger mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Maelezo ya Mkataba wa Awali (Umekataliwa)
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <p><strong>Aina ya Mkataba:</strong>
                            @if ($oldContract->contract_type == 'provision')
                                Mkataba wa Matazamio
                            @else
                                Mkataba Mpya
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <p><strong>Tarehe ya Kuomba:</strong>
                            {{ \Carbon\Carbon::parse($oldContract->applied_at)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <p><strong>Wadhifa:</strong> {{ $oldContract->job_title ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <p><strong>Tarehe ya Kukataliwa:</strong>
                            {{ $oldContract->rejected_at ? \Carbon\Carbon::parse($oldContract->rejected_at)->format('d M Y') : 'N/A' }}
                        </p>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-danger mt-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Sababu ya Kukataliwa:</strong>
                            <p class="mb-0 mt-2">{{ $oldContract->remarks ?? 'Hakuna sababu iliyotolewa' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reapplication Form -->
            <form action="{{ route('contract.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="is_reapply" value="1">
                <input type="hidden" name="original_contract_id" value="{{ $oldContract->id }}">

                @if ($authToken ?? null)
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                @endif

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Aina ya Mkataba</label>
                        <select name="contract_type" required class="form-control">
                            <option value="provision"
                                {{ $oldContract->contract_type == 'provision' ? 'selected' : '' }}>⏳ Mkataba wa
                                Matazamio
                            </option>
                            <option value="new" {{ $oldContract->contract_type == 'new' ? 'selected' : '' }}>
                                📄 Mkataba Mpya
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Barua ya Maombi (PDF)</label>
                        <input type="file" name="application_letter" required class="form-control" accept=".pdf">
                        <small class="text-muted">Pakia barua yako iliyosahihishwa</small>
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ route('contract.index') }}?auth_token={{ $authToken ?? '' }}"
                        class="btn btn-secondary">
                        <i class="fas fa-times mr-2"></i>Ghairi
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>Wasilisha Ombi
                    </button>
                </div>
            </form>
        </div>

        <div class="footer">
            @php
                $startYear = 2025;
                $currentYear = date('Y');
            @endphp
            <p>© {{ $startYear == $currentYear ? $startYear : $startYear . ' - ' . $currentYear }} ShuleApp |
                <a href="{{ route('welcome') }}"><i class="fas fa-home"></i> Nyumbani</a>
            </p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/scripts.js') }}?v={{ time() }}"></script>

    <script>
    $(document).ready(function() {
        // Handle form submission WITHOUT AJAX - let it submit normally
        $('form').on('submit', function() {
            // Show loading state on button
            let submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<span class="spinner-border spinner-border-sm mr-2"></span>Inawasilisha...');

            // Let the form submit normally
            return true;
        });

        // Check for flash messages from redirect
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Imefanikiwa!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                willClose: () => {
                    const token = '{{ $authToken ?? '' }}';
                    const url = token ? '{{ route('contract.dashboard') }}?auth_token=' + token :
                        '{{ route('contract.dashboard') }}';
                    window.location.href = url;
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Hitilafu!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc2626'
            });
        @endif
    });
</script>
</body>

</html>
