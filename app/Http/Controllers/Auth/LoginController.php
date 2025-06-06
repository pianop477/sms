<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use RealRashid\SweetAlert\Facades\Alert;

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
    protected $redirectTo = '/home';

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

        $ip = $request->ip();
        $key = 'login:attempts:' . strtolower($request->username) . ':' . $ip;
        $maxAttempts = 3;
        $decayMinutes = 15;

        // Record the attempt first
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            Alert()->toast('Account locked. Try again in ' . ceil($seconds / 60) . ' minutes.', 'error');
            return redirect()->back();
        }

        RateLimiter::hit($key, $decayMinutes * 60);
        $loginType = $this->username(); // could be 'email' or 'username' depending on your logic
        $credentials = [
            $loginType => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // Clear rate limit on successful login
            RateLimiter::clear($key);
            $request->session()->put('last_activity', time());

            Alert()->toast('Hello 🤩 Welcome back to ShuleApp', 'success');
            return redirect()->intended($this->redirectPath());
        }

        // Log failed login details in database
        DB::table('failed_logins')->insert([
            'ip' => $ip,
            'username' => $request->username,
            'user_agent' => $request->userAgent(),
            'attempted_at' => now()
        ]);

        return back()->with('error', 'Invalid credentials');
    }
}
