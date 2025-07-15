@extends('SRTDashboard.frame')

@section('content')
<div class="row">
  <div class="col-12 mt-5">
    <div class="card">
      <div class="card-body">
        <div class="row mb-4">
          <div class="col-8">
            <h4 class="header-title text-capitalize">Graduate Students Batches</h4>
          </div>
          <div class="col-4">
            <a href="{{ route('graduate.students') }}" class="float-right btn btn-info btn-xs">
              <i class="fas fa-chevron-left"></i> Back
            </a>
          </div>
        </div>
        <hr>

        <div class="row">
            @if ($graduationYears->isEmpty())
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        <h6>No graduation batches found.</h6>
                    </div>
                </div>
            @else
                @foreach ($graduationYears as $gradYear)
                    <div class="col-12 mb-3">
                        <button
                        type="button"
                        class="btn btn-block text-left"
                        style="background: rgb(82, 82, 230); color:white; border-radius: 20px; font-size: 18px;"
                        data-toggle="collapse"
                        data-target="#batch-{{ $gradYear }}"
                        aria-expanded="false"
                        aria-controls="batch-{{ $gradYear }}"
                        onclick="toggleBatch('{{ $gradYear }}', this)">
                        <i class="fas fa-graduation-cap"></i> Graduation Batch/{{ $gradYear }}
                        {{-- <i class="fas fa-chevron-down float-right"></i> --}}
                        </button>

                        <div id="batch-{{ $gradYear }}"
                            class="batch-table collapse mt-2"
                            style="max-height: 400px; overflow-y: auto;">
                        @php $batchStudents = $GraduatedStudents->where('graduated_at', $gradYear); @endphp

                        @if($batchStudents->isNotEmpty())
                        <div class="d-flex justify-content-between align-items-center mb-2" style="margin-top: 10px">
                            <p class="mb-0">
                            <i class="fas fa-users"></i> Graduated Students ({{ $batchStudents->count() }})
                            </p>
                            <div class="float-right mr-3">
                                <form action="{{route('revert.student.batch', ['year' => $gradYear])}}" method="POST" id="revertForm-{{$gradYear}}">
                                    @csrf
                                    @method('PUT')
                                    <button type="button" class="btn btn-warning btn-xs p-1" style="border-radius: 8px;"
                                            onclick="if(confirm('Are you sure you want to revert this batch?')) { document.getElementById('revertForm-{{$gradYear}}').submit(); }">
                                        <i class="fas fa-refresh"></i>
                                        Revert
                                    </button>
                                </form>
                            </div>
                            <a href="{{ route('graduate.students.export', ['year' => $gradYear]) }}"
                                target="_blank"
                                class="btn btn-success btn-xs float-right" style="border-radius: 8px;">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        </div>
                        <table class="table table-sm table-responsive-md table-striped table-hover mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>#</th><th>Admission #</th><th>First</th><th>Middle</th><th>Last</th><th>Gender</th><th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($batchStudents as $key => $s)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ strtoupper($s->admission_number) }}</td>
                                <td>{{ ucwords(strtolower($s->first_name)) }}</td>
                                <td>{{ ucwords(strtolower($s->middle_name)) }}</td>
                                <td>{{ ucwords(strtolower($s->last_name)) }}</td>
                                <td>{{ ucwords(strtolower($s->gender)) }}</td>
                                <td><span class="badge bg-success text-white">Graduated</span></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="alert alert-info mb-0"><strong>Info:</strong> No graduate students in this batch.</div>
                        @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- jQuery and Bootstrap JS (ensure proper order) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleBatch(year, btn) {
  // Toggle collapse for this batch
  const $panel = $('#batch-' + year);
  $panel.collapse('toggle');

  // Toggle button styles and icon
  $(btn).toggleClass('btn-outline-primary btn-primary');
  $(btn).find('i.fa-chevron-down, i.fa-chevron-up').toggleClass('fa-chevron-down fa-chevron-up');
}

$(document).ready(function() {
  // Initialize collapse states (closed by default)
  $('.collapse').collapse({ toggle: false });
});
</script>
@endpush

@push('styles')
<style>
.btn-block {
  padding: 12px 20px;
  font-size: 16px;
  border-radius: 5px;
  transition: all .3s;
}
.batch-table {
  overflow-y: auto;
}
.table thead th {
  position: sticky;
  top: 0;
  background: #f8f9fa;
  z-index: 10;
}
</style>
@endpush
