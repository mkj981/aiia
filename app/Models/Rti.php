<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rti extends Model
{
    protected $fillable = [
        'employer_id',
        'sender_type_id',

        'govt_gateway_id',
        'password',

        'first_name',
        'last_name',
        'email',
        'phone',

        'auto_submit_fps_after_finalising_pay_run',
        'include_employees_with_no_payment_on_fps',
        'test_mode',
        'use_test_gateway',
        'allow_linked_eps',
        'compress_fps',
    ];


    protected $casts = [
        'password'                                  => 'encrypted',
        'auto_submit_fps_after_finalising_pay_run'  => 'boolean',
        'include_employees_with_no_payment_on_fps'  => 'boolean',
        'test_mode'                                 => 'boolean',
        'use_test_gateway'                          => 'boolean',
        'allow_linked_eps'                          => 'boolean',
        'compress_fps'                              => 'boolean',
    ];
}
