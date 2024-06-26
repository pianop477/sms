@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-8 mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title">Subject List By Class</h4>
            <ul class="list-group">
                @foreach ($classes as $class)
                <a href="{{route('courses.view.class', $class->id)}}">
                    <li class="list-group-item d-flex justify-content-between align-items-center text-uppercase">
                        {{$class->class_name}}
                    </li>
                </a>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
