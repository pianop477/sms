@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title">Students List</h4>
            <ul class="list-group">
                @foreach ($classes as $class)
                <a href="{{route('create.selected.class', $class->id)}}">
                    <li class="list-group-item d-flex justify-content-between align-items-center text-uppercase">
                        {{$class->class_name}}
                        <span class="badge badge-primary badge-pill">{{$class->students_count}}</span>
                    </li>
                </a>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
