<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class AeoType extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];



    protected $casts = [
        'is_active' => 'boolean',
    ];
}
