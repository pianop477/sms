@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12 mt-2">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title">Examination Results for ({{ $date }})</h4>
                </div>
                <div class="col-2">
                    <a href="{{ route('results.byExamType', ['course' => Hashids::encode($id[0]), 'year' => $year, 'examType' => Hashids::encode($exam_id[0])])}}" class="float-right">
                        <i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i>
                    </a>
                </div>
            </div>
            <!-- Embed the PDF in an iframe -->
            <iframe src="{{ $pdfUrl }}" width="100%" height="800px"></iframe>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".month-toggle").forEach(button => {
            button.addEventListener("click", function () {
                let monthDiv = document.getElementById(this.dataset.month);
                if (monthDiv.style.display === "none") {
                    monthDiv.style.display = "block";
                } else {
                    monthDiv.style.display = "none";
                }
            });
        });
    });
</script>

@endsection
