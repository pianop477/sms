@if (isset($datas) && $datas->isNotEmpty())
    @php
        // Group by month
        $monthlyData = [];
        foreach ($datas as $date => $attendances) {
            $monthYear = \Carbon\Carbon::parse($date)->format('Y-m');
            if (!isset($monthlyData[$monthYear])) {
                $monthlyData[$monthYear] = [];
            }
            $monthlyData[$monthYear][$date] = $attendances;
        }
        ksort($monthlyData);
    @endphp

    @foreach ($monthlyData as $monthYear => $monthAttendances)
        @php
            $monthName = \Carbon\Carbon::parse($monthYear . '-01')->format('F Y');
            $datesInMonth = array_keys($monthAttendances);
            sort($datesInMonth);

            // Get all students for this month
            $students = [];
            $totalRecords = 0;
            $presentRecords = 0;

            foreach ($monthAttendances as $dateAttendances) {
                $totalRecords += $dateAttendances->count();
                $presentRecords += $dateAttendances->where('attendance_status', 'present')->count();

                foreach ($dateAttendances as $attendance) {
                    $studentId = $attendance->studentId;
                    if (!isset($students[$studentId])) {
                        $students[$studentId] = [
                            'id' => $studentId,
                            'admission_number' => $attendance->admission_number,
                            'name' => ucwords(strtolower($attendance->first_name . ' ' .
                                        ($attendance->middle_name ? $attendance->middle_name . ' ' : '') .
                                        $attendance->last_name)),
                            'gender' => $attendance->gender[0] ?? 'U',
                            'group' => $attendance->group ?? 'N/A',
                            'attendances' => []
                        ];
                    }
                }
            }

            // Populate attendance data
            foreach ($monthAttendances as $date => $dateAttendances) {
                foreach ($dateAttendances as $attendance) {
                    $studentId = $attendance->studentId;
                    if (isset($students[$studentId])) {
                        $students[$studentId]['attendances'][$date] = $attendance->attendance_status;
                    }
                }
            }

            $attendanceRate = $totalRecords > 0 ? round(($presentRecords / $totalRecords) * 100) : 0;
            $firstRecord = reset($monthAttendances)[0];
        @endphp

        <div class="month-section">
            <!-- Header with Month and Stats -->
            <div class="time-duration-header">
                <div class="header-left">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ $monthName }}</span>
                </div>
                <div class="header-right">
                    <span class="attendance-badge">
                        <i class="fas fa-chart-line"></i>
                        {{ $attendanceRate }}% Attendance
                    </span>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="card-icon">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <div class="card-content">
                        <span class="card-label">Class</span>
                        <span class="card-value">{{ $firstRecord->class_name ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="card-icon">
                        <i class="fas fa-calendar-range"></i>
                    </div>
                    <div class="card-content">
                        <span class="card-label">Period</span>
                        <span class="card-value">
                            {{ \Carbon\Carbon::parse($datesInMonth[0])->format('d M') }} -
                            {{ \Carbon\Carbon::parse($datesInMonth[count($datesInMonth)-1])->format('d M Y') }}
                        </span>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <span class="card-label">Students</span>
                        <span class="card-value">{{ count($students) }}</span>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="card-icon">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div class="card-content">
                        <span class="card-label">Attendance</span>
                        <span class="card-value text-primary">{{ $attendanceRate }}%</span>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="table-wrapper">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th class="col-number">#</th>
                            <th class="col-name">Student Name</th>
                            <th class="col-gender">G</th>
                            <th class="col-stream">S</th>
                            @foreach ($datesInMonth as $date)
                                <th class="col-date" title="{{ \Carbon\Carbon::parse($date)->format('D, d M') }}">
                                    {{ \Carbon\Carbon::parse($date)->format('d') }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $index => $student)
                            <tr>
                                <td class="col-number">{{ $index + 1 }}</td>
                                <td class="col-name">{{ $student['name'] }}</td>
                                <td class="col-gender">{{ $student['gender'] }}</td>
                                <td class="col-stream">{{ $student['group'] }}</td>

                                @foreach ($datesInMonth as $date)
                                    @php
                                        $status = $student['attendances'][$date] ?? 'A';
                                        $statusClass = match($status) {
                                            'present' => 'attendance-present',
                                            'absent' => 'attendance-absent',
                                            'permission' => 'attendance-permission',
                                            default => 'attendance-absent'
                                        };
                                        $statusSymbol = match($status) {
                                            'present' => 'P',
                                            'absent' => 'A',
                                            'permission' => '✱',
                                            default => '?'
                                        };
                                    @endphp
                                    <td class="col-date {{ $statusClass }}"
                                        title="{{ ucfirst($status) }} on {{ \Carbon\Carbon::parse($date)->format('d M') }}">
                                        {{ $statusSymbol }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Legend -->
            <div class="legend-section">
                <div class="legend-item">
                    <span class="legend-dot dot-present"></span>
                    <span>Present (P)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot dot-absent"></span>
                    <span>Absent (A)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot dot-permission"></span>
                    <span>Permission (✱)</span>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <h5>No Attendance Records Found</h5>
        <p>No data available for the selected period</p>
    </div>
@endif
