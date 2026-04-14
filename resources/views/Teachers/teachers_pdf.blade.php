<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teachers Report - {{ Auth::user()->school->school_name }}</title>
    <style>
        /* PDF specific resets and landscape optimization */
        @page {
            size: A4 landscape;
            margin: 0.8cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        /* Standardized Header using Tables (domPDF compatible) */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1a5276;
            margin-bottom: 15px;
            padding-bottom: 8px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .school-logo {
            max-height: 65px;
            width: auto;
        }

        .school-info {
            text-align: right;
        }

        .school-info h1 {
            font-size: 18px;
            margin: 0;
            color: #1a5276;
            text-transform: uppercase;
        }

        .school-info p {
            margin: 1px 0;
            color: #666;
            font-size: 10px;
        }

        .report-banner {
            background-color: #f1f4f6;
            padding: 6px;
            text-align: center;
            border: 1px solid #d1d9e0;
            margin-bottom: 12px;
        }

        .report-banner h2 {
            margin: 0;
            font-size: 13px;
            color: #1a5276;
            letter-spacing: 0.5px;
        }

        /* The Main Teacher Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Ensures columns stay within bounds */
        }

        .data-table th {
            background-color: #1a5276;
            color: #ffffff;
            padding: 6px 3px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            border: 1px solid #1a5276;
        }

        .data-table td {
            padding: 5px 3px;
            border: 1px solid #eef1f3;
            word-wrap: break-word; /* Critical for long emails/names */
            font-size: 8.5px;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* Width Logic for 12 Columns (Total 100%) */
        .col-id   { width: 6%; }  /* Member ID */
        .col-sex  { width: 3%; text-align: center; }
        .col-nin  { width: 9%; }
        .col-name { width: 13%; }
        .col-dob  { width: 7%; text-align: center; }
        .col-tel  { width: 8%; }
        .col-mail { width: 14%; font-size: 7.5px; }
        .col-qual { width: 8%; }
        .col-idx  { width: 10%; }
        .col-year { width: 4%; text-align: center; }
        .col-addr { width: 10%; }
        .col-stat { width: 8%; text-align: center; }

        /* Status Styling */
        .badge {
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7.5px;
            display: inline-block;
        }
        .active { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .inactive { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Footer */
        footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 25px;
            font-size: 8px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 4px;
        }

        .pagenum:before {
            content: counter(page);
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #a0aec0;
            font-style: italic;
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
                <p>Email: {{ Auth::user()->school->school_email ?? "N/A" }} | Contact: {{ Auth::user()->school->school_phone ?? "N/A" }}</p>
            </td>
        </tr>
    </table>

    <div class="report-banner">
        <h2>STAFF REGISTRY / TEACHER LISTING - {{ date('F Y') }}</h2>
    </div>

    @if ($teachers->isEmpty())
        <div class="no-data">No active teacher records found in the database.</div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-sex">G</th>
                    <th class="col-nin">NIN/NIDA</th>
                    <th class="col-name">Full Name</th>
                    <th class="col-dob">DOB</th>
                    <th class="col-tel">Phone</th>
                    <th class="col-mail">Email Address</th>
                    <th class="col-qual">Qual.</th>
                    <th class="col-idx">Index No</th>
                    <th class="col-year">In</th>
                    <th class="col-addr">Street</th>
                    <th class="col-stat">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teachers as $teacher)
                <tr>
                    <td class="col-id" style="font-weight: bold;">{{ strtoupper($teacher->member_id) }}</td>
                    <td class="col-sex">{{ strtoupper($teacher->gender[0]) }}</td>
                    <td class="col-nin">{{ $teacher->nida ?? 'N/A' }}</td>
                    <td class="col-name">{{ ucwords(strtolower($teacher->first_name . ' ' . $teacher->last_name)) }}</td>
                    <td class="col-dob">{{ \Carbon\Carbon::parse($teacher->dob)->format('d/m/y') }}</td>
                    <td class="col-tel">{{ $teacher->phone }}</td>
                    <td class="col-mail">{{ strtolower($teacher->email) }}</td>
                    <td class="col-qual">
                        @switch($teacher->qualification)
                            @case(1) Masters @break
                            @case(2) Bachelor @break
                            @case(3) Diploma @break
                            @default Cert.
                        @endswitch
                    </td>
                    <td class="col-idx" style="font-size: 7.5px;">{{ strtoupper($teacher->form_four_index_number) }}-{{ $teacher->form_four_completion_year }}</td>
                    <td class="col-year">{{ $teacher->joined }}</td>
                    <td class="col-addr">{{ Str::limit(ucwords(strtolower($teacher->address)), 15) }}</td>
                    <td class="col-stat">
                        @if($teacher->status == 1)
                            <span class="badge active">ACTIVE</span>
                        @else
                            <span class="badge inactive">LEFT</span>
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
                <td align="center">Staff Records - Confidential</td>
                <td align="right">Page <span class="pagenum"></span> | Printed: {{ date('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </footer>

</body>
</html>
