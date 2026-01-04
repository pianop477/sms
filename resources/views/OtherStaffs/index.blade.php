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
        .student-info-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
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
                                <h4 class="header-title">Registered Staffs</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-action dropdown-toggle mr-2" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cloud-arrow-down me-1"></i> Export
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                            <li>
                                                <a class="dropdown-item" href="{{route('export.other.staffs', ['format' => 'excel'])}}">
                                                    <i class="fas fa-file-excel me-1 text-success"></i> Excel
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{route('export.other.staffs', ['format' => 'pdf'])}}" target="">
                                                    <i class="fas fa-file-pdf me-1 text-danger"></i> PDF
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                                        <i class="fas fa-user-plus me-1"></i> Register
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="student-info-card">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users fa-2x me-3 mr-2"></i>
                                        <div>
                                            <h6 class="mb-0"> Total Staffs</h6>
                                            <h3 class="mb-0"> {{ $combinedStaffs->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-male fa-2x me-3 mr-2"></i>
                                        <div>
                                            <h6 class="mb-0"> Male</h6>
                                            <h3 class="mb-0"> {{ $combinedStaffs->filter(fn($s) => strtolower($s->gender) === 'male')->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-female fa-2x me-3 mr-2"></i>
                                        <div>
                                            <h6 class="mb-0"> Female</h6>
                                            <h3 class="mb-0"> {{ $combinedStaffs->filter(fn($s)=> strtolower($s->gender) === 'female')->count() }}</h3>
                                        </div>
                                    </div>
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
                                            <th scope="col">Staff ID</th>
                                            <th scope="col">Full Name</th>
                                            <th scope="col">Gender</th>
                                            <th scope="col">Job title</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Joined</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($combinedStaffs as $row)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td class="fw-bold">{{strtoupper($row->staff_id ?? 'n/a')}}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @php
                                                            $imageName = $row->profile_image;
                                                            $imagePath = storage_path('app/public/profile/' . $imageName);

                                                            if (!empty($imageName) && file_exists($imagePath)) {
                                                                $avatarImage = asset('storage/profile/' . $imageName);
                                                            } else {
                                                                $avatarImage = asset('storage/profile/' . ($row->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                                                            }
                                                        @endphp
                                                        <img src="{{ $avatarImage }}" alt="Avatar" class="teacher-avatar">
                                                        @if (isset($row->driver_name))
                                                            <span class="text-capitalize"> {{ucwords(strtolower($row->driver_name))}}</span>
                                                        @else
                                                            <span class="text-capitalize"> {{ucwords(strtolower($row->first_name . ' '. $row->last_name))}}</span>
                                                        @endif

                                                    </div>
                                                </td>
                                                <td class="text-capitalize">
                                                    <span class="badge bg-info text-white">{{strtoupper($row->gender[0])}}</span>
                                                </td>
                                                <td>
                                                    {{$row->job_title ?? 'N/A'}}
                                                </td>
                                                <td>{{$row->phone}}</td>
                                                <td>{{$row->joining_year ?? "N/A"}}</td>
                                                <td>
                                                    @if ($row->status == 1)
                                                        <span class="badge bg-success text-white">Active</span>
                                                    @else
                                                        <span class="badge bg-danger text-white">Blocked</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{route('OtherStaffs.profile', ['type' => $row->job_title, 'id' => Hashids::encode($row->id)])}}" class="btn btn-sm btn-primary" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        @if ($row->status == 1)
                                                            <form action="{{route('block.other.staffs', ['type' => $row->job_title, 'id' => Hashids::encode($row->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-warning" title="Block" onclick="return confirm('Are you sure you want to Block {{strtoupper($row->first_name)}} {{strtoupper($row->last_name)}}?')">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{route('unblock.other.staffs', ['type' => $row->job_title, 'id' => Hashids::encode($row->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-success" title="Unblock" onclick="return confirm('Are you sure you want to Unblock {{strtoupper($row->first_name)}} {{strtoupper($row->last_name)}}?')">
                                                                    <i class="ti-reload"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <form action="{{route('remove.other.staffs', ['type' => $row->job_title, 'id' => Hashids::encode($row->id)])}}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger" type="submit" title="Delete" onclick="return confirm('Are you sure you want to Delete {{ strtoupper($row->first_name) }} {{ strtoupper($row->last_name) }} Permanently?')">
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

    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTeacherModalLabel">Staff Registration Form</h5>
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('OtherStaffs.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="text" required name="fname" class="form-control-custom" id="fname" placeholder="First name" value="{{old('fname')}}">
                                @error('fname')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lname" class="form-label">Other Names</label>
                                <input type="text" required name="lname" class="form-control-custom" id="lname" placeholder="Middle & Last name" value="{{old('lname')}}">
                                @error('lname')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <input type="email" name="email" class="form-control-custom" id="email" placeholder="Email ID" value="{{old('email')}}">
                                </div>
                                @error('email')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select name="gender" id="gender" class="form-control-custom" required>
                                    <option value="">-- select gender --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                @error('gender')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Mobile Phone</label>
                                <input type="text" required name="phone" class="form-control-custom" id="phone" placeholder="Phone Number" value="{{old('phone')}}">
                                @error('phone')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="qualification" class="form-label">Education Level</label>
                                <select name="education" id="education" class="form-control-custom" required>
                                    <option value="">-- Select --</option>
                                    <option value="university">University</option>
                                    <option value="college">College</option>
                                    <option value="high_school">High school</option>
                                    <option value="secondary">Secondary school</option>
                                    <option value="primary">Primary school</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('education')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" required name="dob" class="form-control-custom" id="dob" value="{{old('dob')}}" min="{{\Carbon\Carbon::now()->subYears(60)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(18)->format('Y-m-d')}}">
                                @error('dob')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="joined" class="form-label">Year Joined</label>
                                <select name="joined" id="joined" class="form-control-custom" required>
                                    <option value="">-- Select Year --</option>
                                    @for ($year = date('Y'); $year >= 2010; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                                @error('joined')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="street" class="form-label">Street/Village</label>
                                <input type="text" required name="street" class="form-control-custom" id="street" value="{{old('street')}}" placeholder="Street Address">
                                @error('street')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="job_title" class="form-label">Job Title</label>
                                <select name="job_title" id="job_title" class="form-control-custom" required>
                                    <option value="">-- Select --</option>
                                    <option value="cooks">Cooks</option>
                                    <option value="matron">Matron</option>
                                    <option value="patron">Patron</option>
                                    <option value="cleaner">Cleaner</option>
                                    <option value="security guard">Security guard</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('job_title')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nida">NIN (NIDA)</label>
                                <input type="text" name="nida" class="form-control-custom" maxlength="23" required id="nin" value="{{old('nida')}}" placeholder="19700130411110000123">
                                @error('nida')
                                    <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="profile_image" class="form-label">Profile Picture</label>
                                <input type="file" name="image" class="form-control-custom" id="image" value="{{old('image')}}" placeholder="">
                                @error('image')
                                <div class="text-danger small">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="saveButton" class="btn btn-success">Save</button>
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
                    submitButton.innerHTML = "Save";
                    return;
                }

                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });

        document.getElementById('nin').addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^0-9]/g, '');

            let formatted = '';

            if (value.length > 0) {
                formatted += value.substring(0, 8);
            }

            // DASH ya kwanza — ionekane instantly baada ya digits 8
            if (value.length >= 8) {
                formatted += '-';
            }

            if (value.length > 8) {
                formatted += value.substring(8, 13);
            }

            if (value.length >= 13) {
                formatted += '-';
            }

            if (value.length > 13) {
                formatted += value.substring(13, 18);
            }

            // DASH ya tatu — ionekane instantly baada ya digits 18
            if (value.length >= 18) {
                formatted += '-';
            }

            if (value.length > 18) {
                formatted += value.substring(18, 20);
            }

            e.target.value = formatted;
        });
    </script>
@endsection
