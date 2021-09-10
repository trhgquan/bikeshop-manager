<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class APIController extends Controller
{
    /**
     * Validator message for ReportAPIController.
     * (same validator for 2 controller, should declare as array in array
     * if have more.).
     *
     * @var array
     */
    private $validatorMessage = [
        'required' => 'Ngày tháng đang bỏ trống.',
        'date'     => 'Ngày tháng không hợp lệ.',
    ];

    /**
     * Order Services will be using.
     *
     * @var \App\Services\OrderServices
     */
    private $orderServices;

    /**
     * Bike Services will be using.
     *
     * @var \App\Services\BikeServices
     */
    private $bikeServices;

    /**
     * Constructor for APIController.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bikeServices = new \App\Services\BikeServices();
        $this->orderServices = new \App\Services\OrderServices();
    }

    /**
     * API method: handling bike_quantity_month.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function bike_quantity_month(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required|date',
        ], $this->validatorMessage);

        if ($validator->fails()) {
            return response()
                ->json(['data' => ['errors' => $validator->errors()]]);
        }

        // Get the month we are querying for.
        $month = \Carbon\Carbon::parse($request->month);

        $quantity = $this
            ->bikeServices
            ->bikeQuantityInMonth($month);

        return response()->json([
            'data' => [
                'detail' => $quantity,
                'items'  => count($quantity),
                'month'  => $month->format('m-Y'),
            ],
        ]);
    }

    /**
     * API method: Get orders and revenue in a month.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function order_revenue_month(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required|date',
        ], $this->validatorMessage);

        if ($validator->fails()) {
            return response()
                ->json(['data' => ['errors' => $validator->errors()]]);
        }

        $month = \Carbon\Carbon::parse($request->month);

        $ordersInRange = $this
            ->orderServices
            ->getOrdersStatInMonth($month);

        return response()
            ->json([
                'data' => [
                    'month'  => $month->format('m-Y'),
                    'items'  => count($ordersInRange),
                    'detail' => $ordersInRange,
                    'total'  => [
                        'quantity' => collect($ordersInRange)->sum('quantity'),
                        'revenue'  => collect($ordersInRange)->sum('revenue'),
                        'profit'   => collect($ordersInRange)->sum('profit'),
                    ],
                ],
            ]);
    }

    /**
     * View Orders in a specific Month.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return \Illuminate\Http\Response
     */
    public function order_month(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required|date',
        ], $this->validatorMessage);

        if ($validator->fails()) {
            return response()
                ->json(['data' => ['errors' => $validator->errors()]]);
        }

        $month = \Carbon\Carbon::parse($request->month);

        $orders = $this->orderServices->getOrdersInMonth($month);

        return response()
            ->json([
                'data' => [
                    'month'  => $month->format('m-Y'),
                    'detail' => $orders,
                    'items'  => $orders->count(),
                ],
            ]);
    }
}
