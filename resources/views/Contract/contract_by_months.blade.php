@extends('SRTDashboard.frame')

@section('content')
    <div class="row">
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="header-title text-center text-uppercase">Approved Contract By Months</h4>
                        </div>
                        <div class="col-md-4">
                            <a href="{{route('contract.management')}}" class="float-right btn btn-info"><i class="fas fa-arrow-circle-left"></i> Back</a>
                        </div>
                    </div>
                    <p class="text-danger">Select Month</p>
                    @if ($contractsByMonth->isEmpty())
                    <div class="alert alert-danger" role="alert">
                        <p>No Records Available</p>
                    </div>
                    @else
                    <div class="list-group">
                        @foreach ($contractsByMonth as $month => $contracts )
                            <a href="{{route('contract.approved.all', ['year' => $year, 'month' => $month])}}" class="list-group-item list-group-item-action">
                                >> {{\Carbon\Carbon::parse($month)->format('F')}}
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
