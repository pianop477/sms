@extends('SRTDashboard.frame')

@section('content')
    <div class="row">
        <div class="col-md-4 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-center text-capitalize">Approved Contract Group</h4>
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
                    <h4 class="header-title text-center text-capitalize">New Contracts Requests</h4>
                    @if ($contractRequests->isEmpty())
                        <div class="alert alert-warning text-center">
                            <p class="text-danger">There is no new contract request!</p>
                        </div>
                        @else
                        <table class="table table-responsive-md table-bordered table-hover" id="myTable">
                            <thead>
                                <tr class="text-center">
                                    <th>Teacher's Name</th>
                                    <th>Applied_at</th>
                                    <th>Updated_at</th>
                                    <th>Status</th>
                                    <th>Attachment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contractRequests as $row )
                                    <tr class="text-center">
                                        <td class="text-capitalize">{{$row->first_name}} {{$row->last_name}}</td>
                                        <td>{{\Carbon\Carbon::parse($row->applied_at)->format('d-m-Y H:i')}}</td>
                                        <td>{{\Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i')}}</td>
                                        <td>
                                            <span class="badge bg-warning text-white text-capitalize">{{$row->status}}</span>
                                        </td>
                                        <td>
                                            {{-- <a href="{{route('contract.admin.preview', $row->id)}}" target="_blank" class="btn btn-info btn-xs"> view</a> --}}
                                            <a href="{{route('contract.admin.preview', ['id' => Hashids::encode($row->id)])}}" target="_blank" class="">
                                                <i class="fas fa-paperclip text-success"></i>
                                            </a>
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
                                                                            <li class="list-group-item text-capitalize">Member ID: <strong>{{strtoupper($row->member_id)}}</strong></li>
                                                                            <li class="list-group-item text-capitalize">Application Type: <strong>{{$row->contract_type}} Contract</strong></li>
                                                                          </ul>
                                                                    </div>
                                                                </div>
                                                                <hr class="dark horizontal py-0">
                                                                <p class="text-center text-danger">Complete Approval Actions</p>
                                                                <form action="{{route('contract.approval', ['id' => Hashids::encode($row->id)])}}" method="POST" novalidate="" class="needs-validation" enctype="multipart/form-data" role="form">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="" class="control-label">Set Months</label>
                                                                                <input type="number" class="form-control" name="duration" id="validationCustom01" required value="{{old('duration')}}">
                                                                                @error('duration')
                                                                                    <div class="text-danger">{{$message}}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <div class="form-group">
                                                                                <label for="" class="control-label">Remarks</label>
                                                                                <textarea name="remark" id="" cols="30" rows="2" class="form-control" id="validationCustom01" required>{{old('remark')}}</textarea>
                                                                                @error('remark')
                                                                                    <div class="text-danger">{{$message}}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                                <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                                <button type="submit" id="saveButton" class="btn btn-success" onclick="return confirm('Are you sure you want to Approve this request?')">Approve</button>
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
                                                                            <li class="list-group-item text-capitalize">Member ID: <strong>{{strtoupper($row->member_id)}}</strong></li>
                                                                            <li class="list-group-item text-capitalize">Application Type: <strong>{{$row->contract_type}} Contract</strong></li>
                                                                          </ul>
                                                                    </div>
                                                                </div>
                                                                <hr class="dark horizontal py-0">
                                                                <p class="text-center text-danger">Complete Reject Actions</p>
                                                                <form action="{{route('contract.rejection', ['id' => Hashids::encode($row->id)])}}" method="POST" novalidate="" class="needs-validation" enctype="multipart/form-data" role="form">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="" class="control-label">Reason for Rejection</label>
                                                                                <textarea name="remark" id="" cols="30" rows="2" class="form-control" id="validationCustom01" required>{{old('remark')}}</textarea>
                                                                                @error('remark')
                                                                                    <div class="text-danger">{{$message}}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                                <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton"); // Tafuta button kwa ID

            if (!form || !submitButton) return; // Kama form au button haipo, acha script isifanye kazi

            form.addEventListener("submit", function (event) {
                event.preventDefault(); // Zuia submission ya haraka

                // Disable button na badilisha maandishi
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Please Wait...`;

                // Hakikisha form haina errors kabla ya kutuma
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false; // Warudishe button kama kuna errors
                    submitButton.innerHTML = "Approve";
                    return;
                }

                // Chelewesha submission kidogo ili button ibadilike kwanza
                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
@endsection
