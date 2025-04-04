<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Parents;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Transport;
use App\Models\school;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ParentStudentImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $user = Auth::user();
        $school = school::findOrFail($user->school_id);

        // Tafuta Class ID kwa kutumia Class Name
        $class = Grade::where('class_name', $row['class_name'])
                      ->where('school_id', $school->id)
                      ->first();

        if (!$class) {
            return null; // Ikiwa darasa halipo, ruka row hii
        }

        // Tafuta Transport ID kwa kutumia Bus Number (Optional)
        $transport = Transport::where('bus_no', $row['bus_no'])
                              ->where('school_id', $school->id)
                              ->first();


        // Define allowed columns
        $allowedColumns = [
            'student_first_name', 'student_last_name', 'student_middle_name',
            'student_dob', 'student_gender', 'student_group', 'parent_phone',
            'parent_first_name', 'parent_last_name', 'parent_email', 'parent_address'
        ];

        // Check if all columns in the file match allowed columns
        $excelColumns = array_keys($row);

        if (array_diff($excelColumns, $allowedColumns)) {
            // Log error or notify user
            Log::error('Columns mismatch detected!', ['columns' => $excelColumns]);
            // Alert()->toast('Columns mismatch detected!', 'error');
            // return back(); // Or handle error
        }

        $email = !empty($row['parent_email']) ? strtolower(trim($row['parent_email'])) : null;
        $phone = trim($row['parent_phone']);

        if ($email && User::where('email', $email)->exists()) {
            // Kama email ipo, chukua user wa email hiyo
            $user = User::where('email', $email)->first();
        } elseif (User::where('phone', $phone)->exists()) {
            // Kama simu ipo, chukua user wa simu hiyo
            $user = User::where('phone', $phone)->first();
        } else {
            // Hakuna email wala phone waliokuwepo, muunde mzazi mpya
            $user = User::create([
                'first_name' => htmlspecialchars(ucwords(strtolower($row['parent_first_name']))),
                'last_name' => htmlspecialchars(ucwords(strtolower($row['parent_last_name']))),
                'email' => $email,
                'phone' => $phone,
                'gender' => htmlspecialchars(strtolower($row['parent_gender'])),
                'usertype' => 4, // Mzazi
                'password' => Hash::make('shule2025'),
                'school_id' => $school->id,
            ]);
        }

        // Unda mzazi kwenye table ya parents
        $parent = Parents::firstOrCreate(
            ['user_id' => $user->id, 'school_id' => $school->id],
            ['address' => $row['parent_address']]
        );

        $dob = is_numeric($row['student_dob'])
                ? Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['student_dob'])->format('Y-m-d'))
                : Carbon::parse($row['student_dob']);

        $student = Student::firstOrCreate(
            [
                'first_name' => htmlspecialchars(ucwords(strtolower($row['student_first_name']))),
                'last_name' => htmlspecialchars(ucwords(strtolower($row['student_last_name']))),
                'dob' => htmlspecialchars($dob),
                'school_id' => $school->id,
            ],
            [
                'admission_number' => $this->getAdmissionNumber($school->id),
                'middle_name' => htmlspecialchars(ucwords(strtolower($row['student_middle_name'] ?? ''))),
                'gender' => htmlspecialchars(strtolower($row['student_gender'])),
                'parent_id' => $parent->id,
                'class_id' => $class->id,
                'transport_id' => $transport ? $transport->id : null,
                'group' => htmlspecialchars($row['student_group']),
            ]
        );

                // Tuma SMS kwa mzazi kuhusu akaunti yake mpya tu
        if ($user->wasRecentlyCreated) {
            $formattedPhone = $this->formatPhoneNumber($user->phone); // tumia namba halisi iliyosajiliwa

            $nextSmsService = new NextSmsService();
            $link = "https://shuleapp.tech";
            $payload = [
                'from' => $school->sender_id ?? "SHULE APP",
                'to' => [$formattedPhone], // iwe array
                'text' => "Welcome to ShuleApp!\n\nYour login credentials are:\nUsername: {$user->phone}\nPassword: shule2025\n\nLogin here: $link",
                'reference' => $user->id,
            ];

            // $response = $nextSmsService->sendSmsByNext(
            //     $payload['from'],
            //     $payload['to'],
            //     $payload['text'],
            //     $payload['reference']
            // );
        }

        return $student;
    }

    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure the number starts with the country code (e.g., 255 for Tanzania)
        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        return $phone;
    }

    protected function getAdmissionNumber($school_id)
    {
        $schoolData = school::findOrFail($school_id);
        $lastStudent = Student::where('school_id', $school_id)
                            ->orderBy('id', 'desc')
                            ->first();

        $lastId = $lastStudent ? $lastStudent->id + 1 : 1;
        $admissionNumber = str_pad($lastId, 4, '0', STR_PAD_LEFT);

        while (Student::where('admission_number', $schoolData->abbriv_code . '-' . $admissionNumber)->exists()) {
            $lastId++;
            $admissionNumber = str_pad($lastId, 4, '0', STR_PAD_LEFT);
        }

        return $schoolData->abbriv_code . '-' . $admissionNumber;
    }

}
