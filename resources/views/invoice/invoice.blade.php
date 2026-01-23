@extends('SRTDashboard.frame')
@section('content')
    <style>
        /* ===== CORE STYLES ===== */
        :root {
            --primary-color: #1a237e;
            --secondary-color: #283593;
            --accent-color: #3949ab;
            --border-color: #e0e0e0;
            --light-gray: #f5f5f5;
            --text-primary: #212121;
            --text-secondary: #757575;
            --font-main: 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background: #fafafa;
            font-family: var(--font-main);
            color: var(--text-primary);
            line-height: 1.2;
            margin: 0;
            padding: 15px;
        }

        /* ===== INVOICE CONTAINER ===== */
        .invoice-container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .invoice-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* ===== HEADER SECTION ===== */
        .invoice-header {
            padding: 20px 35px 15px;
            border-bottom: 2px solid var(--primary-color);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-shrink: 0;
        }

        .company-section {
            flex: 1;
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .logo-container {
            flex-shrink: 0;
            position: relative;
        }

        /* FIXED LOGO - NO WHITE SPACES */
        .company-logo {
            width: 80px;
            height: 80px;
            object-fit: cover; /* CHANGED: COVER instead of contain */
            border-radius: 50%;
            border: 1px solid var(--primary-color); /* THINNER BORDER */
            background: white;
        }

        .company-info {
            flex: 1;
        }

        .company-name {
            font-size: 26px;
            font-weight: 700;
            color: var(--primary-color);
            letter-spacing: -0.5px;
            margin: 0 0 5px 0;
        }

        .company-tagline {
            font-size: 11px;
            color: var(--text-secondary);
            margin: 0 0 15px 0;
            font-weight: bold;
            font-style: italic;
            text-decoration: underline;
        }

        .contact-details {
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.2;
        }

        .invoice-info-section {
            text-align: right;
            min-width: 250px;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: 300;
            color: var(--primary-color);
            margin: 0 0 15px 0;
            letter-spacing: 2px;
        }

        .invoice-meta {
            font-size: 12px;
        }

        .meta-row {
            margin: 6px 0;
            display: flex;
            justify-content: space-between;
        }

        .meta-label {
            font-weight: 600;
            min-width: 100px;
            text-align: left;
        }

        .meta-value {
            text-align: right;
            color: var(--text-secondary);
        }

        /* ===== CLIENT & DETAILS SECTION ===== */
        .details-section {
            padding: 20px 35px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        .details-card {
            padding: 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid var(--border-color);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px dotted #eee;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-primary);
            min-width: 120px;
        }

        .detail-value {
            color: var(--text-secondary);
            text-align: right;
            flex: 1;
        }

        /* ===== SERVICES TABLE ===== */
        .services-section {
            padding: 20px 35px 0; /* REDUCED BOTTOM PADDING */
            flex: 1;
        }

        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 15px; /* REDUCED MARGIN */
        }

        .services-table thead {
            background: var(--light-gray);
            border-top: 2px solid var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }

        .services-table th {
            padding: 12px 10px;
            font-weight: 600;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-primary);
        }

        .services-table td {
            padding: 14px 10px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: top;
        }

        .amount-input {
            width: 100px;
            padding: 8px 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            text-align: right;
            transition: border-color 0.2s;
        }

        .amount-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        /* ===== TOTALS SECTION ===== */
        .totals-section {
            padding: 0 35px 10px; /* REDUCED BOTTOM PADDING */
            text-align: right;
            flex-shrink: 0;
        }

        .total-row {
            display: inline-block;
            text-align: left;
            min-width: 300px;
        }

        .total-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-right: 20px;
            min-width: 150px;
            display: inline-block;
        }

        .total-amount {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
            letter-spacing: 1px;
        }

        /* ===== SIGNATURE & STAMP SECTION ===== */
        .signature-section {
            padding: 20px 35px;
            border-top: 1px dashed var(--border-color);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            flex-shrink: 0;
        }

        .signature-box, .stamp-box {
            padding: 20px;
            text-align: center;
        }

        .signature-line {
            width: 250px;
            height: 1px;
            background: #000;
            margin: 60px auto 10px;
            position: relative;
        }

        .signature-line:before {
            content: "";
            position: absolute;
            top: -25px;
            left: 0;
            width: 100%;
            height: 50px;
            border-bottom: 1px solid #000;
        }

        .signature-text {
            margin-top: 10px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .signature-name {
            margin-top: 5px;
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .signature-title {
            margin-top: 2px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .stamp-container {
            display: inline-block;
            padding: 35px 50px;
            border: 2px solid var(--primary-color);
            border-radius: 5px;
            transform: rotate(-5deg);
            position: relative;
            background: rgba(255, 255, 255, 0.9);
        }

        .stamp-container:before {
            /* content: "UNPAID"; */
            position: absolute;
            top: -10px;
            right: -10px;
            /* background: #4CAF50; */
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .stamp-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stamp-subtext {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 5px;
        }

        /* ===== PAYMENT & TERMS ===== */
        .payment-section {
            padding: 20px 35px;
            border-top: 1px solid var(--border-color);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
            flex-shrink: 0;
        }

        .payment-method,
        .terms-conditions {
            padding: 15px;
            background: var(--light-gray);
            border-radius: 4px;
        }

        .payment-method h4,
        .terms-conditions h4 {
            margin: 0 0 15px 0;
            color: var(--primary-color);
            font-size: 13px;
            font-weight: 600;
        }

        .payment-detail {
            margin: 6px 0;
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* ===== FIXED FOOTER ===== */
        .invoice-footer {
            padding: 15px 35px;
            background: var(--light-gray);
            border-top: 1px solid var(--border-color);
            font-size: 10px;
            color: var(--text-secondary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            margin-top: auto;
        }

        /* ===== ACTION BUTTONS ===== */
        .action-buttons {
            position: fixed;
            bottom: 35px;
            right: 30px;
            z-index: 1000;
            display: flex;
            gap: 15px;
        }

        .print-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
            text-decoration: none;
        }

        .print-btn:hover {
            background: var(--secondary-color);
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 768px) {
            .invoice-header {
                flex-direction: column;
                gap: 20px;
            }

            .company-section {
                flex-direction: column;
                text-align: center;
                align-items: center;
            }

            .invoice-info-section {
                text-align: left;
                width: 100%;
            }

            .details-section,
            .payment-section,
            .signature-section {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .action-buttons {
                bottom: 25px;
                right: 25px;
                flex-direction: column;
            }

            .signature-line {
                width: 180px;
            }
        }

        @media (max-width: 480px) {
            .invoice-header {
                padding: 10px !important;
            }

            .company-name {
                font-size: 20px;
            }

            .invoice-title {
                font-size: 24px;
            }

            .company-logo {
                width: 70px;
                height: 70px;
            }
        }
    </style>

    <div class="invoice-container">
        <div class="invoice-content">
            <!-- Header - Fixed Layout -->
            <div class="invoice-header">
                <div class="company-section">
                    <div class="logo-container">
                        <img src="{{ asset('storage/logo/new_logo.png') }}" alt="ShuleApp Logo" class="company-logo">
                    </div>
                    <div class="company-info">
                        <h1 class="company-name">SHULEAPP</h1>
                        <p class="company-tagline">Empowering Education</p>
                        <div class="contact-details">
                            <div>{{ ucwords(strtolower(Auth::user()->first_name)) }}
                                {{ ucwords(strtolower(Auth::user()->last_name)) }}</div>
                            <div>Email: {{ Auth::user()->email }}</div>
                            <div>Phone: {{ Auth::user()->phone }}</div>
                        </div>
                    </div>
                </div>

                <div class="invoice-info-section">
                    <h2 class="invoice-title">INVOICE</h2>
                    <div class="invoice-meta">
                        <div class="meta-row">
                            <span class="meta-label">Invoice #:</span>
                            <span class="meta-value">INV-{{ \Carbon\Carbon::now()->format('Ymd-His') }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Date:</span>
                            <span class="meta-value">{{ \Carbon\Carbon::now()->format('d M Y') }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Due Date:</span>
                            <span class="meta-value">{{ \Carbon\Carbon::now()->addMonth()->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client & Billing Details -->
            <div class="details-section">
                <div class="details-card">
                    <h3 class="section-title">Billed To</h3>
                    <div class="detail-row">
                        <span class="detail-label">School:</span>
                        <span class="detail-value"><strong>{{ ucwords(strtolower($schools->school_name)) }}</strong></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value">{{ ucwords(strtolower($schools->postal_address)) }},
                            {{ ucwords(strtolower($schools->postal_name)) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Country:</span>
                        <span class="detail-value">{{ ucwords(strtolower($schools->country)) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Contact Email:</span>
                        <span class="detail-value">{{ $managers->first()->email ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="details-card">
                    <h3 class="section-title">Service Period</h3>
                    <div class="detail-row">
                        <span class="detail-label">Start Date:</span>
                        <span
                            class="detail-value">{{ \Carbon\Carbon::parse($schools->service_start_date)->format('d M Y') ?? 'Not set' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">End Date:</span>
                        <span
                            class="detail-value">{{ \Carbon\Carbon::parse($schools->service_end_date)->format('d M Y') ?? 'Not set' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Duration:</span>
                        <span
                            class="detail-value">{{ \Carbon\Carbon::parse($schools->service_start_date)->diffInMonths(\Carbon\Carbon::parse($schools->service_end_date)) }}
                            months</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">No. of Students:</span>
                        <span class="detail-value"><strong>{{ count($students) }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Services Table -->
            <div class="services-section">
                <h3 class="section-title">Service Description</h3>
                <table class="services-table">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 35%">Description</th>
                            <th style="width: 20%">Period</th>
                            <th style="width: 10%">Students</th>
                            <th style="width: 15%">Unit Cost (TZS)</th>
                            <th style="width: 15%">Total (TZS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <strong>Service Subscription</strong><br>
                                <small style="color: var(--text-secondary);">Complete school management system for academic
                                    year {{ \Carbon\Carbon::now()->format('Y') }}</small>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($schools->service_start_date)->format('d/m/Y') ?? '-' }}<br>
                                to<br>
                                {{ \Carbon\Carbon::parse($schools->service_end_date)->format('d/m/Y') ?? '-' }}
                            </td>
                            <td style="text-align: center; font-weight: 600;">
                                {{ count($students) }}
                            </td>
                            <td style="text-align: center;">
                                <input type="number" id="unit_cost" class="amount-input" placeholder="0" min="0"
                                    value="" oninput="calculateTotal()">
                            </td>
                            <td style="text-align: center; font-weight: 600;" id="total_cost">
                                0
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="totals-section">
                <div class="total-row">
                    <span class="total-label">TOTAL AMOUNT DUE:</span>
                    <span class="total-amount" id="total_balance">TZS 0</span>
                </div>
            </div>

            <!-- SIGNATURE & STAMP SECTION -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="">------------------------------------</div>
                    <div class="signature-text">Signature</div>
                    <div class="signature-title">Recieved/Approved by</div>
                </div>

                <div class="stamp-box">
                    <div class="stamp-container">
                        <div class="stamp-text">{{ucwords(strtolower($schools->school_name))}}</div>
                        <div class="stamp-subtext">Official Stamp here</div>
                    </div>
                </div>
            </div>

            <!-- Payment & Terms -->
            <div class="payment-section">
                <div class="payment-method">
                    <h4>Payment Information</h4>
                    <div class="payment-detail"><strong>Bank:</strong> NMB Bank</div>
                    <div class="payment-detail"><strong>Account:</strong> 50510028891</div>
                    <div class="payment-detail"><strong>Name:</strong> Frank Mathias Masaka</div>
                    {{-- <div class="payment-detail" style="margin-top: 15px;"><strong>Mixx by Yas Lipa:</strong> 15966786 Name: Piano Shop</div> --}}
                </div>

                <div class="terms-conditions">
                    <h4>Terms & Conditions</h4>
                    <div class="payment-detail">• Payment due within 30 days</div>
                    <div class="payment-detail">• Late payment may result in service termination</div>
                    <div class="payment-detail">• Payment mode accepted Cash or Bank transfter</div>
                    <div class="payment-detail">• Contact for invoice queries</div>
                </div>
            </div>
        </div>

        <!-- Fixed Footer -->
        <div class="invoice-footer">
            <div>
                <strong>Invoice Generated By:</strong> {{ Auth::user()->email }}
            </div>
            <div>
                <strong>Printed On:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
            </div>
            <div>
                <strong>Page:</strong> 1 of 1
            </div>
        </div>
    </div>

    <!-- Action Buttons (Screen only) -->
    <div class="action-buttons">
        <button class="print-btn" onclick="generatePDF()" id="downloadPdfBtn">
            📥 Download Invoice
        </button>
        <a href="{{ route('admin.send.invoice', ['school' => Hashids::encode($schools->id)]) }}" class="print-btn"
            style="background: #4CAF50;">
            📧 Send Invoice
        </a>
    </div>

    <!-- Add required libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        // Initialize jsPDF
        window.jsPDF = window.jspdf.jsPDF;

        function calculateTotal() {
            const unitCost = parseFloat(document.getElementById("unit_cost").value) || 0;
            const totalStudents = {{ count($students) }};
            const total = unitCost * totalStudents;

            const formatter = new Intl.NumberFormat('en-US');

            document.getElementById("total_cost").textContent = formatter.format(total);
            document.getElementById("total_balance").textContent = 'TZS ' + formatter.format(total);
        }

        function generatePDF() {
            const unitCost = document.getElementById("unit_cost").value;
            const downloadBtn = document.getElementById("downloadPdfBtn");

            // Validate unit cost
            if (!unitCost || parseFloat(unitCost) <= 0) {
                alert('Please enter unit cost before downloading PDF');
                document.getElementById("unit_cost").focus();
                return;
            }

            // Show loading
            const originalText = downloadBtn.innerHTML;
            downloadBtn.innerHTML = '⏳ Generating PDF...';
            downloadBtn.disabled = true;

            // Get invoice element
            const invoiceElement = document.querySelector('.invoice-container');

            // Set PDF options
            const pdfOptions = {
                scale: 2,
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff',
                width: invoiceElement.offsetWidth,
                height: invoiceElement.offsetHeight
            };

            // Generate PDF
            html2canvas(invoiceElement, pdfOptions)
                .then(canvas => {
                    // Create PDF
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jsPDF('p', 'mm', 'a4');

                    // Calculate dimensions
                    const imgWidth = 190;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;

                    // Add image to PDF
                    pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);

                    // Generate filename
                    const fileName = `Invoice-{{ $schools->school_name }}-{{ \Carbon\Carbon::now()->format('Y-m-d') }}.pdf`
                        .replace(/\s+/g, '-')
                        .toLowerCase();

                    // Save PDF
                    pdf.save(fileName);

                    // Restore button
                    downloadBtn.innerHTML = originalText;
                    downloadBtn.disabled = false;
                })
                .catch(error => {
                    console.error('PDF generation error:', error);
                    alert('Error generating PDF. Please try again.');

                    // Restore button
                    downloadBtn.innerHTML = originalText;
                    downloadBtn.disabled = false;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('unit_cost');
            input.focus();
            input.select();

            if (input.value) calculateTotal();
        });
    </script>
@endsection
