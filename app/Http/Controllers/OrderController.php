<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Bike;
use App\Http\Requests\CreateOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Error messages.
     * 
     * @var array
     */
    private $errorMessages = [
        'out-of-stock' => [
            'Mặt hàng :item không đủ, chỉ còn :stock trong kho.'
        ],
    ];

    /**
     * Success messages.
     * 
     * @var array
     */
    private $successMessages = [
        'create' => [
            'success' => 'Tạo đơn hàng mới thành công.'
        ],
        'update' => [
            'success' => 'Chỉnh sửa đơn hàng thành công.'
        ],
        'destroy' => [
            'success' => 'Hủy đơn hàng thành công.'
        ]
    ];

    /**
     * Get error message telling that item is out of stock.
     * 
     * @param  \App\Models\Bike $bike
     * @return string
     */
    private function getItemOutOfStockError(Bike $item) {
        $itemError = str_replace(
            ':item', 
            $item->bike_name,
            $this->errorMessages['out-of-stock']
        );
        
        return str_replace(
            ':stock',
            $item->bike_stock,
            $itemError
        );
    }

    /**
     * Validate item quantity on Create.
     * aka checking if that item is out of stock or not.
     * 
     * @param  array $validator
     * @return array
     */
    private function validateItemQuantityCreate($validator) {
        $errors = [];

        foreach ($validator['order_detail'] as $order_detail) {
            $bike = Bike::find($order_detail['bike_id']);
            $order_value = (int)$order_detail['order_value'];

            if ($bike->bike_stock < $order_value) {
                array_push($errors, $this->getItemOutOfStockError($bike));
            }
        }

        return $errors;
    }

    /**
     * Validate item quantity on Update.
     * 
     * @param  array $validator
     * @param  \App\Models\Order $order
     * @return array
     */
    private function validateItemQuantityUpdate($validator, Order $order) {
        $errors = [];

        foreach ($validator['order_detail'] as $order_detail) {
            $bike = Bike::find($order_detail['bike_id']);
            $order_value = (int)$order_detail['order_value'];

            if ($bike->bike_stock + $order->orderValue($bike) < $order_value) {
                array_push($errors, $this->getItemOutOfStockError($bike));
            }
        }

        return $errors;
    }

    /**
     * Constructor for OrderController.
     * 
     * @return void
     */
    public function __construct() {
        $this->authorizeResource(Order::class, 'order');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $orders = Order::orderBy('created_at', 'DESC')->get();
        return view('content.orders.dashboard', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $bikes = Bike::where('bike_stock', '>', 0)->get();
        return view('content.orders.create', compact('bikes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateOrderRequest $request) {
        $validator = $request->validated();

        $quantityErrors = $this->validateItemQuantityCreate($validator);

        if (count($quantityErrors) > 0) {
            return redirect()
                ->route('orders.create')
                ->withInput()
                ->withErrors($quantityErrors);
        }

        $new_order = Order::create([
            'customer_name' => $validator['customer_name'],
            'customer_email' => $validator['customer_email'],
            'checkout_at' => $request->has('order_checkout')
                ? \Carbon\Carbon::now()->toDateTimeString()
                : NULL
        ]);

        foreach ($validator['order_detail'] as $order_detail) {
            $bike = Bike::find($order_detail['bike_id']);

            $bike->bike_stock -= (int)$order_detail['order_value'];
            $bike->save();

            $new_order->bikes()->attach($bike->id, [
                'order_value' => $order_detail['order_value'],
                'order_buy_price' => $bike->bike_buy_price,
                'order_sell_price' => $bike->bike_sell_price,
            ]);
        }

        return redirect()
            ->route('orders.show', $new_order->id)
            ->with('notify', $this->successMessages['create']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order) {
        $detail = $order->bikes;
        return view('content.orders.details', compact('order', 'detail'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order) {
        $bikes = Bike::all();
        $details = $order->bikes()->get();

        return view(
            'content.orders.update', 
            compact('order', 'details', 'bikes')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CreateOrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(CreateOrderRequest $request, Order $order) {
        $validator = $request->validated();

        $quantityErrors = $this->validateItemQuantityUpdate($validator, $order);

        if (count($quantityErrors) > 0) {
            return redirect()
                ->route('orders.update', $order)
                ->withInput()
                ->withErrors($quantityErrors);
        }

        // Get new information of updated bikes.
        $bikes_updated = [];

        foreach ($validator['order_detail'] as $order_detail) {
            $bike = Bike::find($order_detail['bike_id']);

            $bike->bike_stock -= (int)$order_detail['order_value']
                                    - $order->orderValue($bike);

            $bike->save();

            $bikes_updated[$bike->id] = [
                'order_value' => $order_detail['order_value'],
                'order_buy_price' => $bike->bike_buy_price,
                'order_sell_price' => $bike->bike_sell_price
            ];
        }

        // Restore bikes that was eliminated from the Order.
        foreach ($order->bikes as $bike) {
            if (!array_key_exists($bike->id, $bikes_updated)) {
                $bike->bike_stock += $order->orderValue($bike);
                $bike->save();
            }
        }

        $order->bikes()->sync($bikes_updated);
        $order->customer_name = $validator['customer_name'];
        $order->customer_email = $validator['customer_email'];

        $order->checkout_at = $request->has('order_checkout')
            ? \Carbon\Carbon::now()->toDateTimeString()
            : NULL;

        $order->save();

        return redirect()
            ->route('orders.show', $order->id)
            ->with('notify', $this->successMessages['update']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order) {
        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('notify', $this->successMessages['destroy']);
    }
}
