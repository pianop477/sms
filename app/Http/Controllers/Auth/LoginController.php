<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $loginType = $this->username();

        $credentials = [
            $loginType => $request->username,
            'password' => $request->password,
        ];

        if(Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            //set active session
            $request->session()->put('last_activity', time());

            $this->clearLoginAttempts($request);

            return redirect()->intended($this->redirectPath());
        }

        return back()->with('error', 'Invalid credentials');
    }
}
