<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    /**
     * Validation rules for LoginController.
     */
    private $validationRules = [
        'username' => 'required',
        'password' => 'required'
    ];

    /**
     * Authentication messages.
     */
    private $authenticationMessage = [
        'failed' => 'Thong tin dang nhap sai, vui long thu lai!'
    ];

    /**
     * Get credentials from HTTP Request.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    private function getCredentials(Request $request) {
        return array(
            'username' => $request->username,
            'password' => $request->password
        );
    }

    /**
     * Path / route name to redirect to (if no access-before page).
     */
    private $redirectTo = 'dashboard';

    /**
     * Show the login form.
     * 
     * @return \Illuminate\View\View
     */
    public function view() {
        return view('auth.login');
    }

    /**
     * Handle an authentication request.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request) {
        $validator = Validator::make(
            $request->all(),
            $this->validationRules
        );

        if (Auth::attempt($this->getCredentials($request))) {
            $request->session()->regenerate();

            return redirect()->intended($this->redirectTo);
        }

        return back()->withErrors($this->authenticationMessage);
    }
}