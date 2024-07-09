@extends('SRTDashboard.frame')
    @section('content')
        <div class="col-md-12">
            <div class="card mt-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-2">
                            <div class="logo">
                                <img src="{{asset('assets/img/logo/'. Auth::user()->school->logo)}}" alt="logo" style="max-width: 120px; object-fit:cover">
                            </div>
                        </div>
                        <div class="col-10 text-center text-uppercase">
                            <h3>the united republic of tanzania</h3>
                            <h4>the president's office - ralg</h4>
                            <h4>{{Auth::user()->school->school_name}}</h4>
                            <h5>{{ $type }} Results - {{ DateTime::createFromFormat('!m', $results->first()->exam_month)->format('F') }}, {{$year}}</h5>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    @endsection
