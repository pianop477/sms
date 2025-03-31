@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-4">
            <div class="row">
                @if ($message->isEmpty())
                    <div class="col-md-12">
                        <div class="alert alert-danger" role="alert">
                            No feedbacks available
                        </div>
                    </div>
                @else
                    @foreach ( $message as $sms )
                        <div class="col-md-4">
                            <div class="card mb-3 p-1 text-center">
                                <div class="card-body">
                                    <h5 class="text-center text-capitalize">{{$sms->name}}</h5>
                                    <p class="text-center">{{$sms->email}}</p>
                                    <hr>
                                    <p>
                                        {{$sms->message}}
                                    </p>
                                </div>
                                <div class="">
                                    <ul class="d-flex justify-content-around">
                                        <li>
                                            <strong>Posted on:</strong> <span>{{\Carbon\Carbon::parse($sms->created_at)->format('d-M-Y H:i')}}</span>
                                        </li>
                                        <li>
                                            <a href="{{route('delete.post', ['sms' => Hashids::encode($sms->id)])}}" onclick="return confirm('Are you sure you want to delete this post?')"><i class="ti-trash text-danger"></i></a>
                                        </li>
                                        <li>
                                            <a href="{{route('reply.post', ['sms' => Hashids::encode($sms->id)])}}" onclick="return confirm('Are you sure you want to reply this post?')"><i class="ti-share text-success"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="d-flex justify-content-center">
                {{$message->links('vendor.pagination.bootstrap-5')}}
            </div>
    </div>
@endsection
