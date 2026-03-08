<?php
// app/Traits/ResolveApplicantTrait.php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ResolveApplicantTrait
{
    private function resolveApplicantDetails($identifier, $schoolId = null)
    {

        /*
        |--------------------------------------------------------------------------
        | 1. SEARCH TEACHERS BY MEMBER_ID
        |--------------------------------------------------------------------------
        */

        $teacher = DB::table('teachers')
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->where('teachers.member_id', $identifier)
            ->where('teachers.status', 1)
            ->where('users.status', 1)
            ->select(
                'users.first_name',
                'users.last_name',
                'users.gender',
                'users.phone',
                'users.email',
                'users.image as profile_image',
                'teachers.member_id as staff_id',
                'teachers.address',
                'teachers.nida',
                'teachers.bank_account_number',
                'teachers.bank_account_name',
                'teachers.bank_name',
                'teachers.dob',
                'teachers.qualification',
                DB::raw("'Teacher' as staff_type"),
                'teachers.id as staff_table_id',
                'teachers.school_id',
                'users.id as user_id'
            )
            ->first();

        if ($teacher) {
            return (array) $teacher;
        }


        /*
        |--------------------------------------------------------------------------
        | 2. SEARCH TRANSPORT STAFF BY STAFF_ID
        |--------------------------------------------------------------------------
        */

        $transport = DB::table('transports')
            ->where('staff_id', $identifier)
            ->where('status', 1)
            ->select(
                'driver_name as first_name',
                DB::raw("'' as last_name"),
                'gender',
                'phone',
                'email',
                'staff_id',
                'street_address as address',
                'nida',
                'bank_account_number',
                'bank_account_name',
                'bank_name',
                'educational_level as qualification',
                'date_of_birth as dob',
                'profile_image',
                DB::raw("'Transport Staff' as staff_type"),
                'id as staff_table_id',
                'school_id',
                DB::raw("NULL as user_id")
            )
            ->first();

        if ($transport) {
            return (array) $transport;
        }


        /*
        |--------------------------------------------------------------------------
        | 3. SEARCH OTHER STAFF BY STAFF_ID
        |--------------------------------------------------------------------------
        */

        $otherStaff = DB::table('other_staffs')
            ->where('staff_id', $identifier)
            ->where('status', 1)
            ->select(
                'first_name',
                'last_name',
                'gender',
                'phone',
                'email',
                'staff_id',
                'street_address as address',
                'nida',
                'bank_account_number',
                'bank_account_name',
                'bank_name',
                'educational_level as qualification',
                'date_of_birth as dob',
                'profile_image',
                DB::raw("'Other Staff' as staff_type"),
                'id as staff_table_id',
                'school_id',
                DB::raw("NULL as user_id")
            )
            ->first();

        if ($otherStaff) {
            return (array) $otherStaff;
        }


        /*
        |--------------------------------------------------------------------------
        | 4. SEARCH TEACHERS BY USER_ID (ONLY IF NUMERIC)
        |--------------------------------------------------------------------------
        */

        if (is_numeric($identifier)) {

            $teacherByUserId = DB::table('teachers')
                ->join('users', 'users.id', '=', 'teachers.user_id')
                ->where('teachers.user_id', $identifier)
                ->where('teachers.status', 1)
                ->where('users.status', 1)
                ->select(
                    'users.first_name',
                    'users.last_name',
                    'users.gender',
                    'users.phone',
                    'users.email',
                    'users.image as profile_image',
                    'teachers.member_id as staff_id',
                    'teachers.address',
                    'teachers.nida',
                    'teachers.bank_account_number',
                    'teachers.bank_account_name',
                    'teachers.bank_name',
                    'teachers.dob',
                    'teachers.qualification',
                    DB::raw("'Teacher' as staff_type"),
                    'teachers.id as staff_table_id',
                    'teachers.school_id',
                    'users.id as user_id'
                )
                ->first();

            if ($teacherByUserId) {
                return (array) $teacherByUserId;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | NOT FOUND
        |--------------------------------------------------------------------------
        */

        return [
            'first_name' => 'Unknown',
            'last_name' => 'Staff',
            'gender' => 'Not Specified',
            'phone' => null,
            'email' => null,
            'staff_id' => $identifier,
            'address' => null,
            'staff_type' => 'Unknown',
            'staff_table_id' => null,
            'school_id' => $schoolId,
            'user_id' => null
        ];
    }
}
