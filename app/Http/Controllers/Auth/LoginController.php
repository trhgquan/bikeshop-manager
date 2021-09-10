<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Authentication messages.
     *
     * @var array
     */
    private $authenticationMessage = [
        'failed' => 'Thông tin đăng nhập sai, vui lòng thử lại!',
    ];

    /**
     * Path / route name to redirect to (if no access-before page).
     */
    private $redirectTo = 'dashboard';

    /**
     * Handle an authentication request.
     *
     * @param \App\Http\Requests\LoginRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(LoginRequest $request)
    {
        // Get validated form data.
        $credentials = $request->validated();

        // Attempt to log in.
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended($this->redirectTo);
        }

        // Log in failed.
        return redirect()
            ->route('auth.login.index')
            ->withErrors($this->authenticationMessage);
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('auth.login');
    }
}
