@extends('SRTDashboard.frame')

@section('content')
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6f42c1;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }

        .header-title {
            font-weight: 600;
            margin: 0;
        }

        .list-group-item {
            border: 1px solid #e3e6f0;
            padding: 12px 20px;
            transition: all 0.3s;
        }

        .list-group-item:hover {
            background-color: #f8f9fc;
            border-color: var(--primary-color);
        }

        .list-group-item-action {
            color: var(--dark-color);
            font-weight: 500;
        }

        .badge-status {
            padding: 2px 4px;
            border-radius: 20px;
            font-weight: 600;
        }

        .btn-action {
            border-radius: 5px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 12px 15px;
        }

        .table td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .form-control-custom {
            border: 1px solid #d1d3e2;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control-custom:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .alert-custom {
            border-radius: 8px;
            padding: 15px 20px;
        }

        @media (max-width: 768px) {
            .table-responsive-md {
                overflow-x: auto;
            }

            .btn-action {
                margin-bottom: 5px;
            }

            .d-flex {
                flex-direction: column;
            }
        }
    </style>

    <div class="py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h4 class="text-primary fw-bold border-bottom pb-2">CONTRACT MANAGEMENT</h4>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Approved Contracts -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header-custom">
                        <h5 class="header-title text-white text-center"><i class="fas fa-file-contract me-2"></i> Approved Contract Group</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-danger fw-bold"><i class="fas fa-calendar me-1"></i> Select Year</label>
                        </div>

                        @if ($contractsByYear->isEmpty())
                        <div class="alert alert-danger alert-custom" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> No Records Available
                        </div>
                        @else
                        <div class="list-group">
                            @foreach ($contractsByYear as $year => $contract )
                                <a href="{{route('contract.by.months', ['year' => $year])}}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-angle-right me-2 text-primary"></i> {{$year}}</span>
                                    <span class="badge bg-primary rounded-pill text-white">{{$contract->count()}}</span>
                                </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Contract Requests -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header-custom">
                        <h5 class="header-title text-white text-center"><i class="fas fa-clock me-2"></i> New Contract Requests</h5>
                    </div>
                    <div class="card-body">
                        @if ($contractRequests->isEmpty())
                            <div class="alert alert-warning alert-custom text-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <span class="text-danger">There is no new contract request!</span>
                            </div>
                        @else
                        <div class="table-responsive-md">
                            <table class="table table-bordered table-hover table-responsive-md" id="myTable">
                                <thead>
                                    <tr class="text-center">
                                        <th>Applicant</th>
                                        <th>Applied At</th>
                                        <th>Updated At</th>
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
                                                <span class="badge-status bg-warning text-white text-capitalize">{{$row->status}}</span>
                                            </td>
                                            <td>
                                                <a href="{{route('contract.admin.preview', ['id' => Hashids::encode($row->id)])}}" target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-paperclip me-1"></i> View
                                                </a>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <!-- Approve Button -->
                                                    <button type="button" class="btn btn-success btn-action me-2 mr-2" data-bs-toggle="modal" data-bs-target="#approveModal{{$row->id}}">
                                                        <i class="fas fa-check me-1"></i> Approve
                                                    </button>

                                                    <!-- Reject Button -->
                                                    <button type="button" class="btn btn-danger btn-action" data-bs-toggle="modal" data-bs-target="#rejectModal{{$row->id}}">
                                                        <i class="fas fa-times me-1"></i> Reject
                                                    </button>
                                                </div>

                                                <!-- Approve Modal -->
                                                <div class="modal fade" id="approveModal{{$row->id}}" tabindex="-1" aria-labelledby="approveModalLabel{{$row->id}}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="approveModalLabel{{$row->id}}">
                                                                    <i class="fas fa-check-circle me-2"></i> Approve Contract Request
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row mb-4">
                                                                    <div class="col-md-12">
                                                                        <h6 class="text-center text-danger mb-3">
                                                                            <i class="fas fa-user-circle me-2"></i> Applicant Details
                                                                        </h6>
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <p class="mb-1"><strong>Name:</strong> {{ucwords(strtolower($row->first_name))}} {{ucwords(strtolower($row->last_name))}}</p>
                                                                                        <p class="mb-1"><strong>Gender:</strong> {{ucwords(strtolower($row->gender))}}</p>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <p class="mb-1"><strong>Member ID:</strong> {{strtoupper($row->member_id)}}</p>
                                                                                        <p class="mb-0"><strong>Contract Type:</strong> {{ucwords(strtolower($row->contract_type))}}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <hr>

                                                                <h6 class="text-center text-danger mb-3">
                                                                    <i class="fas fa-tasks me-2"></i> Complete Approval Actions
                                                                </h6>

                                                                <form action="{{route('contract.approval', ['id' => Hashids::encode($row->id)])}}" method="POST" novalidate class="needs-validation" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group mb-3">
                                                                                <label for="duration{{$row->id}}" class="form-label">Set Months</label>
                                                                                <input type="number" class="form-control form-control-custom" name="duration" id="duration{{$row->id}}" required value="{{old('duration')}}">
                                                                                @error('duration')
                                                                                    <div class="text-danger">{{$message}}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <div class="form-group mb-3">
                                                                                <label for="remark{{$row->id}}" class="form-label">Descriptions/Remarks</label>
                                                                                <textarea name="remark" id="remark{{$row->id}}" cols="30" rows="2" class="form-control form-control-custom" required>{{old('remark')}}</textarea>
                                                                                @error('remark')
                                                                                    <div class="text-danger">{{$message}}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" id="saveButton{{$row->id}}" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this request?')">
                                                                            <i class="fas fa-check me-1"></i> Approve
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{$row->id}}" tabindex="-1" aria-labelledby="rejectModalLabel{{$row->id}}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="rejectModalLabel{{$row->id}}">
                                                                    <i class="fas fa-times-circle me-2"></i> Reject Contract Request
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row mb-4">
                                                                    <div class="col-md-12">
                                                                        <h6 class="text-center text-danger mb-3">
                                                                            <i class="fas fa-user-circle me-2"></i> Applicant Details
                                                                        </h6>
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <p class="mb-1"><strong>Name:</strong> {{ucwords(strtolower($row->first_name))}} {{ucwords(strtolower($row->last_name))}}</p>
                                                                                        <p class="mb-1"><strong>Gender:</strong> {{ucwords(strtolower($row->gender))}}</p>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <p class="mb-1"><strong>Member ID:</strong> {{strtoupper($row->member_id)}}</p>
                                                                                        <p class="mb-0"><strong>Contract Type:</strong> {{ucwords(strtolower($row->contract_type))}}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <hr>

                                                                <h6 class="text-center text-danger mb-3">
                                                                    <i class="fas fa-tasks me-2"></i> Complete Rejection Actions
                                                                </h6>

                                                                <form action="{{route('contract.rejection', ['id' => Hashids::encode($row->id)])}}" method="POST" novalidate class="needs-validation" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group mb-3">
                                                                                <label for="rejectRemark{{$row->id}}" class="form-label">Reason for Rejection</label>
                                                                                <textarea name="remark" id="rejectRemark{{$row->id}}" cols="30" rows="3" class="form-control form-control-custom" required>{{old('remark')}}</textarea>
                                                                                @error('remark')
                                                                                    <div class="text-danger">{{$message}}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this request?')">
                                                                            <i class="fas fa-times me-1"></i> Reject
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Handle form submission with loading state
            const forms = document.querySelectorAll(".needs-validation");

            forms.forEach(function(form) {
                const submitButton = form.querySelector("button[type='submit']");

                if (form && submitButton) {
                    form.addEventListener("submit", function (event) {
                        event.preventDefault();

                        // Validate form
                        if (!form.checkValidity()) {
                            event.stopPropagation();
                            form.classList.add("was-validated");
                            return;
                        }

                        // Disable button and show loading state
                        submitButton.disabled = true;
                        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...`;

                        // Submit form
                        setTimeout(() => {
                            form.submit();
                        }, 500);
                    });
                }
            });
        });
    </script>
@endsection
