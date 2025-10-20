<?php

namespace App\Http\Controllers;

use App\Models\school;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class ExpenditureController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();

        try {
            $response = Http::withToken(session('finance_api_token'))->get(env('SHULEAPP_FINANCE_API_BASE_URL'). '/daily-expense', [
                'school_id' => $user->school_id,
            ]);

            if($response->successful()) {
                $data = $response->json();
                $expenses = $data['expenses'];
                $categories = $data['categories'];
            } else {
                return response()->json([
                    'status' => false,
                    'body' => $response->body(),
                ], 400);
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
            Log::info(
                $response = $http->post(
                env('SHULEAPP_FINANCE_API_BASE_URL') . '/daily-expense',
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
            )
        );

            if ($response->successful()) {
                Alert()->toast('Transaction has been saved successfully', 'success');
            } else {
                Alert()->toast('Failed to save transaction', 'error');
                Log::error('Finance API error: ' . $response->body());
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
            $response = Http::withToken(session('finance_api_token'))->put(env('SHULEAPP_FINANCE_API_BASE_URL').'/daily-expense/'.$decoded[0], [
                'cancel_reason' => $request->cancel_reason,
            ]);

            if($response->successful()) {
                Alert()->toast('Transaction bill has been cancelled successfully', 'success');
            }

            else {
                Alert()->toast('Failed to cancel transaction bill', 'error');
                Log::error("Error body: ". $response->status());
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
                        ->delete(env('SHULEAPP_FINANCE_API_BASE_URL'). '/daily-expense/'.$decoded[0]);

            if($response->successful()) {
                Alert()->toast('Transaction has been deleted successfully', 'success');
            }
            else {
                Alert()->toast('Failed to delete transaction record', 'error');
                Log::error("error found ". $response->status());
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

            $response = Http::withToken(session('finance_api_token'))->get(env('SHULEAPP_FINANCE_API_BASE_URL'). '/all-transactions', [
                'school_id' => $school_id,
            ]);

            if($response->successful()) {
                $data = $response->json();
                $transactions = $data['transactions'];
                $categories = $data['categories'];
                return view('Expenditures.all-transactions', compact('transactions'));
            }
            else {
                Alert()->toast('Failed to get transactions records', 'error');
                Log::error("Error code ". $response->status());
                return back();
            }
        } catch (Throwable $e) {
            Alert()->toast($e->getMessage() ?? "Connection not established from the server", "info");
            return back();
        }

    }
}
