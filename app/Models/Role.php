<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Constants to avoid magic role number!
     *
     * @var int
     */
    public const ROLE_ADMIN = 1;
    public const ROLE_MANAGER = 2;
    public const ROLE_STAFF = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'role_name',
    ];

    /**
     * Table has no timestamp.
     *
     * @var bool
     */
    public $timestamps = false;
}
