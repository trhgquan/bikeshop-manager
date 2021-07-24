<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    /**
     * Notification message when logged out successfully.
     * 
     * @var array
     */
    private $logoutMessage = [
        'success' => 'Dang xuat thanh cong'
    ];

    /**
     * Path / route to redirect when logged out.
     * 
     * @var string
     */
    private $logoutRedirect = 'auth.login.view';

    /**
     * Only method to invoke logout procedure.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request) {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route($this->logoutRedirect)
            ->with('notify', $this->logoutMessage);
    }
}