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
            overflow: auto;
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
    {{-- @dd($services) --}}
    <div class="py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-md-10">
                                <h4 class="header-title"> Services</h4>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                                        <i class="fas fa-plus-circle me-1"></i> New Service
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
                                            <th scope="col">Service Name</th>
                                            <th scope="col">Collection Account</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Payment Mode</th>
                                            <th scope="col">Time Duration</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($services->isEmpty())
                                            <tr>
                                                <td class="text-center text-danger" colspan="9">No Service records were found!</td>
                                            </tr>
                                        @else
                                            @foreach ($services as $row)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ucwords(strtolower($row->service_name))}}</td>
                                                    <td class="">{{$row->collection_account ?? 'N/A'}}</td>
                                                    <td title="{{number_format($row->amount)}}">{{number_format($row->amount)}}</td>
                                                    <td class="" style="font-weight: bold">
                                                        @if ($row->payment_mode == 'partial')
                                                            <span class="text-center text-primary">{{ucwords(strtolower($row->payment_mode))}}</span>
                                                        @else
                                                            <span class="text-center text-success">{{ucwords(strtolower($row->payment_mode))}}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $row->expiry_duration ?? 'N/A' }} Months
                                                    </td>
                                                    @if ($row->status == 'active')
                                                        <td class="text-center text-success fw-bold">
                                                            <span class="badge bg-success text-white">{{ucwords(strtolower($row->status))}}</span>
                                                        </td>
                                                    @else
                                                        <td class="text-center text-danger fw-bold">
                                                            <span class="text-white badge bg-danger">{{ucwords(strtolower($row->status))}}</span>
                                                        </td>
                                                    @endif
                                                    <td>
                                                        <ul class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-xs btn-outline-primary edit-transaction-btn mr-3"
                                                                title="Edit Service"
                                                                data-transaction-id="{{$row->id}}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editTransactionModal">
                                                                <i class="fas fa-pencil"></i>
                                                            </button>
                                                            @if ($row->status == 'active')
                                                                <li class="">
                                                                    <a href="#" data-bs-toggle="modal" title="cancel service" class="btn btn-xs btn-outline-warning" data-bs-target="#cancelModal{{$row->id}}">
                                                                        <i class="fas fa-ban"></i>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li class="">
                                                                    <form action="{{route('services.delete', ['id' => Hashids::encode($row->id)])}}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="btn btn btn-xs btn-outline-danger" title="Delete service" type="submit" onclick="return confirm('Are you sure you want to delete this service?')">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </td>
                                                </tr>
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
                                                <div class="modal fade" id="cancelModal{{$row->id}}" tabindex="-1" aria-labelledby="cancelModalLabel{{$row->id}}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="cancelModalLabel{{$row->id}}">
                                                                    Disable Service - {{strtoupper($row->service_name)}}
                                                                </h5>
                                                                <button type="button" class="btn btn-close btn-link" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close text-white"></i></button>
                                                            </div>
                                                            <form action="{{route('services.block', ['id' => Hashids::encode($row->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="cancelReason{{$row->id}}" class="form-label">Cancel Reason <span class="text-danger">*</span></label>
                                                                        <input type="text"
                                                                            class="form-control-custom"
                                                                            id="cancelReason{{$row->id}}"
                                                                            name="cancel_reason"
                                                                            {{-- rows="4" --}}
                                                                            placeholder="Cancel reason"
                                                                            required
                                                                        >
                                                                    </div>
                                                                    <input type="hidden" name="status" value="{{_('inactive')}}">
                                                                    <div class="mb-3">
                                                                        <small class="text-muted">
                                                                            <strong>Service Details:</strong><br>
                                                                            Reference ID: {{Hashids::encode($row->id)}}<br>
                                                                            Amount: {{number_format($row->amount)}}<br>
                                                                            Time Duration: {{$row->expiry_duration ?? 'N/A'}} Months
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to disable this service?')"> Cancel Service</button>
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
                    <h5 class="modal-title" id="addTeacherModalLabel"><i class="fas fa-coins"></i> Manage new Services</h5>
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('services.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Service Name<span class="text-danger">*</span></label>
                                <input type="text" name="service_name" required class="form-control-custom" placeholder="E.g. School fee, Transport, Accomodation" id="service_name" value="{{old('service_name')}}">
                                @error('service_name')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="payment" class="form-label">Payment Modality<span class="text-danger">*</span></label>
                                <select name="payment" id="payment" class="form-control-custom" required>
                                    <option value="">--Select--</option>
                                    <option value="partial">Partial</option>
                                    <option value="full pay">Full Pay</option>
                                </select>
                                @error('payment')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Amount (in. TZS)<span class="text-danger">*</span></label>
                                <input type="number" required name="amount" class="form-control-custom" id="amount" step="0.01" min="0" placeholder="0.00" value="{{old('amount')}}">
                                @error('amount')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Account Number</label>
                                <div class="input-group">
                                    <input type="text" name="account" class="form-control-custom" placeholder="Account No" value="{{old('account')}}">
                                </div>
                                @error('account')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Time duration (No. of months)<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" required name="duration" class="form-control-custom" placeholder="Months" value="{{old('duration')}}">
                                </div>
                                @error('duration')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="saveButton" class="btn btn-success">Submit</button>
                        </div>
                    </form>
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
                        Edit Service - <span id="modalReferenceNumber" class="text-uppercase fw-bold"></span>
                    </h5>
                    <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <form id="editTransactionForm" class="needs-validation" novalidate method="POST" enctype="multipart/form-data" data-no-preloader>
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editServiceId" name="transaction_id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editName" class="form-label">Service Name <span class="text-danger">*</span></label>
                                <input type="text" name="service_name" class="form-control-custom"
                                    id="editName"
                                    required>
                                <div class="invalid-feedback">Please Enter a valid service Name</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editAmount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="editAmount" class="form-control-custom">
                                <div class="invalid-feedback">Please Enter a Amount</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPaymentMode" class="form-label">Payment Modality <span class="text-danger">*</span></label>
                                <select name="payment_mode" id="editPaymentMode" class="form-control-custom">
                                    <option value="">--Select--</option>
                                    <option value="partial">Partial</option>
                                    <option value="full pay">Full Pay</option>
                                </select>
                                <div class="invalid-feedback">Please Select payment mode</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editDuration" class="form-label">Time duration <span class="text-danger">*</span></label>
                                    <input type="text" name="duration" id="editDuration" class="form-control-custom">
                                <div class="invalid-feedback">Please enter a valid time duration in months</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="editStatus" class="form-label">Account Number <span class="text-danger">*</span></label>
                                <input type="text" name="account" class="form-control-custom" id="editAccount">
                            </div>
                            <div class="col-md-6">
                                <label for="editStatus" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="editStatus" class="form-control-custom">
                                    <option value="">--Select--</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback">Please Select status</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" id="updateTransactionBtn">
                                <i class="fas fa-save me-2"></i> Update Service
                            </button>
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

        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-transaction-btn');
            const editModal = document.getElementById('editTransactionModal');
            const editForm = document.getElementById('editTransactionForm');
            const modalTitle = document.getElementById('modalReferenceNumber');

            // Handle edit button clicks
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const serviceId = this.getAttribute('data-transaction-id');
                    loadTransactionData(serviceId);
                });
            });

            // Load transaction data via AJAX
            function loadTransactionData(serviceId) {
                const updateBtn = document.getElementById('updateTransactionBtn');
                updateBtn.disabled = true;
                updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Loading...';

                fetch(`/Services/${serviceId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === true || data.success) {
                            populateEditForm(data.service);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to load service data',
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
                            text: 'Failed to load service data',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                    })
                    .finally(() => {
                        updateBtn.disabled = false;
                        updateBtn.innerHTML = '<i class="fas fa-save me-2"></i> Update Service';
                    });
            }

            // Populate form with transaction data
            function populateEditForm(service) {
                modalTitle.textContent = service.service_name.toUpperCase();
                document.getElementById('editServiceId').value = service.id;
                document.getElementById('editName').value = service.service_name;
                document.getElementById('editAmount').value = service.amount;
                document.getElementById('editAccount').value = service.collection_account;

                // Payment mode safe selector
                const paymentModeSelect = document.getElementById('editPaymentMode');
                paymentModeSelect.value = service.payment_mode;

                // fallback kama haijachagua
                if (!paymentModeSelect.value) {
                    const option = paymentModeSelect.querySelector(`option[value="${service.payment_mode}"]`);
                    if (option) option.selected = true;
                }

                //status safe selector
                const statusSelect = document.getElementById('editStatus');
                statusSelect.value = service.status;

                //fallback kama haijachagua
                if (!statusSelect.value) {
                    const option = statusSelect.querySelector(`option[value="${service.status}"]`);
                    if (option) option.selected = true;
                }

                document.getElementById('editDuration').value = service.expiry_duration;
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
                const serviceId = document.getElementById('editServiceId').value;

                fetch(`/Service/update/${serviceId}`, {
                    method: 'POST', // leave this as POST
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'PUT'
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
                                text: data.message || 'Service updated successfully',
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
                                text: data.message || 'Failed to update Service',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true
                            });
                            updateBtn.disabled = false;
                            updateBtn.innerHTML = '<i class="fas fa-save me-2"></i> Update Service';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong while updating service',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                        updateBtn.disabled = false;
                        updateBtn.innerHTML = '<i class="fas fa-save me-2"></i> Update Service';
                    });
            });

            // Reset form when modal is hidden
            editModal.addEventListener('hidden.bs.modal', function () {
                editForm.reset();
            });
        });
    </script>
@endsection
