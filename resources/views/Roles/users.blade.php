@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h4 class="header-title text-uppercase text-center">All Users</h4>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Username</th>
                                    <th>User Type</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$user->first_name}} {{$user->last_name}}</td>
                                        @if ($user->usertype == 3)
                                            <td>
                                                <span class="badge bg-info text-white">Teacher</span>
                                            </td>
                                            @else
                                            <td>
                                                <span class="badge bg-primary text-white">Parent</span>
                                            </td>
                                        @endif
                                        <td class="text-capitalize">{{$user->phone}}</td>
                                        <td class="">{{$user->email}}</td>
                                        @if ($user->status == 1)
                                            <td>
                                                <span class="badge bg-success text-white">Active</span>
                                            </td>
                                            @else
                                            <td>
                                                <span class="badge bg-secondary text-white">Blocked</span>
                                            </td>
                                        @endif
                                        <td>
                                            <form action="{{route('users.reset.password', $user->id)}}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-outline-danger btn-xs" onclick="return confirm('Are you sure you want to reset password for {{strtoupper($user->first_name)}} {{strtoupper($user->last_name)}}?')">
                                                    <i class="ti-unlock"> Reset Password</i>
                                                </button>
                                            </form>
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
