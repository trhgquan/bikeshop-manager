<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user) {
        // Any user can view all bikes.
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Brand $brand) {
        // Any user can view any brand.
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user) {
        // Only admin and managers can create brand.
        return in_array($user->role, [
            Role::ROLE_ADMIN,
            Role::ROLE_MANAGER,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Brand $brand) {
        // Only Admin and Managers can update a Brand.
        return in_array($user->role, [
            Role::ROLE_ADMIN,
            Role::ROLE_MANAGER,
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Brand $brand) {
        // Only Admin and Managers can delete a Brand.
        return in_array($user->role, [
            Role::ROLE_ADMIN,
            Role::ROLE_MANAGER,
        ]);
    }
}
