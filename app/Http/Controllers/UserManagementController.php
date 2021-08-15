<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Success messages for UserManagementController
     * 
     * @var array
     */
    private $successMessages = [
        'create' => [
            'success' => 'Thêm người dùng mới thành công!'
        ],
        'update_role' => [
            'success' => 'Chỉnh sửa quyền người dùng thành công!'
        ],
        'update_password' => [
            'success' => 'Chỉnh sửa mật khẩu người dùng thành công!'
        ],
        'destroy' => [
            'success' => 'Xóa người dùng thành công!'
        ],
    ];

    /**
     * Failed messages for UserManagementController
     * 
     * @var array
     */
    private $failedMessages = [
        'update_role' => 'Vui lòng chọn quyền hợp lệ!',
    ];

    /**
     * Update user Role.
     * 
     * @param  array $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    private function updateRole(Array $request, User $user) {
        $validator = Validator::make($request, [
            'role' => [
                'required',
                'exists:App\Models\Role,id',
                Rule::in([
                    Role::ROLE_MANAGER,
                    Role::ROLE_STAFF
                ])
            ]
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('users.edit', $user)
                ->withErrors($this->failedMessages['update_role']);
        }

        $user->role = $request['role'];
        $user->save();

        return redirect()
            ->route('users.edit', $user)
            ->with('notify', $this->successMessages['update_role']);
    }

    /**
     * Update user password.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    private function updatePassword(Array $request, User $user) {
        $validator = Validator::make($request, [
            'new_password' => 'required|min:8',
            're_password' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('users.edit', $user)
                ->withInput()
                ->withErrors($validator);
        }

        $user->password = Hash::make($request['new_password']);
        $user->save();

        return redirect()
            ->route('users.edit', $user)
            ->with('notify', $this->successMessages['update_password']);
    }

    /**
     * Constructor for UserManagementController.
     * 
     * @return void
     */
    public function __construct() {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $users = User::with('roles')->get();
        return view('content.users.dashboard', compact('users'));        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $roles = Role::all()->except(Role::ROLE_ADMIN);
        return view('content.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request) {
        $validator = $request->validated();

        User::create([
            'username' => $validator['username'],
            'password' => Hash::make($validator['password']),
            'email' => $validator['email'],
            'name' => $validator['name'],
            'role' => $validator['role']
        ]);

        return redirect()
            ->route('users.index')
            ->with('notify', $this->successMessages['create']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user) {
        $roles = Role::all()->except(Role::ROLE_ADMIN);
        return view('content.users.update', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) {
        if ($request->has('role')) {
            return $this->updateRole($request->only('role'), $user);
        }

        if ($request->has(['new_password', 're_password'])) {
            return $this->updatePassword(
                $request->only(['new_password', 're_password']),
                $user
            );
        }

        return abort(403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('notify', $this->successMessages['destroy']);
    }
}
