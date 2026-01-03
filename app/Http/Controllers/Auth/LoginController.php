<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FinanceTokenService;
// use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;
use Throwable;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        $login = request()->input('username');
        return filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $ip   = $request->ip();
        $key  = 'login:attempts:' . strtolower($request->username) . ':' . $ip;
        $maxAttempts  = 3;    // after 3 fails lock
        $decayMinutes = 15;   // lock window

        // 1. Stop immediately if user is locked out
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            Alert()->toast(
                'Account locked. Try again in ' . ceil($seconds / 60) . ' minutes.',
                'error'
            );

            return back()->withInput($request->only('username'));  // keep what user typed
        }

        // 2. Attempt login only if not locked
        $loginType   = $this->username();  // method that returns 'email' or 'phone'
        $credentials = [
            $loginType => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // success: clear attempts + regenerate session
            RateLimiter::clear($key);
            $request->session()->regenerate();
            $request->session()->put('last_activity', time());

            $user = Auth::user();

            if ($user->usertype == 5) {
                $tokenService = new FinanceTokenService();

                $debugUrl = $tokenService->debugConnection();

                 $token = $tokenService->ensureValidToken();

                if (!$token) {
                    $sessionId = Session::getId();
                    DB::table('sessions')->where('id', $sessionId)->delete();
                    Auth::logout();
                    Session::invalidate();
                    // Log::error("Finance token acquisition failed for user: {$user->id}");
                    return to_route('login')->with('error', 'Unable to connect to finance service. Please try again.');
                }

                // Log::info("Finance API token acquired for user: {$user->id}");
            }

            Alert()->toast('Hello '. ucwords(strtolower(Auth::user()->first_name))  .' Welcome back!', 'success');
            return redirect()->intended($this->redirectPath());
        }

        // 3. Fail: increment the attempts count
        RateLimiter::hit($key, $decayMinutes * 60);

        // optional logging
        DB::table('failed_logins')->insert([
            'ip'         => $ip,
            'username'   => $request->username,
            'user_agent' => $request->userAgent(),
            'attempted_at' => now(),
        ]);

        // keep username (and password if you insist)
        return back()->with('error', 'Invalid Username or Password')->withInput($request->only('username', 'password'));

    }
}
