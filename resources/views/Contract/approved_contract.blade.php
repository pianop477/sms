@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase text-center">Approved Contracts in: {{$month}} - {{$year}}</h4>
                    </div>
                    <div class="col-4">
                        <a href="{{route('contract.by.months', ['year' => $year, 'month' => $month])}}" class="float-right btn btn-info">
                            <i class="fas fa-circle-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-capitalize">
                                <tr class="text-center">
                                    <th scope="col">ID</th>
                                    <th scope="col">Teacher's Name</th>
                                    <th scope="col">Contract Type</th>
                                    <th scope="col">approved_at</th>
                                    <th scope="col">Expire_at</th>
                                    <th scope="col">Contract length</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allContracts as $row )
                                    <tr class="text-center">
                                        <td>{{strtoupper($row->member_id)}}</td>
                                        <td class="text-capitalize">{{$row->first_name. ' '. $row->last_name}}</td>
                                        <td class="text-capitalize">{{$row->contract_type}}</td>
                                        <td class="text-capitalize">
                                            {{\Carbon\Carbon::parse($row->approved_at)->format('d-m-Y H:i')}}
                                        </td>
                                        <td>{{\Carbon\Carbon::parse($row->end_date)->format('d-m-Y H:i')}}</td>
                                        <td>{{$row->duration}} Months</td>
                                        <td>
                                            @if ($row->duration > 0)
                                                <span class="badge bg-success text-white">{{$row->status}}</span>
                                            @else
                                            <span class="badge bg-danger text-white">{{$row->status}}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
