<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Success messages for UserManagementController.
     *
     * @var array
     */
    private $successMessages = [
        'create' => [
            'success' => 'Thêm người dùng mới thành công!',
        ],
        'update_role' => [
            'success' => 'Chỉnh sửa quyền người dùng thành công!',
        ],
        'update_password' => [
            'success' => 'Chỉnh sửa mật khẩu người dùng thành công!',
        ],
        'destroy' => [
            'success' => 'Xóa người dùng thành công!',
        ],
    ];

    /**
     * User Services will be using.
     *
     * @var \App\Services\UserServices
     */
    private $userServices;

    /**
     * Constructor for UserManagementController.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
        $this->userServices = new \App\Services\UserServices();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('roles')->get();

        return view('content.users.dashboard', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all()->except(Role::ROLE_ADMIN);

        return view('content.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateUserRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $validator = $request->validated();

        $this->userServices->createUser($request->validated());

        return redirect()
            ->route('users.index')
            ->with('notify', $this->successMessages['create']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all()->except(Role::ROLE_ADMIN);

        return view('content.users.update', compact('user', 'roles'));
    }

    /**
     * Update a User's password.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User         $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update_password(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:8',
            're_password'  => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('users.edit', $user)
                ->withErrors($validator);
        }

        $this->userServices
            ->updateUserPassword($user, $request->new_password);

        return redirect()
            ->route('users.edit', $user)
            ->with('notify', $this->successMessages['update_password']);
    }

    /**
     * Update a User's role.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User         $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update_role(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
            'role' => [
                'required',
                'exists:App\Models\Role,id',
                Rule::in([
                    Role::ROLE_MANAGER,
                    Role::ROLE_STAFF,
                ]),
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('users.edit', $user)
                ->withErrors($validator);
        }

        $this->userServices->updateUserRole($user, $request->role);

        return redirect()
            ->route('users.edit', $user)
            ->with('notify', $this->successMessages['update_role']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('notify', $this->successMessages['destroy']);
    }
}
