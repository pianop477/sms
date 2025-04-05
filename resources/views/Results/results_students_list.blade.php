@extends('SRTDashboard.frame')

@section('hidePreloader')
@endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                @if ($studentsResults->isEmpty())
                <div class="alert alert-warning">No students found</div>
                @else
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase">Candidates Exam Report for {{$classId->class_name ?? ''}}</h4>
                    </div>
                    <div class="col-4">
                        <a href="{{route('results.monthsByExamType', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classId->id), 'examType' => Hashids::encode($exam_id), 'months' => $month])}}" class="float-right">
                            <i class="fas fa-arrow-circle-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Scores</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($studentsResults->groupBy('student_id') as $student_id => $studentData)
                                    <tr class="">
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase">
                                            {{$studentData->first()->first_name}} {{$studentData->first()->middle_name}} {{$studentData->first()->last_name}}
                                        </td>
                                        <td>{{$studentData->first()->phone}}</td>
                                        <td>
                                            <div class="d-flex flex-wrap">
                                                @foreach ($studentData as $subject)
                                                    <div class="d-flex align-items-center mr-1 mb-1">
                                                        <span class="mr-2 text-uppercase">{{$subject->course_code}}:</span>
                                                        <form class="update-score-form" method="POST" action="{{ route('update.score') }}">
                                                            @csrf
                                                            <input type="hidden" name="student_id" value="{{ $student_id }}">
                                                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                                            <input type="text" class="score-input form-control w-25 d-inline text-center" name="score" value="{{ $subject->score }}" readonly>
                                                            <i class="fas fa-pencil-alt edit-score text-primary" style="cursor:pointer;"></i>
                                                            <button type="submit" class="btn btn-link p-0"><i class="fas fa-check text-success" style="font-size: 1rem"></i></button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <a href="{{route('download.individual.report', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classId->id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'student' => Hashids::encode($student_id), 'date' => $date])}}" target="_blank" class="btn btn-xs btn-success" onclick="return confirm('Are you sure you want to download report?')">
                                                        Report
                                                    </a>
                                                </li>
                                                <li class="mr-3">
                                                    <form action="{{route('sms.results', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classId->id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'student' => Hashids::encode($student_id), 'date' => $date])}}" method="POST" role="form">
                                                        @csrf
                                                        <button class="btn btn-warning btn-xs" onclick="return confirm('Are you sure you want to Re-send SMS?')">SMS</button>
                                                    </form>
                                                </li>
                                                <li class="mr-3">
                                                    <a href="{{route('delete.student.result', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classId->id), 'examTyoe' => Hashids::encode($exam_id), 'month' => $month, 'student' => Hashids::encode($student_id), 'date' => $date])}}" class="btn btn-danger btn-xs"
                                                     onclick="return confirm('Are you sure you want to delete results for this student?')">Delete</a>
                                                </li>
                                            </ul>
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

<script>
    $(document).ready(function() {
    // Fungua uhariri wa alama kwa kubofya kwenye pencil icon
    $('.edit-score').click(function() {
        let input = $(this).siblings('.score-input');
        input.prop('readonly', false).focus();
    });

    // Funga uhariri wa alama wakati input inapotoka kwenye focus
    $('.score-input').blur(function() {
        $(this).prop('readonly', true);
    });

    // Wasilisha form kwa kutumia Ajax
    $('.update-score-form').on('submit', function(e) {
        e.preventDefault(); // Zuia upakiaji wa ukurasa

        let form = $(this);
        let url = form.attr('action');
        let data = form.serialize(); // Kusanya data kutoka kwa form

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function(response) {
                if (response.success) {
                    alert('Score updated successfully!');
                } else {
                    alert('Failed to update score.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>
@endsection
