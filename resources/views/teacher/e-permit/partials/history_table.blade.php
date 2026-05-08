<div class="table-responsive">
    <table class="table permit-table">
        <thead>
            <tr>
                <th width="12%">Permit #</th>
                <th width="18%">Student</th>
                <th width="15%">Guardian</th>
                <th width="10%">Departure</th>
                <th width="10%">Expected Return</th>
                <th width="10%">Actual Return</th>
                <th width="10%">Status</th>
                <th width="15%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historyPermits as $permit)
            <tr>
                <td>
                    <strong>{{ strtoupper($permit->permit_number) }}</strong><br>
                    <small>{{ $permit->created_at->format('d/m/Y') }}</small>
                </td>
                <td>
                    {{ ucwords(strtolower($permit->student->first_name)) }} {{ ucwords(strtolower($permit->student->last_name)) }}<br>
                    <small class="text-muted">{{ strtoupper($permit->student->admission_number) }}</small>
                </td>
                <td>
                    {{ ucwords(strtolower($permit->guardian_name)) }}<br>
                    <small>{{ $permit->guardian_phone }}</small>
                </td>
                <td>{{ $permit->departure_date->format('d/m/Y') }}</td>
                <td>{{ $permit->expected_return_date->format('d/m/Y') }}</td>
                <td>
                    @if($permit->actual_return_date)
                        {{ \Carbon\Carbon::parse($permit->actual_return_date)->format('d/m/Y') }}
                        @if($permit->is_late_return)
                            <span class="badge bg-warning text-dark ms-1">Late</span>
                        @endif
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @php
                        $statusClass = match($permit->status) {
                            'approved' => 'approved',
                            'rejected' => 'rejected',
                            'completed' => 'completed',
                            default => 'pending',
                        };
                        $statusText = match($permit->status) {
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            'completed' => 'Completed',
                            default => ucfirst($permit->status),
                        };
                    @endphp
                    <span class="status-badge status-{{ $statusClass }}">{{ $statusText }}</span>
                </td>
                <td>
                    <a href="{{ route('teacher.e-permit.show', ['id' => Hashids::encode($permit->id)]) }}"
                       class="btn-action btn-view">
                        <i class="fas fa-eye"></i> View
                    </a>
                    @if($permit->status == 'approved' && $permit->pdf_path)
                        <a href="{{ route('teacher.e-permit.print', ['id' => Hashids::encode($permit->id)]) }}"
                           class="btn-action btn-print mt-1">
                            <i class="fas fa-print"></i> Print
                        </a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    <i class="fas fa-history fa-2x mb-2 d-block"></i>
                    Hakuna historia ya maombi
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($historyPermits->hasPages())
    <div class="mt-3 d-flex justify-content-end">
        {{ $historyPermits->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>
