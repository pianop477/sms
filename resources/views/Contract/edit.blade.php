@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5">
        <div class="card-body">
            @if ($contract->remarks == NULL)
                <h4 class="header-title">Edit Contract Application</h4>
            @else
                <h4 class="header-title">Re-apply Contract Application</h4>
                <hr>
                <div class="alert alert-danger" role="alert">
                    <p><strong>Application Status: </strong>{{$contract->status}}</p>
                    <p><strong>Reason: </strong>{{$contract->remarks}}</p>
                </div>
            @endif
            <form class="needs-validation" novalidate="" action="{{route('contract.update', ['id' => Hashids::encode($contract->id)])}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom01">Contract type</label>
                        <select name="contract_type" id="validationCustom01" required class="form-control">
                            <option value="{{$contract->contract_type}}" selected>{{$contract->contract_type}}</option>
                            <option value="new">New contract</option>
                            <option value="probation">Probation Contract</option>
                            <option value="renewal">Renew Contract</option>
                            {{-- <option value="extension"> Extend Contract</option> --}}
                        </select>
                        @error('contract_type')
                        <div class="text-danger">
                            <span>{{$message}}</span>
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom02">Application Letter</label>
                        <input type="file" name="application_letter" class="form-control" id="validationCustom02" placeholder="New Password" required="" value="">
                        @error('application_letter')
                        <div class="text-danger">
                           <span>{{$message}}</span>
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-success" id="saveButton" type="submit">Save Changes</button>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{route('contract.index')}}" class="btn btn-secondary float-right"> Go Back</a>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const form = document.querySelector(".needs-validation");
                        const submitButton = document.getElementById("saveButton"); // Tafuta button kwa ID

                        if (!form || !submitButton) return; // Kama form au button haipo, acha script isifanye kazi

                        form.addEventListener("submit", function (event) {
                            event.preventDefault(); // Zuia submission ya haraka

                            // Disable button na badilisha maandishi
                            submitButton.disabled = true;
                            submitButton.innerHTML = `<span class="spinner-border text-white" role="status" aria-hidden="true"></span> Please Wait...`;

                            // Hakikisha form haina errors kabla ya kutuma
                            if (!form.checkValidity()) {
                                form.classList.add("was-validated");
                                submitButton.disabled = false; // Warudishe button kama kuna errors
                                submitButton.innerHTML = "Save Changes";
                                return;
                            }

                            // Chelewesha submission kidogo ili button ibadilike kwanza
                            setTimeout(() => {
                                form.submit();
                            }, 500);
                        });
                    });
                </script>
@endsection
