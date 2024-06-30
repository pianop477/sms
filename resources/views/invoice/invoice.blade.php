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
                                    <img src="{{asset('assets/img/logo/sms logo2.jpg')}}" alt="" class="profile-img border-radius-lg shadow-sm" style="width: 150px; object-fit:cover;">
                                    <p class="text-center font-weight-bold">ShuleApp</p>
                                </div>
                                <div class="iv-right col-10 text-md-right">
                                    <span>SHULEAPP - ADMIN</span>
                                    <p class="text-capitalize">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</p>
                                    <p>{{Auth::user()->email}}</p>
                                    <p>{{Auth::user()->phone}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="invoice-address">
                                    <h3>billed to</h3>
                                    <h5 class="text-uppercase">{{$school->school_name}}</h5>
                                    <p class="text-capitalize">{{$managers->first()->first_name}} {{$managers->first()->last_name}}</p>
                                    <p>{{$managers->first()->email}}</p>
                                    <p>{{$managers->first()->phone}}</p>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-right">
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
                                        <th class="text-left" style="width: 45%; min-width: 130px;">description</th>
                                        <th>Number of Students</th>
                                        <th style="min-width: 100px">Cost per Student</th>
                                        <th>total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-left">System Running Cost for Year - {{\Carbon\Carbon::now()->format('Y')}}</td>
                                        <td>{{count($students)}}</td>
                                        <td>{{number_format(3000)}}</td>
                                        @php
                                            $total = count($students) * 3000
                                        @endphp
                                        <td>TZS. {{number_format($total)}}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">total balance :</td>
                                        <td>{{number_format($total)}}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
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
                <p class="">Printed by: {{ Auth::user()->email}}</p>
            </div>
            <div class="col-4">
                <p class="">{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
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
