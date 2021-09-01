<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class BikeServices {
    /**
     * Get bike name and total sales in the range [startDate, endDate].
     * 
     * @param  \Carbon\Carbon $month
     * @return \Illuminate\Support\Collection
     */
    public function bikeQuantityInMonth(
        \Carbon\Carbon $month) : \Illuminate\Support\Collection {
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        return DB::table('order_bike')
        ->join('orders', 'order_bike.order_id', '=', 'orders.id')
        ->join('bikes', 'order_bike.bike_id', '=', 'bikes.id')
        ->select(
            'bikes.id',
            'bikes.bike_name', 
            DB::raw('SUM(order_bike.order_value) AS bike_order_value')
        )
        ->where('checkout_at', '>=', $startDate)
        ->where('checkout_at', '<=', $endDate)
        ->where('bikes.deleted_at', '=', NULL)
        ->where('orders.deleted_at', '=', NULL)
        ->groupBy('bikes.id', 'bikes.bike_name')
        ->orderBy('bike_order_value', 'DESC')
        ->get()
        ->each(function ($item) {
            $item->url = route('bikes.show', $item->id);
        });
    }
}