@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <!-- seo fact area start -->
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-4 mt-5 mb-3">
                <div class="card">
                    <div class="seo-fact sbg1">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <div class="seofct-icon"><i class="fas fa-building"></i> School (s)</div>
                            <h2>{{count($schools)}}</h2>
                        </div>
                        <canvas id="seolinechart1" height="50"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-md-5 mb-3">
                <div class="card">
                    <div class="seo-fact sbg2">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <div class="seofct-icon"><i class="fas fa-user-tie"></i> Teacher (s)</div>
                            <h2>{{count($teachers)}}</h2>
                        </div>
                        <canvas id="seolinechart2" height="50"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-md-5 mb-3">
                <div class="card">
                    <div class="seo-fact sbg3">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <div class="seofct-icon"><i class="fas fa-user-graduate"></i> Student (s)</div>
                            <h2>{{count($students)}}</h2>
                        </div>
                        <canvas id="seolinechart2" height="50"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-4 mt-5 mb-3">
                <div class="card">
                    <div class="seo-fact sbg4">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <div class="seofct-icon"><i class="fas fa-user-shield"></i> Parent (s)</div>
                            <h2>{{count($parents)}}</h2>
                        </div>
                        <canvas id="seolinechart1" height="50"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-md-5 mb-3">
                <div class="card">
                    <div class="seo-fact sbg2">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <div class="seofct-icon"><i class="ti-book"></i> Courses (s)</div>
                            <h2>{{count($subjects)}}</h2>
                        </div>
                        <canvas id="seolinechart2" height="50"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-md-5 mb-3">
                <div class="card">
                    <div class="seo-fact sbg1">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <div class="seofct-icon"><i class="ti-blackboard"></i> Classes/Faculty (s)</div>
                            <h2>{{count($classes)}}</h2>
                        </div>
                        <canvas id="seolinechart2" height="50"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title text-center text-uppercase">Running Institutions</h4>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Institute Name</th>
                                    <th scope="col">Registration No</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">status</th>
                                    <th scope="col">action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($school_details as $school )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase">
                                            <a href="{{route('schools.show', $school->id)}}">{{$school->school_name}}</a>
                                        </td>
                                        <td class="text-uppercase">{{$school->school_reg_no}}</td>
                                        <td class="text-uppercase">P.O box {{$school->postal_address}} - {{$school->postal_name}}</td>
                                        <td>
                                            @if ($school->status == 1)
                                            <span class="status-p bg-success">Active</span>
                                            @else
                                            <span class="status-p bg-secondary">Closed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($school->status == 1)
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <a href="{{route('schools.show', $school->id)}}"><i class="ti-eye text-secondary"></i></a>
                                                </li>
                                                <form action="{{route('deactivate.status', $school->id)}}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <li>
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to block school {{strtoupper($school->school_name)}}?')">
                                                            <i class="ti-trash text-danger"></i>
                                                        </button>
                                                    </li>
                                                </form>
                                            </ul>

                                            @else
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <a href="{{route('schools.show', $school->id)}}"><i class="ti-eye text-secondary"></i></a>
                                                </li>
                                                <form action="{{route('activate.status', $school->id)}}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                    <li>
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want unblock school {{strtoupper($school->school_name)}}?')">
                                                            <i class="ti-share-alt text-success"></i>
                                                        </button>
                                                    </li>
                                                </form>
                                            </ul>
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
