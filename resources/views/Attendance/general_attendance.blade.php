@extends('SRTDashboard.frame')

@section('content')
<div class="card mt-5">
    <div class="card-body">
        <h4 class="header-title text-uppercase text-center">Generate Attendance Report</h4>
        <form class="needs-validation" novalidate
              action="{{route('class.attendance.report')}}"
              method="POST"
              enctype="multipart/form-data"
              onsubmit="showPreloader(event)">
            @csrf
            <div class="form-row">
                <div class="col-md-3 mb-3">
                    <label for="validationCustom01">Class</label>
                    <select name="class" id="validationCustom01" class="form-control text-uppercase" required>
                        <option value="">--Select Classes--</option>
                        @foreach ($classes as $class)
                            <option value="{{$class->id}}">{{$class->class_name}}</option>
                        @endforeach
                    </select>
                    @error('class')
                    <div class="text-danger text-sm">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="validationCustom02">Start Date</label>
                    <input type="date" name="start" class="form-control" id="validationCustom02" required value="{{old('start')}}" max="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                    @error('start')
                    <div class="text-danger text-sm">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="validationCustom02">End Date</label>
                    <input type="date" name="end" class="form-control" id="validationCustom02" required value="{{old('end')}}" max="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                    @error('end')
                    <div class="text-danger text-sm">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="validationCustom02">Stream</label>
                    <select name="stream" class="text-uppercase form-control" id="validationCustom02">
                        <option value="all" selected>All</option>
                        <option value="a">a</option>
                        <option value="b">b</option>
                        <option value="c">c</option>
                    </select>
                    @error('stream')
                        <div class="text-danger text-sm">
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
