<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class EmployerTaxSetting extends Model
{
    use HasActivityLog;

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


    public function ni()
    {
        return $this->belongsTo(Ni::class, 'ni_id');
    }

    public function employer()
    {
        return $this->belongsTo(Employer::class, 'employer_id');
    }
}
