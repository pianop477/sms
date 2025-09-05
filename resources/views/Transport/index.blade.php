@extends('SRTDashboard.frame')

@section('content')
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
            min-height: 100vh;
            overflow-x: hidden;
            /* padding: 20px; */
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: visible;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 25px 30px;
            position: relative;
            overflow: visible;
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
            z-index: 1;
            font-size: 24px;
        }

        .card-body {
            padding: 10px;
            position: relative;
            z-index: 1;
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
            overflow: hidden;
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

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .table-custom {
            margin-bottom: 0;
            width: 100%;
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
        .badge-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            border-radius: 50px;
            padding: 2px 4px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-danger-custom {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: white;
            border-radius: 50px;
            padding: 2px 4px;
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
            font-size: 12px;
            text-decoration: none;
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

        .btn-info-custom:hover {
            background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(23, 162, 184, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            border: none;
            border-radius: 50px;
            padding: 8px 16px;
            font-weight: 600;
            color: #856404;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            text-decoration: none;
        }

        .btn-warning-custom:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            color: #856404;
            text-decoration: none;
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
            font-size: 12px;
            text-decoration: none;
        }

        .btn-danger-custom:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
            color: white;
            text-decoration: none;
        }

        .action-btn {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 16px;
            transition: all 0.3s;
            padding: 8px;
            border-radius: 5px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn:hover {
            background-color: rgba(78, 84, 200, 0.1);
            transform: scale(1.1);
            color: var(--secondary);
        }

        .action-btn-success:hover {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .action-btn-danger:hover {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }

        .driver-avatar {
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

        .driver-info {
            display: flex;
            align-items: center;
        }

        .gender-badge {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            border-radius: 50px;
            padding: 4px 8px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
        }

        .gender-badge.male {
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
        }

        .gender-badge.female {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff9e9e 100%);
        }

        .bus-badge {
            background: linear-gradient(135deg, var(--info) 0%, #17a2b8 100%);
            color: white;
            border-radius: 50px;
            padding: 4px 8px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 60px;
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

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
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

            .driver-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .driver-avatar {
                margin-right: 0;
                margin-bottom: 8px;
            }

            .btn-info-custom, .btn-warning-custom, .btn-danger-custom {
                width: 100%;
                justify-content: center;
            }
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <h4 class="header-title text-white">
                            <i class="fas fa-bus me-2"></i> School Bus Management
                        </h4>
                        <p class="mb-0 text-white-50">Manage bus routes and driver assignments</p>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-primary-custom" data-toggle="modal" data-target="#busModal">
                            <i class="fas fa-plus-circle me-2"></i> New Route
                        </button>
                    </div>
                </div>
                <i class="fas fa-route floating-icons"></i>
            </div>

            <div class="card-body">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-custom table-responsive-md" id="myTable">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Driver's Name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Bus No.</th>
                                    <th scope="col">Routine</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transport as $trans)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <div class="driver-info">
                                                <div class="driver-avatar text-capitalize">
                                                    {{ substr($trans->driver_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-capitalize fw-bold">{{ucwords(strtolower($trans->driver_name))}}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if(strtolower($trans->gender[0]) === 'm')
                                                <span class="gender-badge male" title="Male">M</span>
                                            @elseif(strtolower($trans->gender[0]) === 'f')
                                                <span class="gender-badge female" title="Female">F</span>
                                            @else
                                                <span class="gender-badge" title="Other">{{$trans->gender[0]}}</span>
                                            @endif
                                        </td>
                                        <td>{{$trans->phone}}</td>
                                        <td class="text-center">
                                            <span class="bus-badge">
                                                <i class="fas fa-bus me-1"></i> {{$trans->bus_no}}
                                            </span>
                                        </td>
                                        <td class="text-capitalize">{{$trans->routine}}</td>
                                        <td>
                                            @if ($trans->status == 1)
                                                <span class="badge-success-custom">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            @else
                                                <span class="badge-danger-custom">
                                                    <i class="fas fa-ban me-1"></i> Blocked
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <ul class="action-list">
                                                @if ($trans->status == 1)
                                                    <li>
                                                        <a href="{{route('students.transport', ['trans' => Hashids::encode($trans->id)])}}" class="btn btn-info-custom">
                                                            <i class="fas fa-users me-1"></i> Students
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('transport.edit', ['trans' => Hashids::encode($trans->id)])}}" class="btn btn-warning-custom">
                                                            <i class="fas fa-edit me-1"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{route('transport.update', ['trans' => Hashids::encode($trans->id)])}}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-danger-custom" onclick="return confirm('Are you sure you want to block this bus route?')">
                                                                <i class="fas fa-ban me-1"></i> Block
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{route('transport.restore', ['trans' => Hashids::encode($trans->id)])}}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-success-custom" onclick="return confirm('Are you sure you want to unblock this bus route?')">
                                                                <i class="fas fa-undo me-1"></i> Unblock
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('transport.remove', ['trans' => Hashids::encode($trans->id)])}}" class="btn btn-danger-custom" onclick="return confirm('Are you sure you want to delete this bus route permanently?')">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
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

    <!-- Bus Registration Modal -->
    <div class="modal fade" id="busModal" tabindex="-1" role="dialog" aria-labelledby="busModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header-custom">
                    <h5 class="modal-title">
                        <i class="fas fa-bus me-2"></i> Bus Registration Form
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('Transportation.store')}}" method="POST" enctype="multipart/form-data" id="busForm">
                        @csrf

                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <label for="fullname" class="form-label">
                                        <i class="fas fa-user text-primary"></i>
                                        Driver Name <span class="required-star">*</span>
                                    </label>
                                    <input type="text" name="fullname" class="form-control-custom" id="fullname" placeholder="Driver Full Name" value="{{old('fullname')}}" required>
                                    <div class="invalid-feedback">
                                        Please provide driver's name
                                    </div>
                                    @error('fullname')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label for="gender" class="form-label">
                                        <i class="fas fa-venus-mars text-primary"></i>
                                        Gender <span class="required-star">*</span>
                                    </label>
                                    <select name="gender" id="gender" class="form-control-custom" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select gender
                                    </div>
                                    @error('gender')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone text-primary"></i>
                                        Mobile Phone <span class="required-star">*</span>
                                    </label>
                                    <input type="text" name="phone" class="form-control-custom" id="phone" placeholder="Phone Number" required value="{{old('phone')}}">
                                    <div class="invalid-feedback">
                                        Please provide phone number
                                    </div>
                                    @error('phone')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <label for="bus" class="form-label">
                                        <i class="fas fa-bus-alt text-primary"></i>
                                        Bus Number <span class="required-star">*</span>
                                    </label>
                                    <input type="text" name="bus" class="form-control-custom" placeholder="Bus number" id="bus" value="{{old('bus')}}" required>
                                    <div class="invalid-feedback">
                                        Please provide bus number
                                    </div>
                                    @error('bus')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-8 mb-4">
                                    <label for="routine" class="form-label">
                                        <i class="fas fa-route text-primary"></i>
                                        Bus Routine Description
                                    </label>
                                    <textarea name="routine" id="routine" rows="3" cols="50" class="form-control-custom" placeholder="Describe the bus route">{{old('routine')}}</textarea>
                                    @error('routine')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" id="saveButton" class="btn btn-success-custom">
                                <i class="fas fa-save me-2"></i> Save Route
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("busForm");
            const submitButton = document.getElementById("saveButton");

            if (form && submitButton) {
                form.addEventListener("submit", function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();

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
                    submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Saving...`;
                });
            }

            // Reset form when modal is closed
            $('#busModal').on('hidden.bs.modal', function () {
                if (form) {
                    form.reset();
                    form.classList.remove("was-validated");
                }
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-2"></i> Save Route';
                }
            });
        });
    </script>
@endsection
