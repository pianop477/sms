@extends('SRTDashboard.frame')
@section('content')
<div class="row">
    <div class="col-12 mt-3 mt-lg-5">
        <div class="card">
            <div class="card-body">
                <div class="invoice-area">
                    <!-- Invoice Header -->
                    <div class="invoice-head">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-2 mb-3 mb-md-0 text-center text-md-start">
                                <img src="{{asset('assets/img/logo/logo.png')}}" alt="ShuleApp Logo"
                                     class="img-fluid border-radius-lg shadow-sm" style="max-width: 80px; border-radius:50px;">
                                <h2 class="mt-2 d-none d-md-block">INVOICE</h2>
                            </div>
                            <div class="col-12 col-md-10 text-center text-md-end">
                                <h2 class="d-md-none mb-3">INVOICE</h2>
                                <span class="d-block fw-bold">SHULEAPP - ADMINISTRATOR</span>
                                <p class="text-capitalize mb-1">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</p>
                                <p class="mb-1 small">{{Auth::user()->email}}</p>
                                <p class="small">{{Auth::user()->phone}}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Information -->
                    <div class="row mt-4">
                        <div class="col-12 col-md-6 mb-4 mb-md-0">
                            <div class="invoice-address">
                                <h4>Billed To</h4>
                                <h5 class="text-uppercase">{{$schools->school_name}}</h5>
                                <p class="text-capitalize mb-1">{{$schools->postal_address}} - {{$schools->postal_name}}</p>
                                <p class="text-capitalize mb-1">{{$schools->country}}</p>
                                <p class="mb-0">{{$managers->first()->email}}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <ul class="list-unstyled text-end">
                                <li class="mb-2">
                                    <h4>Invoice Details</h4>
                                </li>
                                <li class="mb-1">Date of Issue: {{\Carbon\Carbon::now()->format('d-m-Y')}}</li>
                                <li>Due Date: {{ \Carbon\Carbon::now()->addMonth()->format('d-m-Y') }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Invoice Table -->
                    <div class="invoice-table table-responsive mt-4">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="text-capitalize">
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th class="text-start">Service Description</th>
                                    <th class="text-start">Service Time Duration</th>
                                    <th class="text-center">No. Students</th>
                                    <th class="text-center">Unit Cost</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-start">System Running Cost for Year - {{ \Carbon\Carbon::now()->format('Y') }}</td>
                                    <td class="text-start">
                                        {{ \Carbon\Carbon::parse($schools->service_start_date)->format('d/m/Y') ?? '-' }} -
                                        {{ \Carbon\Carbon::parse($schools->service_end_date)->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="text-center">{{ count($students) }}</td>
                                    <td class="text-center">
                                        <form action="" role="form">
                                            <input type="number" id="unit_cost" class="form-control text-end"
                                                   placeholder="Enter Amount" min="0" value="">
                                        </form>
                                    </td>
                                    <td class="text-end" id="total_cost">0</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="border-0"></td>
                                    <td class="text-end border-0"><strong>Total Balance:</strong></td>
                                    <td class="text-end border-0"><strong>TZS. <span id="total_balance">0</span></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <hr>

                    <!-- Payment Methods -->
                    <div class="row mt-4">
                        <div class="col-12 text-center mb-3">
                            <h4>Payment Methods</h4>
                        </div>
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="card p-3">
                                <h5>NMB Bank</h5>
                                <p class="mb-1">Account Number: <strong>50510028891</strong></p>
                                <p class="mb-0">Account Name: <strong>Frank Mathias Masaka</strong></p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card p-3">
                                <h5>Lipa Namba | Lipa kwa Simu</h5>
                                <p class="mb-1"><strong>Tigo Lipa Number</strong></p>
                                <p class="mb-1">Merchant Acc. Number: <strong>15966786</strong></p>
                                <p class="mb-0">Merchant Acc. Name: <strong>Piano Shop</strong></p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2 mt-4 no-print">
                        <a href="#" class="btn btn-primary" onclick="scrollToTopAndPrint(); return false;">
                            <i class="fas fa-print me-1"></i> Print Bill
                        </a>
                        <a href="{{route('admin.send.invoice', ['school' => Hashids::encode($schools->id)])}}"
                           class="btn btn-success">
                           <i class="fas fa-paper-plane me-1"></i> Send Bill
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer mt-5 d-print-none" style="position: fixed; bottom: 0; width: 100%; border-top: 1px solid #ddd; padding: 10px 0; background: white;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-8">
                <p class="text-start mb-0">Printed by: {{ Auth::user()->email}}</p>
            </div>
            <div class="col-12 col-md-4">
                <p class="text-start text-md-end mb-0">Printed on: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
    @media print {
        body {
            font-size: 12pt;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .invoice-table {
            font-size: 11pt;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10pt;
        }
        @page {
            margin: 10mm;
        }
        thead {
            display: table-header-group;
        }
        tbody {
            display: table-row-group;
        }
        .no-print {
            display: none !important;
        }
    }

    @media (max-width: 768px) {
        .invoice-table table {
            font-size: 12px;
        }
        .invoice-table th,
        .invoice-table td {
            padding: 5px;
        }
    }
</style>

<!-- jQuery Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
    $(document).ready(function() {
        $("#unit_cost").on("keyup", function() {
            let unitCost = parseFloat($(this).val()) || 0;
            let totalStudents = {{ count($students) }};
            let total = unitCost * totalStudents;

            $("#total_cost").text(total.toLocaleString());
            $("#total_balance").text(total.toLocaleString());
        });
    });

    function scrollToTopAndPrint() {
        window.scrollTo(0, 0);
        window.print();
    }
</script>
