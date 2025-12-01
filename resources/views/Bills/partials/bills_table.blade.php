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
                            <span class="badge bg-warning text-white">{{ strtoupper($row->status) }}</span>
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
                                @if ($row->total_paid == 0 && $row->status == 'active')
                                <li>
                                    <a class="dropdown-item text-sm" href="#" data-bs-toggle="modal" title="cancel bill" data-bs-target="#cancelModal{{$row->control_number}}">
                                        <i class="fas fa-ban text-danger"></i>
                                        Cancel Bill
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <form action="{{route('bills.resend', ['bill' => Hashids::encode($row->id)])}}" class="dropdown-item" method="POST">
                                        @csrf
                                        <button type="submit" class="btn p-1 m-0 text-sm" onclick="return confirm('Are you sure you want to resend this bill?')">
                                            <i class="fas fa-refresh text-success"></i>
                                            Resend SMS
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <!-- Cancel Modal for each row -->
                <div class="modal fade" id="cancelModal{{$row->control_number}}" tabindex="-1" aria-labelledby="cancelModalLabel{{$row->control_number}}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cancelModalLabel{{$row->control_number}}">
                                    Cancel Bill - {{strtoupper($row->control_number)}}
                                </h5>
                                <button type="button" class="btn btn-close btn-link" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close text-white"></i></button>
                            </div>
                            <form action="{{route('bills.cancel', ['bill' => Hashids::encode($row->id)])}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="cancelReason{{$row->control_number}}" class="form-label">Cancel Reason <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control-custom" id="cancelReason{{$row->control_number}}" name="reason" placeholder="Enter reason" required>
                                    </div>
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
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to cancel this bill?')"> Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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

<!-- Bill Details Modal -->
<div class="modal fade" id="billDetailsModal" tabindex="-1" aria-labelledby="billDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md"> <!-- Changed to modal-md for smaller size -->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title mb-0" id="billDetailsModalLabel">
                    <i class="fas fa-file-invoice me-1"></i>
                    Bill Details
                </h6>
                <button type="button" class="btn-close btn-danger btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close"></i></button>
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
                                    <span class="text-white badge bg-${response.bill.status === 'active' ? 'primary' : response.bill.status === 'cancelled' ? 'warning' : 'success'}">
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
