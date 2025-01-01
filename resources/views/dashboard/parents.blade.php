@extends('SRTDashboard.frame')

@section('content')
<div class="col-lg-12">
    <div class="row">
        <div class="col-md-4 mt-md-5 mb-3">
            <div class="card" style="background: #c84fe0">
                <div class="">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <div class="seofct-icon"><i class="fas fa-user-graduate"></i> Watoto Wangu</div>
                        <h2 class="text-white">{{count($students)}}</h2>
                    </div>
                    <canvas id="seolinechart2" height="50"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-8 mt-md-5 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="header-title text-uppercase">Watoto wangu</h4>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-xs btn-info float-right" data-toggle="modal" data-target=".bd-example-modal-lg">
                                <i class="fas fa-plus"></i> Andikisha
                            </button>
                            <div class="modal fade bd-example-modal-lg">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-uppercase">fomu ya Usajili wa Mwanafunzi</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-danger text-center text-capitalize">Tafadhali Jaza taarifa hizi kwa usahihi</p>
                                            <hr>
                                            <form class="needs-validation" novalidate="" action="{{route('register.student')}}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">Jina la Kwanza</label>
                                                        <input type="text" name="fname" class="form-control" id="validationCustom01" placeholder="Jina la Kwanza" value="{{old('fname')}}" required="">
                                                        @error('fname')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Jina la Kati</label>
                                                        <input type="text" name="middle" class="form-control" id="validationCustom02" placeholder="Jina la kati" required="" value="{{old('middle')}}">
                                                        @error('middle')
                                                        <div class="invalid-feedback">
                                                           {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Jina la Mwisho/Ukoo</label>
                                                        <input type="text" name="lname" class="form-control" id="validationCustom02" placeholder="Jina la mwisho" required="" value="{{old('lname')}}">
                                                        @error('lname')
                                                        <div class="invalid-feedback">
                                                           {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">Jinsia</label>
                                                        <select name="gender" id="validationCustom01" class="form-control text-capitalize" required>
                                                            <option value="">-- Chagua Jinsia --</option>
                                                            <option value="male">Mvulana</option>
                                                            <option value="female">Msichana</option>
                                                        </select>
                                                        @error('gender')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Tarehe ya Kuzaliwa</label>
                                                        <input type="date" name="dob" class="form-control" id="validationCustom02" placeholder="Tarehe ya kuzaliwa" required="" value="{{old('dob')}}">
                                                        @error('dob')
                                                        <div class="invalid-feedback">
                                                           {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustomUsername">Darasa</label>
                                                        <div class="input-group">
                                                            <select name="grade" id="" class="form-control text-uppercase" required>
                                                                <option value="">--Chagua Darasa--</option>
                                                                @if ($classes->isEmpty())
                                                                    <option value="" class="text-danger">No classes found</option>
                                                                @else
                                                                    @foreach ($classes as $class )
                                                                        <option value="{{$class->id}}">{{$class->class_name}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            @error('grade')
                                                            <div class="invalid-feedback">
                                                                {{$message}}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">Mkondo</label>
                                                        <input type="text" name="group" id="validationCustomUsername" class="form-control" placeholder="Andika A, B au C" id="validationCustom02" value="{{old('group')}}" required>
                                                        @error('group')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustomUsername">Ruti za Mabasi  :<small class="text-sm text-danger">Chagua kama anatumia usafiri</small></label>
                                                        <div class="input-group">
                                                            <select name="driver" id="" class="form-control text-capitalize">
                                                                <option value="">--Chagua ruti ya basi--</option>
                                                                @if ($buses->isEmpty())
                                                                    <option value="" class="text-danger">No School Bus Routine</option>
                                                                @else
                                                                    @foreach ($buses as $bus )
                                                                        <option value="{{$bus->id}}">{{$bus->routine}}</option>
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
                                                        <label for="validationCustomUsername">Picha ya Passport :<small class="text-sm text-danger"> (Sio lazima)</small></label>
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
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Ghairi</button>
                                            <button type="submit" class="btn btn-success">Hifadhi Taarifa</button>
                                        </div>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- table for students lies here --}}
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table" id="myTable">
                                <thead class="text-capitalize">
                                    <tr class="">
                                        <th scope="col">Jina</th>
                                        <th scope="col" style="width: 10px;">Jinsi</th>
                                        <th scope="col" style="width: 10px;">Darasa</th>
                                        <th scope="col" class="text-center">Vitendo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student )
                                        <tr>
                                            <td class="text-uppercase">
                                                <a href="{{route('Students.show', $student->id)}}">{{$student->first_name. ' '.$student->middle_name.' ' .$student->last_name}}</a>
                                            </td>
                                            <td class="text-uppercase">{{$student->gender[0]}}</td>
                                            <td class="text-uppercase">{{$student->class_code}} {{$student->group}}</td>
                                            <td>
                                                <ul class="d-flex justify-content-center">
                                                    <div class="btn-group" role="group">
                                                        <button id="btnGroupDrop" type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Angali
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                            <a class="dropdown-item" href="{{route('students.modify', $student->id)}}"><i class="ti-pencil text-primary"></i> Hariri</a>
                                                            <a class="dropdown-item" href="{{route('attendance.byYear', $student->id)}}"><i class="fa fa-list-check text-success"></i> Mahudhurio</a>
                                                            <a class="dropdown-item" href="{{route('results.index', $student->id)}}"><i class="ti-file text-info"></i> Matokeo</a>
                                                            <a class="dropdown-item" href="{{route('student.courses.list', $student->id)}}"><i class="ti-book text-warning"></i> Masomo</a>
                                                        </div>
                                                    </div>
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
</div>
<div class="col-lg-12">

</div>
<hr class="dark horizontal py-0">
@endsection
