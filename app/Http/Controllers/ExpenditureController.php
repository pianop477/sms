<?php

namespace App\Http\Controllers;

use App\Models\school;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpWord\PhpWord;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class ExpenditureController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();

        try {
            $response = Http::withToken(session('finance_api_token'))->get(config('app.finance_api_base_url'). '/daily-expense', [
                'school_id' => $user->school_id,
            ]);

            if($response->successful()) {
                $data = $response->json();
                $expenses = $data['expenses'];
                $categories = $data['categories'];
            } else {
                Alert()->toast($response['message'] ?? 'Failed to fetch expense bills records', 'error');
            }
        } catch (Throwable $e) {
            Alert()->toast($e->getMessage() ?? 'Connection not established from the server', 'info');
            // Initialize empty variables in case of error
            $expenses = [];
            $categories = [];
        }

        return view('Expenditures.index', compact('expenses', 'categories'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'category' => 'required|integer',
            'date' => 'required|date|date_format:Y-m-d',
            'amount' => 'required|numeric',
            'description' => 'required|string|max:500',
            'payment' => 'required|string',
            'attachment' => ['nullable', 'file', 'mimetypes:application/pdf,image/jpeg,image/png'],
        ]);

        $user = Auth::user();
        $school = school::findOrFail($user->school_id);

        try {
            $http = Http::withToken(session('finance_api_token'));

            // kama kuna attachment, tumia attach
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $http = $http->attach(
                    'attachment',
                    fopen($file->getRealPath(), 'r'),
                    $file->getClientOriginalName()
                );
            }

            // sasa tuma request
            $response = $http->post(
                config('app.finance_api_base_url') . '/daily-expense',
                [
                    'school_code' => $school->abbriv_code,
                    'school_id' => $school->id,
                    'user_id' => $user->id,
                    'category_id' => $request->category,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'expense_date' => $request->date,
                    'payment_mode' => $request->payment,
                ]
            );

            if ($response->successful()) {
                Alert()->toast('Bills has been saved successfully', 'success');
            } else {
                Alert()->toast($response['message'] ?? 'Failed to register bill', 'error');
                // Log::error('Finance API error: ' . $response->body());
            }
        } catch (Throwable $e) {
            Alert()->toast($e->getMessage() ?? 'Connection not established from the server', 'error');
        }

        return back();
    }

    public function cancelBill (Request $request, $bill)
    {
        // dd($request->all());
        $decoded = Hashids::decode($bill);
        $user = Auth::user();

        $validated = $request->validate([
            'cancel_reason' => 'required|string|max:255'
        ]);

        try {
            $response = Http::withToken(session('finance_api_token'))->put(config('app.finance_api_base_url').'/daily-expense/'.$decoded[0], [
                'cancel_reason' => $request->cancel_reason,
            ]);

            if($response->successful()) {
                Alert()->toast('Bill has been cancelled successfully', 'success');
            }

            else {
                Alert()->toast($response['message'] ?? 'Failed to cancel bill', 'error');
                // Log::error("Error body: ". $response->status());
            }

        } catch (Throwable $e) {
            Alert()->toast($e->getMessage() ?? "Connection not established from the server", 'info');
        }

        return redirect()->back();

    }

    public function deleteInactiveBill($bill)
    {
        $decoded = Hashids::decode($bill);

        $user = Auth::user();

        try {

            $response = Http::withToken(session('finance_api_token'))
                        ->delete(config('app.finance_api_base_url'). '/daily-expense/'.$decoded[0]);

            if($response->successful()) {
                Alert()->toast('Bill has been deleted successfully', 'success');
            }
            else {
                Alert()->toast($response['message'] ?? 'Failed to delete Bill', 'error');
                // Log::error("error found ". $response->status());
            }
        } catch(Throwable $e) {
            Alert()->toast($e->getMessage() ?? 'Connection not established from the server', 'info');
        }

        return redirect()->back();
    }

    public function allTractions()
    {
        $user = Auth::user();
        $school_id = $user->school_id;

        if(! $school_id) {
            Alert()->toast('Invalid or missing required parameter');
            return back();
        }

        try {

            $response = Http::withToken(session('finance_api_token'))->get(config('app.finance_api_base_url'). '/all-transactions', [
                'school_id' => $school_id,
            ]);

            if($response->successful()) {
                $data = $response->json();
                $transactions = $data['transactions'];
                $categories = $data['categories'];
                // Log::info('Transactions data: '. print_r($categories, true));
                return view('Expenditures.all-transactions', compact('transactions', 'categories'));
            }
            else {
                Alert()->toast($response['message'] ?? 'Failed to fetch bills records', 'error');
                Log::error("Error code ". $response->status());
                return back();
            }
        } catch (Throwable $e) {
            Alert()->toast($e->getMessage() ?? "Connection not established from the server", "info");
            return back();
        }
    }

    public function exportCustomReport(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
            'status' => 'nullable|string',
            'category' => 'nullable|integer',
            'payment_mode' => 'nullable|string',
            'export_format' => 'required|string|in:pdf,excel,csv,word',
        ]);

        $user = Auth::user();
        $school_id = $user->school_id;
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $status = $request->input('status');
        $category = $request->input('category');
        $payment_mode = $request->input('payment_mode');
        $export_format = $request->input('export_format');

        try {
            $response = Http::withToken(session('finance_api_token'))->get(config('app.finance_api_base_url'). '/generate-custom-report', [
                'school_id' => $school_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'category' => $category,
                'payment_mode' => $payment_mode,
            ]);

            if($response->successful()) {
                $data = $response->json();

                // Check if transactions exist
                if (empty($data['transactions'])) {
                    if ($request->ajax()) {
                        return response()->json([
                            'error' => 'No bills found for the selected criteria.'
                        ], 404);
                    }
                    Alert()->toast('No bills found for the selected criteria', 'info');
                    return back();
                }

                $transactions = $data['transactions'];
                $total_amount = collect($transactions)->sum('amount');

                // Use default filename for all export formats
                // $filename = 'transaction_report';

                switch ($export_format) {
                    case 'pdf':
                        return $this->generatePDF($transactions, $start_date, $end_date, $school_id, $total_amount);
                    case 'excel':
                        return $this->generateExcel($transactions, $total_amount, $start_date, $end_date, $school_id);
                    case 'csv':
                        return $this->generateCSV($transactions, $total_amount, $school_id, $start_date, $end_date);
                    case 'word':
                        return $this->generateWord($transactions, $total_amount, $school_id, $start_date, $end_date);
                    default:
                        if ($request->ajax()) {
                            return response()->json([
                                'error' => 'Invalid export format selected.'
                            ], 422);
                        }
                        Alert()->toast('Invalid export format selected', 'error');
                        return back();
                }
            }
            else {
                $errorMessage = 'Failed to export bills report. API Error: ' . $response['message'];

                if ($request->ajax()) {
                    return response()->json([
                        'error' => $errorMessage
                    ], $response->status());
                }

                Alert()->toast($errorMessage, 'error');
                Log::error("Error code ". $response->status());
                return back();
            }
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage() ?? "Connection not established from the server";

            if ($request->ajax()) {
                return response()->json([
                    'error' => $errorMessage
                ], 500);
            }

            Alert()->toast($errorMessage, "info");
            return back();
        }
    }

    protected function generatePDF($transactions, $start_date, $end_date, $school_id, $total_amount)
    {
        $school = school::find($school_id);
        $pdf = new Dompdf();
        $html = view('Expenditures.transaction_pdf', compact('transactions', 'start_date', 'end_date', 'school', 'total_amount'))->render();

        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        // Hii itastream PDF kwenye browser badala ya kudownload
        return $pdf->stream('bills_report.pdf', ['Attachment' => true]);
    }


    protected function generateExcel($transactions, $total_amount, $start_date, $end_date, $school_id)
    {
        $school = school::find($school_id);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Financial Report');

        // =========================
        //  PROFESSIONAL HEADER SECTION
        // =========================

        // School Logo (if available) - Row 1
        $logoRow = 1;
        if ($school->logo && file_exists(public_path('assets/img/logo/' . $school->logo))) {
            try {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath(public_path('assets/img/logo/' . $school->logo));
                $drawing->setHeight(60);
                $drawing->setCoordinates('A1');
                $drawing->setWorksheet($sheet);
                $logoRow = 2; // Adjust row if logo is added
            } catch (\Exception $e) {
                // Logo failed to load, continue without it
                $logoRow = 1;
            }
        }

        // School Name - Row 2
        $sheet->mergeCells('A'.$logoRow.':H'.$logoRow);
        $sheet->setCellValue('A'.$logoRow, strtoupper($school->school_name));
        $sheet->getStyle('A'.$logoRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // School Address - Row 3
        $addressRow = $logoRow + 1;
        $sheet->mergeCells('A'.$addressRow.':H'.$addressRow);
        $sheet->setCellValue('A'.$addressRow, ucwords(strtolower($school->postal_address)) . ', ' . ucwords(strtolower($school->postal_name)) . ' - ' . ucwords(strtolower($school->country)));
        $sheet->getStyle('A'.$addressRow)->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '7F8C8D']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Report Title - Row 4
        $titleRow = $addressRow + 1;
        $sheet->mergeCells('A'.$titleRow.':H'.$titleRow);
        $sheet->setCellValue('A'.$titleRow, "FINANCIAL EXPENSE REPORT");
        $sheet->getStyle('A'.$titleRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Report Period - Row 5
        $periodRow = $titleRow + 1;
        $sheet->mergeCells('A'.$periodRow.':H'.$periodRow);
        $sheet->setCellValue('A'.$periodRow, "Reporting Period: " . \Carbon\Carbon::parse($start_date)->format('d M Y') . " - " . \Carbon\Carbon::parse($end_date)->format('d M Y'));
        $sheet->getStyle('A'.$periodRow)->applyFromArray([
            'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '7F8C8D']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Report Summary - Row 6
        $summaryRow = $periodRow + 1;
        $sheet->mergeCells('A'.$summaryRow.':H'.$summaryRow);
        $sheet->setCellValue('A'.$summaryRow, "Total Expenses Bills Count: " . count($transactions) . " | Generated at: " . \Carbon\Carbon::now()->format('d M Y H:i'));
        $sheet->getStyle('A'.$summaryRow)->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '2C3E50']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA']
            ],
            'borders' => [
                'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '3498DB']],
            ],
        ]);

        // Add spacing
        $startRow = $summaryRow + 2;

        // =========================
        //  PROFESSIONAL TABLE HEADER
        // =========================
        $headers = ['#', 'Date', 'Reference No.', 'Category', 'Description', 'Amount', 'Status', 'Payment Mode'];
        $sheet->fromArray($headers, null, 'A' . $startRow, true);

        $headerRow = $startRow;
        $dataStartRow = $headerRow + 1;

        // Professional header styling
        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
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
        //  DATA ROWS WITH PROFESSIONAL STYLING
        // =========================
        $dataArray = [];
        foreach ($transactions as $index => $transaction) {
            $dataArray[] = [
                $index + 1, // Row number
                isset($transaction['transaction_date']) ? \Carbon\Carbon::parse($transaction['transaction_date'])->format('d-m-Y') :
                    (isset($transaction['expense_date']) ? \Carbon\Carbon::parse($transaction['expense_date'])->format('d-m-Y') : ''),
                strtoupper($transaction['reference_number'] ?? ''),
                $transaction['expense_type'] ?? 'N/A',
                $transaction['description'] ?? '',
                $transaction['amount'] ?? 0,
                $transaction['status'] ?? '',
                $transaction['payment_mode'] ?? '',
            ];
        }

        if (!empty($dataArray)) {
            $sheet->fromArray($dataArray, null, 'A' . $dataStartRow, true);
        }

        // Apply professional styling to data rows
        $lastDataRow = $dataStartRow + count($dataArray) - 1;

        if ($lastDataRow >= $dataStartRow) {
            // Alternate row colors for better readability
            for ($row = $dataStartRow; $row <= $lastDataRow; $row++) {
                $fillColor = $row % 2 == 0 ? 'FFFFFF' : 'F8F9FA';
                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
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

            // Format specific columns
            $sheet->getStyle("A{$dataStartRow}:A{$lastDataRow}")->applyFromArray([
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ]);

            $sheet->getStyle("B{$dataStartRow}:B{$lastDataRow}")->applyFromArray([
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ]);

            $sheet->getStyle("F{$dataStartRow}:F{$lastDataRow}")->applyFromArray([
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT],
                'numberFormat' => ['formatCode' => '#,##0.00']
            ]);

            $sheet->getStyle("G{$dataStartRow}:G{$lastDataRow}")->applyFromArray([
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ]);

            // Color code status column
            for ($row = $dataStartRow; $row <= $lastDataRow; $row++) {
                $status = $sheet->getCell("G{$row}")->getValue();
                $status = strtolower(trim($status ?? ''));
                $statusColor = '000000'; // default black

                if (in_array($status, ['completed', 'success', 'active', 'approved'])) {
                    $statusColor = '27AE60'; // green
                } elseif (in_array($status, ['pending', 'processing'])) {
                    $statusColor = 'F39C12'; // orange
                } elseif (in_array($status, ['failed', 'cancelled', 'rejected'])) {
                    $statusColor = 'E74C3C'; // red
                }

                $sheet->getStyle("G{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $statusColor]]
                ]);
            }
        } else {
            $lastDataRow = $dataStartRow - 1; // No data rows
        }

        // =========================
        //  PROFESSIONAL TOTAL ROW
        // =========================
        $totalRow = $lastDataRow + 1;
        $sheet->mergeCells("A{$totalRow}:E{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", "GRAND TOTAL");
        $sheet->setCellValue("F{$totalRow}", $total_amount);
        $sheet->setCellValue("G{$totalRow}", "End of Report");
        $sheet->mergeCells("G{$totalRow}:H{$totalRow}");

        $sheet->getStyle("A{$totalRow}:H{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2C3E50']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '2C3E50']
                ]
            ],
        ]);

        $sheet->getStyle("F{$totalRow}")->applyFromArray([
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0.00']
        ]);

        // =========================
        //  PROFESSIONAL FOOTER
        // =========================
        $footerRow = $totalRow + 2;
        $sheet->mergeCells("A{$footerRow}:H{$footerRow}");
        $sheet->setCellValue("A{$footerRow}",
            strtoupper($school->school_name) . " | " .
            "Computer Generated Financial Report | " .
            "Confidential & Proprietary | " .
            "Generated on " . \Carbon\Carbon::now()->format('F d, Y \\a\\t H:i:s')
        );
        $sheet->getStyle("A{$footerRow}")->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '7F8C8D']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // =========================
        //  FORMATTING & COLUMN WIDTHS
        // =========================

        // Set specific column widths for better layout
        $sheet->getColumnDimension('A')->setWidth(6);  // #
        $sheet->getColumnDimension('B')->setWidth(12); // Date
        $sheet->getColumnDimension('C')->setWidth(16); // Reference
        $sheet->getColumnDimension('D')->setWidth(15); // Category
        $sheet->getColumnDimension('E')->setWidth(30); // Description
        $sheet->getColumnDimension('F')->setWidth(15); // Amount
        $sheet->getColumnDimension('G')->setWidth(12); // Status
        $sheet->getColumnDimension('H')->setWidth(15); // Payment Mode

        // Format amount column
        if ($lastDataRow >= $dataStartRow) {
            $sheet->getStyle("F{$dataStartRow}:F{$lastDataRow}")
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        // Freeze panes for easy scrolling
        $sheet->freezePane('A' . $dataStartRow);

        // =========================
        //  OUTPUT FILE
        // =========================
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'financial_bill_report_' . date('Y_m_d_His') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    protected function generateCSV($transactions, $total_amount, $school_id, $start_date, $end_date)
    {
        $school = school::find($school_id);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Expense Bills Report');

        $row = 1;

        // Report header (school info)
        $sheet->setCellValue("A{$row}", strtoupper($school->school_name)); $row++;
        $sheet->setCellValue("A{$row}", ucwords(strtolower($school->postal_address)). ', '. ucwords(strtolower($school->postal_name)). '-'. ucwords(strtolower($school->country))); $row++;
        $sheet->setCellValue("A{$row}", "FINANCIAL EXPENSE REPORT"); $row++;
        $sheet->setCellValue("A{$row}", "From: " . \Carbon\Carbon::parse($start_date)->format('d M Y') . " To: " . \Carbon\Carbon::parse($end_date)->format('d M Y')); $row++;
        $sheet->setCellValue("A{$row}", "Generated: " . \Carbon\Carbon::now()->format('d M Y H:i')); $row += 2; // space

        // Column headers
        $headers = ['#', 'Date', 'Reference No.', 'Category', 'Description', 'Amount', 'Status', 'Payment Mode'];
        $sheet->fromArray($headers, null, "A{$row}");

        // Style headers
        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F0F0F0']
            ]
        ]);

        $row++;

        // Data rows
        foreach ($transactions as $index => $t) {
            $sheet->fromArray([
                $index + 1,
                isset($t['transaction_date']) ? \Carbon\Carbon::parse($t['transaction_date'])->format('d-m-Y') :
                    (isset($t['expense_date']) ? \Carbon\Carbon::parse($t['expense_date'])->format('d-m-Y') : ''),
                strtoupper($t['reference_number'] ?? ''),
                ucwords(strtolower($t['expense_type'] ?? '')),
                ucwords(strtolower($t['description'] ?? '')),
                $t['amount'] ?? 0,
                ucwords(strtolower($t['status'] ?? '')),
                ucwords(strtolower($t['payment_mode'] ?? '')),
            ], null, "A{$row}");
            $row++;
        }

        // Total amount row
        $sheet->setCellValue("E{$row}", "GRAND TOTAL:");
        $sheet->setCellValue("F{$row}", $total_amount);
        $sheet->getStyle("E{$row}:F{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8E8E8']
            ]
        ]);

        // Auto-size columns for better CSV output
        foreach(range('A','H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Save CSV
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->setSheetIndex(0);

        $filename = 'financial_bills_report_' . date('Y_m_d_His') . '.csv';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    protected function generateWord($transactions, $total_amount, $school_id, $start_date, $end_date, $filename = 'transaction_report')
    {
        try {
            $school = school::find($school_id);

            // Create simple PHPWord instance
            $phpWord = new \PhpOffice\PhpWord\PhpWord();

            // Set basic document properties
            $phpWord->getDocInfo()->setCreator($school->school_name)
                    ->setCompany($school->school_name)
                    ->setTitle('Expense Bills Report')
                    ->setDescription('Generated financial Expense report');

            // Add simple section
            $section = $phpWord->addSection();

            // ===== SIMPLE HEADER =====
            $section->addText(
                strtoupper($school->school_name),
                ['bold' => true, 'size' => 16, 'name' => 'Arial'],
                ['alignment' => 'center']
            );

            $section->addText(
                'Expense Report',
                ['bold' => true, 'size' => 14, 'name' => 'Arial'],
                ['alignment' => 'center']
            );

            $section->addText(
                'Period: ' . \Carbon\Carbon::parse($start_date)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($end_date)->format('d M Y'),
                ['size' => 11, 'name' => 'Arial'],
                ['alignment' => 'center']
            );

            $section->addText(
                'Generated: ' . \Carbon\Carbon::now()->format('d M Y H:i'),
                ['size' => 10, 'name' => 'Arial'],
                ['alignment' => 'center']
            );

            $section->addTextBreak(2);

            // ===== SIMPLE TABLE =====
            $table = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
            ]);

            // Table headers
            $headers = ['#', 'Date', 'Reference', 'Category', 'Description', 'Amount', 'Status', 'Payment Mode'];
            $table->addRow();
            foreach ($headers as $header) {
                $table->addCell(1500)->addText($header, ['bold' => true, 'name' => 'Arial']);
            }

            // Table data
            foreach ($transactions as $index => $transaction) {
                $table->addRow();
                $table->addCell(500)->addText($index + 1, null, ['name' => 'Arial']);

                $date = isset($transaction['transaction_date']) ?
                    \Carbon\Carbon::parse($transaction['transaction_date'])->format('d-m-Y') :
                    (isset($transaction['expense_date']) ?
                    \Carbon\Carbon::parse($transaction['expense_date'])->format('d-m-Y') : 'N/A');
                $table->addCell(1200)->addText($date, null, ['name' => 'Arial']);

                $table->addCell(1500)->addText($transaction['reference_number'] ?? 'N/A', null, ['name' => 'Arial']);
                $table->addCell(1500)->addText($transaction['expense_type'] ?? 'N/A', null, ['name' => 'Arial']);
                $table->addCell(1500)->addText($transaction['description'] ?? 'N/A', null, ['name' => 'Arial']);
                $table->addCell(1600)->addText(number_format($transaction['amount'] ?? 0, 2), null, ['name' => 'Arial']);
                $table->addCell(1000)->addText($transaction['status'] ?? 'N/A', null, ['name' => 'Arial']);
                $table->addCell(1000)->addText($transaction['payment_mode'] ?? 'N/A', null, ['name' => 'Arial']);
            }

            // Total row
            $table->addRow();
            $table->addCell(500)->addText('');
            $table->addCell(1200)->addText('');
            $table->addCell(1500)->addText('');
            $table->addCell(1500)->addText('');
            $table->addCell(1600)->addText('TOTAL:', ['bold' => true, 'name' => 'Arial']);
            $table->addCell(1200)->addText(number_format($total_amount, 2), ['bold' => true, 'name' => 'Arial']);
            $table->addCell(1500)->addText('');
            $table->addCell(1000)->addText('');

            $section->addTextBreak(2);
            $section->addText(
                'Computer Generated Report - ' . $school->school_name,
                ['italic' => true, 'size' => 9, 'name' => 'Arial'],
                ['alignment' => 'center']
            );

            // ===== SAVE FILE =====
            $filename = $filename . '.docx';
            $tempFile = tempnam(sys_get_temp_dir(), 'word_') . '.docx';

            // Use Word2007 writer
            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);

            // Verify file
            if (!file_exists($tempFile) || filesize($tempFile) === 0) {
                throw new \Exception('Generated Word file is empty or corrupted');
            }

            // \Log::info('Word file generated successfully: ' . $tempFile . ' Size: ' . filesize($tempFile));

            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // \Log::error('Word generation error: ' . $e->getMessage());
            throw new \Exception('Failed to generate Word document: ' . $e->getMessage());
        }
    }

    public function getTransaction($id, Request $request)
    {
        $user = Auth::user();

        try {
            $response = Http::withToken(session('finance_api_token'))->get(
                config('app.finance_api_base_url'). '/daily-expense/' . $id,
                ['school_id' => $user->school_id]
            );

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data)) {
                    $transaction = $data['expense'];
                    $categories = $data['categories'];

                    return response()->json([
                        'success' => true,
                        'transaction' => $transaction,
                        'categories' => $categories
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'error' => 'No expense bills were found'
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch expense bills data'
            ], $response->status());

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'Failed to fetch expense bills details',
            ], 500);
        }
    }


    public function updateTransaction ($transactionId, Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|integer',
            'date' => 'required|date|date_format:Y-m-d',
            'amount' => 'required|numeric',
            'description' => 'required|string|max:500',
            'payment' => 'required|string',
            'attachment' => ['nullable', 'file', 'mimetypes:application/pdf,image/jpeg,image/png'],
        ]);

        $user = Auth::user();

        try {
            $http = Http::withToken(session('finance_api_token'));

            // kama kuna attachment, tumia attach
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $http = $http->attach(
                    'attachment',
                    fopen($file->getRealPath(), 'r'),
                    $file->getClientOriginalName()
                );
            }

            // sasa tuma request
            $response = $http->put(
                config('app.finance_api_base_url') . '/daily-expense/update/'. $transactionId,
                [
                    'school_id' => $user->school_id,
                    'user_id' => $user->id,
                    'category_id' => $request->category,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'expense_date' => $request->date,
                    'payment_mode' => $request->payment,
                    'status' => $request->status,
                ]
            );

            if ($response->successful()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Expense bill updated successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $response->json()['message'] ?? 'Failed to update expense bill'
                ], $response->status());
            }
        } catch (Throwable $e) {
            // Alert()->toast($e->getMessage() ?? 'Connection not established from the server', 'error');
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function groupedTransactions ()
    {
        $user = Auth::user();
        $school_id = $user->school_id;

        if(! $school_id) {
            Alert()->toast('Invalid or missing required parameter');
            return back();
        }

        try {

            $response = Http::withToken(session('finance_api_token'))->get(config('app.finance_api_base_url'). '/other/transactions/grouped', [
                'school_id' => $school_id,
            ]);

            if($response->successful()) {
                $data = $response->json();
                $groupedData = $data['data'];
                // Log::info('Transactions data: '. print_r($categories, true));
                return view('Expenditures.grouped_expense', compact('groupedData'));
            }
            else {
                Alert()->toast($response['message'] ?? 'Failed to fetch bills records', 'error');
                Log::error("Error code ". $response->status());
                return back();
            }
        } catch (Throwable $e) {
            Alert()->toast($e->getMessage() ?? "Connection not established from the server", "info");
            return back();
        }
    }
    public function all($year)
    {
        $user = Auth::user();
        $school_id = $user->school_id;

        if(! $school_id) {
            Alert()->toast('Invalid or missing required parameter');
            return back();
        }

        try {

            $response = Http::withToken(session('finance_api_token'))->get(config('app.finance_api_base_url'). '/other/transactions/year/'. $year, [
                'school_id' => $school_id,
            ]);

            if($response->successful()) {
                $data = $response->json();
                $transactions = $data['transactions'];
                $categories = $data['categories'];
                $year = $data['year'];
                // Log::info('Transactions data: '. print_r($categories, true));
                return view('Expenditures.previous_transactions', compact('transactions', 'categories', 'year'));
            }
            else {
                Alert()->toast($response['message'] ?? 'Failed to fetch bills records', 'error');
                Log::error("Error code ". $response->status());
                return back();
            }
        } catch (Throwable $e) {
            Alert()->toast($e->getMessage() ?? "Connection not established from the server", "info");
            return back();
        }
    }

}
