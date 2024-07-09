@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h3 class="header-title text-uppercase text-center">Students Result Form</h3>
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
                @if ($marking_style == 1)
                    <p class="text-center text-danger"><i>(Enter score from 0 to 50 correctly)</i></p>
                    @else
                    <p class="text-center text-danger"><i>(Enter score from 0 to 100 correctly)</i></p>
                @endif
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
                <div class="row">
                    <div class="col-12">
                        <form action="{{route('exams.store.score')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <table class="table table-responsive-md table-hover table-bordered" style="width: 100%;">
                                <thead class="table-primary">
                                    <th style="width: 5px">S/N</th>
                                    <th class="text-center">Admission No.</th>
                                    <th>Students Name</th>
                                    <th style="width: " class="text-center">Stream</th>
                                    <th style="width: ">Score</th>
                                    <th style="width: ">Grade</th>
                                </thead>
                                <tbody>
                                    @if ($students->isEmpty())
                                        <tr>
                                            <td colspan="4" class="alert alert-warning text-center">No students records found!</td>
                                        </tr>

                                        @else
                                        @foreach ($students as $student)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td class="text-center">
                                                    {{str_pad($student->id, 4, '0', STR_PAD_LEFT)}}
                                                    <input type="hidden" name="students[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                                </td>
                                                <td class="text-capitalize">
                                                    {{$student->first_name}} {{$student->middle_name}} {{$student->last_name}}
                                                </td>
                                                <td class="text-center text-uppercase">{{$student->group}}</td>
                                                <td>
                                                    <input type="number" class="form-control score-input" name="students[{{ $loop->index }}][score]" placeholder="Score" min="0" max="100">
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
                                <button type="submit" class="btn btn-primary">Send Results</button>
                            </div>
                        </form>
                    </div>
                </div>
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
@if ($marking_style == 1)
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
    @else
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
                        } else if (score >= 81 && score <= 100) {
                            grade = 'A';
                            bgColor = '#97e897';
                        } else if (score >= 61 && score <= 80) {
                            grade = 'B';
                            bgColor = '#4edcdc';
                        } else if (score >= 41 && score <= 60) {
                            grade = 'C';
                            bgColor = '#e9f0aa';
                        } else if (score >= 21 && score <= 40) {
                            grade = 'D';
                            bgColor = '#ef8f8f';
                        } else if(score >= 0 && score <= 20) {
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
@endif
@endsection
