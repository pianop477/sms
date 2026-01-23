<?php

namespace App\Http\Controllers;

use App\Models\other_staffs;
use App\Models\school;
use App\Models\Teacher;
use App\Models\Transport;
use Dompdf\Dompdf;
use Dompdf\Dompdf as DompdfDompdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpWord\PhpWord;
use Vinkla\Hashids\Facades\Hashids;

class OtherStaffsController extends Controller
{
    //

    public function index()
    {
        $drivers = Transport::all();
        $otherStaffs = other_staffs::all();

        // Normalize name key for every record
        $normalizedDrivers = $drivers->map(function ($driver) {
            $driver->full_name = $driver->driver_name;   // from Transport model
            return $driver;
        });

        $normalizedStaffs = $otherStaffs->map(function ($staff) {
            $staff->full_name = $staff->first_name;      // from OtherStaffs model
            return $staff;
        });

        // Combine
        $combinedStaffs = $normalizedStaffs
                            ->concat($normalizedDrivers)
                            ->sortBy('full_name');

        return view('OtherStaffs.index', compact('combinedStaffs'));
    }


    public function addStaffInformation (Request $request)
    {
        $this->validate($request, [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:other_staffs,phone',
            'email' => 'nullable|email|unique:other_staffs,email',
            'education' => 'required|string|max:255',
            'dob' => 'required|date|date_format:Y-m-d',
            'street' => 'required|string|max:255',
            'joined' => 'required|date_format:Y',
            'job_title' => 'required|string|max:255',
            'nida' => 'required|string|regex:/^\d{8}-?\d{5}-?\d{5}-?\d{2}$/',
            'image' => [
                    'nullable',
                    'file',
                    'mimetypes:image/jpeg,image/png,image/jpg',
                    'max:1024'
                ],
        ],
        [
           'fname.required' => 'First name is required',
            'lname.required' => 'Last name is required',
            'dob.required' => 'Date of birth is required',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must be 10 digits',
            'education.required' => 'Qualification is required',
            'street.required' => 'Street address is required',
            'email.unique' => 'Email already exists',
            'email.email' => 'Email must be a valid email address',
            'joined.required' => 'Joined date is required',
            'job_title.required' => 'Job title is required',
            'image.file' => 'Image file must be a valid image file type',
            'image.max' => 'file size is too large, maximum 1 MB',
            'nida.required' => 'NIN must be filled',
            'nida.regex' => 'Invalid NIN format',
        ]);

         try {

         if($request->hasFile('image')) {
                // Virus scan
                $scanResult = $this->scanFileForViruses($request->file('image'));
                if (! $scanResult['clean']) {
                    Alert()->toast('File security check failed: ' . $scanResult['message'], 'error');
                    return back();
                }
            }

             $nin = preg_replace('/[^0-9]/', '', $request->nida);

            // Check for existing student records
            $existingStaff = other_staffs::query()
                                ->when($request->phone, fn ($q) =>
                                    $q->where('phone', $request->phone)
                                )
                                ->when($nin, fn ($q) =>
                                    $q->orWhere('nida', $nin)
                                )
                                ->when($request->email, fn ($q) =>
                                    $q->orWhere('email', $request->email)
                                )
                                ->exists();

            if ($existingStaff) {
                    Alert()->toast('Staff with the same records already exists', 'error');
                    return back();
            }

            $addStaff = other_staffs::create([
                'staff_id' => $this->getStaffId(),
                'first_name' => $request->fname,
                'last_name' => $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'educational_level' => $request->education,
                'date_of_birth' => $request->dob,
                'job_title' => $request->job_title,
                'street_address' => $request->street,
                'joining_year' => $request->joined,
                'nida' => $nin
                // 'profile_image' => $profile_img
            ]);

            $profile_image = null;

            if ($request->hasFile('image')) {
                $imageFile = time() . '_' . uniqid() . '.' . $request->image->getClientOriginalExtension();
                $request->image->storeAs('profile', $imageFile, 'public');
                $profile_image = $imageFile;
            }

            $addStaff->profile_image = $profile_image;
            $addStaff->save();

            Alert()->toast('Staff information has been recorded successfully', 'success');
            return back();
         } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
         }

    }


    public function staffProfile($type, $id)
    {
        $decoded = Hashids::decode($id);

        if ($type === 'driver') {
            $staff = Transport::findOrFail($decoded[0]);
        }
        else {
            $staff = other_staffs::findOrFail($decoded[0]);
        }

        return view('OtherStaffs.staff_details', compact('staff', 'type'));
    }

    public function updateStaffProfile(Request $request, $type, $id)
    {
        $decoded = Hashids::decode($id);
        if (empty($decoded)) {
            Alert()->toast('Invalid staff identifier', 'error');
            return back();
        }

        // Determine model and table dynamically
        $modelClass = $type === 'driver' ? Transport::class : other_staffs::class;
        $tableName  = $type === 'driver' ? 'transports' : 'other_staffs';

        try {
            $staff = $modelClass::findOrFail($decoded[0]);
        } catch (\Throwable $e) {
            Alert()->toast('Staff record not found', 'error');
            return back();
        }

        // Dynamic validation rules
        $commonRules = [
            'gender'     => 'required|string|in:male,female',
            'phone'      => ["required", "regex:/^[0-9]{10}$/", "unique:{$tableName},phone,{$staff->id},id"],
            'email'      => "nullable|email|unique:{$tableName},email,{$staff->id},id",
            'education'  => 'required|string|max:255',
            'dob'        => 'required|date|date_format:Y-m-d',
            'street'     => 'required|string|max:255',
            'joined'     => 'required|date_format:Y',
            'job_title'  => 'required|string|max:255',
            'image'      => ['nullable','file','mimetypes:image/jpeg,image/png,image/jpg','max:1024'],
            'nida'       => 'required|string|regex:/^\d{8}-?\d{5}-?\d{5}-?\d{2}$/'
        ];

        $extra = $type === 'driver'
            ? ['fname' => 'required|string|max:255']
            : ['fname' => 'required|string|max:255', 'lname' => 'required|string|max:255'];

        $rules = array_merge($commonRules, $extra);

        $messages = [
            'fname.required' => 'First name is required',
            'lname.required' => 'Last name is required',
            'phone.required' => 'Phone number is required',
            'phone.regex'    => 'Phone number must be 10 digits',
            'email.email'    => 'Email must be a valid email address',
            'joined.date_format' => 'Joining year must be in YYYY format',
            'image.max'      => 'File size is too large, maximum 1 MB',
            'nida.required' => 'NIN must be filled',
            'nida.regex' => 'Invalid NIN format provided'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Virus scan (optional)
        if ($request->hasFile('image')) {
            $scanResult = $this->scanFileForViruses($request->file('image'));
            if (!$scanResult['clean']) {
                Alert()->toast('File security check failed: ' . $scanResult['message'], 'error');
                return back()->withInput();
            }
        }

        $nin = preg_replace('/[^0-9]/', '', $request->nida);

        // Map fields based on staff type
        $updateData = [
            'gender'            => $request->gender,
            'phone'             => $request->phone,
            'email'             => $request->email,
            'job_title'         => $request->job_title,
            'date_of_birth'     => $request->dob,
            'street_address'    => $request->street,
            'joining_year'      => $request->joined,
            'educational_level' => $request->education,
            'nida'              => $nin
        ];

        if ($type === 'driver') {
            $updateData['driver_name'] = $request->fname;
        } else {
            $updateData['first_name'] = $request->fname;
            $updateData['last_name']  = $request->lname;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $updateData['profile_image'] = $this->handleProfileImageUpload($request->file('image'), $staff->profile_image);
        }

        try {
            $staff->update($updateData);

            // assign staff_id if missing
            if (is_null($staff->staff_id)) {
                $staff->staff_id = $this->getStaffId();
                $staff->save();
            }

            Alert()->toast('Staff information updated successfully', 'success');
            return back();

        } catch (\Throwable $e) {
            Log::error('Error updating staff profile: '.$e->getMessage(), [
                'staff_id' => $staff->id,
                'type' => $type,
                'payload' => $updateData,
            ]);
            Alert()->toast('An unexpected error occurred while updating profile', 'error');
            return back()->withInput();
        }
    }

    public function blockStatus($type, $id, Request $request, )
    {
        $decoded = Hashids::decode($id);

        try {
            if($type == 'driver') {
                $staff = Transport::findOrFail($decoded[0]);
            } else {
                $staff = other_staffs::findOrFail($decoded[0]);
            }

            if(! $staff) {
                Alert()->toast('failed to get staff information', 'error');
                return back();
            }

            $staff->update([
                'status' => $request->input('status', 0)
            ]);

            Alert()->toast('Staff has been blocked successfully', 'success');
            return back();
        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    public function unblockStatus($type, $id, Request $request)
    {
        $decoded = Hashids::decode($id);

        try {
            if($type == 'driver') {
                $staff = Transport::findOrFail($decoded[0]);
            } else {
                $staff = other_staffs::findOrFail($decoded[0]);
            }

            if(! $staff) {
                Alert()->toast('failed to get staff information', 'error');
                return back();
            }

            $staff->update([
                'status' => $request->input('status', 1)
            ]);

            Alert()->toast('Staff has been unblocked successfully', 'success');
            return back();
        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    public function removeStaff(Request $request, $type, $id)
    {
        $decoded = Hashids::decode($id);

        try {
            if($type == 'driver') {
                $staff = Transport::findOrFail($decoded[0]);
            } else {
                $staff = Other_staffs::findOrFail($decoded[0]);
            }

            if($staff->status == 1) {
                Alert()->toast('Cannot delete active member, please block first', 'info');
                return back();
            }

            if($staff->bus_no != null) {
                Alert()->toast('This driver cannot be deleted because has a bus to drive', 'info');
                return back();
            }
        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    public function exportStaffReport(Request $request, $format)
    {
        $user = Auth::user();
        $school = school::findOrFail($user->school_id);
        $drivers = Transport::all();
        $otherStaffs = other_staffs::all();

        $combinedStaffs = $otherStaffs->concat($drivers)->sortBy('first_name');

        switch($format) {
            case 'pdf':
                return $this->generatePDF($user, $school, $combinedStaffs);
            case 'excel':
                return $this->generateExcel($user, $school, $combinedStaffs);
            default:
                Alert()->toast('Invalid file format has been detected, please try again', 'error');
                return back();
        }
    }

    protected function generatePDF($user, $school, $combinedStaffs)
    {
        $pdf = new Dompdf();
        $html = view('OtherStaffs.export_pdf', compact('user', 'school', 'combinedStaffs'));
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        // Hii itastream PDF kwenye browser badala ya kudownload
        return $pdf->stream('Report.pdf', ['Attachment' => true]);
    }

    protected function generateExcel($user, $school, $combinedStaffs)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Non-Teaching Staff Members');

        // =========================
        //  SCHOOL LOGO
        // =========================
        $logoRow = 1;
        if ($school->logo && file_exists(storage_path('app/public/logo/' . $school->logo))) {
            try {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath(storage_path('app/public/logo/' . $school->logo));
                $drawing->setHeight(60);
                $drawing->setCoordinates('A1');
                $drawing->setWorksheet($sheet);
                $logoRow = 2; // adjust position if logo is displayed
            } catch (\Exception $e) {
                $logoRow = 1; // fallback if logo fails
            }
        }

        // =========================
        //  SCHOOL NAME & ADDRESS
        // =========================
        $sheet->mergeCells("A{$logoRow}:J{$logoRow}");
        $sheet->setCellValue("A{$logoRow}", strtoupper($school->school_name));
        $sheet->getStyle("A{$logoRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $addressRow = $logoRow + 1;
        $sheet->mergeCells("A{$addressRow}:J{$addressRow}");
        $sheet->setCellValue("A{$addressRow}", ucwords(strtolower($school->postal_address)) . ', ' .
            ucwords(strtolower($school->postal_name)) . ' - ' .
            ucwords(strtolower($school->country)));
        $sheet->getStyle("A{$addressRow}")->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '7F8C8D']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // =========================
        //  REPORT TITLE
        // =========================
        $titleRow = $addressRow + 1;
        $sheet->mergeCells("A{$titleRow}:J{$titleRow}");
        $sheet->setCellValue("A{$titleRow}", "NON-TEACHING STAFF MEMBERS REPORT");
        $sheet->getStyle("A{$titleRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $startRow = $titleRow + 2;

        // =========================
        //  TABLE HEADER - 10 COLUMNS
        // =========================
        $headers = ['#', 'NIN', 'Full Name', 'Gender', 'Phone', 'Email', 'Job Title', 'DoB', 'Address', 'Status'];
        $sheet->fromArray($headers, null, 'A' . $startRow, true);

        $headerRow = $startRow;
        $dataStartRow = $headerRow + 1;

        // FIX 1: Badilisha I kuwa J (10 columns badala ya 9)
        $sheet->getStyle("A{$headerRow}:J{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '34495E']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '2C3E50']
                ]
            ],
        ]);

        // =========================
        //  DATA ROWS
        // =========================
        $dataArray = [];
        foreach ($combinedStaffs as $index => $data) {
            $dataArray[] = [
                $index + 1,
                $data->nida ?? 'N/A',
                isset($data->driver_name)
                    ? $data->driver_name
                    : (($data->first_name ?? '') . ' ' . ($data->last_name ?? '')),
                strtoupper($data->gender ?? ''),
                $data->phone ?? 'N/A',
                $data->email ?? 'N/A',
                $data->job_title ?? 'N/A',
                !empty($data->date_of_birth)
                    ? \Carbon\Carbon::parse($data->date_of_birth)->format('d-m-Y')
                    : 'N/A',
                $data->street_address ?? 'N/A',
                $data->status == 1 ? 'Active' : 'Inactive',
            ];
        }

        if (!empty($dataArray)) {
            $sheet->fromArray($dataArray, null, 'A' . $dataStartRow, true);
        }

        $lastDataRow = $dataStartRow + count($dataArray) - 1;

        // =========================
        //  STYLING DATA ROWS
        // =========================
        if ($lastDataRow >= $dataStartRow) {
            for ($row = $dataStartRow; $row <= $lastDataRow; $row++) {
                $fillColor = $row % 2 == 0 ? 'FFFFFF' : 'F8F9FA';
                // FIX 2: Badilisha I kuwa J
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $fillColor]
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'DDDDDD']
                        ]
                    ]
                ]);
            }

            // Alignment - Adjust for all columns
            $sheet->getStyle("A{$dataStartRow}:A{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("B{$dataStartRow}:B{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle("C{$dataStartRow}:C{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // FIX 3: Address column is now J (Status moved from I to J)
            // Add alignment for other columns as needed
            $sheet->getStyle("D{$dataStartRow}:D{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("E{$dataStartRow}:E{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle("F{$dataStartRow}:F{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle("G{$dataStartRow}:G{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("H{$dataStartRow}:H{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("I{$dataStartRow}:I{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT); // Address

            $sheet->getStyle("J{$dataStartRow}:J{$lastDataRow}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Status

            // Status column coloring - FIX 4: Now column J
            for ($row = $dataStartRow; $row <= $lastDataRow; $row++) {
                $status = strtolower(trim($sheet->getCell("J{$row}")->getValue() ?? '')); // Changed from I to J
                $statusColor = match (true) {
                    in_array($status, ['active', 'approved', 'success', 'completed']) => '27AE60', // green
                    in_array($status, ['pending', 'processing']) => 'F39C12', // orange
                    in_array($status, ['inactive', 'blocked', 'failed', 'rejected']) => 'E74C3C', // red
                    default => '000000'
                };

                $sheet->getStyle("J{$row}")->applyFromArray([ // Changed from I to J
                    'font' => ['bold' => true, 'color' => ['rgb' => $statusColor]]
                ]);
            }

            // Outline border - FIX 5: Badilisha I kuwa J
            $sheet->getStyle("A{$headerRow}:J{$lastDataRow}")->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '2C3E50']
                    ]
                ]
            ]);
        }

        // =========================
        //  COLUMN WIDTHS - FIX 6: Add column J
        // =========================
        $sheet->getColumnDimension('A')->setWidth(6);    // #
        $sheet->getColumnDimension('B')->setWidth(20);   // NIN
        $sheet->getColumnDimension('C')->setWidth(25);   // Full Name
        $sheet->getColumnDimension('D')->setWidth(10);   // Gender
        $sheet->getColumnDimension('E')->setWidth(15);   // Phone
        $sheet->getColumnDimension('F')->setWidth(25);   // Email
        $sheet->getColumnDimension('G')->setWidth(18);   // Job Title
        $sheet->getColumnDimension('H')->setWidth(14);   // DoB
        $sheet->getColumnDimension('I')->setWidth(20);   // Address
        $sheet->getColumnDimension('J')->setWidth(12);   // Status - ADD THIS LINE

        // Freeze header row
        $sheet->freezePane('A' . $dataStartRow);

        // =========================
        //  OUTPUT FILE
        // =========================
        $filename = 'Non_Teaching_Staff_Report_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }


    protected function getStaffId()
    {
        $user = Auth::user();
        $schoolData = school::find($user->school_id);

        do {
            // Tengeneza namba ya ID ya staff (4 digits)
            $staffIdNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $fullStaffId = $schoolData->abbriv_code . '-' . $staffIdNumber;

            // Check kama ID ipo kwenye tables zote
            $existsInOtherStaffs = other_staffs::where('staff_id', $fullStaffId)->exists();
            $existsInDrivers     = Transport::where('staff_id', $fullStaffId)->exists();
            $existsInTeachers    = Teacher::where('member_id', $fullStaffId)->exists();

        } while ($existsInOtherStaffs || $existsInDrivers || $existsInTeachers);

        return $fullStaffId;
    }

    private function scanFileForViruses($file): array
    {
        // For production, use actual API
        if (app()->environment('production')) {
            $apiKey = config('services.virustotal.key');
            try {
                $response = Http::withHeaders(['x-apikey' => $apiKey])
                            ->attach('file', fopen($file->path(), 'r'))
                            ->post('https://www.virustotal.com/api/v3/files');

                if ($response->successful()) {
                    $scanId = $response->json()['data']['id'];
                    $analysis = Http::withHeaders(['x-apikey' => $apiKey])
                                ->get("https://www.virustotal.com/api/v3/analyses/{$scanId}");

                    return [
                        'clean' => $analysis->json()['data']['attributes']['stats']['malicious'] === 0,
                        'message' => $analysis->json()['data']['attributes']['status']
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'clean' => false,
                    'message' => 'Scan failed: '.$e->getMessage()
                ];
            }
        }

        // For local development, just mock a successful scan
        return ['clean' => true, 'message' => 'Development mode - scan bypassed'];
    }

    private function handleProfileImageUpload($image, $oldImage = null)
    {
        $directory = 'profile'; // inside storage/app/public/profile
        $imageFile = time() . '_'. uniqid() . '.' . $image->getClientOriginalExtension();

        // Delete old image if it is not one of the default ones
        if ($oldImage && !in_array($oldImage, ['avatar.jpg', 'female-avatar.jpg'])) {
            $oldPath = storage_path("app/public/{$directory}/{$oldImage}");
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Store new image
        $image->storeAs($directory, $imageFile, 'public');

        return $imageFile;
    }
}
