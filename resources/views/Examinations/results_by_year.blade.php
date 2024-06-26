{{-- resources/views/Examinations/results_by_year.blade.php --}}
@extends('SRTDashboard.frame')

@section('content')
                <div class="row">
                    <!-- Links And Buttons start -->
                    <div class="col-md-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-8">
                                        <h4 class="header-title text-uppercase">Examination Results for {{ $year }}</h4>
                                    </div>
                                    <div class="col-4">
                                        <a href="" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                                    </div>
                                </div>
                                @foreach ($examTypes as $exam )
                                    <a href="{{ route('exams.byType', ['year' => $year, 'type' => $exam->exam_type]) }}">
                                        <button type="button" class="list-group-item list-group-item-action">
                                            <h6 class="text-primary text-capitalize"><i class="fas fa-chevron-right"></i> {{ $exam->exam_type }} - {{ DateTime::createFromFormat('!m', $exam->exam_month)->format('F') }}</h6>
                                        </button>
                                    </a>
                                @endforeach
                            </div>
                             <!-- Custom Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $examTypes->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                    <!-- Links And Buttons end -->
                </div>
@endsection
