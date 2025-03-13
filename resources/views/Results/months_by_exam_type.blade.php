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
                    <a href="{{ route('results.examTypesByClass', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($class_id)]) }}" class="float-right">
                        <i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i>
                    </a>
                </div>
            </div>
            <p class="text-danger">Select Month to view results</p>
            <div class="list-group">
                @foreach ($groupedByMonth as $month => $dates)
                    <button type="button" class="list-group-item list-group-item-action month-toggle" data-month="{{ Str::slug($month) }}">
                        <h6 class="text-primary"><i class="fas fa-chevron-right"></i> {{ $month }} - {{ $year }}</h6>
                    </button>
                    <div id="{{ Str::slug($month) }}" class="date-list mt-2" style="display: none; padding-left: 20px;">
                        <p class="text-danger">Select Date to get PDF Result</p>
                        @foreach ($dates as $date => $data)
                            <div class="d-flex justify-content-between align-items-center list-group-item">
                                <a href="{{ route('results.resultsByMonth', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($class_id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'date' => $date]) }}" target="_blank">
                                    <p class=""><i class="fas fa-calendar-alt"></i> {{ $date }}</p>
                                </a>
                                <a href="{{ route('individual.student.reports', ['school' => Hashids::encode($schools->id), 'year' => $year, 'examType' => Hashids::encode($exam_id), 'class' => Hashids::encode($class_id), 'month' => $month, 'date' => $date]) }}" class="btn btn-primary btn-xs">
                                    Students
                                </a>
                                @php
                                    $examStatus = $results->where('exam_date', $date)->first();
                                @endphp

                                @if ($examStatus && $examStatus->status == 1)
                                    <form action="{{ route('publish.results', ['school' => Hashids::encode($schools->id), $year, 'class' => Hashids::encode($class_id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'date' => $date]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to publish this results?')">
                                            <i class="fas fa-toggle-off text-secondary" style="font-size: 2rem;"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('unpublish.results', ['school' => Hashids::encode($schools->id), $year, 'class' => Hashids::encode($class_id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'date' => $date]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to unpublish this results?')">
                                            <i class="fas fa-toggle-on text-success" style="font-size: 2rem;"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('delete.results', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($class_id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'date' => $date]) }}" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete these results?')">
                                    Delete
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<style>
    /* Switcher Style */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
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
        transition: 0.4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".month-toggle").forEach(button => {
            button.addEventListener("click", function () {
                let monthDiv = document.getElementById(this.dataset.month);
                monthDiv.style.display = monthDiv.style.display === "none" ? "block" : "none";
            });
        });
    });
</script>
@endsection
