@extends('SRTDashboard.frame')

@section('hidePreloader')
@endsection

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 25px 30px;
            position: relative;
            overflow: hidden;
        }

        .card-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            transform: rotate(30deg);
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            font-size: 28px;
        }

        .class-highlight {
            color: #ffd700;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            text-transform: uppercase;
        }

        .card-body {
            padding: 30px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 1;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #fff9e6 0%, #ffeeb5 100%);
            border-radius: 15px;
            border-left: 5px solid var(--warning);
        }

        .empty-state i {
            font-size: 60px;
            color: var(--warning);
            margin-bottom: 15px;
        }

        .empty-state h5 {
            color: #856404;
            font-weight: 600;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .table-custom {
            margin-bottom: 0;
            width: 100%;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            text-align: center;
            vertical-align: middle;
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

        .student-name {
            font-weight: 600;
            color: var(--dark);
            text-transform: uppercase;
        }

        .score-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .score-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.3s;
        }

        .score-item:hover {
            background: #e9ecef;
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .subject-code {
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
        }

        .score-input {
            width: 60px;
            text-align: center;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 5px;
            font-weight: 600;
        }

        .score-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .edit-score {
            color: var(--primary);
            cursor: pointer;
            transition: all 0.3s;
        }

        .edit-score:hover {
            color: var(--secondary);
            transform: scale(1.2);
        }

        .save-score {
            color: var(--success);
            cursor: pointer;
            transition: all 0.3s;
        }

        .save-score:hover {
            color: #218838;
            transform: scale(1.2);
        }

        .action-list {
            display: flex;
            gap: 10px;
            justify-content: center;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .btn-action {
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            border: none;
            font-size: 14px;
        }

        .btn-report {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
        }

        .btn-report:hover {
            background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
            color: white;
            transform: translateY(-2px);
        }

        .btn-sms {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            color: #856404;
        }

        .btn-sms:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            color: #856404;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: white;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            color: white;
            transform: translateY(-2px);
        }

        .floating-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 60px;
            opacity: 0.1;
            color: white;
            z-index: 0;
        }

        @media (max-width: 992px) {
            .score-container {
                flex-direction: column;
                gap: 10px;
            }

            .action-list {
                flex-direction: column;
                gap: 10px;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .table-custom {
                display: block;
                overflow-x: auto;
            }

            .card-body {
                padding: 20px;
            }

            .header-title {
                font-size: 24px;
            }
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .success-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            display: none;
            align-items: center;
            gap: 10px;
        }
    </style>
    <div class="">
        <div class="glass-card">
            <div class="card-header-custom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="header-title">
                            <i class="fas fa-user-graduate me-2"></i>
                            Candidates Exam Report for <span class="class-highlight">{{$classId->class_name ?? ''}}</span>
                        </h4>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{route('results.monthsByExamType', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classId->id), 'examType' => Hashids::encode($exam_id), 'months' => $month])}}" class="btn btn-back btn-xs float-right">
                            <i class="fas fa-arrow-circle-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <i class="fas fa-chart-bar floating-icons"></i>
            </div>
            <div class="card-body">
                @if ($studentsResults->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h5>No Students Found</h5>
                        <p class="mb-0">There are no student records available for this examination.</p>
                    </div>
                @else
                    <div class="table-container">
                        <table class="table table-custom" id="myTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Full Name</th>
                                    <th>Phone</th>
                                    <th>Scores</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($studentsResults->groupBy('student_id') as $student_id => $studentData)
                                    <tr>
                                        <td class="text-center fw-bold">{{$loop->iteration}}</td>
                                        <td>
                                            <span class="student-name">
                                                {{$studentData->first()->first_name}} {{$studentData->first()->middle_name}} {{$studentData->first()->last_name}}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold">{{$studentData->first()->phone}}</span>
                                        </td>
                                        <td>
                                            <div class="score-container">
                                                @foreach ($studentData as $subject)
                                                    <div class="score-item">
                                                        <span class="subject-code">{{$subject->course_code}}:</span>
                                                        <form class="update-score-form" method="POST" action="{{ route('update.score') }}">
                                                            @csrf
                                                            <input type="hidden" name="student_id" value="{{ $student_id }}">
                                                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                                            <input type="text" class="score-input" name="score" value="{{ $subject->score }}" readonly>
                                                            <i class="fas fa-pencil-alt edit-score"></i>
                                                            <button type="submit" class="btn btn-link p-0 border-0 bg-transparent">
                                                                <i class="fas fa-check save-score"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <ul class="action-list">
                                                <li>
                                                    <a href="{{route('download.individual.report', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classId->id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'student' => Hashids::encode($student_id), 'date' => $date])}}"
                                                       target="_blank" class="btn-action btn-report"
                                                       onclick="return confirm('Are you sure you want to download report?')">
                                                        <i class="fas fa-download me-1"></i> Report
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{route('sms.results', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classId->id), 'examType' => Hashids::encode($exam_id), 'month' => $month, 'student' => Hashids::encode($student_id), 'date' => $date])}}"
                                                          method="POST" role="form">
                                                        @csrf
                                                        <button class="btn-action btn-sms"
                                                                onclick="return confirm('Are you sure you want to Re-send SMS?')">
                                                            <i class="fas fa-sms me-1"></i> SMS
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a href="{{route('delete.student.result', ['school' => Hashids::encode($schools->id), 'year' => $year, 'class' => Hashids::encode($classId->id), 'examTyoe' => Hashids::encode($exam_id), 'month' => $month, 'student' => Hashids::encode($student_id), 'date' => $date])}}"
                                                       class="btn-action btn-delete"
                                                       onclick="return confirm('Are you sure you want to delete results for this student?')">
                                                        <i class="fas fa-trash me-1"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Success Alert -->
    <div class="success-alert" id="successAlert">
        <i class="fas fa-check-circle"></i>
        <span>Score updated successfully!</span>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Enable score editing
            $('.edit-score').click(function() {
                let input = $(this).siblings('.score-input');
                input.prop('readonly', false).focus();
                $(this).hide();
            });

            // Disable editing when input loses focus
            $('.score-input').blur(function() {
                $(this).prop('readonly', true);
                $(this).siblings('.edit-score').show();
            });

            // Submit form using Ajax
            $('.update-score-form').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let url = form.attr('action');
                let data = form.serialize();

                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            // Show success alert
                            $('#successAlert').fadeIn().delay(3000).fadeOut();

                            // Re-enable readonly and show edit icon
                            form.find('.score-input').prop('readonly', true);
                            form.find('.edit-score').show();
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
