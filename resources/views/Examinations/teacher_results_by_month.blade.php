@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title text-center">Select Months for Academic Year {{ $year }}</h4>
                </div>
                <div class="col-2">
                    <a href="{{route('results.byYear', ['course' => Hashids::encode($class_course->id), 'year' => $year])}}" class="float-right btn btn-xs btn-info">
                        <i class="fas fa-arrow-circle-left" style=""></i>
                        Back
                    </a>
                </div>
            </div>
            <p class="text-danger">Select Month</p>
            <div class="list-group">
                @foreach ($months as $month => $dates)
                    <button type="button" class="list-group-item list-group-item-action month-toggle" data-month="{{ Str::slug($month) }}">
                        <h6 class="text-primary"><i class="fas fa-chevron-right"></i> {{ $month }} - {{ $year }}</h6>
                    </button>
                    <div id="{{ Str::slug($month) }}" class="date-list mt-2" style="display: none; padding-left: 20px;">
                        <p class="text-danger">Select Date to get result</p>
                        @foreach ($dates as $date => $results)
                            <div class="row align-items-center mb-2">
                        <div class="col-md-10">
                            <a href="{{ route('results.byMonth', ['course' => Hashids::encode($class_course->id), 'year' => $year, 'examType' => Hashids::encode($exam_id), 'month' => $month, 'date' => $date]) }}">
                            <button type="button" class="list-group-item list-group-item-action w-100">
                                <p class="text-success mb-0" style="text-decoration: underline;">
                                <i class="fas fa-file-pdf-o"></i>
                                {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}
                                </p>
                            </button>
                            </a>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="{{ route('results.delete.byTeacher', ['course' => Hashids::encode($class_course->id), 'year' => $year, 'examType' => Hashids::encode($exam_id), 'month' => $month, 'date' => $date]) }}"
                            class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete this result for date: {{\Carbon\Carbon::parse($date)->format('d-m-Y')}}?')">
                            <i class="fas fa-trash" style="font-size:1.5rem;"></i>
                            </a>
                        </div>
                        </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".month-toggle").forEach(button => {
            button.addEventListener("click", function () {
                let monthDiv = document.getElementById(this.dataset.month);
                if (monthDiv.style.display === "none") {
                    monthDiv.style.display = "block";
                } else {
                    monthDiv.style.display = "none";
                }
            });
        });
    });
</script>

@endsection
