@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase text-center">My Contracts Catalog</h4>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-xs btn-info float-right" data-toggle="modal" data-target=".bd-example-modal-lg">
                            <i class="fas fa-plus"></i> Apply Contract
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Contract Application Form</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('contract.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom01">Contract type</label>
                                                    <select name="contract_type" id="validationCustom01" required class="form-control">
                                                        <option value="">-- Select Contract Type --</option>
                                                        <option value="new">New contract</option>
                                                        <option value="probation">Probation Contract</option>
                                                        <option value="renewal">Renew Contract</option>
                                                    </select>
                                                    @error('contract_type')
                                                    <div class="text-danger">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom02">Application Letter</label>
                                                    <input type="file" required name="application_letter" class="form-control" id="validationCustom02" placeholder="" required="" value="">
                                                    @error('application_letter')
                                                    <div class="text-danger">
                                                       {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" id="saveButton" class="btn btn-success">Submit Application</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        @if ($contracts->isEmpty())
                            <table class="table table-hover progress-table" id="myTable">
                                <thead class="text-capitalize">
                                    <th scope="col">#</th>
                                    <th scope="col">Application Type</th>
                                    <th scope="col">Applied_at</th>
                                    <th scope="col">Updated_at</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" class="text-center text-danger">No Contract record found</td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            @if ($contracts->contains('status', 'pending'))
                                <table class="table table-hover progress-table" id="myTable">
                                    <thead class="text-capitalize">
                                        <tr class="text-center">
                                            <th scope="col">#</th>
                                            <th scope="col">Contract Type</th>
                                            <th scope="col">Applied on</th>
                                            <th scope="col">Updated_at</th>
                                            <th scope="col" class="text-center">Status</th>
                                            <th scope="col">Attachment</th>
                                            <th scope="col" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contracts as $row )
                                            <tr class="text-capitalize text-center">
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$row->contract_type}} contract</td>
                                                <td>{{\Carbon\Carbon::parse($row->applied_at)->format('d-m-Y H:i')}}</td>
                                                <td>{{\Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i')}}</td>
                                                <td class="text-center text-white">
                                                    @if ($row->status == 'pending')
                                                        <span class="badge bg-warning">{{$row->status}}</span>
                                                    @elseif ($row->status == 'approved')
                                                        <span class="badge bg-success">{{$row->status}}</span>
                                                    @elseif ($row->status == 'expired')
                                                        <span class="badge bg-danger">{{$row->status}}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{$row->status}}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{route('preview.my.application', ['id' => Hashids::encode($row->id)])}}" class="" target="_blank">
                                                        <i class="fas fa-paperclip text-success"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <ul class="d-flex justify-content-center">
                                                        <li class="mr-3"><a href="{{route('contract.edit', ['id' => Hashids::encode($row->id)])}}" class="text-primary"><i class="ti-pencil"></i></a></li>
                                                        <li class="mr-3"><a href="{{route('contract.destroy', ['id' => Hashids::encode($row->id)])}}" onclick="return confirm('Are you sure you want to delete this application?')" class="text-danger"><i class="ti-trash"></i></a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @elseif ($contracts->contains('status', 'approved'))
                                <table class="table table-hover progress-table" id="myTable">
                                    <thead class="text-capitalize">
                                        <tr class="text-center">
                                            <th scope="col">#</th>
                                            <th scope="col">Contract Type</th>
                                            <th scope="col">Applied_at</th>
                                            <th scope="col">Approved_at</th>
                                            <th scope="col">Expire_at</th>
                                            <th scope="col">Contract length</th>
                                            <th scope="col" class="text-center">Status</th>
                                            <th scope="col">Attachment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contracts as $row )
                                            <tr class="text-center text-capitalize">
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$row->contract_type}} contract</td>
                                                <td>{{\Carbon\Carbon::parse($row->applied_at)->format('d-m-Y H:i')}}</td>
                                                <td>{{\Carbon\Carbon::parse($row->approved_at)->format('d-m-Y H:i')}}</td>
                                                <td>{{\Carbon\Carbon::parse($row->end_date)->format('d-m-Y H:i')}}</td>
                                                <td>{{$row->duration}} Months</td>
                                                <td class="text-center text-white">
                                                    @if ($row->status == 'pending')
                                                        <span class="badge bg-warning">{{$row->status}}</span>
                                                    @elseif ($row->status == 'approved')
                                                        <span class="badge bg-success">Active</span>
                                                    @elseif ($row->status == 'expired')
                                                        <span class="badge bg-danger">{{$row->status}}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{$row->status}}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{route('contract.download', ['id' => Hashids::encode($row->id)])}}" class="btn btn-info btn-xs" target="_blank"> Preview</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @elseif ($contracts->contains('status', 'rejected'))
                                <table class="table table-hover progress-table" id="myTable">
                                    <thead class="text-capitalize">
                                        <tr class="text-center">
                                            <th scope="col">#</th>
                                            <th scope="col">Contract Type</th>
                                            <th scope="col">Applied_at</th>
                                            <th scope="col">Returned_at</th>
                                            <th scope="col" class="text-center">Status</th>
                                            <th scope="col">Attachment</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contracts as $row )
                                            <tr class="text-center text-capitalize">
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$row->contract_type}} contract</td>
                                                <td>{{\Carbon\Carbon::parse($row->applied_at)->format('d-m-Y H:i')}}</td>
                                                <td>{{\Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i')}}</td>
                                                <td class="text-center text-white">
                                                   <span class="badge bg-danger">{{$row->status}}</span>
                                                </td>
                                                <td>
                                                    <a href="{{route('preview.my.application', ['id' => Hashids::encode($row->id)])}}" class="btn btn-info btn-xs" target="_blank"> View</a>
                                                </td>
                                                <td>
                                                    <a href="{{route('contract.edit', ['id' => Hashids::encode($row->id)])}}" class="btn btn-success btn-xs" target=""> Re-apply</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <table class="table table-hover progress-table" id="myTable">
                                    <thead class="text-capitalize">
                                        <tr class="text-center">
                                            <th scope="col">#</th>
                                            <th scope="col">Contract Type</th>
                                            <th scope="col">Applied_at</th>
                                            <th scope="col">Approved_at</th>
                                            <th scope="col">Expire_at</th>
                                            <th scope="col">Contract length</th>
                                            <th scope="col" class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contracts as $row )
                                            <tr class="text-center text-capitalize">
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$row->contract_type}} contract</td>
                                                <td>{{\Carbon\Carbon::parse($row->applied_at)->format('d-m-Y H:i')}}</td>
                                                <td>{{\Carbon\Carbon::parse($row->approved_at)->format('d-m-Y H:i')}}</td>
                                                <td>{{\Carbon\Carbon::parse($row->end_date)->format('d-m-Y H:i')}}</td>
                                                <td>{{$row->duration}} Months</td>
                                                <td class="text-center text-white">
                                                    @if ($row->status == 'pending')
                                                        <span class="badge bg-warning">{{$row->status}}</span>
                                                    @elseif ($row->status == 'approved')
                                                        <span class="badge bg-success">{{$row->status}}</span>
                                                    @elseif ($row->status == 'expired')
                                                        <span class="badge bg-danger">{{$row->status}}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{$row->status}}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        @endif
                    </div>
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
                submitButton.innerHTML = "Submit Application";
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
