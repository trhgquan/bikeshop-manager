<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Bike;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\CreateOrderUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private $resultsPerPage = 10;

    private $errorMessages = [
        'out-of-stock' => [
            'Mat hang :item khong du, chi con :stock trong kho.'
        ]
    ];

    private $successMessages = [
        'create' => [
            'success' => 'Tao don hang moi thanh cong.'
        ],
        'update' => [
            'success' => 'Chinh sua don hang thanh cong.'
        ],
        'destroy' => [
            'success' => 'Huy don hang thanh cong.'
        ]
    ];

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

    private function validateItemQuantity($validator) {
        $errors = array();

        foreach ($validator['bike_id'] as $index => $bike_id) {
            $bike = Bike::find($bike_id);

            if ($bike->bike_stock < $validator['order_value'][$index]) {
                array_push($errors, $this->getItemOutOfStockError($bike));
            }
        }

        return $errors;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $orders = Order::orderBy('created_at', 'DESC')
            ->paginate($this->resultsPerPage);
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

        $quantityErrors = $this->validateItemQuantity($validator);

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
                ? Carbon\Carbon::now()->toDateTimeString()
                : NULL
        ]);

        foreach ($validator['bike_id'] as $index => $bike_id) {
            $bike = Bike::find($bike_id);

            $bike->bike_stock -= (int)$validator['order_value'][$index];
            $bike->save();

            $new_order->bikes()->attach($bike_id, [
                'order_value' => $validator['order_value'][$index],
                'order_buy_price' => $bike->bike_buy_price,
                'order_sell_price' => $bike->bike_sell_price
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
        $detail = $order->bikes()->get();
        return view('content.orders.details', compact('order', 'detail'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order) {
        $bikes = Bike::where('bike_stock', '>', 0)->get();
        $details = $order->bikes()->get();

        return view(
            'content.orders.update', 
            compact('order', 'details', 'bikes')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CreateOrderUpdateRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(CreateOrderUpdateRequest $request, Order $order) {
        $validator = $request->validated();

        $quantityErrors = $this->validateItemQuantity($validator);

        if (count($quantityErrors) > 0) {
            return redirect()
                ->route('orders.update')
                ->withInput()
                ->withErrors($quantityErrors);
        }

        // Remove any non-chosen bike.
        foreach ($order->bikes as $bike) {
            if (! in_array($bike->id, $request->bike_id)) {
                $bike->bike_stock += $bike->pivot->order_value;
                $bike->save();
            }
        }

        // Get new information of updated bikes.
        $bikes_updated = array();

        foreach ($validator['bike_id'] as $index => $bike_id) {
            // Update bike stock.
            $bike = Bike::find($bike_id);

            $bike->bike_stock -=
                (int)$validator['order_value'][$index]
                - $order->orderValue($bike);
            
            $bike->save();

            $bikes_updated[$bike_id] = [
                'order_value' => $validator['order_value'][$index],
                'order_buy_price' => $bike->bike_buy_price,
                'order_sell_price' => $bike->bike_sell_price
            ];
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
