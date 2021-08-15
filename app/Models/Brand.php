<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'brand_name',
        'brand_description',
        'created_by_user',
        'updated_by_user'
    ];

    /**
     * The attributes that should be cast to native types.
     * 
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Return id - name format.
     * 
     * @return string
     */
    public function idAndName() {
        return sprintf('%s - %s', $this->id, $this->brand_name);
    }

    /**
     * Get Bikes that belongs to this Brand.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bikes() {
        return $this->hasMany(Bike::class);
    }

    /**
     * Get the user that created the Brand
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_by() {
        return $this->belongsTo(
            User::class, 
            'created_by_user',
            'id'
        )->withTrashed();
    }

    /**
     * Get the user that last edit the Brand.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updated_by() {
        return $this->belongsTo(
            User::class,
            'updated_by_user', 
            'id'
        )->withTrashed();
    }
}
