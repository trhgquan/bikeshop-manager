<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Order;
use App\Models\Bike;

class OrderDetail extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'bike_id',
        'order_value',
        'order_buy_price',
        'order_sell_price'
    ];

    public function order() {
        return $this->belongsTo(
            Order::class,
            'order_id',
            'id'
        );
    }

    public function bike() {
        return $this->belongsTo(
            Bike::class,
            'bike_id',
            'id'
        );
    }
}
