@extends('SRTDashboard.frame')
    @section('content')
    @if ($studentList->isEmpty())
        <div class="alert alert-warning text-center">
            <h6>No Students Records Available for this Class</h6>
            <hr>
            <p><a href="{{route('home')}}" class="btn btn-primary btn-sm">Go Back</a></p>
        </div>
    @else
    <form action="{{ route('store.attendance', $student_class->id) }}" method="POST" enctype="multipart/form-data" onsubmit="showPreloader()">
        @csrf
        <div class="single-table">
            <div class="table-responsive-lg">
                <table class="table">
                    <thead class="text-capitalize bg-info">
                        <tr class="text-white">
                            <th scope="col">#</th>
                            <th scope="col">AdmNo.</th>
                            <th scope="col">Name</th>
                            <th scope="col">Sex</th>
                            <th scope="col">Group</th>
                            <th scope="col" colspan="3" class="text-center">Attendance Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentList as $student)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <input type="hidden" name="student_id[]" value="{{ $student->id }}">
                                    {{ str_pad($student->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="text-uppercase">
                                    <a href="{{ route('Students.show', $student->id) }}">{{ $student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name }}</a>
                                </td>
                                <td class="text-uppercase">{{ $student->gender[0] }}</td>
                                <td class="text-uppercase">
                                    <input type="hidden" name="group[{{$student->id}}]" value="{{$student->group}}">
                                    {{ $student->group }}
                                </td>
                                <td>
                                    <ul class="d-flex justify-content-center">
                                        <li class="mr-3">
                                            <input type="radio" name="attendance_status[{{ $student->id }}]" value="present" {{ old('attendance_status.' . $student->id) == 'present' ? 'checked' : '' }}> Pres
                                        </li>
                                        <li class="mr-3">
                                            <input type="radio" name="attendance_status[{{ $student->id }}]" value="absent" {{ old('attendance_status.' . $student->id) == 'absent' ? 'checked' : '' }}> Abs
                                        </li>
                                        <li class="mr-3">
                                            <input type="radio" name="attendance_status[{{ $student->id }}]" value="permission" {{ old('attendance_status.' . $student->id) == 'permission' ? 'checked' : '' }}> Perm
                                        </li>
                                    </ul>
                                    @error('attendance_status.' . $student->id)
                                        <span class="text-sm text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer text-center">
            <ul class="d-flex justify-content-center">
                <li class="mr-3"><button type="submit" class="btn btn-primary">Submit Attendance</button></li>
                <li><a href="{{route('today.attendance', $student_class->id)}}" class="btn btn-success">Check Today Report</a></li>
            </ul>
        </div>
    </form>
    @endif

    @endsection
