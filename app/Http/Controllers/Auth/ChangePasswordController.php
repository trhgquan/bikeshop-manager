<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\User;

class ChangePasswordController extends Controller
{
    /**
     * Validation rules for ChangePasswordController.
     * 
     * @var array
     */
    private $validationRules = [
        'password' => 'required|current_password',
        'new_password' => 'required|min:8',
        'confirm_password' => 'required|same:new_password'
    ];

    /**
     * Validation message when fails.
     * 
     * @var array
     */
    private $validationMessages = [
        'required' => 'Ban chua dien o :attribute',
        'current_password' => 'Mat khau hien tai khong khop',
        'min' => 'Do dai o :attribute phai toi thieu :min ky tu',
        'same' => 'O :attribute khong trung voi o :other'
    ];

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
    private function confirmChangeSuccess($password) {
        return User::find(Auth::id())->password == $password;
    }

    /**
     * Update user's password.
     * 
     * @param  string $password
     */
    private function update($password) {
        $user = User::find(Auth::user()->id);

        $user->password = $password;

        $user->save();
    }

    /**
     * Invoke change password procedure.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request) {
        $validator = Validator::make(
            $request->all(), 
            $this->validationRules, 
            $this->validationMessages
        );
        
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Create new password hash.
        $new_password = Hash::make($request->new_password);

        // Update new password.
        $this->update($new_password);

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
    public function view() {
        return view('auth.password');
    }
}