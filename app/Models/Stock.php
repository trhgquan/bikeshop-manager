<?php

namespace App\Models;

use App\Models\Bike;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'bike_id',
        'stock',
        'buy_price',
        'sell_price',
    ];

    /**
     * This table has no timestamp.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * Primary key of this table.
     * 
     * @var string
     */
    protected $primaryKey = 'bike_id';
}
