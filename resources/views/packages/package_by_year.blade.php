@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <h4 class="header-title text-center">Holiday Packages by Year</h4>
                        </div>
                        <div class="col-2">
                            <a href="{{route('home')}}" class="float-right btn btn-xs btn-info"><i class="fas fa-arrow-circle-left" style=""></i> Back</a>
                        </div>
                    </div>
                    <p class="text-danger">Select Year</p>
                    @if ($groupedByYear->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <p>No holiday Package available!</p>
                        </div>
                        @else
                        <div class="list-group">
                            @foreach ($groupedByYear as $year => $package)
                                <a href="{{route('package.byClass', ['year' => $year])}}">
                                    <button type="button" class="list-group-item list-group-item-action">
                                        <h6 class="text-primary"><i class="fas fa-chevron-right"></i> {{ $year ?? now() }}</h6>
                                    </button>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <h4 class="header-title text-center">Recent Holiday Packages</h4>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary float-right btn-xs" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-upload"></i> Upload Package
                            </button>
                            <div class="modal fade bd-example-modal-lg">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Upload new holiday package</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="needs-validation" novalidate="" action="{{route('package.upload')}}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">Package title <span class="text-danger">*</span></label>
                                                        <input type="text" required name="title" class="form-control text-capitalize" id="validationCustom01" placeholder="Package name" value="{{old('title')}}" required="">
                                                        @error('sname')
                                                        <div class="text-danger">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Class <span class="text-danger">*</span></label>
                                                        <select name="class" id="validationCustom02" class="form-control text-uppercase" required>
                                                            <option value="">{{_('--select class--')}}</option>
                                                            @foreach ($classes as $class)
                                                                <option value="{{$class->id}}" {{old('class') == $class->id ? 'selected' : ''}}>{{$class->class_name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('class')
                                                        <div class="text-danger">
                                                           {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Term <span class="text-danger">*</span></label>
                                                        <select name="term" id="validationCustom02" class="form-control text-capitalize" required>
                                                            <option value="">{{_('--select term--')}}</option>
                                                            <option value="{{_('i')}}" {{old('term')}}>Term 1</option>
                                                            <option value="{{_('ii')}}"  {{old('term')}}>Term 2</option>
                                                        </select>
                                                        @error('term')
                                                        <div class="text-danger">
                                                           {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-4">
                                                        <label for="">Upload File <span class="text-danger">*</span></label>
                                                        <input type="file" name="package_file" class="form-control" id="validationCustom03" required>
                                                        @error('package_file')
                                                        <div class="text-danger">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="">Description</label>
                                                        <textarea name="description" class="form-control" id="validationCustom04" rows="3" placeholder="Description">{{old('description')}}</textarea>
                                                        @error('description')
                                                        <div class="text-danger">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                            <button type="submit" id="saveButton" class="btn btn-success">Upload Package</button>
                                        </div>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($recentPackages->isEmpty())
                        <table class="table table-responsive-md table-borderless table-striped-columns" id="myTable">
                            <thead>
                                <tr class="text-capitalize text-center">
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Class</th>
                                    <th>Term</th>
                                    <th>Issued by</th>
                                    <th>Issued at</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center text-danger">No recently holiday package available!</td>
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
                                    <th>Issued by</th>
                                    <th>Status</th>
                                    <th>Isssued at</th>
                                    <th>Downloads</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($recentPackages as $recent)
                                   <tr class="text-capitalize text-center">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$recent->title}}</td>
                                        <td>{{ucwords(strtoupper($recent->class_code))}}</td>
                                        <td>{{ucwords(strtolower($recent->first_name. '. '. $recent->last_name[0]))}}</td>
                                        <td>
                                            @if ($recent->is_active == true)
                                                <span class="badge badge-success">Active <i class="fas fa-unlock"></i></span>
                                            @else
                                                <span class="badge badge-danger">Locked <i class="fas fa-lock"></i></span>
                                            @endif
                                        </td>
                                        <td>{{\Carbon\Carbon::parse($recent->created_at)->format('d-m-Y H:i') ?? \Carbon\Carbon::parse($recent->updated_at)->format('d-m-Y H:i')}}</td>
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
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
