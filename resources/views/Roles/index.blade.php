@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h4 class="header-title text-uppercase text-center">Roles & Permission</h4>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Teacher's Name</th>
                                    <th scope="col" class="text-center">Gender</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Role</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$user->first_name}} {{$user->last_name}}</td>
                                        <td class="text-capitalize text-center">{{$user->gender[0]}}</td>
                                        <td class="text-capitalize">{{$user->phone}}</td>
                                        <td class="text-capitalize">
                                            @if ($user->role_id == 3)
                                                <span class="badge bg-primary text-white">{{$user->role_name}}</span>
                                            @elseif ($user->role_id == 2)
                                                <span class="badge bg-success text-white">{{$user->role_name}}</span>
                                            @elseif ($user->role_id == 4)
                                                <span class="badge bg-warning">{{$user->role_name}}</span>
                                            @else
                                                <span class="badge bg-secondary text-white">{{$user->role_name}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{route('roles.assign', ['user' => Hashids::encode($user->id)])}}" class="btn btn-primary btn-xs">Update Role</a>
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
{{-- end of role assigning card --}}
</div>
@endsection
