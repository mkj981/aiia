<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rti extends Model
{
    use HasActivityLog;

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
        'password' => 'encrypted',
        'auto_submit_fps_after_finalising_pay_run' => 'boolean',
        'include_employees_with_no_payment_on_fps' => 'boolean',
        'test_mode' => 'boolean',
        'use_test_gateway' => 'boolean',
        'allow_linked_eps' => 'boolean',
        'compress_fps' => 'boolean',
    ];

    public function senderType(): BelongsTo
    {
        return $this->belongsTo(SenderType::class);
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }
}
