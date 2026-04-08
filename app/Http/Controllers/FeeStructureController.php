<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use App\Models\FeeInstallment;
use App\Models\Grade;
use App\Traits\HashIdTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use ProtoneMedia\LaravelCrossEloquentSearch\Searcher;

class FeeStructureController extends Controller
{
    use HashIdTrait;

    public function index()
    {
        $structures = FeeStructure::where('school_id', Auth::user()->school_id)
            ->with(['installments', 'class'])
            ->orderBy('created_at', 'desc')
            ->get();

        $classes = Grade::where('school_id', Auth::user()->school_id)
            ->orderBy('class_name')
            ->get();

        return view('fee-structures.index', compact('structures', 'classes'));
    }

    public function create()
    {
        $classes = Grade::where('school_id', Auth::user()->school_id)
            ->orderBy('name')
            ->get();

        return view('fee-structures.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'class_id' => 'nullable|exists:grades,id',
            'class_type' => 'required|in:regular,hostel',
            'transport_applies' => 'required_if:class_type,regular|nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Check for duplicate structure
            $existing = FeeStructure::where('school_id', Auth::user()->school_id)
                ->where('name', $request->name)
                ->when($request->class_id, function ($query) use ($request) {
                    return $query->where('class_id', $request->class_id);
                })
                ->when(!$request->class_id, function ($query) {
                    return $query->whereNull('class_id');
                })
                ->first();

            if ($existing) {
                throw new \Exception('A fee structure with this name already exists for this class.');
            }

            // Determine values based on class type
            $isHostelClass = ($request->class_type === 'hostel');
            $transportApplies = $isHostelClass ? false : (bool)$request->transport_applies;

            $structure = FeeStructure::create([
                'school_id' => Auth::user()->school_id,
                'class_id' => $request->class_id,
                'name' => $request->name,
                'total_amount' => $request->total_amount,
                'transport_applies' => $transportApplies,
                'is_hostel_class' => $isHostelClass,
            ]);

            DB::commit();

            Alert::success('Success!', 'Fee structure created successfully.');
            return redirect()->route('fee-structures.installments', $structure->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error!', 'Failed to create fee structure: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function manageInstallments(FeeStructure $feeStructure, Request $request)
    {
        // Ensure the fee structure belongs to the authenticated user's school
        if ($feeStructure->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $installments = $feeStructure->installments()->orderBy('order')->get();

        // Get unique years from installments
        $years = $installments->pluck('academic_year')->unique()->sort()->values();

        // Determine selected year (from request, or current year, or first available year)
        $selectedYear = $request->get('year', date('Y'));

        // If selected year doesn't exist in installments but there are years available, use the first year
        if (!$years->contains($selectedYear) && $years->isNotEmpty()) {
            $selectedYear = $years->first();
        }

        return view('fee-structures.installments', compact('feeStructure', 'installments', 'selectedYear'));
    }

    /**
     * Store a new installment
     */
    public function storeInstallment(Request $request, FeeStructure $feeStructure)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'cumulative_required' => 'required|numeric|min:0',
            'academic_year' => 'required|integer|min:2020|max:2100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'order' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Check if order already exists for the SAME academic year
            $existing = FeeInstallment::where('fee_structure_id', $feeStructure->id)
                ->where('academic_year', $request->academic_year)
                ->where('order', $request->order)
                ->first();

            if ($existing) {
                throw new \Exception("Installment with order {$request->order} already exists for academic year {$request->academic_year}. Please use a different order number.");
            }

            // Optional: Check if cumulative required is consistent with previous installments
            $previousInstallments = FeeInstallment::where('fee_structure_id', $feeStructure->id)
                ->where('academic_year', $request->academic_year)
                ->where('order', '<', $request->order)
                ->orderBy('order', 'desc')
                ->first();

            if ($previousInstallments && $request->cumulative_required < $previousInstallments->cumulative_required) {
                throw new \Exception("Cumulative required amount cannot be less than previous installment ({$previousInstallments->cumulative_required} TZS).");
            }

            FeeInstallment::create([
                'fee_structure_id' => $feeStructure->id,
                'name' => $request->name,
                'amount' => $request->amount,
                'academic_year' => $request->academic_year,
                'cumulative_required' => $request->cumulative_required,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'order' => $request->order,
            ]);

            DB::commit();

            Alert::success('Success!', 'Installment added successfully.');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error!', $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Get installment data for editing (AJAX)
     */
    public function editInstallment($id)
    {
        try {
            // Find installment with its fee structure
            $installment = FeeInstallment::with('feeStructure')->findOrFail($id);

            // Check permission - ensure the installment belongs to the user's school
            if ($installment->feeStructure->school_id !== Auth::user()->school_id) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            // Return JSON response for AJAX
            return response()->json([
                'id' => $installment->id,
                'name' => $installment->name,
                'order' => $installment->order,
                'amount' => $installment->amount,
                'academic_year' => $installment->academic_year,
                'cumulative_required' => $installment->cumulative_required,
                'start_date' => $installment->start_date instanceof \DateTime
                    ? $installment->start_date->format('Y-m-d')
                    : date('Y-m-d', strtotime($installment->start_date)),
                'end_date' => $installment->end_date instanceof \DateTime
                    ? $installment->end_date->format('Y-m-d')
                    : date('Y-m-d', strtotime($installment->end_date)),
                'fee_structure_id' => $installment->fee_structure_id
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Installment not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load installment data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update an existing installment
     */
    public function updateInstallment(Request $request, FeeInstallment $installment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'cumulative_required' => 'required|numeric|min:0',
            'academic_year' => 'required|integer|min:2020|max:2100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'order' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Check permission
            if ($installment->feeStructure->school_id !== Auth::user()->school_id) {
                throw new \Exception('Unauthorized action');
            }

            // Check if order conflicts with other installments for the SAME academic year
            $conflict = FeeInstallment::where('fee_structure_id', $installment->fee_structure_id)
                ->where('academic_year', $request->academic_year)
                ->where('order', $request->order)
                ->where('id', '!=', $installment->id)
                ->first();

            if ($conflict) {
                throw new \Exception("Installment with order {$request->order} already exists for academic year {$request->academic_year}.");
            }

            // Optional: Check if cumulative required is consistent with other installments
            $previousInstallments = FeeInstallment::where('fee_structure_id', $installment->fee_structure_id)
                ->where('academic_year', $request->academic_year)
                ->where('order', '<', $request->order)
                ->where('id', '!=', $installment->id)
                ->orderBy('order', 'desc')
                ->first();

            if ($previousInstallments && $request->cumulative_required < $previousInstallments->cumulative_required) {
                throw new \Exception("Cumulative required amount cannot be less than previous installment ({$previousInstallments->cumulative_required} TZS).");
            }

            $nextInstallments = FeeInstallment::where('fee_structure_id', $installment->fee_structure_id)
                ->where('academic_year', $request->academic_year)
                ->where('order', '>', $request->order)
                ->where('id', '!=', $installment->id)
                ->orderBy('order', 'asc')
                ->first();

            if ($nextInstallments && $request->cumulative_required > $nextInstallments->cumulative_required) {
                throw new \Exception("Cumulative required amount cannot be greater than next installment ({$nextInstallments->cumulative_required} TZS).");
            }

            $installment->update([
                'name' => $request->name,
                'amount' => $request->amount,
                'cumulative_required' => $request->cumulative_required,
                'academic_year' => $request->academic_year,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'order' => $request->order,
            ]);

            DB::commit();

            Alert::success('Success!', 'Installment updated successfully.');
            return redirect()->route('fee-structures.installments', $installment->fee_structure_id);
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error!', $e->getMessage());
            return back()->withInput();
        }
    }


    public function deleteInstallment(FeeInstallment $installment)
    {
        try {
            DB::beginTransaction();

            // Check permission
            if ($installment->feeStructure->school_id !== Auth::user()->school_id) {
                throw new \Exception('Unauthorized action');
            }

            // Check if this installment has any tokens
            if ($installment->tokens()->exists()) {
                throw new \Exception("Cannot delete installment with active tokens. Tokens must be expired first.");
            }

            $feeStructureId = $installment->fee_structure_id;
            $installment->delete();

            DB::commit();

            Alert::success('Success!', 'Installment deleted successfully.');
            return redirect()->route('fee-structures.installments', $feeStructureId);
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error!', $e->getMessage());
            return back();
        }
    }

    public function edit(FeeStructure $feeStructure)
    {
        return response()->json($feeStructure);
    }

    public function update(Request $request, FeeStructure $feeStructure)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $feeStructure->update([
                'name' => $request->name,
                'total_amount' => $request->total_amount,
            ]);

            DB::commit();

            Alert::success('Success!', 'Fee structure updated successfully.');
            return redirect()->route('fee-structures.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error!', 'Failed to update fee structure: ' . $e->getMessage());
            return back()->withInput();
        }
    }
}
