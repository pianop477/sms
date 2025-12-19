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
            border-radius: 15px;
            box-shadow: 0 0.5rem 1.5rem 0 rgba(58, 59, 69, 0.2);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.75rem 2rem 0 rgba(58, 59, 69, 0.25);
        }

        .header-title {
            color: var(--primary-color);
            font-weight: 800;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 15px;
            margin-bottom: 25px;
            font-size: 1.75rem;
        }

        /* Statistics Cards Styling */
        .stat-card {
            border-radius: 15px;
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            min-height: 140px;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.175);
        }

        .stat-card .card-body {
            position: relative;
            z-index: 2;
            padding: 1.5rem;
        }

        .stat-card .card-icon {
            position: absolute;
            right: 20px;
            top: 20px;
            opacity: 0.2;
            font-size: 4rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover .card-icon {
            opacity: 0.3;
            transform: scale(1.1);
        }

        .stat-card .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .stat-card .card-value {
            font-size: 1.68rem;
            font-weight: 800;
            margin-bottom: 0;
        }

        .bg-success-custom {
            background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
        }

        .bg-primary-custom {
            background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
        }

        .bg-danger-custom {
            background: linear-gradient(135deg, #e74a3b 0%, #d52a1e 100%);
        }

        .bg-info-custom {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        }

        .bg-warning-custom {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }

        .bg-secondary-custom {
            background: linear-gradient(135deg, #858796 0%, #60616f 100%);
        }

        .btn-action {
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-export {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .table-responsive {
            border-radius: 15px;
            overflow: auto;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }

        .progress-table {
            background-color: white;
            border: none;
        }

        .progress-table thead {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2e59d9 100%);
            color: white;
        }

        .progress-table th {
            padding: 18px 12px;
            font-weight: 700;
            vertical-align: middle;
            border: none;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .progress-table td {
            padding: 15px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .progress-table tbody tr:hover td {
            background-color: #f8f9fc;
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
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .action-buttons a:hover, .action-buttons button:hover {
            transform: translateY(-2px);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2e59d9 100%);
            color: white;
            border-radius: 0;
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
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.25);
        }

        /* Status badges enhancement */
        .status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Back button styling */
        .btn-back {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
            color: white;
        }

        /* Report Form Styles */
        .report-form .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .report-form .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }

        .report-form .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .date-input-group {
            position: relative;
        }

        .date-input-group .form-control {
            padding-left: 40px;
        }

        .date-input-group::before {
            content: '\f073';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 5;
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

            .stat-card .card-value {
                font-size: 1.5rem;
            }

            .stat-card .card-icon {
                font-size: 2.5rem;
            }
        }

        /* Loading animation */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Custom scrollbar for table */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #2e59d9;
        }

        /* Timeline Styles */
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
        .error-message {
            font-size: 0.875rem;
            margin-top: 5px;
            display: block;
        }

        /* Loading animation improvements */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Success message styling */
        .alert-success {
            border-radius: 10px;
            border: none;
        }
    </style>

    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-8">
                                <h4 class="header-title">
                                    <i class="fas fa-receipt me-3"></i> All Expense Transactions for year {{$year}}
                                </h4>
                                <p class="text-muted mb-0">Overview Financial Report</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{route('expenditure.grouped.transactions')}}" class="btn btn-back">
                                        <i class="fas fa-arrow-circle-left me-2"></i>
                                        Back
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Bill Statistics --}}
                        <div class="row mb-5">
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="stat-card bg-success-custom text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Total Active Amount</div>
                                                @php
                                                    $totalActiveBills = collect($transactions)->where('status', 'active')->sum('amount');
                                                @endphp
                                                <div class="card-value">{{number_format($totalActiveBills) ?? 0}}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-check-circle card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="stat-card bg-danger-custom text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Total Cancelled Amount</div>
                                                @php
                                                    $totalCancelledBills = collect($transactions)->where('status', 'cancelled')->sum('amount');
                                                @endphp
                                                <div class="card-value">{{number_format($totalCancelledBills) ?? 0}}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-ban card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="stat-card bg-warning-custom text-white">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="card-title">Cancelled Count</div>
                                                @php
                                                    $totalCancelledCount = collect($transactions)->where('status', 'cancelled')->count();
                                                @endphp
                                                <div class="card-value">{{number_format($totalCancelledCount) ?? 0}}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-times-circle card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        {{-- End of Bill Statistics --}}

                        <!-- Transactions Table -->
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
                                            <th scope="col">Status</th>
                                            <th scope="col">Issued by</th>
                                            <th scope="col">Issued at</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (empty($transactions))
                                            <tr>
                                                <td class="text-center text-danger py-4" colspan="9">
                                                    <i class="fas fa-exclamation-triangle fa-2x mb-3 d-block"></i>
                                                    No expense bills records were found!
                                                </td>
                                            </tr>
                                        @else
                                            @foreach ($transactions as $row)
                                                <tr>
                                                    <td class="fw-bold">{{$loop->iteration}}</td>
                                                    <td class="fw-bold text-primary">{{strtoupper($row['reference_number'])}}</td>
                                                    <td>
                                                        <span class="badge bg-light text-dark border">
                                                            {{ucwords(strtolower($row['expense_type'] ?? 'N/A'))}}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="text-truncate" title="{{$row['description']}}">
                                                            {{Str::limit($row['description'], 50)}}
                                                        </span>
                                                    </td>
                                                    <td class="text-center fw-bold text-success">
                                                        {{number_format($row['amount'])}}
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($row['status'] == 'active')
                                                            <span class="badge bg-success text-white">
                                                                <i class="fas fa-check-circle me-1"></i>
                                                                {{ucwords($row['status'])}}
                                                            </span>
                                                        @elseif ($row['status'] == 'pending')
                                                            <span class="badge bg-warning text-white">
                                                                <i class="fas fa-clock me-1"></i>
                                                                {{ucwords($row['status'])}}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger text-white">
                                                                <i class="fas fa-times-circle me-1"></i>
                                                                {{ucwords($row['status'])}}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $user = \App\Models\User::where('id', $row['user_id'])->first();
                                                        @endphp
                                                        @if ($user == null)
                                                            <span class="text-muted">N/A</span>
                                                        @else
                                                            <div class="d-flex align-items-center">
                                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-1" style="width: 35px; height: 35px;">
                                                                    <span class="text-white fw-bold small">
                                                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                                                    </span>
                                                                </div>
                                                                <span class="ms-2 fw-semibold">
                                                                    {{ ucwords(strtolower($user->first_name. '. '. $user->last_name[0]))}}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-muted">
                                                        <small>
                                                            <i class="fas fa-calendar me-1"></i>
                                                            {{\Carbon\Carbon::parse($row['expense_date'])->format('d-m-Y')}}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <button type="button"
                                                                class="btn btn-xs btn-outline-primary edit-transaction-btn"
                                                                title="Edit Bill"
                                                                data-transaction-id="{{$row['id']}}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editTransactionModal">
                                                                <i class="fas fa-pencil"></i>
                                                            </button>
                                                            @if ($row['status'] == 'active')
                                                                <a href="#" title="View Bill" data-bs-toggle="modal" class="btn btn-sm btn-outline-info" data-bs-target="#viewModal{{$row['reference_number']}}">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="#" title="Cancel Bill" data-bs-toggle="modal" class="btn btn-sm btn-outline-warning" data-bs-target="#cancelModal{{$row['reference_number']}}">
                                                                    <i class="fas fa-ban"></i>
                                                                </a>
                                                            @else
                                                                <a href="#" title="View Bill" data-bs-toggle="modal" class="btn btn-sm btn-outline-primary" data-bs-target="#viewModal{{$row['reference_number']}}">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <form action="{{route('expenditure.delete.bill', ['bill' => Hashids::encode($row['id'])])}}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete Bill" onclick="return confirm('Are you sure you want to delete this bill?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
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
    {{-- edit transaction modal --}}
    <div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editTransactionModalLabel">
                        <i class="fas fa-pencil me-2"></i>
                        Edit Transaction - <span id="modalReferenceNumber" class="text-uppercase fw-bold"></span>
                    </h5>
                    <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <form id="editTransactionForm" class="needs-validation" novalidate method="POST" enctype="multipart/form-data" data-no-preloader>
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editTransactionId" name="transaction_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editDate" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control-custom"
                                    id="editDate"
                                    min="{{\Carbon\Carbon::now()->subYears(1)->format('Y-m-d')}}"
                                    max="{{\Carbon\Carbon::now()->format('Y-m-d')}}"
                                    required>
                                <div class="invalid-feedback">Please select a valid date</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editCategory" class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category" id="editCategory" class="form-control-custom" required>
                                    <option value="">--Select category--</option>
                                    @if(empty($categories))
                                        <option value="">No categories found</option>
                                    @else
                                        @foreach ($categories as $category)
                                            <option value="{{$category['id']}}">{{ucwords(strtolower($category['expense_type']))}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback">Please select a category</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editDescription" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control-custom"
                                        id="editDescription"
                                        rows="3"
                                        placeholder="Enter transaction description"
                                        required></textarea>
                                <div class="invalid-feedback">Please enter a description</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editAmount" class="form-label">Amount (in TZS) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">TZS</span>
                                    <input type="number" name="amount" class="form-control-custom"
                                        id="editAmount"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        required>
                                </div>
                                <div class="invalid-feedback">Please enter a valid amount</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="editPayment" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                <select name="payment" id="editPayment" class="form-control-custom" required>
                                    <option value="cash">Cash</option>
                                    <option value="mobile_money">Mobile Payment</option>
                                    <option value="bank">Bank Transfer</option>
                                </select>
                                <div class="invalid-feedback">Please select payment mode</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editStatus" class="form-label">Status <span class="text-danger"></span></label>
                                <select name="status" id="editStatus" class="form-control-custom" required>
                                    <option value="active">Active</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <div class="invalid-feedback">Please select payment mode</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editAttachment" class="form-label">
                                    Receipt Attachment
                                    <span class="text-muted small">(optional - leave empty to keep current)</span>
                                </label>
                                <input type="file" name="attachment" class="form-control-custom"
                                    id="editAttachment"
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                <div class="form-text">Max file size: 5MB. Allowed: JPG, PNG, PDF, DOC</div>
                            </div>
                        </div>

                        <!-- Current Attachment Preview -->
                        <div class="row" id="currentAttachmentSection" style="display: none;">
                            <div class="col-12 mb-3">
                                <label class="form-label">Current Attachment:</label>
                                <div id="currentAttachmentPreview" class="border rounded p-3 bg-light">
                                    <!-- Attachment preview will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" id="updateTransactionBtn">
                                <i class="fas fa-save me-2"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
        @if (!empty($transactions))
            @foreach ($transactions as $row)
                <!-- View Modal -->
                <div class="modal fade" id="viewModal{{$row['reference_number']}}" tabindex="-1" aria-labelledby="viewModalLabel{{$row['reference_number']}}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="viewModalLabel{{$row['reference_number']}}">
                                    <i class="fas fa-receipt me-2"></i> Expense Details
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
                                    <li class="nav-item" role="presentation">
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
                                                <p class="fw-semibold">{{ucwords(strtolower($row['expense_type'] ?? 'N/A'))}}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted small mb-1">Amount</label>
                                                <p class="fw-bold fs-5 text-primary">TZS {{number_format($row['amount'])}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label text-muted small mb-1">Description</label>
                                                <p class="fw-semibold">{{$row['description'] ?? 'No description provided'}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                @php
                                                    $user = \App\Models\User::where('id', $row['user_id'])->first();
                                                @endphp
                                                <label class="form-label text-muted small mb-1">Issued By</label>
                                                <p class="fw-semibold">
                                                    @if ($user == null)
                                                        N/A
                                                    @else
                                                        {{ ucwords(strtolower($user->first_name. ' '. $user->last_name))}}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted small mb-1">Created Date</label>
                                                <p class="fw-semibold">
                                                    @if(isset($row['created_at']))
                                                        {{\Carbon\Carbon::parse($row['expense_date'])->format('d-m-Y')}}
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
                                                <p class="fw-semibold">
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
                                                <label class="form-label text-muted small mb-1">Bill Type</label>
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
                                                    <h6 class="fw-bold">Bill Created</h6>
                                                    <p class="text-muted small mb-0">{{\Carbon\Carbon::parse($row['expense_date'])->format('M d, Y h:i A')}}</p>
                                                </div>
                                            </div>
                                            @endif

                                            @if(isset($row['updated_at']) && $row['created_at'] != $row['updated_at'])
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-primary"></div>
                                                <div class="timeline-content">
                                                    <h6 class="fw-bold">Bill Updated</h6>
                                                    <p class="text-muted small mb-0">{{\Carbon\Carbon::parse($row['updated_at'])->format('M d, Y h:i A')}}</p>
                                                </div>
                                            </div>
                                            @endif

                                            @if($row['status'] == 'cancelled')
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-danger"></div>
                                                <div class="timeline-content">
                                                    <h6 class="fw-bold">Bill Cancelled</h6>
                                                    <p class="text-muted small mb-0">{{\Carbon\Carbon::parse($row['updated_at'])->format('M d, Y h:i A')}}</p>
                                                    @if(isset($row['cancel_reason']))
                                                    <p>Reason: <span class="small text-danger">{{$row['cancel_reason']}}</span></p>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- attachment Tab -->
                                    <div class="tab-pane fade" id="attachment{{$row['reference_number']}}" role="tabpanel">
                                        <div class="timeline">
                                            @if(isset($row['attachment']))
                                                <div class="timeline-item">
                                                    <div class="timeline-marker bg-success"></div>
                                                    <div class="timeline-content">
                                                        <h6 class="fw-bold">Expense Bill Receipt</h6>

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
                                    <i class="fas fa-ban me-2"></i> Cancel
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cancel Modal -->
                <div class="modal fade" id="cancelModal{{$row['reference_number']}}" tabindex="-1" aria-labelledby="cancelModalLabel{{$row['reference_number']}}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title" id="cancelModalLabel{{$row['reference_number']}}">
                                    Cancel Bill - {{strtoupper($row['reference_number'])}}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close text-danger"></i></button>
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
                                            placeholder="Enter cancel reason"
                                            required
                                        >
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Expense Details:</strong><br>
                                            Reference: {{strtoupper($row['reference_number'])}}<br>
                                            Amount: {{number_format($row['amount'])}}<br>
                                            Category: {{ucwords(strtolower($row['expense_type'] ?? 'N/A'))}}
                                        </small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this bill?')">
                                        <i class="fas fa-ban me-2"></i> Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Export Report Form Handling
            const exportForm = document.getElementById('exportReportForm');
            const generateBtn = document.getElementById('generateReportBtn');

            if (exportForm && generateBtn) {
                exportForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Prevent the global preloader from showing
                    e.stopImmediatePropagation();

                    // Clear previous errors
                    clearErrors();

                    // Validate dates
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;
                    const exportFormat = document.getElementById('export_format').value;

                    if (!startDate || !endDate || !exportFormat) {
                        showError('Please fill all required fields');
                        return;
                    }

                    if (startDate > endDate) {
                        showError('start_date', 'Start date cannot be after end date.');
                        return;
                    }

                    // Show loading state
                    const originalText = generateBtn.innerHTML;
                    generateBtn.disabled = true;
                    generateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Generating...';

                    // Prepare form data
                    const formData = new FormData(this);

                    // Send AJAX request
                    fetch('{{ route("expenditure.export.custom.report") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            // If response is not OK, check if it's a validation error
                            if (response.status === 422) {
                                return response.json().then(data => {
                                    throw new Error(data.message || 'Validation failed');
                                });
                            }
                            throw new Error('Network response was not ok');
                        }

                        // Check content type to see if it's a file or JSON error
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json().then(data => {
                                if (data.error) {
                                    throw new Error(data.error);
                                }
                                return response.blob();
                            });
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        // Check if blob is actually an error message
                        if (blob instanceof Blob) {
                            // Create blob URL
                            const url = window.URL.createObjectURL(blob);

                            // Open in new tab
                            const newTab = window.open(url, '_blank');

                            // Focus on new tab
                            if (newTab) {
                                newTab.focus();
                            } else {
                                // Fallback: download if popup blocked
                                const a = document.createElement('a');
                                a.href = url;
                                a.download = `transactions_report.${exportFormat}`;
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                            }

                            // Clean up
                            setTimeout(() => window.URL.revokeObjectURL(url), 100);

                            // RESET FORM FIELDS ONLY (without closing modal)
                            exportForm.reset();
                            clearErrors();
                        }

                        // Reset button state
                        resetButtonState(generateBtn, originalText);
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        // Show error message in modal without opening new tab
                        showErrorInModal(error.message || 'An error occurred while generating the report. Please try again.');
                        resetButtonState(generateBtn, originalText);
                    });
                });

                // Reset form when modal is closed manually by user
                const exportModal = document.getElementById('exportReportModal');
                if (exportModal) {
                    exportModal.addEventListener('hidden.bs.modal', function () {
                        exportForm.reset();
                        clearErrors();
                        resetButtonState(generateBtn, '<i class="fas fa-download me-2"></i> Generate Report');

                        // Clear any error alerts
                        const existingAlert = document.getElementById('exportErrorAlert');
                        if (existingAlert) {
                            existingAlert.remove();
                        }
                    });
                }
            }

            // Helper functions
            function clearErrors() {
                document.querySelectorAll('.error-message').forEach(el => {
                    el.textContent = '';
                });
            }

            function showError(field, message) {
                if (typeof field === 'object') {
                    // Global error
                    alert(message);
                } else {
                    // Field-specific error
                    const errorElement = document.getElementById(`${field}_error`);
                    if (errorElement) {
                        errorElement.textContent = message;
                    }
                }
            }

            function showErrorInModal(message) {
                // Remove any existing error alerts
                const existingAlert = document.getElementById('exportErrorAlert');
                if (existingAlert) {
                    existingAlert.remove();
                }

                // Create error alert
                const errorAlert = document.createElement('div');
                errorAlert.id = 'exportErrorAlert';
                errorAlert.className = 'alert alert-danger alert-dismissible fade show mt-3';
                errorAlert.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                // Insert after the form
                const modalBody = document.querySelector('#exportReportModal .modal-body');
                modalBody.appendChild(errorAlert);
            }

            function resetButtonState(button, originalText) {
                button.disabled = false;
                button.innerHTML = originalText;
            }

            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

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
                    submitButton.innerHTML = "Save";
                    return;
                }

                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-transaction-btn');
            const editModal = document.getElementById('editTransactionModal');
            const editForm = document.getElementById('editTransactionForm');
            const modalTitle = document.getElementById('modalReferenceNumber');

            // Handle edit button clicks
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const transactionId = this.getAttribute('data-transaction-id');
                    loadTransactionData(transactionId);
                });
            });

            // Load transaction data via AJAX
            function loadTransactionData(transactionId) {
                const updateBtn = document.getElementById('updateTransactionBtn');
                updateBtn.disabled = true;
                updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Loading...';

                fetch(`/expenditure/get-transaction/${transactionId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.status || data.success) {
                            populateEditForm(data.transaction);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to load expense data',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load expense data',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                    })
                    .finally(() => {
                        updateBtn.disabled = false;
                        updateBtn.innerHTML = '<i class="fas fa-save me-2"></i> Update';
                    });
            }

            // Populate form with transaction data
            function populateEditForm(transaction) {
                modalTitle.textContent = transaction.reference_number;
                document.getElementById('editTransactionId').value = transaction.id;
                document.getElementById('editDate').value = transaction.expense_date;
                document.getElementById('editCategory').value = transaction.expense_type_id || transaction.category_id;
                document.getElementById('editDescription').value = transaction.description || '';
                document.getElementById('editAmount').value = transaction.amount || '';
                document.getElementById('editPayment').value = transaction.payment_mode || 'cash';
                document.getElementById('editStatus').value = transaction.status || 'active';

                const attachmentSection = document.getElementById('currentAttachmentSection');
                const attachmentPreview = document.getElementById('currentAttachmentPreview');

                if (transaction.attachment_url && transaction.attachment) {
                    const extension = transaction.attachment.split('.').pop().toLowerCase();
                    const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(extension);
                    const isPDF = extension === 'pdf';

                    let previewHtml = '';
                    if (isImage) {
                        previewHtml = `
                            <div class="d-flex align-items-center">
                                <img src="${transaction.attachment_url}"
                                    alt="Current receipt"
                                    style="max-width: 100px; max-height: 100px; border-radius: 5px;"
                                    class="me-3">
                                <div>
                                    <strong>Current Attachment:</strong><br>
                                    <a href="${transaction.attachment_url}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </div>
                            </div>
                        `;
                    } else if (isPDF) {
                        previewHtml = `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf text-danger fa-3x me-3"></i>
                                <div>
                                    <strong>Current Attachment:</strong><br>
                                    <a href="${transaction.attachment_url}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="fas fa-eye me-1"></i> View PDF
                                    </a>
                                </div>
                            </div>
                        `;
                    } else {
                        previewHtml = `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file text-secondary fa-3x me-3"></i>
                                <div>
                                    <strong>Current Attachment:</strong><br>
                                    <a href="${transaction.attachment_url}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        `;
                    }

                    attachmentPreview.innerHTML = previewHtml;
                    attachmentSection.style.display = 'block';
                } else {
                    attachmentSection.style.display = 'none';
                }
            }

            // Handle update form submission
            editForm.addEventListener('submit', function (event) {
                event.preventDefault();

                if (!editForm.checkValidity()) {
                    editForm.reportValidity();
                    return;
                }

                const updateBtn = document.getElementById('updateTransactionBtn');
                updateBtn.disabled = true;
                updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Updating...';

                const formData = new FormData(editForm);
                const transactionId = document.getElementById('editTransactionId').value;

                fetch(`/expenditure/update-transaction/${transactionId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === true) {
                            // Show success message with SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message || 'Expense bill updated successfully',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                            }).then(() => {
                                editForm.reset();

                                // reload full window
                                window.location.reload();
                            });

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to update expense bill',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true
                            });
                            updateBtn.disabled = false;
                            updateBtn.innerHTML = '<i class="fas fa-save me-2"></i> Update';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong while updating',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                        updateBtn.disabled = false;
                        updateBtn.innerHTML = '<i class="fas fa-save me-2"></i> Update';
                    });
            });

            // Reset form when modal is hidden
            editModal.addEventListener('hidden.bs.modal', function () {
                editForm.reset();
                document.getElementById('currentAttachmentSection').style.display = 'none';
            });
        });
    </script>
@endsection
