<table style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
    {{-- SCHOOL HEADER --}}
    <tr>
        <td colspan="9" style="font-weight: bold; font-size: 18px; text-align: center; padding: 10px; color: #2c3e50;">
            {{ strtoupper($school->school_name) }}
        </td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: center; padding: 5px; font-size: 14px; color: #34495e;">
            {{ $school->postal_address }} - {{ strtoupper($school->postal_name) }}
        </td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: center; padding: 5px; font-size: 14px; color: #34495e;">
            {{ strtoupper($school->country) }}
        </td>
    </tr>
    <tr><td colspan="9" style="height: 20px;"></td></tr>

    {{-- BATCH TITLE --}}
    <tr>
        <td colspan="9" style="font-weight: bold; font-size: 16px; text-align: center; padding: 8px; background-color: #3498db; color: white;">
            Bills Batch: {{ strtoupper($batch->batch_name) }}
        </td>
    </tr>
    <tr><td colspan="9" style="height: 15px;"></td></tr>

    {{-- TABLE HEADER --}}
    <tr style="background-color: #2c3e50; color: white; font-weight: bold; font-size: 12px; font-weight:bold">
        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">S/N</td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">CONTROL NUMBER</td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">STUDENT NAME</td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">LEVEL</td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">ACADEMIC YEAR</td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">SERVICE</td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">BILLED AMOUNT</td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">STATUS</td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">DUE DATE</td>
    </tr>

    {{-- TABLE ROWS --}}
    @php $i = 1; @endphp
    @foreach($bills as $bill)
        <tr style="font-size: 11px;">
            <td style="border: 1px solid #ddd; padding: 6px; text-align: center; background-color: {{ $i % 2 == 0 ? '#f8f9fa' : '#ffffff' }};">
                {{ $i++ }}
            </td>
            <td style="border: 1px solid #ddd; padding: 6px; text-align: left; background-color: {{ $i % 2 == 0 ? '#f8f9fa' : '#ffffff' }};">
                {{ strtoupper($bill->control_number) }}
            </td>
            <td style="border: 1px solid #ddd; padding: 6px; text-align: left; background-color: {{ $i % 2 == 0 ? '#f8f9fa' : '#ffffff' }};">
                {{ ucwords(strtolower($bill->first_name)) }} {{ ucwords(strtolower($bill->middle_name)) }} {{ ucwords(strtolower($bill->last_name)) }}
            </td>
            <td style="border: 1px solid #ddd; padding: 6px; text-align: center; background-color: {{ $i % 2 == 0 ? '#f8f9fa' : '#ffffff' }};">
                {{ strtoupper($bill->class_code) }}
            </td>
            <td style="border: 1px solid #ddd; padding: 6px; text-align: center; background-color: {{ $i % 2 == 0 ? '#f8f9fa' : '#ffffff' }};">
                {{ $bill->academic_year }}
            </td>
            <td style="border: 1px solid #ddd; padding: 6px; text-align: left; background-color: {{ $i % 2 == 0 ? '#f8f9fa' : '#ffffff' }};">
                {{ $bill->service_name }}
            </td>
            <td style="border: 1px solid #ddd; padding: 6px; text-align: right; background-color: {{ $i % 2 == 0 ? '#f8f9fa' : '#ffffff' }};">
                {{ number_format($bill->amount) }}
            </td>
            <td style="border: 1px solid #ddd; padding: 6px; text-align: center; background-color: {{ $i % 2 == 0 ? '#f8f9fa' : '#ffffff' }};">
                <span style="
                    padding: 3px 8px;
                    border-radius: 3px;
                    font-size: 10px;
                    font-weight: bold;
                    {{ $bill->status == 'paid' ? 'background-color: #2ecc71; color: white;' : '' }}
                    {{ $bill->status == 'pending' ? 'background-color: #f39c12; color: white;' : '' }}
                    {{ $bill->status == 'overdue' ? 'background-color: #e74c3c; color: white;' : '' }}
                ">
                    {{ ucfirst(strtolower($bill->status)) }}
                </span>
            </td>
            <td style="border: 1px solid #ddd; padding: 6px; text-align: center; background-color: {{ $i % 2 == 0 ? '#f8f9fa' : '#ffffff' }};">
                {{ date('d/m/Y', strtotime($bill->due_date)) }}
            </td>
        </tr>
    @endforeach

    {{-- TOTAL ROW --}}
    <tr style="font-weight: bold; font-size: 12px; background-color: #ecf0f1;">
        <td colspan="6" style="border: 1px solid #ddd; padding: 10px; text-align: right;">GRAND TOTAL:</td>
        <td style="border: 1px solid #ddd; padding: 10px; text-align: right; color: #e74c3c; font-size: 14px;">
            {{ number_format($totalBilled) }}
        </td>
        <td colspan="2" style="border: 1px solid #ddd; padding: 10px;"></td>
    </tr>

    {{-- FOOTER --}}
    <tr><td colspan="9" style="height: 20px;"></td></tr>
    <tr>
        <td colspan="9" style="text-align: right; font-size: 10px; color: #7f8c8d; padding: 5px;">
            Generated on: {{ date('d/m/Y H:i:s') }}
        </td>
    </tr>
</table>
