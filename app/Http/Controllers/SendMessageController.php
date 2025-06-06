<?php

namespace App\Http\Controllers;

use App\Models\message;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class SendMessageController extends Controller
{
    /**
     * Show the form for creating the resource.
     */
    public function create(): never
    {
        abort(404);
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request)
    {
        // abort(404);
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'message' => 'required|string|max:500'
        ], [
            'name.required' => 'Name is required',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Phone number must be 10 digits',
            'message.required' => 'Message is required',
        ]);
        $sendFeeback = new message();
        $sendFeeback->name = $request->name;
        $sendFeeback->email = $request->phone;
        $sendFeeback->message = $request->message;
        $sendFeeback->save();
        // return back()->with('success', 'Message sent! Thank you for your feedback. Happy enjoy our Services');
        Alert()->toast('Thank you for your feedback. Happy enjoy our Services', 'success');
        return back();
    }

    /**
     * Display the resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(): never
    {
        abort(404);
    }
}
