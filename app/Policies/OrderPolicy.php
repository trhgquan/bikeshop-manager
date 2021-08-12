<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user) {
        // Any User can view every Orders.
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Order $order) {
        // Any User can view any Order.
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user) {
        // Any User can create Order.
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Order $order) {
        // User can update an order if it is not checked out,
        // and he's the creator; or User is an Admin / Manager!
        return (! $order->getCheckedOut() 
                && $user->id === $order->created_by_user)
            ||  in_array($user->role, [
                Role::ROLE_ADMIN,
                Role::ROLE_MANAGER,
            ]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Order $order) {
        // User can delete an order if it is not checked out,
        // and is the creator of the order; or User is an Admin / Manager!
        return (! $order->getCheckedOut() 
                && $user->id === $order->created_by_user) 
            || in_array($user->role, [
                Role::ROLE_ADMIN,
                Role::ROLE_MANAGER,
            ]);
    }
}
