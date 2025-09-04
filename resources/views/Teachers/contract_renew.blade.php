@extends('SRTDashboard.frame')
@section('content')

<style>
    .page-header {
        background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
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
        text-transform: uppercase;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    .btn {
        border-radius: 25px;
        font-weight: 600;
    }
    .form-control {
        border-radius: 8px;
    }
    .modal-content {
        border-radius: 15px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.2);
    }
    .badge {
        font-size: 0.85rem;
        padding: 6px 12px;
        border-radius: 20px;
    }
</style>

{{-- Header --}}
<div class="page-header mt-4 mb-4">
    <h4><i class="fas fa-file-contract"></i> My Contracts Catalog</h4>
    <button type="button" class="btn btn-light" data-toggle="modal" data-target=".bd-example-modal-lg">
        <i class="fas fa-plus"></i> Apply Contract
    </button>
</div>

{{-- Main Card --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                {{-- Modal --}}
                <div class="modal fade bd-example-modal-lg">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-light">
                                <h5 class="modal-title"><i class="fas fa-pen-nib"></i> Contract Application Form</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <form class="needs-validation" novalidate
                                      action="{{ route('contract.store') }}"
                                      method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-row">
                                        <div class="col-md-6 mb-3">
                                            <label>Contract Type</label>
                                            <select name="contract_type" required class="form-control">
                                                <option value="">-- Select Contract Type --</option>
                                                <option value="new">New contract</option>
                                                <option value="probation">Probation Contract</option>
                                                <option value="renewal">Renew Contract</option>
                                            </select>
                                            @error('contract_type')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Application Letter</label>
                                            <input type="file" name="application_letter" required class="form-control">
                                            @error('application_letter')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="fas fa-times-circle"></i> Close
                                        </button>
                                        <button type="submit" id="saveButton" class="btn btn-success">
                                            <i class="fas fa-paper-plane"></i> Submit Application
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="single-table mt-3">
                    <div class="table-responsive">
                        @if ($contracts->isEmpty())
                            <table class="table table-hover progress-table table-responsive-md">
                                <thead class="text-capitalize">
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Application Type</th>
                                        <th>Applied At</th>
                                        <th>Updated At</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" class="text-center text-danger">
                                            No Contract record found
                                        </td>
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

{{-- Scripts --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector(".needs-validation");
        const submitButton = document.getElementById("saveButton");

        if (!form || !submitButton) return;

        form.addEventListener("submit", function (event) {
            event.preventDefault();

            // Disable button + spinner
            submitButton.disabled = true;
            submitButton.innerHTML =
                `<span class="spinner-border spinner-border-sm text-white" role="status"></span> Submitting...`;

            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                submitButton.disabled = false;
                submitButton.innerHTML = `<i class="fas fa-paper-plane"></i> Submit Application`;

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Failed',
                    text: 'Please fill in all required fields before submitting.',
                    confirmButtonColor: '#185a9d'
                });

                return;
            }

            // Delay kidogo ili spinner ionekane
            setTimeout(() => {
                form.submit();
            }, 600);
        });
    });

    // Success/Failure feedback kutoka session
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session("success") }}',
            confirmButtonColor: '#43cea2'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session("error") }}',
            confirmButtonColor: '#d33'
        });
    @endif
</script>
@endsection
