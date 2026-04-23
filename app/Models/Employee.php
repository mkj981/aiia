<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employer_id',

        'photo',
        'title',
        'first_name',
        'middle_name',
        'last_name',

        'date_of_birth',
        'age',
        'gender',
        'marital_status',

        'email',
        'alternative_email',
        'telephone',
        'mobile',

        'passport_number',
        'ni_number',
        'previous_surname',

        'address_line_1',
        'address_line_2',
        'address_line_3',
        'address_line_4',
        'postcode',
        'country',

        'has_partner',
        'partner_first_name',
        'partner_initials',
        'partner_last_name',
        'partner_ni_number',
    ];


    protected $casts = [
        'date_of_birth' => 'date',
        'has_partner'   => 'boolean',
    ];
}
