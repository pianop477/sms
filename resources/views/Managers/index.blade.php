
@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
</div>
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-center text-uppercase">managers list</h4>
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table table-hover progress-table" id="myTable">
                                <thead class="text-uppercase">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">School Manager</th>
                                        <th scope="col">Gender</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">School</th>
                                        <th scope="col">status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($managers as $manager )
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td class="text-capitalize">
                                                {{$manager->first_name. ' '. $manager->last_name}}
                                            </td>
                                            <td class="text-capitalize">{{$manager->gender[0]}}</td>
                                            <td> {{$manager->phone}}</td>
                                            <td>{{$manager->email}}</td>
                                            <td class="text-capitalize">{{$manager->school_name}}</td>
                                            <td>
                                                @if ($manager->status == 1)
                                                <span class="status-p bg-success">Active</span>
                                                @else
                                                <span class="status-p bg-danger">Blocked</span>
                                                @endif
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
