<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentsExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $classId;

    public function __construct($classId)
    {
        $this->classId = $classId;
    }

    public function collection()
    {
        $user = auth()->user();

        return Student::query()
            ->join('grades', 'grades.id', '=', 'students.class_id')
            ->join('parents', 'parents.id', '=', 'students.parent_id')
            ->leftJoin('users', 'users.id', '=', 'parents.user_id')
            ->select(
                'students.admission_number',
                'students.gender',
                'students.group',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.dob',
                'parents.address',
                'users.phone',
                'students.transport_id',
                'grades.class_name',
                'grades.class_code'
            )
            ->where('students.class_id', $this->classId)
            ->where('students.school_id', $user->school_id)
            ->where('students.status', 1)
            ->orderBy('students.first_name')
            ->get();
    }

    public function map($student): array
    {
        return [
            strtoupper($student->admission_number),
            ucwords(strtolower($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name)),
            strtoupper(substr($student->gender, 0, 1)),
            strtoupper($student->class_code ?? ''),
            strtoupper($student->group ?? ''),
            $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '',
            ucwords(strtolower($student->address ?? '')),
            $student->phone ?? '',
            $student->transport_id ? 'Yes' : 'No',
        ];
    }

    public function headings(): array
    {
        return [
            'Admission No',
            'Student Name',
            'Gender',
            'Class',
            'Stream',
            'DOB',
            'Address',
            'Parent Phone',
            'School Bus',
        ];
    }
}

