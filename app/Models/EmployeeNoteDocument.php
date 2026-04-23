<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeNoteDocument extends Model
{

    protected $fillable = [
        'employee_note_id',
        'file_path',
        'file_name',
    ];

}
