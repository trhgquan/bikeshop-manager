<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
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
                : NULL,

            // replace this with an Observer.
            'created_by_user' => Auth::id(),
            'updated_by_user' => Auth::id()
        ]);

        foreach ($validator['bike_id'] as $index => $bike_id) {
            $bike = Bike::find($bike_id);

            $order_detail = OrderDetail::create([
                'order_id' => $new_order->id,
                'bike_id' => $bike_id,
                'order_value' => $validator['order_value'][$index],
                'order_buy_price' => $bike->bike_buy_price,
                'order_sell_price' => $bike->bike_sell_price
            ]);

                       $bike->bike_stock -= $order_detail->order_value;
            $bike->save();
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
        $detail = $order->detail;
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
        $details = $order->detail;

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
        dd($request);
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
