
@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title">School Registration Form</h4>
            <form class="needs-validation" novalidate="" action="{{route('Schools.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">School name</label>
                        <input type="text" name="name" class="form-control" id="validationCustom01" placeholder="First name" value="{{old('name')}}" required="">
                        @error('name')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Registration No</label>
                        <input type="text" name="reg_no" class="form-control" id="validationCustom02" placeholder="Last name" required="" value="{{old('reg_no')}}">
                        @error('reg_no')
                        <div class="invalid-feedback">
                           {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <button class="btn btn-primary" type="submit">Register</button>
                </div>
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-center text-uppercase">Registered institutions</h4>
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table table-hover progress-table" id="myTable">
                                <thead class="text-uppercase">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">School Name</th>
                                        <th scope="col">Registration No</th>
                                        <th scope="col">status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schools as $school )
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td class="text-uppercase">
                                                {{$school->school_name}}
                                            </td>
                                            <td class="text-uppercase">{{$school->school_reg_no}}</td>
                                            <td>
                                                @if ($school->status == 1)
                                                <span class="status-p bg-success">Open</span>
                                                @else
                                                <span class="status-p bg-secondary">Closed</span>
                                                @endif
                                            </td>
                                            {{-- @if ($teacher->status == 1)
                                            <ul class="d-flex justify-content-center">
                                                <form action="{{route('activate.status', $manager->id)}}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                    <li>
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want block this school?')">
                                                            <i class="fas fa-ban text-success"></i>
                                                        </button>
                                                    </li>
                                                </form>
                                            </ul>
                                            @endif --}}
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
