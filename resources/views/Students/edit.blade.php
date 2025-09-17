@extends('SRTDashboard.frame')
@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .header-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }

        .avatar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar i {
            font-size: 6rem;
            color: #dddfeb;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            color: #495057 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        .note-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .avatar {
                width: 120px;
                height: 120px;
            }

            .avatar i {
                font-size: 4rem;
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
                            @php
                                $imageName = $students->image;
                                $imagePath = public_path('assets/img/students/' . $imageName);

                                if (!empty($imageName) && file_exists($imagePath)) {
                                    $avatarImage = asset('assets/img/students/' . $imageName);
                                } else {
                                    $avatarImage = asset('assets/img/students/student.jpg');
                                }
                            @endphp
                            <div class="avatar">
                                <img src="{{ $avatarImage }}" alt="Student Image">
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
