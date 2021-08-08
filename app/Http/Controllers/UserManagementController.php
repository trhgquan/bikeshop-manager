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
        'update' => [
            'success' => 'Chỉnh sửa người dùng thành công!'
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
        'update' => 'Vui lòng chọn quyền hợp lệ!',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $this->authorize('viewAny', User::class);

        $users = User::with('roles')->get();
        return view('content.users.dashboard', compact('users'));        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $this->authorize('create', User::class);

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
        $this->authorize('create', User::class);

        $validator = $request->validated();

        $user = User::create([
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
        $this->authorize('update', $user);

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
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
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
                ->withErrors($this->failedMessages['update']);
        }

        $user->role = $request->role;
        $user->save();

        return redirect()
            ->route('users.edit', $user)
            ->with('notify', $this->successMessages['update']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('notify', $this->successMessages['destroy']);
    }
}
