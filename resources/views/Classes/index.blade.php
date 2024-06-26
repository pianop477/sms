@extends('SRTDashboard.frame')

@section('content')
<div class="row">
   <!-- table primary start -->
   <div class="col-lg-6 mt-5">
    <div class="card">
        <div class="card-body">
            <div class="col-row">
                <div class="d-flex">
                    <div class="col-10">
                        <h4 class="header-title">Classes List</h4>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-circle-plus text-secondary" style="font-size: 2rem;"></i>
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Classes Registration Form</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('Classes.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom01">Class name</label>
                                                    <input type="text" name="name" class="form-control" id="validationCustom01" placeholder="Class Name" value="{{old('name')}}" required="">
                                                    @error('name')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom02">Class Code</label>
                                                    <input type="text" name="code" class="form-control" id="validationCustom02" placeholder="Class Numeric Code" required="" value="{{old('code')}}" required>
                                                    @error('code')
                                                    <div class="invalid-feedback">
                                                       {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>

                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="single-table">
                <div class="table-responsive">
                    <table class="table text-uppercase">
                        <thead class="text-uppercase bg-dark">
                            <tr class="text-white">
                                <th scope="col">#</th>
                                <th scope="col">Class Name</th>
                                <th scope="col">Class Code</th>
                                <th scope="col">action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classes as $class )
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$class->class_name}}</td>
                                    <td>{{$class->class_code}}</td>
                                    <td>
                                        <a href=""><i class="ti-pencil"></i></a>
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
<!-- table primary end -->

<div class="col-md-6 mt-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title">View Class Teachers</h4>
                </div>
            </div>
            <ul class="list-group">
                @foreach ($classes as $class)
                <a href="{{route('Class.Teachers', $class->id)}}">
                    <li class="list-group-item d-flex justify-content-between align-items-center text-uppercase">
                        {{$class->class_name}}
                    </li>
                </a>
                @endforeach
            </ul>
        </div>
    </div>
</div>
</div>
@endsection
