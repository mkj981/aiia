<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class EmployeeNote extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employee_id',
        'employee_note_type_id',
        'note',
    ];


}
