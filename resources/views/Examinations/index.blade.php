@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-uppercase">Tests & Examination Type</h4>
                    </div>
                    @if (Route::has('exams.store'))
                    <div class="col-2">
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-circle-plus text-secondary" style="font-size: 2rem"></i>
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Register Examination or Test</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('exams.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom01">Examination Name</label>
                                                    <input type="text" name="name" class="form-control text-uppercase" id="validationCustom01" placeholder="Enter Exam name or test" value="" required="">
                                                    @error('name')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom01">Symbolic Abbreviation</label>
                                                    <input type="text" name="abbreviation" class="form-control text-uppercase" id="validationCustom01" placeholder="Examination Abbreviation" value="{{old('abbreviation')}}" required="">
                                                    @error('abbreviation')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
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
                    @endif
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive-md">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Examination Type</th>
                                    <th scope="col" class="text-center">symbolic abbreviation</th>
                                    <th scope="col" class="text-center">Status</th>
                                    {{-- @if (Route::has(['exams.block', 'exams.destroy', 'exams.edit', 'exams.unblock', 'exams.update'])) --}}
                                    <th scope="col" class="text-center">Action</th>
                                    {{-- @endif --}}
                                </tr>
                            </thead>
                            @foreach ($exams as $exam)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td class="text-uppercase">{{$exam->exam_type}}</td>
                                    <td class="text-uppercase text-center">{{$exam->symbolic_abbr}}</td>
                                    <td class="text-center">
                                        @if ($exam->status ==  1)
                                            <span class="badge bg-success text-white">{{_('Open')}}</span>
                                            @else
                                            <span class="badge bg-danger text-white">{{_('Closed')}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="d-flex justify-content-center">
                                            @if ($exam->status == 1)
                                                <li class="mr-3">
                                                    <a href="{{route('exams.type.edit', $exam->id)}}"><i class="ti-pencil text-primary"></i></a>
                                                </li>
                                                <li class="mr-3">
                                                    <form action="{{route('exams.block', $exam->id)}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class=" btn btn-link p-0" onclick="return confirm('Are you sure you want to Block {{strtoupper($exam->exam_type)}} Examination test?')"><i class="ti-na text-secondary"></i></button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a href="{{route('exams.destroy', $exam->id)}}" onclick="return confirm('Are you sure you want to Delete this Examination test permanently?')"><i class="ti-trash text-danger"></i></a>
                                                </li>
                                            @else
                                                <li>
                                                    <form action="{{route('exams.unblock', $exam->id)}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Unblock {{strtoupper($exam->exam_type)}} Examination test?')"><i class="ti-reload text-success"></i></button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
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
            submitButton.innerHTML = "Saving...";

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
