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
                'parents.address', 'users.first_name as parent_first_name',
                'users.last_name as parent_last_name', 'users.email as parent_email',
                'users.phone', 'users.gender as parent_gender',
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
            ucwords(strtolower($student->first_name)),
            ucwords(strtolower($student->middle_name)),
            ucwords(strtolower($student->last_name)),
            strtoupper(substr($student->gender, 0, 1)),
            strtoupper($student->class_code ?? ''),
            strtoupper($student->group ?? ''),
            $student->dob ? \Carbon\Carbon::parse($student->dob)->format('Y-m-d') : '',
            $student->parent_first_name ?? '',
            $student->parent_last_name ?? '',
            strtoupper(substr($student->parent_gender, 0, 1) ?? ''),
            $student->parent_email ?? '',
            $student->phone ?? '',
            ucwords(strtolower(substr($student->address ?? '', 0, 15))),
            $student->transport_id ? 'Yes' : 'No',
        ];
    }

    public function headings(): array
    {
        return [
            'Admission No',
            'First name',
            'Middle name',
            'Last name',
            'Gender',
            'Class',
            'Stream',
            'DOB',
            'Parent First Name',
            'Parent Last Name',
            'Parent Gender',
            'Parent Email',
            'Parent Phone',
            'Address',
            'School Bus',
        ];
    }
}

