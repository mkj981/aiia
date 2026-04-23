<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ni extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'code',
        'description',
        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];
}
