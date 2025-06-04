@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <h4 class="header-title">Holiday Packages lists - {{$year}}</h4>
                        </div>
                        <div class="col-3">
                            <a href="{{route('package.byClass', ['year' => $year])}}" class="float-right btn btn-xs btn-info"><i class="fas fa-arrow-circle-left" style=""></i> Back</a>
                        </div>
                    </div>
                    <hr>
                    @if ($packages->isEmpty())
                        <table class="table table-responsive-md table-borderless table-striped-columns" id="myTable">
                            <thead>
                                <tr class="text-capitalize text-center">
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Class</th>
                                    <th>Term</th>
                                    <th>Issued by</th>
                                    <th>status</th>
                                    <th>Issued at</th>
                                    <th>Released at</th>
                                    <th>Downloads</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="10" class="text-center text-danger">No holiday package available!</td>
                                </tr>
                            </tbody>
                        </table>
                        @else
                            <table class="table table-responsive-md table-borderless table-striped-columns" id="myTable">
                                <thead>
                                    <tr class="text-capitalize text-center">
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Class</th>
                                        <th>Term</th>
                                        <th>Issued by</th>
                                        <th>Status</th>
                                        <th>Isssued at</th>
                                        <th>Released at</th>
                                        <th>Downloads</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($packages as $recent)
                                    <tr class="text-capitalize text-center">
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$recent->title}}</td>
                                            <td>{{ucwords(strtoupper($recent->class_code))}}</td>
                                            <td>term {{$recent->term}}</td>
                                            <td>{{ucwords(strtolower($recent->first_name. '. '. $recent->last_name[0]))}}</td>
                                            <td>
                                                @if ($recent->is_active == true)
                                                    <span class="badge badge-success">Active <i class="fas fa-unlock"></i></span>
                                                @else
                                                    <span class="badge badge-danger">Locked <i class="fas fa-lock"></i></span>
                                                @endif
                                            </td>
                                            <td>{{$recent->created_at ?? $recent->updated_at}}</td>
                                            <td>{{$recent->release_date ?? 'Not Released'}}</td>
                                            <td>
                                                <span class="badge bg-primary text-white">{{$recent->download_count}}</span>
                                            </td>
                                            <td>
                                                <ul class="d-flex justify-content-center">
                                                @if ($recent->is_active == true)
                                                        <li class="mr-3">
                                                            <form action="{{route('deactivate.holiday.package', ['id' => Hashids::encode($recent->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn p-0 btn-link" title="Deactivate" onclick="return confirm('Are you sure you want to deactivate this package?')">
                                                                    <i class="fas fa-eye text-primary"></i>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        <li class="mr-3">
                                                            <form action="{{route('activate.holiday.package', ['id' => Hashids::encode($recent->id)])}}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn p-0 btn-link" title="Activate" onclick="return confirm('Are you sure you want to activate this package?')">
                                                                    <i class="fas fa-eye-slash text-primary"></i>
                                                                </button>
                                                            </form>
                                                        </li>
                                                @endif
                                                    <li class="mr-3">
                                                        <a href="{{route('download.holiday.package', ['id' => Hashids::encode($recent->id), 'preview' => true])}}" title="Download" target="_blank" onclick="return confirm('Are you sure you want to download this package?')">
                                                            <i class="fas fa-download text-info"></i>
                                                        </a>
                                                    </li>
                                                    <li class="mr-3">
                                                        <a href="{{route('delete.holiday.package', ['id' => Hashids::encode($recent->id)])}}" onclick="return confirm('Are you sure you want to delete this package?')" title="Delete">
                                                            <i class="fas fa-trash text-danger"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
