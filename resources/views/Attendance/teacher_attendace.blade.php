@extends('SRTDashboard.frame')

    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title">Generate Attendance Report</h4>
                </div>
                <div class="col-2">
                    <a href="{{ route('home') }}">
                        <i class="fas fa-circle-arrow-left text-secondary" style="font-size: 2rem"></i>
                    </a>
                </div>
            </div>
            <form class="needs-validation" novalidate="" action="{{ route('attendance.generate.report', $classTeacher->id) }}" method="POST" enctype="multipart/form-data" onsubmit="showPreloader(event)">
                @csrf
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Class</label>
                        <input type="text" disabled name="class" class="form-control text-uppercase" value="{{ $classTeacher->class_name }}" id="validationCustom02" required>
                        @error('class')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Start Month</label>
                        <input type="month" name="start" class="form-control" id="validationCustom02" placeholder="" required  value="{{ old('month') }}" max="{{\Carbon\Carbon::now()->format('Y-m')}}">
                        @error('month')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">End Month</label>
                        <input type="month" name="end" class="form-control" id="validationCustom02" placeholder="" required value="{{ old('month') }}" max="{{\Carbon\Carbon::now()->format('Y-m')}}">
                        @error('month')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <button class="btn btn-primary float-right" type="submit"><i class="ti-settings"></i> Generate</button>
                </div>
            </form>
            <div id="preloader" style="display:none;">
                <div class="error-area ptb--100 text-center">
                    <div class="container">
                        <div class="error-content">
                            <h5>Loading....</h5>
                            <p>Retrieving Data, please wait.....</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showPreloader(event) {
            const form = event.target; // Get the form element

            if (form.checkValidity()) {
                // Form is valid; show the preloader
                document.getElementById('preloader').style.display = 'block';
            } else {
                // Form is invalid; prevent submission
                event.preventDefault();
                form.classList.add('was-validated'); // Add Bootstrap validation styles
            }
        }
    </script>

    @endsection
