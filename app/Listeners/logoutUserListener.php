<?php

namespace App\Listeners;

use App\Events\PasswordResetEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class logoutUserListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordResetEvent $event)
    {
        //invalidate all sessions for the user
        DB::table('sessions')->where('user_id', $event->userId)->delete();
        if(Auth::id() == $event->userId) {
            Auth::logout();
        }
    }
}
