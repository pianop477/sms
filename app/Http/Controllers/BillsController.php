<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\payment_service;
use App\Models\school;
use App\Models\school_fees;
use App\Models\school_fees_payment;
use App\Models\Student;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class BillsController extends Controller
{
    //

    public function index(Request $request)
    {
        $user = Auth::user();

        $students = Student::where('school_id', $user->school_id)->orderBy('first_name')->get();
        $services = payment_service::orderBy('service_name')->get();

        // Get selected year from session or request
        $currentYear = date('Y');

        // Prioritize: 1. Request parameter, 2. Session, 3. Current year
        if ($request->has('year') && !empty($request->year)) {
            $selectedYear = $request->get('year');
            session(['selected_year' => $selectedYear]); // Store in session
        } else {
            $selectedYear = session('selected_year', $currentYear);
        }

        // Get bills data
        if ($request->ajax()) {
            return $this->getBillsData($request);
        }

        // Load initial data for first page load
        $bills = $this->getBillsData($request, true);

        return view('Bills.index', compact('students', 'services', 'bills', 'selectedYear', 'currentYear'));
    }

    private function getBillsData(Request $request, $returnData = false)
    {
        $user = Auth::user();

        $query = DB::table('school_fees')
            ->join('payment_services', 'payment_services.id', '=', 'school_fees.service_id')
            ->leftJoin('students', 'students.id', '=', 'school_fees.student_id')
            ->leftJoin('grades', 'grades.id', '=', 'students.class_id')
            ->leftJoin('parents', 'parents.id', '=', 'students.parent_id')
            ->leftJoin('users', 'users.id', '=', 'parents.user_id')
            ->select(
                'school_fees.*', 'payment_services.service_name',
                'students.first_name as student_first_name',
                'students.middle_name as student_middle_name',
                'students.last_name as student_last_name',
                'grades.class_code',
                'users.phone as parent_phone',
                DB::raw('(SELECT COALESCE(SUM(amount), 0)
                        FROM school_fees_payments
                        WHERE student_fee_id = school_fees.id) AS total_paid'),
                DB::raw('(SELECT MAX(approved_at)
                        FROM school_fees_payments
                        WHERE student_fee_id = school_fees.id) AS latest_approved_at'),
                DB::raw('(SELECT MAX(updated_at)
                        FROM school_fees_payments
                        WHERE student_fee_id = school_fees.id) AS latest_payment_updated_at'),
                // Add this column for proper sorting
                DB::raw('COALESCE(
                    (SELECT MAX(approved_at) FROM school_fees_payments WHERE student_fee_id = school_fees.id),
                    school_fees.cancelled_at,
                    school_fees.updated_at,
                    school_fees.created_at
                ) AS latest_activity_date')
            )
            ->where('school_fees.school_id', $user->school_id);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('school_fees.control_number', 'LIKE', "%{$searchTerm}%")
                ->orWhere('students.first_name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('students.middle_name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('students.last_name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('grades.class_code', 'LIKE', "%{$searchTerm}%")
                ->orWhere('users.phone', 'LIKE', "%{$searchTerm}%")
                ->orWhere('payment_services.service_name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('school_fees.status', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Year filter functionality - FIXED
        $currentYear = date('Y');

        if ($request->filled('year')) {
            session(['selected_year' => $request->input('year')]);
        }

        $selectedYear = session('selected_year', $currentYear); // FIXED: Read session correctly

        // Apply year filter
        if (!empty($selectedYear)) {
            $query->where('school_fees.academic_year', 'LIKE', "%{$selectedYear}%");
        }

        // Remove the subquery join and use direct ordering instead
        $query->orderBy('latest_activity_date', 'DESC');

        // For getting only the latest 20 records if needed (remove if you want all)
        // $query->limit(20); // Remove this line if you want pagination to work properly

        // Pagination
        $bills = $query->paginate(10);

        // Add parameters to pagination links
        if ($request->has('search')) {
            $bills->appends(['search' => $request->search]);
        }
        if ($request->has('year')) {
            $bills->appends(['year' => $selectedYear]);
        }
        $bills->appends(['ajax' => true]);

        if ($returnData) {
            return $bills;
        }

        try {
            return response()->json([
                'success' => true,
                'html' => view('Bills.partials.bills_table', compact('bills'))->render(),
                'pagination' => view('Bills.partials.pagination', compact('bills'))->render(),
                'total' => $bills->total(),
                'selectedYear' => $selectedYear
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'student_name' => 'required|exists:students,id',
            'service' => 'required|exists:payment_services,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date_format:Y-m-d',
            'academic_year' => 'required|date_format:Y',
            'control_number' => 'nullable|string|max:255',
            'school_id' => 'exists:schools,id',
            'class_id' => 'exists:grades,id',
        ]);

        try {
            $user     = Auth::user();
            $student  = Student::find($request->input('student_name'));
            $services = payment_service::find($request->input('service'));

            // Generate due date automatically
            $due_date = Carbon::now()->addMonth($services->expiry_duration)->format('Y-m-d H:i:s');

            // Generate control number if not provided
            $controlNumber = $request->input('control_number')
                ? $request->input('control_number')
                : $this->generateControlNumber();

            // Check existing control number status
            $existingBill = school_fees::where('control_number', $controlNumber)
                                ->whereIn('status', ['active', 'full_paid', 'overpaid', 'expired'])
                                ->first();

            if ($existingBill) {
                Alert()->toast(
                    'Control number already exists and has an active or completed bill',
                    'error'
                );
                return back();
            }

            $bills = school_fees::create([
                'student_id'     => $request->input('student_name'),
                'service_id'     => $request->input('service'),
                'amount'         => $request->input('amount'),
                'due_date'       => $due_date,
                'academic_year'  => $request->input('academic_year'),
                'control_number' => $controlNumber,
                'school_id'      => $user->school_id,
                'class_id'       => $student->class_id,
                'created_by'     => $user->id,
            ]);

            Alert()->toast('Bill generated successfully', 'success');
            return back();

        } catch (Exception $e) {
            Alert()->toast('Error '. $e->getMessage(), 'error');
            return back();
        }
    }

    private function generateControlNumber()
    {
        $prefix = 'SA99406'; // Prefix

        // Fetch last
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

    public function feesPayment(Request $request)
    {
        $currentYear = Carbon::now()->year;

        if ($request->filled('year')) {
            session(['selected_year' => $request->year]);
        }

        $selectedYear = session('selected_year', $currentYear);

        $query = school_fees_payment::query()
            ->join('school_fees', 'school_fees.id', '=', 'school_fees_payments.student_fee_id')
            ->leftJoin('students', 'students.id', '=', 'school_fees.student_id')
            ->leftJoin('grades', 'grades.id', '=', 'students.class_id')
            ->leftJoin('parents', 'parents.id', '=', 'students.parent_id')
            ->leftJoin('users as parent_users', 'parent_users.id', '=', 'parents.user_id')
            ->leftJoin('users as approver_users', 'approver_users.id', '=', 'school_fees_payments.approved_by')
            ->select(
                'school_fees_payments.*',
                'school_fees.control_number',
                'school_fees.academic_year',
                'school_fees.student_id',
                'students.first_name as student_first_name',
                'students.middle_name as student_middle_name',
                'students.last_name as student_last_name',
                'grades.class_code',
                'parent_users.phone as parent_phone',
                DB::raw('CONCAT(approver_users.first_name, " ", approver_users.last_name) as approver_name'),
                // Add latest activity columns for sorting
                DB::raw('COALESCE(school_fees_payments.approved_at, school_fees_payments.updated_at, school_fees_payments.created_at) as latest_activity_date')
            );

        // Year filter - FIXED to use selectedYear consistently
        if (!empty($selectedYear)) {
            $query->where('school_fees.academic_year', 'LIKE', "%{$selectedYear}%");
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('school_fees.control_number', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('students.first_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('students.middle_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('students.last_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('grades.class_code', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('parent_users.phone', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('school_fees.academic_year', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('school_fees_payments.amount', 'LIKE', "%{$searchTerm}%")
                    ->orWhere(
                        DB::raw('CONCAT(approver_users.first_name, " ", approver_users.last_name)'),
                        'LIKE',
                        "%{$searchTerm}%"
                    );
            });
        }

        // Remove the subquery join that limits to 20 records
        // Instead, use proper ordering for all records

        // Order by latest activity date (approved_at, then updated_at, then created_at)
        $query->orderBy('latest_activity_date', 'DESC');

        // If you also want secondary sorting, you can add more orderBy clauses
        $query->orderBy('school_fees_payments.id', 'DESC');

        // Pagination
        $transactions = $query->paginate(10);

        // Add parameters to pagination links if needed
        if ($request->has('search')) {
            $transactions->appends(['search' => $request->search]);
        }
        if ($request->has('year')) {
            $transactions->appends(['year' => $selectedYear]);
        }

        // Students with bills
        $studentIds = school_fees::distinct()->pluck('student_id')->toArray();
        $students = Student::whereIn('id', $studentIds)->orderBy('first_name')->get();

        return view('Bills.transaction', compact(
            'transactions',
            'students',
            'selectedYear',
            'currentYear'
        ));
    }

    // ADD THIS NEW METHOD FOR AJAX
    public function getStudentFees($studentId)
    {
        try {
            $fees = school_fees::where('student_id', $studentId)
                    ->select('id', 'control_number', 'academic_year', 'status')
                    ->where('status', 'active')
                    ->get();

            return response()->json([
                'success' => true,
                'fees' => $fees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch student fees'
            ], 500);
        }
    }

    public function recordPayment(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            'student_id' => 'required|exists:students,id',
            'control_number' => 'required|string|exists:school_fees,control_number',
            'payment' => 'required|string|in:bank,mobile,cash',
            'amount' => 'required|numeric',
        ]);

        try {
            // verify control number
            $controlNumber = school_fees::where('control_number', $request->control_number)
                    ->whereIn('status', ['active', 'full paid', 'overpaid'])
                    ->first();

            if (!$controlNumber) {
                Alert()->toast('Control number is either invalid or expired', 'error');
                return back();
            }

            $studentFeeId = $controlNumber->id;

            $latestInstallment = school_fees_payment::where('student_fee_id', $studentFeeId)
                                ->max('installment');

            $installment = $latestInstallment ? $latestInstallment + 1 : 1;

            // CREATE
            $newPayment = school_fees_payment::create([
                'school_id' => $user->school_id,
                'student_id' => $request->student_id,
                'student_fee_id' => $studentFeeId,
                'amount' => $request->amount,
                'payment_mode' => $request->payment,
                'installment' => $installment,
                'approved_by' => $user->id,
                'approved_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            Alert()->toast('Payment has been recorded successfully', 'success');
            return back();
        } catch (Exception $e) {
            Alert()->toast('Error '. $e->getMessage(), 'error');
            return back();
        }
    }

    public function paymentReport(Request $request)
    {
        if ($request->filled('year')) {
            session(['selected_year' => $request->input('year')]);
        }

        $year = session('selected_year', date('Y'));

        $search = $request->input('search');

        $classes = Grade::where('school_id', Auth::user()->school_id)
            ->orderBy('class_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SUBQUERY: latest 20 control numbers (MariaDB safe)
        |--------------------------------------------------------------------------
        */
        $latestControls = DB::table('school_fees_payments')
            ->join('school_fees', 'school_fees.id', '=', 'school_fees_payments.student_fee_id')
            ->select('school_fees.control_number')
            ->when($year, fn ($q) => $q->where('school_fees.academic_year', $year))
            ->when($search, function ($q) use ($search) {
                $q->where('school_fees.control_number', 'LIKE', "%{$search}%");
            })
            ->groupBy('school_fees.control_number')
            ->orderByRaw('MAX(school_fees_payments.approved_at) DESC')
            ->limit(20);

        /*
        |--------------------------------------------------------------------------
        | FETCH RAW PAYMENTS (ONLY latest 20 control numbers)
        |--------------------------------------------------------------------------
        */
        $raw = school_fees_payment::query()
            ->join('school_fees', 'school_fees.id', '=', 'school_fees_payments.student_fee_id')
            ->join('students', 'students.id', '=', 'school_fees_payments.student_id')
            ->join('grades', 'grades.id', '=', 'students.class_id')
            ->joinSub($latestControls, 'latest_controls', function ($join) {
                $join->on('school_fees.control_number', '=', 'latest_controls.control_number');
            })
            ->select(
                'school_fees_payments.*',
                'school_fees.control_number',
                'school_fees.academic_year',
                'school_fees.amount as billed_amount',
                'school_fees.status',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'grades.class_code'
            )
            ->orderBy('school_fees_payments.approved_at', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SUMMARY SECTION (DIRECT FROM DB)
        |--------------------------------------------------------------------------
        */
        $billsQuery = school_fees::query()
            ->when($year, fn ($q) => $q->where('academic_year', $year))
            ->when($search, function ($q) use ($search) {
                $q->where('control_number', 'LIKE', "%{$search}%");
            });

        $totalActiveBills = (clone $billsQuery)
            ->whereIn('status', ['active', 'full paid', 'overpaid'])
            ->sum('amount');

        $totalCancelledBills = (clone $billsQuery)
            ->where('status', 'cancelled')
            ->sum('amount');

        $totalCancelledCount = (clone $billsQuery)
            ->where('status', 'cancelled')
            ->count();

        // Total paid from payments table (already filtered)
        $totalPaid = $raw->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | GROUP BY CONTROL NUMBER
        |--------------------------------------------------------------------------
        */
        $grouped = $raw->groupBy('control_number')->map(function ($rows) {
            $first = $rows->first();
            $latestPayment = $rows->sortByDesc('approved_at')->first();

            return (object)[
                'id' => $first->id,
                'control_number' => $first->control_number,
                'academic_year' => $first->academic_year,
                'status' => $first->status,
                'first_name' => $first->first_name,
                'middle_name' => $first->middle_name,
                'last_name' => $first->last_name,
                'class_code' => $first->class_code,

                'approved_at' => $latestPayment->approved_at,
                'approved_by' => $latestPayment->approved_by,

                // Calculations
                'total_paid' => $rows->sum('amount'),
                'billed_amount' => $first->billed_amount,
                'last_payment_date' => $rows->max('approved_at'),
                'payment_count' => $rows->count(),
            ];
        })->values();

        /*
        |--------------------------------------------------------------------------
        | MANUAL PAGINATION (10 per page → 2 pages)
        |--------------------------------------------------------------------------
        */
        $perPage = 10;
        $page    = LengthAwarePaginator::resolveCurrentPage();
        $total   = $grouped->count();

        $results = $grouped->forPage($page, $perPage);

        $transactions = new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path'  => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $services = payment_service::orderBy('service_name')->get();

        return view(
            'Bills.transaction_report',
            compact(
                'transactions',
                'year',
                'totalActiveBills',
                'totalPaid',
                'totalCancelledBills',
                'totalCancelledCount',
                'services',
                'classes'
            )
        );
    }


   public function viewBill($billId)
    {
        try {
            // Log::info('View Bill Request:', ['billId' => $billId]);

            $decodedBill = Hashids::decode($billId);

            if (empty($decodedBill)) {
                Log::warning('Invalid bill ID format:', ['billId' => $billId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid bill ID format'
                ], 400);
            }

            $billId = $decodedBill[0];
            // Log::info('Decoded Bill ID:', ['decodedId' => $billId]);

            // Get bill basic info
            $bill = school_fees::query()
                                ->join('payment_services', 'payment_services.id', '=', 'school_fees.service_id')
                                ->leftJoin('students', 'students.id', '=', 'school_fees.student_id')
                                ->leftJoin('grades', 'grades.id', '=', 'students.class_id')
                                ->leftJoin('parents', 'parents.id', '=', 'students.parent_id')
                                ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                                ->select(
                                    'school_fees.*',
                                    'payment_services.service_name',
                                    'students.first_name as student_first_name',
                                    'students.middle_name as student_middle_name',
                                    'students.last_name as student_last_name',
                                    'grades.class_code',
                                    'users.phone as parent_phone'
                                )
                                ->find($billId);

            if (!$bill) {
                // Log::warning('Bill not found:', ['billId' => $billId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Bill not found'
                ], 404);
            }

            // Log::info('Bill found:', ['control_number' => $bill->control_number]);

            // Get ALL payment history for this bill
            $paymentHistory = school_fees_payment::query()
                ->where('student_fee_id', $bill->id)
                ->orderBy('installment', 'asc')
                ->orderBy('approved_at', 'asc')
                ->get();

            // Log::info('Payment history count:', ['count' => $paymentHistory->count()]);

            // Calculate totals
            $totalPaid = $paymentHistory->sum('amount');
            $balance = $bill->amount - $totalPaid;

            return response()->json([
                'success' => true,
                'bill' => $bill,
                'payment_history' => $paymentHistory,
                'summary' => [
                    'total_billed' => (float) $bill->amount,
                    'total_paid' => (float) $totalPaid,
                    'balance' => (float) $balance,
                    'payment_count' => $paymentHistory->count()
                ]
            ]);

        } catch (\Exception $e) {
            // Log::error('Error in viewBill: ' . $e->getMessage(), [
            //     'exception' => $e,
            //     'billId' => $billId ?? 'unknown'
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error occurred while loading bill details'
            ], 500);
        }
    }

    public function cancelBill($billId, Request $request)
    {
        $this->validate($request, [
            'reason' => 'required|string|max:500',
        ]);

        try {
            $decodedBill = Hashids::decode($billId);

            if (empty($decodedBill)) {
               Alert()->toast('Invalid bill parameter', 'error');
                return back();
            }

            $billId = $decodedBill[0];

            $bill = school_fees::find($billId);

            if (!$bill) {
                Alert()->toast('Bill not found', 'error');
                return back();
            }

            if ($bill->total_paid > 0) {
                Alert()->toast('Cannot cancel bill with payments made', 'error');
                return back();
            }

            // Proceed to cancel the bill
            $bill->update([
                'is_cancelled' => true,
                'cancel_reason' => $request->reason,
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            Alert()->toast('Bill cancelled successfully', 'success');
            return back();
        } catch (\Exception $e) {
            Alert()->toast('Error '. $e->getMessage(), 'error');
            return back();
        }
    }

    public function resendBill (Request $request, $billId)
    {
        //
        $decodedBill = Hashids::decode($billId);

        // return $decodedBill;
        try {
            $bill = school_fees::query()
                            ->join('students', 'students.id', '=', 'school_fees.student_id')
                            ->join('payment_services', 'payment_services.id', '=', 'school_fees.service_id')
                            ->leftJoin('parents', 'parents.id', '=', 'students.parent_id')
                            ->leftJoin('users', 'users.id', '=', 'parents.user_id')
                            ->select(
                                'school_fees.*',
                                'users.phone as parent_phone', 'payment_services.service_name',
                                DB::raw('(SELECT COALESCE(SUM(amount), 0)
                                    FROM school_fees_payments
                                    WHERE student_fee_id = school_fees.id) AS total_paid'), 'students.first_name', 'students.last_name',
                            )
                            ->find($decodedBill[0]);

            if($bill->status != 'active') {
                Alert()->toast('Invalid Bill or Inactive bill', 'error');
                return back();
            }

            // find important information so as to prepare sms payload
            $controlNumber = strtoupper($bill->control_number);
            $studentName = strtoupper($bill->first_name . ' '. $bill->last_name);
            $paidAmount = (float) $bill->total_paid;
            $billedAmount = (float) $bill->amount;
            $dueDate = Carbon::parse($bill->due_date)->format('d-m-Y');
            $balance = $billedAmount - $paidAmount;
            $destinationPhone = $this->formatPhoneNumber($bill->parent_phone);
            $service = strtoupper($bill->service_name);

            $formattedPaidAmount = number_format($paidAmount);
            $formattedBilledAmount = number_format($billedAmount);
            $formattedBalance = number_format($balance);

            //SMS payload
            $sendBillBySms = new NextSmsService();
            $user = Auth::user();
            $school = school::findOrFail($user->school_id);
            $message = "Habari! Unakumbushwa kulipa {$service} ya {$studentName}, Tsh. {$formattedBalance}. Tumia Control#: {$controlNumber} kulipa kabla ya {$dueDate}. Tafadhali lipa kwa wakati!";

            $senderId = $school->sender_id ?? 'SHULE APP';
            $payload = [
                'from' => $senderId,
                'to' => $destinationPhone,
                'text' => $message,
                'reference' => $controlNumber,
            ];

            Log::info('Sending sms to '. $studentName. ' with phone number '. $payload['to']. ' and message content is '. $payload['text']. ' from '. $payload['from']);

            $response = $sendBillBySms->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

            if(!$response['success']) {
                Alert()->toast('SMS failed: '.$response['error'], 'error');
                return back();
            }

            Alert()->toast('SMS sent successfully', 'success');
            return back();
        }
        catch (Exception $e) {
            Log::error('Error '. $e->getMessage());
            Alert()->toast('Error '. $e->getMessage(), 'error');
            return back();
        }

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

    public function deleteInactiveBill($billId)
    {
        try {
            $decodedBill = Hashids::decode($billId);

            if (empty($decodedBill)) {
               Alert()->toast('Invalid bill parameter', 'error');
                return back();
            }

            $billId = $decodedBill[0];

            $bill = school_fees_payment::find($billId);

            if (!$bill) {
                Alert()->toast('Bill not found', 'error');
                return back();
            }

            // Proceed to delete the bill
            $bill->delete();

            Alert()->toast('Bill has been deleted successfully', 'success');
            return back();
        } catch (\Exception $e) {
            Alert()->toast('Error '. $e->getMessage(), 'error');
            return back();
        }
    }

    public function editBill($billId)
    {
        //
        $decodedBill = Hashids::decode($billId);

        $bill = school_fees_payment::query()
                            ->join('school_fees', 'school_fees.id', '=', 'school_fees_payments.student_fee_id')
                            ->join('students', 'students.id', '=', 'school_fees_payments.student_id')
                            ->leftJoin('grades', 'grades.id', '=', 'students.class_id')
                            ->select(
                                'school_fees_payments.*', 'grades.class_name', 'grades.class_code',
                                'students.first_name', 'students.middle_name', 'students.last_name',
                                'school_fees.control_number', 'school_fees.academic_year'
                            )
                            ->find($decodedBill[0]);

        return view('Bills.edit', compact('bill'));
    }

    public function updateBill (Request $request, $billId)
    {
        $decodedBill = Hashids::decode($billId);

        $this->validate($request, [
            'amount' => 'required|numeric',
            'date' => 'required|date_format:Y-m-d',
            'payment' => 'required|string|in:bank,cash,mobile',
        ]);

        try {
            $bill = school_fees_payment::find($decodedBill[0]);

            if(! $bill) {
                Alert()->toast('Invalid bill or missing parameter');
                return back();
            }

            $bill->update([
                'amount' => $request->amount,
                'approved_at' => Carbon::parse($request->date)->format('Y-m-d H:i:s'),
                'payment_mode' => $request->payment
            ]);

            Alert()->toast('Payment has been updated successfully', 'success');
            return to_route('bills.transactions');
        } catch (Exception $e) {
            Alert()->toast('Error '. $e->getMessage(), 'error');
            return back();
        }
    }

    public function exportBill(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
            'status' => 'nullable|string',
            'service' => 'nullable|integer',
            'class' => 'nullable|integer',
            'export_format' => 'required|string|in:pdf,excel,csv,word',
        ]);

        $user = Auth::user();
        $school_id = $user->school_id;
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $status = $request->input('status');
        $service = $request->input('service');
        $class = $request->input('class');
        $export_format = $request->input('export_format');

        // Query kuboreshwa kwa kuzingatia control number
        $bills = DB::table('school_fees')
            ->join('payment_services', 'payment_services.id', '=', 'school_fees.service_id')
            ->leftJoin('students', 'students.id', '=', 'school_fees.student_id')
            ->leftJoin('grades', 'grades.id', '=', 'students.class_id')
            ->leftJoin('parents', 'parents.id', '=', 'students.parent_id')
            ->leftJoin('users', 'users.id', '=', 'parents.user_id')
            ->select(
                'school_fees.*',
                'payment_services.service_name',
                'students.first_name as student_first_name',
                'students.middle_name as student_middle_name',
                'students.last_name as student_last_name',
                'grades.class_code',
                'users.phone as parent_phone',
                DB::raw('(SELECT COALESCE(SUM(amount), 0)
                        FROM school_fees_payments
                        WHERE student_fee_id = school_fees.id) AS total_paid'),
                DB::raw('(SELECT MAX(approved_at)
                        FROM school_fees_payments
                        WHERE student_fee_id = school_fees.id) AS latest_approved_at'),
                DB::raw('(SELECT MAX(updated_at)
                        FROM school_fees_payments
                        WHERE student_fee_id = school_fees.id) AS latest_payment_updated_at')
            )
            ->where('school_fees.school_id', $school_id)
            ->whereBetween('school_fees.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->when($status, fn($q) => $q->where('school_fees.status', $status))
            ->when($service, fn($q) => $q->where('school_fees.service_id', $service))
            ->when($class, fn($q) => $q->where('school_fees.class_id', $class))
            ->orderBy('students.first_name', 'asc')
            ->get();

        // Calculate balances and prepare data for export
        $exportData = $bills->map(function ($bill) {
            $billed = $bill->amount ?? 0;
            $paid = $bill->total_paid ?? 0;
            $balance = $billed - $paid;

            return [
                'control_number' => $bill->control_number,
                'student_name' => trim(ucwords(strtolower($bill->student_first_name ?? 'N/A')) . ' ' .
                                ucwords(strtolower($bill->student_middle_name ?? '')) . ' ' .
                                ucwords(strtolower($bill->student_last_name ?? ''))),
                'level' => strtoupper($bill->class_code ?? 'N/A'),
                'academic_year' => $bill->academic_year,
                'billed_amount' => $billed,
                'paid_amount' => $paid,
                'balance' => $balance,
                'status' => $bill->status,
                'issued_at' => $bill->created_at,
                'expires_at' => $bill->due_date,
                'service_name' => ucwords(strtolower($bill->service_name))
            ];
        });

        try {
            if($exportData->isEmpty()) {
                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'No bills found for the selected criteria.'
                    ], 404);
                }
                Alert()->toast('No bills found for the selected criteria.', 'error');
                return back();
            }

            $total_billed = $exportData->sum('billed_amount');
            $total_paid = $exportData->sum('paid_amount');
            $total_balance = $exportData->sum('balance');

            switch ($export_format) {
                case 'pdf':
                    return $this->generatePDF($exportData, $start_date, $end_date, $school_id, $total_billed, $total_paid, $total_balance);
                case 'excel':
                    return $this->generateExcel($exportData, $total_billed, $total_paid, $total_balance, $start_date, $end_date, $school_id);
                case 'csv':
                    return $this->generateCSV($exportData, $total_billed, $total_paid, $total_balance, $school_id, $start_date, $end_date);
                case 'word':
                    return $this->generateWord($exportData, $total_billed, $total_paid, $total_balance, $school_id, $start_date, $end_date);
                default:
                    if ($request->ajax()) {
                        return response()->json([
                            'error' => 'Invalid export format selected.'
                        ], 422);
                    }
                    Alert()->toast('Invalid export format selected', 'error');
                    return back();
            }
        } catch (Exception $e) {
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

    protected function generatePDF($bills, $start_date, $end_date, $school_id, $total_billed, $total_paid, $total_balance)
    {
        $school = school::find($school_id);
        $pdf = new Dompdf();
        $html = view('Bills.report_pdf', compact('bills', 'start_date', 'end_date', 'school', 'total_billed', 'total_paid', 'total_balance'))->render();

        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape'); // Landscape for better table view
        $pdf->render();

        return $pdf->stream('Transaction_report.pdf', ['Attachment' => true]);
    }


    protected function generateExcel($bills, $total_billed, $total_paid, $total_balance, $start_date, $end_date, $school_id)
    {
        $school = school::find($school_id);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bills Report');

        // =========================
        //  PROFESSIONAL HEADER SECTION
        // =========================

        // School Logo (if available) - Row 1
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
                $logoRow = 2; // Adjust row if logo is added
            } catch (\Exception $e) {
                // Logo failed to load, continue without it
                $logoRow = 1;
            }
        }

        // School Name - Row 2
        $sheet->mergeCells('A'.$logoRow.':L'.$logoRow);
        $sheet->setCellValue('A'.$logoRow, strtoupper($school->school_name));
        $sheet->getStyle('A'.$logoRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // School Address - Row 3
        $addressRow = $logoRow + 1;
        $sheet->mergeCells('A'.$addressRow.':L'.$addressRow);
        $sheet->setCellValue('A'.$addressRow, ucwords(strtolower($school->postal_address)) . ', ' . ucwords(strtolower($school->postal_name)) . ' - ' . ucwords(strtolower($school->country)));
        $sheet->getStyle('A'.$addressRow)->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '7F8C8D']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Report Title - Row 4
        $titleRow = $addressRow + 1;
        $sheet->mergeCells('A'.$titleRow.':L'.$titleRow);
        $sheet->setCellValue('A'.$titleRow, "BILLS REPORT");
        $sheet->getStyle('A'.$titleRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Report Period - Row 5
        $periodRow = $titleRow + 1;
        $sheet->mergeCells('A'.$periodRow.':L'.$periodRow);
        $sheet->setCellValue('A'.$periodRow, "Reporting Period: " . \Carbon\Carbon::parse($start_date)->format('d M Y') . " - " . \Carbon\Carbon::parse($end_date)->format('d M Y'));
        $sheet->getStyle('A'.$periodRow)->applyFromArray([
            'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '7F8C8D']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Report Summary - Row 6
        $summaryRow = $periodRow + 1;
        $sheet->mergeCells('A'.$summaryRow.':L'.$summaryRow);
        $sheet->setCellValue('A'.$summaryRow,
            "Total Bills: " . count($bills) .
            " | Total Billed: " . number_format($total_billed) .
            " | Total Paid: " . number_format($total_paid) .
            " | Total Balance: " . number_format($total_balance) .
            " | Generated at: " . \Carbon\Carbon::now()->format('d M Y H:i')
        );
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
        $headers = ['#', 'Control #', 'Student Name', 'Level', 'Year', 'Service', 'Billed Amount', 'Paid Amount', 'Balance', 'Status', 'Issued At', 'Expires At'];
        $sheet->fromArray($headers, null, 'A' . $startRow, true);

        $headerRow = $startRow;
        $dataStartRow = $headerRow + 1;

        // Professional header styling
        $sheet->getStyle("A{$headerRow}:L{$headerRow}")->applyFromArray([
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
        foreach ($bills as $index => $bill) {
            $dataArray[] = [
                $index + 1, // Row number
                strtoupper($bill['control_number']),
                $bill['student_name'],
                $bill['level'],
                $bill['academic_year'],
                $bill['service_name'],
                $bill['billed_amount'],
                $bill['paid_amount'],
                $bill['balance'],
                $bill['status'],
                \Carbon\Carbon::parse($bill['issued_at'])->format('d-m-Y'),
                $bill['expires_at'] ? \Carbon\Carbon::parse($bill['expires_at'])->format('d-m-Y') : 'N/A',
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
                $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
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

            // Format amount columns
            $amountColumns = ['G', 'H', 'I']; // Billed, Paid, Balance
            foreach ($amountColumns as $column) {
                $sheet->getStyle("{$column}{$dataStartRow}:{$column}{$lastDataRow}")->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT],
                    'numberFormat' => ['formatCode' => '#,##0']
                ]);
            }

            // Color code status column
            for ($row = $dataStartRow; $row <= $lastDataRow; $row++) {
                $status = $sheet->getCell("J{$row}")->getValue();
                $status = strtolower(trim($status ?? ''));
                $statusColor = '000000'; // default black

                if ($status == 'full paid') {
                    $statusColor = '27AE60'; // green
                } elseif ($status == 'expired') {
                    $statusColor = 'E74C3C'; // red
                } elseif ($status == 'cancelled') {
                    $statusColor = 'F39C12'; // orange
                }
                elseif ($status == 'overpaid') {
                    $statusColor = '8E44AD'; // purple
                }
                 else {
                    $statusColor = '2980B9'; // blue
                }

                $sheet->getStyle("J{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $statusColor]]
                ]);
            }

            // Center align status and date columns
            $centerColumns = ['J', 'K', 'L']; // Status, Issued At, Expires At
            foreach ($centerColumns as $column) {
                $sheet->getStyle("{$column}{$dataStartRow}:{$column}{$lastDataRow}")->applyFromArray([
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
                ]);
            }
        } else {
            $lastDataRow = $dataStartRow - 1; // No data rows
        }

        // =========================
        //  PROFESSIONAL TOTAL ROW
        // =========================
        $totalRow = $lastDataRow + 1;
        $sheet->mergeCells("A{$totalRow}:F{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", "GRAND TOTALS");
        $sheet->setCellValue("G{$totalRow}", $total_billed);
        $sheet->setCellValue("H{$totalRow}", $total_paid);
        $sheet->setCellValue("I{$totalRow}", $total_balance);
        $sheet->setCellValue("J{$totalRow}", "End of Report");
        $sheet->mergeCells("J{$totalRow}:L{$totalRow}");

        $sheet->getStyle("A{$totalRow}:L{$totalRow}")->applyFromArray([
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

        // Format total amounts
        $sheet->getStyle("G{$totalRow}:I{$totalRow}")->applyFromArray([
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0']
        ]);

        // =========================
        //  PROFESSIONAL FOOTER
        // =========================
        $footerRow = $totalRow + 2;
        $sheet->mergeCells("A{$footerRow}:L{$footerRow}");
        $sheet->setCellValue("A{$footerRow}",
            strtoupper($school->school_name) . " | " .
            "Computer Generated Bills Report | " .
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
        $sheet->getColumnDimension('B')->setWidth(15); // Control #
        $sheet->getColumnDimension('C')->setWidth(25); // Student Name
        $sheet->getColumnDimension('D')->setWidth(12); // Level
        $sheet->getColumnDimension('E')->setWidth(10); // Year
        $sheet->getColumnDimension('F')->setWidth(20); // Service
        $sheet->getColumnDimension('G')->setWidth(15); // Billed Amount
        $sheet->getColumnDimension('H')->setWidth(15); // Paid Amount
        $sheet->getColumnDimension('I')->setWidth(15); // Balance
        $sheet->getColumnDimension('J')->setWidth(12); // Status
        $sheet->getColumnDimension('K')->setWidth(12); // Issued At
        $sheet->getColumnDimension('L')->setWidth(12); // Expires At

        // Format amount columns
        if ($lastDataRow >= $dataStartRow) {
            $amountColumns = ['G', 'H', 'I'];
            foreach ($amountColumns as $column) {
                $sheet->getStyle("{$column}{$dataStartRow}:{$column}{$lastDataRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');
            }
        }

        // Freeze panes for easy scrolling
        $sheet->freezePane('A' . $dataStartRow);

        // =========================
        //  OUTPUT FILE
        // =========================
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'bills_report_' . $start_date . '_to_' . $end_date . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    protected function generateCSV($bills, $total_billed, $total_paid, $total_balance, $school_id, $start_date, $end_date)
    {
        $school = school::find($school_id);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bills Report');

        $row = 1;

        // Report header
        $sheet->setCellValue("A{$row}", strtoupper($school->school_name)); $row++;
        $sheet->setCellValue("A{$row}", ucwords(strtolower($school->postal_address)) . ', ' . ucwords(strtolower($school->postal_name)) . ' - ' . ucwords(strtolower($school->country))); $row++;
        $sheet->setCellValue("A{$row}", "BILLS REPORT"); $row++;
        $sheet->setCellValue("A{$row}", "Reporting Period: " . \Carbon\Carbon::parse($start_date)->format('d M Y') . " - " . \Carbon\Carbon::parse($end_date)->format('d M Y')); $row++;
        $sheet->setCellValue("A{$row}",
            "Total Bills: " . count($bills) .
            " | Total Billed: " . number_format($total_billed) .
            " | Total Paid: " . number_format($total_paid) .
            " | Total Balance: " . number_format($total_balance)
        ); $row++;
        $sheet->setCellValue("A{$row}", "Generated: " . \Carbon\Carbon::now()->format('d M Y H:i')); $row += 2;

        // Column headers
        $headers = ['#', 'Control #', 'Student Name', 'Level', 'Year', 'Service', 'Billed Amount', 'Paid Amount', 'Balance', 'Status', 'Issued At', 'Expires At'];
        $sheet->fromArray($headers, null, "A{$row}");

        // Style headers
        $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '34495E']
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF']
            ]
        ]);

        $row++;

        // Data rows
        foreach ($bills as $index => $bill) {
            $sheet->fromArray([
                $index + 1,
                strtoupper($bill['control_number']),
                $bill['student_name'],
                $bill['level'],
                $bill['academic_year'],
                $bill['service_name'],
                $bill['billed_amount'],
                $bill['paid_amount'],
                $bill['balance'],
                $bill['status'],
                \Carbon\Carbon::parse($bill['issued_at'])->format('d-m-Y'),
                $bill['expires_at'] ? \Carbon\Carbon::parse($bill['expires_at'])->format('d-m-Y') : 'N/A',
            ], null, "A{$row}");
            $row++;
        }

        // Total row
        $sheet->setCellValue("A{$row}", "GRAND TOTALS");
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("G{$row}", $total_billed);
        $sheet->setCellValue("H{$row}", $total_paid);
        $sheet->setCellValue("I{$row}", $total_balance);
        $sheet->setCellValue("J{$row}", "End of Report");
        $sheet->mergeCells("J{$row}:L{$row}");

        $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2C3E50']
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF']
            ]
        ]);

        // Auto-size columns
        foreach(range('A','L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Format amount columns
        $amountColumns = ['G', 'H', 'I'];
        foreach ($amountColumns as $column) {
            $sheet->getStyle("{$column}2:{$column}{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');
        }

        // Save CSV
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->setSheetIndex(0);

        $filename = 'bills_report_' . $start_date . '_to_' . $end_date . '.csv';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    public function parentPaymentHistory($student, Request $request)
    {
        $decoded = Hashids::decode($student);
        $user = Auth::user();
        $students = Student::find($decoded[0]);
        $currentYear = date('Y');

        if ($request->filled('year')) {
            session(['selected_year' => $request->year]);
        }

        $selectedYear = session('selected_year', $currentYear);

        // Get bills for the selected student
        $billsQuery = DB::table('school_fees')
            ->join('payment_services', 'payment_services.id', '=', 'school_fees.service_id')
            ->select(
                'school_fees.id',
                'school_fees.control_number',
                'school_fees.academic_year',
                'school_fees.amount as billed_amount',
                'school_fees.due_date',
                'school_fees.status',
                'school_fees.created_at',
                'payment_services.service_name'
            )
            ->where('school_fees.student_id', $students->id)
            ->where('school_fees.status', '!=', 'cancelled')
            ->where('school_fees.school_id', $user->school_id);

        // Apply year filter
        if ($selectedYear) {
            $billsQuery->where('school_fees.academic_year', 'LIKE', "%{$selectedYear}%");
        }

        $bills = $billsQuery->orderBy('school_fees.created_at', 'DESC')->get();

        // Get payments for these bills
        $billIds = $bills->pluck('id');
        $payments = DB::table('school_fees_payments')
            ->whereIn('student_fee_id', $billIds)
            ->orderBy('approved_at', 'ASC')
            ->get();

        // Create combined records for display
        $paymentRecords = collect();

        foreach ($bills as $bill) {
            // Add the bill itself as first record
            $paymentRecords->push([
                'type' => 'invoice',
                'control_number' => $bill->control_number,
                'academic_year' => $bill->academic_year,
                'service_name' => $bill->service_name,
                'amount' => $bill->billed_amount,
                'date' => $bill->created_at,
                'due_date' => $bill->due_date,
                'status' => $bill->status,
                'payment_mode' => null,
                'installment' => null
            ]);

            // Add payments for this bill
            $billPayments = $payments->where('student_fee_id', $bill->id);
            foreach ($billPayments as $payment) {
                $paymentRecords->push([
                    'type' => 'payment',
                    'control_number' => $bill->control_number,
                    'academic_year' => $bill->academic_year,
                    'service_name' => $bill->service_name,
                    'amount' => $payment->amount,
                    'date' => $payment->approved_at,
                    'due_date' => $bill->due_date,
                    'status' => 'paid',
                    'payment_mode' => $payment->payment_mode,
                    'installment' => $payment->installment
                ]);
            }
        }

        // Calculate totals
        $totalBilled = $bills->whereNotIn('status', ['cancelled'])->sum('billed_amount');
        $totalPaid = $payments->sum('amount');
        $totalBalance = $totalBilled - $totalPaid;

        return view('Bills.student_bills', compact(
            'students', 'paymentRecords', 'totalBilled', 'totalPaid', 'totalBalance', 'selectedYear'
        ));
    }
}
