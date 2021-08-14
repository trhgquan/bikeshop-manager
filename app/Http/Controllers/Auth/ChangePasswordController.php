<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;

class ChangePasswordController extends Controller
{
    /**
     * Message when success.
     * 
     * @var array
     */
    private $successMessage = [
        'success' => 'Đổi mật khẩu thành công.'
    ];

    /**
     * Message when failed.
     * 
     * @var array
     */
    private $failedMessage = [
        'failed' => 'Đổi mật khẩu thất bại.'
    ];

    /**
     * Invoke change password procedure.
     * 
     * @param  \App\Http\Requests\ChangePasswordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function handle(ChangePasswordRequest $request) {
        // Generate hash value for new password.
        $new_password = Hash::make($request->new_password);

        // Update new password, then return with success message.
        Auth::user()->password = $new_password;
        Auth::user()->save();

        return redirect()
            ->route('auth.changepassword.index')
            ->with('notify', $this->successMessage);
    }

    /**
     * Show the change password form.
     * 
     * @return \Illuminate\View\View
     */
    public function index() {
        return view('auth.password');
    }
}