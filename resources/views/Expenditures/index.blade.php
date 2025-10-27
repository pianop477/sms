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

        .header-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .btn-action {
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .teacher-avatar {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid #e3e6f0;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-table {
            background-color: white;
        }

        .progress-table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .progress-table th {
            padding: 15px 10px;
            font-weight: 600;
            vertical-align: middle;
        }

        .progress-table td {
            padding: 15px 10px;
            vertical-align: middle;
        }

        .badge-role {
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-buttons a, .action-buttons button {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .dropdown-menu {
            border-radius: 5px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .btn-action {
                margin-bottom: 10px;
            }
        }
    </style>
    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-md-10">
                                <h4 class="header-title">registered Transactions on - {{\Carbon\Carbon::now()->format('d-m-Y')}}</h4>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                                        <i class="fas fa-exchange-alt me-1"></i> New Transaction
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Teachers Table -->
                        <div class="single-table">
                            <div class="table-responsive">
                                <table class="table table-hover progress-table table-responsive-md" id="myTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Reference #</th>
                                            <th scope="col">Category</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Payment Mode</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (empty($expenses))
                                            <tr>
                                                <td class="text-center text-danger" colspan="9">No transaction records were found for {{\Carbon\Carbon::now()->format('d-m-Y')}}!</td>
                                            </tr>
                                        @else
                                            @foreach ($expenses as $row)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td class="">{{strtoupper($row['reference_number'])}}</td>
                                                    <td>{{ucwords(strtolower($row['expense_type'] ?? 'N/A'))}}</td>
                                                    <td title="{{ $row['description'] }}">{{\Str::limit($row['description'] ?? N/A, 50)}}</td>
                                                    <td class="text-center">{{number_format($row['amount'])}}</td>
                                                    <td class="" style="font-weight: bold">
                                                        @if ($row['payment_mode'] == 'cash')
                                                            <span class="text-center text-success">{{ucwords(strtolower($row['payment_mode']))}}</span>
                                                        @elseif($row['payment_mode'] == 'mobile_money')
                                                            <span class="text-center text-primary">{{ucwords(strtolower($row['payment_mode']))}}</span>
                                                        @else
                                                            <span class="text-center text-danger">{{ucwords(strtolower($row['payment_mode']))}}</span>
                                                        @endif
                                                    </td>
                                                    @if ($row['status'] == 'active')
                                                        <td class="text-center text-success fw-bold">
                                                            <span class="badge bg-success text-white">{{ucwords(strtolower($row['status']))}}</span>
                                                        </td>
                                                    @elseif ($row['status'] == 'pending')
                                                        <td class="text-center text-secondary fw-bold">
                                                            <span class="badge bg-secondary text-white">{{ucwords(strtolower($row['status']))}}</span>
                                                        </td>
                                                    @else
                                                        <td class="text-center text-danger fw-bold">
                                                            <span class="text-white badge bg-danger">{{ucwords(strtolower($row['status']))}}</span>
                                                        </td>
                                                    @endif
                                                    <td>
                                                        <ul class="d-flex justify-content-center">
                                                            @if ($row['status'] == 'active')
                                                                <li class="mr-3">
                                                                    <a href="#" data-bs-toggle="modal" title="view bill" class="btn btn-xs btn-primary" data-bs-target="#viewModal{{$row['reference_number']}}"><i class="fas fa-eye"></i></a>
                                                                </li>
                                                                <li class="">
                                                                    <a href="#" data-bs-toggle="modal" title="cancel bill" class="btn btn-xs btn-warning" data-bs-target="#cancelModal{{$row['reference_number']}}">
                                                                        <i class="fas fa-ban"></i>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li class="mr-3">
                                                                    <a href="#" data-bs-toggle="modal" title="view bill" class="btn btn-xs btn-primary" data-bs-target="#viewModal{{$row['reference_number']}}"><i class="fas fa-eye"></i></a>
                                                                </li>
                                                                <li class="">
                                                                    <form action="{{route('expenditure.delete.bill', ['bill' => Hashids::encode($row['id'])])}}">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="btn btn btn-xs btn-danger" title="Delete bill" type="submit" onclick="return confirm('Are you sure you want to delete this transaction?')">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </td>
                                                </tr>
                                                {{-- view modal for each row --}}
                                                <!-- VIEW MODAL WITH TABS -->
                                                <div class="modal fade" id="viewModal{{$row['reference_number']}}" tabindex="-1" aria-labelledby="viewModalLabel{{$row['reference_number']}}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title" id="viewModalLabel{{$row['reference_number']}}">
                                                                    <i class="fas fa-receipt me-2"></i> Transaction Details
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close text-danger"></i></button>
                                                            </div>
                                                            <div class="modal-body p-0">
                                                                <!-- Header with Reference & Status -->
                                                                <div class="bg-light p-4 border-bottom">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <h6 class="text-muted mb-1">Reference Number</h6>
                                                                            <h4 class="text-primary fw-bold">{{strtoupper($row['reference_number'])}}</h4>
                                                                        </div>
                                                                        <div class="col-md-6 text-end">
                                                                            <h6 class="text-muted mb-1">Status</h6>
                                                                            @if ($row['status'] == 'active')
                                                                                <span class="badge bg-success text-white fs-6">{{ucwords($row['status'])}}</span>
                                                                            @elseif ($row['status'] == 'pending')
                                                                                <span class="badge bg-warning text-white fs-6">{{ucwords($row['status'])}}</span>
                                                                            @else
                                                                                <span class="badge bg-danger text-white fs-6">{{ucwords($row['status'])}}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Tabs Navigation -->
                                                                <ul class="nav nav-tabs nav-justified" id="transactionTabs{{$row['reference_number']}}" role="tablist">
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link active" id="details-tab{{$row['reference_number']}}" data-bs-toggle="tab"
                                                                                data-bs-target="#details{{$row['reference_number']}}" type="button" role="tab">
                                                                            <i class="fas fa-info-circle me-2"></i> Details
                                                                        </button>
                                                                    </li>
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="payment-tab{{$row['reference_number']}}" data-bs-toggle="tab"
                                                                                data-bs-target="#payment{{$row['reference_number']}}" type="button" role="tab">
                                                                            <i class="fas fa-credit-card me-2"></i> Payment
                                                                        </button>
                                                                    </li>
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="timeline-tab{{$row['reference_number']}}" data-bs-toggle="tab"
                                                                                data-bs-target="#timeline{{$row['reference_number']}}" type="button" role="tab">
                                                                            <i class="fas fa-history me-2"></i> Timeline
                                                                        </button>
                                                                    </li>
                                                                        <button class="nav-link" id="attachment-table{{$row['reference_number']}}" data-bs-toggle="tab"
                                                                            data-bs-target="#attachment{{$row['reference_number']}}" type="button" role="tab">
                                                                            <i class="fas fa-paperclip me-2"></i> Attachments
                                                                        </button>
                                                                    </li>
                                                                </ul>

                                                                <!-- Tabs Content -->
                                                                <div class="tab-content p-4" id="transactionTabsContent{{$row['reference_number']}}">
                                                                    <!-- Details Tab -->
                                                                    <div class="tab-pane fade show active" id="details{{$row['reference_number']}}" role="tabpanel">
                                                                        <div class="row">
                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="form-label text-muted small mb-1">Category</label>
                                                                                <p class="fw-semibold" style="font-weight: bold">{{ucwords(strtolower($row['expense_type'] ?? 'N/A'))}}</p>
                                                                            </div>
                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="form-label text-muted small mb-1">Amount</label>
                                                                                <p class="fw-bold fs-5 text-primary" style="font-weight: bold">TZS {{number_format($row['amount'])}}</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-6 mb-3">
                                                                                <label class="form-label text-muted small mb-1">Description</label>
                                                                                <p class="fw-semibold" style="font-weight: bold">{{$row['description'] ?? 'No description provided'}}</p>
                                                                            </div>
                                                                            <div class="col-6 mb-3">
                                                                                @php
                                                                                    $user = \App\Models\User::where('id', $row['user_id'])->first();
                                                                                @endphp
                                                                                <label class="form-label text-muted small mb-1">Issued By</label>
                                                                                <p class="fw-semibold" style="font-weight: bold">
                                                                                    @if ($user == null)
                                                                                        N/A
                                                                                    @else
                                                                                        {{ ucwords(strtolower($user->first_name. ' '. $user->last_name))}}
                                                                                    @endif
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="form-label text-muted small mb-1">Created Date</label>
                                                                                <p class="fw-semibold" style="font-weight: bold">
                                                                                    @if(isset($row['created_at']))
                                                                                        {{\Carbon\Carbon::parse($row['created_at'])->format('d-m-Y h:i A')}}
                                                                                    @else
                                                                                        N/A
                                                                                    @endif
                                                                                </p>
                                                                            </div>
                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="form-label text-muted small mb-1">Last Updated</label>
                                                                                <p class="fw-semibold" style="font-weight: bold">
                                                                                    @if(isset($row['updated_at']))
                                                                                        {{\Carbon\Carbon::parse($row['updated_at'])->format('d-m-Y h:i A')}}
                                                                                    @else
                                                                                        N/A
                                                                                    @endif
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Payment Tab -->
                                                                    <div class="tab-pane fade" id="payment{{$row['reference_number']}}" role="tabpanel">
                                                                        <div class="row">
                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="form-label text-muted small mb-1">Payment Mode</label>
                                                                                <p class="fw-semibold" style="font-weight: bold">
                                                                                    @if ($row['payment_mode'] == 'cash')
                                                                                        <span class="text-success fs-6">{{ucwords($row['payment_mode'])}}</span>
                                                                                    @elseif($row['payment_mode'] == 'mobile_money')
                                                                                        <span class="text-primary fs-6">{{ucwords($row['payment_mode'])}}</span>
                                                                                    @else
                                                                                        <span class="text-danger fs-6">{{ucwords($row['payment_mode'])}}</span>
                                                                                    @endif
                                                                                </p>
                                                                            </div>
                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="form-label text-muted small mb-1">Transaction Type</label>
                                                                                <p class="fw-semibold">
                                                                                    <span class="badge bg-info fs-6 text-white">Expense</span>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Timeline Tab -->
                                                                    <div class="tab-pane fade" id="timeline{{$row['reference_number']}}" role="tabpanel">
                                                                        <div class="timeline">
                                                                            @if(isset($row['created_at']))
                                                                            <div class="timeline-item">
                                                                                <div class="timeline-marker bg-success"></div>
                                                                                <div class="timeline-content">
                                                                                    <h6 class="fw-bold">Transaction Created</h6>
                                                                                    <p class="text-muted small mb-0" style="font-weight: bold">{{\Carbon\Carbon::parse($row['expense_date'])->format('d-m-Y')}}</p>
                                                                                </div>
                                                                            </div>
                                                                            @endif

                                                                            @if(isset($row['updated_at']) && $row['created_at'] != $row['updated_at'])
                                                                            <div class="timeline-item">
                                                                                <div class="timeline-marker bg-primary"></div>
                                                                                <div class="timeline-content">
                                                                                    <h6 class="fw-bold">Transaction Updated</h6>
                                                                                    <p class="text-muted small mb-0" style="font-weight: bold">{{\Carbon\Carbon::parse($row['updated_at'])->format('d-m-Y')}}</p>
                                                                                </div>
                                                                            </div>
                                                                            @endif

                                                                            @if($row['status'] == 'cancelled')
                                                                            <div class="timeline-item">
                                                                                <div class="timeline-marker bg-danger"></div>
                                                                                <div class="timeline-content">
                                                                                    <h6 class="fw-bold">Transaction Cancelled</h6>
                                                                                    <p class="text-muted small mb-0" style="font-weight: bold">{{\Carbon\Carbon::parse($row['updated_at'])->format('d-m-Y')}}</p>
                                                                                    @if(isset($row['cancel_reason']))
                                                                                    <p style="font-weight: bold">Reason: <span class="small text-danger">{{$row['cancel_reason']}}</span></p>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="attachment{{$row['reference_number']}}" role="tabpanel">
                                                                        <div class="timeline">
                                                                            @if(isset($row['attachment']))
                                                                                <div class="timeline-item">
                                                                                    <div class="timeline-marker bg-success"></div>
                                                                                    <div class="timeline-content">
                                                                                        <h6 class="fw-bold">Transaction Bill Receipt</h6>

                                                                                        @if(!empty($row['attachment_url']))
                                                                                            @php
                                                                                                $extension = strtolower(pathinfo($row['attachment'], PATHINFO_EXTENSION));
                                                                                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                                                                            @endphp

                                                                                            @if($isImage)
                                                                                                <!-- Display Image -->
                                                                                                <a href="{{ $row['attachment_url'] }}" target="_blank">
                                                                                                    <img src="{{ $row['attachment_url'] }}"
                                                                                                        alt="Receipt"
                                                                                                        style="max-width: 100px; max-height: 100px; border-radius: 5px;">
                                                                                                </a>
                                                                                            @elseif($extension === 'pdf')
                                                                                                <!-- Display PDF -->
                                                                                                <a href="{{ $row['attachment_url'] }}" target="_blank" class="btn btn-sm btn-primary">
                                                                                                    <i class="fas fa-file-pdf"></i> View PDF Receipt
                                                                                                </a>
                                                                                            @else
                                                                                                <!-- Other file types -->
                                                                                                <a href="{{ $row['attachment_url'] }}" target="_blank" class="btn btn-sm btn-secondary">
                                                                                                    <i class="fas fa-file"></i> View File
                                                                                                </a>
                                                                                            @endif
                                                                                        @else
                                                                                            <span class="text-muted">No attachment</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                                @else
                                                                                    <span class="text-muted">No attachment</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="fas fa-times me-2"></i> Close
                                                                </button>
                                                                @if($row['status'] == 'active')
                                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{$row['reference_number']}}">
                                                                    <i class="fas fa-ban me-2"></i> Cancel Transaction
                                                                </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- CSS for Timeline -->
                                                <style>
                                                    .timeline {
                                                        position: relative;
                                                        padding-left: 30px;
                                                    }
                                                    .timeline-item {
                                                        position: relative;
                                                        margin-bottom: 20px;
                                                    }
                                                    .timeline-marker {
                                                        position: absolute;
                                                        left: -30px;
                                                        top: 5px;
                                                        width: 12px;
                                                        height: 12px;
                                                        border-radius: 50%;
                                                        border: 3px solid #fff;
                                                        box-shadow: 0 0 0 3px currentColor;
                                                    }
                                                    .timeline-content {
                                                        background: #f8f9fa;
                                                        padding: 15px;
                                                        border-radius: 8px;
                                                        border-left: 4px solid currentColor;
                                                    }
                                                    .nav-tabs .nav-link {
                                                        color: #6c757d;
                                                        font-weight: 500;
                                                    }
                                                    .nav-tabs .nav-link.active {
                                                        color: #0d6efd;
                                                        font-weight: 600;
                                                    }
                                                </style>
                                                <!-- Cancel Modal for each row -->
                                                <div class="modal fade" id="cancelModal{{$row['reference_number']}}" tabindex="-1" aria-labelledby="cancelModalLabel{{$row['reference_number']}}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="cancelModalLabel{{$row['reference_number']}}">
                                                                    Cancel Transaction - {{strtoupper($row['reference_number'])}}
                                                                </h5>
                                                                <button type="button" class="btn btn-close btn-link" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close text-white"></i></button>
                                                            </div>
                                                            <form action="{{route('expenditure.cancel.bill', ['bill' => Hashids::encode($row['id'])])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="cancelReason{{$row['reference_number']}}" class="form-label">Cancel Reason <span class="text-danger">*</span></label>
                                                                        <input type="text"
                                                                            class="form-control-custom"
                                                                            id="cancelReason{{$row['reference_number']}}"
                                                                            name="cancel_reason"
                                                                            {{-- rows="4" --}}
                                                                            placeholder="Cancel reason"
                                                                            required
                                                                        >
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <small class="text-muted">
                                                                            <strong>Transaction Details:</strong><br>
                                                                            Reference: {{strtoupper($row['reference_number'])}}<br>
                                                                            Amount: {{number_format($row['amount'])}}<br>
                                                                            Category: {{ucwords(strtolower($row['expense_type'] ?? 'N/A'))}}
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to cancel this transaction?')"> Cancel Transaction</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTeacherModalLabel"><i class="fas fa-coins"></i> Manage new Transactions and Bills</h5>
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('expenditure.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Date</label>
                                <input type="date" name="date" min="{{\Carbon\Carbon::now()->subWeek(2)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->format('Y-m-d')}}" class="form-control-custom" id="date" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                @error('date')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Categories</label>
                                <select name="category" id="category" class="form-control-custom">
                                    <option value="">--Select category--</option>
                                    @if(empty($categories))
                                        <option value="">{{"No categories were found"}}</option>
                                    @else
                                        @foreach ($categories as $row )
                                            <option value="{{$row['id']}}">{{ucwords(strtolower($row['expense_type']))}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('category')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Service description</label>
                                <textarea type="text" required name="description" class="form-control-custom" id="description" placeholder="Enter service description e.g. umeme" value="{{old('description')}}"></textarea>
                                @error('description')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Amount (in. TZS)</label>
                                <div class="input-group">
                                    <input type="number" name="amount" class="form-control-custom" step="0.01" min="0" placeholder="0.00" value="{{old('amount')}}">
                                </div>
                                @error('amount')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Payment Mode</label>
                                <select name="payment" id="payment" class="form-control-custom">
                                    <option value="cash" selected>Cash</option>
                                    <option value="mobile_money">Mobile Payment</option>
                                    <option value="bank">Bank Transfer</option>
                                    {{-- <option value="other">Others</option> --}}
                                </select>
                                @error('payment')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="street" class="form-label">Receipt Attachment <span class="text-muted">(optional)</span></label>
                                <input type="file" name="attachment" class="form-control-custom" id="attachment" value="{{old('attachment')}}">
                                @error('attachment')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="saveButton" class="btn btn-success">Save Transaction</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (!form || !submitButton) return;

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...`;

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = "Save Transaction";
                    return;
                }

                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
@endsection
