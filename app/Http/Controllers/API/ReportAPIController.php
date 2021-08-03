<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ReportAPIController extends Controller
{
    private function bikeQuantityInMonth($startDate, $endDate) {
        return DB::table('order_bike')
            ->join('orders', 'order_bike.order_id', '=', 'orders.id')
            ->join('bikes', 'order_bike.bike_id', '=', 'bikes.id')
            ->select(
                'bikes.bike_name', 
                DB::raw('SUM(order_bike.order_value) AS bike_order_value')
            )
            ->where('checkout_at', '>=', $startDate)
            ->where('checkout_at', '<=', $endDate)
            ->groupBy('bikes.bike_name')
            ->orderBy('bike_order_value', 'DESC')
            ->get();
    }

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

        $startDate = \Carbon\Carbon::parse($request->month)
            ->startOfMonth();
        $endDate = \Carbon\Carbon::parse($request->month)
            ->endOfMonth();

        $data = $this->bikeQuantityInMonth($startDate, $endDate);

        return response()->json(['data' => $data]);
    }
}
