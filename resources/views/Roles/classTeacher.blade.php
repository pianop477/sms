@extends('SRTDashboard.frame')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Pakia Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- Pakia Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<style>
    /* Override Select2 default styles to match Bootstrap form-control */
    .select2-container .select2-selection--single {
        height: 38px !important;  /* Ensure same height as form-control */
        border: 1px solid #ccc !important; /* Border to match Bootstrap */
        border-radius: 4px !important; /* Rounded corners to match Bootstrap */
        padding: 6px 12px !important; /* Padding to match form-control */
    }
    .select2-container {
    width: 100% !important; /* Ensure Select2 takes full width of the parent */
    }

    .select2-container {
        width: 100% !important; /* Set full width for Select2 container */
        max-width: 100% !important; /* Ensure it does not exceed container */
    }

    .select2-selection--single {
        width: 100% !important; /* Set width of the selection box */
    }
    .select2-selection--single {
        width: 100% !important; /* Ensure selection box inside Select2 also takes full width */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #495057; /* Match the default text color */
        line-height: 26px; /* Align text */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px; /* Arrow should be aligned */
    }

</style>
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase">assigned Class Teachers: <span style="text-decoration: underline"><strong>{{ $classes->class_name}}</strong></span></h4>
                    </div>
                    <div class="col-2">
                        <a href="{{route('Classes.index', ['class' => Hashids::encode($classes->id)])}}" class="btn btn-xs btn-info float-right"><i class="fas fa-arrow-circle-left"></i> Back</a>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary btn-xs p-1 float-right" data-toggle="modal" data-target=".bd-example-modal-md"><i class="fas fa-user-plus"></i> Assign
                        </button>
                        <div class="modal fade bd-example-modal-md">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Assign Class Teacher</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('Class.teacher.assign', ['classes' => Hashids::encode($classes->id)])}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom01">Teacher's Name</label>
                                                    <select name="teacher" id="parentSelect" class="form-control select2 text-capitalize" required>
                                                        <option value="">-- Select Class Teacher --</option>
                                                        @if ($teachers->isEmpty())
                                                            <option value="" class="text-danger">No teachers found</option>
                                                        @else
                                                            @foreach ($teachers as $teacher)
                                                                <option value="{{$teacher->id}}">{{ucwords(strtolower($teacher->teacher_first_name))}} {{ucwords(strtolower($teacher->teacher_last_name))}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('teacher')
                                                    <div class="text-danger">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom02">Class Group</label>
                                                    <select name="group" id="" class="form-control text-capitalize" required>
                                                        <option value="">-- Select Class Group --</option>
                                                        <option value="A">Stream A</option>
                                                        <option value="B">stream B</option>
                                                        <option value="C">stream C</option>
                                                    </select>
                                                    @error('group')
                                                    <div class="text-danger">
                                                       {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success" id="saveButton">Assign</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-capitalize text-center">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Class Name</th>
                                    <th scope="col">Class Group</th>
                                    <th scope="col">Teacher Name</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classTeacher as $teacher )
                                    <tr class="text-capitalize text-center">
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase">{{$teacher->class_name}}</td>
                                        <td class="text-capitalize text-center">Stream {{$teacher->group}}</td>
                                        <td>{{$teacher->teacher_first_name. ' '. $teacher->teacher_last_name}}</td>
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <a href="{{route('roles.edit', ['teacher' => Hashids::encode($teacher->id)])}}"><i class="ti-pencil"></i></a>
                                                </li>
                                                <li>
                                                    <a href="{{route('roles.destroy', ['teacher' => Hashids::encode($teacher->id)])}}" onclick="return confirm('Are you sure you want to remove {{ strtoupper($teacher->teacher_first_name) }} {{ strtoupper($teacher->teacher_last_name) }} from this class?')">
                                                        <i class="ti-trash text-danger"></i>
                                                    </a>
                                                </li>
                                            </ul>
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
<script>
    window.onload = function() {
        // Hakikisha jQuery na Select2 inapatikana
        if (typeof $.fn.select2 !== 'undefined') {
            // Fanya initialization ya Select2
            $('#parentSelect').select2({
                placeholder: "Search...",
                allowClear: true
            }).on('select2:open', function () {
                $('.select2-results__option').css('text-transform', 'capitalize');  // Capitalize option text
            });
        } else {
            console.error("Select2 haijapakiwa!");
        }
    };


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
