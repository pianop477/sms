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
                            <span class="badge bg-info text-white">{{strtoupper($row->status)}}</span>
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
                            <button class="btn btn-action btn-xs dropdown-toggle mr-2" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cog me-1"></i> Manage
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                <li>
                                    <a class="dropdown-item text-sm" href="javascript:void(0)" onclick="viewBill('{{ Hashids::encode($row->id) }}')" title="view bill">
                                        <i class="fas fa-eye text-primary"></i>
                                        View Bill
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-sm"
                                        href="#"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editBillModal"
                                        onclick="openEditBillModal('{{ Hashids::encode($row->id) }}')">
                                            <i class="fas fa-pencil text-info"></i>
                                            Edit Bill
                                    </a>
                                </li>
                                @if ($row->total_paid == 0 && $row->status == 'active')
                                <li>
                                    <a href="#"
                                        class="dropdown-item cancel-btn"
                                        data-id="{{ Hashids::encode($row->id) }}"
                                        data-control="{{ strtoupper($row->control_number) }}"
                                        data-service="{{ $row->service_name }}"
                                        data-amount="{{ number_format($billed) }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#cancelBillModal">
                                        <i class="fas fa-ban text-danger"></i> Cancel Bill
                                    </a>
                                </li>
                                @endif
                                @if ($row->status == 'active')
                                    <li>
                                        <form action="{{route('bills.resend', ['bill' => Hashids::encode($row->id)])}}" class="dropdown-item" method="POST">
                                            @csrf
                                            <button type="submit" class="btn p-1 m-0 text-sm" onclick="return confirm('Are you sure you want to resend this bill?')">
                                                <i class="fas fa-refresh text-success"></i>
                                                Resend SMS
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center py-4">
                        @if(request('search'))
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
                        <i class="fas fa-edit"></i> Edit Bill - Control# {{strtoupper($row->control_number)}}
                    </h5>
                    <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal"><i class="fas fa-close"></i></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit_bill_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Control Number</label>
                            <input type="text" class="form-control-custom" name="control_number" id="edit_control_number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Student</label>
                            <select class="form-control-custom select2 text-capitalize" id="edit_student_id" name="student_id" required>
                                <option value="">-- Select Student --</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Service</label>
                            <select class="form-control-custom text-capitalize" id="edit_service_id" name="service_id" required>
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
                            <input type="text" class="form-control-custom" id="academic_year" name="academic_year">
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
        <h5 class="modal-title">Cancel Bill - Control# {{strtoupper($row->control_number)}}</h5>
        <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal"><i class="fas fa-close"></i></button>
      </div>
      <form method="POST" id="cancelBillForm">
        @csrf
        @method('PUT')

        <div class="modal-body">
          <input type="hidden" id="cancelBillId">

          <div class="mb-3">
            <label class="form-label">Cancel Reason <span class="text-danger">*</span></label>
            <input type="text" name="reason" class="form-control-custom" required>
          </div>

          <div class="small text-muted" id="billPreview"></div>
          <div class="mb-3">
            <small class="text-muted">
                <strong>Bill Details:</strong><br>
                    Reference: {{strtoupper($row->control_number)}}<br>
                    Amount: {{number_format($billed)}}<br>
                    Service Type: {{ucwords(strtolower($row->service_name ?? 'N/A'))}}
            </small>
        </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this bill?')">Cancel Bill</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Bill Details Modal -->
<div class="modal fade" id="billDetailsModal" tabindex="-1" aria-labelledby="billDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md"> <!-- Changed to modal-md for smaller size -->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title mb-0" id="billDetailsModalLabel">
                    <i class="fas fa-file-invoice me-1"></i>
                    Bill Details
                </h6>
                <button type="button" class="btn btn-xs btn-danger btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close"></i></button>
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
<style>
    /* Table styling for payment history */
    .table-sm td, .table-sm th {
        padding: 0.8rem 0.7rem;
        font-size: 0.85rem;
    }

    .table-borderless tbody tr:last-child {
        border-bottom: none !important;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    /* Scrollbar styling */
    [style*="max-height"]::-webkit-scrollbar {
        width: 4px;
    }

    [style*="max-height"]::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    [style*="max-height"]::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 2px;
    }
