<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
            --info-color: #17a2b8;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            background: white;
            margin-bottom: 30px;
        }

        .card-header-custom {
            background: linear-gradient(120deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 30px;
            border-bottom: 0;
        }

        .header-title {
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
        }

        .back-button {
            transition: var(--transition);
            font-size: 1.8rem;
            color: white !important;
        }

        .back-button:hover {
            transform: scale(1.1);
        }

        .card-body {
            padding: 30px;
        }

        .avatar-container {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }

        .avatar {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: var(--transition);
        }

        .avatar:hover {
            transform: scale(1.03);
        }

        .camera-icon {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: var(--secondary-color);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition);
            border: 3px solid white;
        }

        .camera-icon:hover {
            background: var(--primary-color);
            transform: scale(1.1);
        }

        .student-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .student-name {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .student-admission {
            color: var(--dark-color);
            font-weight: 500;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: var(--secondary-color);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .form-control, .form-select, .select2-container .select2-selection--single {
            border-radius: 8px;
            padding: 12px 15px;
            border: 2px solid #ddd;
            transition: var(--transition);
            font-size: 1rem;
            height: auto;
        }

        .form-control:focus, .form-select:focus, .select2-container--focus .select2-selection--single {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        .text-danger {
            font-weight: 500;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .note-text {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-top: 5px;
        }

        .btn-save {
            background: linear-gradient(120deg, var(--success-color), #2ecc71);
            border: none;
            color: white;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: var(--transition);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 30px auto 0;
            width: 250px;
        }

        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(39, 174, 96, 0.6);
        }

        .btn-save i {
            margin-right: 8px;
        }

        .btn-back {
            background: linear-gradient(120deg, var(--info-color), #2D9CDB);
            border: none;
            color: white;
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 50px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            color: white;
        }

        .btn-back i {
            margin-right: 8px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 0 !important;
            color: #495057 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 1.5rem;
            }

            .avatar {
                width: 150px;
                height: 150px;
            }

            .btn-back {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }

        .spinner-border {
            width: 1.2rem;
            height: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="header-title"><i class="fas fa-user-edit me-2"></i>Edit Student Information</h1>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('create.selected.class', ['class' => Hashids::encode($students->grade_class_id)])}}" class="btn-back">
                            <i class="fas fa-arrow-circle-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Student Avatar -->
                <div class="d-flex justify-content-center">
                    <div class="avatar-container">
                        @php
                            $imageName = $students->image;
                            $imagePath = public_path('assets/img/students/' . $imageName);

                            if (!empty($imageName) && file_exists($imagePath)) {
                                $avatarImage = asset('assets/img/students/' . $imageName);
                            } else {
                                $avatarImage = asset('assets/img/students/student.jpg');
                            }
                        @endphp
                        <img src="{{ $avatarImage }}" alt="Student Image" class="avatar shadow">
                        <div class="camera-icon" onclick="document.getElementById('image').click()">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                </div>

                <div class="student-info">
                    <h2 class="student-name">{{ $students->first_name }} {{ $students->last_name }}</h2>
                    <p class="student-admission">Admission No: {{ $students->admission_number }}</p>
                </div>

                <form class="needs-validation" novalidate action="{{route('students.update.records', ['students' => Hashids::encode($students->id)])}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h3 class="section-title"><i class="fas fa-user-circle"></i> Personal Information</h3>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="fname" class="form-label">First Name</label>
                            <input type="text" name="fname" class="form-control text-capitalize" id="fname" value="{{$students->first_name}}" required>
                            @error('fname')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle" class="form-label">Middle Name</label>
                            <input type="text" name="middle" class="form-control text-capitalize" id="middle" value="{{$students->middle_name}}" required>
                            @error('middle')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="lname" class="form-label">Last Name</label>
                            <input type="text" name="lname" class="form-control text-capitalize" id="lname" value="{{$students->last_name}}" required>
                            @error('lname')
                                <div class="text-danger">{{$message}}</div>
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
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" id="dob" value="{{$students->dob}}" required min="{{\Carbon\Carbon::now()->subYears(17)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(2)->format('Y-m-d')}}">
                            @error('dob')
                                <div class="text-danger">{{$message}}</div>
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
                                        <option value="{{$bus->id}}">BUS # {{$bus->bus_no}}</option>
                                     @endforeach
                                    @endif
                                @else
                                    <option value="{{$students->transport_id}}" selected>Bus {{$students->bus_no}}</option>
                                    <option value="">Home Alone</option>
                                        @foreach ($buses as $bus)
                                            <option value="{{$bus->id}}">BUS # {{$bus->bus_no}}</option>
                                        @endforeach
                                @endif
                            </select>
                            <div class="note-text">Select if using/change school bus</div>
                            @error('driver')
                            <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                    </div>

                    <h3 class="section-title mt-5"><i class="fas fa-graduation-cap"></i> Academic Information</h3>

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
                            <div class="text-danger">{{$message}}</div>
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
                            <div class="text-danger">{{$message}}</div>
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
                            <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="image" class="form-label">Profile Photo</label>
                            <input type="file" name="image" class="form-control" id="image" accept="image/*">
                            <div class="note-text">Maximum 1MB - Blue background recommended</div>
                            @error('image')
                            <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                    </div>

                    <button class="btn btn-save" id="saveButton" type="submit">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
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

            // Image preview functionality
            const imageUpload = document.getElementById('image');
            const avatar = document.querySelector('.avatar');

            if (imageUpload && avatar) {
                imageUpload.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            avatar.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (!form || !submitButton) return;

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    return;
                }

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving Changes...`;

                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
</body>
</html>
