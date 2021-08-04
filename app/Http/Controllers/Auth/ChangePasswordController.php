<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
     * Save new password to the database.
     * 
     * @param  \App\Models\User $user
     * @param  string $password
     */
    private function store(User $user, string $password) {
        $user->password = $password;

        // Generate new API token.
        $user->api_token = hash('sha256', Str::random(60));

        $user->save();
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
        $this->store(Auth::user(), $new_password);

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
    public function index() {
        return view('auth.password');
    }
}