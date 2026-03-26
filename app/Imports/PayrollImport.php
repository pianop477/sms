<?php
// app/Imports/PayrollImport.php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PayrollImport implements ToCollection, WithHeadingRow
{
    public $data = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->data[] = [
                'staff_id' => $row['staff_id'] ?? null,
                'basic_salary' => $row['basic_salary'] ?? 0,
                'allowances' => $row['allowances'] ?? 0,
                'department' => $row['department'] ?? '',
                'contract_type' => $row['contract_type'] ?? 'new',
            ];
        }
    }

    public function getData()
    {
        return $this->data;
    }
}
