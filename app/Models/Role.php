<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public const ROLE_ADMIN = 1;
    public const ROLE_MANAGER = 2;
    public const ROLE_STAFF = 3;

    protected $fillable = [
        'id',
        'role_name'
    ];

    public $timestamps = false;
}
