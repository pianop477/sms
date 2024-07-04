@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase">Students Result Form</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{route('score.prepare.form', $courseId)}}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 1.7rem;"></i></a>
                    </div>
                    <div class="col-2">

                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <ul>
                            <li>
                                <p>
                                    Class Code: <span class="text-uppercase fw-bold"><strong>{{$className}}</strong></span>
                                </p>
                            </li>
                            <li>
                                <p>
                                    Course Code: <span class="text-uppercase fw-bold"><strong>{{$courseName}}</strong></span>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <ul>
                            <li>
                                <p>
                                    Exam Type: <span class="text-uppercase fw-bold"><strong>{{$examName}}</strong></span>
                                </p>
                            </li>
                            <li>
                                <p>
                                    Exam Date: <span class="text-uppercase"><strong>{{\Carbon\Carbon::parse($examDate)->format('d-M-Y')}}</strong></span>
                                </p>
                            </li>
                            <li>
                                <p>
                                    Term: <span class="text-uppercase"><strong>{{$term}}</strong></span>
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
                <hr>
                <h5 class="text-center">Examination Scores</h5>
                <p class="text-center text-muted"><i>(Enter Marks from 0 to 50 correctly)</i></p>
                <hr>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{route('exams.store.score')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <table class="table table-responsive-md table-hover">
                        <thead class="table-dark">
                            <th>#ID</th>
                            <th>Students Name</th>
                            <th>Score</th>
                            <th>Grade</th>
                        </thead>
                        <tbody>
                            @if ($students->isEmpty())
                                <tr>
                                    <td colspan="4" class="alert alert-warning text-center">No students records found!</td>
                                </tr>

                                @else
                                @foreach ($students as $student)
                                    <tr>
                                        <td>
                                            {{str_pad($student->id, 4, '0', STR_PAD_LEFT)}}
                                            <input type="hidden" name="students[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                        </td>
                                        <td class="text-capitalize">
                                            {{$student->first_name}} {{$student->middle_name}} {{$student->last_name}}
                                        </td>
                                        <td>
                                            <input type="number" class="form-control score-input" name="students[{{ $loop->index }}][score]" placeholder="Score" min="0" max="50">
                                        </td>
                                        <td>
                                            <input type="text" disabled name="students[{{ $loop->index }}][grade]" class="form-control grade-input">
                                        </td>
                                    </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-center my-3">
                        <button type="submit" class="btn btn-primary">Submit Results</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .score-input {
        width: 80px;
    }

    @media (max-width: 576px) {
        .score-input {
            width: 70px;
        }
    }
    .grade-input {
        width: 70px;
    }

    @media(max-width:576px) {
        .grade-input {
            width: 80px;
        }
    }

</style>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const scoreInputs = document.querySelectorAll('.score-input');
        const gradeInputs = document.querySelectorAll('.grade-input');

        scoreInputs.forEach((input, index) => {
            input.addEventListener('blur', () => {
                const score = parseFloat(input.value);
                let grade = '';
                let bgColor = '';

                if (isNaN(score)) {
                    grade = 'Abs';
                    bgColor = 'orange';
                } else if (score >= 41 && score <= 50) {
                    grade = 'A';
                    bgColor = '#97e897';
                } else if (score >= 31 && score <= 40) {
                    grade = 'B';
                    bgColor = '#4edcdc';
                } else if (score >= 21 && score <= 30) {
                    grade = 'C';
                    bgColor = '#e9f0aa';
                } else if (score >= 11 && score <= 20) {
                    grade = 'D';
                    bgColor = '#ef8f8f';
                } else if(score >= 0 && score <= 10) {
                    grade = 'E';
                    bgColor = '#ebc4f3';
                }
                else {
                    grade = 'Error'
                    bgColor= 'red';
                }

                gradeInputs[index].value = grade;
                gradeInputs[index].style.backgroundColor = bgColor;
            });
        });
    });
</script>
@endsection
