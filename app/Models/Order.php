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

    public function detail() {
        return $this->hasMany(
            OrderDetail::class,
            'order_id',
            'id'
        );
    }
}
