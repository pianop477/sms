@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-2">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-2">
                    <div class="logo">
                        <img src="{{asset('assets/img/logo/' .Auth::user()->school->logo)}}" alt="" class="" style="max-width: 100px; object-fit:cover;">
                    </div>
                </div>
                <div class="col-8 text-center text-uppercase">
                    <h4>{{_('the united republic of tanzania')}}</h4>
                    <h5>{{_("the president's office - ralg")}}</h5>
                    <h5>{{Auth::user()->school->school_name}} - P.O Box {{Auth::user()->school->postal_address}}, {{Auth::user()->school->postal_name}}</h5>
                    <h6>{{$results->first()->exam_type}} results for {{$month}} - {{$year}}</h6>
                </div>
            </div>
            <hr>
            @if ($results->isEmpty())
                <div class="alert alert-warning text-center" role="alert">
                    <h6>No Result Records found</h6>
                </div>
            @else
                <!-- Display your results here -->
            @endif
        </div>
    </div>
</div>
@endsection
