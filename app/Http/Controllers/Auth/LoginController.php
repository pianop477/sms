<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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

            if($user->usertype == 5) {
                // Request token from ShuleApp-Finance
                // dd(env('SHULEAPP_CLIENT_KEY'). ' '. env('SHULEAPP_CLIENT_SECRET'));
                try {
                    $response = Http::post(config('app.finance_api_base_url') . '/auth/token', [
                        'client_key' => config('app.finance_api_client_key'),
                        'client_secret' => config('app.finance_api_client_secret'),
                    ]);

                    if ($response->successful()) {
                        $tokenData = $response->json();
                        // Log::info('Finance API token fetched successfully', ['token_data' => $tokenData]);
                        session([
                            'finance_api_token' => $tokenData['token'],
                            'finance_token_expires_at' => now()->addSeconds($tokenData['expires_in']),
                        ]);
                        // dd(session()->all());
                    } else {
                        // Log::error('Failed to fetch finance API token', ['response' => $response->body()]);
                        Alert()->toast($response->status(), 'error');
                    }
                } catch (\Throwable $e) {
                    // Log::error('Error connecting to finance API for token', ['error' => $e->getMessage()]);
                    Alert()->toast('Connection not established from the server', 'info');
                }
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
