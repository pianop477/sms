@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-4">
            <div class="row">
                @foreach ( $message as $sms )
                    <div class="col-md-4">
                        <div class="card mb-3 p-1 text-center">
                            <div class="card-body">
                                <h5 class="text-center text-capitalize">{{$sms->name}}</h5>
                                <p class="text-center">{{$sms->email}}</p>
                                <hr>
                                <p>
                                    <strong>Message:</strong>  {{$sms->message}}
                                </p>
                            </div>
                            <div class="">
                                <ul class="d-flex justify-content-around">
                                    <li>
                                        <strong>Posted on:</strong> <span>{{\Carbon\Carbon::parse($sms->created_at)->format('d-F-Y H:i')}}</span>
                                    </li>
                                    <li>
                                        <a href="{{route('delete.post', $sms->id)}}" onclick="return confirm('Are you sure you want to delete this post?')"><i class="ti-trash text-danger"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center">
                {{$message->links('vendor.pagination.bootstrap-5')}}
            </div>
    </div>
@endsection