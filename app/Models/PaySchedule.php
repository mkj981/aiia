<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaySchedule extends Model
{
    use HasActivityLog, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'shows_annual_salary',
        'is_active',
    ];

    protected $casts = [
        'shows_annual_salary' => 'boolean',
        'is_active' => 'boolean',
    ];

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function payBases()
    {
        return $this->hasMany(PayBasis::class);
    }
}
