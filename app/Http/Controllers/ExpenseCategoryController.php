<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class ExpenseCategoryController extends Controller
{
    //

    public function index()
    {
        $user = Auth::user();
        $categories = [];
        $token = session('finance_api_token');
        try {
            $response = Http::withToken($token)->get(config('app.finance_api_base_url'). '/expense-categories', [
                'school_id' => $user->school_id,
            ]);

            if($response->successful()) {
                $data = $response->json();
                $categories = $data['categories'];
            } else {
                // logger()->error('Failed to fetch expense categories', ['status' => $response->status()]);
                Alert()->toast($response['message'] ?? 'Failed to fetch categories', 'error');
                // Log::error("Error code: ". $response->status());
            }
        }
        catch (\Throwable $e) {
            Alert()->toast($e->getMessage() ?? 'Connection not established from the server', 'info');
        }

        return view('Expenses.index', compact('categories'));
    }

    public function store (Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ],
        [
            'name.required' => 'Expense type is required',
            'name.unique' => 'Expense type must be unique',
            'description.max' => 'Description must not exceed 500 characters',
        ]);

        // dd($request->all());
        try {
            $response = Http::withToken(session('finance_api_token'))->post(config('app.finance_api_base_url'). '/expense-categories', [
                'school_id' => $user->school_id,
                'expense_type' => $request->name,
                'expense_description' => $request->description,
            ]);

            // dd($response['expense_description']);

            if($response->successful()) {
                Alert()->toast('Expense category added successfully', 'success');
                return back();
            } else {
                // logger()->error('Failed to add expense category', ['status' => $response->status()]);
                Alert()->toast($response['message'] ?? 'Failed to register expense category', 'error');
                // Log::error('Error code: '. $response->status());
                return back();
            }
        } catch (\Throwable $e) {
            Alert()->toast($e->getMessage() ?? 'Connection not established from the server', 'info');
            return back();
        }
    }

    public function destroy($id)
    {
        $decode = Hashids::decode($id);
        $user = Auth::user();

        try {
            $response = Http::withToken(session('finance_api_token'))->delete(config('app.finance_api_base_url'). '/expense-categories/' . $decode[0], [
                'school_id' => $user->school_id,
            ]);

            if($response->successful()) {
                Alert()->toast('Expense category deleted successfully', 'success');
                return back();
            } else {
                // logger()->error('Failed to delete expense category', ['status' => $response->status()]);
                Alert()->toast($response['message'] ?? 'Failed to delete expense category', 'error');
                // Log::error("Error code ". $response->status());
                return back();
            }
        } catch (\Throwable $e) {
            Alert()->toast($e->getMessage() ?? 'Connection not established from the server', 'info');
            return back();
        }
    }

    public function Edit($category)
    {
        $decode = Hashids::decode($category);
        $user = Auth::user();

        try {
            $response = Http::withToken(session('finance_api_token'))->get(config('app.finance_api_base_url'). '/expense-categories/' .$decode[0], [
                'school_id' => $user->school_id,
            ]);

            if($response->successful()) {
                $data = $response->json();
                $category = $data['category'];
            } else {
                Alert()->toast($response['message'] ?? 'Expense category not found', 'error');
                // Log::error("Error ". $response->status());

                return back();
            }
        } catch (Throwable $e) {
            Alert()->toast($e->getMessage() ?? 'Connection not established from the server', 'info');
            return back();
        }

        return view('Expenses.edit', compact('category'));
    }

    public function Update(Request $request, $category)
    {
        $decode = Hashids::decode($category);
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'string|required|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $response = Http::withToken(session('finance_api_token'))->put(config('app.finance_api_base_url'). '/expense-categories/'. $decode[0], [
                'school_id' => $user->school_id,
                'expense_type' => $request->name,
                'expense_description' => $request->description
            ]);

            if($response->successful()) {
                $data = $response->json();
                Alert()->toast('Category type updated successfully', 'success');
                return to_route('expenses.index');
            } else {
                Alert()->toast($response['message'] ?? 'Failed to update expense category', 'error');
                // Log::error('Error'. $response->status());
            }
        } catch (Throwable $e) {
            Alert()->toast($e->getMessage() ?? 'Connection not established from the server', 'info');
            return back();
        }
    }
}
