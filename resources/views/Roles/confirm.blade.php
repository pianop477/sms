@extends('SRTDashboard.frame')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card col-md-6 mt-5">
            <div class="card-body text-center">
                <h4 class="card-title">Change Confirmation Request</h4>
                <p class="card-text text-danger">
                    The selected teacher already has another assigned role. Do you want to proceed with changing the role?
                </p>

                <form action="{{ route('roles.confirmProceed') }}" method="POST">
                    @csrf
                    <input type="hidden" name="teacher_id" value="{{ session('confirm_role_change.teacher_id') }}">
                    <input type="hidden" name="new_role" value="{{ session('confirm_role_change.new_role') }}">

                    <button type="submit" class="btn btn-success">Yes, Proceed</button>
                    <a href="{{ route('roles.cancelConfirmation') }}" class="btn btn-danger">No, Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
