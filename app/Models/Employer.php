<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employer extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'logo',
        'payroll_start_year',
        'company_number',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'address_line_4',
        'postcode',
        'country',
    ];

    public function hmrc()
    {
        return $this->hasOne(EmployerHrmc::class);
    }
}
