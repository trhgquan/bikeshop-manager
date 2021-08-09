<?php

namespace App\Policies;

use App\Models\Bike;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class BikePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user) {
        // Any User can view every Bike.
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bike  $bike
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Bike $bike) {
        // Any User can view any Bike.
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user) {
        // Only Admin and Managers can create new Bike.
        return in_array($user->role, [
            Role::ROLE_ADMIN,
            Role::ROLE_MANAGER,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bike  $bike
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Bike $bike) {
        // Only Admin and Managers can update any Bike.
        return in_array($user->role, [
            Role::ROLE_ADMIN,
            Role::ROLE_MANAGER,
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bike  $bike
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Bike $bike) {
        // Only Admin and Managers can delete any Bike.
        return in_array($user->role, [
            Role::ROLE_ADMIN,
            Role::ROLE_MANAGER,
        ]);
    }
}
