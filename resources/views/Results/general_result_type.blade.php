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
            <div class="row">
                <div class="col-md-6 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-10">
                                    <h4 class="header-title">Single Month results</h4>
                                </div>
                                <div class="col-2">
                                    <a href="{{route('results.classesByYear', [$school->id, 'year'=>$year])}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
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
                                        <a href="{{ route('results.monthsByExamType', ['school' => $school->id, 'year' => $year, 'class' => $class, 'examType' => $exam_type_id]) }}">
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
                <div class="col-md-6 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <h4 class="header-title">Combined Results</h4>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-xs btn-info float-right" data-toggle="modal" data-target=".bd-example-modal-lg">
                                        Compile Results
                                    </button>
                                    <div class="modal fade bd-example-modal-lg">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Combine Results for Different Months</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="needs-validation" novalidate="" action="{{route('submit.compiled.results', ['school' => $school, 'year' => $year, 'class' => $class])}}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-row">
                                                            <div class="col-md-4 mb-3">
                                                                <label for="validationCustom01">Class</label>
                                                                <input type="hidden" name="class_id" value="{{$class}}">
                                                                <input type="text" readonly name="class" class="form-control text-uppercase" value="{{$grades->class_name}}">
                                                                @error('class')
                                                                <div class="invalid-feedback">
                                                                    {{$message}}
                                                                </div>
                                                                @enderror
                                                            </div>
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
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="validationCustom01">Report Date</label>
                                                                <input type="date" name="report_date" class="form-control" value=""
                                                                    min="{{ \Carbon\Carbon::create($year)->startOfYear()->format('Y-m-d') }}"
                                                                    max="{{ \Carbon\Carbon::create($year)->endOfYear()->format('Y-m-d') }}">
                                                                @error('report_date')
                                                                <div class="invalid-feedback">
                                                                    {{$message}}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="validationCustom02">Report Term</label>
                                                                <select name="term" id="" class="form-control text-capitalize" required>
                                                                    <option value="">--Select Term--</option>
                                                                    <option value="i">term 1</option>
                                                                    <option value="ii">Term 2</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="col-md-12 mb-3">
                                                                <div class="row">
                                                                    <div class="col-md-5">
                                                                        <label for="available-options">Available Results Months</label>
                                                                        <select id="available-options" class="form-control" size="10" multiple>
                                                                            @if ($groupedByMonth->isEmpty())
                                                                                <option value="" disabled class="text-danger">{{ _('No results record found') }}</option>
                                                                            @else
                                                                                @foreach ($groupedByMonth as $month => $monthsResult)
                                                                                    <option value="{{ $month }}">{{ strtoupper($month) }} - {{strtoupper($monthsResult->first()->symbolic_abbr)}} ({{strtoupper('term '.$monthsResult->first()->Exam_term)}})</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2 text-center">
                                                                        <div style="margin-top: 40px;">
                                                                            <button type="button" id="btn-add" class="btn btn-primary btn-sm mb-2">>></button><br />
                                                                            <button type="button" id="btn-remove" class="btn btn-secondary btn-sm"><<</button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-5">
                                                                        <label for="selected-options">Selected Months</label>
                                                                        <select id="selected-options" name="months[]" class="form-control" size="10" multiple required></select>
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
                            <p class="text-danger text-center">Fetch Combined Results</p>
                            <hr>
                            <!-- Loader GIF -->
                            <div id="preloader" style="
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(255, 255, 255, 0.8); /* Optional: semi-transparent background */
                            display: none; /* Ensure it is hidden by default */
                            justify-content: center;
                            align-items: center;
                            z-index: 9999; /* Ensures it stays on top */
                        ">
                            <img src="{{ asset('assets/img/loader/loader.gif') }}" alt="Loading..." width="100">
                        </div>


                            <form action="{{ route('fetch.report', ['class' => $class, 'year' => $year, 'school' => $school->id]) }}" method="POST" role="form" class="needs-validation" novalidate id="" onsubmit="showPreloader(event)">
                                @csrf
                                <!-- Loader GIF -->
                                <div id="loaderContainer" style="display: none; text-align: center; margin-top: 10px;">
                                    <img src="{{ asset('assets/img/loader/loader.gif') }}" alt="Loading..." width="50">
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label for="">Class</label>
                                        <select name="class" id="class-id" class="form-control text-capitalize" required>
                                            <option value="{{$class}}" selected>{{$grades->class_name}}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Report Type</label>
                                        <select name="exam_type" id="" class="form-control" required>
                                            <option value="">--Select--</option>
                                            @foreach ($compiledGroupByExam as $report_type => $compiled_results )
                                                <option value="{{$report_type}}">{{$report_type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label for="">Report Date</label>
                                        <input type="date" name="report_date" class="form-control" required
                                            min="{{ \Carbon\Carbon::create($year)->startOfYear()->format('Y-m-d') }}"
                                            max="{{ \Carbon\Carbon::create($year)->endOfYear()->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Term</label>
                                        <select name="term" id="term" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="i">Term 1</option>
                                            <option value="ii">Term 2</option>
                                        </select>
                                    </div>
                                </div>
                                <hr class="">
                                <div class="form-row">
                                    <div class="col-md-12 d-flex justify-content-center">
                                        <button type="submit" class="btn btn-success" id="searchBtn">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </form>
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
