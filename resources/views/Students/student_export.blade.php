<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students Report - {{ Auth::user()->school->school_name }}</title>
    <style>
        body, html {
            margin: 0;
            padding: 30px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            background: #fff;
            color: #2c3e50;
        }

        .header-container {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 25px;
            position: relative;
        }

        .school-logo {
            position: absolute;
            left: 0;
            height: 70px;
            width: auto;
            max-width: 80px;
        }

        .school-info {
            width: 100%;
            text-align: center;
        }

        .school-info h4 {
            font-size: 18px;
            margin: 5px 0;
            text-transform: uppercase;
            color: #1a5276;
        }

        .school-info h5 {
            font-size: 13px;
            margin: 3px 0;
        }

        .table-container {
            margin-top: 15px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }

        .table th {
            background-color: #293d4a;
            color: #fff;
            padding: 8px 5px;
            border: 1px solid #dcdcdc;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
        }

        .table td {
            padding: 6px 5px;
            border: 1px solid #eee;
            vertical-align: middle;
        }

        .table tr:nth-child(even) {
            background-color: #f5f8fa;
        }

        .table tr:hover {
            background-color: #f0f0f0;
        }

       footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 25px;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding: 4px 20px;
            text-align: center;
            background-color: white; /* Hakikisha footer ina background */
            z-index: 1000; /* Hakikisha footer iko juu ya content */
        }
        footer .page-number:after {
        content: "Page " counter(page);
        }
        footer .copyright {
        float: left;
        margin-left: 10px;
        }
        footer .printed {
        float: right;
        margin-right: 10px;
        }
        /* Clear floats */
        footer:after {
        content: "";
        display: table;
        clear: both;
        }
        .no-records {
            text-align: center;
            color: #999;
            font-style: italic;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="header-container">
        @if(Auth::user()->school->logo)
            <img class="school-logo" src="{{ public_path('assets/img/logo/' . Auth::user()->school->logo) }}" alt="School Logo">
        @endif
        <div class="school-info">
            <h4>{{ Auth::user()->school->school_name }}</h4>
            <h5>{{ strtoupper(Auth::user()->school->postal_address) }} - {{ strtoupper(Auth::user()->school->postal_name) }}</h5>
            <h5>{{ ucwords(strtolower($students->first()->class_name)) ?? 'CLASS' }} Students List</h5>
        </div>
    </div>

    @if ($students->isEmpty())
        <p class="no-records">No student records found</p>
    @else
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Adm No</th>
                        <th>Gender</th>
                        <th>Stream</th>
                        <th>Student Name</th>
                        <th>DOB</th>
                        <th>Address</th>
                        <th>Parent Phone</th>
                        <th>School Bus</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td style="text-align: center;">{{ strtoupper($student->admission_number) }}</td>
                        <td style="text-align: center;">{{ strtoupper($student->gender[0]) }}</td>
                        <td style="text-align: center;">{{ strtoupper($student->group) }}</td>
                        <td>{{ ucwords(strtolower($student->first_name.' '.$student->middle_name.' '.$student->last_name)) }}</td>
                        <td style="text-align: center;">{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '' }}</td>
                        <td>{{ ucwords(strtolower($student->address)) }}</td>
                        <td>{{ $student->phone }}</td>
                        <td style="text-align: center;">
                            @if($student->transport_id == null)
                                <span style="color: red; font-weight: bold;">No</span>
                            @else
                                <span style="color: green; font-weight: bold;">Yes</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <footer>
        <span class="copyright">
        &copy; {{ ucwords(strtolower(Auth::user()->school->school_name)) }} – {{ date('Y') }}
        </span>
        <span class="page-number"></span>
        <span class="printed">
        Printed at: {{ now()->format('d-M-Y H:i') }}
        </span>
    </footer>

    <script>
        // Optional dynamic pagination script placeholder
        document.addEventListener('DOMContentLoaded', function() {
            const pages = document.querySelectorAll('.page-number');
            pages.forEach((page, index) => {
                page.textContent = index + 1;
            });
        });
    </script>
</body>
</html>
