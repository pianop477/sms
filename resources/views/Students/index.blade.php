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
                    <div class="col-md-12">
                        <h4 class="header-title text-uppercase text-center">{{$classId->class_name. ' Students list - ('.$classId->class_code.')'}}</h4>
                    </div>
                </div>
                <div class="row col-md-6 float-right">
                    @if ($students->isNotEmpty())
                    <div class="col-3">
                            <!-- Button trigger modal -->
                        <button type="button" class="btn btn-info btn-xs float-right" data-toggle="modal" data-target="#exampleModal">
                            <i class="fas fa-exchange-alt"></i> Promotes
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Promote Students to the Next class</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                                <div class="modal-body">
                                    <span class="text-danger text-capitalize">select class you want to promote students</span>
                                    <form class="needs-validation" novalidate="" action="{{route('promote.student.class', ['class' => Hashids::encode($classId->id)])}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="validationCustom01">Class name</label>
                                                <select name="class_id" id="" class="form-control text-uppercase" required>
                                                    <option value="">--Select Class--</option>
                                                    @if ($classes->isEmpty())
                                                        <option value="" class="text-danger">No more classes found</option>
                                                        <option value="0" class="text-success font-weight-bold" style="font-size: 15px">🎓 Graduate Class 🎉</option>
                                                    @else
                                                        @foreach ($classes as $class)
                                                            <option value="{{$class->id}}" class="">{{$class->class_name}}</option>
                                                        @endforeach
                                                        <option value="0" class="text-success font-weight-bold" style="font-size: 15px">🎓 Graduate Class 🎉</option>
                                                    @endif
                                                </select>
                                                @error('name')
                                                <div class="invalid-feedback">
                                                    {{$message}}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" id="upGradeButton" class="btn btn-success" onclick="return confirm('Are you sure you want to promote this class?')">Upgrade</button>
                                    </div>
                                </form>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <a href="{{route('export.student.pdf', ['class' => Hashids::encode($classId->id)])}}" target="_blank" class="float-right btn btn-primary btn-xs"><i class="fas fa-cloud-arrow-down"></i> Export</a>
                    </div>
                    @endif
                    <div class="col-3">
                        <a href="{{route('classes.list', ['class' => Hashids::encode($classId->id)])}}" class="float-right"><i class="fas fa-arrow-circle-left"></i> Back</a>
                    </div>
                    <div class="col-3">
                            <a type="#" class="btn p-0 float-right btn-link" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-plus-circle"></i> New
                            </a>
                            <div class="modal fade bd-example-modal-lg">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-uppercase">{{$classId->class_name}} Students Registration Form</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="needs-validation" novalidate="" action="{{route('student.store', ['class' => Hashids::encode($classId->id)])}}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">First name</label>
                                                        <input type="text" required name="fname" class="form-control" id="validationCustom01" placeholder="First name" value="{{old('fname')}}" required="">
                                                        @error('fname')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Middle name</label>
                                                        <input type="text" required name="middle" class="form-control" id="validationCustom02" placeholder="Middle name" required="" value="{{old('middle')}}">
                                                        @error('middle')
                                                        <div class="invalid-feedback">
                                                        {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Last name</label>
                                                        <input type="text" required name="lname" class="form-control" id="validationCustom02" placeholder="Last name" required="" value="{{old('lname')}}">
                                                        @error('lname')
                                                        <div class="invalid-feedback">
                                                        {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">Gender</label>
                                                        <select name="gender" required id="validationCustom01" class="form-control text-capitalize" required>
                                                            <option value="">-- select gender --</option>
                                                            <option value="male">male</option>
                                                            <option value="female">female</option>
                                                        </select>
                                                        @error('gender')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Date of Birth</label>
                                                        <input type="date" required id="customDatePicker" name="dob" class="form-control" id="validationCustom02" placeholder="Enter your birth date" required="" value="{{old('dob')}}" min="{{\Carbon\Carbon::now()->subYears(17)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(3)->format('Y-m-d')}}">
                                                        @error('dob')
                                                        <div class="invalid-feedback">
                                                        {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustomUsername">Parent/Guardian Name</label>
                                                            <select name="parent" id="parentSelect" class="form-control select2 text-capitalize" required>
                                                                <option value="">Select Parent</option>
                                                                @if ($parents->isEmpty())
                                                                    <option value="" disabled class="text-danger text-capitalize">No parents records found</option>
                                                                @else
                                                                    @foreach ($parents as $parent)
                                                                        <option value="{{$parent->id}}">
                                                                            {{ucwords(strtoupper($parent->first_name . ' ' . $parent->last_name))}} - {{$parent->phone}}
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            @error('parent')
                                                            <div class="invalid-feedback">
                                                                {{$message}}
                                                            </div>
                                                            @enderror
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">Class Group</label>
                                                        <select name="group" id="validationCustom02" required class="form-control">
                                                            <option value="">--Select Stream--</option>
                                                            <option value="a">Stream A</option>
                                                            <option value="b">Stream B</option>
                                                            <option value="c">Stream C</option>
                                                        </select>
                                                        {{-- <input type="text" name="group" required id="validationCustomUsername" class="form-control text-uppercase" placeholder="Enter Group A, B or C" id="validationCustom02" value="{{old('dob')}}" required> --}}
                                                        @error('group')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustomUsername">Bus Number:<small class="text-sm text-muted">Select if using School bus</small></label>
                                                        <div class="input-group">
                                                            <select name="driver" id="validationCustomUsername" class="form-control text-uppercase">
                                                                <option value="">-- select bus number --</option>
                                                                @if ($buses->isEmpty())
                                                                    <option value="" class="text-danger text-capitalize" disabled>No school bus records found</option>
                                                                @else
                                                                    @foreach ($buses as $bus)
                                                                        <option value="{{$bus->id}}">Bus No. {{$bus->bus_no}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            @error('driver')
                                                            <div class="invalid-feedback">
                                                                {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustomUsername">Photo :<small class="text-sm text-danger">(Optional)</small></label>
                                                        <div class="input-group">
                                                            <input type="file" name="image" id="validationCustomUsername" class="form-control" value="{{old('image')}}">
                                                            @error('image')
                                                            <div class="invalid-feedback">
                                                                {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
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
                <div class="single-table mt-5">
                    <form action="{{ route('students.batchUpdateStream') }}" method="POST" onsubmit="return confirm('Are you sure you want to move selected students to a new stream?')">
                        @csrf
                            <div class="col-md-12 mb-3">
                                <div class="row">
                                    <div class="col-md-2">
                                        <select name="new_stream" class="form-control text-capitalize" required>
                                            <option value="">-- Select Stream --</option>
                                            <option value="A">Stream A</option>
                                            <option value="B">Stream B</option>
                                            <option value="C">Stream C</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-warning btn-xs text-capitalize"><i class="fas fa-random"></i> Move</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover progress-table" id="myTable">
                                    <thead class="text-capitalize">
                                        <tr>
                                            <th scope="col" class="text-center"><input type="checkbox" id="selectAll"> All</th>
                                            <th scope="col" class="text-center">Adm No.</th>
                                            <th scope="col">First Name</th>
                                            <th scope="col">Middle Name</th>
                                            <th scope="col">Surname</th>
                                            <th scope="col" class="text-center">Gender</th>
                                            <th scope="col" class="text-center">Stream</th>
                                            <th scope="col">DoB</th>
                                            <th scope="col" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $student )
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" name="student[]" value="{{$student->id}}">
                                                    {{$loop->iteration}}
                                                </td>
                                                <td class="text-uppercase text-center">{{$student->admission_number}}</td>
                                                <td class="text-capitalize">{{ucwords(strtolower($student->first_name))}}</td>
                                                <td class="text-capitalize">{{ucwords(strtolower($student->middle_name))}}</td>
                                                <td class="text-capitalize">{{ucwords(strtolower($student->last_name))}}</td>
                                                <td class="text-center text-capitalize">{{$student->gender[0]}}</td>
                                                <td class="text-center text-capitalize">{{$student->group}}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($student->dob)->format('M d, Y') }}
                                                </td>
                                                <td>
                                                    <ul class="d-flex justify-content-center">
                                                        <li class="mr-3">
                                                            <a href="{{route('students.modify', ['students' => Hashids::encode($student->id)])}}"><i class="ti-pencil text-primary"></i></a>
                                                        </li>
                                                        <li class="mr-3">
                                                            <a href="{{route('Students.show', ['student' => Hashids::encode($student->id)])}}"><i class="ti-eye text-secondary"></i></a>
                                                        </li>

                                                        <li>
                                                            <form action="{{route('Students.destroy', ['student' => Hashids::encode($student->id)])}}" method="POST">
                                                                @csrf
                                                                <button class="btn btn-link p-0" onclick="return confirm('Are you sure you want to block {{strtoupper($student->first_name)}} {{strtoupper($student->middle_name)}} {{strtoupper($student->last_name)}}?')">
                                                                    <i class="ti-trash text-danger"></i>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
 <script>

    document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="student[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    window.onload = function() {
        // Hakikisha jQuery na Select2 inapatikana
        if (typeof $.fn.select2 !== 'undefined') {
            // Fanya initialization ya Select2
            $('#parentSelect').select2({
                placeholder: "Search Parent...",
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
