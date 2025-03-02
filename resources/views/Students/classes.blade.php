@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title text-uppercase text-center">Students List</h4>
            @if ($classes->isEmpty())
            <div class="alert alert-warning text-center">
                <p>No classes records found. Please register classes first!</p>
            </div>

            @else
            <ul class="list-group">
                @foreach ($classes as $class)
                <a href="{{route('create.selected.class', ['class' => Hashids::encode($class->id)])}}">
                    <li class="list-group-item d-flex justify-content-between align-items-center text-uppercase">
                        <span class="text-primary">>> {{$class->class_name}}</span>
                        <span class="badge badge-primary badge-pill">{{$class->students_count}}</span>
                    </li>
                </a>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection
