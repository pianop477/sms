@forelse($deductions as $index => $deduction)
<tr>
    <td class="text-center">{{ $index + 1 }}</td>
    <td><strong>{{ strtoupper($deduction['staff_id']) }}</strong></td>
    <td>{{ ucwords(strtolower($deduction['employee_name'])) }}</td>
    <td>{{ $deduction['staff_type'] }}</td>
    <td>
        <span class="deduction-type type-{{ $deduction['deduction_type'] }}">
            {{ ucfirst($deduction['deduction_type']) }}
        </span>
    </td>
    <td>{{ $deduction['description'] }}</td>
    <td class="text-end">{{ number_format($deduction['amount'], 0) }}</td>
    <td>{{ \Carbon\Carbon::parse($deduction['deducted_at'])->format('d/m/Y') }}</td>
    <td>{{ $deduction['payroll_month'] }}</td>
</tr>
@empty
<tr>
    <td colspan="9" class="text-center text-muted py-4">
        <i class="fas fa-history me-1"></i> No deduction history for this year
    </td>
</tr>
@endforelse
