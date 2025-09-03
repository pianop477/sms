@extends('SRTDashboard.frame')
@section('content')
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .classic-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            margin-top: 30px;
        }

        .classic-card-header {
            background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #ddd;
            padding: 15px 20px;
            font-weight: bold;
            color: #333;
        }

        .classic-card-body {
            padding: 20px;
        }

        .classic-form-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }

        .classic-form-control {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 10px;
            font-size: 14px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .classic-form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .classic-btn {
            border-radius: 4px;
            padding: 8px 16px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .classic-btn-success {
            background: linear-gradient(to bottom, #28a745, #218838);
            color: white;
            border: 1px solid #1e7e34;
        }

        .classic-btn-success:hover {
            background: linear-gradient(to bottom, #218838, #1e7e34);
        }

        .classic-btn-secondary {
            background: linear-gradient(to bottom, #6c757d, #5a6268);
            color: white;
            border: 1px solid #545b62;
        }

        .classic-btn-secondary:hover {
            background: linear-gradient(to bottom, #5a6268, #545b62);
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: 4px;
            padding: 15px;
        }

        .text-danger {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        .header-title {
            color: #333;
            font-weight: bold;
            margin-bottom: 0;
        }

        .form-row {
            margin-bottom: 20px;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .file-input-info {
            font-size: 13px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="classic-card">
                    <div class="classic-card-header">
                        @if ($contract->remarks == NULL)
                            <h4 class="header-title"><i class="fas fa-edit me-2"></i>Edit Contract Application</h4>
                        @else
                            <h4 class="header-title"><i class="fas fa-redo-alt me-2"></i> Re-apply Contract Application</h4>
                        @endif
                    </div>
                    <div class="classic-card-body">
                        @if ($contract->remarks != NULL)
                            <div class="alert-danger mb-4">
                                <p class="mb-1"><strong><i class="fas fa-exclamation-circle me-1"></i>Application Status: </strong>{{$contract->status}}</p>
                                <p class="mb-0"><strong><i class="fas fa-comment me-1"></i>Reason: </strong>{{$contract->remarks}}</p>
                            </div>
                        @endif

                        <form class="needs-validation" novalidate="" action="{{route('contract.update', ['id' => Hashids::encode($contract->id)])}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="validationCustom01" class="classic-form-label">Contract type</label>
                                    <select name="contract_type" id="validationCustom01" required class="form-control classic-form-control">
                                        <option value="{{$contract->contract_type}}" selected>{{ucfirst($contract->contract_type)}} contract</option>
                                        <option value="new">New contract</option>
                                        <option value="probation">Probation Contract</option>
                                        <option value="renewal">Renew Contract</option>
                                    </select>
                                    @error('contract_type')
                                    <div class="text-danger">
                                        <span>{{$message}}</span>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="validationCustom02" class="classic-form-label">Application Letter</label>
                                    <input type="file" name="application_letter" class="form-control classic-form-control" id="validationCustom02" required="">
                                    <div class="file-input-info">Please upload your application letter in PDF format</div>
                                    @error('application_letter')
                                    <div class="text-danger">
                                       <span>{{$message}}</span>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <button class="btn classic-btn classic-btn-success" id="saveButton" type="submit">
                                        <i class="fas fa-save me-1"></i> Save Changes
                                    </button>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{route('contract.index')}}" class="btn classic-btn classic-btn-secondary float-right">
                                        <i class="fas fa-arrow-left me-1"></i> Go Back
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
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

                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Please Wait...`;

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = `<i class="fas fa-save me-1"></i>Save Changes`;
                    return;
                }

                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
@endsection
