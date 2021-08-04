<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ReportAPIController extends Controller
{
    /**
     * Get bike name and total sales in the range [startDate, endDate].
     * 
     * @param  \Carbon\Carbon $startDate
     * @param  \Carbon\Carbon $endDate
     * @return \Illuminate\Support\Facades\DB
     */
    private function bikeQuantityInRange($startDate, $endDate) {
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
            ->groupBy('bikes.id', 'bikes.bike_name')
            ->orderBy('bike_order_value', 'DESC')
            ->get();
    }

    /**
     * API method: handling bike_quantity_month.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function bike_quantity_month(Request $request) {
        $validator = Validator::make($request->all(),[
            'month' => 'required|date'
        ],[
            'required' => 'Ngay thang dang bo trong.',
            'date' => 'Ngay thang khong hop le.'
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['data' => ['error' => $validator->errors()]]);
        }

        // Get the month we are querying for.
        $startDate = \Carbon\Carbon::parse($request->month)
            ->startOfMonth();
        $endDate = \Carbon\Carbon::parse($request->month)
            ->endOfMonth();

        $quantity = $this->bikeQuantityInRange($startDate, $endDate);

        return response()->json([
            'data' => [
                'detail' => $quantity,
                'month' => $startDate->format('m-Y')
            ]
        ]);
    }
}
