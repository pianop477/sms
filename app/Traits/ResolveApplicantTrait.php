<?php
// app/Traits/ResolveApplicantTrait.php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ResolveApplicantTrait
{
    private function resolveApplicantDetails($identifier, $schoolId = null)
    {
        // Kwanza angalia kama identifier ni user_id (numeric)
        if (is_numeric($identifier)) {
            // Try to find in TEACHERS table via user_id
            $teacher = DB::table('teachers')
                ->join('users', 'users.id', '=', 'teachers.user_id')
                ->where('teachers.user_id', $identifier)
                ->where('teachers.status', 1)
                ->where('users.status', 1)
                ->select(
                    'users.first_name',
                    'users.last_name',
                    'users.gender',
                    'users.phone',
                    'teachers.member_id as staff_id',
                    DB::raw("'Teacher' as staff_type"),
                    'teachers.id as staff_table_id',
                    'users.id as user_id',
                    'teachers.school_id'
                )
                ->first();

            if ($teacher) {
                return (array) $teacher;
            }

            // Angalia kwenye TRANSPORT kwa user_id (kama ipo)
            $transport = DB::table('transports')
                ->where('transports.user_id', $identifier)
                ->where('transports.status', 1)
                ->select(
                    'transports.driver_name as first_name',
                    DB::raw("'' as last_name"),
                    'transports.gender',
                    'transports.phone',
                    'transports.staff_id',
                    DB::raw("'Transport Staff' as staff_type"),
                    'transports.id as staff_table_id',
                    DB::raw("NULL as user_id"),
                    'transports.school_id'
                )
                ->first();

            if ($transport) {
                return (array) $transport;
            }

            // Angalia kwenye OTHER_STAFFS kwa user_id (kama ipo)
            $otherStaff = DB::table('other_staffs')
                ->where('other_staffs.user_id', $identifier)
                ->where('other_staffs.status', 1)
                ->select(
                    'other_staffs.first_name',
                    'other_staffs.last_name',
                    'other_staffs.gender',
                    'other_staffs.phone',
                    'other_staffs.staff_id',
                    DB::raw("'Other Staff' as staff_type"),
                    'other_staffs.id as staff_table_id',
                    DB::raw("NULL as user_id"),
                    'other_staffs.school_id'
                )
                ->first();

            if ($otherStaff) {
                return (array) $otherStaff;
            }
        }

        // Kama identifier ni staff_id/member_id (string)
        // Try to find in TEACHERS table by member_id
        $teacher = DB::table('teachers')
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->where('teachers.member_id', $identifier)
            ->select(
                'users.first_name',
                'users.last_name',
                'users.gender',
                'users.phone',
                'teachers.member_id as staff_id',
                DB::raw("'Teacher' as staff_type"),
                'teachers.id as staff_table_id',
                'users.id as user_id',
                'teachers.school_id'
            )
            ->first();

        if ($teacher) {
            return (array) $teacher;
        }

        // Try to find in TRANSPORT table by staff_id
        $transport = DB::table('transports')
            ->where('transports.staff_id', $identifier)
            ->select(
                'transports.driver_name as first_name',
                DB::raw("'' as last_name"),
                'transports.gender',
                'transports.phone',
                'transports.staff_id',
                DB::raw("'Transport Staff' as staff_type"),
                'transports.id as staff_table_id',
                DB::raw("NULL as user_id"),
                'transports.school_id'
            )
            ->first();

        if ($transport) {
            return (array) $transport;
        }

        // Try to find in OTHER_STAFFS table by staff_id
        $otherStaff = DB::table('other_staffs')
            ->where('other_staffs.staff_id', $identifier)
            ->select(
                'other_staffs.first_name',
                'other_staffs.last_name',
                'other_staffs.gender',
                'other_staffs.phone',
                'other_staffs.staff_id',
                DB::raw("'Other Staff' as staff_type"),
                'other_staffs.id as staff_table_id',
                DB::raw("NULL as user_id"),
                'other_staffs.school_id'
            )
            ->first();

        if ($otherStaff) {
            return (array) $otherStaff;
        }

        // If not found anywhere, return basic info
        return [
            'first_name' => 'Unknown',
            'last_name' => 'Staff',
            'gender' => 'Not Specified',
            'phone' => null,
            'staff_id' => $identifier,
            'staff_type' => 'Unknown',
            'staff_table_id' => null,
            'user_id' => null,
            'school_id' => null
        ];
    }
}
