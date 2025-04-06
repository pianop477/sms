@extends('SRTDashboard.frame')

@section('content')
<div class="container">
    <h3 class="mb-3">Generated Timetable</h3>
    <button onclick="window.print()" class="btn btn-primary mb-4">Print Timetable</button>

    @foreach($classTimetables as $classId => $timetables)
        <div class="card mb-5">
            <div class="card-header">
                <h5>Class: {{ $timetables->first()->class->class_name }}</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($settings->active_days as $day)
                            @foreach($timetables->where('day_of_week', $day) as $entry)
                                <tr>
                                    <td>{{ ucfirst($day) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($entry->end_time)->format('H:i') }}</td>
                                    <td>{{ $entry->course->course_name }}</td>
                                    <td>{{ $entry->teacher->user->first_name.'.'. $entry->teacher->user->last_name[0] }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
