@extends('SRTDashboard.frame')
@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
    <div class="py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body p-5">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 class="header-title">Edit Student Information</h4>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{route('create.selected.class', ['class' => Hashids::encode($students->grade_class_id)])}}" class="btn btn-info float-right">
                                    <i class="fas fa-arrow-circle-left me-2"></i> Back
                                </a>
                            </div>
                        </div>

                        <!-- Student Avatar -->
                        <div class="avatar-container mb-4">
                            <div class="avatar">
                                @if (!empty($students->image))
                                    <img src="{{ asset('assets/img/students/' . $students->image) }}" alt="Student Image">
                                @else
                                    <i class="fas fa-user-graduate"></i>
                                @endif
                            </div>
                            <div class="mt-3 text-center">
                                <h5 class="mb-0 text-uppercase">{{ $students->first_name }} {{ $students->last_name }}</h5>
                                <p class="text-muted text-uppercase">Admission No: {{ $students->admission_number }}</p>
                            </div>
                        </div>

                        <!-- Edit Form -->
                        <form class="needs-validation" novalidate action="{{route('students.update.records', ['students' => Hashids::encode($students->id)])}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" name="fname" class="form-control text-capitalize" id="fname" value="{{$students->first_name}}" required>
                                    @error('fname')
                                        <div class="text-danger small mt-1">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="middle" class="form-label">Middle Name</label>
                                    <input type="text" name="middle" class="form-control text-capitalize" id="middle" value="{{$students->middle_name}}" required>
                                    @error('middle')
                                        <div class="text-danger small mt-1">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" name="lname" class="form-control text-capitalize" id="lname" value="{{$students->last_name}}" required>
                                    @error('lname')
                                        <div class="text-danger small mt-1">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select name="gender" class="form-control text-capitalize" id="gender" required>
                                        <option value="{{$students->gender}}" selected>{{ucfirst($students->gender)}}</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    @error('gender')
                                        <div class="text-danger small mt-1">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control" id="dob" value="{{$students->dob}}" required min="{{\Carbon\Carbon::now()->subYears(17)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(2)->format('Y-m-d')}}">
                                    @error('dob')
                                        <div class="text-danger small mt-1">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="driver" class="form-label">Bus Number</label>
                                    <select name="driver" id="driver" class="form-control text-capitalize">
                                        <option value="">-- Home Alone --</option>
                                        @if ($students->transport == NULL)
                                            @if ($buses->isEmpty())
                                                <option value="" class="text-danger">No buses found</option>
                                            @else
                                             @foreach ($buses as $bus )
                                                <options value="">Home Alone</option>
                                                 <option value="{{$bus->id}}">BUS # {{$bus->bus_no}}</option>
                                             @endforeach
                                            @endif
                                        @else
                                            <option value="{{$students->transport_id}}" selected>Bus {{$students->bus_no}}</option>
                                            <options value="">Home Alone</option>
                                                @foreach ($buses as $bus)
                                                    <option value="{{$bus->id}}">BUS # {{$bus->bus_no}}</option>
                                                @endforeach
                                        @endif
                                    </select>
                                    <div class="note-text">Select if using/change school bus</div>
                                    @error('driver')
                                    <div class="text-danger small mt-1">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="parentSelect" class="form-label">Parent/Guardian</label>
                                    <select name="parent" id="parentSelect" class="form-control select2 text-capitalize" required>
                                        <option value="{{$parents->parent_id}}" selected>{{ucwords(strtolower($parents->first_name))}} {{ucwords(strtolower($parents->last_name))}} - {{$parents->phone}}</option>
                                        @foreach ($allParents as $parent)
                                            <option value="{{$parent->parent_id}}">
                                                {{ucwords(strtolower($parent->first_name))}} {{ucwords(strtolower($parent->last_name))}} - {{$parent->phone}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent')
                                    <div class="text-danger small mt-1">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="class" class="form-label">Class</label>
                                    <select name="class" id="class" class="form-control text-uppercase" required>
                                        <option value="{{$students->class_id}}">{{$students->class_name}}</option>
                                        @if ($classes->isEmpty())
                                            <option value="" class="text-danger">No classes found</option>
                                        @else
                                            @foreach ($classes as $class)
                                                <option value="{{$class->id}}">{{$class->class_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('class')
                                    <div class="text-danger small mt-1">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="group" class="form-label">Stream</label>
                                    <select name="group" id="group" required class="form-control">
                                        <option value="{{$students->group}}" selected>Stream {{strtoupper($students->group)}}</option>
                                        <option value="a">Stream A</option>
                                        <option value="b">Stream B</option>
                                        <option value="c">Stream C</option>
                                    </select>
                                    @error('group')
                                    <div class="text-danger small mt-1">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="image" class="form-label">Profile Photo</label>
                                    <input type="file" name="image" class="form-control" id="image" accept="image/*">
                                    <div class="note-text">Maximum 1MB - Blue background recommended</div>
                                    @error('image')
                                    <div class="text-danger small mt-1">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <button class="btn btn-success" id="saveButton" type="submit">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('#parentSelect').select2({
                    placeholder: "Search Parent...",
                    allowClear: true,
                    width: '100%'
                }).on('select2:open', function () {
                    $('.select2-results__option').css('text-transform', 'capitalize');
                });
            } else {
                console.error("Select2 is not loaded!");
            }

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
                    submitButton.innerHTML = `<i class="fas fa-save me-2"></i> Save Changes`;
                    return;
                }

                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
@endsection
