<?php

namespace App\Services;

use App\Models\Bike;
use App\Models\Order;

class OrderServices {
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
    public function validateItemQuantityCreate($validator) {
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
    public function validateItemQuantityUpdate($validator, Order $order) {
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
     * Create a new Order.
     * 
     * @param  array $validator
     * @return \App\Models\Order
     */
    public function createOrder($validator) : Order {
        $new_order = Order::create([
            'customer_name' => $validator['customer_name'],
            'customer_email' => $validator['customer_email'],
            'checkout_at' => $validator['order_checkout']
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

        return $new_order;
    }

    /**
     * Update an existing order.
     * 
     * @param  \App\Models\Order $order
     * @param  array $validator
     * @return void
     */
    public function updateOrder(Order $order, $validator) : void {
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

        $order->checkout_at = $validator['order_checkout']
            ? \Carbon\Carbon::now()->toDateTimeString()
            : NULL;

        $order->save();
    }
}
