@extends('SRTDashboard.frame')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<style>
    :root {
        --primary: #4e54c8;
        --secondary: #8f94fb;
        --info: #17a2b8;
        --warning: #ffc107;
        --danger: #dc3545;
        --success: #28a745;
        --light: #f8f9fa;
        --dark: #343a40;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        /* min-height: 100vh; */
        overflow-x: hidden;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        overflow-x: hidden;
        margin-top: 30px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        position: relative;
    }

    .card-header-custom {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 25px 30px;
        position: relative;
        overflow: hidden;
        /* z-index: 100; */
    }

    .card-header-custom::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
        transform: rotate(30deg);
    }

    .header-title {
        font-weight: 700;
        margin: 0;
        position: relative;
        font-size: 24px;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border: none;
        border-radius: 50px;
        padding: 12px 25px;
        font-weight: 600;
        color: white;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 10;
        cursor: pointer;
    }

    .btn-primary-custom:hover {
        background: linear-gradient(135deg, #3f43b5 0%, #7a80f9 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
        color: white;
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 20px;
        overflow-x: hidden;
        border: none;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .modal-header-custom {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 20px 25px;
    }

    .modal-title {
        font-weight: 700;
        margin: 0;
    }

    .close {
        color: white;
        opacity: 0.8;
    }

    .close:hover {
        color: white;
        opacity: 1;
    }

    .form-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }

    .form-label {
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .required-star {
        color: var(--danger);
    }

    .form-control-custom {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 16px;
        transition: all 0.3s;
        background-color: white;
    }

    .form-control-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
    }

    /* Select2 Custom Styling */
    .select2-container .select2-selection--multiple {
        border: 2px solid #e9ecef !important;
        border-radius: 10px !important;
        min-height: 45px !important;
        padding: 5px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%) !important;
        border: none !important;
        border-radius: 20px !important;
        color: white !important;
        padding: 4px 12px !important;
        margin: 3px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white !important;
        margin-right: 5px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ffd54f !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25) !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%) !important;
        color: white !important;
    }

    /* Table Styles */
    .table-container {
        background: white;
        border-radius: 15px;
        overflow-x: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        width: 100%;
        overflow-x: auto;
    }

    .table-custom {
        margin-bottom: 0;
        width: 100%;
        min-width: 600px;
    }

    .table-custom thead th {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        padding: 15px 12px;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-custom tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        border-color: #e9ecef;
    }

    .table-custom tbody tr:nth-child(even) {
        background-color: rgba(78, 84, 200, 0.05);
    }

    .table-custom tbody tr:hover {
        background-color: rgba(78, 84, 200, 0.1);
    }

    /* Badge Styles */
    .badge-primary-custom {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border-radius: 50px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 12px;
    }

    .badge-info-custom {
        background: linear-gradient(135deg, var(--info) 0%, #17a2b8 100%);
        color: white;
        border-radius: 50px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 12px;
    }

    .badge-success-custom {
        background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
        color: white;
        border-radius: 50px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 12px;
    }

    .badge-warning-custom {
        background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
        color: #856404;
        border-radius: 50px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 12px;
    }

    .badge-secondary-custom {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        border-radius: 50px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 12px;
    }

    .badge-danger-custom {
        background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
        color: white;
        border-radius: 50px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 12px;
    }

    /* Action Buttons */
    .action-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        justify-content: center;
        gap: 10px;
        position: relative;
        /* z-index: 10; */
    }

    .action-list li {
        display: inline-block;
    }

    .btn-info-custom {
        background: linear-gradient(135deg, var(--info) 0%, #17a2b8 100%);
        border: none;
        border-radius: 50px;
        padding: 8px 16px;
        font-weight: 600;
        color: white;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        position: relative;
        /* z-index: 10; */
        cursor: pointer;
    }

    .btn-info-custom:hover {
        background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(23, 162, 184, 0.3);
        color: white;
    }

    .btn-success-custom {
        background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
        border: none;
        border-radius: 50px;
        padding: 8px 16px;
        font-weight: 600;
        color: white;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        position: relative;
        /* z-index: 10; */
        cursor: pointer;
    }

    .btn-success-custom:hover {
        background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        color: white;
    }

    .btn-danger-custom {
        background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
        border: none;
        border-radius: 50px;
        padding: 8px 16px;
        font-weight: 600;
        color: white;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        position: relative;
        /* z-index: 10; */
        cursor: pointer;
    }

    .btn-danger-custom:hover {
        background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
        color: white;
    }

    .floating-icons {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 60px;
        opacity: 0.1;
        color: white;
        z-index: 0;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state p {
        font-size: 18px;
        margin: 0;
        font-weight: 500;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 16px;
        margin-right: 10px;
    }

    .user-info {
        display: flex;
        align-items: center;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 10px;
        }

        .header-title {
            font-size: 20px;
        }

        .table-responsive {
            font-size: 14px;
        }

        .action-list {
            flex-direction: column;
            gap: 10px;
        }

        .btn-info-custom, .btn-success-custom, .btn-danger-custom {
            width: 100%;
            justify-content: center;
        }

        .user-info {
            flex-direction: column;
            align-items: flex-start;
        }

        .user-avatar {
            margin-right: 0;
            margin-bottom: 8px;
        }
    }
</style>

<div class="">
    <!-- Assign Roster Button -->
    <div class="text-right mb-4">
        <button type="button" class="btn btn-primary-custom" data-toggle="modal" data-target="#assignRosterModal">
            <i class="fas fa-plus me-2"></i> Assign Duty Roster
        </button>
    </div>

    <!-- Main Content Card -->
    <div class="glass-card">
        <div class="card-header-custom">
            <div class="row align-items-center">
                <div class="col-md-12 text-center">
                    <h4 class="header-title text-white">
                        <i class="fas fa-calendar-check me-2"></i> Duty Roster Management
                    </h4>
                    <p class="mb-0 text-white"> Manage teacher duty assignments and schedules</p>
                </div>
            </div>
            <i class="fas fa-clipboard-list floating-icons"></i>
        </div>

        <div class="card-body">
            @if($rosters->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p> No duty rosters assigned yet</p>
                </div>
            @else
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-custom table-responsive-md" id="myTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Roster ID</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Issued by</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rosters as $key => $roster)
                                    @php
                                        $firstRoster = $roster->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-bold text-uppercase">{{ $firstRoster->roster_id }}</td>
                                        <td>{{ $firstRoster->start_date ?? "N/A" }}</td>
                                        <td>{{ $firstRoster->end_date ?? "N/A" }}</td>
                                        <td class="text-capitalize">{{ $firstRoster->created_by ?? 'N/A' }}</td>
                                        <td>
                                            @if ($firstRoster->status == 'active')
                                                <span class="badge-warning-custom">
                                                    <i class="fas fa-play-circle me-1"></i> Active
                                                </span>
                                            @elseif ($firstRoster->status == 'pending')
                                                <span class="badge-secondary-custom">
                                                    <i class="fas fa-clock me-1"></i> Pending
                                                </span>
                                            @elseif ($firstRoster->status == 'completed')
                                                <span class="badge-success-custom">
                                                    <i class="fas fa-check-circle me-1"></i> Completed
                                                </span>
                                            @elseif ($firstRoster->status == 'cancelled')
                                                <span class="badge-danger-custom">
                                                    <i class="fas fa-times-circle me-1"></i> Cancelled
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <ul class="action-list">
                                                <li>
                                                    <button type="button" class="btn btn-info-custom" data-toggle="modal" data-target="#rosterModal{{ $key }}">
                                                        <i class="fas fa-eye me-1"></i> View
                                                    </button>
                                                </li>
                                                @if ($firstRoster->status == "pending")
                                                    <li>
                                                        <form method="POST" action="{{ route('tod.roster.activate', $firstRoster->id) }}" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button class="btn btn-success-custom" type="submit" title="Activate" onclick="return confirm('Activate this roster?');">
                                                                <i class="fas fa-check me-1"></i> Activate
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                <li>
                                                    <form action="{{ route('tod.roster.destroy', $firstRoster->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger-custom" type="submit" title="Delete" onclick="return confirm('Delete this roster?');">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Assign Roster Modal -->
<div class="modal fade" id="assignRosterModal" tabindex="-1" role="dialog" aria-labelledby="assignRosterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user-plus me-2"></i> Assign Teacher(s) Duty Roster
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate action="{{route('tod.roster.store')}}" method="POST" id="rosterForm">
                    @csrf

                    <div class="form-section">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="teacher_ids" class="form-label">
                                    <i class="fas fa-chalkboard-teacher text-primary"></i>
                                    Select Teacher(s) <span class="required-star">*</span>
                                </label>
                                <select name="teacher_ids[]" id="teacher_ids" style="text-transform: capitalize" class="form-control select2-multiple text-capitalize" multiple="multiple" required>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ (collect(old('teacher_ids'))->contains($teacher->id)) ? 'selected' : '' }}>
                                            {{ ucwords(strtolower($teacher->first_name)) }} {{ ucwords(strtolower($teacher->last_name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please select at least one teacher
                                </div>
                                @error('teacher_ids')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="start_date" class="form-label">
                                    <i class="fas fa-calendar-start text-primary"></i>
                                    Start Date <span class="required-star">*</span>
                                </label>
                                <input type="date" name="start_date" class="form-control-custom" id="start_date" value="{{ old('start_date') }}" required>
                                <div class="invalid-feedback">
                                    Please provide a start date
                                </div>
                                @error('start_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="end_date" class="form-label">
                                    <i class="fas fa-calendar-day text-primary"></i>
                                    End Date <span class="required-star">*</span>
                                </label>
                                <input type="date" name="end_date" class="form-control-custom" id="end_date" value="{{ old('end_date') }}" required>
                                <div class="invalid-feedback">
                                    Please provide an end date
                                </div>
                                @error('end_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button class="btn btn-success-custom" id="saveButton" type="submit">
                            <i class="fas fa-check me-2"></i> Assign Duty
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Roster Detail Modals -->
@foreach($rosters as $key => $roster)
    @php
        $firstRoster = $roster->first();
    @endphp
    <div class="modal fade" id="rosterModal{{ $key }}" tabindex="-1" role="dialog" aria-labelledby="rosterModalLabel{{ $key }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header-custom">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-info-circle me-2"></i> Roster #{{ strtoupper($firstRoster->roster_id) }} Details
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-calendar-start text-primary me-2"></i> Start Date:</strong>
                            <span class="text-danger">{{ $firstRoster->start_date ?? 'N/A' }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-calendar-day text-primary me-2"></i> End Date:</strong>
                            <span class="text-danger">{{ $firstRoster->end_date ?? 'N/A' }}</span></p>
                        </div>
                    </div>

                    <p><strong><i class="fas fa-info-circle text-primary me-2"></i> Status:</strong>
                        @if ($firstRoster->status == 'active')
                            <span class="badge-warning-custom">
                                <i class="fas fa-play-circle me-1"></i> Active
                            </span>
                        @elseif ($firstRoster->status == 'pending')
                            <span class="badge-secondary-custom">
                                <i class="fas fa-clock me-1"></i> Pending
                            </span>
                        @elseif ($firstRoster->status == 'completed')
                            <span class="badge-success-custom">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </span>
                        @elseif ($firstRoster->status == 'cancelled')
                            <span class="badge-danger-custom">
                                <i class="fas fa-times-circle me-1"></i> Cancelled
                            </span>
                        @endif
                    </p>

                    <hr>

                    <h6><i class="fas fa-users text-primary me-2"></i> Assigned Teachers:</h6>
                    <div class="teacher-list">
                        @foreach($roster as $teacher)
                            <div class="d-flex align-items-center mb-2 p-2 rounded" style="background: rgba(78, 84, 200, 0.05);">
                                <div class="user-avatar text-capitalize">
                                    {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-capitalize"> {{ $teacher->first_name }} {{ $teacher->last_name }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    @if ($firstRoster->status == "pending")
                        <form action="{{route('tod.roster.activate', $firstRoster->id)}}" class="d-inline" method="POST">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-success-custom" type="submit" onclick="return confirm('Activate this roster?');">
                                <i class="fas fa-check me-2"></i> Activate
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('tod.roster.destroy', $firstRoster->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger-custom" type="submit" onclick="return confirm('Delete this roster?');">
                            <i class="fas fa-trash me-2"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Select2
        $('#teacher_ids').select2({
            placeholder: "Search and select teachers...",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#assignRosterModal')
        }).on('select2:open', function () {
            $('.select2-results__option').css('text-transform', 'capitalize');
        });

        // Form validation and submission handling
        const form = document.getElementById("rosterForm");
        const submitButton = document.getElementById("saveButton");

        if (form && submitButton) {
            form.addEventListener("submit", function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    form.classList.add("was-validated");

                    // Scroll to first invalid field
                    const invalidElements = form.querySelectorAll(':invalid');
                    if (invalidElements.length > 0) {
                        invalidElements[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                    return;
                }

                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Assigning...`;
            });
        }

        // Reset form when modal is closed
        $('#assignRosterModal').on('hidden.bs.modal', function () {
            if (form) {
                form.reset();
                form.classList.remove("was-validated");
                $('#teacher_ids').val(null).trigger('change');
            }
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-check me-2"></i> Assign Duty';
            }
        });
    });
</script>
@endsection
