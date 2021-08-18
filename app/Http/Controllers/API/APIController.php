<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class APIController extends Controller
{
    /**
     * Validator message for ReportAPIController.
     * (same validator for 2 controller, should declare as array in array
     * if have more.)
     * 
     * @var array
     */
    private $validatorMessage = [
        'required' => 'Ngày tháng đang bỏ trống.',
        'date' => 'Ngày tháng không hợp lệ.'
    ];

    /**
     * Get start / end date of month.
     * 
     * @param  string $date
     * @return array
     */
    private function getStartEndDateOfMonth($date) {
        return [
            'start_date' => \Carbon\Carbon::parse($date)->firstOfMonth(),
            'end_date' => \Carbon\Carbon::parse($date)->endOfMonth(),
        ];
    }

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
            ->where('bikes.deleted_at', '=', NULL)
            ->where('orders.deleted_at', '=', NULL)
            ->groupBy('bikes.id', 'bikes.bike_name')
            ->orderBy('bike_order_value', 'DESC')
            ->get();
    }

    /**
     * Get Orders that checked out in range [startDate, endDate].
     * 
     * @param  \Carbon\Carbon $startDate
     * @param  \Carbon\Carbon $endDate
     * @return \Illuminate\Support\Facades\DB
     */
    private function getOrdersStatInRange($startDate, $endDate) {
        return DB::table('order_bike')
            ->join('orders', 'order_bike.order_id', '=', 'orders.id')
            ->join('bikes', 'order_bike.bike_id', '=', 'bikes.id')
            ->select(
                'orders.id',
                DB::raw('SUM(order_bike.order_value) AS quantity'),
                DB::raw(
                    'SUM(order_bike.order_value * 
                        order_bike.order_sell_price) as revenue'
                ),
                DB::raw(
                    'SUM(order_bike.order_value * 
                        ( order_bike.order_sell_price - 
                        order_bike.order_buy_price )) as profit'
                ))
            ->where('checkout_at', '>=', $startDate)
            ->where('checkout_at', '<=', $endDate)
            ->where('bikes.deleted_at', '=', NULL)
            ->where('orders.deleted_at', '=', NULL)
            ->groupBy('orders.id')
            ->orderBy('profit', 'DESC')
            ->get();
    }

    /**
     * Get Orders in range [startDate, endDate]
     * 
     * @param  string $startData
     * @param  string $endDate
     * @return \App\Models\Order
     */
    private function getOrdersInRange($startDate, $endDate) {
        return Order::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->each(function ($item) {
                $item->created = $item->created_at->format('Y-m-d h:i:s');
                $item->detail_url = route('orders.show', $item->id);
                $item->checkout = $item->getCheckedOut()
                    ? $item->checkout_at->format('Y-m-d h:i:s')
                    : 'Chưa thanh toán';
            });
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
        ], $this->validatorMessage);

        if ($validator->fails()) {
            return response()
                ->json(['data' => ['errors' => $validator->errors()]]);
        }

        // Get the month we are querying for.
        $thisMonth = $this->getStartEndDateOfMonth($request->month);

        $quantity = $this
            ->bikeQuantityInRange(
                $thisMonth['start_date'], 
                $thisMonth['end_date']
            )
            ->each(function ($item) {
                $item->url = route('bikes.show', $item->id);
            });

        return response()->json([
            'data' => [
                'detail' => $quantity,
                'items' => count($quantity),
                'month' => $thisMonth['start_date']->format('m-Y')
            ]
        ]);
    }

    /**
     * API method: Get orders and revenue in a month.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function order_revenue_month(Request $request) {
        $validator = Validator::make($request->all(), [
            'month' => 'required|date'
        ], $this->validatorMessage);

        if ($validator->fails()) {
            return response()
                ->json(['data' => ['errors' => $validator->errors()]]);
        }

        $thisMonth = $this->getStartEndDateOfMonth($request->month);

        $ordersInRange = $this
            ->getOrdersStatInRange(
                $thisMonth['start_date'],
                $thisMonth['end_date']
            )
            ->each(function($item) {
                $item->url = route('orders.show', $item->id);
            });
        $ordersTotal = [
            'quantity' => collect($ordersInRange)->sum('quantity'),
            'revenue' => collect($ordersInRange)->sum('revenue'),
            'profit' => collect($ordersInRange)->sum('profit'),
        ];

        return response()
            ->json([
                'data' => [
                    'month' => $thisMonth['start_date']->format('m-Y'),
                    'items' => count($ordersInRange),
                    'detail' => $ordersInRange,
                    'total' => $ordersTotal
                ],
            ]);
    }

    /**
     * View Orders in a specific Month.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function order_month(Request $request) {
        $validator = Validator::make($request->all(), [
            'month' => 'required|date'
        ], $this->validatorMessage);

        if ($validator->fails()) {
            return response()
                ->json(['data' => ['errors' => $validator->errors()]]);
        }

        $thisMonth = $this->getStartEndDateOfMonth($request->month);
        
        $orders = $this->getOrdersInRange(
            $thisMonth['start_date'], 
            $thisMonth['end_date']
        );

        return response()
            ->json([
                'data' => [
                    'month' => $thisMonth['start_date']->format('m-Y'),
                    'detail' => $orders,
                    'items' => $orders->count(),
                ]
            ]);
    }
}
