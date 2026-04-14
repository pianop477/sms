<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Report - {{ $school->school_name }}</title>
    <style>
        /* Landscape is much safer for 11 columns */
        @page {
            size: A4 landscape;
            margin: 0.8cm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #334155;
            margin: 0;
            padding: 0;
        }

        /* Modern Professional Header */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #334155;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .logo {
            max-width: 80px;
            max-height: 70px;
        }

        .school-info {
            text-align: right;
        }

        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
            margin: 0;
        }

        .school-address {
            font-size: 10px;
            color: #64748b;
            margin: 2px 0;
        }

        /* Banner Style Title */
        .report-banner {
            background-color: #f1f5f9;
            padding: 10px;
            text-align: center;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .report-banner h1 {
            font-size: 14px;
            margin: 0;
            color: #0f172a;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Data Table Logic */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Forces fixed widths to prevent bleeding */
        }

        th {
            background-color: #1e293b;
            color: #ffffff;
            padding: 8px 4px;
            font-size: 8px;
            text-transform: uppercase;
            text-align: left;
            border: 1px solid #1e293b;
        }

        td {
            padding: 6px 4px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
            word-wrap: break-word; /* Crucial for long emails */
            font-size: 9px;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Column Width Definitions (Total 100%) */
        .w-idx   { width: 3%; text-align: center; }
        .w-id    { width: 9%; }
        .w-nin   { width: 10%; }
        .w-name  { width: 14%; }
        .w-sex   { width: 4%; text-align: center; }
        .w-tel   { width: 9%; }
        .w-mail  { width: 14%; font-size: 8px; }
        .w-job   { width: 10%; }
        .w-dob   { width: 8%; text-align: center; }
        .w-addr  { width: 10%; }
        .w-stat  { width: 9%; text-align: center; }

        /* Status Badges */
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
        }
        .active { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .inactive { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Footer */
        footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 8px;
            color: #94a3b8;
        }

        .pagenum:before { content: counter(page); }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="border:none; padding:0;">
                @php
                    $logoBase64 = null;
                    if (!empty($school->logo)) {
                        $logoFile = storage_path('app/public/logo/' . $school->logo);
                        if (file_exists($logoFile)) {
                            $type = pathinfo($logoFile, PATHINFO_EXTENSION);
                            $data = file_get_contents($logoFile);
                            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        }
                    }
                @endphp

                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
                @else
                    <div style="background:#f1f5f9; width:60px; height:60px; line-height:30px; text-align:center; font-size:9px; border:1px solid #cbd5e1;">LOGO</div>
                @endif
            </td>
            <td class="school-info" style="border:none; padding:0;">
                <div class="school-name">{{ strtoupper($school->school_name) }}</div>
                <div class="school-address">{{ ucwords(strtolower($school->postal_address)) }}, {{ ucwords(strtolower($school->postal_name)) }}</div>
                <div class="school-address">Printed: {{ date('d M, Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="report-banner">
        <h1>Non-Teaching Staff Registry</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th class="w-idx">#</th>
                <th class="w-id">Staff ID</th>
                <th class="w-nin">NIN</th>
                <th class="w-name">Full Name</th>
                <th class="w-sex">G</th>
                <th class="w-tel">Phone</th>
                <th class="w-mail">Email Address</th>
                <th class="w-job">Job Title</th>
                <th class="w-dob">DOB</th>
                <th class="w-addr">Address</th>
                <th class="w-stat">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($combinedStaffs as $t)
                <tr>
                    <td class="w-idx">{{ $loop->iteration }}</td>
                    <td class="w-id" style="font-weight:bold;">{{strtoupper($t->staff_id ?? 'N/A')}}</td>
                    <td class="w-nin">{{$t->nida ?? 'N/A'}}</td>
                    <td class="w-name">{{$t->job_title == 'driver' ? ucwords(strtolower($t->driver_name)) : ucwords(strtolower($t->first_name. ' '. $t->last_name)) }}</td>
                    <td class="w-sex">{{ strtoupper(substr($t->gender, 0, 1)) }}</td>
                    <td class="w-tel">{{ $t->phone ?? "N/A" }}</td>
                    <td class="w-mail">{{ strtolower($t->email) ?? 'N/A' }}</td>
                    <td class="w-job">{{ ucwords(strtolower($t->job_title)) ?? "N/A"}}</td>
                    <td class="w-dob">{{ \Carbon\Carbon::parse($t->date_of_birth)->format('d/m/Y')}}</td>
                    <td class="w-addr">{{ ucwords(strtolower($t->street_address)) ?? "N/A"}}</td>
                    <td class="w-stat">
                        @if($t->status == 1)
                            <span class="badge active">Active</span>
                        @else
                            <span class="badge inactive">Inactive</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        <table width="100%" style="border:none;">
            <tr>
                <td style="border:none; padding:0;">&copy; {{ $school->school_name }}</td>
                <td style="border:none; padding:0; text-align:center;">Staff Record - Confidential</td>
                <td style="border:none; padding:0; text-align:right;">Page <span class="pagenum"></span></td>
            </tr>
        </table>
    </footer>
</body>
</html>
