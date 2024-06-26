@extends('SRTDashboard.frame')
    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title">Edit Examination Test</h4>
                </div>
                <div class="col-2">
                    <a href="{{route('exams.index')}}"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                </div>
            </div>
            <form class="needs-validation" novalidate="" action="{{route('exams.update', $exams->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">Examination Name</label>
                        <input type="text" name="name" class="form-control text-uppercase" id="validationCustom01" placeholder="Enter Examination Eest" value="{{$exams->exam_type}}" required="">
                        @error('name')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Update</button>
            </form>
        </div>
    </div>

    @endsection
