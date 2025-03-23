@extends('SRTDashboard.frame')
@section('content')
  <div class="card card-body mx-3 mx-md-4 mt-n6 content">
    <div class="row">
      <div class="col-auto">
        <div class="avatar position-relative">
            @if (!empty($data->image))
                <img src="{{ asset('assets/img/students/' . $data->image) }}" alt="profile_image" class="profile-img border-radius-lg shadow-sm" style="width: 120px; object-fit:cover; border-radius: 50px;">
            @else
                <i class="fas fa-user-graduate text-secondary" style="font-size: 8rem"></i>
            @endif
        </div>

      </div>
      <div class="col-auto my-auto">
        <div class="h-100">
          <h5 class="mb-1">
            <span class="text-uppercase">{{$data->first_name. ' '. $data->middle_name}}</span>
            <p class="font-weight-bold mb-3 text-sm">
                Admission Number: <span class="text-uppercase" style="text-decoration: underline">{{$data->admission_number}}</span>
            </p>
          </h5>
          Account Type: <span class="badge bg-info text-white">Student</span>
          <p class="mb-0 font-weight-normal text-sm">
            @if ($data->status == 1)
                Account Status: <span class="badge bg-success text-white">{{_('Active')}}</span>
                @else
                Account Status: <span class="badge bg-secondary text-white">{{_('Blocked')}}</span>
            @endif
          </p>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3 float-right">
        <a href="{{ route('home') }}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
      </div>
    </div>
    <hr class="dark horizontal my-0">
    <div class="row">
        <!-- Basic List Group start -->
        <!-- Buttons Items start -->
        <div class="col-md-6 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Other Student Information</h4>
                    <div class="list-group">
                        <button type="button" class="list-group-item list-group-item-action">
                            Surname: <span class="text-uppercase font-weight-bold">{{$data->last_name}}</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action">
                            Gender: <span class="text-uppercase font-weight-bold">{{$data->gender[0]}}</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action">
                            Date of Birth: <span class="font-weight-bold">{{\Carbon\Carbon::parse($data->dob)->format('F d, Y')}}</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action">
                            Class: <span class="text-uppercase font-weight-bold">{{$data->grade_class_name}}</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action" disabled="">
                            Stream: <span class="text-uppercase font-weight-bold">{{$data->group}}</span>
                        </button>
                        @if ($data->transport_id == NULL)
                        <button type="button" class="list-group-item list-group-item-action" disabled="">
                            Is student Using School Bus?: <span class="text-capitalize font-weight-bold text-danger">{{_('No')}}</span>
                        </button>
                        @else
                        <button type="button" class="list-group-item list-group-item-action" disabled="">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="ti-eye"> SCHOOL BUS DETAILS</i>
                            </button>
                            <div class="modal fade bd-example-modal-lg">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">SCHOOL BUS DETAILS</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="col-md-12 mt-5">
                                                <div class="card">
                                                        <div class="list-group">
                                                            <button type="button" class="list-group-item list-group-item-action">
                                                                Driver Name: <span class="text-uppercase font-weight-bold">{{$data->driver}}</span>
                                                            </button>
                                                            <button type="button" class="list-group-item list-group-item-action">
                                                                Driver Gender: <span class="text-uppercase font-weight-bold">{{$data->driver_gender[0]}}</span>
                                                            </button>
                                                            <button type="button" class="list-group-item list-group-item-action">
                                                                Driver Phone Number: <span class="text-uppercase font-weight-bold">{{$data->driver_phone}}</span>
                                                            </button>
                                                            <button type="button" class="list-group-item list-group-item-action">
                                                                School Bus Number: <span class="text-uppercase font-weight-bold">{{$data->bus_number}}</span>
                                                            </button>
                                                            <button type="button" class="list-group-item list-group-item-action" disabled="">
                                                                School Bus Routine: <span class="text-uppercase font-weight-bold">{{$data->bus_routine}}</span>
                                                            </button>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Buttons Items end -->
        <!-- Flush start -->
        <div class="col-md-6 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Parents Information</h4>
                    <div class="list-group">
                        <button type="button" class="list-group-item list-group-item-action">
                            Parent's full name: <span class="text-uppercase font-weight-bold">{{$data->user_first_name. ' '. $data->user_last_name}}</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action">
                            Parent's gender: <span class="text-uppercase font-weight-bold">{{$data->user_gender[0]}}</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action">
                            Parent's Phone: <span class="text-uppercase font-weight-bold">{{$data->phone}}</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action" disabled="">
                            Street Address/Location: <span class="text-uppercase font-weight-bold">{{$data->parent_address}}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Flush end -->
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="justify-content-center float-right">
                <a href="{{route('parent.edit.student', ['students' => Hashids::encode($data->id)])}}" class="btn btn-primary">Edit</a>
            </div>
        </div>
    </div>
@endsection
