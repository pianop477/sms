
@extends('SRTDashboard.frame')
@section('content')
    <div class="col-12">
        {{-- schools list --}}
        <div class="card mt-5">
            <div class="card-body">
                <h4 class="header-title text-center text-uppercase">Failed Login attempts</h4>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-capitalize">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">IP Address</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">User Agent</th>
                                    <th scope="col">Attempted_at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($attempts->isEmpty())
                                    <tr>
                                        <td class="text-danger text-center" colspan="5">No any failed login attempts</td>
                                    </tr>
                                @else
                                @foreach ($attempts as $row )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase">
                                            {{$row->ip}}
                                        </td>
                                        <td class="text-capitalize">
                                            {{$row->username}}
                                        </td>
                                        <td class="">
                                            {{$row->user_agent}}
                                        </td>
                                        <td class="">{{$row->attempted_at}}</td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Please Wait...`;

                // Hakikisha form haina errors kabla ya kutuma
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false; // Warudishe button kama kuna errors
                    submitButton.innerHTML = "Submit";
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
