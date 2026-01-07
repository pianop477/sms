<div class="table-responsive">
    <table class="table table-hover progress-table table-responsive-md">
        <thead>
            <tr>
                <th scope="col">Control #</th>
                <th scope="col">Student Name</th>
                <th scope="col">Level</th>
                <th scope="col">Phone</th>
                <th scope="col">Service</th>
                <th scope="col">Bill</th>
                <th scope="col">Paid</th>
                <th scope="col">Balance</th>
                <th scope="col">Status</th>
                <th scope="col">Expires At</th>
                <th scope="col" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bills as $row)
                @php
                    $billed = $row->amount ?? 0;
                    $paid = $row->total_paid ?? 0;
                    $balance = $billed - $paid;
                @endphp
                <tr>
                    <td>{{ strtoupper($row->control_number) }}</td>
                    <td>
                        {{ ucwords(strtolower($row->student_first_name ?? 'N/A')) }}
                        {{ ucwords(strtolower($row->student_middle_name ?? '')) }}
                        {{ ucwords(strtolower($row->student_last_name ?? '')) }}
                    </td>
                    <td>{{ strtoupper($row->class_code) }}</td>
                    <td>{{ $row->parent_phone }}</td>
                    <td>{{ ucwords(strtolower($row->service_name)) }}</td>
                    <td>{{ number_format($billed) }}</td>
                    <td>{{ number_format($paid) }}</td>
                    <td>{{ number_format($balance) }}</td>
                    <td>
                        @if ($row->status == 'active')
                            <span class="badge bg-primary text-white">{{ strtoupper($row->status) }}</span>
                        @elseif ($row->status == 'cancelled')
                            <span class="badge bg-warning text-primary">{{ strtoupper($row->status) }}</span>
                        @elseif ($row->status == 'expired')
                            <span class="badge bg-danger text-white">{{ strtoupper($row->status) }}</span>
                        @elseif ($row->status == 'full paid')
                            <span class="badge bg-success text-white">{{ strtoupper($row->status) }}</span>
                        @else
                            <span class="badge bg-info text-white">{{ strtoupper($row->status) }}</span>
                        @endif
                    </td>
                    <td>
                        @if ($row->due_date)
                            {{ $row->due_date }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <ul class="d-flex justify-content-center">
                                <li class="mr-3">
                                    <button class="btn btn-action btn-xs dropdown-toggle mr-2" type="button"
                                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-cog me-1"></i> Manage
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                        <li>
                                            <a class="dropdown-item text-sm" href="javascript:void(0)"
                                                onclick="viewBill('{{ Hashids::encode($row->id) }}')"
                                                title="view bill">
                                                <i class="fas fa-eye text-primary"></i>
                                                View Bill
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-sm" href="#" data-bs-toggle="modal"
                                                data-bs-target="#editBillModal"
                                                onclick="openEditBillModal('{{ Hashids::encode($row->id) }}')">
                                                <i class="fas fa-pencil text-info"></i>
                                                Edit Bill
                                            </a>
                                        </li>
                                        @if ($row->total_paid == 0 && $row->status == 'active')
                                            <li>
                                                <a href="#" class="dropdown-item cancel-btn"
                                                    data-id="{{ Hashids::encode($row->id) }}"
                                                    data-control="{{ strtoupper($row->control_number) }}"
                                                    data-service="{{ $row->service_name }}"
                                                    data-amount="{{ number_format($billed) }}" data-bs-toggle="modal"
                                                    data-bs-target="#cancelBillModal">
                                                    <i class="fas fa-ban text-danger"></i> Cancel Bill
                                                </a>
                                            </li>
                                        @endif
                                        @if ($row->status == 'active')
                                            <li>
                                                <form
                                                    action="{{ route('bills.resend', ['bill' => Hashids::encode($row->id)]) }}"
                                                    class="dropdown-item" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn p-1 m-0 text-sm"
                                                        onclick="return confirm('Are you sure you want to resend this bill?')">
                                                        <i class="fas fa-refresh text-success"></i>
                                                        Resend SMS
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                                @if ($row->status == 'active')
                                    <li>
                                        <button type="button" class="btn btn-info btn-pay btn-xs"
                                            data-bs-toggle="modal" data-bs-target="#addPaymentModal"
                                            data-bill-id="{{ Hashids::encode($row->id) }}"
                                            data-student-id="{{ $row->student_id }}"
                                            data-student-name="{{ ucwords(strtolower($row->student_first_name . ' ' . $row->student_middle_name . ' ' . $row->student_last_name)) }}"
                                            data-control-number="{{ strtoupper($row->control_number) }}"
                                            data-academic-year="{{ $row->academic_year }}"
                                            data-class="{{ strtoupper($row->class_code) }}"
                                            data-service="{{ ucwords(strtolower($row->service_name)) }}"
                                            data-billed="{{ $billed }}" data-paid="{{ $paid }}"
                                            data-balance="{{ $balance }}">
                                            <i class="fas fa-credit-card"></i> Pay
                                        </button>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center py-4">
                        @if (request('search'))
                            No records found for "{{ request('search') }}"
                        @else
                            No records found
                        @endif
                    </td>
                </tr>
            @endforelse

            <style>
                .btn-action {
                    background-color: #cccccc;
                    border: 1px solid #ced4da;
                    color: #212529;
                    font-weight: bold
                }

                .btn-pay {
                    background-color: #7fd89c border: 1px solid #7ef67c;
                    color: #212529;
                    font-weight: bold;
                }
            </style>
        </tbody>
    </table>
</div>
{{-- edit bill modal --}}
<div class="modal fade" id="editBillModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editBillForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark">
                        <i class="fas fa-edit"></i> Edit Bill
                    </h5>
                    <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal"><i
                            class="fas fa-close"></i></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit_bill_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Control Number</label>
                            <input type="text" class="form-control-custom" name="control_number"
                                id="edit_control_number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Student</label>
                            <select class="form-control-custom select2 text-capitalize" id="edit_student_id"
                                name="student_id" required>
                                <option value="">-- Select Student --</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Service</label>
                            <select class="form-control-custom text-capitalize" id="edit_service_id"
                                name="service_id" required>
                                <option value="">-- Select Service --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Billed Amount</label>
                            <input type="number" class="form-control-custom" id="edit_amount" name="amount">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Due Date</label>
                            <input type="date" class="form-control-custom" id="edit_due_date" name="due_date">
                        </div>
                        <div class="col-md-6">
                            <label>Status</label>
                            <select class="form-control-custom" id="edit_status" name="status">
                                <option value="active">Active</option>
                                <option value="full paid">Full Paid</option>
                                <option value="overpaid">Overpaid</option>
                                <option value="expired">Expired</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Academic Year</label>
                            <input type="text" class="form-control-custom" id="academic_year"
                                name="academic_year">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Bill
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- cancel bill modal --}}
<div class="modal fade" id="cancelBillModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Cancel Bill</h5>
                <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal"><i
                        class="fas fa-close"></i></button>
            </div>
            <form method="POST" id="cancelBillForm">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <input type="hidden" id="cancelBillId">
                    <div class="mb-3">
                        <label class="form-label">Cancel Reason <span class="text-danger">*</span></label>
                        <input type="text" name="reason" class="form-control-custom" placeholder="Enter cancel reason" required>
                    </div>
                    <div class="small text-muted" id="billPreview"></div>
                    <div class="mb-3">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to cancel this bill?')">Cancel Bill</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Bill Details Modal -->
<div class="modal fade" id="billDetailsModal" tabindex="-1" aria-labelledby="billDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md"> <!-- Changed to modal-md for smaller size -->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title mb-0" id="billDetailsModalLabel">
                    <i class="fas fa-file-invoice me-1"></i>
                    Bill Details
                </h6>
                <button type="button" class="btn btn-xs btn-danger btn-sm" data-bs-dismiss="modal"
                    aria-label="Close"><i class="fas fa-close"></i></button>
            </div>
            <div class="modal-body p-3">
                <div id="billDetailsContent">
                    <!-- Content will be loaded here via AJAX -->
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 mb-0 small">Loading bill details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
