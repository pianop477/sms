@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title text-center text-uppercase">Subject List By Class</h4>
            <p class="text-danger">Select Class to view Courses/Subjects</p>
            @if ($classes->isEmpty())
                <div class="alert alert-warning text-center">
                    <p>No Classes records found!</p>
                </div>

                @else
                <ul class="list-group">
                    @foreach ($classes as $class)
                    <a href="{{route('courses.view.class', $class->id)}}">
                        <li class="list-group-item text-primary align-items-center text-uppercase">
                            <i class="ti-angle-double-right"></i> {{$class->class_name}}
                        </li>
                    </a>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
