<?php

namespace App\Imports;

use App\Models\Grade;
use App\Models\Parents;
use App\Models\School;
use App\Models\Student;
use App\Models\Transport;
use App\Models\User;
use App\Jobs\SendParentWelcomeSms;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParentStudentImport implements ToModel, WithValidation, WithHeadingRow
{
    public function rules(): array
    {
        return [
            'parent_first_name' => 'required|string',
            'parent_last_name' => 'required|string',
            'parent_email' => 'nullable|email',
            'parent_phone' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    // Ondoa space na characters zisizo namba
                    $phone = preg_replace('/[^0-9]/', '', $value);

                    // Angalia muundo wa namba
                    $isValid = false;

                    // 1. Muundo wa 0XXXXXXXXX (10 tarakimu)
                    if (preg_match('/^0\d{9}$/', $phone)) {
                        $isValid = true;
                    }
                    // 2. Muundo wa 255XXXXXXXXX (12 tarakimu)
                    elseif (preg_match('/^255\d{9}$/', $phone)) {
                        $isValid = true;
                    }
                    // 3. Muundo wa XXXXXXXX (9 tarakimu)
                    elseif (preg_match('/^\d{9}$/', $phone)) {
                        $isValid = true;
                    }

                    if (!$isValid) {
                        $fail('Namba ya simu lazima iwe: 0712345678, 255712345678 au 712345678');
                    }
                },
            ],

            'parent_gender' => 'required|string',
                                function ($attribute, $value, $fail) {
                                    $value = strtolower(trim($value));
                                    $allowed = ['male', 'female'];

                                    if(!in_array($value, $allowed)) {
                                        $fail("Invalid selected {$attribute} is invalid. allowed values are: ". implode(', ', $allowed));
                                    }
                                },
            'parent_address' => 'required|string',
            'class_name' => 'required|exists:grades,class_name,school_id,' . Auth::user()->school_id,
            'bus_no' => 'nullable|exists:transports,bus_no,school_id,' . Auth::user()->school_id,
            'student_first_name' => 'required|string',
            'student_middle_name' => 'nullable|string',
            'student_last_name' => 'required|string',
            'student_gender' => 'required|string',
                                function ($attribute, $value, $fail) {
                                    $value = strtolower(trim($value));
                                    $allowed = ['male', 'female'];

                                    if(!in_array($value, $allowed)) {
                                        $fail("Invalid selected {$attribute} is invalid. allowed values are: ". implode(', ', $allowed));
                                    }
                                },
            'student_dob' => [
                'required',
                    function ($attribute, $value, $fail) {
                        try {
                            $date = \Carbon\Carbon::parse($value);
                            if ($date->isAfter(now())) {
                                $fail('The date of birth must be before or equal today.');
                        }
                    } catch (\Exception $e) {
                        $fail('Invalid date format. Use mm/dd/yyyy format.');
                    }
                }
            ],
            'student_group' => [
                'required',
                'string',
                    function ($attribute, $value, $fail) {
                        $value = strtolower(trim($value)); // Convert to lowercase & remove spaces
                        $allowed = ['a', 'b', 'c', 'd', 'e'];

                        if (!in_array($value, $allowed)) {
                            $fail("The selected {$attribute} is invalid. Allowed values are: " . implode(', ', $allowed));
                        }
                    },
            ],
        ];
    }

    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            $user = Auth::user();
            $school = School::findOrFail($user->school_id);

            // Find Class
            $class = Grade::where('class_name', $row['class_name'])
                ->where('school_id', $school->id)
                ->firstOrFail();

            // Find Transport (Optional)
            $transport = Transport::where('bus_no', $row['bus_no'])
                ->where('school_id', $school->id)
                ->first();

            // Format & Validate Data
            $email = !empty($row['parent_email']) ? strtolower(trim($row['parent_email'])) : null;

            // Rekebisha namba ya simu inayohifadhiwa kwenye table
            $phone = $row['parent_phone'];
            $preparedPhone = $this->formatPhoneForDatabase($phone);

            // Create/Find Parent User
            $user = User::firstOrCreate(
                ['email' => ucwords(strtolower($email))],
                [
                    'first_name' => ucwords(strtolower($row['parent_first_name'])),
                    'last_name' => ucwords(strtolower($row['parent_last_name'])),
                    'gender' => ucwords(strtolower($row['parent_gender'])),
                    'phone' => $preparedPhone,
                    'usertype' => 4,
                    'password' => Hash::make('shule2025'),
                    'school_id' => $school->id,
                ]
            );

            // Create Parent Record
            $parent = Parents::firstOrCreate(
                ['user_id' => $user->id, 'school_id' => $school->id],
                ['address' => ucwords(strtolower($row['parent_address']))],
                ['school_id' => $school->id],
            );

            // Parse Student DOB
            $dob = is_numeric($row['student_dob'])
                ? Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['student_dob']))
                : Carbon::parse($row['student_dob']);

            // Create Student
            $student = Student::firstOrCreate(
                [
                    'first_name' => ucwords(strtolower($row['student_first_name'])),
                    'middle_name' => ucwords(strtolower($row['student_middle_name'])),
                    'last_name' => ucwords(strtolower($row['student_last_name'])),
                    'school_id' => $school->id,
                    'gender' => ucwords(strtolower($row['student_gender'])),
                    'group' => ucwords(strtolower($row['student_group'])),
                ],
                [
                    'dob' => $dob,
                    'admission_number' => $this->getAdmissionNumber($school->id),
                    'class_id' => $class->id,
                    'parent_id' => $parent->id,
                    'transport_id' => $transport?->id,
                ]
            );

            if ($user->wasRecentlyCreated) {
                $formattedPhone = $this->formatPhoneNumber($user->phone); // tumia namba halisi iliyosajiliwa

                $nextSmsService = new NextSmsService();
                $link = "https://shuleapp.tech/login";
                $payload = [
                    'from' => $school->sender_id ?? "SHULE APP",
                    'to' => [$formattedPhone], // iwe array
                    'text' => "Hello {$user->first_name} {$user->last_name}\nWelcome to ShuleApp\nYour login credentials are:\nUsername: {$preparedPhone}\nPassword: shule2025\nclick here {$link} to Login",
                    'reference' => $user->id,
                ];

                // Log::info("Sending Message to: " . $user->phone, ['message' => $payload['text']]);
                $response = $nextSmsService->sendSmsByNext(
                    $payload['from'],
                    $payload['to'],
                    $payload['text'],
                    $payload['reference']
                );
            }

            return $student;
        });
    }

    protected function formatPhoneForDatabase(string $phone): string
    {
        // Ondoa character zisizo namba
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // 1. Kama iko kwa muundo wa 255XXXXXXXXX
        if (strlen($phone) === 12 && str_starts_with($phone, '255')) {
            return '0' . substr($phone, 3);
        }
        // 2. Kama iko kwa muundo wa XXXXXXXX (9 tarakimu)
        elseif (strlen($phone) === 9) {
            return '0' . $phone;
        }
        // 3. Kama tayari iko kwa muundo sahihi (0XXXXXXXXX)
        return $phone;
    }

    protected function getAdmissionNumber($school_id)
    {
        $school = School::findOrFail($school_id);

        $lastStudent = Student::where('school_id', $school_id)
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->first();

        $lastId = $lastStudent ? $lastStudent->id + 1 : 1;
        $admissionNumber = str_pad($lastId, 4, '0', STR_PAD_LEFT);

        return $school->abbriv_code . '-' . $admissionNumber;
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && $phone[0] == '0') {
            $phone = '255' . substr($phone, 1);
        }

        return $phone;
    }
}
