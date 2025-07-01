<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teachers Report - {{ Auth::user()->school->school_name }}</title>
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
            background-color: #1a5276;
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

        body, html {
        margin: 0;
        padding: 30px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 11px; /* slightly reduced */
        background: #fff;
        color: #2c3e50;
    }

    .table-container {
        margin-top: 15px;
        overflow: hidden;
    }

    .table {
        width: 100%;
        table-layout: fixed; /* Force fixed layout to prevent overflow */
        border-collapse: collapse;
        font-size: 10.5px;
        word-wrap: break-word;
    }

    .table th, .table td {
        border: 1px solid #ddd;
        padding: 4px 4px;
        vertical-align: top;
        text-align: left;
        word-break: break-word;
    }

    .table th {
        background-color: #293b47;
        color: white;
        text-transform: uppercase;
        font-size: 10px;
        text-align: center;
    }

    .table tr:nth-child(even) {
        background-color: #f5f8fa;
    }

    .table tr:hover {
        background-color: #f0f0f0;
    }

    /* Responsive column widths */
    .table th:nth-child(1),
    .table td:nth-child(1) { width: 8%; text-align: center; } /* Member ID */
    .table th:nth-child(2),
    .table td:nth-child(2) { width: 5%; text-align: center; } /* Gender */
    .table th:nth-child(3),
    .table td:nth-child(3) { width: 20%; } /* Full Name */
    .table th:nth-child(4),
    .table td:nth-child(4) { width: 10%; } /* Role */
    .table th:nth-child(5),
    .table td:nth-child(5) { width: 12%; text-align: center; } /* DOB */
    .table th:nth-child(6),
    .table td:nth-child(6) { width: 10%; } /* Phone */
    .table th:nth-child(7),
    .table td:nth-child(7) { width: 15%; } /* Email */
    .table th:nth-child(8),
    .table td:nth-child(8) { width: 10%; } /* Qualification */
    .table th:nth-child(9),
    .table td:nth-child(9) { width: 7%; text-align: center; } /* Joined */
    .table th:nth-child(10),
    .table td:nth-child(10) { width: 8%; } /* Street */
    .table th:nth-child(11),
    .table td:nth-child(11) { width: 7%; } /* Status */

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
            background-color: white;
            z-index: 1000;
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
                        <th>sex</th>
                        <th>Full Name</th>
                        <th>DOB</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Qualification</th>
                        <th>Joined</th>
                        <th>Street</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                    <tr>
                        <td style="text-align:center">{{ strtoupper($teacher->member_id) }}</td>
                        <td style="text-align:center">{{ strtoupper($teacher->gender[0]) }}</td>
                        <td>{{ ucwords(strtolower($teacher->first_name . ' ' . $teacher->last_name)) }}</td>
                        <td>{{ \Carbon\Carbon::parse($teacher->dob)->format('d/m/Y') }}</td>
                        <td>{{ $teacher->phone }}</td>
                        <td>{{ $teacher->email }}</td>
                        <td>
                            @switch($teacher->qualification)
                                @case(1) Masters Degree @break
                                @case(2) Bachelor Degree @break
                                @case(3) Diploma @break
                                @default Certificate
                            @endswitch
                        </td>
                        <td style="text-align:center">{{ $teacher->joined }}</td>
                        <td>{{ $teacher->address }}</td>
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
            Printed at: {{ now()->format('d-M-Y H:i') }}
        </span>
    </footer>

    <script>
        // Optional dynamic page number (may not work in static PDF)
        document.addEventListener('DOMContentLoaded', function() {
            const pages = document.querySelectorAll('.page-number');
            pages.forEach((page, index) => {
                page.textContent = index + 1;
            });
        });
    </script>

</body>
</html>
