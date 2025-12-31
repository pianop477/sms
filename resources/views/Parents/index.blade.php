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

        .badge-status {
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

        .action-buttons a,
        .action-buttons button {
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

        .form-control:focus,
        .form-select:focus {
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

        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e3e6f0;
        }

        .form-section {
            background-color: #f8f9fc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
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
                            <div class="col-md-8">
                                <h4 class="header-title">Registered Parents</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <button type="button" class="btn btn-secondary btn-action mr-2" data-bs-toggle="modal"
                                        data-bs-target="#importModal">
                                        <i class="fas fa-file-import me-1"></i> Import File
                                    </button>
                                    <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal"
                                        data-bs-target="#parentModal">
                                        <i class="fas fa-user-plus me-1"></i> Parent Form
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Parents Table -->
                        <div class="single-table">
                            <div class="table-responsive">
                                <table class="table table-hover progress-table table-responsive-md" id="myTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Parent's Name</th>
                                            <th scope="col">Gender</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($parents as $parent)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-capitalize fw-bold">
                                                    {{ ucwords(strtolower($parent->first_name . ' ' . $parent->last_name)) }}
                                                </td>
                                                <td class="text-capitalize">
                                                    <span class="badge bg-info text-white">{{ $parent->gender[0] }}</span>
                                                </td>
                                                <td>{{ $parent->phone }}</td>
                                                <td>{{ $parent->email ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($parent->status == 1)
                                                        <span class="badge-status bg-success text-white">Active</span>
                                                    @else
                                                        <span class="badge-status bg-danger text-white">Blocked</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{ route('Parents.edit', ['parent' => Hashids::encode($parent->id)]) }}"
                                                            class="btn btn-sm btn-primary" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        @if ($parent->status == 1)
                                                            <form
                                                                action="{{ route('Update.parents.status', ['parent' => Hashids::encode($parent->id)]) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-warning"
                                                                    title="Block"
                                                                    onclick="return confirm('Are you sure you want to Block {{ strtoupper($parent->first_name) }} {{ strtoupper($parent->last_name) }}?')">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form
                                                                action="{{ route('restore.parents.status', ['parent' => Hashids::encode($parent->id)]) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-success"
                                                                    title="Unblock"
                                                                    onclick="return confirm('Are you sure you want to Unblock {{ strtoupper($parent->first_name) }} {{ strtoupper($parent->last_name) }}?')">
                                                                    <i class="ti-reload"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <form
                                                            action="{{ route('Parents.remove', ['parent' => Hashids::encode($parent->id)]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button class="btn btn-sm btn-danger" type="submit"
                                                                title="Delete"
                                                                onclick="return confirm('Are you sure you want to delete {{ strtoupper($parent->first_name) }} {{ strtoupper($parent->last_name) }} Permanently?')">
                                                                <i class="ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Parents File</h5>
                    <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <!-- Step 1: Upload Form -->
                    <div id="uploadStep">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Upload Excel File</label>
                                <span class="text-danger">Only excel file allowed</span>
                                <input type="file" required name="file" class="form-control" accept=".xlsx,.xls,.csv"
                                    id="fileInput" required>
                                <div class="form-text text-muted">Maximum file size: 2MB</div>
                                <div id="fileError" class="text-danger small d-none"></div>
                            </div>
                            <p class="mb-0">Download Sample file 👉
                                <a href="{{ route('parent.template.export') }}" class="text-decoration-none">
                                    <i class="fas fa-download me-1"></i> Download Template
                                </a>
                            </p>
                        </form>
                    </div>

                    <!-- Step 2: Preview Section -->
                    <div id="previewStep" class="d-none">
                        <!-- Summary Card -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <h5 class="text-primary" id="totalRows">0</h5>
                                            <p class="text-muted mb-0"><i class="fas fa-file-archive text-primary"></i>
                                                Total Rows</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <h5 class="text-success" id="validRows">0</h5>
                                            <p class="text-muted mb-0"><i
                                                    class="fas fa-file-circle-check text-success"></i> Valid Data</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <h5 class="text-danger" id="invalidRows">0</h5>
                                            <p class="text-muted mb-0"><i
                                                    class="fas fa-file-circle-xmark text-danger"></i> Errors</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <button type="button" id="startImportBtn" class="btn btn-success btn-sm"
                                                disabled>
                                                <i class="fas fa-cloud-upload-alt me-1"></i> Import All
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Errors Display -->
                        <div id="errorsContainer" class="d-none">
                            <div class="alert alert-danger">
                                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Validation
                                    Errors</h6>
                                <ul id="errorsList" class="mb-0 small"></ul>
                            </div>
                        </div>

                        <!-- Preview Table Container -->
                        <div class="preview-table-container">
                            <table class="table table-hover table-bordered mb-0" id="previewTable">
                                <thead class="table-primary" style="position: sticky; top: 0;">
                                    <tr>
                                        <th>#</th>
                                        <th>Parent Name</th>
                                        <th>Gender</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Student Name</th>
                                        <th>Student Gender</th>
                                        <th>Class</th>
                                        <th>Stream</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="previewTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Row count info -->
                        <div id="tableInfo" class="text-muted small mt-2 text-center d-none">
                            <i class="fas fa-info-circle me-1"></i>
                            <span id="rowCount">0</span> records displayed. Scroll to view all data.
                        </div>

                        <!-- Import Progress -->
                        <div id="importProgress" class="d-none mt-4">
                            <div class="progress" style="height: 25px;">
                                <div id="importProgressBar"
                                    class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                    style="width: 0%">0%</div>
                            </div>
                            <div class="mt-2 text-center">
                                <span id="importStatus">Preparing import...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="uploadButton" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload & Preview
                    </button>
                    <button type="button" id="backButton" class="btn btn-secondary d-none">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Parent Registration Modal -->
    <div class="modal fade" id="parentModal" tabindex="-1" aria-labelledby="parentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="parentModalLabel">Parent Registration Form</h5>
                    <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{ route('Parents.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Parent Information Section -->
                        <div class="form-section">
                            <h6 class="section-title"><i class="fas fa-user me-2"></i> Parent/Guardian Information</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" name="fname" class="form-control-custom"
                                        value="{{ old('fname') }}" required placeholder="First Name">
                                    @error('fname')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" name="lname" class="form-control-custom"
                                        value="{{ old('lname') }}" required placeholder="Last Name">
                                    @error('lname')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select name="gender" class="form-control-custom" required>
                                        <option value="">-- select Parent gender --</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    @error('gender')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Mobile Phone</label>
                                    <input type="text" name="phone" class="form-control-custom"
                                        value="{{ old('phone') }}" required placeholder="07XXXXXXXX">
                                    @error('phone')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <input type="email" name="email" class="form-control-custom"
                                            value="{{ old('email') }}" placeholder="Email Address">
                                    </div>
                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="street" class="form-label">Street/Village</label>
                                    <input type="text" name="street" class="form-control-custom"
                                        value="{{ old('street') }}" required placeholder="Street or Village">
                                    @error('street')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Student Information Section -->
                        <div class="form-section">
                            <h6 class="section-title"><i class="fas fa-user-graduate me-2"></i> Student Information</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="student_first_name" class="form-label">First Name</label>
                                    <input type="text" name="student_first_name" class="form-control-custom"
                                        value="{{ old('student_first_name') }}" required placeholder="First Name">
                                    @error('student_first_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="student_middle_name" class="form-label">Middle Name</label>
                                    <input type="text" name="student_middle_name" class="form-control-custom"
                                        value="{{ old('student_middle_name') }}" required placeholder="Middle Name">
                                    @error('student_middle_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="student_last_name" class="form-label">Last Name</label>
                                    <input type="text" name="student_last_name" class="form-control-custom"
                                        value="{{ old('student_last_name') }}" required placeholder="Last Name">
                                    @error('student_last_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="student_gender" class="form-label">Student Gender</label>
                                    <select name="student_gender" class="form-control-custom" required>
                                        <option value="">-- Select gender --</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    @error('student_gender')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control-custom"
                                        value="{{ old('dob') }}" required
                                        min="{{ \Carbon\Carbon::now()->subYears(17)->format('Y-m-d') }}"
                                        max="{{ \Carbon\Carbon::now()->subYears(3)->format('Y-m-d') }}"
                                        placeholder="Date of Birth">
                                    @error('dob')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="class" class="form-label">Student Class</label>
                                    <select name="class" class="form-control-custom" required>
                                        <option value="">-- Select Class --</option>
                                        @if ($classes->isEmpty())
                                            <option value="" disabled class="text-danger">No classes found</option>
                                        @else
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('class')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="group" class="form-label">Class Stream</label>
                                    <select name="group" class="form-control-custom" required>
                                        <option value="">--Select Stream--</option>
                                        <option value="a">A</option>
                                        <option value="b">B</option>
                                        <option value="c">C</option>
                                    </select>
                                    @error('group')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bus_no" class="form-label">Student Bus Number</label>
                                    <select name="bus_no" class="form-control-custom">
                                        <option value="">-- Select Bus --</option>
                                        @if ($buses->isEmpty())
                                            <option value="" disabled class="text-danger">No buses found</option>
                                        @else
                                            @foreach ($buses as $bus)
                                                <option value="{{ $bus->id }}">Bus No. {{ $bus->bus_no }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('bus_no')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="passport" class="form-label">Student Photo</label>
                                    <input type="file" name="passport" class="form-control-custom" accept="image/*">
                                    <div class="note-text">Maximum 1MB - Blue background recommended</div>
                                    @error('passport')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
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
    <style>
        /* Add to your existing CSS */

        /* Scrollable table container */
        .preview-table-container {
            max-height: 400px;
            /* Fixed height for table */
            overflow-y: auto;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Style for the scrollable table */
        .preview-table-container table {
            margin-bottom: 0;
        }

        /* Fixed header for better UX */
        .preview-table-container thead th {
            position: sticky;
            top: 0;
            background-color: var(--primary-color);
            z-index: 10;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
        }

        /* Style scrollbar for better appearance */
        .preview-table-container::-webkit-scrollbar {
            width: 8px;
        }

        .preview-table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .preview-table-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .preview-table-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Modal adjustments for better fit */
        #importModal .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        #importModal .modal-content {
            max-height: 90vh;
            overflow: hidden;
        }

        /* Stats card adjustments */
        .stat-card {
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Make sure the table fits well */
        #previewTable {
            width: 100%;
            table-layout: fixed;
        }

        #previewTable th,
        #previewTable td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        /* Column widths for better display */
        #previewTable th:nth-child(1),
        #previewTable td:nth-child(1) {
            width: 50px;
            /* # column */
            text-align: center;
        }

        #previewTable th:nth-child(2),
        #previewTable td:nth-child(2) {
            width: 150px;
            /* Parent Name */
            min-width: 150px;
        }

        #previewTable th:nth-child(3),
        #previewTable td:nth-child(3) {
            width: 70px;
            /* Gender */
            text-align: center;
        }

        #previewTable th:nth-child(4),
        #previewTable td:nth-child(4) {
            width: 120px;
            /* Phone */
        }

        #previewTable th:nth-child(5),
        #previewTable td:nth-child(5) {
            width: 150px;
            /* Email */
        }

        #previewTable th:nth-child(6),
        #previewTable td:nth-child(6) {
            width: 150px;
            /* Student Name */
        }

        #previewTable th:nth-child(7),
        #previewTable td:nth-child(7) {
            width: 70px;
            /* Student Gender */
            text-align: center;
        }

        #previewTable th:nth-child(8),
        #previewTable td:nth-child(8) {
            width: 100px;
            /* Class */
        }

        #previewTable th:nth-child(9),
        #previewTable td:nth-child(9) {
            width: 70px;
            /* Stream */
            text-align: center;
        }

        #previewTable th:nth-child(10),
        #previewTable td:nth-child(10) {
            width: 90px;
            /* Status */
            text-align: center;
        }

        .page-item.active .page-link {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }

        .page-link {
            color: #4e73df;
            border: 1px solid #ddd;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .page-link:hover {
            color: #2e59d9;
            background-color: #eaecf4;
            border-color: #ddd;
        }

        .page-item.disabled .page-link {
            color: #858796;
            pointer-events: none;
            background-color: #fff;
            border-color: #ddd;
        }

        /* Stats counter styling */
        .stat-card {
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #e3e6f0;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-card h5 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            font-size: 0.85rem;
        }

        /* Table pagination info */
        .stat-card {
            padding: 15px;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-card h5 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        #previewTable th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        #previewTable tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }

        #importProgress {
            transition: all 0.3s ease;
        }

        .progress-bar {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .alert-danger ul {
            max-height: 150px;
            overflow-y: auto;
            padding-left: 20px;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.4em 0.8em;
        }

        /* Pagination Styles */
        .pagination {
            margin-bottom: 0;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .page-link {
            color: var(--primary-color);
            cursor: pointer;
        }

        .page-link:hover {
            color: #2e59d9;
            background-color: #eaecf4;
            border-color: #ddd;
        }

        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #f8f9fc;
            border-color: #dee2e6;
        }

        /* File upload area styling */
        #fileInput {
            border: 2px dashed #4e73df;
            padding: 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        #fileInput:hover {
            border-color: #2e59d9;
            background-color: rgba(78, 115, 223, 0.05);
        }

        /* Loading overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        /* Status badges */
        .badge-pending {
            background-color: #f6c23e;
            color: #000;
        }

        .badge-success {
            background-color: #1cc88a;
            color: white;
        }

        .badge-error {
            background-color: #e74a3b;
            color: white;
        }

        .import-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .import-overlay .spinner-container {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            border: 1px solid #e3e6f0;
            max-width: 500px;
            width: 90%;
        }

        .import-overlay .spinner-container h5 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-weight: 600;
        }

        .import-overlay .progress {
            width: 100%;
            height: 25px;
            margin: 20px 0;
        }

        .import-overlay .status-text {
            margin-top: 15px;
            font-size: 14px;
            color: #6c757d;
        }

        /* Disable table during import */
        .table-disabled {
            opacity: 0.5;
            pointer-events: none;
            user-select: none;
        }

        /* SweetAlert customization */
        .swal2-popup {
            border-radius: 10px !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }

        .swal2-success {
            border-color: var(--success-color) !important;
            color: var(--success-color) !important;
        }

        .swal2-error {
            border-color: var(--danger-color) !important;
            color: var(--danger-color) !important;
        }

        /* Progress bar animation */
        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }

        @keyframes progress-bar-stripes {
            0% {
                background-position: 1rem 0;
            }

            100% {
                background-position: 0 0;
            }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ========== PART 1: Form validation kwa Parent Registration ==========
            const forms = document.querySelectorAll(".needs-validation");

            forms.forEach(form => {
                const submitButton = form.querySelector('button[type="submit"]');

                if (!form || !submitButton) return;

                form.addEventListener("submit", function(event) {
                    event.preventDefault();

                    // Disable button and show loading state
                    submitButton.disabled = true;

                    if (submitButton.id === 'saveButton') {
                        submitButton.innerHTML =
                            `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...`;
                    }

                    // Check form validity
                    if (!form.checkValidity()) {
                        form.classList.add("was-validated");
                        submitButton.disabled = false;

                        if (submitButton.id === 'saveButton') {
                            submitButton.innerHTML = "Submit";
                        }

                        return;
                    }

                    // Delay submission to show loading state
                    setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            });

            // ========== PART 2: Import Modal Functionality ==========
            const uploadForm = document.getElementById('uploadForm');
            const fileInput = document.getElementById('fileInput');
            const uploadButton = document.getElementById('uploadButton');
            const backButton = document.getElementById('backButton');
            const startImportBtn = document.getElementById('startImportBtn');
            const uploadStep = document.getElementById('uploadStep');
            const previewStep = document.getElementById('previewStep');
            const previewTableBody = document.getElementById('previewTableBody');
            const errorsContainer = document.getElementById('errorsContainer');
            const errorsList = document.getElementById('errorsList');
            const importProgress = document.getElementById('importProgress');
            const importProgressBar = document.getElementById('importProgressBar');
            const importStatus = document.getElementById('importStatus');
            const rowCountElement = document.getElementById('rowCount');
            const tableInfoElement = document.getElementById('tableInfo');
            const previewTableContainer = document.querySelector('.preview-table-container');

            // Create import overlay element
            const importOverlay = document.createElement('div');
            importOverlay.className = 'import-overlay d-none';
            importOverlay.innerHTML = `
        <div class="spinner-container">
            <div class="text-center mb-3">
                <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-3"></i>
                <h5>Importing Records</h5>
            </div>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                     role="progressbar" style="width: 0%">
                    <span class="progress-text">0%</span>
                </div>
            </div>
            <div class="status-text text-center">
                <i class="fas fa-sync-alt fa-spin me-2"></i>
                <span class="current-status">Starting import process...</span>
            </div>
            <div class="mt-4 text-center">
                <button class="btn btn-sm btn-outline-secondary" id="cancelImportBtn">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
            </div>
        </div>
    `;
            document.body.appendChild(importOverlay);

            // Real-time file upload and preview
            fileInput.addEventListener('change', function() {
                if (!this.files.length) {
                    showError('');
                    return;
                }

                const file = this.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB

                // Validate file
                if (file.size > maxSize) {
                    showError('File size exceeds 2MB limit');
                    return;
                }

                if (!file.name.match(/\.(xlsx|xls|csv)$/i)) {
                    showError('Please select an Excel file (.xlsx, .xls, .csv)');
                    return;
                }

                // Show loading state on upload button
                uploadButton.disabled = true;
                uploadButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';

                // Upload and process file immediately
                uploadAndPreviewFile(file);
            });

            // Function to upload and preview file
            function uploadAndPreviewFile(file) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('import.parents.students') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showPreview(data);
                        } else {
                            showError(data.message || 'Failed to process file');
                            resetUploadButton();
                        }
                    })
                    .catch(error => {
                        showError('Network error. Please try again.');
                        console.error('Error:', error);
                        resetUploadButton();
                    });
            }

            // Manual upload button (fallback)
            uploadButton.addEventListener('click', function() {
                if (!fileInput.files.length) {
                    showError('Please select a file');
                    return;
                }

                const file = fileInput.files[0];
                uploadAndPreviewFile(file);
            });

            // Show preview with ALL data (no pagination)
            function showPreview(data) {
                console.log('Showing preview with', data.valid_rows, 'valid rows');

                // Update summary
                document.getElementById('totalRows').textContent = data.total_rows;
                document.getElementById('validRows').textContent = data.valid_rows;
                document.getElementById('invalidRows').textContent = data.invalid_rows;

                // Show/hide errors
                if (data.errors && data.errors.length > 0) {
                    errorsContainer.classList.remove('d-none');
                    errorsList.innerHTML = '';
                    data.errors.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        errorsList.appendChild(li);
                    });
                } else {
                    errorsContainer.classList.add('d-none');
                }

                // Clear table first
                previewTableBody.innerHTML = '';

                // Populate table with ALL data
                if (data.preview_data && data.preview_data.length > 0) {
                    data.preview_data.forEach((row, index) => {
                        const tr = document.createElement('tr');

                        tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td class="text-capitalize fw-bold" title="${row.parent_name}">${row.parent_name}</td>
                    <td><span class="badge bg-info text-white">${row.parent_gender[0]}</span></td>
                    <td>${row.parent_phone}</td>
                    <td title="${row.parent_email}">${row.parent_email}</td>
                    <td class="text-capitalize" title="${row.student_name}">${row.student_name}</td>
                    <td><span class="badge bg-secondary text-white">${row.student_gender[0]}</span></td>
                    <td>${row.class_name}</td>
                    <td><span class="badge bg-primary">${row.student_group}</span></td>
                    <td><span class="badge bg-warning">Pending</span></td>
                `;
                        previewTableBody.appendChild(tr);
                    });

                    // Show row count info
                    rowCountElement.textContent = data.preview_data.length;
                    tableInfoElement.classList.remove('d-none');

                    console.log('Displayed', data.preview_data.length, 'rows in table');
                } else {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                <td colspan="10" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <p class="mb-0">No valid data to display</p>
                    </div>
                </td>
            `;
                    previewTableBody.appendChild(tr);
                    tableInfoElement.classList.add('d-none');
                }

                // Show preview step
                uploadStep.classList.add('d-none');
                previewStep.classList.remove('d-none');
                uploadButton.classList.add('d-none');
                backButton.classList.remove('d-none');

                // Enable/disable import button
                if (data.valid_rows > 0) {
                    startImportBtn.disabled = false;
                    startImportBtn.innerHTML =
                        `<i class="fas fa-cloud-upload-alt me-1"></i> Import ${data.valid_rows} Records`;
                } else {
                    startImportBtn.disabled = true;
                    startImportBtn.innerHTML = '<i class="fas fa-cloud-upload-alt me-1"></i> Import All';
                }

                resetUploadButton();

                // Scroll to top of preview table
                setTimeout(() => {
                    if (previewTableContainer) {
                        previewTableContainer.scrollTop = 0;
                    }
                }, 100);
            }

            // Back button handler
            backButton.addEventListener('click', function() {
                previewStep.classList.add('d-none');
                uploadStep.classList.remove('d-none');
                uploadButton.classList.remove('d-none');
                backButton.classList.add('d-none');
                importProgress.classList.add('d-none');

                // Reset form
                fileInput.value = '';
                showError('');
                previewTableBody.innerHTML = '';
                tableInfoElement.classList.add('d-none');
                resetUploadButton();
            });

            // Start import function
            startImportBtn.addEventListener('click', function() {
                const validRows = document.getElementById('validRows').textContent;

                if (!confirm(`Are you sure you want to import ${validRows} records?`)) {
                    return;
                }

                // Show overlay and disable table
                showImportOverlay();

                // Start import process
                processImport();
            });

            // Show import overlay
            function showImportOverlay() {
                importOverlay.classList.remove('d-none');
                if (previewTableContainer) {
                    previewTableContainer.classList.add('table-disabled');
                }

                // Update overlay progress
                updateOverlayProgress(0, 'Starting import process...');

                // Setup cancel button
                const cancelBtn = document.getElementById('cancelImportBtn');
                if (cancelBtn) {
                    cancelBtn.onclick = function() {
                        if (confirm('Are you sure you want to cancel the import?')) {
                            cancelImport();
                        }
                    };
                }
            }

            // Hide import overlay
            function hideImportOverlay() {
                importOverlay.classList.add('d-none');
                if (previewTableContainer) {
                    previewTableContainer.classList.remove('table-disabled');
                }
            }

            // Update overlay progress
            function updateOverlayProgress(percent, status) {
                const progressBar = importOverlay.querySelector('.progress-bar');
                const progressText = importOverlay.querySelector('.progress-text');
                const currentStatus = importOverlay.querySelector('.current-status');

                if (progressBar) {
                    progressBar.style.width = percent + '%';
                    if (progressText) {
                        progressText.textContent = percent + '%';
                    }
                }

                if (currentStatus) {
                    currentStatus.textContent = status;
                }
            }

            // Cancel import
            function cancelImport() {
                hideImportOverlay();
                resetImportButton();

                // Show cancellation message
                Swal.fire({
                    icon: 'info',
                    title: 'Import Cancelled',
                    text: 'The import process has been cancelled.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }

            // Process import with real progress
            function processImport() {
                // Disable import button
                startImportBtn.disabled = true;
                startImportBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Importing...';

                // Simulate progress updates
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 5;
                    if (progress > 95) progress = 95;

                    updateOverlayProgress(progress, `Processing records... ${progress}% complete`);
                }, 300);

                // Make actual API call
                fetch('{{ route('process.import') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        clearInterval(progressInterval);

                        if (data.success) {
                            // Complete the progress
                            updateOverlayProgress(100, 'Import completed successfully!');

                            setTimeout(() => {
                                hideImportOverlay();

                                // Show success message with SweetAlert
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data Imported Successfully!',
                                    html: `
                            <div class="text-center">
                                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                <h5 class="mb-2">${data.count} records imported successfully</h5>
                                <p class="text-muted">${data.message}</p>
                            </div>
                        `,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: true,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#1cc88a',
                                    timer: 5000,
                                    timerProgressBar: true,
                                    showClass: {
                                        popup: 'animate__animated animate__fadeInRight'
                                    },
                                    hideClass: {
                                        popup: 'animate__animated animate__fadeOutRight'
                                    }
                                }).then((result) => {
                                    // Close modal and reload page
                                    window.location.reload();
                                });
                            }, 1000);
                        } else {
                            clearInterval(progressInterval);
                            hideImportOverlay();

                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Import Failed',
                                text: data.message || 'Failed to import records',
                                confirmButtonText: 'Try Again',
                                confirmButtonColor: '#e74a3b'
                            }).then(() => {
                                resetImportButton();
                            });
                        }
                    })
                    .catch(error => {
                        clearInterval(progressInterval);
                        hideImportOverlay();

                        // Show network error
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: error.message || 'Please check your internet connection',
                            confirmButtonText: 'Retry',
                            confirmButtonColor: '#e74a3b'
                        }).then(() => {
                            resetImportButton();
                        });
                    });
            }

            // Helper functions
            function showError(message) {
                const errorDiv = document.getElementById('fileError');
                if (message) {
                    errorDiv.textContent = message;
                    errorDiv.classList.remove('d-none');
                } else {
                    errorDiv.textContent = '';
                    errorDiv.classList.add('d-none');
                }
            }

            function resetUploadButton() {
                uploadButton.disabled = false;
                uploadButton.innerHTML = '<i class="fas fa-upload me-1"></i> Upload & Preview';
            }

            function resetImportButton() {
                startImportBtn.disabled = false;
                const validRows = document.getElementById('validRows').textContent;
                startImportBtn.innerHTML =
                    `<i class="fas fa-cloud-upload-alt me-1"></i> Import ${validRows} Records`;
            }
        });
    </script>
@endsection
