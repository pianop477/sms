<?php

namespace App\Http\Controllers;

use App\Exports\BatchBillsExport;
use App\Models\payment_service;
use App\Models\school;
use App\Models\school_fees;
use App\Models\school_fees_batches;
use App\Models\school_fees_payment;
use App\Models\Student;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class paymentBatchController extends Controller
{
    //

    public function index(Request $request)
    {
        $year   = $request->input('year', date('Y'));
        $batch = school_fees_batches::when($year, fn($q) => $q->where('school_fees_batches.year', $year))
                                    ->orderBy('created_at')->get();
        return view('payment_batches.batch_index', compact('batch'));
    }

    // BatchController.php
    public function preview(Request $request)
    {
        try {
            $request->validate([
                'upload_file' => 'required|file|mimes:xlsx,xls|max:5120'
            ]);

            $file = $request->file('upload_file');

            // Load Excel file
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();

            // Extract data
            $data = [];
            $errors = [];
            $headers = [];

            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];
                foreach ($cellIterator as $cellIndex => $cell) {
                    $value = $cell->getCalculatedValue();

                    // Convert Excel dates to proper format
                    if (Date::isDateTime($cell)) {
                        $value = Date::excelToDateTimeObject($value)->format('m/d/Y');
                    }

                    $rowData[] = $value;
                }

                if ($rowIndex === 1) {
                    $headers = $rowData;
                    continue;
                }

                // Skip empty rows
                if (empty(array_filter($rowData))) {
                    continue;
                }

                // Create associative array
                $assocData = [];
                foreach ($headers as $index => $header) {
                    $assocData[$header] = $rowData[$index] ?? null;
                }

                $data[] = $assocData;

                // Validate bill row data
                $rowErrors = $this->validateBillRowData($assocData, $rowIndex);
                $errors = array_merge($errors, $rowErrors);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'errors' => $errors,
                'total_records' => count($data)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function validateBillRowData($rowData, $rowIndex)
    {
        $errors = [];
        $requiredColumns = [
            'ADMISSION_NUMBER' => 'Admission Number',
            'ACADEMIC_YEAR' => 'Academic Year',
            'SERVICE_NAME' => 'Service Name',
            'AMOUNT' => 'Amount',
            // 'DUE_DATE' => 'Due Date'
        ];

        // Check required columns
        foreach ($requiredColumns as $column => $label) {
            if (empty($rowData[$column])) {
                $errors[] = [
                    'row' => $rowIndex,
                    'type' => 'error',
                    'message' => "{$label} is required (Row {$rowIndex})"
                ];
            }
        }

        // Validate admission number format and existence
        if (!empty($rowData['ADMISSION_NUMBER'])) {

            // normalize admission (trim, remove spaces, lowercase)
            $admission = trim($rowData['ADMISSION_NUMBER']);

            // check student
            $student = Student::whereRaw('LOWER(admission_number) = ?', strtolower($admission))
                            ->where('school_id', auth()->user()->school_id)
                            ->first();

            if (!$student) {
                $errors[] = [
                    'row' => $rowIndex,
                    'type' => 'error',
                    'message' => "Student with admission number {$rowData['ADMISSION_NUMBER']} not found (Row {$rowIndex})"
                ];
            }
        }


        // Validate academic year
        if (!empty($rowData['ACADEMIC_YEAR']) && (!is_numeric($rowData['ACADEMIC_YEAR']) || $rowData['ACADEMIC_YEAR'] < 2020 || $rowData['ACADEMIC_YEAR'] > 2030)) {
            $errors[] = [
                'row' => $rowIndex,
                'type' => 'error',
                'message' => "Invalid academic year: {$rowData['ACADEMIC_YEAR']} (Row {$rowIndex})"
            ];
        }

        // Validate service existence
        if (!empty($rowData['SERVICE_NAME'])) {
            $service = payment_service::where('service_name', $rowData['SERVICE_NAME'])
                                ->where('status', 'active')
                                ->first();

            if (!$service) {
                $errors[] = [
                    'row' => $rowIndex,
                    'type' => 'error',
                    'message' => "Service '{$rowData['SERVICE_NAME']}' not found or inactive (Row {$rowIndex})"
                ];
            }
        }

        // Validate amount
        if (!empty($rowData['AMOUNT'])) {
            $amount = $rowData['AMOUNT'];
            $cleanAmount = is_string($amount) ? str_replace(',', '', $amount) : $amount;
            if (!is_numeric($cleanAmount) || $cleanAmount <= 0) {
                $errors[] = [
                    'row' => $rowIndex,
                    'type' => 'error',
                    'message' => "Invalid amount: {$amount} (Row {$rowIndex})"
                ];
            }
        }

        // Validate due date
        if (!empty($rowData['DUE_DATE'])) {
            $dueDate = $rowData['DUE_DATE'];

            if (is_numeric($dueDate)) {
                // Excel serial date - valid
                try {
                    $unixTimestamp = ($dueDate - 25569) * 86400;
                    $formattedDate = date('m/d/Y', $unixTimestamp);
                    $rowData['DUE_DATE'] = $formattedDate;
                } catch (\Exception $e) {
                    $errors[] = [
                        'row' => $rowIndex,
                        'type' => 'error',
                        'message' => "Invalid date value: {$dueDate} (Row {$rowIndex})"
                    ];
                }
            } elseif (is_string($dueDate) && !preg_match('/^(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01])\/(19|20)\d{2}$/', $dueDate)) {
                $errors[] = [
                    'row' => $rowIndex,
                    'type' => 'error',
                    'message' => "Invalid due date format. Use mm/dd/yyyy: {$dueDate} (Row {$rowIndex})"
                ];
            }
        }

        // Warning for empty control number
        if (empty($rowData['CONTROL_NUMBER'])) {
            $errors[] = [
                'row' => $rowIndex,
                'type' => 'warning',
                'message' => "Control number is empty, will be auto-generated (Row {$rowIndex})"
            ];
        }

        return $errors;
    }


    public function exportFile()
    {
        if (!Storage::exists('templates/sample bill file.xlsx')) {
            abort(404, 'File not found.');
        }
        return Storage::download('templates/sample bill file.xlsx');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'batch_name' => 'required|string|max:255',
                'batch_number' => 'required|string|max:255',
                'academic_year' => 'required|integer|min:2024',
                'extracted_data' => 'required|json'
            ]);

            $user = auth()->user();
            $schoolId = auth()->user()->school_id;
            $extractedData = json_decode($request->extracted_data, true);

            // 1. Create batch record first
            $batch = school_fees_batches::create([
                'batch_name' => $request->batch_name,
                'batch_number' => $request->batch_number,
                'year' => $request->academic_year,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $successfulInserts = 0;
            $failedInserts = 0;
            $errors = [];

            // 2. Process each bill record
            foreach ($extractedData as $index => $row) {
                try {
                    // Get student data using admission_number
                    $student = Student::where('admission_number', $row['ADMISSION_NUMBER'])
                                    ->where('school_id', $schoolId)
                                    ->first();

                    if (!$student) {
                        $failedInserts++;
                        $errors[] = "Student with admission number {$row['ADMISSION_NUMBER']} not found (Row " . ($index + 2) . ")";
                        continue;
                    }

                    // Get service data using service_name
                    $service = payment_service::where('service_name', $row['SERVICE_NAME'])
                                        ->where('status', 'active')
                                        ->first();

                    if (!$service) {
                        $failedInserts++;
                        $errors[] = "Service '{$row['SERVICE_NAME']}' not found or inactive (Row " . ($index + 2) . ")";
                        continue;
                    }

                    // Handle control number
                    $controlNumber = $this->handleControlNumber($row['CONTROL_NUMBER']);

                    // Handle due date
                    $dueDate = $this->handleDueDate($row['DUE_DATE'], $service->expiry_duration);

                    // Prepare school fees data
                    $schoolFeeData = [
                        'school_id' => $schoolId,
                        'student_id' => $student->id,
                        'batch_id' => $batch->id,
                        'service_id' => $service->id,
                        'class_id' => $student->class_id,
                        'academic_year' => $row['ACADEMIC_YEAR'],
                        'control_number' => $controlNumber,
                        'amount' => $this->cleanAmount($row['AMOUNT']),
                        'due_date' => $dueDate,
                        'status' => 'active',
                        'is_cancelled' => 0,
                        'created_by' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    // Insert into school_fees table
                    school_fees::create($schoolFeeData);
                    $successfulInserts++;

                } catch (\Exception $e) {
                    $failedInserts++;
                    $errors[] = "Error processing row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            // Prepare response message
            $message = "Batch uploaded successfully. {$successfulInserts} records inserted.";
            if ($failedInserts > 0) {
                $message .= " {$failedInserts} records failed.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'batch_id' => $batch->id,
                'successful_inserts' => $successfulInserts,
                'failed_inserts' => $failedInserts,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload batch: ' . $e->getMessage()
            ], 500);
        }
    }

/**
 * Handle control number generation
 */
    private function handleControlNumber($controlNumberFromExcel)
    {
        // If control number is provided in Excel, use it
        if (!empty($controlNumberFromExcel)) {
            return $controlNumberFromExcel;
        }

        // Otherwise generate automatically
        return $this->generateControlNumber();
    }

/**
 * Generate automatic control number
 */
    private function generateControlNumber()
    {
        $prefix = 'SA99406';

        // Fetch last control number
        $last = school_fees::orderBy('id', 'desc')->value('control_number');

        // If none exists
        if (!$last) {
            return $prefix . '6001';
        }

        // Remove prefix safely
        $number = (int) str_replace($prefix, '', $last);

        // Add 1
        $newNumber = $number + 1;

        // Determine padding
        $padLength = max(4, strlen((string)$newNumber));

        return $prefix . str_pad($newNumber, $padLength, '0', STR_PAD_LEFT);
    }

/**
 * Handle due date calculation
 */
    private function handleDueDate($dueDateFromExcel, $expiryDuration)
    {
        // If due date is provided in Excel, use it (convert from mm/dd/yyyy to timestamp)
        if (!empty($dueDateFromExcel)) {
            // Handle both string dates and Excel serial numbers
            if (is_numeric($dueDateFromExcel)) {
                // Convert Excel serial date to timestamp
                $unixTimestamp = ($dueDateFromExcel - 25569) * 86400;
                return date('Y-m-d H:i:s', $unixTimestamp);
            } else {
                // Convert from mm/dd/yyyy to Y-m-d H:i:s
                $date = DateTime::createFromFormat('m/d/Y', $dueDateFromExcel);
                if ($date) {
                    return $date->format('Y-m-d H:i:s');
                }
            }
        }

        // If no due date in Excel, calculate from expiry_duration
        if ($expiryDuration > 0) {
            return now()->addMonths($expiryDuration)->format('Y-m-d H:i:s');
        }

        // Default: 30 days from now
        return now()->addDays(30)->format('Y-m-d H:i:s');
    }

/**
 * Clean amount - remove commas and format for database
 */
    private function cleanAmount($amount)
    {
        if (is_string($amount)) {
            $amount = str_replace(',', '', $amount);
        }

        return (float) $amount;
    }

    public function deleteBatch(Request $request, $batch)
    {
        $decoded = Hashids::decode($batch);

        if (!isset($decoded[0])) {
            Alert()->toast('Invalid batch parameter', 'error');
            return back();
        }

        $batch = school_fees_batches::findOrFail($decoded[0]);

        $billsToDelete = school_fees::where('batch_id', $batch->id)
            ->whereDoesntHave('payments')
            ->get();

        // if ($billsToDelete->isEmpty()) {
        //     Alert()->toast('No bills related to this batch', 'error');
        //     // return back();

        // }

        // delete all eligible bills
        foreach ($billsToDelete as $bill) {
            $bill->delete();
        }

        $batch->delete();

        Alert()->toast('Batch has been deleted successfully', 'success');
        return back();
    }

    public function downloadBatch(Request $request, $batch)
    {
        $decoded = Hashids::decode($batch);
        $user = Auth::user();

        try {

            $batch = school_fees_batches::findOrFail($decoded[0]);

            // Get school
            $school = school::findOrFail($user->school_id);

            // Get bills
            $bills = school_fees::query()
                ->join('students', 'students.id', '=', 'school_fees.student_id')
                ->join('grades', 'grades.id', '=', 'school_fees.class_id')
                ->join('payment_services', 'payment_services.id', '=', 'school_fees.service_id')
                ->select(
                    'school_fees.*',
                    'students.first_name', 'students.middle_name', 'students.last_name',
                    'payment_services.service_name', 'grades.class_code'
                )
                ->where('school_fees.batch_id', $batch->id)
                ->where('school_fees.school_id', $user->school_id)
                ->orderBy('students.first_name')
                ->get();

            // Calculate totals except cancelled
            $totalBilled = $bills->sum('amount');

            // Export
            return FacadesExcel::download(
                new BatchBillsExport($school, $bills, $totalBilled, $batch),
                strtoupper($batch->batch_name) . '.xlsx'
            );

        } catch (\Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }
}
