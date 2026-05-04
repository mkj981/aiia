<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class LeavesType extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'name',
        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];
}
