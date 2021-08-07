<?php

namespace App\Observers;

use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class BrandObserver
{
    /**
     * Handle the Brand "creating" event.
     * 
     * @param  \App\Models\Brand $brand
     * @return void
     */
    public function creating(Brand $brand) {
        if (Auth::check()) {
            $brand->created_by_user = Auth::id();
            $brand->updated_by_user = Auth::id();
        }
    }

    /**
     * Handle the Brand "updating" event.
     * 
     * @param  \App\Models\Brand $brand
     * @return void
     */
    public function updating(Brand $brand) {
        if (Auth::check()) {
            $brand->updated_by_user = Auth::id();
        }
    }

    /**
     * Handle the Brand "deleted" event.
     * 
     * @param  \App\Models\Brand $brand
     * @return void
     */
    public function deleted(Brand $brand) {
        if (Auth::check()) {
            // Deleting also counted as updated.
            $brand->updated_by_user = Auth::id();
            $brand->save();
        }

        // Delete child Bike(s).
        $brand->bikes->each(function($bike) {
            $bike->delete();
        });
    }
}
