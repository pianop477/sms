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
                                                    <input type="text" required name="name" class="form-control text-uppercase" id="validationCustom01" placeholder="Class Name" value="{{old('name')}}" required="">
                                                    @error('name')
                                                    <div class="text-danger">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom02">Class Code</label>
                                                    <input type="text" required name="code" class="form-control text-uppercase" id="validationCustom02" placeholder="Class Code" required="" value="{{old('code')}}" required>
                                                    @error('code')
                                                    <div class="text-danger">
                                                       {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>

                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" id="saveButton" class="btn btn-success">Save</button>
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
                                        <div class="alert alert-warning">No classes records found.</div>
                                    </td>
                                </tr>
                            @else
                            @foreach ($classes as $class )
                                <tr>
                                    <td class="text-uppercase">
                                        <i class="ti-angle-double-right"></i> {{$class->class_name}}
                                    </td>
                                    <td class="text-uppercase">{{$class->class_code}}</td>
                                    <td>
                                        <ul class="d-flex justify-content-center">
                                            <li class="mr-3">
                                                <a href="{{route('Classes.edit', ['id' => Hashids::encode($class->id)])}}"><i class="ti-pencil text-secondary"></i></a>
                                            </li>
                                            <li>
                                                <form action="{{route('Classes.destroy', ['id' => Hashids::encode($class->id)])}}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-link p-0" onclick="return confirm('Are you sure you want to delete this class Permanently?')">
                                                        <i class="ti-trash text-danger"></i>
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
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
                    <h4 class="header-title text-center text-uppercase">Assign Class Teachers</h4>
                </div>
            </div>
            @if ($classes->isEmpty())
                <div class="alert alert-warning text-center">
                    <p>No classes records found.</p>
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
                                        <a href="{{route('Class.Teachers', ['class' => Hashids::encode($class->id)])}}">
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector(".needs-validation");
        const submitButton = document.getElementById("saveButton"); // Tafuta button kwa ID

        if (!form || !submitButton) return; // Kama form au button haipo, acha script isifanye kazi

        form.addEventListener("submit", function (event) {
            event.preventDefault(); // Zuia submission ya haraka

            // Disable button na badilisha maandishi
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Please Wait...`;

            // Hakikisha form haina errors kabla ya kutuma
            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                submitButton.disabled = false; // Warudishe button kama kuna errors
                submitButton.innerHTML = "Save";
                return;
            }

            // Chelewesha submission kidogo ili button ibadilike kwanza
            setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
</script>
@endsection
