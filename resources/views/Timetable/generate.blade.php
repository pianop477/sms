@extends('SRTDashboard.frame')

@section('content')
<div class="container">
    <h2 class="mb-4">Generate Timetable</h2>
    <form method="POST" action="{{ route('timetable.generate') }}">
        @csrf
        <p>This will automatically generate a timetable for your school based on settings and subject assignments.</p>
        <button type="submit" class="btn btn-success">Generate Timetable</button>
    </form>
</div>
@endsection
