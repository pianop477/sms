<?php
// app/Exports/EPermitReportExport.php

namespace App\Exports;

use App\Models\EPermit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EPermitReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $permits;
    protected $filters;

    public function __construct($permits, $filters)
    {
        $this->permits = $permits;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->permits;
    }

    public function headings(): array
    {
        return [
            '#',
            'Permit Number',
            'Student Name',
            'Student ID',
            'Class',
            'Stream/Group',
            'Guardian Name',
            'Guardian Phone',
            'Relationship',
            'Reason',
            'Departure Date',
            'Departure Time',
            'Expected Return Date',
            'Status',
            'Created Date',
            'Approved By Class Teacher',
            'Approved By Academic Teacher',
            'Approved By Head Teacher',
            'Return Date',
            'Return Status'
        ];
    }

    public function map($permit): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $reasonText = match($permit->reason) {
            'medical' => 'Matibabu',
            'family_matter' => 'Jambo la Kifamilia',
            'other' => 'Sababu Nyingine',
            default => ucfirst($permit->reason)
        };

        if ($permit->reason === 'other' && $permit->other_reason) {
            $reasonText .= ' - ' . $permit->other_reason;
        }

        $returnStatus = $permit->is_late_return ? 'Late Return' : ($permit->verified_at ? 'On Time' : 'Not Returned Yet');

        return [
            $rowNumber,
            $permit->permit_number,
            ucwords(strtolower($permit->student->first_name . ' ' . $permit->student->last_name)),
            strtoupper($permit->student->admission_number),
            strtoupper($permit->student->class->class_name ?? 'N/A'),
            strtoupper($permit->student->group ?? 'N/A'),
            ucwords(strtolower($permit->guardian_name)),
            $permit->guardian_phone,
            ucfirst($permit->relationship),
            $reasonText,
            $permit->departure_date->format('d/m/Y'),
            $permit->departure_time->format('H:i'),
            $permit->expected_return_date->format('d/m/Y'),
            ucfirst($permit->status),
            $permit->created_at->format('d/m/Y H:i'),
            $permit->classTeacher?->user?->first_name. ' '. $permit->classTeacher?->user?->last_name ?? 'N/A',
            $permit->academicTeacher?->user?->first_name. ' '. $permit->academicTeacher?->user?->last_name ?? 'N/A',
            $permit->headTeacher?->user?->first_name. ' '. $permit->headTeacher?->user?->last_name ?? 'N/A',
            $permit->verified_at ? $permit->verified_at->format('d/m/Y H:i') : 'N/A',
            $returnStatus
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '667eea']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 25,
            'D' => 15,
            'E' => 12,
            'F' => 12,
            'G' => 25,
            'H' => 15,
            'I' => 15,
            'J' => 30,
            'K' => 15,
            'L' => 10,
            'M' => 15,
            'N' => 15,
            'O' => 18,
            'P' => 25,
            'Q' => 25,
            'R' => 25,
            'S' => 18,
            'T' => 15,
        ];
    }
}
