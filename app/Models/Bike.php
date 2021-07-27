<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bike extends Model
{
    use HasFactory;

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
}
