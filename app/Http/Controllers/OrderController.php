<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Bike;
use App\Http\Requests\CreateOrderRequest;
use App\Services\OrderServices;

class OrderController extends Controller
{
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
     * Order Service will be using.
     * 
     * @var  \App\Services\OrderServices
     */
    private $orderServices;

    /**
     * Constructor for OrderController.
     * 
     * @return void
     */
    public function __construct() {
        $this->authorizeResource(Order::class, 'order');
        $this->orderServices = new OrderServices;
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
        $validator['order_checkout'] = $request->has('order_checkout');

        $quantityErrors = $this
            ->orderServices
            ->validateItemQuantityCreate($validator);

        if (count($quantityErrors) > 0) {
            return redirect()
                ->route('orders.create')
                ->withInput()
                ->withErrors($quantityErrors);
        }

        $new_order = $this->orderServices->createOrder($validator);

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
        $bikes = Bike::where('bike_stock', '>', 0)
            ->orWhereHas('orders', function ($query) use ($order) {
                $query->where('order_id', $order->id);
            })
            ->get();
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
        $validator['order_checkout'] = $request->has('order_checkout');

        $quantityErrors = $this
            ->orderServices
            ->validateItemQuantityUpdate($order, $validator);

        if (count($quantityErrors) > 0) {
            return redirect()
                ->route('orders.edit', $order)
                ->withInput()
                ->withErrors($quantityErrors);
        }

        $this->orderServices->updateOrder($order, $validator);

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