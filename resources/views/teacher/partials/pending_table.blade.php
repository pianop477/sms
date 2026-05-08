<div class="table-responsive">
    <table class="table permit-table">
        <thead>
            <tr>
                <th width="12%">Permit #</th>
                <th width="18%">Student</th>
                <th width="10%">Class</th>
                <th width="18%">Guardian</th>
                <th width="10%">Requested At</th>
                <th width="15%">Status</th>
                <th width="17%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingPermits as $permit)
            <tr>
                <td>
                    <strong>{{ strtoupper($permit->permit_number) }}</strong><br>
                    <small class="text-muted">{{ $permit->created_at->format('d/m/Y H:i') }}</small>
                </td>
                <td>
                    {{ ucwords(strtolower($permit->student->first_name)) }} {{ ucwords(strtolower($permit->student->last_name)) }}<br>
                    <small class="text-muted">{{ strtoupper($permit->student->admission_number) }}</small>
                </td>
                <td>
                    {{ strtoupper($permit->student->class->class_code ?? 'N/A') }}<br>
                    @if($permit->student->group)
                        <small class="text-muted">{{ strtoupper($permit->student->group) }}</small>
                    @endif
                </td>
                <td>
                    {{ ucwords(strtolower($permit->guardian_name)) }}<br>
                    <small>{{ $permit->guardian_phone }}</small>
                </td>
                <td>
                    {{ $permit->departure_date->format('d/m/Y') }}<br>
                    <small>{{ $permit->departure_time->format('H:i') }}</small>
                </td>
                <td>
                    @php
                        $statusClass = match($permit->status) {
                            'pending_class_teacher' => 'pending-class-teacher',
                            'pending_duty_teacher' => 'pending-duty-teacher',
                            'pending_academic' => 'pending-academic',
                            'pending_head' => 'pending-head',
                            default => 'pending',
                        };
                        $statusText = match($permit->status) {
                            'pending_class_teacher' => 'Mwalimu wa Darasa',
                            'pending_duty_teacher' => 'Mwalimu wa Zamu',
                            'pending_academic' => 'Mwalimu wa Taaluma',
                            'pending_head' => 'Mwalimu Mkuu',
                            default => ucfirst(str_replace('_', ' ', $permit->status)),
                        };
                    @endphp
                    <span class="status-badge status-{{ $statusClass }}">
                        <i class="fas fa-clock me-1"></i> {{ $statusText }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1 flex-wrap">
                        <a href="{{ route('teacher.e-permit.show', ['id' => Hashids::encode($permit->id)]) }}"
                           class="btn-action btn-view" title="View Details">
                            <i class="fas fa-eye"></i> <span class="d-none d-md-inline">View</span>
                        </a>

                        @if(($permit->status === 'pending_class_teacher' && $teacher->role_id == 4) ||
                            ($teacher->role_id == 3 && in_array($permit->status, ['pending_duty_teacher', 'pending_academic'])) ||
                            ($permit->status === 'pending_head' && $teacher->role_id == 2))
                            <button onclick="quickApprove({{ $permit->id }})" class="btn-action btn-approve" title="Approve">
                                <i class="fas fa-check"></i> <span class="d-none d-md-inline">Approve</span>
                            </button>
                            <button onclick="quickReject({{ $permit->id }})" class="btn-action btn-reject" title="Reject">
                                <i class="fas fa-times"></i> <span class="d-none d-md-inline">Reject</span>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                    Hakuna ombi linalosubiri kuthibitishwa
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($pendingPermits->hasPages())
    <div class="mt-3 d-flex justify-content-end">
        {{ $pendingPermits->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>

<script>
    // Re-attach pagination event handlers after table refresh
    document.querySelectorAll('#pendingTableContainer .pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            if (url) {
                loadPendingTableWithUrl(url);
            }
        });
    });
</script>
