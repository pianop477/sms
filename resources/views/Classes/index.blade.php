@extends('SRTDashboard.frame')

@section('content')
<div class="row">
   <!-- table primary start -->
   <div class="col-lg-6 mt-5">
    <div class="card">
        <div class="card-body">
            <div class="col-row">
                <div class="d-flex">
                    <div class="col-8">
                        <h4 class="header-title text-center text-uppercase">Classes</h4>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-link float-right" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-plus"></i> New Class
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
                                                    <input type="text" name="name" class="form-control text-uppercase" id="validationCustom01" placeholder="Class Name" value="{{old('name')}}" required="">
                                                    @error('name')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom02">Class Code</label>
                                                    <input type="text" name="code" class="form-control text-uppercase" id="validationCustom02" placeholder="Class Numeric Code" required="" value="{{old('code')}}" required>
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
                    <table class="table text-capitalize">
                        <thead class="bg-success">
                            <tr class="text-white">
                                <th scope="col">Class Name</th>
                                <th scope="col">Class Code</th>
                                <th scope="col">action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($classes->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <div class="alert alert-warning">No classes records found. Please register classes</div>
                                    </td>
                                </tr>
                            @else
                            @foreach ($classes as $class )
                                <tr>
                                    <td>
                                        <i class="ti-angle-double-right"></i> {{$class->class_name}}
                                    </td>
                                    <td class="text-uppercase">{{$class->class_code}}</td>
                                    <td>
                                        <a href="{{route('Classes.edit', $class->id)}}"><i class="ti-pencil"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            @endif
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
                    <h4 class="header-title text-center text-uppercase">Assigned Class Teachers</h4>
                </div>
            </div>
            @if ($classes->isEmpty())
                <div class="alert alert-warning text-center">
                    <p>No classes records found. Please register classes!</p>
                </div>
            @else
            <div class="single-table">
                <div class="table-responsive">
                    <table class="table text-uppercase">
                        <thead class="bg-primary">
                            <tr class="text-white">
                                <th scope="col">Classes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classes as $class )
                                <tr>
                                    <td class="">
                                        <a href="{{route('Class.Teachers', $class->id)}}">
                                            <i class="ti-angle-double-right"></i> {{$class->class_name}} - {{$class->class_code}}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
