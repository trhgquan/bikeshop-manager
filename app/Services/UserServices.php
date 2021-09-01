<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserServices {
    /**
     * Update User's password.
     * 
     * @param  \App\Models\User $user
     * @param  string $new_password
     * @return void
     */
    public function updateUserPassword(User $user, string $new_password) : void {
        $user->password = Hash::make($new_password);
        $user->save();
    }

    /**
     * Update User's role.
     * 
     * @param  \App\Models\User $user
     * @param  mixed $role
     * @return void
     */
    public function updateUserRole(User $user, $role) : void {
        $user->role = $role;
        $user->save();
    }    
}