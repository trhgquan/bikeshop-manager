<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
        'success' => 'Doi mat khau thanh cong.'
    ];

    /**
     * Message when failed.
     * 
     * @var array
     */
    private $failedMessage = [
        'failed' => 'Doi mat khau that bai.'
    ];

    /**
     * Confirm password changed successfully.
     * 
     * @param  string $password
     * @return bool
     */
    private function confirmChangeSuccess(string $password) {
        return User::find(Auth::id())->password == $password;
    }

    /**
     * Invoke change password procedure.
     * 
     * @param  \App\Http\Requests\ChangePasswordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function handle(ChangePasswordRequest $request) {
        // Get validated data.
        $validated = $request->validated();

        // Generate hash value for new password.
        $new_password = Hash::make($validated['new_password']);

        // Update new password.
        User::find(Auth::id())->setPassword($new_password);

        // Confirm password change success, then send notification.
        if ($this->confirmChangeSuccess($new_password)) {
            return back()->with('notify', $this->successMessage);
        }
        return back()->withErrors($this->failedMessage);
    }

    /**
     * Show the change password form.
     * 
     * @return \Illuminate\View\View
     */
    public function view() : \Illuminate\View\View
    {
        return view('auth.password');
    }
}