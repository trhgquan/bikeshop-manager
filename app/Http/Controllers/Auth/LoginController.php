<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /**
     * Authentication messages.
     * 
     * @var array
     */
    private $authenticationMessage = [
        'failed' => 'Thong tin dang nhap sai, vui long thu lai!'
    ];

    /**
     * Path / route name to redirect to (if no access-before page).
     */
    private $redirectTo = 'dashboard';

    /**
     * Handle an authentication request.
     * 
     * @param  \App\Http\Requests\LoginRequest $request
     * @return \Illuminate\Http\Response
     */
    public function handle(LoginRequest $request) {
        // Get validated form data.
        $credentials = $request->validated();

        // Attempt to log in.
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended($this->redirectTo);
        }

        // Log in failed.
        return back()->withErrors($this->authenticationMessage);
    }

    /**
     * Show the login form.
     * 
     * @return \Illuminate\View\View
     */
    public function index() {
        return view('auth.login');
    }
}