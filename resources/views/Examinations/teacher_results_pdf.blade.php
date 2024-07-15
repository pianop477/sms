@extends('SRTDashboard.frame')
@section('content')
<div class="col-md-12 mt-2">
        <div class="">
            <iframe src="{{ url('results.pdf') }}" style="width: 100%; height: 100vh;" frameborder="0"></iframe>
        </div>
</div>
@endsection
