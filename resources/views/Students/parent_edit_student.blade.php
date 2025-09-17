@extends('SRTDashboard.frame')
@section('content')
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
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

        .card-header {
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
            font-size: 2.2rem;
        }

        .back-button:hover {
            transform: scale(1.1);
            color: white !important;
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

        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 15px;
            border: 2px solid #ddd;
            transition: var(--transition);
            font-size: 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        .text-danger {
            font-weight: 500;
            font-size: 0.9rem;
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

        .input-group {
            margin-bottom: 20px;
        }

        .info-text {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-top: 5px;
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
        }

        .spinner-border {
            width: 1.2rem;
            height: 1.2rem;
        }
    </style>
    <div class="main-container">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-10">
                        <h1 class="header-title"><i class="fas fa-user-edit me-2"></i>Edit Student Information</h1>
                    </div>
                    <div class="col-2 text-end">
                        <a href="{{route('home')}}" class="back-button">
                            <i class="fas fa-arrow-circle-left text-light"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form class="needs-validation" novalidate="" action="{{route('parent.update.student', ['students' => Hashids::encode($students->id)])}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                            <img src="{{ $avatarImage }}" alt="profile_image" class="avatar shadow">
                            <div class="camera-icon" onclick="document.getElementById('imageUpload').click()">
                                <i class="fas fa-camera"></i>
                            </div>
                            <input type="file" name="image" id="imageUpload" class="d-none" accept="image/*">
                        </div>
                    </div>

                    <h3 class="section-title mt-5"><i class="fas fa-user-circle"></i> Personal Information</h3>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom01" class="form-label">First Name</label>
                            <input type="text" name="fname" class="form-control text-capitalize" id="validationCustom01" value="{{$students->first_name}}" required>
                            @error('fname')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom02" class="form-label">Middle Name</label>
                            <input type="text" name="middle" class="form-control text-capitalize" id="validationCustom02" value="{{$students->middle_name}}" required>
                            @error('middle')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom03" class="form-label">Last Name</label>
                            <input type="text" name="lname" class="form-control text-capitalize" id="validationCustom03" value="{{$students->last_name}}" required>
                            @error('lname')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom04" class="form-label">Gender</label>
                            <select name="gender" id="validationCustom04" class='form-select text-capitalize'>
                                <option value="{{$students->gender}}">{{$students->gender}}</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            @error('gender')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom05" class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" id="validationCustom05" value="{{$students->dob}}" required min="{{\Carbon\Carbon::now()->subYears(17)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(2)->format('Y-m-d')}}">
                            @error('dob')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom06" class="form-label">Select Bus Number</label>
                            <select name="driver" id="validationCustom06" class="form-select text-capitalize">
                                <option value="">--Home Alone--</option>
                                @if ($students->transport == NULL)
                                    <option value="">-- Select Bus Number --</option>
                                    @if ($buses->isEmpty())
                                        <option value="" class="text-danger">No buses found</option>
                                    @else
                                        @foreach ($buses as $bus )
                                            <option value="{{$bus->id}}">bus {{$bus->bus_no}}</option>
                                        @endforeach
                                    @endif
                                @else
                                    <option value="{{$students->transport_id}}" selected>bus {{$students->bus_no}}</option>
                                    @if ($buses->isEmpty())
                                        <option value="" class="text-danger">No buses found</option>
                                    @else
                                        @foreach ($buses as $bus)
                                            <option value="{{$bus->id}}">bus {{$bus->bus_no}}</option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                            <div class="info-text">Select if using/changing school bus</div>
                            @error('driver')
                            <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                    </div>

                    <h3 class="section-title mt-5"><i class="fas fa-graduation-cap"></i> Academic Information</h3>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom07" class="form-label">Class</label>
                            <select name="class" id="validationCustom07" class="form-select text-uppercase" required>
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
                            <label for="validationCustom08" class="form-label">Stream</label>
                            <select name="group" id="validationCustom08" required class="form-select">
                                <option value="{{$students->group}}" selected>Stream {{$students->group}}</option>
                                <option value="a">Stream A</option>
                                <option value="b">Stream B</option>
                                <option value="c">Stream C</option>
                            </select>
                            @error('group')
                            <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validationCustom09" class="form-label">Photo</label>
                            <input type="file" name="image" class="form-control" id="validationCustom09" value="{{old('image')}}">
                            <div class="info-text">Maximum 1MB - Blue background preferred</div>
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            // Image preview functionality
            const imageUpload = document.getElementById('imageUpload');
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
@endsection
