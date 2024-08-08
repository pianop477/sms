<?php

namespace App\Exports;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeachersExport implements FromView, WithStyles, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view() : View
    {
        $userLogged = Auth::user();
        $teachers = Teacher::query()
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->join('schools', 'schools.id', '=', 'teachers.school_id')
            ->select('teachers.*', 'users.first_name', 'users.last_name', 'users.gender', 'users.phone', 'users.email',
            'teachers.qualification', 'schools.school_reg_no', 'teachers.address')
            ->where('teachers.school_id', '=', $userLogged->school_id)
            ->where(function ($query) {
                $query->where('teachers.status', 1)
                    ->orWhere('teachers.status', 0);
            })
            ->orderBy('users.first_name', 'ASC')
            ->get();

        return view('Export.teachers', [
            'teachers' => $teachers
        ]);
    }

    /**
    * Apply styles to the spreadsheet
    *
    * @param Worksheet $sheet
    * @return array
    */
    public function styles(Worksheet $sheet)
{
    // Merge cells A1 to L1 for the title
    $sheet->mergeCells('A1:L1');

    // Style the merged title cell
    $sheet->getStyle('A1')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 16,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '1D4E89'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Set the width of columns
    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(10);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->getColumnDimension('H')->setWidth(25);
    $sheet->getColumnDimension('I')->setWidth(20);
    $sheet->getColumnDimension('J')->setWidth(15);
    $sheet->getColumnDimension('K')->setWidth(30);
    $sheet->getColumnDimension('L')->setWidth(10);

    // Style the header
    $sheet->getStyle('A2:L2')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 12,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '1D4E89'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Style the gender column (C) to be uppercase
    $sheet->getStyle('C3:C' . $sheet->getHighestRow())->applyFromArray([
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        ],
    ]);

    // Style the ID column (B) and other columns to be capitalized
    $sheet->getStyle('B3:L' . $sheet->getHighestRow())->applyFromArray([
        'font' => [
            'size' => 11,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        ],
        'textTransform' => 'capitalize', // Custom text transformation (Note: Only applies to certain methods, but included for clarity)
    ]);

    // Ensure the rest of the columns are also properly styled
    $sheet->getStyle('A3:A' . $sheet->getHighestRow())->applyFromArray([
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ]);

    // Set the height of rows
    $sheet->getDefaultRowDimension()->setRowHeight(20);

    return [];
}


    /**
    * Set the title of the worksheet
    *
    * @return string
    */
    public function title(): string
    {
        return 'Teachers Lists';
    }
}
