<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students Report - {{ Auth::user()->school->school_name }}</title>
    <style>
        /* PDF specific resets */
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px; /* Smaller for high-column density */
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Professional Header Layout (Old School Tables for domPDF) */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1a5276;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .school-logo {
            max-height: 80px;
            width: auto;
        }

        .school-info {
            text-align: right;
        }

        .school-info h1 {
            font-size: 20px;
            margin: 0;
            color: #1a5276;
            text-transform: uppercase;
        }

        .school-info p {
            margin: 2px 0;
            color: #555;
            font-size: 11px;
        }

        .report-title {
            background-color: #f8f9fa;
            padding: 8px;
            text-align: center;
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
        }

        .report-title h2 {
            margin: 0;
            font-size: 14px;
            color: #2c3e50;
            letter-spacing: 1px;
        }

        /* Professional Table Styling */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Critical for many columns */
        }

        .main-table th {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 8px 4px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            border: 1px solid #2c3e50;
        }

        .main-table td {
            padding: 6px 4px;
            border-bottom: 1px solid #e9ecef;
            border-right: 1px solid #f1f1f1;
            word-wrap: break-word; /* Prevents text overflow */
            font-size: 9px;
        }

        .main-table tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* Specific Column Widths (Adjust as needed) */
        .col-id { width: 25px; text-align: center; }
        .col-adm { width: 50px; }
        .col-sex { width: 30px; text-align: center; }
        .col-name { width: 70px; }
        .col-phone { width: 75px; }
        .col-bus { width: 40px; text-align: center; }

        /* Status Pills */
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8px;
        }
        .badge-yes { background-color: #d4edda; color: #155724; }
        .badge-no { background-color: #f8d7da; color: #721c24; }

        /* Footer */
        footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 30px;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        .pagenum:before {
            content: counter(page);
        }

        .no-records {
            text-align: center;
            padding: 50px;
            color: #999;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="15%">
                @if(Auth::user()->school->logo)
                    <img class="school-logo" src="{{ public_path('storage/logo/' . Auth::user()->school->logo) }}" alt="Logo">
                @endif
            </td>
            <td class="school-info">
                <h1>{{ Auth::user()->school->school_name }}</h1>
                <p>{{ strtoupper(Auth::user()->school->postal_address) }} - {{ strtoupper(Auth::user()->school->postal_name) }}</p>
                <p>Email: {{ Auth::user()->school->school_email ?? "N/A" }} | Tel: {{ Auth::user()->school->school_phone ?? "N/A" }}</p>
            </td>
        </tr>
    </table>

    <div class="report-title">
        <h2>STUDENT ENROLLMENT LIST: {{ strtoupper($students->first()->class_name ?? 'N/A') }}</h2>
    </div>

    @if ($students->isEmpty())
        <div class="no-records">No student records found for this criteria.</div>
    @else
        <table class="main-table">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th class="col-adm">Adm. No</th>
                    <th class="col-sex">Gen</th>
                    <th class="col-name">First Name</th>
                    <th class="col-name">Middle</th>
                    <th class="col-name">Last Name</th>
                    <th style="width: 55px;">DOB</th>
                    <th class="col-name">Parent First</th>
                    <th class="col-name">Parent Last</th>
                    <th class="col-phone">Phone</th>
                    <th style="width: 100px;">Email</th>
                    <th style="width: 80px;">Address</th>
                    <th class="col-bus">Bus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td class="col-id">{{ $loop->iteration }}</td>
                    <td style="font-weight: bold;">{{ strtoupper($student->admission_number) }}</td>
                    <td class="col-sex">{{ strtoupper($student->gender[0]) }}</td>
                    <td>{{ ucwords(strtolower($student->first_name)) }}</td>
                    <td>{{ ucwords(strtolower($student->middle_name)) }}</td>
                    <td>{{ ucwords(strtolower($student->last_name)) }}</td>
                    <td>{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '-' }}</td>
                    <td>{{ ucwords(strtolower($student->parent_first_name)) }}</td>
                    <td>{{ ucwords(strtolower($student->parent_last_name)) }}</td>
                    <td>{{ $student->phone }}</td>
                    <td style="font-size: 8px;">{{ strtolower($student->parent_email) }}</td>
                    <td>{{ Str::limit($student->address, 20) }}</td>
                    <td class="col-bus">
                        @if($student->transport_id == null)
                            <span class="badge badge-no">NO</span>
                        @else
                            <span class="badge badge-yes">YES</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <footer>
        <table width="100%">
            <tr>
                <td align="left">&copy; {{ date('Y') }} {{ Auth::user()->school->school_name }}</td>
                <td align="center">Page <span class="pagenum"></span></td>
                <td align="right">Printed: {{ date('d M Y, H:i') }}</td>
            </tr>
        </table>
    </footer>

</body>
</html>
