<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\OrderDetail;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'checkout_at',
        'created_by_user',
        'updated_by_user'
    ];

    protected $casts = [
        'checkout_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getCheckedOut() {
        return $this->checkout_at != NULL;
    }

    public function created_by() {
        return $this->hasOne(
            User::class,
            'id',
            'created_by_user'
        );
    }

    public function updated_by() {
        return $this->hasOne(
            User::class,
            'id',
            'updated_by_user'
        );
    }

    public function bikes() {
        return $this
            ->belongsToMany(Bike::class, 'order_bike')
            ->withPivot([
                'order_value',
                'order_buy_price',
                'order_sell_price'
            ]);
    }

    public function orderValue(Bike $bike) {
        $ordered = $this
            ->bikes()
            ->where('bike_id', $bike->id);

        return ($ordered->count() > 0)
            ? $ordered->first()->pivot->order_value
            : 0;
    }

    public function quantity() {
        return $this->bikes->sum('pivot.order_value');
    }

    public function income() {
        return $this->bikes->sum(function($detail) {
            return $detail->pivot->order_value
                * $detail->pivot->order_sell_price;
        });
    }

    public function revenue() {
        return $this->bikes->sum(function ($detail) {
            return $detail->pivot->order_value 
                * ($detail->pivot->order_sell_price
                - $detail->pivot->order_buy_price);
        });
    }
}
