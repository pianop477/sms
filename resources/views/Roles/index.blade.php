@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-6 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-uppercase text-center">Teacher's Roles</h4>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Teacher's Name</th>
                                    <th scope="col">Role Name</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$user->first_name}} {{$user->last_name}}</td>
                                        <td class="text-capitalize">{{$user->role_name}}</td>
                                        <td>
                                            <a href="" class="btn btn-primary btn-xs">Update Role</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{$users->links('vendor.pagination.bootstrap-5')}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-uppercase text-center">usertype & permissions</h4>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Teacher's Name</th>
                                    <th scope="col">Usertype</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$user->first_name}} {{$user->last_name}}</td>
                                        <td>
                                            @if ($user->usertype == 3)
                                                Teacher
                                            @endif
                                        </td>
                                        <td>
                                            <a href="" class="btn btn-success btn-xs">Assign</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{$users->links('vendor.pagination.bootstrap-5')}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
