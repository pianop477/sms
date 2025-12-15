<?php

namespace App\Http\Middleware;

use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class forceUpdateNidaAndIndexNumber
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check()) {
            $user = Auth::user();

            if($user->usertype == 3) {
                $teacher = Teacher::where('user_id', $user->id)->first();

                if($teacher->nida == null || $teacher->form_four_index_number == null || $teacher->form_four_completion_year == null) {
                    Alert::info('Info', 'You must update your NIDA and FORM FOUR details to proceed');
                    return redirect()->route('get.nida.form.four');
                }

                return $next($request);
            }

            return $next($request);
        }
    }
}
