<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers Export</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Teachers Export Report</h1>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>MEMBER ID</th>
                <th>GENDER</th>
                <th>NIN</th>
                <th>FULL NAME</th>
                <th>ROLE</th>
                <th>DATE OF BIRTH</th>
                <th>PHONE</th>
                <th>EMAIL</th>
                <th>QUALIFICATION</th>
                <th>FORM FOUR INDEX#</th>
                <th>JOINED IN</th>
                <th>STREET ADDRESS</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers as $teacher)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-uppercase" style="text-transform: uppercase">{{ $teacher->member_id }}</td>
                    <td class="text-capitalize" style="text-transform: uppercase">{{ $teacher->gender[0] }}</td>
                    <td>{{$teacher->nida ?? 'N/A'}}</td>
                    <td class="text-capitalize" style="text-transform: capitalize">{{ ucwords(strtolower($teacher->first_name. ' '. $teacher->last_name)) }}</td>
                    <td class="text-capitalize" style="text-transform: capitalize">{{ $teacher->role_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($teacher->dob)->format('d/M/Y') }}</td>
                    <td>{{ $teacher->phone }}</td>
                    <td>{{ $teacher->email }}</td>
                    <td class="text-capitalize" style="text-transform: capitalize">
                        @if ($teacher->qualification == 1)
                            {{ __('Masters Degree') }}
                        @elseif ($teacher->qualification == 2)
                            {{ __('Bachelor Degree') }}
                        @elseif ($teacher->qualification == 3)
                            {{ __('Diploma') }}
                        @else
                            {{ __('Certificate') }}
                        @endif
                    </td>
                    @php
                        $indexNo = $teacher->form_four_index_number.'-'.$teacher->form_four_completion_year
                    @endphp
                    <td>{{strtoupper($indexNo ?? 'N/A')}}</td>
                    <td>{{ $teacher->joined }}</td>
                    <td class="text-capitalize" style="text-transform: capitalize">{{ $teacher->address }}</td>
                    <td>{{ $teacher->status == 1 ? 'Active' : 'Inactive' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
