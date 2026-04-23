<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeNote extends Model
{
    protected $fillable = [
        'employee_id',
        'employee_note_type_id',
        'note',
    ];


}
