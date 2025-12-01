<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BatchBillsExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $school;
    protected $bills;
    protected $totalBilled;
    protected $batch;

    public function __construct($school, $bills, $totalBilled, $batch)
    {
        $this->school = $school;
        $this->bills = $bills;
        $this->totalBilled = $totalBilled;
        $this->batch = $batch;
    }

    public function view(): View
    {
        return view('payment_batches.batch_export', [
            'school' => $this->school,
            'bills' => $this->bills,
            'totalBilled' => $this->totalBilled,
            'batch' => $this->batch,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            6 => ['font' => ['bold' => true]], // header row
        ];
    }
}
