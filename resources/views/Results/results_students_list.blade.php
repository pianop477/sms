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
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px; */
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 5px 10px;
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
            font-size: 24px;
        }

        .card-body {
            padding: 5px;
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

        .form-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }

        .form-label {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .required-star {
            color: var(--danger);
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: white;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .select2-container--default .select2-selection--single {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px;
            height: auto;
            background-color: white;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .flatpickr-input {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            background-color: white;
        }

        .flatpickr-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .invalid-feedback {
            font-weight: 600;
            color: var(--danger);
            margin-top: 5px;
        }

        .text-danger small {
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #3f43b5 0%, #7a80f9 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78, 84, 200, 0.3);
            color: white;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #ffd54f 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            color: #856404;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-warning-custom:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-success-custom:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #1c9e75 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .info-alert {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.15) 0%, rgba(23, 162, 184, 0.25) 100%);
            border: 1px solid rgba(23, 162, 184, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            backdrop-filter: blur(5px);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            /* padding: 10px; */
            font-weight: 600;
            text-align: center;
        }

        .table-custom tbody td {
            /* padding: 10px; */
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: rgba(78, 84, 200, 0.05);
        }

        .table-custom tbody tr:hover {
            background-color: rgba(78, 84, 200, 0.1);
        }

        .score-input {
            width: auto;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            transition: all 0.3s;
        }

        .score-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.25);
        }

        .grade-input {
            width: 60px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            /* padding: 8px 12px; */
            font-weight: bold;
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

        .instruction-text {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.25) 100%);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 193, 7, 0.3);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .card-body {
                padding: 5px;
            }

            .header-title {
                font-size: 20px;
            }

            .table-responsive {
                font-size: 14px;
            }

            .score-input, .grade-input {
                width: 100%;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(78, 84, 200, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(78, 84, 200, 0);
            }
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
