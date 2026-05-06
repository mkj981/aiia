<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BenefitType extends Model
{
    use HasActivityLog, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];
}
