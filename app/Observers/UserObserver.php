<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function creating(User $user)
    {
        // Generate API token.
        $user->api_token = hash('sha256', Str::random(60));
    }

    /**
     * Handle the User "updating" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function updating(User $user)
    {
        // Update API token.
        $user->api_token = hash('sha256', Str::random(60));
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function deleted(User $user)
    {
        // Please add removal here.
    }
}
