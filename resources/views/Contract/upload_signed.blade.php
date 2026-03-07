@extends('SRTDashboard.frame')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-gradient-info text-white">
                    <h4 class="mb-0"><i class="fas fa-file-signature me-2"></i> Upload Signed Contract</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        By uploading the signed contract, you agree to all terms and conditions.
                    </div>

                    <form action="{{ route('contract.upload.signed', ['id' => Hashids::encode($contract->id)]) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-4">
                            <label>Signed Contract Document</label>
                            <input type="file" name="signed_contract" class="form-control" required accept=".pdf">
                            @error('signed_contract')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i> Upload & Activate
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
