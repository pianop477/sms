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
            z-index: 1;
            font-size: 24px;
        }

        .card-body {
            padding: 10px;
            position: relative;
            /* z-index: 1; */
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #3f43b5 0%, #7a80f9 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            color: #856404;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-warning-custom:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        .driver-info {
            background: linear-gradient(135deg, rgba(78, 84, 200, 0.1) 0%, rgba(143, 148, 251, 0.1) 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid var(--primary);
        }

        .driver-name {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .bus-details {
            color: #6c757d;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .bus-badge {
            background: linear-gradient(135deg, var(--info) 0%, #17a2b8 100%);
            color: white;
            border-radius: 50px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .transfer-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 20px;
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

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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

        .student-avatar {
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

        .student-info {
            display: flex;
            align-items: center;
        }

        .gender-badge {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            border-radius: 50px;
            padding: 6px 12px;
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

        .class-badge {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            border-radius: 50px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 12px;
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

        .checkbox-custom {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 2px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s;
        }

        .checkbox-custom:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .select-all-container {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .select-all-label {
            font-weight: 600;
            color: var(--dark);
            margin: 0;
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

            .bus-details {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .student-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .student-avatar {
                margin-right: 0;
                margin-bottom: 8px;
            }
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
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
    </style>

    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title text-white">
                            <i class="fas fa-users me-2"></i> Student Transport Management
                        </h4>
                        <p class="mb-0 text-white">Manage student assignments to school buses</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('Transportation.index')}}" class="btn btn-primary-custom float-right">
                            <i class="fas fa-arrow-circle-left me-2"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-bus-alt floating-icons"></i>
            </div>

            <div class="card-body">
                <!-- Driver Information -->
                <div class="driver-info">
                    <h5 class="driver-name text-capitalize">
                        <i class="fas fa-user-tie"></i> Driver: {{$transport->driver_name}}
                    </h5>
                    <div class="bus-details">
                        <span class="bus-badge">
                            <i class="fas fa-bus"></i> Bus #{{$transport->bus_no}}
                        </span>
                        <span><i class="fas fa-phone text-primary"></i> {{$transport->phone}}</span>
                        <span class="text-capitalize"><i class="fas fa-route text-primary"></i> {{$transport->routine}}</span>
                    </div>
                </div>

                <!-- Transfer Section -->
                <div class="transfer-section">
                    <form action="{{route('update.transport.batch')}}" novalidate class="needs-validation" method="POST" id="transportForm">
                        @csrf
                        <input type="hidden" name="current_bus" value="{{$transport->id}}">

                        <div class="row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label for="new_bus" class="form-label">
                                    <i class="fas fa-exchange-alt text-primary"></i>
                                    Transfer Students routine <span class="required-star">*</span>
                                </label>
                                <select name="new_bus" id="new_bus" class="form-control-custom text-capitalize" required>
                                    <option value="">-- Select School Bus --</option>
                                    @if ($AllBuses->isEmpty())
                                        <option value="" disabled class="text-danger">No other school buses available</option>
                                    @else
                                        @foreach ($AllBuses as $bus)
                                            <option value="{{$bus->id}}">Bus #{{$bus->bus_no}} - {{$bus->driver_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    Please select a bus
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <button type="submit" class="btn btn-warning-custom" onclick="return confirm('Are you sure you want to move selected students to the new school bus?')">
                                    <i class="fas fa-random me-2"></i> Transfer Students
                                </button>
                            </div>

                            <div class="col-md-2 mb-3 text-end">
                                @if ($students->isNotEmpty())
                                    <a href="{{route('transport.export', ['trans' => Hashids::encode($transport->id)])}}"
                                       target="_blank"
                                       class="btn btn-primary-custom">
                                        <i class="fas fa-cloud-download-alt me-2"></i> Export
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Students Table -->
                        @if($students->isEmpty())
                            <div class="empty-state">
                                <i class="fas fa-user-graduate"></i>
                                <p>No students assigned to this bus</p>
                            </div>
                        @else
                            <div class="table-container">
                                <div class="table-responsive">
                                    <table class="table table-custom table-responsive-md" id="myTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th scope="col" class="text-center">
                                                    <div class="select-all-container">
                                                        <input type="checkbox" id="selectAll" class="checkbox-custom">
                                                        <label for="selectAll" class="select-all-label">All</label>
                                                    </div>
                                                </th>
                                                <th scope="col">Student Name</th>
                                                <th scope="col">Gender</th>
                                                <th scope="col">Class</th>
                                                <th scope="col">Stream</th>
                                                <th scope="col">Parent Phone</th>
                                                <th scope="col">Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($students as $student)
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="student[]" value="{{$student->id}}" class="checkbox-custom student-checkbox">
                                                        <span class="text-muted">{{$loop->iteration}}</span>
                                                    </td>
                                                    <td>
                                                        <div class="student-info">
                                                            <div class="student-avatar text-capitalize">
                                                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold text-capitalize">{{ucwords(strtolower($student->first_name))}} {{ucwords(strtolower($student->middle_name))}} {{ucwords(strtolower($student->last_name))}}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if(strtolower($student->gender[0]) === 'm')
                                                            <span class="gender-badge male" title="Male">M</span>
                                                        @elseif(strtolower($student->gender[0]) === 'f')
                                                            <span class="gender-badge female" title="Female">F</span>
                                                        @else
                                                            <span class="gender-badge" title="Other">{{$student->gender[0]}}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="class-badge text-uppercase">{{$student->class_code}}</span>
                                                    </td>
                                                    <td class="text-uppercase">{{$student->group}}</td>
                                                    <td>{{$student->parent_phone ?? 'N/A'}}</td>
                                                    <td class="text-capitalize">{{ucwords(strtolower($student->address)) ?? 'N/A'}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Select all functionality
            const selectAll = document.getElementById('selectAll');
            const studentCheckboxes = document.querySelectorAll('.student-checkbox');

            selectAll.addEventListener('change', function() {
                studentCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Form validation and submission handling
            const form = document.getElementById("transportForm");
            const submitButton = form.querySelector('button[type="submit"]');

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

                    // Check if at least one student is selected
                    const selectedStudents = form.querySelectorAll('input[name="student[]"]:checked');
                    if (selectedStudents.length === 0) {
                        event.preventDefault();
                        alert('Please select at least one student to transfer.');
                        return;
                    }

                    submitButton.disabled = true;
                    submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Transferring...`;
                });
            }

            // Reset button state if validation fails
            form.addEventListener('invalid', function() {
                setTimeout(() => {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-random me-2"></i> Transfer Students';
                    }
                }, 1000);
            }, true);
        });
    </script>
@endsection
