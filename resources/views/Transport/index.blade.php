@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-uppercase">School Bus List</h4>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-plus-circle text-secondary" style="font-size: 2rem;"></i>
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Bus Registration Form</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('Transportation.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Driver name</label>
                                                    <input type="text" name="fullname" class="form-control" id="validationCustom01" placeholder="Driver Full Name" value="{{old('fname')}}" required="">
                                                    @error('fname')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Gender</label>
                                                    <select name="gender" id="validationCustom01" class="form-control text-capitalize" required>
                                                        <option value="">-- select gender --</option>
                                                        <option value="male">male</option>
                                                        <option value="female">female</option>
                                                    </select>
                                                    @error('gender')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom02">Mobile Phone</label>
                                                    <input type="text" name="phone" class="form-control" id="validationCustom02" placeholder="Phone Number" required="" value="{{old('phone')}}">
                                                    @error('phone')
                                                    <div class="invalid-feedback">
                                                       {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Bus Number</label>
                                                    <input type="text" name="bus" class="form-control" id="validationCustom02" value="{{old('bus')}}" required>
                                                    @error('bus')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustomUsername">Bus Routine Description</label>
                                                    <div class="input-group">
                                                        <textarea name="routine" id="" cols="60" rows="4" class="form-control"></textarea>
                                                        @error('routine')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Register</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Driver's Name</th>
                                    <th scope="col">Sex</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Bus No.</th>
                                    <th scope="col">Routine</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transport as $trans )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$trans->driver_name}}</td>
                                        <td class="text-capitalize">{{$trans->gender[0]}}</td>
                                        <td>{{$trans->phone}}</td>
                                        <td class="text-uppercase">
                                            {{$trans->bus_no}}
                                        </td>
                                        <td class="text-capitalize">{{$trans->routine}}</td>
                                        <td>
                                            @if ($trans->status == 1)
                                                <span class="text-white badge bg-success">Active</span>
                                                @else
                                                <span class="text-white badge bg-danger">Blocked</span>
                                            @endif
                                        </td>
                                        @if ($trans->status == 1)
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <a href="{{route('transport.edit', $trans->id)}}"><i class="ti-pencil text-primary"></i></a>
                                                </li>
                                                <li class="mr-3">
                                                    <form action="{{route('transport.update', $trans->id)}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Block this Bus Routine?')"><i class="fas fa-ban text-secondary"></i></button>
                                                    </form>
                                                </li>
                                                <li><a href="{{route('transport.remove', $trans->id)}}" onclick="return confirm('Are you sure you want to delete this Bus Routine Permanently?')" class="text-danger"><i class="ti-trash"></i></a></li>
                                            </ul>
                                        </td>
                                        @else
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <form action="{{route('transport.restore', $trans->id)}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Unblock this Bus Routine?')"><i class="ti-share-alt text-success"></i></button>
                                                    </form>
                                                </li>
                                                <li><a href="{{route('transport.remove', $trans->id)}}" onclick="return confirm('Are you sure you want to delete this Bus Routine Permanently?')" class="text-danger"><i class="ti-trash"></i></a></li>
                                            </ul>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection