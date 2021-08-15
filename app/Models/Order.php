<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'customer_name',
        'customer_email',
        'checkout_at',
        'created_by_user',
        'updated_by_user'
    ];

    /**
     * The attributes that should be cast to native types.
     * 
     * @var array
     */
    protected $casts = [
        'checkout_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Is this order checked out?
     * 
     * @return bool
     */
    public function getCheckedOut() {
        return $this->checkout_at != NULL;
    }

    /**
     * The User that created this Order.
     * 
     * @return \Illuminate\Database\Eloquent\Relationship\BelongsTo
     */
    public function created_by() {
        return $this->belongsTo(
            User::class,
            'created_by_user',
            'id'
        )->withTrashed();
    }

    /**
     * The User that last updated this Order.
     * 
     * @return \Illuminate\Database\Eloquent\Relationship\BelongsTo
     */
    public function updated_by() {
        return $this->belongsTo(
            User::class,
            'updated_by_user',
            'id'
        )->withTrashed();
    }

    /**
     * Bikes that ordered in this Order.
     * 
     * @return \Illuminate\Database\Eloquent\Relationship\BelongsToMany
     */
    public function bikes() {
        return $this
            ->belongsToMany(Bike::class, 'order_bike')
            ->withPivot([
                'order_value',
                'order_buy_price',
                'order_sell_price'
            ]);
    }

    /**
     * Order value of a Bike in this Order.
     * 
     * @return int
     */
    public function orderValue(Bike $bike) {
        $bikes_ordered = $this
            ->bikes()
            ->where('bike_id', $bike->id);

        return ($bikes_ordered->count() > 0)
            ? $bikes_ordered->first()->pivot->order_value
            : 0;
    }

    /**
     * Total bikes in this Order.
     * 
     * @return int
     */
    public function quantity() {
        return $this->bikes->sum('pivot.order_value');
    }

    /**
     * Revenue gained by this Order.
     * 
     * @return int
     */
    public function revenue() {
        return $this->bikes->sum(function($detail) {
            return $detail->pivot->order_value
                * $detail->pivot->order_sell_price;
        });
    }

    /**
     * Profit gained by this Order.
     * 
     * @return int
     */
    public function profit() {
        return $this->bikes->sum(function ($detail) {
            return $detail->pivot->order_value 
                * ($detail->pivot->order_sell_price
                - $detail->pivot->order_buy_price);
        });
    }
}