</style>
<script>
    $(document).on('click', '.cancel-btn', function () {
        const billId = $(this).data('id');

        $('#cancelBillForm').attr('action', `/Bills/cancel/${billId}`);
    });

    //edit bill function
    function openEditBillModal(hashId) {
        $('#editBillModal').modal('show');

        $.get(`/Bills/edit/${hashId}`, function (response) {
            $('#editBillForm').attr('action', `/Bills/update/${hashId}`);

            // Fill basic fields
            $('#edit_control_number').val(response.bill.control_number);
            $('#edit_amount').val(response.bill.amount);
            $('#academic_year').val(response.bill.academic_year);
            $('#edit_status').val(response.bill.status);

            // Handle due_date conversion
            if (response.bill.due_date) {
                // Convert "2026-10-30 21:34:51" to "2026-10-30"
                const dueDate = response.bill.due_date.split(' ')[0];
                $('#edit_due_date').val(dueDate);
            } else {
                $('#edit_due_date').val('');
            }

            // Populate students
            $('#edit_student_id').empty();
            response.students.forEach(student => {
                $('#edit_student_id').append(
                    `<option value="${student.id}">${student.first_name} ${student.last_name}</option>`
                );
            });
            $('#edit_student_id').val(response.bill.student_id).trigger('change');

            // Populate services
            $('#edit_service_id').empty();
            response.services.forEach(service => {
                $('#edit_service_id').append(
                    `<option value="${service.id}"
                            data-amount="${service.amount}"
                            data-duration="${service.expiry_duration}">
                        ${service.service_name}
                    </option>`
                );
            });
            $('#edit_service_id').val(response.bill.service_id);
        }).fail(function(error) {
            console.error('Error loading bill data:', error);
            alert('Error loading bill details. Please try again.');
        });
    }

    // Real-time service change - EDIT MODAL
    $(document).on('change', '#edit_service_id', function () {
        let option = $(this).find(':selected');
        const amount = option.data('amount');
        const duration = option.data('duration');

        // Set amount if exists
        if (amount) {
            $('#edit_amount').val(amount);
        }

        // Set due date based on duration (months from now)
        if (duration) {
            const today = new Date();
            today.setMonth(today.getMonth() + parseInt(duration));

            // Format as YYYY-MM-DD
            const formattedDate = today.toISOString().split('T')[0];
            $('#edit_due_date').val(formattedDate);
        } else {
            $('#edit_due_date').val('');
        }
    });

    // Select2
    $('.select2').select2({
        dropdownParent: $('#editBillModal')
    });

    function viewBill(billId) {
        console.log('Loading bill:', billId);

        // Show loading spinner
        $('#billDetailsContent').html(`
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0 small">Loading bill details...</p>
            </div>
        `);

        // Show modal
        $('#billDetailsModal').modal('show');

        // Fetch bill details
        $.ajax({
            url: `/Bills/view/${billId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Format dates
                    const formatDate = (dateString) => {
                        if (!dateString) return 'N/A';
                        return new Date(dateString).toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric'
                        });
                    };

                    $('#billDetailsContent').html(`
                        <!-- Student & Bill Info - Compact -->
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1 text-dark text-capitalize">${response.bill.student_first_name} ${response.bill.student_middle_name} ${response.bill.student_last_name} - ${response.bill.class_code.toUpperCase()}</h6>
                                    </div>
                                    <span class="
                                        badge
                                        bg-${response.bill.status === 'active'
                                            ? 'primary'
                                            : response.bill.status === 'cancelled'
                                            ? 'warning'
                                            : 'success'}
                                        ${response.bill.status === 'cancelled' ? 'text-primary' : 'text-white'}
                                    ">
                                        ${response.bill.status.toUpperCase()}
                                    </span>
                                </div>
                                <div class="text-muted mb-2">
                                    <div><strong>Control #:</strong> ${response.bill.control_number.toUpperCase()}</div>
                                    <div><strong>Academic Year:</strong> ${response.bill.academic_year}</div>
                                    <div><strong>Due Date:</strong> ${response.bill.due_date}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary - Compact -->
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="border rounded p-2 text-center bg-light">
                                    <div class="small text-muted">Billed</div>
                                    <div class="fw-bold text-primary">${new Intl.NumberFormat().format(response.summary.total_billed)}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center bg-light">
                                    <div class="small text-muted">Paid</div>
                                    <div class="fw-bold text-success">${new Intl.NumberFormat().format(response.summary.total_paid)}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center bg-light">
                                    <div class="small text-muted">Balance</div>
                                    <div class="fw-bold ${response.summary.balance > 0 ? 'text-danger' : 'text-success'}">
                                        ${new Intl.NumberFormat().format(response.summary.balance)}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment History - Table Style -->
                        <div class="border-top pt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 small text-muted" style="font-weight:bold"><i class="fas fa-history"></i> PAYMENT HISTORY</h6>
                                <span class="badge bg-secondary text-white">${response.summary.payment_count} payments</span>
                            </div>

                            ${response.payment_history.length > 0 ? `
                                <div style="max-height: 200px; overflow-y: auto;">
                                    <table class="table table-sm table-borderless mb-0">
                                        <thead>
                                            <tr class="small text-muted border-bottom">
                                                <th class="ps-2">Installment</th>
                                                <th class="ps-2">Type</th>
                                                <th>Date</th>
                                                <th>Mode</th>
                                                <th class="text-end pe-2">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${response.payment_history.map((payment, index) => `
                                                <tr class="small border-bottom">
                                                    <td class="ps-2">
                                                        <span class="">Instal#${payment.installment}</span>
                                                    </td>
                                                    <td class="ps-2">
                                                        ${response.bill.service_name}
                                                    </td>
                                                    <td>${new Date(payment.approved_at).toLocaleDateString('en-GB')}</td>
                                                    <td>
                                                        <span class="">${payment.payment_mode}</span>
                                                    </td>
                                                    <td class="text-end pe-2 fw-bold text-success">
                                                        ${new Intl.NumberFormat().format(payment.amount)}
                                                    </td>
                                                </tr>
                                            `).join('')}
                                            <!-- Total Row -->
                                            <tr class="small border-top fw-bold bg-light">
                                                <td class="ps-2" colspan="4" style="font-weight:bold">Closing Balance:</td>
                                                <td class="text-end pe-2 text-success" style="font-weight:bold">
                                                    ${new Intl.NumberFormat().format(response.summary.total_paid)}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            ` : `
                                <div class="text-center py-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        No payments recorded
                                    </small>
                                </div>
                            `}
                        </div>
                    `);
                } else {
                    $('#billDetailsContent').html(`
                        <div class="alert alert-danger py-2 mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <small>${response.message || 'Failed to load details'}</small>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                $('#billDetailsContent').html(`
                    <div class="alert alert-danger py-2 mb-0">
                        <i class="fas fa-times-circle me-1"></i>
                        <small>Error loading bill details</small>
                    </div>
                `);
            }
        });
    }

</script>
