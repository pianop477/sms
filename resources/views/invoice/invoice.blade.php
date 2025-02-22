@extends('SRTDashboard.frame')
    @section('content')
    <div class="row">
        <div class="col-lg-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="invoice-area">
                        <div class="invoice-head">
                            <div class="row">
                                <div class="iv-left col-2">
                                    <h2>INVOICE</h2>
                                    <img src="{{asset('assets/img/logo/logo.png')}}" alt="" class="profile-img border-radius-lg shadow-sm" style="width: 150px; object-fit:cover; border-radius:50px;">
                                    {{-- <p class="text-center font-weight-bold">ShuleApp</p> --}}
                                </div>
                                <div class="iv-right col-10 text-right">
                                    <span>SHULEAPP - ADMIN</span>
                                    <p class="text-capitalize">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</p>
                                    <p>{{Auth::user()->email}}</p>
                                    <p>{{Auth::user()->phone}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="invoice-address">
                                    <h4>Billed To</h4>
                                    <h5 class="text-uppercase">{{$school->school_name}}</h5>
                                    <p class="text-capitalize">{{$managers->first()->first_name}} {{$managers->first()->last_name}} - Manager</p>
                                    <p class="text-capitalize">P.O Box {{$school->postal_address}} - {{$school->postal_name}}</p>
                                    <p class="text-capitalize">{{$school->country}}</p>
                                    <p>{{$managers->first()->email}}</p>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <ul class="invoice-date">
                                    <li>
                                        <h4>Invoice Details</h4>
                                    </li>
                                    <li>Date of Issue: {{\Carbon\Carbon::now()->format('d-m-Y')}}</li>
                                    <li>Due Date : {{ \Carbon\Carbon::now()->addMonth()->format('d-m-Y') }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="invoice-table table-responsive mt-5">
                            <table class="table table-bordered table-hover text-right">
                                <thead>
                                    <tr class="text-capitalize">
                                        <th class="text-center" style="width: 5%;">id</th>
                                        <th class="text-left" style="">description</th>
                                        <th class="text-left">Service Time Duration</th>
                                        <th style="">No.Students</th>
                                        <th style="" style="max-width: 10px">Unit Cost</th>
                                        <th>total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-left">System Running Cost for Year - {{ \Carbon\Carbon::now()->format('Y') }}</td>
                                        <td class="text-left">
                                            {{ \Carbon\Carbon::parse($school->service_start_date)->format('d/m/Y') ?? '-' }} -
                                            {{ \Carbon\Carbon::parse($school->service_end_date)->format('d/m/Y') ?? '-' }}
                                        </td>
                                        <td class="text-center">{{ count($students) }}</td>
                                        <td class="text-center">
                                            <form action="" role="form">
                                                <input type="number" id="unit_cost" class="form-control text-right" placeholder="Enter Unit Amount" min="0" value="">
                                            </form>
                                        </td>
                                        <td id="total_cost">0</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"><strong>Total Balance:</strong></td>
                                        <td><strong>TZS. <span id="total_balance">0</span></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <h4>Payments</h4>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-4">
                                <h5>NMB Bank</h5>
                                <p>Account Number: <strong>50510028891</strong></p>
                                <p>Account Name: <strong>Frank Mathias Masaka</strong></p>
                            </div>
                            <div class="col-4">
                                <h5>Mobile Money Networks</h5>
                                <p>Tigo Pesa</p>
                                <p>Phone Number: <strong>{{Auth::user()->phone}}</strong></p>
                                <p>Account Name: <strong>Frank Mathias Masaka</strong></p>
                            </div>
                            <div class="col-4">
                                <h5>Lipa Namba | Lipa kwa Simu</h5>
                                <p><strong>Tigo Lipa Number</strong></p>
                                <p>Merchant Acc. Number: <strong>15966786</strong></p>
                                <p>Merchant Acc. Name: <strong>Piano Shop</strong></p>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="text-right">
                        <a href="#" class="btn btn-primary no-print" onclick="scrollToTopAndPrint(); return false;">Print Invoice</a>
                        <a href="" class="btn btn-success no-print">Send Invoice</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer mt-5" style="position: fixed; bottom: 0; width: 100%; border-top: 1px solid #ddd; padding-top: 10px;">
        <div class="row">
            <div class="col-8">
                <p class="text-left">Printed by: {{ Auth::user()->email}}</p>
            </div>
            <div class="col-4">
                <p class="text-right">Printed on: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
        <script type="text/php">
            if ( isset($pdf) ) {
                $pdf->page_text(270, 770, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0,0,0));
            }
        </script>
    </div>
    @endsection
    <style>
        @media print {
            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                text-align: center;
                border-top: 1px solid #ddd;
                padding-top: 10px;
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
        }
    </style>
    <!-- jQuery Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#unit_cost").on("keyup", function() {
                let unitCost = parseFloat($(this).val()) || 0; // Ikiwa input ni tupu, iwe 0
                let totalStudents = {{ count($students) }};
                let total = unitCost * totalStudents;

                // Weka thamani mpya kwenye total cost na total balance
                $("#total_cost").text(total.toLocaleString());
                $("#total_balance").text(total.toLocaleString());
            });
        });
    </script>
