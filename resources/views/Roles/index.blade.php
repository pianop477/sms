@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-uppercase">Teacher's Roles</h4>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-plus-circle text-secondary" style="font-size: 2rem;"></i>
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Assign Teacher Roles</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('roles.assign', $teachers->first()->id)}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Teacher's Name</label>
                                                    <select name="teacher" id="" class="form-control text-capitalize" required>
                                                        <option value="">-- select teacher --</option>
                                                       @if ($teachers->isEmpty())
                                                           <option value="">No Records Found</option>
                                                           @else
                                                           @foreach ($teachers as  $teacher)
                                                                <option value="{{$teacher->id}}">{{$teacher->first_name}} {{$teacher->last_name}}</option>
                                                            @endforeach
                                                       @endif
                                                    </select>
                                                    @error('teacher')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Roles</label>
                                                    <select name="role" id="validationCustom01" class="form-control text-capitalize" required>
                                                        <option value="">-- select Roles --</option>
                                                        @foreach ($roles as $role )
                                                            <option value="{{$role->id}}" class="text-capitalize">{{$role->role_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('role')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Assign</button>
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
                                    <th scope="col">Teacher's Name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Role Name</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$user->first_name}} {{$user->last_name}}</td>
                                        <td class="text-capitalize">{{$user->gender[0]}}</td>
                                        <td>{{$user->role_name}}</td>
                                        <td>
                                            @if ($user->status == 1)
                                                <span class="text-white badge bg-success">Active</span>
                                                @else
                                                <span class="text-white badge bg-danger">Blocked</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="" class="btn btn-primary btn-xs">Update Role</a>
                                        </td>
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
