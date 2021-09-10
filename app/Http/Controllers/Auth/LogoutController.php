<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Notification message when logged out successfully.
     *
     * @var array
     */
    private $logoutMessage = [
        'success' => 'Đăng xuất thành công.',
    ];

    /**
     * Path / route to redirect when logged out.
     *
     * @var string
     */
    private $logoutRedirect = 'auth.login.index';

    /**
     * Handle logout procedure.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route($this->logoutRedirect)
            ->with('notify', $this->logoutMessage);
    }

    /**
     * By default, user cannot access to logout route with GET method.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('dashboard');
    }
}
