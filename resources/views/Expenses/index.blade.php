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

        .badge-status {
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
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="row mb-4">
                            <div class="col-md-10">
                                <h4 class="header-title">Expenses Catogies</h4>
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-primary btn-action float-right" data-bs-toggle="modal" data-bs-target="#addExamModal">
                                    <i class="fas fa-circle-plus me-1"></i> Add Category
                                </button>
                            </div>
                        </div>

                        <!-- Exams Table -->
                        <div class="single-table">
                            <div class="table-responsive">
                                <table class="table table-hover progress-table" id="myTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Category Type</th>
                                            <th scope="col" class="">Description</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (empty($categories))
                                            <tr>
                                                <td colspan="4" class="text-center">No Expense Categories Found.</td>
                                            </tr>
                                        @else
                                            @foreach ($categories as $row)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td class="fw-medium">{{ucwords(strtolower($row['expense_type']))}}</td>
                                                <td class="fw-bold" title="{{ $row['expense_description'] }}">{{ \Str::limit($row['expense_description'] ?? 'N/A', 60) }}</td>
                                                <td>
                                                    <ul class="d-flex justify-content-center">
                                                        <li class="mr-3">
                                                            <a href="{{route('expense.edit', ['category' => Hashids::encode($row['id'])])}}" style="border-radius: 15px" class="btn btn-sm btn-primary text-center" title="Edit">
                                                                <i class="ti-pencil"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{route('expenses.destroy', ['expenseCategory' => Hashids::encode($row['id'])])}}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger text-center" title="Delete" style="border-radius: 15px" onclick="return confirm('Are you sure you want to delete this category?')">
                                                                    <i class="ti-trash"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>

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
        </div>
    </div>

    <!-- Add Exam Modal -->
    @if (Route::has('exams.store'))
    <div class="modal fade" id="addExamModal" tabindex="-1" aria-labelledby="addExamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExamModalLabel">Register New Expense</h5>
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close"> Close</button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('expenses.store')}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Expense Type</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Expense Type" required>
                                @error('name')
                                <div class="text-danger small">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="abbreviation" class="form-label">Description</label>
                                <textarea name="description" class="form-control" id="abbreviation" placeholder="Expense Description" required></textarea>
                                @error('description')
                                <div class="text-danger small">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="saveButton" class="btn btn-success">Save </button>
                </div>
            </form>
            </div>
        </div>
    </div>
    @endif
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

                // Check form validity
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false;
                    submitButton.innerHTML = "Save";
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
