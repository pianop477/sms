<?php

namespace App\Exports;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
// use Maatwebsite\Excel\Concerns\FromView;

class TeachersExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view() : view
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

}
