@extends('SRTDashboard.frame')
@section('content')
    <div class="col-md-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-center text-uppercase">Select Month</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{ route('results.examTypesByClass', ['school' => $school->id, 'year' => $year, 'class' => $class->id]) }}" class="float-right"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                    </div>
                </div>
                <div class="list-group">
                    @if ($groupedByMonth->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            <h6>No Result Records found</h6>
                        </div>
                    @else
                    <table class="table table-responsive-md table-hover">
                        <tbody>
                            @foreach ($groupedByMonth as $month => $results)
                                @php
                                    $firstResult = $results->first();
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('results.resultsByMonth', ['school' => $school->id, 'year' => $year, 'class' => $class->id, 'examType' => $examType, 'month' => $month]) }}">
                                            <h6 class="text-primary text-capitalize"><i class="fas fa-chevron-right"></i> {{ $month }} Results Link</h6>
                                        </a>
                                    </td>
                                    @if ($firstResult->status == 1)
                                        <td>
                                            <form action="{{ route('publish.results', ['school' => $school->id, 'year' => $year, 'class' => $class->id, 'examType' => $examType, 'month' => $month]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-primary btn-xs" onclick="return confirm('Are you sure you want to publish the results? Once you publish, the results will be visible to all parents.')">Publish</button>
                                            </form>
                                        </td>
                                    @else
                                        <td>
                                            <form action="{{ route('unpublish.results', ['school' => $school->id, 'year' => $year, 'class' => $class->id, 'examType' => $examType, 'month' => $month]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to block the results? Parents will no longer be able to access the results.')">Unpublish</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
