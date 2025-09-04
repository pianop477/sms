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
    <div class="">
        <!-- Header Card -->
        <div class="header-card">
            <h3><i class="fas fa-chart-line me-2"></i> Results for <span class="class-highlight">{{strtoupper($classes->class_code)}}</span> - <span class="year-highlight">{{$year}}</span></h3>
        </div>

        <div class="row">
            <!-- Single Results Section -->
            <div class="col-lg-5 col-md-6 mb-4">
                <div class="card-section gold-border">
                    <div class="card-header-custom gold-header">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> Single Results Reports</h5>
                        <a href="{{route('results.classesByYear', ['school' => Hashids::encode($schools->id), 'year'=>$year])}}" class="btn btn-info btn-xs text-white">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                    <div class="card-body-custom">
                        <p class="instruction-text"><i class="fas fa-mouse-pointer me-2"></i> Select Examination type to view results</p>

                        @if ($groupedByExamType->isEmpty())
                            <div class="empty-state">
                                <i class="fas fa-exclamation-triangle"></i>
                                <h6> No Result Records Found</h6>
                            </div>
                        @else
                            <div class="exam-list">
                                @foreach ($groupedByExamType as $exam_type_id => $results )
                                    <div class="exam-item">
                                        <a href="{{ route('results.monthsByExamType', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classes->id), 'examType' => Hashids::encode($exam_type_id)]) }}" class="exam-link text-uppercase">
                                            <i class="fas fa-chevron-right"></i>
                                            {{ $results->first()->exam_type }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Compiled Results Section -->
            <div class="col-lg-7 col-md-6 mb-4">
                <div class="card-section teal-border">
                    <div class="card-header-custom teal-header">
                        <h5 class="mb-0"><i class="fas fa-copy me-2"></i> Compiled Results Reports</h5>
                        <button type="button" class="btn btn-compile" data-bs-toggle="modal" data-bs-target="#compileModal">
                            <i class="fas fa-plus me-1"></i> Compile Results
                        </button>
                    </div>
                    <div class="card-body-custom">
                        <!-- Alerts -->
                        @if (Session::has('success'))
                            <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-check-circle me-2"></i>Success!</strong> {{ Session::get('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (Session::has('error'))
                            <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-circle me-2"></i>Error!</strong> {{ Session::get('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Results Table -->
                        <div class="table-container">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Issued at</th>
                                        <th>Issued by</th>
                                        <th class="text-center">Display Mode</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($reports->isEmpty())
                                        <tr>
                                            <td colspan="5" class="text-center text-danger py-4">
                                                <i class="fas fa-inbox me-2"></i> No compiled results found
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($reports as $report)
                                            <tr>
                                                <td class="text-capitalize fw-bold">{{ $report->title }}</td>
                                                <td>{{ \Carbon\Carbon::parse($report->created_at)->format('d-m-Y') }}</td>
                                                <td>{{ ucwords(strtolower($report->first_name. '. '.  $report->last_name[0])) }}</td>
                                                <td class="text-capitalize text-center"><span class="badge bg-info text-white">{{$report->combine_option}}</span></td>
                                                <td>
                                                    <ul class="action-list">
                                                        <li>
                                                            <a href="{{route('students.combined.report', [
                                                            'school' => Hashids::encode($report->school_id), 'year' => $year,
                                                            'class' => Hashids::encode($report->class_id), 'report' => Hashids::encode($report->id)])}}"
                                                            class="action-link view-link" title="Student Report">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{route('download.general.combined', ['school' => Hashids::encode($report->school_id), 'year' => $year, 'class' => Hashids::encode($report->class_id), 'report' => Hashids::encode($report->id)])}}"
                                                            onclick="return confirm('Are you sure you want to download this report?')"
                                                            class="action-link download-link" title="Download Report">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            @if ($report->status === 0)
                                                                <form action="{{route('publish.combined.report', ['school' => Hashids::encode($report->school_id), 'year' => $year, 'class' => Hashids::encode($report->class_id), 'report' => Hashids::encode($report->id)])}}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button class="btn btn-link p-0 action-link" type="submit" title="Publish report" onclick="return confirm('Are you sure you want to Publish this report?')">
                                                                        <i class="fas fa-toggle-off text-secondary"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <form action="{{route('Unpublish.combined.report', ['school' => Hashids::encode($report->school_id), 'year' => $year, 'class' => Hashids::encode($report->class_id), 'report' => Hashids::encode($report->id)])}}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button class="btn btn-link p-0 action-link publish-link" type="submit" title="Unpublish report" onclick="return confirm('Are you sure you want to Unpublish this report and Lock it?')">
                                                                        <i class="fas fa-toggle-on text-success"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </li>
                                                        <li>
                                                            <a href="{{route('generated.report.delete',
                                                                ['school' => Hashids::encode($report->school_id),
                                                                'year' =>$year , 'class' => Hashids::encode($report->class_id),
                                                                'report' => Hashids::encode($report->id)])}}"
                                                                onclick="return confirm('Are you sure you want to delete this report?')"
                                                                class="action-link delete-link" title="Delete Report">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($reports->hasPages())
                            <div class="pagination-container">
                                {{ $reports->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compile Results Modal -->
    <div class="modal fade" id="compileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-cogs me-2"></i> Combine Results Data Set</h5>
                    <button type="button" class="btn-close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <form class="needs-validation" novalidate action="{{route('submit.compiled.results', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classes->id)])}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="report-type" class="form-label">Report Type:</label>
                                <select name="exam_type" id="report-type" class="form-control form-control-custom text-capitalize" required>
                                    <option value="">--Select Report Type--</option>
                                    <option value="mid-term">Mid-Term Assessment</option>
                                    <option value="terminal">Terminal Assessment</option>
                                    <option value="annual">Annual Assessment</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3" id="custom-input-container" style="display: none;">
                                <label for="custom-report-type" class="form-label">Custom Report Type</label>
                                <input type="text" name="custom_exam_type" id="custom-report-type" class="form-control form-control-custom" placeholder="Enter Report Type Name">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="class-select" class="form-label">Class</label>
                                <select name="class_id" id="class-select" class="form-control form-control-custom text-uppercase">
                                    <option value="{{$classes->id}}" selected>{{$classes->class_name}}</option>
                                </select>
                                @error('class_id')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="term-select" class="form-label">Term</label>
                                <select name="term" id="term-select" class="form-control form-control-custom text-capitalize" required>
                                    <option value="" selected>--select term--</option>
                                    <option value="i">Term 1</option>
                                    <option value="ii">Term 2</option>
                                </select>
                                @error('class_id')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="text-danger fw-bold"><i class="fas fa-database me-2"></i> Examination Results Data Sets</p>
                                <hr>
                                @if ($groupedByMonth->isEmpty())
                                    <p class="text-danger">No exam results records found</p>
                                @else
                                    <div class="checkbox-container">
                                        @foreach ($groupedByMonth as $date => $resultDate)
                                            <div class="checkbox-item">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="exam_dates[]" value="{{$date}}" id="date-{{$date}}">
                                                    <label class="form-check-label" for="date-{{$date}}">
                                                        {{\Carbon\Carbon::parse($date)->format('d-m-Y')}} ==> {{ucwords(strtolower($resultDate->first()->exam_type))}}
                                                        @if ($resultDate->first()->Exam_term == 'i')
                                                            (Term 1)
                                                        @else
                                                            (Term 2)
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <p class="text-danger fw-bold"><i class="fas fa-chart-pie me-2"></i> Report Aggregation Method</p>
                                <div class="mt-3">
                                    <div class="radio-option">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="combine_option" id="option-sum" value="sum" required>
                                            <label class="form-check-label fw-bold" for="option-sum">
                                                Display Total Marks Only
                                            </label>
                                        </div>
                                    </div>

                                    <div class="radio-option">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="combine_option" id="option-average" value="average" required>
                                            <label class="form-check-label fw-bold" for="option-average">
                                                Display Average Score Only
                                            </label>
                                        </div>
                                    </div>

                                    <div class="radio-option">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="combine_option" id="option-individual" value="individual" required>
                                            <label class="form-check-label fw-bold" for="option-individual">
                                                Display Individual Subject Scores
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to generate compiled results?')">
                            <i class="fas fa-cog me-2"></i> Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Handle dynamic input for custom report type
        document.getElementById('report-type').addEventListener('change', function () {
            var customInputContainer = document.getElementById('custom-input-container');

            if (this.value === 'custom') {
                customInputContainer.style.display = 'block';
                document.getElementById('custom-report-type').setAttribute('required', 'required');
            } else {
                customInputContainer.style.display = 'none';
                document.getElementById('custom-report-type').removeAttribute('required');
            }
        });

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation');

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
    @endsection
