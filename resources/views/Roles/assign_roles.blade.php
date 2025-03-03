@extends('SRTDashboard.frame')
    @section('content')
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-5">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-10">
                                <h4 class="header-title">Assign Roles</h4>
                            </div>
                            <div class="col-2">
                                <a href="{{route('roles.updateRole')}}"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem"></i></a>
                            </div>
                        </div>
                        <form action="{{route('roles.assign.new', ['user' => Hashids::encode($teachers->id)])}}" class="needs-validation" novalidate="" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustom01">Teacher's Name</label>
                                    <input type="text" name="teacher" disabled class="form-control text-uppercase" id="validationCustom01" placeholder="First name" value="{{old('teacher', $teachers->first_name. ' '. $teachers->last_name)}} - {{$teachers->role_name ?? ''}}" required="">
                                    @error('teacher')
                                    <div class="text-danger">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="">Roles</label>
                                    <select name="role" id="validationCustom01" class="form-control text-capitalize" required>
                                        <option value="">-- Select role --</option>
                                        @if ($roles->isEmpty())
                                            <option value="" class="text-danger">No Roles found</option>
                                            @else
                                            @foreach ($roles as $role)
                                                <option value="{{$role->id}}">{{$role->role_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('role')
                                    <div class="text-danger">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="">
                                <div class="">
                                    <button class="btn btn-success float-right">Assign</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
