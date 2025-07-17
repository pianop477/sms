@extends('SRTDashboard.frame')
    @section('content')
    <style>
        #available-options,
        #selected-options {
            height: 100px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 5px;
        }
        button#btn-add, button#btn-remove {
            width: 40px;
        }
    </style>
        <div class="col-md-12 mt-5">
            <div class="card col-md-12 text-center">
                <div class="card-body">
                    <h5>Results for {{strtoupper($classes->class_code)}} - {{$year}}</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 mt-2">
                    <div class="card" style="border-top: 5px solid gold;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <h4 class="header-title">Single results reports</h4>
                                </div>
                                <div class="col-4">
                                    <a href="{{route('results.classesByYear', ['school' => Hashids::encode($schools->id), 'year'=>$year])}}" class="float-right btn btn-warning btn-xs"><i class="fas fa-arrow-circle-left"></i> Back</a>
                                </div>
                            </div>
                            <p class="text-danger">Select Examination type to view results</p>
                            <div class="list-group">
                                @if ($groupedByExamType->isEmpty())
                                <div class="alert alert-warning text-center" role="alert">
                                    <h6>No Result Records found</h6>
                                </div>
                                @else
                                    @foreach ($groupedByExamType as $exam_type_id => $results )
                                        <a href="{{ route('results.monthsByExamType', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classes->id), 'examType' => Hashids::encode($exam_type_id)]) }}">
                                            <button type="button" class="list-group-item list-group-item-action">
                                                <h6 class="text-primary text-capitalize">>> {{ $results->first()->exam_type }}</h6>
                                            </button>
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 mt-2">
                    <div class="card" style="border-top: 5px solid #33a4c6;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <h4 class="header-title">Compiled Results Reports</h4>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-xs btn-info float-right" data-toggle="modal" data-target=".bd-example-modal-lg">
                                        Compile Results
                                    </button>
                                    <div class="modal fade bd-example-modal-lg">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Combine Results Data Set</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="needs-validation" novalidate="" action="{{route('submit.compiled.results', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classes->id)])}}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-row">
                                                            <div class="col-md-4">
                                                                <label for="validationCustom02">Report Type:</label>
                                                                <select name="exam_type" id="report-type" class="form-control text-capitalize" required>
                                                                    <option value="">--Select Report Type--</option>
                                                                    <option value="mid-term">Mid-Term Assessment</option>
                                                                    <option value="terminal">Terminal Assessment</option>
                                                                    <option value="annual">Annual Assessment</option>
                                                                    <option value="custom">Custom</option>
                                                                </select>
                                                            </div>
                                                            <!-- Placeholder for Dynamic Input Field -->
                                                            <div class="col-md-4" id="custom-input-container" style="display: none;">
                                                                <label for="custom-report-type">Custom Report Type</label>
                                                                <input type="text" name="custom_exam_type" id="custom-report-type" class="form-control" placeholder="Enter Report Type Name">
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <label for="validationCustom01">Class</label>
                                                                <select name="class_id" id="" class="form-control text-uppercase">
                                                                    <option value="{{$classes->id}}" selected>{{$classes->class_name}}</option>
                                                                </select>
                                                                @error('class_id')
                                                                <div class="invalid-feedback">
                                                                    {{$message}}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label for="validationCustom01">Term</label>
                                                                <select name="term" id="" class="form-control text-capitalize" required>
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
                                                        <div class="form-row">
                                                            <div class="col-md-12 mb-3">
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <p class="text-danger" style="font-weight: bold">Examination Results Data Sets</p>
                                                                        <hr>
                                                                        @if ($groupedByMonth->isEmpty())
                                                                            <p class="text-danger">No exam results records found</p>
                                                                        @else
                                                                            <div style="max-height: 170px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                                                                @foreach ($groupedByMonth as $date => $resultDate)
                                                                                    <div style="margin-bottom: 6px;">
                                                                                        <label>
                                                                                            <input type="checkbox" style="font-size: 16px;" name="exam_dates[]" value="{{$date}}">
                                                                                            {{\Carbon\Carbon::parse($date)->format('d-m-Y')}} ==> {{ucwords(strtolower($resultDate->first()->exam_type))}} ({{ucwords(strtolower('term '.$resultDate->first()->Exam_term))}})
                                                                                        </label>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <p class="text-danger fw-bold">Report Aggregation Method</p>
                                                                        <div class="mt-4">
                                                                            <input type="radio" name="combine_option" value="sum" required>
                                                                            <label>Display Total Marks Only</label> <br>

                                                                            <input type="radio" name="combine_option" value="average" required>
                                                                            <label>Display Average Score Only</label> <br>

                                                                            <input type="radio" name="combine_option" value="individual" required>
                                                                            <label>Display Individual Subject Scores</label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="clear" class="btn btn-danger" data-dismiss="modal">Clear</button>
                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to generate compiled results?')">Generate</button>
                                                </div>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @if (Session::has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success!</strong> {{ Session::get('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if (Session::has('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error!</strong> {{ Session::get('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <table class="table table-bordered table-responsive-md table-striped table-hover" id="myTable">
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
                                            <td colspan="5" class="text-center text-danger">No compiled results found</td>
                                        </tr>
                                    @else
                                        @foreach ($reports as $report)
                                            <tr>
                                                <td class="text-capitalize">{{ $report->title }}</td>
                                                <td>{{ \Carbon\Carbon::parse($report->created_at)->format('d-m-Y') }}</td>
                                                <td>{{ ucwords(strtolower($report->first_name. '. '.  $report->last_name[0])) }}</td>
                                                <td class="text-capitalize text-center">{{$report->combine_option}}</td>
                                                <td>
                                                    <ul class="d-flex justify-content-center">
                                                        <li class="mr-3">
                                                            <a href="{{route('students.combined.report', [
                                                            'school' => Hashids::encode($report->school_id), 'year' => $year,
                                                            'class' => Hashids::encode($report->class_id), 'report' => Hashids::encode($report->id)])}}" class="" title="Student Report"><i class="fas fa-eye"></i></a>
                                                        </li>
                                                        <li class="mr-3">
                                                            <a href="{{route('download.general.combined', ['school' => Hashids::encode($report->school_id), 'year' => $year, 'class' => Hashids::encode($report->class_id), 'report' => Hashids::encode($report->id)])}}" onclick="return confirm('Are you sure you want to download this report?')" class="" title="Download Report"><i class="fas fa-download text-info"></i></a>
                                                        </li>
                                                        <li class="mr-3">
                                                            @if ($report->status === 0)
                                                                <form action="{{route('publish.combined.report', ['school' => Hashids::encode($report->school_id), 'year' => $year, 'class' => Hashids::encode($report->class_id), 'report' => Hashids::encode($report->id)])}}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button class="btn btn-link p-0" type="submit" title="Publish report" onclick="return confirm('Are you sure you want to Publish this report?')">
                                                                        <i class="fas fa-toggle-off text-secondary" style="font-size: 20px;"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <form action="{{route('Unpublish.combined.report', ['school' => Hashids::encode($report->school_id), 'year' => $year, 'class' => Hashids::encode($report->class_id), 'report' => Hashids::encode($report->id)])}}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button class="btn btn-link p-0" type="submit" title="Publish report" onclick="return confirm('Are you sure you want to Unpublish this report and Lock it?')">
                                                                        <i class="fas fa-toggle-on text-success" style="font-size: 20px;"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </li>
                                                        <li class="mr-3">
                                                            <a href="{{route('generated.report.delete',
                                                                ['school' => Hashids::encode($report->school_id),
                                                                'year' =>$year , 'class' => Hashids::encode($report->class_id),
                                                                'report' => Hashids::encode($report->id)])}}"
                                                                onclick="return confirm('Are you sure you want to delete this report?')" title="Delete Report">
                                                                <i class="fas fa-trash text-danger"></i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            {{ $reports->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            //handle moving options between select elements**************************************
            document.addEventListener('DOMContentLoaded', function () {
                const btnAdd = document.getElementById('btn-add');
                const btnRemove = document.getElementById('btn-remove');
                const availableOptions = document.getElementById('available-options');
                const selectedOptions = document.getElementById('selected-options');

                // Move selected options from available to selected
                btnAdd.addEventListener('click', function () {
                    moveOptions(availableOptions, selectedOptions);
                });

                // Move selected options from selected to available
                btnRemove.addEventListener('click', function () {
                    moveOptions(selectedOptions, availableOptions);
                });

                // Function to move options between select elements
                function moveOptions(source, destination) {
                    Array.from(source.selectedOptions).forEach(option => {
                        destination.appendChild(option); // Move to destination
                    });
                }
            });

            //handle dynamic adding input type text***********************************************
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

            //display preloader*************************************************************
            document.addEventListener("DOMContentLoaded", function () {
                let preloader = document.getElementById('preloader');

                // Ensure preloader is hidden on page load
                if (preloader) {
                    preloader.style.display = 'none';
                }

                // Handle back button navigation or reload
                window.addEventListener("pageshow", function (event) {
                    if (event.persisted) {
                        preloader.style.display = 'none';
                    }
                });

                // Optional: Ensure preloader hides on complete page load
                window.onload = function () {
                    preloader.style.display = 'none';
                };
            });

            // Show preloader when form submission is valid
            function showPreloader(event) {
                const form = event.target;

                if (form.checkValidity()) {
                    document.getElementById('preloader').style.display = 'flex'; // Show preloader
                } else {
                    event.preventDefault();
                    form.classList.add('was-validated'); // Add validation styles
                }
            }
        </script>

    @endsection
