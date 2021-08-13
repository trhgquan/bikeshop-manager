<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Bike;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    /**
     * Handle the Order "creating" event.
     * 
     * @param  \App\Models\Order $order
     */
    public function creating(Order $order) {
        if (Auth::check()) {
            $order->created_by_user = Auth::id();
            $order->updated_by_user = Auth::id();
        }
    }

    /**
     * Handle the Order "saving" event.
     * 
     * @param  \App\Models\Order $order
     */
    public function saving(Order $order) {
        if (Auth::check()) {
            $order->updated_by_user = Auth::id();
        }
    }

    /**
     * Handle the Order "deleted" event.
     * 
     * @param  \App\Models\Order $order
     */
    public function deleted(Order $order) {
        if (Auth::check()) {
            // If order isn't checked out, recover added bikes.
            if (! $order->getCheckedOut()) {
                foreach ($order->bikes as $bike) {
                    $bike->bike_stock += $order->orderValue($bike);
                    $bike->save();
                }
            }

            $order->updated_by_user = Auth::id();
            $order->save();
        }
    }
}
