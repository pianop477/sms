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
            --gold: #ffd700;
            --teal: #33a4c6;
        }

        body {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin-top: 20px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .header-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .class-highlight {
            color: var(--gold);
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            text-transform: uppercase;
        }

        .year-highlight {
            color: var(--gold);
            font-weight: 600;
        }

        .card-section {
            border-radius: 15px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.7);
        }

        .card-header-custom {
            padding: 20px;
            font-weight: 700;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .gold-border {
            border-top: 5px solid var(--gold);
        }

        .teal-border {
            border-top: 5px solid var(--teal);
        }

        .gold-header {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #856404;
        }

        .teal-header {
            background: linear-gradient(135deg, var(--teal) 0%, #5bc0de 100%);
            color: white;
        }

        .card-body-custom {
            padding: 25px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 8px 16px;
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

        .btn-compile {
            background: linear-gradient(135deg, var(--teal) 0%, #5bc0de 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-compile:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(51, 164, 198, 0.3);
            color: white;
        }

        .instruction-text {
            color: var(--danger);
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 16px;
            position: relative;
            display: inline-block;
            background: rgba(220, 53, 69, 0.1);
            padding: 8px 16px;
            border-radius: 50px;
        }

        .exam-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .exam-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 15px;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
        }

        .exam-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        }

        .exam-link {
            text-decoration: none;
            color: var(--primary);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-grow: 1;
        }

        .exam-link:hover {
            color: var(--secondary);
        }

        .empty-state {
            text-align: center;
            padding: 30px 20px;
            background: linear-gradient(135deg, #fff9e6 0%, #ffeeb5 100%);
            border-radius: 10px;
            border-left: 4px solid var(--warning);
        }

        .empty-state i {
            font-size: 50px;
            color: var(--warning);
            margin-bottom: 15px;
        }

        .empty-state h6 {
            color: #856404;
            font-weight: 600;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 15px 20px;
        }

        .modal-title {
            font-weight: 700;
        }

        .close {
            color: white;
            opacity: 0.8;
        }

        .close:hover {
            color: white;
            opacity: 1;
        }

        .modal-body {
            padding: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .checkbox-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            background: #f8f9fa;
        }

        .checkbox-item {
            margin-bottom: 10px;
            padding: 8px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.3s;
        }

        .checkbox-item:hover {
            background: #f8f9fa;
            border-color: var(--primary);
        }

        .radio-option {
            margin-bottom: 12px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.3s;
        }

        .radio-option:hover {
            background: #e9ecef;
            border-color: var(--primary);
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
        }

        .table-custom tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr {
            transition: all 0.3s;
        }

        .table-custom tbody tr:hover {
            background-color: #f8f9fa;
        }

        .action-list {
            display: flex;
            gap: 15px;
            justify-content: center;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .action-link {
            color: var(--dark);
            font-size: 18px;
            transition: all 0.3s;
        }

        .action-link:hover {
            transform: translateY(-3px);
        }

        .view-link:hover {
            color: var(--primary);
        }

        .download-link:hover {
            color: var(--info);
        }

        .publish-link:hover {
            color: var(--success);
        }

        .delete-link:hover {
            color: var(--danger);
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .alert-custom {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        @media (max-width: 992px) {
            .action-list {
                flex-direction: column;
                gap: 10px;
            }
        }

        .floating-icons {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 50px;
            opacity: 0.1;
            color: white;
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
