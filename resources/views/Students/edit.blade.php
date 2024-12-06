@extends('SRTDashboard.frame')
@section('content')
<div class="card mt-5">
    <div class="card-body">
        <div class="row">
            <div class="col-10">
                <h4 class="header-title">Hariri Taarifa za Mwanafunzi</h4>
            </div>
            <div class="col-2">
                <a href="{{url()->previous()}}" class=""><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
            </div>
        </div>
        <form class="needs-validation" novalidate="" action="{{route('students.update.records', $students->id)}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="col-md-4">
                    <div class="avatar position-relative">
                        @if (!empty($students->image))
                            <img src="{{ asset('assets/img/students/' . $students->image) }}" alt="profile_image" class="profile-img border-radius-lg shadow-sm" style="width: 150px; object-fit:cover; border-radius:4px; border: 1px solid black;">
                        @else
                            <i class="fas fa-user-graduate text-secondary" style="font-size: 8rem;"></i>
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Jina la Kwanza</label>
                    <input type="text" name="fname" class="form-control text-capitalize" id="validationCustom01" value="{{$students->first_name}}" required>
                    @error('fname')
                        <div class="invalid-feedback">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Jina la Kati</label>
                    <input type="text" name="middle" class="form-control text-capitalize" id="validationCustom01" value="{{$students->middle_name}}" required>
                    @error('middle')
                        <div class="invalid-feedback">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Jina la Mwisho/Ukoo</label>
                    <input type="text" name="lname" class="form-control text-capitalize" id="validationCustom01" value="{{$students->last_name}}" required>
                    @error('lname')
                        <div class="invalid-feedback">{{$message}}</div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Jinsia</label>
                    <select name="gender" id="" class='form-control text-capitalize'>
                        <option value="{{$students->gender}}">{{$students->gender}}</option>
                        <option value="male">Mvulana</option>
                        <option value="female">Msichana</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Tarehe ya Kuzaliwa</label>
                    <input type="date" name="dob" class="form-control" id="validationCustom01" value="{{$students->dob}}" required>
                    @error('dob')
                        <div class="invalid-feedback">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Chagua Ruti za Mabasi: <span class="text-danger">Chagua kama anatumia usafiri/Kubadilisha ruti</span></label>
                    <select name="driver" id="validationCustom01" class="form-control text-uppercase">
                        <option value="">--Hatumii Usafiri--</option>
                        @if ($students->transport == NULL)
                            <option value="">-- Chagua Ruti --</option>
                            @foreach ($buses as $bus )
                                <option value="{{$bus->id}}">{{$bus->routine}}</option>
                            @endforeach
                            @else
                            <option value="{{$students->transport_id}}" selected>{{$students->driver_name}}</option>
                            @foreach ($buses as $bus)
                                <option value="{{$bus->id}}">{{$bus->driver_name}}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('driver')
                        <div class="invalid-feeback">{{$message}}</div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Darasa</label>
                    <select name="class" id="validationCustom01" class="form-control text-uppercase" required>
                        <option value="{{$students->class_id}}">{{$students->class_name}}</option>
                        @foreach ($classes as $class)
                            <option value="{{$class->id}}">{{$class->class_name}}</option>
                        @endforeach
                    </select>
                    @error('class')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Mkondo</label>
                    <input type="text" name="group" class="form-control text-capitalize" id="validationCustom01" value="{{$students->group}}">
                    @error('group')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Picha ya Passport</label>
                    <input type="file" name="image" class="form-control text-capitalize" id="validationCustom01" value="{{old('image')}}">
                    @error('image')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>

            </div>
            <div class="col-md-4 mb-3">
                <button class="btn btn-success" type="submit">Hifadhi Taarifa</button>
            </div>

@endsection
