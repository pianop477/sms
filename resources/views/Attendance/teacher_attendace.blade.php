@extends('SRTDashboard.frame')

    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title">Generate Attendance Report</h4>
                </div>
                <div class="col-2">
                    <a href="{{ route('home') }}" class="btn btn-info float-right">
                        <i class="fas fa-circle-arrow-left" style=""></i> Back
                    </a>
                </div>
            </div>
            <form class="needs-validation" novalidate="" action="{{ route('attendance.generate.report', ['classTeacher' => Hashids::encode($classTeacher->id)]) }}" method="POST" enctype="multipart/form-data" onsubmit="showPreloader(event)">
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
                        <label for="validationCustom02">Start Date</label>
                        <input type="date" name="start" class="form-control" id="validationCustom02" placeholder="" required  value="{{ old('month') }}" max="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                        @error('month')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">End Date</label>
                        <input type="date" name="end" class="form-control" id="validationCustom02" placeholder="" required value="{{ old('month') }}" max="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
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
            const form = event.target; // Chukua form element
            const submitButton = form.querySelector('button[type="submit"]');

            if (form.checkValidity()) {
                // Onyesha preloader ndani ya button na disable button
                submitButton.innerHTML = '<i class="ti-settings"></i> Generating...';
                submitButton.disabled = true;

                // Ruhusu submission ifanyike kawaida (ili PDF ipakuliwe)
            } else {
                // Zuia submission ikiwa form si sahihi na onyesha validation errors
                event.preventDefault();
                form.classList.add('was-validated');
            }
        }

        // **Hakikisha button inarudi kwenye hali yake ikiwa user anarudi nyuma**
        window.addEventListener("pageshow", function(event) {
            const submitButton = document.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = '<i class="ti-settings"></i> Generate';
                submitButton.disabled = false;
            }
        });
    </script>


    @endsection
