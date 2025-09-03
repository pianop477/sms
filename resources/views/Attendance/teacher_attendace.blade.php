@extends('SRTDashboard.frame')
@section('content')

<style>
    .page-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .page-header h4 {
        margin: 0;
        font-weight: 600;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    .btn-primary {
        border-radius: 25px;
        font-weight: 600;
        padding: 0.6rem 1.3rem;
    }
    .form-control {
        border-radius: 8px;
    }
    #preloader {
        position: fixed;
        top:0; left:0;
        width:100%; height:100%;
        background: rgba(255,255,255,0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
</style>

{{-- Header --}}
<div class="page-header mt-4 mb-4">
    <h4><i class="fas fa-file-alt"></i> Generate Attendance Report</h4>
    <a href="{{ route('home') }}" class="btn btn-light">
        <i class="fas fa-circle-arrow-left"></i> Back
    </a>
</div>

{{-- Form Card --}}
<div class="card">
    <div class="card-body">
        <form class="needs-validation" novalidate
              action="{{ route('attendance.generate.report', ['classTeacher' => Hashids::encode($classTeacher->id)]) }}"
              method="POST" enctype="multipart/form-data" onsubmit="showPreloader(event)">
            @csrf
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold">Class</label>
                    <input type="text" disabled name="class"
                           class="form-control text-uppercase"
                           value="{{ $classTeacher->class_name }}" required>
                    @error('class')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold">Start Date</label>
                    <input type="date" name="start" class="form-control"
                           required value="{{ old('month') }}"
                           max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                    @error('month')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold">End Date</label>
                    <input type="date" name="end" class="form-control"
                           required value="{{ old('month') }}"
                           max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                    @error('month')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <button class="btn btn-primary float-right" type="submit">
                    <i class="fas fa-cogs"></i> Generate
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Preloader --}}
<div id="preloader" style="display:none;">
    <div class="text-center">
        <div class="spinner-border text-primary mb-3" style="width:3rem;height:3rem;" role="status"></div>
        <h5>Generating Report...</h5>
        <p class="text-muted">Retrieving Data, please wait...</p>
    </div>
</div>

{{-- Scripts --}}
<script>
    function showPreloader(event) {
        const form = event.target;
        const submitButton = form.querySelector('button[type="submit"]');

        if (form.checkValidity()) {
            // Onyesha preloader
            document.getElementById("preloader").style.display = "flex";

            // Disable button
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            submitButton.disabled = true;
        } else {
            event.preventDefault();
            form.classList.add('was-validated');

            // SweetAlert notification
            Swal.fire({
                icon: 'error',
                title: 'Missing Fields',
                text: 'Please fill all required fields before generating the report.',
                confirmButtonColor: '#2575fc'
            });
        }
    }

    // Reset button on back navigation
    window.addEventListener("pageshow", function(event) {
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.innerHTML = '<i class="fas fa-cogs"></i> Generate';
            submitButton.disabled = false;
        }
        document.getElementById("preloader").style.display = "none";
    });
</script>
@endsection
