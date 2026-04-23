<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaySchedule extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'shows_annual_salary',
        'is_active',
    ];


    protected $casts = [
        'shows_annual_salary'   => 'boolean',
        'is_active'             => 'boolean',
    ];
}
