@extends('SRTDashboard.frame')
    @section('content')
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --success: #28a745;
            --info: #17a2b8;
            --dark: #343a40;
            --light: #f8f9fa;
        }

        body {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px;
            position: relative;
        }

        .card-header-custom::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .header-title i {
            margin-right: 10px;
            font-size: 28px;
        }

        .card-body {
            padding: 30px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
            background-color: white;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            color: white;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:disabled {
            background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%);
            transform: none;
            box-shadow: none;
        }

        .invalid-feedback {
            font-weight: 500;
            padding: 5px 0;
            display: block;
        }

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }

            .btn-back {
                margin-top: 15px;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
        }
    </style>
    <div class="container">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <h4 class="header-title text-white"><i class="fas fa-edit"></i> Edit Examination Test</h4>
                    </div>
                    <div class="col-md-2">
                        <a href="{{route('exams.index')}}" class="btn btn-back btn-xs float-right">
                            <i class="fas fa-arrow-circle-left"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-graduation-cap floating-icons"></i>
            </div>
            <div class="card-body">
                <form class="needs-validation" novalidate="" action="{{route('exams.update', $exam->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <div class="col-md-6 mb-4">
                            <label for="validationCustom01" class="form-label">Examination Name</label>
                            <input type="text" name="name" class="form-control form-control-custom text-uppercase" id="validationCustom01" placeholder="Enter Examination Test" value="{{$exam->exam_type}}" required="">
                            @error('name')
                            <div class="invalid-feedback">
                                <span class="text-danger">{{$message}}</span>
                            </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="validationCustom02" class="form-label">Symbolic Abbreviation</label>
                            <input type="text" name="abbreviation" class="form-control form-control-custom text-uppercase" id="validationCustom02" placeholder="Examination Abbreviation" value="{{ $exam->symbolic_abbr}}" required="">
                            @error('abbreviation')
                            <div class="invalid-feedback">
                                <span class="text-danger">{{$message}}</span>
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="text-right mt-4">
                        <button class="btn btn-submit pulse-animation" id="saveButton" type="submit">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
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

                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Please Wait...`;
                submitButton.classList.remove("pulse-animation");

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = `<i class="fas fa-save me-2"></i>Save Changes`;
                    submitButton.classList.add("pulse-animation");
                    return;
                }

                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
    @endsection
