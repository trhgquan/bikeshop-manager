<?php

namespace App\Observers;

use App\Models\Bike;
use Illuminate\Support\Facades\Auth;

class BikeObserver
{
    /**
     * Handle the Bike "creating" event.
     *
     * @param \App\Models\Bike $bike
     *
     * @return void
     */
    public function creating(Bike $bike)
    {
        if (Auth::check()) {
            $bike->created_by_user = Auth::id();
            $bike->updated_by_user = Auth::id();
        }
    }

    /**
     * Handle the Bike "updating" event.
     *
     * @param \App\Models\Bike $bike
     *
     * @return void
     */
    public function updating(Bike $bike)
    {
        if (Auth::check()) {
            $bike->updated_by_user = Auth::id();
        }
    }

    /**
     * Handle the Bike "deleted" event.
     *
     * @param \App\Models\Bike $bike
     *
     * @return void
     */
    public function deleted(Bike $bike)
    {
        if (Auth::check()) {
            $bike->updated_by_user = Auth::id();
            $bike->save();
        }

        // Adding relationships to delete here.
    }
}
