<?php

namespace App\Http\Controllers;

use App\Models\payment_service;
use App\Models\school_fees;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class ServiceController extends Controller
{
    //
    public function index()
    {
        $services = payment_service::orderBy('service_name', 'ASC')->get();
        // dd($services);
        return view('Services.index', compact('services'));
    }

    public function storeService (Request $request)
    {
        $this->validate($request, [
            'service_name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'payment' => 'required|string|max:255',
            'duration' => 'required|string|max:255',
            'account' => 'nullable|string',
        ]);

        $service = payment_service::where('service_name', $request->service_name)->first();

        if($service) {
            Alert()->toast('Service already exists', 'error');
            return back();
        }

        payment_service::create([
            'service_name' => $request->service_name,
            'amount' => $request->amount,
            'payment_mode' => $request->payment,
            'expiry_duration' => $request->duration,
            'collection_account' => $request->account
        ]);

        Alert()->toast('Service added successfully', 'success');
        return back();
    }

    public function editService (Request $request, $id)
    {
        $service = payment_service::findOrFail($id);

        return response()->json([
            'status' => true,
            'service' => $service
        ], 200);
    }

    public function updateService(Request $request, $serviceId)
    {
        $service = payment_service::findOrFail($serviceId);
        $service->update([
            'service_name' => $request->service_name,
            'amount' => $request->amount,
            'payment_mode' => $request->payment_mode,
            'expiry_duration' => $request->duration,
            'status' => $request->status,
            'collection_account' => $request->account
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Service updated successfully'
        ], 200);
    }

    public function blockService(Request $request, $id)
    {
        $decoded = Hashids::decode($id);
        $service = payment_service::findOrFail($decoded[0]);

        if(! $service) {
            Alert()->toast('No service were found', 'error');
            return back();
        }

        // dd( $request->all());
        $reason = cache('cancel_reason_'.$decoded[0]);

        $service->update([
            'status' => $request->input('status', 'inactive')
        ]);

        Alert()->toast('Service cancelled successfully', 'success');
        return back();
    }

    public function deleteService(Request $request, $id)
    {
        $decoded = Hashids::decode($id);
        $service = payment_service::findOrFail($decoded[0]);

        if(! $service) {
            Alert()->toast('No service were found', 'error');
            return back();
        }

        $school_fees = school_fees::where('service_id', $decoded[0])->first();

        if($school_fees) {
            Alert()->toast('Selected service is in use, please try again', 'error');
            return back();
        }

        $service->delete();

        Alert()->toast('Service deleted successfully', 'success');
        return back();
    }
}
