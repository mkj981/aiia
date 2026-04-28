<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BankCsvFormat extends Model
{
    protected $fillable = [
        'name',
        'description',
        'requires_bacs_sun',
        'is_active',
    ];

    protected $casts = [

        'requires_bacs_sun' => 'boolean',
        'is_active' => 'boolean',

    ];

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
