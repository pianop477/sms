<?php

namespace App\Http\Controllers;

use App\Models\generated_reports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneratedReportController extends Controller
{
    //
    public function index()
    {
        $reports = generated_reports::latest()->paginate(10);
        return view('generated_reports.index', compact('reports'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'exam_type' => 'required|string|max:255',
            'class_id' => 'required|integer',
            'exam_dates' => 'required|array',
            'combine_option' => 'required|in:sum,average,individual',
        ]);

        $selectedDataSet = $request->input('exam_dates', []);
        $examType = $request->input('exam_type');
        $classId = $request->input('class_id');
        $customExamType = $request->input('custom_exam_type'); // Capture custom exam type
        $combineMode = $request->input('combine_option');

        if ($examType === 'custom' && !empty($customExamType)) {
            $examType = $customExamType;
        }

        $report = generated_reports::create([
            'title' => $examType,
            'class_id' => $classId,
            'school_id' => Auth::user()->school_id,
            'exam_dates' => $selectedDataSet,
            'combine_option' => $combineMode,
            'created_by' => auth()->id(),
        ]);

        // return redirect()->route('generated-reports.show', $report->id)->with('success', 'Report generated successfully.');
        Alert()->toast('Report generated successfully.', 'success');
        return redirect()->back();
    }

}
