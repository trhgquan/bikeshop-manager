<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;

class ChangePasswordController extends Controller
{
    /**
     * Message when success.
     *
     * @var array
     */
    private $successMessage = [
        'success' => 'Đổi mật khẩu thành công.',
    ];

    /**
     * User Service will be using.
     *
     * @var  \App\Services\UserServices;
     */
    private $userServices;

    /**
     * Constructor for ChangePasswordController.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userServices = new \App\Services\UserServices();
    }

    /**
     * Invoke change password procedure.
     *
     * @param \App\Http\Requests\ChangePasswordRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(ChangePasswordRequest $request)
    {
        $this->userServices->updateUserPassword(
            Auth::user(),
            $request->new_password
        );

        return redirect()
            ->route('auth.changepassword.index')
            ->with('notify', $this->successMessage);
    }

    /**
     * Show the change password form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('auth.password');
    }
}
