<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teachers Report - {{ Auth::user()->school->school_name }}</title>
    <style>
        body, html {
            margin: 0;
            padding: 15px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px; /* Reduced for more space */
            background: #fff;
            color: #2c3e50;
        }

        .header-container {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 15px;
            position: relative;
        }

        .school-logo {
            position: absolute;
            left: 0;
            height: 60px;
            width: auto;
            max-width: 70px;
        }

        .school-info {
            width: 100%;
            text-align: center;
        }

        .school-info h4 {
            font-size: 16px;
            margin: 4px 0;
            text-transform: uppercase;
            color: #1a5276;
        }

        .school-info h5 {
            font-size: 12px;
            margin: 2px 0;
        }

        .table-container {
            margin-top: 10px;
            overflow: visible;
        }

        .table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 9.5px;
            word-wrap: break-word;
        }

        .table th {
            background-color: #1a5276;
            color: #fff;
            padding: 6px 3px;
            border: 1px solid #dcdcdc;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }

        .table td {
            padding: 5px 3px;
            border: 1px solid #eee;
            vertical-align: middle;
            line-height: 1.2;
            word-break: break-word;
        }

        .table tr:nth-child(even) {
            background-color: #f5f8fa;
        }

        /* Optimized column widths for 12 columns */
        .table th:nth-child(1),
        .table td:nth-child(1) { width: 5%; } /* Member ID */
        .table th:nth-child(2),
        .table td:nth-child(2) { width: 4%; } /* Gender */
        .table th:nth-child(3),
        .table td:nth-child(3) { width: 9%; } /* NIN */
        .table th:nth-child(4),
        .table td:nth-child(4) { width: 14%; } /* Full Name */
        .table th:nth-child(5),
        .table td:nth-child(5) { width: 7%; } /* DOB */
        .table th:nth-child(6),
        .table td:nth-child(6) { width: 8%; } /* Phone */
        .table th:nth-child(7),
        .table td:nth-child(7) { width: 14%; } /* Email */
        .table th:nth-child(8),
        .table td:nth-child(8) { width: 9%; } /* Qualification */
        .table th:nth-child(9),
        .table td:nth-child(9) { width: 8%; } /* Form Four Index# */
        .table th:nth-child(10),
        .table td:nth-child(10) { width: 5%; } /* Joined */
        .table th:nth-child(11),
        .table td:nth-child(11) { width: 9%; } /* Street */
        .table th:nth-child(12),
        .table td:nth-child(12) { width: 8%; } /* Status */

        /* Text alignment */
        .table td:nth-child(1),
        .table td:nth-child(2),
        .table td:nth-child(5),
        .table td:nth-child(10) {
            text-align: center;
        }

        /* Compact text for small cells */
        .compact-text {
            font-size: 8.5px;
            padding: 2px 1px;
        }

        @page {
            size: A4 landscape;
            margin-top: 5mm;
            margin-bottom: 8mm;
            margin-left: 5mm;
            margin-right: 5mm;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4mm;
            font-size: 8px;
            padding-top: 6px;
            border-top: 1px solid #ddd;
            text-align: center;
            background-color: white;
            z-index: 1000;
        }

        footer .page-number:after {
            content: "Page " counter(page);
        }

        footer .copyright {
            float: left;
            margin-left: 5px;
        }

        footer .printed {
            float: right;
            margin-right: 5px;
        }

        footer:after {
            content: "";
            display: table;
            clear: both;
        }

        .no-records {
            text-align: center;
            color: #999;
            font-style: italic;
            margin-top: 30px;
            font-size: 12px;
        }

        /* Force table to stay on one page if possible */
        .table {
            page-break-inside: auto;
        }

        .table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        /* Optional: Add ellipsis for very long content */
        .ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
    </style>
</head>
<body>

    <div class="header-container">
        @if(Auth::user()->school->logo)
            <img class="school-logo" src="{{ storage_path('app/public/logo/' . Auth::user()->school->logo) }}" alt="School Logo">
        @endif
        <div class="school-info">
            <h4>{{ Auth::user()->school->school_name }}</h4>
            <h5>{{ strtoupper(Auth::user()->school->postal_address) }} - {{ strtoupper(Auth::user()->school->postal_name) }}</h5>
            <h5>Teachers List</h5>
        </div>
    </div>

    @if ($teachers->isEmpty())
        <p class="no-records">No teacher records found</p>
    @else
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>Gender</th>
                        <th>NIN</th>
                        <th>Full Name</th>
                        <th>DOB</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Qualification</th>
                        <th>Form Four Index#</th>
                        <th>Joined</th>
                        <th>Street</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                    <tr>
                        <td>{{ strtoupper($teacher->member_id) }}</td>
                        <td>{{ strtoupper($teacher->gender[0]) }}</td>
                        <td>{{ $teacher->nida ?? 'N/A' }}</td>
                        <td>{{ ucwords(strtolower($teacher->first_name . ' ' . $teacher->last_name)) }}</td>
                        <td>{{ \Carbon\Carbon::parse($teacher->dob)->format('d/m/Y') }}</td>
                        <td>{{ $teacher->phone }}</td>
                        <td>{{ $teacher->email }}</td>
                        <td>
                            @switch($teacher->qualification)
                                @case(1) Masters @break
                                @case(2) Bachelor @break
                                @case(3) Diploma @break
                                @default Certificate
                            @endswitch
                        </td>
                        @php
                            $indexNo = $teacher->form_four_index_number . '-' . $teacher->form_four_completion_year
                        @endphp
                        <td>{{ strtoupper($indexNo ?? 'N/A') }}</td>
                        <td>{{ $teacher->joined }}</td>
                        <td class="ellipsis" title="{{ $teacher->address }}">{{ $teacher->address }}</td>
                        <td>{{ $teacher->status == 1 ? 'Active' : 'Inactive' }}</td>
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
            Printed: {{ now()->format('d-M-Y H:i') }}
        </span>
    </footer>

</body>
</html>
