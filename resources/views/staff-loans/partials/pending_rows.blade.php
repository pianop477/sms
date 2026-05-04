@forelse($deductions as $index => $deduction)
<tr>
    <td class="text-center">{{ $index + 1 }}</td>
    <td><strong>{{ strtoupper($deduction['staff_id']) }}</strong></td>
    <td>{{ ucwords(strtolower($deduction['employee_name'])) }}</td>
    <td>{{ $deduction['staff_type'] }}</td>
    <td>
        <span class="deduction-type type-{{ $deduction['deduction_type'] }}">
            <i class="fas
                @if($deduction['deduction_type'] == 'loan') fa-hand-holding-usd
                @elseif($deduction['deduction_type'] == 'advance') fa-money-bill-wave
                @elseif($deduction['deduction_type'] == 'penalty') fa-gavel
                @elseif($deduction['deduction_type'] == 'fine') fa-exclamation-triangle
                @else fa-tag @endif me-1"></i>
            {{ ucfirst($deduction['deduction_type']) }}
        </span>
    </td>
    <td>{{ $deduction['description'] }}</td>
    <td class="text-end fw-bold">{{ number_format($deduction['amount'], 0) }}</td>
    <td class="text-center">
        @if($deduction['is_recurring'])
            <span class="badge bg-info">
                <i class="fas fa-sync-alt me-1"></i>
                {{ $deduction['remaining_months'] }}/{{ $deduction['recurring_months'] }} months
            </span>
        @else
            <span class="badge bg-secondary">One-time</span>
        @endif
    </td>
    <td>{{ \Carbon\Carbon::parse($deduction['created_at'])->format('d/m/Y') }}</td>
    <td>
        <div class="action-buttons">
            <button class="btn btn-xs btn-warning" style="border-radius: 12px"
                onclick="editDeduction({{ $deduction['id'] }})">
                <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn btn-xs btn-danger" style="border-radius: 12px"
                onclick="cancelDeduction({{ $deduction['id'] }})">
                <i class="fas fa-trash"></i> Cancel
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="11" class="text-center text-muted py-4">
        <i class="fas fa-check-circle me-1"></i> No pending deductions for this year
    </td>
</tr>
@endforelse
