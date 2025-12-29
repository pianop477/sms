@extends('SRTDashboard.frame')

@section('content')
    <!DOCTYPE html>
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

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            padding: 15px 10px;
            font-weight: 600;
            vertical-align: middle;
        }

        .table td {
            padding: 15px 10px;
            vertical-align: middle;
        }

        .table-success thead {
            background-color: var(--success-color);
            color: white;
        }

        .table-primary thead {
            background-color: var(--primary-color);
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
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

        .action-buttons a,
        .action-buttons button {
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

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .class-link {
            color: var(--dark-color);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .class-link:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .alert-warning {
            background-color: #fcf8e3;
            border-color: #faebcc;
            color: #8a6d3b;
            border-radius: 8px;
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
    </style>
    <div class="py-4">
        <div class="row">
            <!-- Classes List Section -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 class="header-title">Classes List</h4>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" class="btn btn-info btn-action float-right" data-bs-toggle="modal"
                                    data-bs-target="#addClassModal">
                                    <i class="fas fa-plus me-1"></i> New Class
                                </button>
                            </div>
                        </div>

                        <!-- Classes Table -->
                        <div class="single-table">
                            <div class="table-responsive">
                                <table class="table table-hover table-success">
                                    <thead>
                                        <tr>
                                            <th scope="col">Class Name</th>
                                            <th scope="col">Class Code</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($classes->isEmpty())
                                            <tr>
                                                <td colspan="3" class="text-center py-4">
                                                    <div class="alert alert-warning mb-0">
                                                        <i class="fas fa-exclamation-triangle me-2"></i> No classes records
                                                        found.
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            @foreach ($classes as $class)
                                                <tr>
                                                    <td class="text-uppercase fw-bold">
                                                        <i class="fas fa-angle-double-right me-2 text-success"></i>
                                                        {{ $class->class_name }}
                                                    </td>
                                                    <td class="text-uppercase">{{ $class->class_code }}</td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            @if ($class->status == 1)
                                                                <form
                                                                    action="{{ route('Classes.block', ['id' => Hashids::encode($class->id)]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button class="btn btn-sm btn-warning" type="submit"
                                                                        title="Disable"
                                                                        onclick="return confirm('Are you sure you want to disable this class?')">
                                                                        <i class="ti-na"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <form
                                                                    action="{{ route('Classes.unblock', ['id' => Hashids::encode($class->id)]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button class="btn btn-sm btn-success" type="submit"
                                                                        title="Enable"
                                                                        onclick="return confirm('Are you sure you want to enable this class?')">
                                                                        <i class="fas fa-refresh"></i>
                                                                    </button>
                                                                </form>
                                                                <form
                                                                    action="{{ route('Classes.destroy', ['id' => Hashids::encode($class->id)]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-sm btn-danger" type="submit"
                                                                        title="Delete"
                                                                        onclick="return confirm('Are you sure you want to delete this class Permanently?')">
                                                                        <i class="ti-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif

                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Class Teachers Section -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4 class="header-title">Class Teachers by Classes</h4>
                            </div>
                        </div>

                        @if ($classes->isEmpty())
                            <div class="alert alert-warning text-center py-4">
                                <i class="fas fa-exclamation-triangle me-2"></i> No classes records found.
                            </div>
                        @else
                            <div class="single-table">
                                <div class="table-responsive">
                                    <table class="table table-hover table-primary">
                                        <thead>
                                            <tr>
                                                <th scope="col">Classes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($classes as $class)
                                                <tr>
                                                    <td class="text-uppercase fw-bold">
                                                        <a href="{{ route('Class.Teachers', ['class' => Hashids::encode($class->id)]) }}"
                                                            class="class-link">
                                                            <i class="fas fa-angle-double-right me-2 text-primary"></i>
                                                            {{ $class->class_name }} - {{ $class->class_code }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Class Modal -->
    <div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClassModalLabel">Register New Class</h5>
                    <button type="button" class="btn btn-xs btn-danger" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fas fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{ route('Classes.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Class Name</label>
                                <input type="text" required name="name" class="form-control-custom text-uppercase"
                                    id="name" placeholder="Class Name" value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger small">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Class Code</label>
                                <input type="text" required name="code" class="form-control-custom text-uppercase"
                                    id="code" placeholder="Class Code" value="{{ old('code') }}">
                                @error('code')
                                    <div class="text-danger small">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="saveButton" class="btn btn-success"><i class="fas fa-save"></i>
                        Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton");

            if (!form || !submitButton) return;

            form.addEventListener("submit", function(event) {
                event.preventDefault();

                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...`;

                // Check form validity
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = "Save Class";
                    return;
                }

                // Delay submission to show loading state
                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
@endsection
