<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bike extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'brand_id',
        'bike_name',
        'bike_description',
        'created_by_user',
        'updated_by_user'
    ];

    /**
     * The attributes that should be cast to native types.
     * 
     * @var array
     */
    protected $cast = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * SoftDelete all (actually just one) Stocks referenced to this Bike.
     * 
     */
    public static function boot() {
        parent::boot();

        static::deleting(function($bike) {
            $bike->stock->each(function($stock) {
                $stock->delete();
            });
        });
    }

    /**
     * Get the Brand that owns the Bike
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand() {
        return $this->belongsTo(
            Brand::class, 
            'brand_id', 
            'id'
        );
    }
    
    /**
     * Get the User that created the Bike.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_by() {
        return $this->belongsTo(
            User::class,
            'created_by_user',
            'id'
        );
    }

    /**
     * Get the User that last updated the Bike.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updated_by() {
        return $this->belongsTo(
            User::class,
            'updated_by_user',
            'id'
        );
    }

    /**
     * Get the Bike's stock stat.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stock() {
        return $this->hasOne(Stock::class);
    }
}
