@extends('SRTDashboard.frame')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
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
                                    <button type="button" class="btn btn-secondary btn-action mr-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                        <i class="fas fa-file-import me-1"></i> Import File
                                    </button>
                                    <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal" data-bs-target="#parentModal">
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
                                                <td>{{$loop->iteration}}</td>
                                                <td class="text-capitalize fw-bold">{{ucwords(strtolower($parent->first_name. ' '. $parent->last_name))}}</td>
                                                <td class="text-capitalize">
                                                    <span class="badge bg-info text-white">{{$parent->gender[0]}}</span>
                                                </td>
                                                <td>{{$parent->phone}}</td>
                                                <td>{{$parent->email ?? 'N/A'}}</td>
                                                <td>
                                                    @if ($parent->status == 1)
                                                        <span class="badge-status bg-success text-white">Active</span>
                                                    @else
                                                        <span class="badge-status bg-danger text-white">Blocked</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{route('Parents.edit', ['parent' => Hashids::encode($parent->id)])}}" class="btn btn-sm btn-primary" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        @if ($parent->status == 1)
                                                            <form action="{{route('Update.parents.status', ['parent' => Hashids::encode($parent->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-warning" title="Block" onclick="return confirm('Are you sure you want to Block {{strtoupper($parent->first_name)}} {{strtoupper($parent->last_name)}}?')">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{route('restore.parents.status', ['parent' => Hashids::encode($parent->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-success" title="Unblock" onclick="return confirm('Are you sure you want to Unblock {{strtoupper($parent->first_name)}} {{strtoupper($parent->last_name)}}?')">
                                                                    <i class="ti-reload"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <form action="{{route('Parents.remove', ['parent' => Hashids::encode($parent->id)])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button class="btn btn-sm btn-danger" type="submit" title="Delete" onclick="return confirm('Are you sure you want to delete {{strtoupper($parent->first_name)}} {{strtoupper($parent->last_name)}} Permanently?')">
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
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Parents File</h5>
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('import.parents.students')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Upload Excel File</label>
                            <span class="text-danger">Only excel file allowed</span>
                            <input type="file" required name="file" class="form-control" accept=".xlsx,.xls" id="file" required>
                            @error('file')
                                <div class="text-danger small">
                                    {{$message}}
                                </div>
                            @enderror
                        </div>
                        <p class="mb-0">Download Sample file here 👉
                            <a href="{{route('template.export')}}" class="text-decoration-none">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>
                        </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="importButton" class="btn btn-success">Upload File</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Parent Registration Modal -->
    <div class="modal fade" id="parentModal" tabindex="-1" aria-labelledby="parentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="parentModalLabel">Parent Registration Form</h5>
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('Parents.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Parent Information Section -->
                        <div class="form-section">
                            <h6 class="section-title"><i class="fas fa-user me-2"></i> Parent/Guardian Information</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" name="fname" class="form-control-custom" value="{{old('fname')}}" required placeholder="First Name">
                                    @error('fname')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" name="lname" class="form-control-custom" value="{{old('lname')}}" required placeholder="Last Name">
                                    @error('lname')
                                    <div class="text-danger small">{{$message}}</div>
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
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Mobile Phone</label>
                                    <input type="text" name="phone" class="form-control-custom" value="{{old('phone')}}" required placeholder="07XXXXXXXX">
                                    @error('phone')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <input type="email" name="email" class="form-control-custom" value="{{old('email')}}" placeholder="Email Address">
                                    </div>
                                    @error('email')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="street" class="form-label">Street/Village</label>
                                    <input type="text" name="street" class="form-control-custom" value="{{old('street')}}" required placeholder="Street or Village">
                                    @error('street')
                                    <div class="text-danger small">{{$message}}</div>
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
                                    <input type="text" name="student_first_name" class="form-control-custom" value="{{old('student_first_name')}}" required placeholder="First Name">
                                    @error('student_first_name')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="student_middle_name" class="form-label">Middle Name</label>
                                    <input type="text" name="student_middle_name" class="form-control-custom" value="{{old('student_middle_name')}}" required placeholder="Middle Name">
                                    @error('student_middle_name')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="student_last_name" class="form-label">Last Name</label>
                                    <input type="text" name="student_last_name" class="form-control-custom" value="{{old('student_last_name')}}" required placeholder="Last Name">
                                    @error('student_last_name')
                                    <div class="text-danger small">{{$message}}</div>
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
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control-custom" value="{{old('dob')}}" required min="{{\Carbon\Carbon::now()->subYears(17)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(3)->format('Y-m-d')}}" placeholder="Date of Birth">
                                    @error('dob')
                                    <div class="text-danger small">{{$message}}</div>
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
                                                <option value="{{$class->id}}">{{$class->class_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('class')
                                    <div class="text-danger small">{{$message}}</div>
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
                                    <div class="text-danger small">{{$message}}</div>
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
                                                <option value="{{$bus->id}}">Bus No. {{$bus->bus_no}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('bus_no')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="passport" class="form-label">Student Photo</label>
                                    <input type="file" name="passport" class="form-control-custom" accept="image/*">
                                    <div class="note-text">Maximum 1MB - Blue background recommended</div>
                                    @error('passport')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="saveButton" class="btn btn-success">Save Parent</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Handle form submissions with loading states
            const forms = document.querySelectorAll(".needs-validation");

            forms.forEach(form => {
                const submitButton = form.querySelector('button[type="submit"]');

                if (!form || !submitButton) return;

                form.addEventListener("submit", function (event) {
                    event.preventDefault();

                    // Disable button and show loading state
                    submitButton.disabled = true;

                    if (submitButton.id === 'saveButton') {
                        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...`;
                    } else if (submitButton.id === 'importButton') {
                        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Uploading...`;
                    }

                    // Check form validity
                    if (!form.checkValidity()) {
                        form.classList.add("was-validated");
                        submitButton.disabled = false;

                        if (submitButton.id === 'saveButton') {
                            submitButton.innerHTML = "Save Parent";
                        } else if (submitButton.id === 'importButton') {
                            submitButton.innerHTML = "Upload File";
                        }

                        return;
                    }

                    // Delay submission to show loading state
                    setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            });
        });
    </script>
@endsection
