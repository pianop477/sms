@extends('SRTDashboard.frame')

@section('content')
<div class="card mt-5">
    <div class="card-body">
        <h4 class="header-title text-uppercase text-center">Generate Attendance Report</h4>
        <form class="needs-validation" novalidate="" action="{{route('manage.attendance')}}" method="POST" enctype="multipart/form-data" onsubmit="showPreloader()">
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
                    <input type="month" name="start" class="form-control" id="validationCustom02" placeholder="Last name" required value="{{old('start_date')}}">
                    @error('start_date')
                    <div class="invalid-feedback">
                       {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom02">To Month</label>
                    <input type="month" name="end" class="form-control" id="validationCustom02" placeholder="Last name" required value="{{old('end_date')}}">
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
    // Prevent caching of the preloader state
    if (performance.navigation.type === 2) {
        // If back/forward navigation is detected
        window.location.reload(); // Force a page reload
    }

    // Alternative for browsers that support `pageshow`
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

    // Ensure the preloader is not shown unnecessarily
    document.addEventListener("DOMContentLoaded", function () {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.style.display = 'none';
        }
    });
    // Hide preloader on page load
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('preloader').style.display = 'none';
    });

    // Show preloader when the form is submitted
    function showPreloader() {
        document.getElementById('preloader').style.display = 'block';
    }
</script>
@endsection
