<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class ContractController extends Controller
{
    //

    public function index()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $contracts = Contract::where('teacher_id', $teacher->id)->orderBy('approved_at', 'DESC')->orderBY('updated_at', 'DESC')->get();
        return view('Teachers.contract_renew', compact('contracts'));
    }

    public function store(Request $request)
    {
        // Get authenticated teacher
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // Validate the input
        $validator = Validator::make($request->all(), [
            'contract_type' => 'required|string|in:probation,new,renewal,extension',
            'application_letter' => 'required|file|mimes:pdf|max:512', // Max 512 KB
        ]);

        // Check validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach($errors as $error) {
                Alert::error('Validation Fails', $error);
                return back();
            }
        }

        // Check if there is an existing pending request
        $existingRequest = Contract::where('teacher_id', $teacher->id)
                                ->whereIn('status', ['pending', 'rejected'])
                                ->exists();

        if ($existingRequest) {
            Alert::error('Request Denied', 'You already have a pending contract request. Please wait for approval.');
            return redirect()->back();
        }

        // Check for active contract with at least 2 months or more
        $activeContract = Contract::where('teacher_id', $teacher->id)
                                    ->whereIn('status', ['approved', 'completed'])
                                    ->whereRaw('DATEDIFF(end_date, NOW()) > 30') // Active for at least 1 months
                                    ->first();

        if ($activeContract) {
            Alert::info(
                'Info',
                'Your request failed because you still have an active contract. Your contract will expire on ' .
                Carbon::parse($activeContract->end_date)->format('d-m-Y H:i')
            );
            return redirect()->back();
        }


        // Handle file upload
        $filePath = '';
        if ($request->hasFile('application_letter')) {
            $applicationFile = $request->file('application_letter');
            $fileName = time() . '.' . $applicationFile->getClientOriginalExtension();
            $destinationPath = storage_path('app/public/contracts/contract_application');

            // Ensure the directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Move the file to the destination
            $applicationFile->move($destinationPath, $fileName);
            $filePath = 'contracts/contract_application/' . $fileName; // Path to store in the database
        }

        // Save contract data
        Contract::create([
            'teacher_id' => $teacher->id,
            'school_id' => $teacher->school_id,
            'contract_type' => $request->input('contract_type'),
            'application_file' => $filePath,
            'applied_at' => now(),
        ]);

        // Notify success
        Alert::success('Done', 'Your application has been submitted successfully.');
        return redirect()->back();
    }

    //preview teachers his/her contract application
    public function previewMyApplication($id)
    {
        $contract = Contract::find($id);

        if (!$contract) {
            Alert::error('Failed', 'No such contract records found');
            return back();
        }

        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        if ($contract->teacher_id != $teacher->id) {
            Alert::error('Error', 'The selected Contract is not uploaded by you');
            return back();
        }

        $filePath = 'public/' . $contract->application_file; // Adjust path as needed

        if (!file_exists(storage_path('app/public/' . $contract->application_file))) {
            Alert::error('Error', 'The application file is missing');
            return back();
        }

        return response()->file(storage_path('app/' . $filePath));
    }

    public function edit($id)
    {
        $contract = Contract::find($id);

        if (!$contract) {
            Alert::error('Failed', 'No such contract records found');
            return back();
        }

        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        if ($contract->teacher_id != $teacher->id) {
            Alert::error('Error', 'The selected Contract is not uploaded by you');
            return back();
        }

        return view('Contract.edit', compact('contract'));
    }

    public function update(Request $request, $id)
    {
        $contract = Contract::find($id);
        if(!$contract) {
            Alert::error('Failed', 'No such contract records found');
            return back();
        }

        try {
            $validator = Validator::make($request->all(), [
                'contract_type' => 'required|string|in:new,renewal, extension, probation',
                'application_letter' => 'required|file|mimes:pdf|max:512',
            ]);

            if($validator->fails()){
                $errors = $validator->errors();

                foreach($errors as $error) {
                    Alert::error('Validation Fails', $error);
                    return back();
                }
            }

            $filePath = '';

            if ($request->hasFile('application_letter')) {
                $file = $request->file('application_letter'); // Correctly access the file

                // Generate a unique file name
                $fileName = time() . '.' . $file->getClientOriginalExtension();

                // Define the destination path (within 'storage/app/public')
                $destinationPath = storage_path('app/public/contracts/contract_application');

                // Ensure the destination directory exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }

                // Delete the existing file, if any
                if ($contract->application_file) {
                    $existingFile = storage_path('app/public/' . $contract->application_file);
                    if (file_exists($existingFile)) {
                        unlink($existingFile);
                    }
                }

                // Move the new file to the destination path
                $file->move($destinationPath, $fileName);

                // Save the relative file path for database storage
                $filePath = 'contracts/contract_application/' . $fileName;

                // Update the contract's application_file column
                $contract->update(['application_file' => $filePath]);
            }


            $contract->update([
                'status' => $request->input('status', 'pending'),
                'contract_type' => $request->input('contract_type'),
                'application_file' => $filePath,
            ]);

            Alert::success('Done', 'Application has been updated successfully');
            return redirect()->route('contract.index');
        }
        catch(\Exception $e){
            Alert::error('Error', $e->getMessage());
            return back();
        }
    }

    public function destroy($id)
    {
        $contract = Contract::find($id);
        if(! $contract) {
            Alert::error('Failed', 'No such contract found');
            return back();
        }

        try {

            if ($contract->application_file) {
                $existingFile = storage_path('app/public/' . $contract->application_file);
                if (file_exists($existingFile)) {
                    unlink($existingFile);
                }
            }

            $contract->delete();
            Alert::success('Done', 'Application has been deleted successfully');
            return redirect()->route('contract.index');
        }
        catch(\Exception $e) {
            Alert::error('Error', $e->getMessage());
            return back();
        }
    }

    public function contractManager()
    {
        $user = Auth::user();

        $contractsByYear = Contract::where('status', 'approved')
                        ->where('school_id', $user->school_id)
                        ->orderBy('approved_at', 'DESC')
                        ->get()
                        ->groupBy(function ($contract) {
                            return \Carbon\Carbon::parse($contract->approved_at)->format('Y'); // Group by year
                        });

        $contractRequests = Contract::query()
                                    ->join('teachers', 'teachers.id', '=', 'contracts.teacher_id')
                                    ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                    ->select(
                                        'contracts.*', 'users.first_name', 'users.last_name', 'teachers.member_id', 'users.gender'
                                    )
                                    ->where('contracts.school_id', $user->school_id)
                                    ->where('contracts.status', 'pending')
                                    ->orderBy('contracts.applied_at', 'DESC')
                                    ->orderBy('contracts.updated_at', 'DESC')
                                    ->get();
        return view('Contract.manager_contact_group', compact('contractsByYear', 'contractRequests'));
    }


    public function adminPreviewFile($id)
    {
        $contract = Contract::find($id);

        if (!$contract) {
            Alert::error('Failed', 'No such contract records found');
            return back();
        }

        $user = Auth::user();

        $filePath = 'public/' . $contract->application_file; // Adjust path as needed

        if (!file_exists(storage_path('app/public/' . $contract->application_file))) {
            Alert::error('Error', 'The application file is missing');
            return back();
        }

        return response()->file(storage_path('app/' . $filePath));
    }

    public function approveContract (Request $request, $id)
    {
        $contract = Contract::find($id);

        if(! $contract ) {
            Alert::error('Error', 'No such contract was found');
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'duration' => 'required|integer',
            'remark' => 'required|string|max:255',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();
            foreach($errors as $error) {
                Alert::error('Validation Fails', $error);
                return back();
            }
        }
        $approved_at = Carbon::now();
        $endDate = $approved_at->copy()->addMonth($request->duration);

        $contract->update([
            'status' => $request->input('status', 'approved'),
            'start_date' => $approved_at,
            'end_date' => $endDate,
            'approved_at' => $approved_at,
            'duration' => $request->input('duration'),
            'remarks' => $request->input('remark'),
        ]);

        Alert::success('Done', 'The request has been approved successfully');
        return redirect()->back();
    }

    public function rejectContract (Request $request, $id)
    {
        $contract = Contract::find($id);

        if(! $contract ) {
            Alert::error('Error', 'No such contract was found');
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'remark' => 'required|string|max:255',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();
            foreach($errors as $error) {
                Alert::error('Validation Fails', $error);
                return back();
            }
        }

        $contract->update([
            'status' => $request->input('status', 'rejected'),
            'remarks' => $request->input('remark'),
        ]);

        Alert::success('Done', 'The request has been rejected successfully');
        return redirect()->back();
    }

    public function contractByMonths ($year)
    {
        $user = Auth::user();

        // Fetch and group contracts by month
        $contractsByMonth = Contract::where('status', 'approved')
                                ->where('school_id', $user->school_id)
                                ->whereYear('approved_at', $year) // Filter by year
                                ->orderBy('approved_at', 'DESC')
                                ->get()
                                ->groupBy(function ($contract) {
                                    return \Carbon\Carbon::parse($contract->approved_at)->format('F'); // Group by month name
                                });
        return view('Contract.contract_by_months', compact('year', 'contractsByMonth'));

    }

    public function getAllApprovedContract($year, $month)
    {
        $user = Auth::user();
        $monthsArray = [
            'January' => 1, 'February' => 2, 'March' => 3,
            'April' => 4, 'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8,
            'September' => 9, 'November' => 11, 'December' => 12
        ];

        $targetMonth = $monthsArray[$month];

        $allContracts = Contract::query()
                                    ->join('teachers', 'teachers.id', '=', 'contracts.teacher_id')
                                    ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                                    ->select(
                                        'contracts.*', 'users.first_name', 'users.last_name', 'users.gender',
                                        'users.phone', 'teachers.member_id'
                                    )
                                    ->whereYear('contracts.approved_at', $year)
                                    ->whereMonth('contracts.approved_at', $targetMonth)
                                    ->where('contracts.school_id', $user->school_id)
                                    ->orderBy('users.first_name')
                                    ->get();
        return view('Contract.approved_contract', compact('year', 'month', 'allContracts'));

    }

    public function downloadContract($id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $contract = Contract::query()
                            ->join('teachers', 'teachers.id', '=', 'contracts.teacher_id')
                            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
                            ->join('schools', 'schools.id', '=', 'contracts.school_id')
                            ->select(
                                'contracts.*', 'teachers.member_id', 'users.first_name', 'users.last_name', 'users.gender',
                                'users.phone', 'schools.school_name', 'teachers.address', 'schools.school_reg_no',
                                'schools.postal_address', 'schools.postal_name', 'schools.logo', 'schools.country'
                            )
                            ->where('contracts.teacher_id', $teacher->id)
                            ->find($id);
        if(! $contract) {
            Alert::error('Failed', 'No such contract record was found');
            return redirect()->back();
        }

        $pdf = \PDF::loadView('Contract.contract_file', compact('contract'));
        return $pdf->stream($contract->approved_at.'pdf');
    }

}
