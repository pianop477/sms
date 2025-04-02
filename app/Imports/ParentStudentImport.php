<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Parents;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Services\NextSmsService;
use App\Services\BeemSmsService;

class ParentStudentImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        // Check if email exists, if not, set null
        $email = isset($row['email']) && !empty($row['email']) ? $row['email'] : null;

        // Ensure unique email if provided
        if ($email && User::where('email', $email)->exists()) {
            return null; // Skip duplicate emails
        }

        // Create User (Parent Account)
        $parentUser = User::create([
            'first_name' => $row['parent_first_name'],
            'last_name'  => $row['parent_last_name'],
            'email'      => $email,
            'phone'      => $row['phone'],
            'gender'     => $row['gender'],
            'usertype'   => 4, // Parent role
            'password'   => Hash::make('shule2025'),
            'school_id'  => $schoolId,
        ]);

        // Create Parent Record
        $parent = Parents::create([
            'user_id'   => $parentUser->id,
            'school_id' => $schoolId,
            'address'   => $row['address'],
        ]);

        // Create Student Record
        $student = Student::create([
            'admission_number' => $this->getAdmissionNumber($schoolId),
            'first_name'       => $row['student_first_name'],
            'middle_name'      => $row['student_middle_name'],
            'last_name'        => $row['student_last_name'],
            'parent_id'        => $parent->id,
            'gender'           => $row['student_gender'],
            'dob'              => $row['dob'],
            'class_id'         => $row['class_id'],
            'transport_id'     => $row['transport_id'],
            'group'            => $row['group'],
            'school_id'        => $schoolId,
        ]);

        // Send SMS Notification
        $this->sendSmsNotification($parentUser, $schoolId);
    }

    private function getAdmissionNumber($schoolId)
    {
        $lastStudent = Student::where('school_id', $schoolId)->orderBy('id', 'desc')->first();
        $lastId = $lastStudent ? $lastStudent->id + 1 : 1;
        return 'SCH-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
    }

    private function sendSmsNotification($user, $schoolId)
    {
        $school = User::where('id', $schoolId)->first();
        $senderId = $school->sender_id ?? "SHULE APP";
        $url = "https://shuleapp.tech";
        $message = "Welcome to ShuleApp, Your Login details are: Username: {$user->phone} Password: shule2025. Visit {$url} to Login";
        $reference = uniqid();

        $formattedPhone = $this->formatPhoneNumber($user->phone);
        $recipients = [['recipient_id' => 1, 'dest_addr' => $formattedPhone]];

        $beemSmsService = new BeemSmsService();
        $beemSmsService->sendSms($senderId, $message, $recipients);
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) == 9) {
            return '255' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            return '255' . substr($phone, 1);
        }
        return $phone;
    }
}
