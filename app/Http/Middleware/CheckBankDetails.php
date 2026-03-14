<?php

namespace App\Http\Middleware;

use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckBankDetails
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return $next($request);
        }

        // Check if user is logged in
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user is a teacher (usertype = 3)
            if ($user->usertype == 3) {
                // Get teacher record
                $teacher = Teacher::where('user_id', $user->id)->first();

                if ($teacher) {
                    // ANGAZA: Tumia majina sahihi ya columns (account_number, account_name)
                    $bankDetailsMissing = empty($teacher->bank_name) ||
                        empty($teacher->bank_account_number) ||    // ← Badilisha hapa
                        empty($teacher->bank_account_name);        // ← Badilisha hapa

                    $lastShown = session('bank_modal_last_shown');
                    $shouldShowModal = true;

                    if ($lastShown && (time() - $lastShown) < 300) {
                        $shouldShowModal = false;
                    }

                    $isOnBankPage = $request->routeIs('bank.details');

                    if ($bankDetailsMissing && !$isOnBankPage && $shouldShowModal) {
                        session([
                            'show_bank_modal' => true,
                            'bank_modal_last_shown' => time(),
                            'teacher_id_for_modal' => $teacher->id
                        ]);

                        // Log for debugging
                        Log::info('Bank modal should show for teacher: ' . $teacher->id);
                    } else {
                        if (!$bankDetailsMissing || $isOnBankPage) {
                            session()->forget(['show_bank_modal', 'teacher_id_for_modal']);
                        }
                    }
                }
            } else {
                session()->forget(['show_bank_modal', 'teacher_id_for_modal']);
            }
        }

        return $next($request);
    }
}
