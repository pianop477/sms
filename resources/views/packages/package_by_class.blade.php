@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <h4 class="header-title text-center">Holiday Packages by class</h4>
                        </div>
                        <div class="col-3">
                            <a href="{{route('package.byYear')}}" class="float-right btn btn-xs btn-info"><i class="fas fa-arrow-circle-left" style=""></i> Back</a>
                        </div>
                    </div>
                    <p class="text-danger">Select Class</p>
                    @if ($classGroups->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <p>No holiday Package available!</p>
                        </div>
                        @else
                        <div class="list-group">
                            @foreach ($classGroups as $class => $package)
                                <a href="{{route('packages.list', ['year' => $year, 'class' => Hashids::encode($package->first()->grade_id)])}}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary text-uppercase"><i class=""></i> >> {{ $class }}</h6>
                                    </button>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
