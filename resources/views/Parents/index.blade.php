@extends('SRTDashboard.frame')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
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
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px; */
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 5px 10px;
            position: relative;
            overflow: hidden;
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
            z-index: 1;
            font-size: 24px;
        }

        .card-body {
            padding: 5px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 1;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .form-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
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

        .select2-container--default .select2-selection--single {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px;
            height: auto;
            background-color: white;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .flatpickr-input {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            background-color: white;
        }

        .flatpickr-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .invalid-feedback {
            font-weight: 600;
            color: var(--danger);
            margin-top: 5px;
        }

        .text-danger small {
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #3f43b5 0%, #7a80f9 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            color: #856404;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-warning-custom:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-success-custom:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .info-alert {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.15) 0%, rgba(23, 162, 184, 0.25) 100%);
            border: 1px solid rgba(23, 162, 184, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            backdrop-filter: blur(5px);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            /* padding: 10px; */
            font-weight: 600;
            text-align: center;
        }

        .table-custom tbody td {
            /* padding: 10px; */
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: rgba(78, 84, 200, 0.05);
        }

        .table-custom tbody tr:hover {
            background-color: rgba(78, 84, 200, 0.1);
        }

        .score-input {
            width: auto;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            transition: all 0.3s;
        }

        .score-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .grade-input {
            width: 60px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            font-weight: bold;
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

        .instruction-text {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.25) 100%);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 193, 7, 0.3);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .card-body {
                padding: 5px;
            }

            .header-title {
                font-size: 20px;
            }

            .table-responsive {
                font-size: 14px;
            }

            .score-input, .grade-input {
                width: 100%;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(78, 84, 200, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0);
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
                                    <input type="text" name="fname" class="form-control" value="{{old('fname')}}" required>
                                    @error('fname')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" name="lname" class="form-control" value="{{old('lname')}}" required>
                                    @error('lname')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select name="gender" class="form-control" required>
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
                                    <input type="text" name="phone" class="form-control" value="{{old('phone')}}" required>
                                    @error('phone')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="email" name="email" class="form-control" value="{{old('email')}}">
                                    </div>
                                    @error('email')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="street" class="form-label">Street/Village</label>
                                    <input type="text" name="street" class="form-control" value="{{old('street')}}" required>
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
                                    <input type="text" name="student_first_name" class="form-control" value="{{old('student_first_name')}}" required>
                                    @error('student_first_name')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="student_middle_name" class="form-label">Middle Name</label>
                                    <input type="text" name="student_middle_name" class="form-control" value="{{old('student_middle_name')}}" required>
                                    @error('student_middle_name')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="student_last_name" class="form-label">Last Name</label>
                                    <input type="text" name="student_last_name" class="form-control" value="{{old('student_last_name')}}" required>
                                    @error('student_last_name')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="student_gender" class="form-label">Student Gender</label>
                                    <select name="student_gender" class="form-control" required>
                                        <option value="">-- select gender --</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    @error('student_gender')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control" value="{{old('dob')}}" required min="{{\Carbon\Carbon::now()->subYears(17)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(3)->format('Y-m-d')}}">
                                    @error('dob')
                                    <div class="text-danger small">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="class" class="form-label">Student Class</label>
                                    <select name="class" class="form-control" required>
                                        <option value="">-- select Student Class --</option>
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
                                    <select name="group" class="form-control" required>
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
                                    <select name="bus_no" class="form-control">
                                        <option value="">-- select Student Bus --</option>
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
                                    <input type="file" name="passport" class="form-control" accept="image/*">
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
