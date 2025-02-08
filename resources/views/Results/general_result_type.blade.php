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
                                                                <label for="validationCustom02">Combine Report as:</label>
                                                                <select name="exam_type" id="" class="form-control text-capitalize" required>
                                                                    <option value="">--Select Exam type--</option>
                                                                    @foreach ($exams as $row)
                                                                        <option value="{{$row->id}}">{{$row->exam_type}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="validationCustom02">Combined Report Term</label>
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
                                                                                    <option value="{{ $month }}">{{ $month }}</option>
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
                            <p class="text-danger">Select Examination type to view results</p>
                            <div class="list-group">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
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
        </script>

    @endsection
