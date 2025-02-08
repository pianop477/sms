@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-center text-uppercase">Select Month</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{ route('results.examTypesByClass', ['school' => $school->id, 'year' => $year, 'class' => $class->id]) }}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                    </div>
                </div>
                <p class="text-danger">Select Month to view results</p>
                <div class="list-group">
                    @if ($groupedByMonth->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Result Records found</h6>
                        </div>
                    @else
                    <table class="table table-responsive-md table-hover">
                        <tbody>
                            @foreach ($groupedByMonth as $month => $results)
                                @php
                                    $firstResult = $results->first();
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('results.resultsByMonth', ['school' => $school->id, 'year' => $year, 'class' => $class->id, 'examType' => $examType, 'month' => $month]) }}" target="_blank">
                                            <h6 class="text-primary text-capitalize"><i class="fas fa-chevron-right"></i> {{ $month }} Results Link</h6>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{route('individual.student.reports', ['school' => $school, 'year' => $year, 'examType' => $examType, 'class' => $class, 'month' => $month])}}" class="float-right btn btn-primary btn-xs">
                                            Students
                                        </a>
                                    </td>
                                    <td>
                                        <label class="switch float-right">
                                            <input type="checkbox" class="toggle-status" data-school="{{ $school->id }}" data-year="{{ $year }}" data-class="{{ $class->id }}" data-exam-type="{{ $examType }}" data-month="{{ $month }}" {{ $firstResult->status == 2 ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <a href="{{route('delete.results', ['school' => $school->id, 'year' => $year, 'class' => $class->id, 'examType' => $examType, 'month' => $month])}}" class="float-right btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this results?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const toggleButtons = document.querySelectorAll('.toggle-status');

                        toggleButtons.forEach(button => {
                            button.addEventListener('change', function () {
                                const isChecked = this.checked;
                                const schoolId = this.getAttribute('data-school');
                                const year = this.getAttribute('data-year');
                                const classId = this.getAttribute('data-class');
                                const examType = this.getAttribute('data-exam-type');
                                const month = this.getAttribute('data-month');
                                const url = isChecked ? '{{ route('publish.results', ['school' => ':school', 'year' => ':year', 'class' => ':class', 'examType' => ':examType', 'month' => ':month']) }}' : '{{ route('unpublish.results', ['school' => ':school', 'year' => ':year', 'class' => ':class', 'examType' => ':examType', 'month' => ':month']) }}';

                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = url.replace(':school', schoolId).replace(':year', year).replace(':class', classId).replace(':examType', examType).replace(':month', month);

                                const csrfField = document.createElement('input');
                                csrfField.type = 'hidden';
                                csrfField.name = '_token';
                                csrfField.value = '{{ csrf_token() }}';
                                form.appendChild(csrfField);

                                const methodField = document.createElement('input');
                                methodField.type = 'hidden';
                                methodField.name = '_method';
                                methodField.value = 'PUT';
                                form.appendChild(methodField);

                                document.body.appendChild(form);
                                form.submit();
                            });
                        });
                    });
                </script>

                <style>
                    .switch {
                        position: relative;
                        display: inline-block;
                        width: 50px;
                        height: 24px;
                    }

                    .switch input {
                        opacity: 0;
                        width: 0;
                        height: 0;
                    }

                    .slider {
                        position: absolute;
                        cursor: pointer;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background-color: #ccc;
                        transition: .4s;
                        border-radius: 34px;
                    }

                    .slider:before {
                        position: absolute;
                        content: "";
                        height: 16px;
                        width: 16px;
                        left: 2px;
                        bottom: 4px;
                        background-color: white;
                        transition: .4s;
                        border-radius: 50%;
                    }

                    input:checked + .slider {
                        background-color: #2196F3;
                    }

                    input:checked + .slider:before {
                        transform: translateX(26px);
                    }
                </style>
            </div>
        </div>
    </div>
@endsection
