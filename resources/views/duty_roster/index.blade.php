@extends('SRTDashboard.frame')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Pakia Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- Pakia Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<style>
    /* Override Select2 default styles to match Bootstrap form-control */
    .select2-container .select2-selection--single {
        height: 38px !important;  /* Ensure same height as form-control */
        border: 1px solid #ccc !important; /* Border to match Bootstrap */
        border-radius: 4px !important; /* Rounded corners to match Bootstrap */
        padding: 6px 12px !important; /* Padding to match form-control */
    }
    .select2-container {
    width: 100% !important; /* Ensure Select2 takes full width of the parent */
    }

    .select2-container {
        width: 100% !important; /* Set full width for Select2 container */
        max-width: 100% !important; /* Ensure it does not exceed container */
    }

    .select2-selection--single {
        width: 100% !important; /* Set width of the selection box */
    }
    .select2-selection--single {
        width: 100% !important; /* Ensure selection box inside Select2 also takes full width */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #495057; /* Match the default text color */
        line-height: 26px; /* Align text */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px; /* Arrow should be aligned */
    }

</style>
<div class="col-12">
    <!-- Button to trigger modal -->
    <div class="text-right mb-3">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#assignRosterModal">
            <i class="fas fa-plus"></i> Assign Duty Roster
        </button>
    </div>

    <!-- Assign Roster Modal -->
    <div class="modal fade" id="assignRosterModal" tabindex="-1" role="dialog" aria-labelledby="assignRosterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" id="assignRosterModalLabel">Assign Teacher(s) Duty Roster</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate action="{{route('tod.roster.store')}}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="teacher_ids">Select Teacher(s)</label>
                                <select name="teacher_ids[]" id="teacher_ids" class="form-control select2-multiple" multiple="multiple" required>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}"
                                            {{ (collect(old('teacher_ids'))->contains($teacher->id)) ? 'selected' : '' }}>
                                            {{ strtoupper($teacher->first_name. ' '. $teacher->last_name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacher_ids')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" class="form-control" id="start_date"
                                    value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" class="form-control" id="end_date"
                                    value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-success" id="saveButton" type="submit">
                                Assign Duty
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- List of Assigned Rosters --}}
    <div class="card mt-3">
        <div class="card-body">
            <h4 class="header-title text-center text-uppercase">Assigned Duty Rosters</h4>
            <div class="table-responsive">
                <table class="table table-hover progress-table" id="myTable">
                    <thead class="text-capitalize">
                        <tr>
                            <th>#</th>
                            <th>Roster ID#</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Issued by</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rosters as $key => $roster)
                            @php
                                $firstRoster = $roster->first();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ strtoupper($firstRoster->roster_id) }}</td>
                                <td>{{ $firstRoster->start_date ?? "N/A" }}</td>
                                <td>{{ $firstRoster->end_date ?? "N/A" }}</td>
                                <td>{{ ucwords(strtolower($firstRoster->created_by ?? 'N/A')) }}</td>
                                <td>
                                    @if ($firstRoster->status == 'active')
                                        <span class="badge badge-warning">Active</span>
                                    @elseif ($firstRoster->status == 'pending')
                                        <span class="badge badge-secondary">Pending</span>
                                    @elseif ($firstRoster->status == 'completed')
                                        <span class="badge badge-success">Completed</span>
                                    @elseif ($firstRoster->status == 'cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <ul class="d-flex justify-content-center">
                                        <li class="mr-3">
                                            <!-- Button to trigger modal -->
                                            <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#rosterModal{{ $key }}">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </li>
                                        @if ($firstRoster->status == "pending")
                                            <li class="mr-3">
                                                <form method="POST" action="{{ route('tod.roster.activate', $firstRoster->id) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button class="btn btn-xs btn-success" type="submit" title="Activate" onclick="return confirm('Activate this roster?');">
                                                        <i class="fas fa-check"></i> Activate
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        <li>
                                            <form action="{{ route('tod.roster.destroy', $firstRoster->id) }}" method="POST" onsubmit="return confirm('Delete this roster?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-xs btn-danger" type="submit" title="Delete">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="rosterModal{{ $key }}" tabindex="-1" role="dialog" aria-labelledby="rosterModalLabel{{ $key }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="rosterModalLabel{{ $key }}">
                                                Roster #{{ strtoupper($firstRoster->roster_id) }} Details
                                            </h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Start Date:</strong> <span class="text-danger">{{ $firstRoster->start_date ?? 'N/A' }}</span></p>
                                            <p><strong>End Date:</strong> <span class="text-danger">{{ $firstRoster->end_date ?? 'N/A' }}</span></p>
                                            <p><strong>Status:</strong>
                                                @if ($firstRoster->status == 'active')
                                                    <span class="badge badge-warning">{{ ucfirst($firstRoster->status) }}</span>
                                                @elseif ($firstRoster->status == 'pending')
                                                    <span class="badge badge-secondary">{{ ucfirst($firstRoster->status) }}</span>
                                                @elseif ($firstRoster->status == 'completed')
                                                    <span class="badge badge-success">{{ ucfirst($firstRoster->status) }}</span>
                                                @elseif ($firstRoster->status == 'cancelled')
                                                    <span class="badge badge-danger">{{ ucfirst($firstRoster->status) }}</span>
                                                @endif
                                            </p>
                                            <hr>
                                            <h6>Assigned Teachers:</h6>
                                            <ul>
                                                @foreach($roster as $teacher)
                                                    <li class="text-italic">{{$loop->iteration}}. {{ ucwords(strtolower($teacher->first_name)) }} {{ ucwords(strtolower($teacher->last_name)) }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                            @if ($firstRoster->status == "pending")
                                                <form action="{{route('tod.roster.activate', $firstRoster->id)}}" class="d-inline" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button class="btn btn-success btn-sm" type="submit" onclick="return confirm('Activate this roster?');">
                                                        <i class="fas fa-check"></i> Activate
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('tod.roster.destroy', $firstRoster->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm" type="submit" onclick="return confirm('Delete this roster?');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($rosters->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center text-muted">No duty rosters assigned yet</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector(".needs-validation");
        const submitButton = document.getElementById("saveButton");

        if (!form || !submitButton) return;

        form.addEventListener("submit", function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add("was-validated");
                return;
            }
            submitButton.disabled = true;
            submitButton.innerHTML =
                `<span class="spinner-border spinner-border-sm text-white" role="status"></span> Please Wait...`;
        });

        // Reset form when modal is closed
        $('#assignRosterModal').on('hidden.bs.modal', function () {
            form.reset();
            form.classList.remove("was-validated");
            submitButton.disabled = false;
            submitButton.innerHTML = 'Assign Duty';
        });

        window.onload = function() {
            // Hakikisha jQuery na Select2 inapatikana
            if (typeof $.fn.select2 !== 'undefined') {
                // Fanya initialization ya Select2
                $('#teacher_ids').select2({
                    placeholder: "Search Teachers...",
                    allowClear: true
                }).on('select2:open', function () {
                    $('.select2-results__option').css('text-transform', 'capitalize');  // Capitalize option text
                });
            } else {
                console.error("Select2 haijapakiwa!");
            }
        };
    });
</script>
@endsection
