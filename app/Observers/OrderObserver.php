<?php

namespace App\Observers;

use App\Models\Order;
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
     * Handle the Order "updating" event.
     * 
     * @param  \App\Models\Order $order
     */
    public function updating(Order $order) {
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
            $order->updated_by_user = Auth::id();
            $order->save();
        }
    }
}
