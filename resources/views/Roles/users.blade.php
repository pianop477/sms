@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h4 class="header-title text-uppercase">All Users</h4>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <td>{{$loop->iteration}}</td>
                                    <td class="text-capitalize">{{$user->first_name}} {{$user->last_name}}</td>
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
                                        <td>Reset</td>
                                    @endif
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
