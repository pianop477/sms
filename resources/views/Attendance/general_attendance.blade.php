@extends('SRTDashboard.frame')

@section('content')
<div class="card mt-5">
    <div class="card-body">
        <h4 class="header-title text-uppercase text-center">Generate Attendance Report</h4>
        <form class="needs-validation" novalidate
              action="{{route('manage.attendance')}}"
              method="POST"
              enctype="multipart/form-data"
              onsubmit="showPreloader(event)">
            @csrf
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Class</label>
                    <select name="class" id="validationCustom01" class="form-control text-uppercase" required>
                        <option value="">-- Select Class --</option>
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
                    <label for="validationCustom02">From Month</label>
                    <input type="month" name="start" class="form-control" id="validationCustom02" required value="{{old('start_date')}}" max="{{\Carbon\Carbon::now()->format('Y-m')}}">
                    @error('start_date')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom02">To Month</label>
                    <input type="month" name="end" class="form-control" id="validationCustom02" required value="{{old('end_date')}}" max="{{\Carbon\Carbon::now()->format('Y-m')}}">
                    @error('end_date')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="col-12 justify-content-center">
                    <button class="btn btn-primary float-right" type="submit"><i class="ti-settings"></i> Generate</button>
                </div>
            </div>

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
        </form>
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
