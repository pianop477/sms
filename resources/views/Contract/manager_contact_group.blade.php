@extends('SRTDashboard.frame')

@section('content')
    <div class="row">
        <div class="col-md-4 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-center text-uppercase">Contract Groups</h4>
                    <p class="text-danger">Select Year</p>
                    @if ($contractsByYear->isEmpty())
                    <div class="alert alert-danger" role="alert">
                        <p>No Records Available</p>
                    </div>
                    @else
                    <div class="list-group">
                        @foreach ($contractsByYear as $year => $contract )
                            <a href="{{route('contract.by.months', ['year' => $year])}}" class="list-group-item list-group-item-action">
                                >> {{$year}}
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-center text-uppercase">Contracts Request List</h4>
                    @if ($contractRequests->isEmpty())
                        <div class="alert alert-warning text-center">
                            <p class="text-danger">There is no new contract request!</p>
                        </div>
                        @else
                        <table class="table table-responsive-md table-bordered table-hover" id="myTable">
                            <thead>
                                <tr>
                                    <th>Teacher's Name</th>
                                    <th>Applied_at</th>
                                    <th>Status</th>
                                    <th>Attachment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contractRequests as $row )
                                    <tr>
                                        <td class="text-capitalize">{{$row->first_name}} {{$row->last_name}}</td>
                                        <td>{{\Carbon\Carbon::parse($row->applied_at)->format('d-m-Y H:i:s')}}</td>
                                        <td>
                                            <span class="badge bg-warning text-white text-capitalize">{{$row->status}}</span>
                                        </td>
                                        <td>
                                            <a href="{{route('contract.admin.preview', $row->id)}}" target="_blank" class="btn btn-info btn-xs"> view</a>
                                        </td>
                                        <td>
                                            <ul class="d-flex">
                                                <li class="mr-3">
                                                    <!-- Button trigger modal -->
                                                    <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#exampleModal1">
                                                        Approve
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Approve Contract Request</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <p class="text-center text-danger">Applicant Details</p>
                                                                        <ul class="list-group">
                                                                            <li class="list-group-item text-capitalize">Applicant Name: <strong>{{$row->first_name}} {{$row->last_name}}</strong> - Gender: <strong>{{$row->gender}}</strong></li>
                                                                            <li class="list-group-item text-capitalize">Member ID: <strong>{{$row->member_id}}</strong></li>
                                                                            <li class="list-group-item text-capitalize">Application Type: <strong>{{$row->contract_type}} Contract</strong></li>
                                                                          </ul>
                                                                    </div>
                                                                </div>
                                                                <hr class="dark horizontal py-0">
                                                                <p class="text-center text-danger">Complete Approval Actions</p>
                                                                <form action="{{route('contract.approval', $row->id)}}" method="POST" novalidate="" class="needs-validation" enctype="multipart/form-data" role="form">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <input type="hidden" name="contract_id" value="{{$row->id}}">
                                                                                <label for="" class="control-label">Set Months</label>
                                                                                <input type="number" class="form-control" name="duration" id="validationCustom01" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <div class="form-group">
                                                                                <label for="" class="control-label">Remarks</label>
                                                                                <textarea name="remark" id="" cols="30" rows="2" class="form-control" id="validationCustom01" required></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                                <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to Approve this request?')">Approve</button>
                                                            </form>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="mr-3">
                                                    <!-- Button trigger modal -->
                                                    <button type="button" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#exampleModal">
                                                        Reject
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Reject Contract Request</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <p class="text-center text-danger">Applicant Details</p>
                                                                        <ul class="list-group">
                                                                            <li class="list-group-item text-capitalize">Applicant Name: <strong>{{$row->first_name}} {{$row->last_name}}</strong> - Gender: <strong>{{$row->gender}}</strong></li>
                                                                            <li class="list-group-item text-capitalize">Member ID: <strong>{{$row->member_id}}</strong></li>
                                                                            <li class="list-group-item text-capitalize">Application Type: <strong>{{$row->contract_type}} Contract</strong></li>
                                                                          </ul>
                                                                    </div>
                                                                </div>
                                                                <hr class="dark horizontal py-0">
                                                                <p class="text-center text-danger">Complete Reject Actions</p>
                                                                <form action="{{route('contract.rejection', $row->id)}}" method="POST" novalidate="" class="needs-validation" enctype="multipart/form-data" role="form">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <input type="hidden" name="contract_id" value="{{$row->id}}">
                                                                                <label for="" class="control-label">Reason for Rejection</label>
                                                                                <textarea name="remark" id="" cols="30" rows="2" class="form-control" id="validationCustom01" required></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                                <div class="modal-footer">
                                                                <button type="button" class="btn" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this request?')">Reject</button>
                                                            </form>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
