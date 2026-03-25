{{-- resources/views/payroll/scan-qr.blade.php --}}

@extends('SRTDashboard.frame')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-qrcode me-2"></i> Scan Salary Slip QR Code
                    </h5>
                </div>
                <div class="card-body text-center">

                    {{-- QR Reader Container --}}
                    <div id="qr-reader" style="width: 100%; max-width: 450px; margin: 0 auto;"></div>

                    {{-- Loading State --}}
                    <div id="qr-result" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-spinner fa-spin me-2"></i> Verifying...
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Manual Token Entry --}}
                    <p class="text-muted mb-2">Or enter token manually:</p>
                    <form id="manual-token-form" class="d-flex justify-content-center gap-2 flex-wrap">
                        <input type="text" id="manual-token" class="form-control w-50"
                               placeholder="Paste token here" autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle me-1"></i> Verify
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let html5QrCode;
        const readerElement = document.getElementById('qr-reader');

        // Initialize QR scanner
        html5QrCode = new Html5Qrcode("qr-reader");

        const onScanSuccess = (decodedText, decodedResult) => {
            // Stop scanning
            html5QrCode.stop();

            // Show processing
            const resultDiv = document.getElementById('qr-result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i> Verifying token...</div>';

            // Redirect to verification page
            window.location.href = `/verify-slip?token=${decodedText}`;
        };

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            (errorMessage) => {
                // Silent ignore - continuous scanning
            }
        ).catch(err => {
            console.error("Camera error:", err);
            readerElement.innerHTML = '<div class="alert alert-warning mt-3">Camera access denied or not supported. Please use manual token entry.</div>';
        });

        // Manual token submission
        document.getElementById('manual-token-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const token = document.getElementById('manual-token').value.trim();
            if (token) {
                window.location.href = `/verify-slip?token=${token}`;
            } else {
                alert('Please enter a token');
            }
        });
    });
</script>
@endpush

@endsection
