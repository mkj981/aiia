<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerTaxSetting extends Model
{
    protected $fillable = [
        'employer_id',
        'tax_code',
        'week1_month1',
        'ni_id',
        'ni_secondary_class_nics_not_payable',
        'enable_foreign_tax_credit'
    ];

   protected $casts = [
       'week1_month1'                           => 'boolean',
       'ni_secondary_class_nics_not_payable'    => 'boolean',
       'enable_foreign_tax_credit'              => 'boolean'
   ];
}
