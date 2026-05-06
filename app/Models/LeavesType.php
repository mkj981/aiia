<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeavesType extends Model
{
    use HasActivityLog, SoftDeletes;

    protected $fillable = [
        'name',
        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];
}
